<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

/**
 * @OA\Info(
 *      title="API_GET_POST_UPDATE _DELETE_DATA",
 *      version="1.0.0",
 *      description="API PROJECT MODEL CHANGE RECORD"
 * )
 */

class ApiGetDataController extends Controller
{
    /**
     * @OA\Get(
     *     path="/get/won",
     *     summary="Get work order to model change",
     *     tags={"Work Order"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     )
     * )
     */
    public function DataWon()
    {
        $data_won = DB::table('VWORLIST')
            ->select('WON', 'MDLNM')
            ->get();
        return response()->json($data_won);
    }

    /**
     * @OA\Get(
     *     path="/get/modelname",
     *     summary="Get data model name to model change",
     *     tags={"Model Name"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     )
     * )
     */
    public function DataModelName()
    {
        $data_modelname = DB::table('VWORLIST')
            ->select('MDLCD')
            ->distinct()
            ->get();
        return response()->json($data_modelname);
    }

    /**
     * @OA\Get(
     *     path="/get/line",
     *     summary="Get data line to model change",
     *     tags={"Line Model Change"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     )
     * )
     */

    public function DataLine()
    {
        $data_line = DB::table('VLINE')
            ->select('LINE_NAME')
            ->where('LINE_SECTION', 'AM')
            ->orderBy('LINE_LIST', 'asc')
            ->get();

        return response()->json($data_line);
    }

    /**
     * @OA\Get(
     *     path="/get/checkmodel",
     *     summary="Get work order and model name check match",
     *     tags={"Check Model"},
     *     @OA\Parameter(
     *          name="value",
     *          in="query",
     *          description="Work Order",
     *          @OA\Schema(type="string")
     *     ),
     *
     *     @OA\Response(
     *          response=200,
     *          description="Success"
     *     )
     * )
     */

    public function CheckModelName(Request $request)
    {
        $won = $request->input('value');
        $check_db_model = DB::table('VWORLIST')
            ->select('WON', 'MDLCD')
            ->where('WON', $won)
            ->get();

        return response()->json($check_db_model);
    }

    /**
     * @OA\Get(
     *     path="/get/count/opr",
     *     summary="Get count of record",
     *     tags={"Count of Record Form"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     )
     * )
     */

    public function CountInsertOpr()
    {
        $count_ins = DB::table('OPR_HREC_TBL')
            ->where('OPR_HREC_STD', 0)
            ->count();

        return response()->json($count_ins);
    }

    /**
     * @OA\Get(
     *     path="/get/record/opr",
     *     summary="Get record data",
     *     tags={"Get Record Form"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     )
     * )
     */

    public function GetRecordOpr()
    {
        $all = DB::table('OPR_HREC_TBL')
            ->where('OPR_HREC_STD', 0)
            ->get();

        return response()->json($all);
    }


    /**
     * @OA\Get(
     *     path="/get/oprform",
     *     summary="Get record data status = 1",
     *     tags={"Get Record Form"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     )
     * )
     */

    public function GetOprform()
    {
        $all = DB::table('OPR_HREC_TBL')
            ->where('OPR_HREC_STD', 1)
            ->where('OPR_HREC_CHOOSE', null)
            ->get();

        return response()->json($all);
    }

    /**
     * @OA\Get(
     *     path="/get/alldata",
     *     summary="Get All Record Data",
     *     tags={"Get Record Form"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     )
     * )
     */

    public function GetAllData()
    {
        // Process CP
        $cpData = DB::table('OPR_HREC_TBL')
            ->join('TEC_CP_HREC_TBL', 'OPR_HREC_TBL.OPR_HREC_ID', '=', 'TEC_CP_HREC_TBL.OPR_HREC_ID')
            ->where('OPR_HREC_TBL.OPR_HREC_PROCS', '=', 'CP')
            ->where('OPR_HREC_TBL.OPR_HREC_SENDAPP_STD', '=', null)
            ->select('OPR_HREC_TBL.*', 'TEC_CP_HREC_TBL.*')
            ->get();

        // Process RF
        $rfData = DB::table('OPR_HREC_TBL')
            ->join('TEC_RF_HREC_TBL', 'OPR_HREC_TBL.OPR_HREC_ID', '=', 'TEC_RF_HREC_TBL.OPR_HREC_ID')
            ->where('OPR_HREC_TBL.OPR_HREC_PROCS', '=', 'RF')
            ->where('OPR_HREC_TBL.OPR_HREC_SENDAPP_STD', '=', null)
            ->select('OPR_HREC_TBL.*', 'TEC_RF_HREC_TBL.*')
            ->get();

        // รวมข้อมูลทั้งสองแบบ
        return response()->json([
            'cp' => $cpData,
            'rf' => $rfData
        ]);
    }

    /**
     * @OA\Get(
     *     path="/get/users",
     *     summary="Get users on web",
     *     tags={"Get Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     )
     * )
     */

    public function GetUsersWeb()
    {
        $users = DB::table('VUSER_DEPT')->get();

        return response()->json($users);
    }
}
