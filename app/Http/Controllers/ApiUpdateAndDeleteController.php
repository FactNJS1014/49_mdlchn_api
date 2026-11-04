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

        try {
            DB::beginTransaction();

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
                'OPR_HREC_PROCSCP' => $data['procs_cp'],
                'OPR_HREC_PROCSCP_CHN' => $data['procs_cp_chn'],
                'OPR_HREC_STD' => 1,
                'OPR_HREC_OPRATOR_RJ_STD' => 2,
                'OPR_HREC_REVNO' => $data['rev_no']
            ];

            DB::table('OPR_HREC_TBL')
                ->where('OPR_HREC_ID', $id)
                ->update($update);

            if ($update > 0) {

                Log::info("message", [
                    'data' => $update,

                ]);
                DB::commit();
                return response()->json(['status' => true]);
            } else {
                DB::rollBack();
                Log::error("ApiUpdateAndDeleteController > update > error:can't updateOPR_HREC_TBL " . $request);
                return response()->json(['status' => false, 'message' => 'Error: แก้ไขไม่สำเร็จ'], 500);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("ApiUpdateAndDeleteController > UpdateOPRForm > error:update : " . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
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
            'OPR_HREC_STD' => 1,
            'OPR_HREC_OPRATOR_RJ_STD' => 2,
            'OPR_HREC_REVNO' => $data['rev_no']
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

        Log::info("submit", [
            'data' => $submit,

        ]);

        return response()->json(['status' => true]);
    }

    /**
     * @OA\Put(
     *     path="/update/approve",
     *     summary="Update approval status of OPR form",
     *     tags={"Approval"},
     *     @OA\RequestBody(
     *         required=true,
     *        @OA\JsonContent(
     *             type="object",
     *            @OA\Property(property="opr_hrec_id", type="integer", example=1),
     *           @OA\Property(property="approve_std", type="integer", example=1)
     *    )
     *    ),
     *    @OA\Response(
     *        response=200,
     *       description="Approval status updated successfully"
     *   ),
     *   @OA\Response(
     *       response=404,
     *      description="Record not found"
     *   )
     * )
     */

    public function UpdateApproveStatus(Request $request)
    {
        $data = $request->all();
        $appr_empno = $request->APPROVE_BY;
        $level = (int)$request->APP_LEV;


        try {
            DB::beginTransaction();
            $update_sendapp = DB::table('OPR_HREC_TBL')
                ->where('OPR_HREC_ID', $data['OPR_HREC_ID'])
                ->update(['OPR_HREC_SENDAPP_STD' => DB::raw('OPR_HREC_SENDAPP_STD + 1')]);

            $update_apprec = DB::table('APPREC_H_TBL')
                ->where('OPR_HREC_ID', $data['OPR_HREC_ID'])
                ->where('APP_REC_LEVEL', $level)
                ->where('APP_REC_REJSTD', 0)
                ->where('APP_REC_STD', 0)
                ->update([
                    'APP_REC_EMPAPP' => $appr_empno,
                    'APP_REC_STD' => 1,
                    'APP_REC_TIMESTAMP' => date('Y-m-d H:i:s'),
                    'APP_REC_STAMP_STD' => 1,
                ]);
            if ($update_sendapp > 0 && $update_apprec > 0) {
                DB::commit();
                Log::info("approve", [
                    'appr_empno' => $appr_empno,
                    'level' => $level,
                ]);

                return response()->json([
                    'data' => $data,
                    'appr_empno' => $appr_empno,
                    'level' => $level,
                    'success' => true,
                ]);
            } else {
                DB::rollBack();
                Log::error("ApiUpdateAndDeleteController > UpdateApproveStatus > status_err", [
                    'error' => 'Failed to update approval status',
                    'success' => false,
                ]);
                return response()->json([
                    'message' => 'Failed to update approval status',
                    'success' => false,
                ], 500);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("ApiUpdateAndDeleteController > UpdateApproveStatus > approve_error", [
                'error' => $e->getMessage(),
                'success' => false,
            ]);
            return response()->json([
                'message' => 'Failed to update approval status',
                'error' => $e->getMessage(),
                'success' => false,
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/update/reject",
     *     summary="Update approval status of OPR form",
     *     tags={"Approval"},
     *     @OA\RequestBody(
     *         required=true,
     *        @OA\JsonContent(
     *             type="object",
     *            @OA\Property(property="opr_hrec_id", type="integer", example=1),
     *           @OA\Property(property="approve_std", type="integer", example=1)
     *    )
     *    ),
     *    @OA\Response(
     *        response=200,
     *       description="Approval status updated successfully"
     *   ),
     *   @OA\Response(
     *       response=404,
     *      description="Record not found"
     *   )
     * )
     */
    public function UpdateRejectStatus(Request $request)
    {
        $data = $request->all();

        try {
            DB::beginTransaction();

            if ($data['form'] === "Operator") {

                DB::table('APPREC_H_TBL')
                    ->where('OPR_HREC_ID', $data['id'])
                    ->delete();

                DB::table('OPR_HREC_TBL')
                    ->where('OPR_HREC_ID', $data['id'])
                    ->update([
                        'OPR_HREC_OPRATOR_RJ_STD' => 1,
                        'OPR_HREC_OPRATOR_RJ_REMARK' => $data['remark'],
                        'OPR_HREC_STD' => 0,
                        'OPR_HREC_SENDAPP_STD' => 0
                    ]);
            } else if ($data['form'] === "Technician") {

                DB::table('APPREC_H_TBL')
                    ->where('OPR_HREC_ID', $data['id'])
                    ->delete();

                DB::table('OPR_HREC_TBL')
                    ->where('OPR_HREC_ID', $data['id'])
                    ->update([
                        'OPR_HREC_TECH_RJ_STD' => 1,
                        'OPR_HREC_TECH_RJ_REMARK' => $data['remark'],
                        'OPR_HREC_SENDAPP_STD' => 0
                    ]);
            }
            DB::commit();
            Log::info("reject", [
                'data' => $data,
            ]);
            return response()->json([
                'data' => $data,
                'success' => true,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("reject_error", [
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'message' => 'Failed to update reject status',
                'error' => $e->getMessage(),
                'success' => false,
            ], 500);
        }
    }
    /**
     * @OA\Put(
     *     path="/cpupdate",
     *     summary="Update Technichian form process CP",
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

    public function UpdateCPData(Request $request)
    {
        $data = $request->all();
        $dateTime = date('Y-m-d H:i:s');

        DB::beginTransaction();

        $update_cp = [

            'TEC_CPHREC_LOADINP' => $data['load_inp'],
            'TEC_CPHREC_LOADINPPITCH' => $data['pitch'],
            'TEC_CPHREC_STACK' => $data['stack_inp'],
            'TEC_CPHREC_TRACEINP' => $data['trace_inp'],
            'TEC_CPHREC_PCBCLEAN' => $data['cln_inp'],
            'TEC_CPHREC_PCBCLNFUNC' => is_array($data['func'])
                ? implode(', ', $data['func'])
                : $data['func'],
            'TEC_CPHREC_GLUE' => $data['glu_inp'],
            'TEC_CPHREC_GLUEPROG' => $data['prg_nm'],
            'TEC_CPHREC_GLUENUM' => $data['glu_num'],
            'TEC_CPHREC_GLUEHUNIT' => is_array($data['headunit'])
                ? implode(', ', $data['headunit'])
                : $data['headunit'],
            'TEC_CPHREC_GLUESTDNOT' => $data['bpst_detail'],
            'TEC_CPHREC_GLUESTDOK' => $data['confirm_bpst'],
            'TEC_CPHREC_GLUESND' => $data['glu2_inp'],
            'TEC_CPHREC_GLUESNDPROG' => $data['prg2_nm'],
            'TEC_CPHREC_GLUESNDNUM' => $data['glu2_num'],
            'TEC_CPHREC_GLUESNDHUNIT' => is_array($data['headunit2'])
                ? implode(', ', $data['headunit2'])
                : $data['headunit2'],
            'TEC_CPHREC_GLUESNDNOT' => $data['bpst2_detail'],
            'TEC_CPHREC_GLUESNDOK' => $data['confirm_bpst2'],
            'TEC_CPHREC_MNTF' => $data['mounter_inp'],
            'TEC_CPHREC_MNTFPROG' => $data['mounter_inp'] === "Not Use" ? "" : $data['prg_mount1'],
            'TEC_CPHREC_MNTFNOZ' => $data['mounter_inp'] === "Not Use" ? "" : $data['noz_mount1'],
            'TEC_CPHREC_MNTFSUPT' => $data['mounter_inp'] === "Not Use" ? "" : $data['sup_mount1'],
            'TEC_CPHREC_MNTSN' => $data['mounter2_inp'],
            'TEC_CPHREC_MNTSNPROG' => $data['mounter2_inp'] === "Not Use" ? "" : $data['prg_mount2'],
            'TEC_CPHREC_MNTSNNOZ' => $data['mounter2_inp'] === "Not Use" ? "" : $data['noz_mount2'],
            'TEC_CPHREC_MNTSNSUPT' => $data['mounter2_inp'] === "Not Use" ? "" : $data['sup_mount2'],
            'TEC_CPHREC_MNTTR' => $data['mounter3_inp'],
            'TEC_CPHREC_MNTTRPROG' => $data['mounter3_inp'] === "Not Use" ? "" : $data['prg_mount3'],
            'TEC_CPHREC_MNTTRNOZ' => $data['mounter3_inp'] === "Not Use" ? "" : $data['noz_mount3'],
            'TEC_CPHREC_MNTTRSUPT' => $data['mounter3_inp'] === "Not Use" ? "" : $data['sup_mount3'],
            'TEC_CPHREC_MNTFO' => $data['mounter4_inp'],
            'TEC_CPHREC_MNTFOPROG' => $data['mounter4_inp'] === "Not Use" ? "" : $data['prg_mount4'],
            'TEC_CPHREC_MNTFONOZ' => $data['mounter4_inp'] === "Not Use" ? "" : $data['noz_mount4'],
            'TEC_CPHREC_MNTFOSUPT' => $data['mounter4_inp'] === "Not Use" ? "" : $data['sup_mount4'],
            'TEC_CPHREC_MNTINSP' => $data['mount_inps'],
            'TEC_CPHREC_MNTINSPPROG' => $data['prg_inspct'],
            'TEC_CPHREC_REFLOW' => $data['reflow_std'],
            'TEC_CPHREC_REFLPROG' => $data['prg_reflow'],
            'TEC_CPHREC_REFLOXYGEN' => $data['oxygen_reflow_std'],
            'TEC_CPHREC_REFLUSEOO' => $data['oxygen_use'],
            'TEC_CPHREC_REFLPCBSUPT' => $data['sup_reflow_std'],
            'TEC_CPHREC_PCBCOOL' => $data['cooling_std'],
            'TEC_CPHREC_AUTO' => $data['auto_inps'],
            'TEC_CPHREC_AUTOPROG' => $data['prg_auto'],
            'TEC_CPHREC_NGSTCK' => $data['ng_stock_std'],
            'TEC_CPHREC_NGSTCKPITCH' => $data['ng_stock_pitch'],
            'TEC_CPHREC_TRACE' => $data['trace_inp_std'],
            'TEC_CPHREC_UNLOADER' => $data['unloader_std'],
            'TEC_CPHREC_UNLOADERPITCH' => $data['unloader_pitch'],
            'TEC_CPHREC_PCBLNETC_DTL' => $data['etc_details'],
            'TEC_CPHREC_LSTDT' => $dateTime,
            'TEC_CPHREC_STARTMCHN' => $data['start_machine'],
            'TEC_CPHREC_FINISHMCHN' => $data['finish_machine'],
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
            'TEC_CPHREC_STD' => 2
        ];

        DB::table('TEC_CP_HREC_TBL')
            ->where('OPR_HREC_ID', $data['id'])
            ->update($update_cp);

        DB::table('OPR_HREC_TBL')
            ->where('OPR_HREC_ID', $data['id'])
            ->update([
                'OPR_HREC_UPDATESTD' => 1,
                'OPR_HREC_UPDATELSTDT' => $dateTime,
                'OPR_HREC_TECH_RJ_STD' => 2
            ]);

        DB::commit();

        Log::info("cp_update", [
            'data' => $update_cp,
        ]);
        return response()->json(['status' => true]);
    }


    /**
     * @OA\Put(
     *   path="/rfupdate",
     *   summary="Update Technichian form process RF",
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

    public function UpdateRFData(Request $request)
    {
        $data = $request->all();
        $dateTime = date('Y-m-d H:i:s');

        DB::beginTransaction();

        $update_rf_data = [
            'TEC_RFHREC_EMPNO' => $data['empno'],
            'TEC_RFHREC_LOADINP' => $data['load_inp'],
            'TEC_RFHREC_LOADINPPITCH' => $data['pitch_rf'],
            'TEC_RFHREC_STACK' => $data['stack_inp'],
            'TEC_RFHREC_TRACEINP' => $data['trace_inp'],
            'TEC_RFHREC_PCBCLEAN' => $data['cln_inp'],
            'TEC_RFHREC_PCBCLNFUNC' => is_array($data['func'])
                ? implode(', ', $data['func'])
                : $data['func'],

            'TEC_RFHREC_PRINT' => $data['printer_std'],
            'TEC_RFHREC_PRINTPROG' => $data['printer_prg'],
            // 'TEC_RFHREC_PRINTMM' => $data['metalmask'],
            'TEC_RFHREC_PRINTMMREF' => $data['ref_mm'],
            'TEC_RFHREC_PRINTSQUE' => $data['squee'],
            'TEC_RFHREC_PRINTSUPT' => $data['sup_rf'],
            'TEC_RFHREC_PRINTSOLDER' => $data['solder'],
            'TEC_RFHREC_GLUE' => $data['glue_rf'],
            'TEC_RFHREC_GLUEPROG' => $data['glue_prg'],
            'TEC_RFHREC_GLUENUM' => $data['glue_num'],
            'TEC_RFHREC_SOLDERINSP' => $data['solder_use'],
            'TEC_RFHREC_SOLDERPROG' => $data['solder_prgnm'],
            'TEC_RFHREC_MNTF' => $data['mounter_ref_inp'],
            'TEC_RFHREC_MNTFPROG' => $data['mounter_ref_inp'] === "Not Use" ? "" : $data['prg_ref_mount1'],
            'TEC_RFHREC_MNTFNOZ' => $data['mounter_ref_inp'] === "Not Use" ? "" : $data['noz_ref_mount1'],
            'TEC_RFHREC_MNTFSUPT' => $data['mounter_ref_inp'] === "Not Use" ? "" : $data['sup_ref_mount1'],
            'TEC_RFHREC_MNTSN' => $data['mounter2_ref_inp'],
            'TEC_RFHREC_MNTSNPROG' => $data['mounter2_ref_inp'] === "Not Use" ? "" : $data['prg_ref_mount2'],
            'TEC_RFHREC_MNTSNNOZ' => $data['mounter2_ref_inp'] === "Not Use" ? "" : $data['noz_ref_mount2'],
            'TEC_RFHREC_MNTSNSUPT' => $data['mounter2_ref_inp'] === "Not Use" ? "" : $data['sup_ref_mount2'],
            'TEC_RFHREC_MNTTR' => $data['mounter3_ref_inp'],
            'TEC_RFHREC_MNTTRPROG' => $data['mounter3_ref_inp'] === "Not Use" ? "" : $data['prg_ref_mount3'],
            'TEC_RFHREC_MNTTRNOZ' => $data['mounter3_ref_inp'] === "Not Use" ? "" : $data['noz_ref_mount3'],
            'TEC_RFHREC_MNTTRSUPT' => $data['mounter3_ref_inp'] === "Not Use" ? "" : $data['sup_ref_mount3'],
            'TEC_RFHREC_MNTFO' => $data['mounter4_ref_inp'],
            'TEC_RFHREC_MNTFOPROG' => $data['mounter4_ref_inp'] === "Not Use" ? "" : $data['prg_ref_mount4'],
            'TEC_RFHREC_MNTFONOZ' => $data['mounter4_ref_inp'] === "Not Use" ? "" : $data['noz_ref_mount4'],
            'TEC_RFHREC_MNTFOSUPT' => $data['mounter4_ref_inp'] === "Not Use" ? "" : $data['sup_ref_mount4'],
            'TEC_RFHREC_MNTINSP' => $data['mounter_ref_inps'],
            'TEC_RFHREC_MNTINSPPROG' => $data['prg_ref_inspct'],
            'TEC_RFHREC_REFLOW' => $data['reflow_rf_std'],
            'TEC_RFHREC_REFLPROG' => $data['prg_rf_reflow'],
            'TEC_RFHREC_REFLOXYGEN' => $data['oxygen_rf_reflow_std'],
            'TEC_RFHREC_REFLOO' => $data['oxyen_rf_use'],
            'TEC_RFHREC_REFLPCBSUPT' => $data['sup_rf_reflow_std'],
            'TEC_RFHREC_TOP_TEMPCH1' => $data['temp_rf_top_ch1'],
            'TEC_RFHREC_TOP_TEMPCH2' => $data['temp_rf_top_ch2'],
            'TEC_RFHREC_TOP_TEMPCH3' => $data['temp_rf_top_ch3'],
            'TEC_RFHREC_TOP_TEMPCH4' => $data['temp_rf_top_ch4'],
            'TEC_RFHREC_TOP_TEMPCH5' => $data['temp_rf_top_ch5'],
            'TEC_RFHREC_TOP_TEMPCH6' => $data['temp_rf_top_ch6'],
            'TEC_RFHREC_TOP_TEMPCH7' => $data['temp_rf_top_ch7'],
            'TEC_RFHREC_TOP_TEMPCH8' => $data['temp_rf_top_ch8'],
            'TEC_RFHREC_TOP_TEMPCH9' => $data['temp_rf_top_ch9'],
            'TEC_RFHREC_TOP_TEMPCH10' => $data['temp_rf_top_ch10'],
            'TEC_RFHREC_BTM_TEMPCH1' => $data['temp_rf_btm_ch1'],
            'TEC_RFHREC_BTM_TEMPCH2' => $data['temp_rf_btm_ch2'],
            'TEC_RFHREC_BTM_TEMPCH3' => $data['temp_rf_btm_ch3'],
            'TEC_RFHREC_BTM_TEMPCH4' => $data['temp_rf_btm_ch4'],
            'TEC_RFHREC_BTM_TEMPCH5' => $data['temp_rf_btm_ch5'],
            'TEC_RFHREC_BTM_TEMPCH6' => $data['temp_rf_btm_ch6'],
            'TEC_RFHREC_BTM_TEMPCH7' => $data['temp_rf_btm_ch7'],
            'TEC_RFHREC_BTM_TEMPCH8' => $data['temp_rf_btm_ch8'],
            'TEC_RFHREC_BTM_TEMPCH9' => $data['temp_rf_btm_ch9'],
            'TEC_RFHREC_BTM_TEMPCH10' => $data['temp_rf_btm_ch10'],
            'TEC_RFHREC_PCBCOOL' => $data['cooling_rf_std'],
            'TEC_RFHREC_AUTO' => $data['auto_rf_inps'],
            'TEC_RFHREC_AUTOPROG' => $data['prg_rf_auto'],
            'TEC_RFHREC_NGSTCK' => $data['ng_stock_rf_std'],
            'TEC_RFHREC_NGSTCKPITCH' => $data['ng_stock_rf_pitch'],
            'TEC_RFHREC_TRACE' => $data['trace_rf_inp_std'],
            'TEC_RFHREC_UNLOADER' => $data['unloader_rf_std'],
            'TEC_RFHREC_UNLOADERPITCH' => $data['unloader_rf_pitch'],
            'TEC_RFHREC_CONV_SPD' => (float) str_replace(',', '.', $data['temp_rf_conveyor_speed']),
            'TEC_RFHREC_STD' => 2,
            'TEC_RFHREC_LSTDT' => $dateTime,
            'TEC_RFHREC_PCBLNETC' => $data['etc_details'],
            'TEC_RFHREC_STARTMCHN' => $data['start_machine'],
            'TEC_RFHREC_FINISHMCHN' => $data['finish_machine'],
        ];

        DB::table('TEC_RF_HREC_TBL')
            ->where('OPR_HREC_ID', $data['id'])
            ->update($update_rf_data);

        DB::table('OPR_HREC_TBL')
            ->where('OPR_HREC_ID', $data['id'])
            ->update([
                'OPR_HREC_UPDATESTD' => 1,
                'OPR_HREC_UPDATELSTDT' => $dateTime,
                'OPR_HREC_TECH_RJ_STD' => 2
            ]);

        DB::commit();

        Log::info("rf_update", [
            "rf_data" => $update_rf_data,
        ]);

        return response()->json(['status' => true]);
    }

    /**
     * @OA\Put(
     *     path="/update/master",
     *     summary="Update master user approve",
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

    public function UpdateMasterLevel(Request $request)
    {
        $data = $request->all();

        $activeMaster = DB::table('APP_MASTER_TBL')
            ->where('MASTER_STD', 1)
            ->first();

        if (!$activeMaster) {
            return response()->json([
                'success' => false,
                'message' => 'No active approval master found.'
            ], 404);
        }

        $dateTime = date('Y-m-d H:i:s');

        DB::beginTransaction();
        try {
            foreach ($data['levels'] as $levelBlock) {
                DB::table('APP_MASTER_TBL')
                    ->where('MASTER_ID', $activeMaster->MASTER_ID)
                    ->where('MASTER_SEQ', $levelBlock['level'])
                    ->update([
                        'MASTER_EMPID' => implode(',', $levelBlock['empnos']),
                        'MASTER_LSTDT' => $dateTime
                    ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Updated successfully.',
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
     * @OA\Put(
     *     path="/update/std/delete",
     *     summary="Update status data",
     *     tags={"Form"},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="remark", type="string"),
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

    public function DeleteStdData(Request $request)
    {
        try {
            $data = $request->all();

            DB::beginTransaction();

            $up_data = [
                'OPR_HREC_STD' => 2,
                'OPR_HREC_RM_STD' => 1,
                'OPR_HREC_RM_REMARK' => $data['remark'],
            ];

            DB::table('OPR_HREC_TBL')
                ->where('OPR_HREC_ID', $data['id'])
                ->update($up_data);

            DB::commit();

            Log::info("Success: ", [
                'data' => $up_data,
                'whereID' => $data['id'],
            ]);


            return response()->json([
                'std' => true
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error Message: ', [
                'error' => $e->getMessage(),

            ]);
            return response()->json([
                'std' => false,

            ]);
        }
    }

    /**
     * @OA\Delete(
     *     path="/delete/techform/{id}",
     *     summary="Delete tech form data",
     *     tags={"Form"},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          description="ID of the tech form to delete"
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *     )
     * )
     */

    public function DeleteTechForm($id)
    {

        try {
            DB::beginTransaction();

            $delete_data = [
                'OPR_HREC_STD' => 2,
                'OPR_HREC_CHOOSE' => 2,
            ];

            DB::table('OPR_HREC_TBL')
                ->where('OPR_HREC_ID', $id)
                ->update($delete_data);

            DB::commit();

            Log::info("Success: ", [
                'data' => $delete_data,
                'whereID' => $id,
            ]);
            return response()->json([
                'status' => 200,
                'message' => 'Deleted successfully.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error Message: ', [
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'status' => 500,
                'message' => 'Failed: ' . $e->getMessage(),
            ]);
        }
    }
}
