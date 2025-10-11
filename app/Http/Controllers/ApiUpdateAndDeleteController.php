<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ApiUpdateAndDeleteController extends Controller
{
    /**
     * @OA\Put(
     *     path="/oprform/{id}",
     *     summary="Update OPR Process CP form data",
     *     tags={"OPR CP Form"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Record ID to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="prs", type="string", example="CP"),
     *              @OA\Property(property="ecn", type="string", example="Yes"),
     *              @OA\Property(property="ecn_num", type="string", example="E1234"),
     *              @OA\Property(property="ecn_rev", type="string", example="01"),
     *              @OA\Property(property="date_rec", type="string", example="2025-10-08"),
     *              @OA\Property(property="line", type="string", example="Line A"),
     *              @OA\Property(property="issue_no", type="string", example="IS-2025-001"),
     *              @OA\Property(property="status", type="string", example="active"),
     *              @OA\Property(property="lots", type="string", example="10"),
     *              @OA\Property(property="prog_name", type="string", example="Program X"),
     *              @OA\Property(property="prog_rev", type="string", example="Rev A"),
     *              @OA\Property(property="empno", type="string", example="2970011"),
     *              @OA\Property(property="cur_modelname", type="string", example="Model-CUR"),
     *              @OA\Property(property="chn_modelname", type="string", example="Model-CHN"),
     *              @OA\Property(property="won_cur", type="string", example="WO-CUR-001"),
     *              @OA\Property(property="won_chn", type="string", example="WO-CHN-002"),
     *              @OA\Property(property="lots_chn", type="string", example="5")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Record updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found"
     *     )
     * )
     */

    public function UpdateOprForm($id, Request $request)
    {
        $data = $request->all();
        $date_rec = date('Y-m-d');
        $dateTime = date('Y-m-d H:i:s');
        $update = [
            'OPR_HREC_EMPNO' => $data['empno'],
            'OPR_HREC_PROCS' => $data['prs'],
            'OPR_HREC_LINE' => $data['line'],
            'OPR_HREC_DATEREC' => $date_rec,
            'OPR_HREC_ISSUENO' => $data['issue_no'],
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
            'OPR_HREC_UPDATESTD' => 1,
            'OPR_HREC_UPDATELSTDT' => $dateTime,
            'OPR_HREC_LOTS_CHN' => $data['lots_chn'],
        ];

        DB::table('OPR_HREC_TBL')
            ->where('OPR_HREC_ID', $id)
            ->update($update);

        Log::info("message", [
            'data' => $update,

        ]);

        return response()->json(['status' => true]);
    }

    /**
     * @OA\Put(
     *     path="/oprform/rf/{id}",
     *     summary="Update OPR Process RF form data",
     *     tags={"OPR RF Form"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Record ID to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
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
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Record updated successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found"
     *     )
     * )
     */

    public function UpdateOprRfForm($id, Request $request)
    {
        $data = $request->all();
        $date_rec = date('Y-m-d');
        $dateTime = date('Y-m-d H:i:s');

        $update_rf = [
            'OPR_HREC_EMPNO' => $data['empno'],
            'OPR_HREC_PROCS' => $data['prs'],
            'OPR_HREC_PROCS_RF' => $data['procs_rf'],
            'OPR_HREC_LINE' => $data['line'],
            'OPR_HREC_DATEREC' => $date_rec,
            'OPR_HREC_ISSUENO' => $data['issue_no'],
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
            'OPR_HREC_UPDATESTD' => 0,
            'OPR_HREC_UPDATELSTDT' => $dateTime,
            'OPR_HREC_LOTS_CHN' => $data['lots_chn'],
            'OPR_HREC_PROCS_RF_CHN' => $data['procs_rf_chn'],
        ];

        DB::table('OPR_HREC_TBL')
            ->where('OPR_HREC_ID', $id)
            ->update($update_rf);

        Log::info("message", [
            'data' => $update_rf,

        ]);

        return response()->json(['status' => true]);
    }


    /**
     * @OA\Delete(
     *     path="/data/delete/{id}",
     *     summary="Delete OPR form by ID",
     *     tags={"OPR Form"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the record to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Record deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found"
     *     )
     * )
     */


    public function DeleteDataOprForm($id)
    {

        $update_std_form = [
            'OPR_HREC_STD' => 2
        ];

        DB::table('OPR_HREC_TBL')
            ->where('OPR_HREC_ID', $id)
            ->update($update_std_form);

        Log::info("delete", [
            'data' => $update_std_form,

        ]);

        return response()->json(['status' => true]);
    }



    /**
     * @OA\Put(
     *     path="/submit/{id}",
     *     summary="Submit OPR form by ID",
     *     tags={"OPR Form"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the record to submit",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Record deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Record not found"
     *     )
     * )
     */

    public function SubmitDataOprForm($id)
    {

        $submit = [
            'OPR_HREC_STD' => 1
        ];

        DB::table('OPR_HREC_TBL')
            ->where('OPR_HREC_ID', $id)
            ->update($submit);

        Log::info("delete", [
            'data' => $submit,

        ]);

        return response()->json(['status' => true]);
    }
}
