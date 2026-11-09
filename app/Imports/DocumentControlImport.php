<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; //* for excel upload with heading row
use Maatwebsite\Excel\Concerns\WithValidation; //* for check data validation on excel upload
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Log;

class DocumentControlImport implements ToCollection, WithHeadingRow, WithValidation
{
    private $result = [];
    private $empno;

    public function __construct($empno)
    {
        $this->empno = $empno;
    }


    public function rules(): array
    {
        return [
            'customer'          => 'required',
            'document_no'       => 'required',
            'rev'               => 'required',
            'effective'         => 'required',
        ];
    }
    public function customValidationMessages()
    {
        // (Optional) กำหนดข้อความเตือนภาษาไทย
        return [
            'customer.required'         => 'customer ห้ามว่าง',
            'document_no.required'      => 'document_no ห้ามว่าง',
            'rev.required'              => 'rev ห้ามว่าง',
            'effective.required'        => 'effective ห้ามว่าง',
        ];
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $YM = date('Ym');
        DB::beginTransaction(); // เริ่มต้น Transaction
        try {
            foreach ($collection as $row) {
                Log::info('Excel Row Keys: ', array_keys($row->toArray()));
                /**
                 * * get name of column in excel file
                 * * ชื่อหัวตาราง Cust Work จะถูกแปลงเป็น cust_work
                 */
                $customer       = trim($row['customer']);
                $document_no    = trim($row['document_no']);
                $rev            = trim($row['rev']);
                $effective      = trim($row['effective']);

                /**
                 * * ตรวจสอบว่าข้อมูลนี้มีแล้วหรือยัง
                 * * if : มีแล้ว
                 * * else : ยังไม่มี
                 */
                $checkSameData = DB::table('DOC_CONTROL_TBL')
                ->where('DCT_NO',$document_no)
                ->where('DCT_EFT',$effective)
                ->where('DCT_REV',$rev)
                ->first();

                if(!empty($checkSameData)){
                    $data['msg']        = 'มีอยู่แล้ว';
                    $data['std']        = null;
                    $data['stdcheck']   = true;
                    $data['type']       = 'ECN';
                    $data['rev']        = $rev;
                    $data['cus']        = $customer;
                    $data['doc']        = $document_no;
                    $data['eft']        = $effective;
                } else {

                    /**
                     * * ปรับสถานะอันเก่าให้เป็น 0
                     * * หา work , ecn , status
                     */
                    $DisabledPreviousVersion = DB::table('DOC_CONTROL_TBL')
                    ->where('DCT_NO',$document_no)
                    ->where('DCT_EFT',$effective)
                    ->where('DCT_STD',1)
                    ->update([
                        'DCT_STD'       => 0,
                        'DCT_LSTDT'     => date('Y-m-d H:i:s'),
                        'DCT_REM_LST'   => 'CLOSE VERSION',
                        'DCT_EMPNO_LST' => $this->empno,
                    ]);

                    $lastRecordTrans = DB::table('DOC_CONTROL_TBL')->orderBy('DCT_ID', 'DESC')->first();
                    $lastRecordTrans2 = $lastRecordTrans ? $lastRecordTrans->DCT_ID : 'DCT-' . $YM . '-000000';
                    $DCT_ID = AutogenerateKey('DCT', $lastRecordTrans2);
                    $insertTrans = DB::table('DOC_CONTROL_TBL')->insert([
                        'DCT_ID'            => $DCT_ID,
                        'DCT_TYPE'          => 'ECN',
                        'DCT_REV'           => $rev,
                        'DCT_STD'           => 1,
                        'DCT_CUS'           => $customer,
                        'DCT_NO'            => $document_no,
                        'DCT_EFT'           => $effective,
                        'DCT_DT_ADD'        => date('Y-m-d H:i:s'),
                        'DCT_EMPNO_ADD'     => $this->empno,
                    ]);

                    if($insertTrans) {
                        $data['msg']        = 'สำเร็จ';
                        $data['std']        = true;
                        $data['stdcheck']   = true;
                        $data['type']       = 'ECN';
                        $data['rev']        = $rev;
                        $data['cus']        = $customer;
                        $data['doc']        = $document_no;
                        $data['eft']        = $effective;
                    } else {
                        $data['msg']        = 'ไม่สำเร็จ';
                        $data['std']        = false;
                        $data['stdcheck']   = true;
                        $data['type']       = 'ECN';
                        $data['rev']        = $rev;
                        $data['cus']        = $customer;
                        $data['doc']        = $document_no;
                        $data['eft']        = $effective;
                    }
                }
                array_push($this->result, $data);
            }

            // * ตรวจสอบว่าในผลลัพธ์ มี 'std' ที่เป็น false อยู่บ้างไหม
            $hasError = collect($this->result)->contains('stdcheck', false);
            if ($hasError) {
                // * มีอย่างน้อยหนึ่งรายการที่พัง
                DB::rollBack();
                return "พบข้อผิดพลาด ระบบทำการ Rollback ข้อมูลทั้งหมด";
            } else {
                DB::commit();
                return "บันทึกข้อมูลสำเร็จ";
            }
        } catch (\Exception $e) {
            Log::error('DocumentControlImport > collection > Unexpected error: ' . $e->getMessage());
            DB::rollBack(); // * ยกเลิก Transaction หากมีข้อผิดพลาด
            throw $e; // * ส่งต่อข้อผิดพลาด
        }
    }

    /**
     * * ส่งกลับไปให้ที่หน้า Controller ApiInsertController
     * * เป็นตัวแปรที่เก็บผลลัพธ์ของแต่ละบรรทัดว่าเข้าหรือไหม
     */
    public function getResults(): array
    {
        return $this->result;
    }
}
