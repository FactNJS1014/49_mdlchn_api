<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;



class ApiInsertController extends Controller
{
    /**
     * @OA\Post(
     *     path="/oprform",
     *     summary="Save form data",
     *     tags={"Form"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="prs", type="string"),
     *              @OA\Property(property="ecn", type="string"),
     *              @OA\Property(property="ecn_num", type="string"),
     *              @OA\Property(property="ecn_rev", type="string"),
     *              @OA\Property(property="date_rec", type="string"),
     *              @OA\Property(property="line", type="string"),
     *              @OA\Property(property="issue_no", type="string"),
     *              @OA\Property(property="status", type="string"),
     *              @OA\Property(property="lots", type="number"),
     *              @OA\Property(property="prog_name", type="string"),
     *              @OA\Property(property="prog_rev", type="string"),
     *              @OA\Property(property="empno", type="string"),
     *              @OA\Property(property="cur_modelname", type="string"),
     *              @OA\Property(property="chn_modelname", type="string"),
     *              @OA\Property(property="won_cur", type="string"),
     *              @OA\Property(property="won_chn", type="string"),
     *              @OA\Property(property="lots_chn", type="number"),
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *     )
     * )
     */

    public function InsertOPRForm(Request $request)
    {
        $data = $request->all();

        $Ym = date('Ym');
        $oprID = '';

        $date_rec = date('Y-m-d');
        $dateTime = date('Y-m-d H:i:s');

        try {
            DB::beginTransaction();

            $findPrevious = DB::table('OPR_HREC_TBL')
                ->select('OPR_HREC_ID')
                ->orderBy('OPR_HREC_ID', 'DESC')
                ->get();

            if (empty($findPrevious[0])) {
                $oprID = 'OPRF-' . $Ym . '-000001';
            } else {
                $oprID = AutogenerateKey('OPRF', $findPrevious[0]->OPR_HREC_ID);
            }

            $Ym = date('Ym');
            $issue_id = '';

            $findPrevious = DB::table('OPR_HREC_TBL')
                ->select('OPR_HREC_ID')
                ->orderBy('OPR_HREC_ID', 'DESC')
                ->get();

            if (empty($findPrevious[0])) {
                $issue_id = 'MCRS-' . $Ym . '-000001';
            } else {
                $issue_id = AutogenerateKey('MCRS', $findPrevious[0]->OPR_HREC_ID);
            }

            $insert = [
                'OPR_HREC_ID' => $oprID,
                'OPR_HREC_EMPNO' => $data['empno'],
                'OPR_HREC_PROCS' => $data['prs'],
                'OPR_HREC_LINE' => $data['line'],
                'OPR_HREC_DATEREC' => $date_rec,
                'OPR_HREC_ISSUENO' => $issue_id,
                'OPR_HREC_STATUSMDL' => $data['status'],
                'OPR_HREC_WON_CURRENT' => $data['won_cur'],
                'OPR_HREC_CURMDLNM' => $data['cur_modelname'],
                'OPR_HREC_WON_CHANGE' => $data['won_chn'],
                'OPR_HREC_CHNMDLNM' => $data['chn_modelname'],
                'OPR_HREC_LOTS' => $data['lots'],
                'OPR_HREC_CONTECN' => $data['ecn'],
                'OPR_HREC_HAVECONTECN_NO' => $data['ecn_num'],
                'OPR_HREC_HAVECONTECN_REV' => $data['ecn_rev'],
                'OPR_HREC_PRGMNM' => $data['prog_name'],
                'OPR_HREC_PRGMREV' => $data['prog_rev'],
                'OPR_HREC_STD' => 0,
                'OPR_HREC_UPDATESTD' => 0,
                'OPR_HREC_LSTDT' => $dateTime,
                'OPR_HREC_UPDATELSTDT' => null,
                'OPR_HREC_LOTS_CHN' => $data['lots_chn'],
                'OPR_HREC_SENDAPP_STD' => 0,
                'OPR_HREC_CHOOSE' => 0,
                'OPR_HREC_CUS' => $data['customer'],
                'OPR_HREC_PROCSCP' => $data['procs_cp'],
                'OPR_HREC_PROCSCP_CHN' => $data['procs_cp_chn'],
                'OPR_HREC_OPRATOR_RJ_STD' => 0,
                'OPR_HREC_TECH_RJ_STD' => 0,
                'OPR_HREC_RM_STD' => 0,
                'OPR_HREC_REVNO' => $data['rev_no'],

            ];

            $insert2 = DB::table('OPR_HREC_TBL')
                ->insert($insert);

            if ($insert2) {
                Log::info("message", [
                    'data' => $insert,
                    'OPR_HREC_ID' => $oprID
                ]);
                DB::commit();
                return response()->json([
                    'status' => true,
                    'data' => $data
                ]);
            } else {
                DB::rollBack();
                Log::error("ApiInsertController > InsertOPRForm > error:can't insert OPR_HREC_TBL-1 " . $insert);
                return response()->json(['status' => false, 'message' => 'Error: บันทึกไม่สำเร็จ'], 500);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("ApiInsertController > InsertOPRForm > error:insert : " . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/oprform/rf",
     *     summary="Save form data",
     *     tags={"Form"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="prs", type="string"),
     *              @OA\Property(property="procs_rf", type="string"),
     *              @OA\Property(property="ecn", type="string"),
     *              @OA\Property(property="ecn_num", type="string"),
     *              @OA\Property(property="ecn_rev", type="string"),
     *              @OA\Property(property="date_rec", type="string"),
     *              @OA\Property(property="line", type="string"),
     *              @OA\Property(property="issue_no", type="string"),
     *              @OA\Property(property="status", type="string"),
     *              @OA\Property(property="lots", type="number"),
     *              @OA\Property(property="prog_name", type="string"),
     *              @OA\Property(property="prog_rev", type="string"),
     *              @OA\Property(property="empno", type="string"),
     *              @OA\Property(property="cur_modelname", type="string"),
     *              @OA\Property(property="chn_modelname", type="string"),
     *              @OA\Property(property="won_cur", type="string"),
     *              @OA\Property(property="won_chn", type="string"),
     *              @OA\Property(property="lots_chn", type="number"),
     *              @OA\Property(property="procs_rf_chn", type="string"),
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *     )
     * )
     */

    public function InsertOPRFormRF(Request $request)
    {
        $data = $request->all();

        $Ym = date('Ym');
        $oprID = '';

        $date_rec = date('Y-m-d');
        $dateTime = date('Y-m-d H:i:s');

        try {
            DB::beginTransaction();
            $findPrevious = DB::table('OPR_HREC_TBL')
                ->select('OPR_HREC_ID')
                ->orderBy('OPR_HREC_ID', 'DESC')
                ->get();

            if (empty($findPrevious[0])) {
                $oprID = 'OPRF-' . $Ym . '-000001';
            } else {
                $oprID = AutogenerateKey('OPRF', $findPrevious[0]->OPR_HREC_ID);
            }

            $Ym = date('Ym');
            $issue_id = '';

            $findPrevious = DB::table('OPR_HREC_TBL')
                ->select('OPR_HREC_ID')
                ->orderBy('OPR_HREC_ID', 'DESC')
                ->get();

            if (empty($findPrevious[0])) {
                $issue_id = 'MCRS' . $Ym . '-000001';
            } else {
                $issue_id = AutogenerateKey('MCRS', $findPrevious[0]->OPR_HREC_ID);
            }

            $insert = [
                'OPR_HREC_ID' => $oprID,
                'OPR_HREC_EMPNO' => $data['empno'],
                'OPR_HREC_PROCS' => $data['prs'],
                'OPR_HREC_PROCS_RF' => $data['procs_rf'],
                'OPR_HREC_LINE' => $data['line'],
                'OPR_HREC_DATEREC' => $date_rec,
                'OPR_HREC_ISSUENO' => $issue_id,
                'OPR_HREC_STATUSMDL' => $data['status'],
                'OPR_HREC_WON_CURRENT' => $data['won_cur'],
                'OPR_HREC_CURMDLNM' => $data['cur_modelname'],
                'OPR_HREC_WON_CHANGE' => $data['won_chn'],
                'OPR_HREC_CHNMDLNM' => $data['chn_modelname'],
                'OPR_HREC_LOTS' => $data['lots'],
                'OPR_HREC_CONTECN' => $data['ecn'],
                'OPR_HREC_HAVECONTECN_NO' => $data['ecn_num'],
                'OPR_HREC_HAVECONTECN_REV' => $data['ecn_rev'],
                'OPR_HREC_PRGMNM' => $data['prog_name'],
                'OPR_HREC_PRGMREV' => $data['prog_rev'],
                'OPR_HREC_STD' => 0,
                'OPR_HREC_UPDATESTD' => 0,
                'OPR_HREC_LSTDT' => $dateTime,
                'OPR_HREC_UPDATELSTDT' => null,
                'OPR_HREC_SENDAPP_STD' => 0,
                'OPR_HREC_CHOOSE' => 0,
                'OPR_HREC_LOTS_CHN' => $data['lots_chn'],
                'OPR_HREC_PROCS_RF_CHN' => $data['procs_rf_chn'],
                'OPR_HREC_CUS' => $data['customer'],
                'OPR_HREC_OPRATOR_RJ_STD' => 0,
                'OPR_HREC_TECH_RJ_STD' => 0,
                'OPR_HREC_RM_STD' => 0,
                'OPR_HREC_REVNO' => $data['rev_no'],

            ];

            DB::table('OPR_HREC_TBL')
                ->insert($insert);
            DB::commit();

            Log::info("message", [
                'data' => $insert,
                'OPR_HREC_ID' => $oprID
            ]);

            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error("ApiInsertController > InsertOPRFormRF > error:begin transaction : " . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/cpinsert",
     *     summary="Create a new Technichian form process CP",
     *     tags={"Technichian Form"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *                @OA\Property(property="pitch", type="string", example="1.0"),
     *                @OA\Property(property="etc_details", type="string", example="Additional details"),
     *                @OA\Property(property="prg_nm", type="string", example="Program A"),
     *                @OA\Property(property="glu_num", type="string", example="GLU123"),
     *                @OA\Property(property="headunit", type="string", example="HU-01"),
     *                @OA\Property(property="bpst_detail", type="string", example="BPST detail info"),
     *                @OA\Property(property="prg2_nm", type="string", example="Program B"),
     *                @OA\Property(property="glu2_num", type="string", example="GLU456"),
     *                @OA\Property(property="headunit2", type="string", example="HU-02"),
     *                @OA\Property(property="bpst2_detail", type="string", example="BPST2 detail info"),
     *                @OA\Property(property="load_inp", type="string", example="LOAD-IN"),
     *                @OA\Property(property="cln_inp", type="string", example="CLEAN-IN"),
     *                @OA\Property(property="func", type="string", example="Function name"),
     *                @OA\Property(property="stack_inp", type="string", example="STACK-IN"),
     *                @OA\Property(property="trace_inp", type="string", example="TRACE-IN"),
     *                @OA\Property(property="confirm_bpst", type="string", example="Confirmed"),
     *                @OA\Property(property="glu_inp", type="string", example="GLU-INP"),
     *                @OA\Property(property="confirm_bpst2", type="string", example="Confirmed 2"),
     *                @OA\Property(property="glu2_inp", type="string", example="GLU2-INP"),
     *                @OA\Property(property="mounter_inp", type="string", example="Mounter Input 1"),
     *                @OA\Property(property="mounter2_inp", type="string", example="Mounter Input 2"),
     *                @OA\Property(property="mounter3_inp", type="string", example="Mounter Input 3"),
     *                @OA\Property(property="mounter4_inp", type="string", example="Mounter Input 4"),
     *                @OA\Property(property="prg_mount1", type="string", example="Program Mount 1"),
     *                @OA\Property(property="noz_mount1", type="string", example="Nozzle 1"),
     *                @OA\Property(property="sup_mount1", type="string", example="Supplier 1"),
     *                @OA\Property(property="prg_mount2", type="string", example="Program Mount 2"),
     *                @OA\Property(property="noz_mount2", type="string", example="Nozzle 2"),
     *                @OA\Property(property="sup_mount2", type="string", example="Supplier 2"),
     *                @OA\Property(property="prg_mount3", type="string", example="Program Mount 3"),
     *                @OA\Property(property="noz_mount3", type="string", example="Nozzle 3"),
     *                @OA\Property(property="sup_mount3", type="string", example="Supplier 3"),
     *                @OA\Property(property="prg_mount4", type="string", example="Program Mount 4"),
     *                @OA\Property(property="noz_mount4", type="string", example="Nozzle 4"),
     *                @OA\Property(property="sup_mount4", type="string", example="Supplier 4"),
     *                @OA\Property(property="mount_inps", type="string", example="Mount Inputs"),
     *                @OA\Property(property="prg_inspct", type="string", example="Inspection Program"),
     *                @OA\Property(property="reflow_std", type="string", example="Reflow Standard"),
     *                @OA\Property(property="prg_reflow", type="string", example="Reflow Program"),
     *                @OA\Property(property="oxygen_reflow_std", type="string", example="Oxygen Standard"),
     *                @OA\Property(property="oxyen_use", type="string", example="Used"),
     *                @OA\Property(property="sup_reflow_std", type="string", example="Supplier Reflow"),
     *                @OA\Property(property="temp_std", type="string", example="Temperature 250C"),
     *                @OA\Property(property="cooling_std", type="string", example="Cooling Standard"),
     *                @OA\Property(property="auto_inps", type="string", example="Auto Inputs"),
     *                @OA\Property(property="prg_auto", type="string", example="Auto Program"),
     *                @OA\Property(property="ng_stock_std", type="string", example="NG Stock Standard"),
     *                @OA\Property(property="ng_stock_pitch", type="string", example="Pitch 10mm"),
     *                @OA\Property(property="trace_inp_std", type="string", example="Trace Standard"),
     *                @OA\Property(property="unloader_std", type="string", example="Unloader Standard"),
     *                @OA\Property(property="unloader_pitch", type="string", example="Pitch 20mm")
     *
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Data inserted successfully"
     *     )
     * )
     */

    public function InsertTechCPForm(Request $request)
    {
        $data = $request->all();
        $Ym = date('Ym');
        $tec_cp_id = '';

        $dateTime = date('Y-m-d H:i:s');

        $findPrevious = DB::table('TEC_CP_HREC_TBL')
            ->select('TEC_CPHREC_ID')
            ->orderBy('TEC_CPHREC_ID', 'DESC')
            ->get();

        if (empty($findPrevious[0])) {
            $tec_cp_id = 'TECP-' . $Ym . '-000001';
        } else {
            $tec_cp_id = AutogenerateKey('TECP', $findPrevious[0]->TEC_CPHREC_ID);
        }




        $insert = [
            'TEC_CPHREC_ID' => $tec_cp_id,
            'OPR_HREC_ID' => $data['id'],
            'TEC_CPHREC_EMPNO' => $data['empno'],
            'TEC_CPHREC_LOADINP' => $data['load_inp'],
            'TEC_CPHREC_LOADINPPITCH' => $data['pitch'],
            'TEC_CPHREC_STACK' => $data['stack_inp'],
            'TEC_CPHREC_TRACEINP' => $data['trace_inp'],
            'TEC_CPHREC_PCBCLEAN' => $data['cln_inp'],
            'TEC_CPHREC_PCBCLNFUNC' =>
            is_array($data['func'] ?? null) && count($data['func'])
                ? implode(', ', $data['func'])
                : '',

            'TEC_CPHREC_GLUE' => $data['glu_inp'],
            'TEC_CPHREC_GLUEPROG' => $data['prg_nm'],
            'TEC_CPHREC_GLUENUM' => $data['glu_num'],
            'TEC_CPHREC_GLUEHUNIT' =>
            is_array($data['headunit'] ?? null) && count($data['headunit'])
                ? implode(', ', $data['headunit'])
                : '',
            'TEC_CPHREC_GLUESTDNOT' => $data['bpst_detail'],
            'TEC_CPHREC_GLUESTDOK' => $data['confirm_bpst'],
            'TEC_CPHREC_GLUESND' => $data['glu2_inp'],
            'TEC_CPHREC_GLUESNDPROG' => $data['prg2_nm'],
            'TEC_CPHREC_GLUESNDNUM' => $data['glu2_num'],
            'TEC_CPHREC_GLUESNDHUNIT' =>
            is_array($data['headunit2'] ?? null) && count($data['headunit2'])
                ? implode(', ', $data['headunit2'])
                : '',
            'TEC_CPHREC_GLUESNDNOT' => $data['bpst2_detail'],
            'TEC_CPHREC_GLUESNDOK' => $data['confirm_bpst2'],
            'TEC_CPHREC_MNTF' => $data['mounter_inp'],
            'TEC_CPHREC_MNTFPROG' => $data['prg_mount1'],
            'TEC_CPHREC_MNTFNOZ' => $data['noz_mount1'],
            'TEC_CPHREC_MNTFSUPT' => $data['sup_mount1'],
            'TEC_CPHREC_MNTSN' => $data['mounter2_inp'],
            'TEC_CPHREC_MNTSNPROG' => $data['prg_mount2'],
            'TEC_CPHREC_MNTSNNOZ' => $data['noz_mount2'],
            'TEC_CPHREC_MNTSNSUPT' => $data['sup_mount2'],
            'TEC_CPHREC_MNTTR' => $data['mounter3_inp'],
            'TEC_CPHREC_MNTTRPROG' => $data['prg_mount3'],
            'TEC_CPHREC_MNTTRNOZ' => $data['noz_mount3'],
            'TEC_CPHREC_MNTTRSUPT' => $data['sup_mount3'],
            'TEC_CPHREC_MNTFO' => $data['mounter4_inp'],
            'TEC_CPHREC_MNTFOPROG' => $data['prg_mount4'],
            'TEC_CPHREC_MNTFONOZ' => $data['noz_mount4'],
            'TEC_CPHREC_MNTFOSUPT' => $data['sup_mount4'],
            'TEC_CPHREC_MNTINSP' => $data['mount_inps'],
            'TEC_CPHREC_MNTINSPPROG' => $data['prg_inspct'],
            'TEC_CPHREC_REFLOW' => $data['reflow_std'],
            'TEC_CPHREC_REFLPROG' => $data['prg_reflow'],
            'TEC_CPHREC_REFLOXYGEN' => $data['oxygen_reflow_std'],
            'TEC_CPHREC_REFLUSEOO' => $data['oxygen_use'],
            'TEC_CPHREC_REFLPCBSUPT' => $data['sup_reflow_std'],
            'TEC_CPHREC_TOP_TEMPCH1' => $data['temp_top_ch1'],
            'TEC_CPHREC_TOP_TEMPCH2' => $data['temp_top_ch2'],
            'TEC_CPHREC_TOP_TEMPCH3' => $data['temp_top_ch3'],
            'TEC_CPHREC_TOP_TEMPCH4' => $data['temp_top_ch4'],
            'TEC_CPHREC_TOP_TEMPCH5' => $data['temp_top_ch5'],
            'TEC_CPHREC_TOP_TEMPCH6' => $data['temp_top_ch6'],
            'TEC_CPHREC_TOP_TEMPCH7' => $data['temp_top_ch7'],
            'TEC_CPHREC_TOP_TEMPCH8' => $data['temp_top_ch8'],
            'TEC_CPHREC_TOP_TEMPCH9' => $data['temp_top_ch9'],
            'TEC_CPHREC_TOP_TEMPCH10' => $data['temp_top_ch10'],
            'TEC_CPHREC_BTM_TEMPCH1' => $data['temp_btm_ch1'],
            'TEC_CPHREC_BTM_TEMPCH2' => $data['temp_btm_ch2'],
            'TEC_CPHREC_BTM_TEMPCH3' => $data['temp_btm_ch3'],
            'TEC_CPHREC_BTM_TEMPCH4' => $data['temp_btm_ch4'],
            'TEC_CPHREC_BTM_TEMPCH5' => $data['temp_btm_ch5'],
            'TEC_CPHREC_BTM_TEMPCH6' => $data['temp_btm_ch6'],
            'TEC_CPHREC_BTM_TEMPCH7' => $data['temp_btm_ch7'],
            'TEC_CPHREC_BTM_TEMPCH8' => $data['temp_btm_ch8'],
            'TEC_CPHREC_BTM_TEMPCH9' => $data['temp_btm_ch9'],
            'TEC_CPHREC_BTM_TEMPCH10' => $data['temp_btm_ch10'],
            'TEC_CPHREC_CONV_SPD' => (float) str_replace(',', '.', $data['temp_conveyor_speed']),

            'TEC_CPHREC_PCBCOOL' => $data['cooling_std'],
            'TEC_CPHREC_AUTO' => $data['auto_inps'],
            'TEC_CPHREC_AUTOPROG' => $data['prg_auto'],
            'TEC_CPHREC_NGSTCK' => $data['ng_stock_std'],
            'TEC_CPHREC_NGSTCKPITCH' => $data['ng_stock_pitch'],
            'TEC_CPHREC_TRACE' => $data['trace_inp_std'],
            'TEC_CPHREC_UNLOADER' => $data['unloader_std'],
            'TEC_CPHREC_UNLOADERPITCH' => $data['unloader_pitch'],
            'TEC_CPHREC_PCBLNETC_DTL' => $data['etc_details'],
            'TEC_CPHREC_STARTMCHN' => $data['start_machine'],
            'TEC_CPHREC_FINISHMCHN' => $data['finish_machine'],
            'TEC_CPHREC_STD' => 1,
            'TEC_CPHREC_LSTDT' => $dateTime,
        ];
        // dd($data);

        DB::table('TEC_CP_HREC_TBL')
            ->insert($insert);

        $choose_cp = [
            'OPR_HREC_CHOOSE' => 1
        ];

        Log::info('cp insert', $insert);

        DB::table('OPR_HREC_TBL')
            ->where('OPR_HREC_ID', $data['id'])
            ->update($choose_cp);

        return response()->json(['status' => true]);
    }

    /**
     * @OA\Post(
     *   path="/rfinsert",
     *   summary="Create a new Technichian form process RF",
     *   tags={"Technichian Form"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="load_inp", type="string"),
     *       @OA\Property(property="pitch_rf", type="string"),
     *       @OA\Property(property="cln_inp", type="string"),
     *       @OA\Property(property="func", type="string"),
     *       @OA\Property(property="stack_inp", type="string"),
     *       @OA\Property(property="trace_inp", type="string"),
     *       @OA\Property(property="etc_details", type="string"),
     *       @OA\Property(property="printer_std", type="string"),
     *       @OA\Property(property="printer_prg", type="string"),
     *       @OA\Property(property="metalmask", type="string"),
     *       @OA\Property(property="ref_mm", type="string"),
     *       @OA\Property(property="squee", type="string"),
     *       @OA\Property(property="sup_rf", type="string"),
     *       @OA\Property(property="solder", type="string"),
     *       @OA\Property(property="glue_rf", type="string"),
     *       @OA\Property(property="glue_prg", type="string"),
     *       @OA\Property(property="glue_num", type="string"),
     *       @OA\Property(property="solder_use", type="string"),
     *       @OA\Property(property="solder_prgnm", type="string"),
     *       @OA\Property(property="mounter_ref_inp", type="string"),
     *       @OA\Property(property="prg_ref_mount1", type="string"),
     *       @OA\Property(property="noz_ref_mount1", type="string"),
     *       @OA\Property(property="sup_ref_mount1", type="string"),
     *       @OA\Property(property="mounter2_ref_inp", type="string"),
     *       @OA\Property(property="prg_ref_mount2", type="string"),
     *       @OA\Property(property="noz_ref_mount2", type="string"),
     *       @OA\Property(property="sup_ref_mount2", type="string"),
     *       @OA\Property(property="mounter3_ref_inp", type="string"),
     *       @OA\Property(property="prg_ref_mount3", type="string"),
     *       @OA\Property(property="noz_ref_mount3", type="string"),
     *       @OA\Property(property="sup_ref_mount3", type="string"),
     *       @OA\Property(property="mounter4_ref_inp", type="string"),
     *       @OA\Property(property="prg_ref_mount4", type="string"),
     *       @OA\Property(property="noz_ref_mount4", type="string"),
     *       @OA\Property(property="sup_ref_mount4", type="string"),
     *       @OA\Property(property="mounter_ref_inps", type="string"),
     *       @OA\Property(property="prg_ref_inspct", type="string"),
     *       @OA\Property(property="reflow_rf_std", type="string"),
     *       @OA\Property(property="prg_rf_reflow", type="string"),
     *       @OA\Property(property="oxygen_rf_reflow_std", type="string"),
     *       @OA\Property(property="oxyen_rf_use", type="string"),
     *       @OA\Property(property="sup_rf_reflow_std", type="string"),
     *       @OA\Property(property="temp_rf_std", type="string"),
     *       @OA\Property(property="cooling_rf_std", type="string"),
     *       @OA\Property(property="auto_rf_inps", type="string"),
     *       @OA\Property(property="prg_rf_auto", type="string"),
     *       @OA\Property(property="ng_stock_rf_std", type="string"),
     *       @OA\Property(property="ng_stock_rf_pitch", type="string"),
     *       @OA\Property(property="trace_rf_inp_std", type="string"),
     *       @OA\Property(property="unloader_rf_std", type="string"),
     *       @OA\Property(property="unloader_rf_pitch", type="string"),
     *       @OA\Property(property="empno", type="string")
     *     )
     *   ),
     *   @OA\Response(response=200, description="Insert success"),
     *   @OA\Response(response=400, description="Bad request")
     * )
     */


    public function InsertTechRFForm(Request $request)
    {
        try {

            $Ym = date('Ym');
            $tec_rf_id = '';

            $dateTime = date('Y-m-d H:i:s');

            $findPrevious = DB::table('TEC_RF_HREC_TBL')
                ->select('TEC_RFHREC_ID')
                ->orderBy('TEC_RFHREC_ID', 'DESC')
                ->get();

            if (empty($findPrevious[0])) {
                $tec_rf_id = 'TERF-' . $Ym . '-000001';
            } else {
                $tec_rf_id = AutogenerateKey('TERF', $findPrevious[0]->TEC_RFHREC_ID);
            }



            $payload = $request->except('pin_image');

            $request->validate([
                'pin_image.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'ref_mm' => 'required|string',
            ]);

            $uploadedImages = [];

            if ($request->hasFile('pin_image')) {

                $files = $request->file('pin_image');
                $files = is_array($files) ? $files : [$files];

                // สร้าง folder ถ้ายังไม่มี
                $destinationPath = public_path('images');

                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0777, true);
                }

                foreach ($files as $file) {

                    if ($file->isValid()) {
                        $extension = $file->extension();
                        $fileName = 'IMG-' . $Ym . '-' . uniqid() . '.' . $extension;

                        $file->move($destinationPath, $fileName);

                        $uploadedImages[] = $fileName;
                    }
                }
            }

            // If multiple images were uploaded, return them as an array,
            // otherwise returning as a single string (or null) to maintain compatibility if possible
            $payload['pin_image'] = count($uploadedImages) === 1 ? $uploadedImages[0] : (empty($uploadedImages) ? null : $uploadedImages);


            $insert = [
                'TEC_RFHREC_ID' => $tec_rf_id,
                'OPR_HREC_ID' => $payload['id'],
                'TEC_RFHREC_EMPNO' => $payload['empno'],
                'TEC_RFHREC_LOADINP' => $payload['load_inp'],
                'TEC_RFHREC_LOADINPPITCH' => $payload['pitch_rf'],
                'TEC_RFHREC_STACK' => $payload['stack_inp'],
                'TEC_RFHREC_TRACEINP' => $payload['trace_inp'],
                'TEC_RFHREC_PCBCLEAN' => $payload['cln_inp'],
                'TEC_RFHREC_PCBCLNFUNC' => $payload['func'],
                // 'TEC_RFHREC_PCBCLNFUNC' =>  is_array($payload['func'] ?? null) && count($payload['func'])
                //     ? implode(', ', $payload['func'])
                //     : '',
                'TEC_RFHREC_PRINT' => $payload['printer_std'],
                'TEC_RFHREC_PRINTPROG' => $payload['printer_prg'],
                // 'TEC_RFHREC_PRINTMM' => $data['metalmask'],
                'TEC_RFHREC_PRINTMMREF' => $payload['ref_mm'],
                'TEC_RFHREC_PRINTSQUE' => $payload['squee'],
                'TEC_RFHREC_PRINTSUPT' => $payload['sup_rf'],
                'TEC_RFHREC_PRINTSOLDER' => $payload['solder'],
                'TEC_RFHREC_GLUE' => $payload['glue_rf'],
                'TEC_RFHREC_GLUEPROG' => $payload['glue_prg'],
                'TEC_RFHREC_GLUENUM' => $payload['glue_num'],
                'TEC_RFHREC_SOLDERINSP' => $payload['solder_use'],
                'TEC_RFHREC_SOLDERPROG' => $payload['solder_prgnm'],
                'TEC_RFHREC_MNTF' => $payload['mounter_ref_inp'],
                'TEC_RFHREC_MNTFPROG' => $payload['prg_ref_mount1'],
                'TEC_RFHREC_MNTFNOZ' => $payload['noz_ref_mount1'],
                'TEC_RFHREC_MNTFSUPT' => $payload['sup_ref_mount1'],
                'TEC_RFHREC_MNTSN' => $payload['mounter2_ref_inp'],
                'TEC_RFHREC_MNTSNPROG' => $payload['prg_ref_mount2'],
                'TEC_RFHREC_MNTSNNOZ' => $payload['noz_ref_mount2'],
                'TEC_RFHREC_MNTSNSUPT' => $payload['sup_ref_mount2'],
                'TEC_RFHREC_MNTTR' => $payload['mounter3_ref_inp'],
                'TEC_RFHREC_MNTTRPROG' => $payload['prg_ref_mount3'],
                'TEC_RFHREC_MNTTRNOZ' => $payload['noz_ref_mount3'],
                'TEC_RFHREC_MNTTRSUPT' => $payload['sup_ref_mount3'],
                'TEC_RFHREC_MNTFO' => $payload['mounter4_ref_inp'],
                'TEC_RFHREC_MNTFOPROG' => $payload['prg_ref_mount4'],
                'TEC_RFHREC_MNTFONOZ' => $payload['noz_ref_mount4'],
                'TEC_RFHREC_MNTFOSUPT' => $payload['sup_ref_mount4'],
                'TEC_RFHREC_MNTINSP' => $payload['mounter_ref_inps'],
                'TEC_RFHREC_MNTINSPPROG' => $payload['prg_ref_inspct'],
                'TEC_RFHREC_REFLOW' => $payload['reflow_rf_std'],
                'TEC_RFHREC_REFLPROG' => $payload['prg_rf_reflow'],
                'TEC_RFHREC_REFLOXYGEN' => $payload['oxygen_rf_reflow_std'],
                'TEC_RFHREC_REFLOO' => $payload['oxyen_rf_use'],
                'TEC_RFHREC_REFLPCBSUPT' => $payload['sup_rf_reflow_std'],
                'TEC_RFHREC_TOP_TEMPCH1' => $payload['temp_rf_top_ch1'],
                'TEC_RFHREC_TOP_TEMPCH2' => $payload['temp_rf_top_ch2'],
                'TEC_RFHREC_TOP_TEMPCH3' => $payload['temp_rf_top_ch3'],
                'TEC_RFHREC_TOP_TEMPCH4' => $payload['temp_rf_top_ch4'],
                'TEC_RFHREC_TOP_TEMPCH5' => $payload['temp_rf_top_ch5'],
                'TEC_RFHREC_TOP_TEMPCH6' => $payload['temp_rf_top_ch6'],
                'TEC_RFHREC_TOP_TEMPCH7' => $payload['temp_rf_top_ch7'],
                'TEC_RFHREC_TOP_TEMPCH8' => $payload['temp_rf_top_ch8'],
                'TEC_RFHREC_TOP_TEMPCH9' => $payload['temp_rf_top_ch9'],
                'TEC_RFHREC_TOP_TEMPCH10' => $payload['temp_rf_top_ch10'],
                'TEC_RFHREC_BTM_TEMPCH1' => $payload['temp_rf_btm_ch1'],
                'TEC_RFHREC_BTM_TEMPCH2' => $payload['temp_rf_btm_ch2'],
                'TEC_RFHREC_BTM_TEMPCH3' => $payload['temp_rf_btm_ch3'],
                'TEC_RFHREC_BTM_TEMPCH4' => $payload['temp_rf_btm_ch4'],
                'TEC_RFHREC_BTM_TEMPCH5' => $payload['temp_rf_btm_ch5'],
                'TEC_RFHREC_BTM_TEMPCH6' => $payload['temp_rf_btm_ch6'],
                'TEC_RFHREC_BTM_TEMPCH7' => $payload['temp_rf_btm_ch7'],
                'TEC_RFHREC_BTM_TEMPCH8' => $payload['temp_rf_btm_ch8'],
                'TEC_RFHREC_BTM_TEMPCH9' => $payload['temp_rf_btm_ch9'],
                'TEC_RFHREC_BTM_TEMPCH10' => $payload['temp_rf_btm_ch10'],
                'TEC_RFHREC_PCBCOOL' => $payload['cooling_rf_std'],
                'TEC_RFHREC_AUTO' => $payload['auto_rf_inps'],
                'TEC_RFHREC_AUTOPROG' => $payload['prg_rf_auto'],
                'TEC_RFHREC_NGSTCK' => $payload['ng_stock_rf_std'],
                'TEC_RFHREC_NGSTCKPITCH' => $payload['ng_stock_rf_pitch'],
                'TEC_RFHREC_TRACE' => $payload['trace_rf_inp_std'],
                'TEC_RFHREC_UNLOADER' => $payload['unloader_rf_std'],
                'TEC_RFHREC_UNLOADERPITCH' => $payload['unloader_rf_pitch'],
                'TEC_RFHREC_CONV_SPD' => (float) str_replace(',', '.', $payload['temp_rf_conveyor_speed']),
                'TEC_RFHREC_STD' => 1,
                'TEC_RFHREC_LSTDT' => $dateTime,
                'TEC_RFHREC_PCBLNETC' => $payload['etc_details'],
                'TEC_RFHREC_STARTMCHN' => $payload['start_machine'],
                'TEC_RFHREC_FINISHMCHN' => $payload['finish_machine'],
                'TEC_RFHREC_PINPIC' => $payload['pin_image'],
            ];
            // dd($insert);
            DB::beginTransaction();

            DB::table('TEC_RF_HREC_TBL')
                ->insert($insert);


            $choose_rf = [
                'OPR_HREC_CHOOSE' => 1
            ];

            DB::table('OPR_HREC_TBL')
                ->where('OPR_HREC_ID', $payload['id'])
                ->update($choose_rf);

            DB::commit();
            Log::info("ApiInsertController > InsertTechRFForm > data:" . json_encode($insert));
            return response()->json([
                'status' => true,
                'message' => 'Data inserted successfully',
                'data' => $insert,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("ApiInsertController > InsertTechRFForm > error:insert : " . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *   path="/rfinsert-test",
     *   summary="Create a new Technichian form process RF",
     *   tags={"Technichian Form"},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="load_inp", type="string"),
     *       @OA\Property(property="pitch_rf", type="string"),
     *       @OA\Property(property="cln_inp", type="string"),
     *       @OA\Property(property="func", type="string"),
     *       @OA\Property(property="stack_inp", type="string"),
     *       @OA\Property(property="trace_inp", type="string"),
     *       @OA\Property(property="etc_details", type="string"),
     *       @OA\Property(property="printer_std", type="string"),
     *       @OA\Property(property="printer_prg", type="string"),
     *       @OA\Property(property="metalmask", type="string"),
     *       @OA\Property(property="ref_mm", type="string"),
     *       @OA\Property(property="squee", type="string"),
     *       @OA\Property(property="sup_rf", type="string"),
     *       @OA\Property(property="solder", type="string"),
     *       @OA\Property(property="glue_rf", type="string"),
     *       @OA\Property(property="glue_prg", type="string"),
     *       @OA\Property(property="glue_num", type="string"),
     *       @OA\Property(property="solder_use", type="string"),
     *       @OA\Property(property="solder_prgnm", type="string"),
     *       @OA\Property(property="mounter_ref_inp", type="string"),
     *       @OA\Property(property="prg_ref_mount1", type="string"),
     *       @OA\Property(property="noz_ref_mount1", type="string"),
     *       @OA\Property(property="sup_ref_mount1", type="string"),
     *       @OA\Property(property="mounter2_ref_inp", type="string"),
     *       @OA\Property(property="prg_ref_mount2", type="string"),
     *       @OA\Property(property="noz_ref_mount2", type="string"),
     *       @OA\Property(property="sup_ref_mount2", type="string"),
     *       @OA\Property(property="mounter3_ref_inp", type="string"),
     *       @OA\Property(property="prg_ref_mount3", type="string"),
     *       @OA\Property(property="noz_ref_mount3", type="string"),
     *       @OA\Property(property="sup_ref_mount3", type="string"),
     *       @OA\Property(property="mounter4_ref_inp", type="string"),
     *       @OA\Property(property="prg_ref_mount4", type="string"),
     *       @OA\Property(property="noz_ref_mount4", type="string"),
     *       @OA\Property(property="sup_ref_mount4", type="string"),
     *       @OA\Property(property="mounter_ref_inps", type="string"),
     *       @OA\Property(property="prg_ref_inspct", type="string"),
     *       @OA\Property(property="reflow_rf_std", type="string"),
     *       @OA\Property(property="prg_rf_reflow", type="string"),
     *       @OA\Property(property="oxygen_rf_reflow_std", type="string"),
     *       @OA\Property(property="oxyen_rf_use", type="string"),
     *       @OA\Property(property="sup_rf_reflow_std", type="string"),
     *       @OA\Property(property="temp_rf_std", type="string"),
     *       @OA\Property(property="cooling_rf_std", type="string"),
     *       @OA\Property(property="auto_rf_inps", type="string"),
     *       @OA\Property(property="prg_rf_auto", type="string"),
     *       @OA\Property(property="ng_stock_rf_std", type="string"),
     *       @OA\Property(property="ng_stock_rf_pitch", type="string"),
     *       @OA\Property(property="trace_rf_inp_std", type="string"),
     *       @OA\Property(property="unloader_rf_std", type="string"),
     *       @OA\Property(property="unloader_rf_pitch", type="string"),
     *       @OA\Property(property="empno", type="string")
     *     )
     *   ),
     *   @OA\Response(response=200, description="Insert success"),
     *   @OA\Response(response=400, description="Bad request")
     * )
     */


    public function InsertTechRFFormTest(Request $request)
    {
        // $data = $request->all();

        $Ym = date('Ym');
        $tec_rf_id = '';

        $dateTime = date('Y-m-d H:i:s');

        $findPrevious = DB::table('TEC_RF_HREC_TBL')
            ->select('TEC_RFHREC_ID')
            ->orderBy('TEC_RFHREC_ID', 'DESC')
            ->get();

        if (empty($findPrevious[0])) {
            $tec_rf_id = 'TERF-' . $Ym . '-000001';
        } else {
            $tec_rf_id = AutogenerateKey('TERF', $findPrevious[0]->TEC_RFHREC_ID);
        }

        $request->validate([
            'image.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $payload = $request->except('pin_image');

        $request->validate([
            'pin_image.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $uploadedImages = [];

        if ($request->hasFile('pin_image')) {

            $files = $request->file('pin_image');
            $files = is_array($files) ? $files : [$files];

            // สร้าง folder ถ้ายังไม่มี
            $destinationPath = public_path('images');

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            foreach ($files as $file) {

                if ($file->isValid()) {
                    $extension = $file->extension();
                    $fileName = 'IMG-' . $Ym . '-' . uniqid() . '.' . $extension;

                    $file->move($destinationPath, $fileName);

                    $uploadedImages[] = $fileName;
                }
            }
        }

        // If multiple images were uploaded, return them as an array,
        // otherwise returning as a single string (or null) to maintain compatibility if possible
        $payload['pin_image'] = count($uploadedImages) === 1 ? $uploadedImages[0] : (empty($uploadedImages) ? null : $uploadedImages);


        $insert = [
            'TEC_RFHREC_ID' => $tec_rf_id,
            'OPR_HREC_ID' => $payload['id'],
            'TEC_RFHREC_EMPNO' => $payload['empno'],
            'TEC_RFHREC_LOADINP' => $payload['load_inp'],
            'TEC_RFHREC_LOADINPPITCH' => $payload['pitch_rf'],
            'TEC_RFHREC_STACK' => $payload['stack_inp'],
            'TEC_RFHREC_TRACEINP' => $payload['trace_inp'],
            'TEC_RFHREC_PCBCLEAN' => $payload['cln_inp'],
            'TEC_RFHREC_PCBCLNFUNC' =>  is_array($payload['func'] ?? null) && count($payload['func'])
                ? implode(', ', $payload['func'])
                : '',
            'TEC_RFHREC_PRINT' => $payload['printer_std'],
            'TEC_RFHREC_PRINTPROG' => $payload['printer_prg'],
            // 'TEC_RFHREC_PRINTMM' => $data['metalmask'],
            'TEC_RFHREC_PRINTMMREF' => $payload['ref_mm'],
            'TEC_RFHREC_PRINTSQUE' => $payload['squee'],
            'TEC_RFHREC_PRINTSUPT' => $payload['sup_rf'],
            'TEC_RFHREC_PRINTSOLDER' => $payload['solder'],
            'TEC_RFHREC_GLUE' => $payload['glue_rf'],
            'TEC_RFHREC_GLUEPROG' => $payload['glue_prg'],
            'TEC_RFHREC_GLUENUM' => $payload['glue_num'],
            'TEC_RFHREC_SOLDERINSP' => $payload['solder_use'],
            'TEC_RFHREC_SOLDERPROG' => $payload['solder_prgnm'],
            'TEC_RFHREC_MNTF' => $payload['mounter_ref_inp'],
            'TEC_RFHREC_MNTFPROG' => $payload['prg_ref_mount1'],
            'TEC_RFHREC_MNTFNOZ' => $payload['noz_ref_mount1'],
            'TEC_RFHREC_MNTFSUPT' => $payload['sup_ref_mount1'],
            'TEC_RFHREC_MNTSN' => $payload['mounter2_ref_inp'],
            'TEC_RFHREC_MNTSNPROG' => $payload['prg_ref_mount2'],
            'TEC_RFHREC_MNTSNNOZ' => $payload['noz_ref_mount2'],
            'TEC_RFHREC_MNTSNSUPT' => $payload['sup_ref_mount2'],
            'TEC_RFHREC_MNTTR' => $payload['mounter3_ref_inp'],
            'TEC_RFHREC_MNTTRPROG' => $payload['prg_ref_mount3'],
            'TEC_RFHREC_MNTTRNOZ' => $payload['noz_ref_mount3'],
            'TEC_RFHREC_MNTTRSUPT' => $payload['sup_ref_mount3'],
            'TEC_RFHREC_MNTFO' => $payload['mounter4_ref_inp'],
            'TEC_RFHREC_MNTFOPROG' => $payload['prg_ref_mount4'],
            'TEC_RFHREC_MNTFONOZ' => $payload['noz_ref_mount4'],
            'TEC_RFHREC_MNTFOSUPT' => $payload['sup_ref_mount4'],
            'TEC_RFHREC_MNTINSP' => $payload['mounter_ref_inps'],
            'TEC_RFHREC_MNTINSPPROG' => $payload['prg_ref_inspct'],
            'TEC_RFHREC_REFLOW' => $payload['reflow_rf_std'],
            'TEC_RFHREC_REFLPROG' => $payload['prg_rf_reflow'],
            'TEC_RFHREC_REFLOXYGEN' => $payload['oxygen_rf_reflow_std'],
            'TEC_RFHREC_REFLOO' => $payload['oxyen_rf_use'],
            'TEC_RFHREC_REFLPCBSUPT' => $payload['sup_rf_reflow_std'],
            'TEC_RFHREC_TOP_TEMPCH1' => $payload['temp_rf_top_ch1'],
            'TEC_RFHREC_TOP_TEMPCH2' => $payload['temp_rf_top_ch2'],
            'TEC_RFHREC_TOP_TEMPCH3' => $payload['temp_rf_top_ch3'],
            'TEC_RFHREC_TOP_TEMPCH4' => $payload['temp_rf_top_ch4'],
            'TEC_RFHREC_TOP_TEMPCH5' => $payload['temp_rf_top_ch5'],
            'TEC_RFHREC_TOP_TEMPCH6' => $payload['temp_rf_top_ch6'],
            'TEC_RFHREC_TOP_TEMPCH7' => $payload['temp_rf_top_ch7'],
            'TEC_RFHREC_TOP_TEMPCH8' => $payload['temp_rf_top_ch8'],
            'TEC_RFHREC_TOP_TEMPCH9' => $payload['temp_rf_top_ch9'],
            'TEC_RFHREC_TOP_TEMPCH10' => $payload['temp_rf_top_ch10'],
            'TEC_RFHREC_BTM_TEMPCH1' => $payload['temp_rf_btm_ch1'],
            'TEC_RFHREC_BTM_TEMPCH2' => $payload['temp_rf_btm_ch2'],
            'TEC_RFHREC_BTM_TEMPCH3' => $payload['temp_rf_btm_ch3'],
            'TEC_RFHREC_BTM_TEMPCH4' => $payload['temp_rf_btm_ch4'],
            'TEC_RFHREC_BTM_TEMPCH5' => $payload['temp_rf_btm_ch5'],
            'TEC_RFHREC_BTM_TEMPCH6' => $payload['temp_rf_btm_ch6'],
            'TEC_RFHREC_BTM_TEMPCH7' => $payload['temp_rf_btm_ch7'],
            'TEC_RFHREC_BTM_TEMPCH8' => $payload['temp_rf_btm_ch8'],
            'TEC_RFHREC_BTM_TEMPCH9' => $payload['temp_rf_btm_ch9'],
            'TEC_RFHREC_BTM_TEMPCH10' => $payload['temp_rf_btm_ch10'],
            'TEC_RFHREC_PCBCOOL' => $payload['cooling_rf_std'],
            'TEC_RFHREC_AUTO' => $payload['auto_rf_inps'],
            'TEC_RFHREC_AUTOPROG' => $payload['prg_rf_auto'],
            'TEC_RFHREC_NGSTCK' => $payload['ng_stock_rf_std'],
            'TEC_RFHREC_NGSTCKPITCH' => $payload['ng_stock_rf_pitch'],
            'TEC_RFHREC_TRACE' => $payload['trace_rf_inp_std'],
            'TEC_RFHREC_UNLOADER' => $payload['unloader_rf_std'],
            'TEC_RFHREC_UNLOADERPITCH' => $payload['unloader_rf_pitch'],
            'TEC_RFHREC_CONV_SPD' => (float) str_replace(',', '.', $payload['temp_rf_conveyor_speed']),
            'TEC_RFHREC_STD' => 1,
            'TEC_RFHREC_LSTDT' => $dateTime,
            'TEC_RFHREC_PCBLNETC' => $payload['etc_details'],
            'TEC_RFHREC_STARTMCHN' => $payload['start_machine'],
            'TEC_RFHREC_FINISHMCHN' => $payload['finish_machine'],
            'TEC_RFHREC_PINPIC' => $payload['pin_image'],
        ];
        // dd($insert);


        return response()->json(['status' => true, 'insert' => $insert, 'message' => 'success']);
    }


    /**
     * @OA\Post(
     *     path="/insert/master",
     *     summary="Insert master user approve",
     *     tags={"Form"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="levels", type="string"),
     *
     *
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *     )
     * )
     */
    public function MasterApprove(Request $request)
    {
        $data = $request->all();

        $now = now();
        $Ym = date('Ym');
        $masterID = '';



        $findPrevious = DB::table('APP_MASTER_TBL')
            ->select('MASTER_ID')
            ->orderBy('MASTER_ID', 'DESC')
            ->get();

        if (empty($findPrevious[0])) {
            $masterID = 'MASID-' . $Ym . '-000001';
        } else {
            $masterID = AutogenerateKey('MASID', $findPrevious[0]->MASTER_ID);
        }


        DB::beginTransaction();
        try {
            $insertRows = [];
            $dateTime = date('Y-m-d H:i:s');

            foreach ($data['levels'] as $levelBlock) {
                $insertRows[] = [
                    'MASTER_ID' => $masterID,
                    'MASTER_SEQ' => $levelBlock['level'],
                    'MASTER_EMPID' => implode(',', $levelBlock['empnos']), // รวม EMPID ของ seq นั้น
                    'MASTER_STD' => 1,
                    'MASTER_LSTDT' => $dateTime,
                ];
            }


            $existing = DB::table('APP_MASTER_TBL')
                ->where('MASTER_STD', 1)
                ->first();


            if ($existing) {
                // update แถวเดิม
                DB::table('APP_MASTER_TBL')
                    ->where('MASTER_ID', $existing->MASTER_ID)
                    ->update(['MASTER_STD' => 0]);
            }


            // insert แถวใหม่
            DB::table('APP_MASTER_TBL')->insert($insertRows);
            DB::commit();
            Log::info('MasterApprove inserted', ['inserted' => $insertRows]);
            return response()->json([
                'success' => true,
                'message' => 'Approval recipients created.',
                'inserted' => count($insertRows), // จะได้ 2 row ตาม level
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('MasterApprove failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/insert/approve",
     *     summary="Insert approve of users",
     *     tags={"Form"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="OPR_HREC_ID", type="string"),
     *              @OA\Property(property="TEC_CPORRF_HREC_ID", type="string"),
     *              @OA\Property(property="MASTER_ID", type="string"),
     *
     *
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *     )
     * )
     */

    public function InsertAppr(Request $request)
    {
        $data = $request->all();
        $Ym = date('Ym');
        $appsId = '';


        DB::beginTransaction();

        $findPrevious = DB::table('APPREC_H_TBL')
            ->select('APP_REC_ID')
            ->orderBy('APP_REC_ID', 'DESC')
            ->lockForUpdate()
            ->first();


        if (!$findPrevious) {
            $appsId = 'APPR-' . $Ym . '-000001';
        } else {
            $appsId = AutogenerateKey('APPR', $findPrevious->APP_REC_ID);
        }

        try {


            $apps = DB::table('APP_MASTER_TBL')->where('MASTER_STD', 1)->orderBy('MASTER_SEQ', 'ASC')->get();

            if ($apps->isEmpty()) {
                throw new \Exception('Approval master not found');
            }

            $insertRows = [];
            $dateTime = date('Y-m-d H:i:s');
            $currentAppId = '';
            for ($i = 0; $i < sizeof($apps); $i++) {
                $rows = $apps[$i];

                // สร้าง APP_REC_ID ใหม่ตามลำดับ
                if ($i === 0) {
                    $currentAppId = $appsId; // แถวแรกใช้ $appsId ที่คำนวณมาแล้ว
                } else {
                    $currentAppId = AutogenerateKey('APPR', $currentAppId);
                }

                $insertRows[] = [
                    'APP_REC_ID' => $currentAppId,
                    'OPR_HREC_ID' => $data['OPR_HREC_ID'],
                    'TEC_CPORRF_HREC_ID' => $data['TEC_CPHREC_ID'],
                    'APP_REC_EMPNO' => $rows->MASTER_EMPID,
                    'APP_REC_LEVEL' => $rows->MASTER_SEQ,
                    'APP_REC_EMPAPP' => 0,
                    'APP_REC_STD' => 0,
                    'APP_REC_LSTDT' => $dateTime,
                    'APP_REC_REJSTD' => 0,
                    'APP_REC_STAMP_STD' => 0,
                    // 'MASTER_ID' => $data['MASTER_ID'],
                ];
            }

            DB::table('APPREC_H_TBL')->insert($insertRows);



            $db_std_app = DB::table('OPR_HREC_TBL')
                ->where('OPR_HREC_ID', $data['OPR_HREC_ID'])
                ->update([
                    'OPR_HREC_SENDAPP_STD' => 1
                ]);
            if ($db_std_app === 0) {
                throw new \Exception('OPR_HREC_ID not found');
            }


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Approval recipients created successfully',
                'inserted' => count($insertRows)
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/insert/approvr/rf",
     *     summary="Insert approve of users",
     *     tags={"Form"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="OPR_HREC_ID", type="string"),
     *              @OA\Property(property="TEC_CPORRF_HREC_ID", type="string"),
     *              @OA\Property(property="MASTER_ID", type="string"),
     *
     *
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *     )
     * )
     */

    public function InsertApprRF(Request $request)
    {
        $data = $request->all();
        $Ym = date('Ym');
        $appsId = '';

        DB::beginTransaction();
        $findPrevious = DB::table('APPREC_H_TBL')
            ->select('APP_REC_ID')
            ->orderBy('APP_REC_ID', 'DESC')
            ->lockForUpdate()
            ->first();


        if (!$findPrevious) {
            $appsId = 'APPR-' . $Ym . '-000001';
        } else {
            $appsId = AutogenerateKey('APPR', $findPrevious->APP_REC_ID);
        }
        try {



            $apps = DB::table('APP_MASTER_TBL')->where('MASTER_STD', 1)->orderBy('MASTER_SEQ', 'ASC')->get();

            if ($apps->isEmpty()) {
                throw new \Exception('Approval master not found');
            }

            $insertRows = [];
            $dateTime = date('Y-m-d H:i:s');
            $currentAppId = '';
            for ($i = 0; $i < sizeof($apps); $i++) {
                $rows = $apps[$i];

                // สร้าง APP_REC_ID ใหม่ตามลำดับ
                if ($i === 0) {
                    $currentAppId = $appsId; // แถวแรกใช้ $appsId ที่คำนวณมาแล้ว
                } else {
                    $currentAppId = AutogenerateKey('APPR', $currentAppId);
                }

                $insertRows[] = [
                    'APP_REC_ID' => $currentAppId,
                    'OPR_HREC_ID' => $data['OPR_HREC_ID'],
                    'TEC_CPORRF_HREC_ID' => $data['TEC_RFHREC_ID'],
                    'APP_REC_EMPNO' => $rows->MASTER_EMPID,
                    'APP_REC_LEVEL' => $rows->MASTER_SEQ,
                    'APP_REC_EMPAPP' => 0,
                    'APP_REC_STD' => 0,
                    'APP_REC_LSTDT' => $dateTime,
                    'APP_REC_REJSTD' => 0,
                    'APP_REC_STAMP_STD' => 0,
                    // 'MASTER_ID' => $data['MASTER_ID'],
                ];
            }

            $insertAPPREC_H_TBL = DB::table('APPREC_H_TBL')->insert($insertRows);



            $db_std_app = DB::table('OPR_HREC_TBL')
                ->where('OPR_HREC_ID', $data['OPR_HREC_ID'])
                ->update([
                    'OPR_HREC_SENDAPP_STD' => 1
                ]);
            // if ($db_std_app === 0) {
            //     throw new \Exception('OPR_HREC_ID not found');
            // }


            // DB::commit();

            // return response()->json([
            //     'success' => true,
            //     'message' => 'Approval recipients created successfully',
            //     'inserted' => count($insertRows)
            // ]);


            if ($insertAPPREC_H_TBL && $db_std_app !== 0) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Approval recipients created successfully',
                    'inserted' => count($insertRows)
                ]);
            } else {
                DB::rollBack();
                Log::error("ApiInsertController > InsertApprRF > error:can't insert insertAPPREC_H_TBL-1 / update OPR_HREC_TBL ");
                throw new \Exception('OPR_HREC_ID not found');
                return response()->json(['status' => false, 'message' => 'Error: บันทึกไม่สำเร็จ'], 500);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("ApiInsertController > InsertApprRF > error:can't insert / update " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/insert/settings",
     *     summary="Insert Master Submat and Glue",
     *     tags={"Form"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="category", type="string"),
     *              @OA\Property(property="maker", type="string"),
     *              @OA\Property(property="submatnumber", type="string"),
     *              @OA\Property(property="customers", type="string"),
     *
     *
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *     )
     * )
     */

    public function InsertSettings(Request $request)
    {

        $payload = $request->all();

        $dateTime = date('Y-m-d H:i:s');

        $insert_payload = [
            'SAG_CATEGROY' => $payload['category'],
            'SAG_MAKER' => $payload['maker'],
            'SAG_SUBMATNUM' => $payload['submatnum'],
            'SAG_CUS' => $payload['cus'],
            'SAG_STD' => 1,
            'SAG_LSTDT' => $dateTime,
        ];

        DB::table('SAG_MASTER_TBL')
            ->insert($insert_payload);

        return response()->json([
            'std' => true,
            'data' => $insert_payload,
        ]);
    }

    /**
     * @OA\Post(
     *     path="/insert/master",
     *     summary="Insert master user approve",
     *     tags={"Form"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="levels", type="string"),
     *
     *
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *     )
     * )
     */
    public function MasterApprove(Request $request)
    {
        $data = $request->all();

        $now = now();
        $Ym = date('Ym');
        $masterID = '';



        $findPrevious = DB::table('APP_MASTER_TBL')
            ->select('MASTER_ID')
            ->orderBy('MASTER_ID', 'DESC')
            ->get();

        if (empty($findPrevious[0])) {
            $masterID = 'MASID-' . $Ym . '-000001';
        } else {
            $masterID = AutogenerateKey('MASID', $findPrevious[0]->MASTER_ID);
        }


        DB::beginTransaction();
        try {
            $insertRows = [];
            $dateTime = date('Y-m-d H:i:s');

            foreach ($data['levels'] as $levelBlock) {
                $insertRows[] = [
                    'MASTER_ID' => $masterID,
                    'MASTER_SEQ' => $levelBlock['level'],
                    'MASTER_EMPID' => implode(',', $levelBlock['empnos']), // รวม EMPID ของ seq นั้น
                    'MASTER_STD' => 1,
                    'MASTER_LSTDT' => $dateTime,
                ];
            }


            $existing = DB::table('APP_MASTER_TBL')
                ->where('MASTER_STD', 1)
                ->first();


            if ($existing) {
                // update แถวเดิม
                DB::table('APP_MASTER_TBL')
                    ->where('MASTER_ID', $existing->MASTER_ID)
                    ->update(['MASTER_STD' => 0]);
            }


            // insert แถวใหม่
            DB::table('APP_MASTER_TBL')->insert($insertRows);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Approval recipients created.',
                'inserted' => count($insertRows), // จะได้ 2 row ตาม level
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/insert/approve",
     *     summary="Insert approve of users",
     *     tags={"Form"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="OPR_HREC_ID", type="string"),
     *              @OA\Property(property="TEC_CPORRF_HREC_ID", type="string"),
     *              @OA\Property(property="MASTER_ID", type="string"),
     *
     *
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *     )
     * )
     */

    public function InsertAppr(Request $request)
    {
        $data = $request->all();
        $Ym = date('Ym');
        $appsId = '';



        $findPrevious = DB::table('APPREC_H_TBL')
            ->select('APP_REC_ID')
            ->orderBy('APP_REC_ID', 'DESC')
            ->get();

        if (empty($findPrevious[0])) {
            $appsId = 'APPR-' . $Ym . '-000001';
        } else {
            $appsId = AutogenerateKey('APPR', $findPrevious[0]->APP_REC_ID);
        }

        DB::beginTransaction();
        try {
            $insertRows = [];
            $dateTime = date('Y-m-d H:i:s');
            $currentAppId = '';

            $apps = DB::table('APP_MASTER_TBL')->where('MASTER_STD', 1)->get();
            for ($i = 0; $i < sizeof($apps); $i++) {
                $rows = $apps[$i];

                // สร้าง APP_REC_ID ใหม่ตามลำดับ
                if ($i === 0) {
                    $currentAppId = $appsId; // แถวแรกใช้ $appsId ที่คำนวณมาแล้ว
                } else {
                    $currentAppId = AutogenerateKey('APPR', $currentAppId);
                }

                $insertRows[] = [
                    'APP_REC_ID' => $currentAppId,
                    'OPR_HREC_ID' => $data['OPR_HREC_ID'],
                    'TEC_CPORRF_HREC_ID' => $data['TEC_CPHREC_ID'],
                    'APP_REC_EMPNO' => $rows->MASTER_EMPID,
                    'APP_REC_LEVEL' => $rows->MASTER_SEQ,
                    'APP_REC_EMPAPP' => 0,
                    'APP_REC_STD' => 0,
                    'APP_REC_LSTDT' => $dateTime
                ];
            }

            DB::table('APPREC_H_TBL')->insert($insertRows);

            DB::table('OPR_HREC_TBL')
                ->where('OPR_HREC_ID', $data['OPR_HREC_ID'])
                ->update([
                    'OPR_HREC_SENDAPP_STD' => 1
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Approval recipients created.',
                'inserted' => count($insertRows), // จะได้ 2 row ตาม level
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
