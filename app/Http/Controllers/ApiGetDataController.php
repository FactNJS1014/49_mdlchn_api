<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            ->select('WON', 'MDLNM', 'BGCD')
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
            ->select('WON', 'MDLQTY')
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
            ->select('WON', 'MDLCD', 'WONQT')
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
            ->orderBy('OPR_HREC_ID', 'desc')
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
            ->where('OPR_HREC_CHOOSE', 0)
            ->orderBy('OPR_HREC_ID', 'desc')
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

    public function GetAllData(Request $request)
    {
        $empno = $request->empno;
        // Process CP
        $cpData = DB::table('OPR_HREC_TBL')
            ->join('TEC_CP_HREC_TBL', 'OPR_HREC_TBL.OPR_HREC_ID', '=', 'TEC_CP_HREC_TBL.OPR_HREC_ID')
            // ->join('APPREC_H_TBL', 'OPR_HREC_TBL.OPR_HREC_ID', '=', 'APPREC_H_TBL.OPR_HREC_ID')
            // ->join('APPREC_H_TBL as A2', 'TEC_CP_HREC_TBL.TEC_CPHREC_ID', '=', 'A2.TEC_CPORRF_HREC_ID')
            ->where('OPR_HREC_TBL.OPR_HREC_PROCS', '=', 'CP')
<<<<<<< HEAD
            ->where('OPR_HREC_TBL.OPR_HREC_STD', '=', 1)
            ->where('OPR_HREC_TBL.OPR_HREC_RM_STD', '=', 0)
=======
            ->where('OPR_HREC_TBL.OPR_HREC_SENDAPP_STD', '=', null)
>>>>>>> 6eff0287dc32e2ea99c50d6cc48fe346852a0a93
            ->select('OPR_HREC_TBL.*', 'TEC_CP_HREC_TBL.*')
            ->orderBy('OPR_HREC_TBL.OPR_HREC_ID', 'desc')
            ->get();

        // Process RF
        $rfData = DB::table('OPR_HREC_TBL')
            ->join('TEC_RF_HREC_TBL', 'OPR_HREC_TBL.OPR_HREC_ID', '=', 'TEC_RF_HREC_TBL.OPR_HREC_ID')
            ->where('OPR_HREC_TBL.OPR_HREC_PROCS', '=', 'RF')
<<<<<<< HEAD
            ->where('OPR_HREC_TBL.OPR_HREC_STD', '=', 1)
            ->where('OPR_HREC_TBL.OPR_HREC_RM_STD', '=', 0)
=======
            ->where('OPR_HREC_TBL.OPR_HREC_SENDAPP_STD', '=', null)
>>>>>>> 6eff0287dc32e2ea99c50d6cc48fe346852a0a93
            ->select('OPR_HREC_TBL.*', 'TEC_RF_HREC_TBL.*')
            ->orderBy('OPR_HREC_TBL.OPR_HREC_ID', 'desc')
            ->get();

        $app_cp = DB::table('APPREC_H_TBL')
            ->join('TEC_CP_HREC_TBL', 'APPREC_H_TBL.TEC_CPORRF_HREC_ID', '=', 'TEC_CP_HREC_TBL.TEC_CPHREC_ID')
            ->join('OPR_HREC_TBL', 'TEC_CP_HREC_TBL.OPR_HREC_ID', '=', 'OPR_HREC_TBL.OPR_HREC_ID')
            ->where('OPR_HREC_TBL.OPR_HREC_STD', '=', 1)
            ->where('OPR_HREC_TBL.OPR_HREC_RM_STD', '=', 0)
            ->where('APPREC_H_TBL.APP_REC_STD', '=', 0)
            ->select('APPREC_H_TBL.*', 'TEC_CP_HREC_TBL.TEC_CPHREC_ID', 'OPR_HREC_TBL.OPR_HREC_ID')
            ->orderBy('OPR_HREC_TBL.OPR_HREC_ID', 'desc')
            ->orderBy('APPREC_H_TBL.APP_REC_LEVEL', 'asc')
            ->get();

        $app_rf = DB::table('APPREC_H_TBL')
            ->join('TEC_RF_HREC_TBL', 'APPREC_H_TBL.TEC_CPORRF_HREC_ID', '=', 'TEC_RF_HREC_TBL.TEC_RFHREC_ID')
            ->join('OPR_HREC_TBL', 'TEC_RF_HREC_TBL.OPR_HREC_ID', '=', 'OPR_HREC_TBL.OPR_HREC_ID')
            ->where('OPR_HREC_TBL.OPR_HREC_STD', '=', 1)
            ->where('OPR_HREC_TBL.OPR_HREC_RM_STD', '=', 0)
            ->where('APPREC_H_TBL.APP_REC_STD', '=', 0)
            ->select('APPREC_H_TBL.*', 'TEC_RF_HREC_TBL.TEC_RFHREC_ID', 'OPR_HREC_TBL.OPR_HREC_ID')
            ->orderBy('OPR_HREC_TBL.OPR_HREC_ID', 'desc')
            ->orderBy('APPREC_H_TBL.APP_REC_LEVEL', 'asc') //
            ->get();

        // รวมข้อมูลทั้งสองแบบ
        return response()->json([
            'cp' => $cpData,
            'rf' => $rfData,
            'empno' => $empno,
            'app_cp' => $app_cp,
            'app_rf' => $app_rf,
        ]);
    }

    /**
     * @OA\Get(
     *     path="users",
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
        $users_AM = DB::table('VUSER_DEPT')
            ->where('DEPT_S_NAME', 'AM')
            ->get();
        $users_QC = DB::table('VUSER_DEPT')
            ->where('DEPT_S_NAME', 'QC')
            ->get();
        $user_all = DB::table('VUSER_DEPT')
            ->get();

        return response()->json([
            'AM' => $users_AM,
            'QC' => $users_QC,
            'ALL' => $user_all,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/approve/users",
     *     summary="Get approve users on web",
     *     tags={"Get Approve Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *        description="Invalid request"
     *     ),
     * )
     */

    public function GetApproveUsers()
    {
        $approve_users = DB::table('APP_MASTER_TBL')
            ->where('MASTER_STD', 1)
            ->get();
        return response()->json($approve_users);
    }
    /**
     * @OA\Get(
     *     path="/session/user",
     *     summary="Get current session information from URL login",
     *     tags={"Session"},
     *     @OA\Response(
     *         response=200,
     *         description="Return current session data",
     *         @OA\JsonContent(
     *            type="object",
     *            @OA\Property(property="empno", type="string", example="2240003"),
     *            @OA\Property(property="username", type="string", example="Natdanai Jansomboon"),
     *            @OA\Property(property="department", type="string", example="SYS"),
     *            @OA\Property(property="USE_PERMISSION", type="integer", example=9),
     *            @OA\Property(property="sec", type="string", example="EMS"),
     *            @OA\Property(property="MSECT_ID", type="string", example="SC227-0001"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Session expired or unauthorized"
     *     ),
     * )
     */

    public function GetSessionUser(Request $request)
    {
        if ($request->has('empno')) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            session([
                'username'   => $request->username,
                'empno'      => $request->empno,
                'department' => $request->department,
                'permission' => $request->USE_PERMISSION,
                'sec'        => $request->sec,
                'sect_id'    => $request->MSECT_ID,
            ]);

            $request->session()->save();
        }

        // ✅ Redirect ทุกกรณีถ้ามี return_url
        if ($request->has('return_url')) {
            return redirect($request->return_url);
        }

        return response()->json([
            'username'   => session('username'),
            'empno'      => session('empno'),
            'department' => session('department'),
            'permission' => session('permission'),
            'sec'        => session('sec'),
            'sect_id'    => session('sect_id'),
        ]);
    }


    /**
     * @OA\Get(
     *     path="/appr/data/cp",
     *     summary="Get approve users on web",
     *     tags={"Get Approve Users"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *        description="Invalid request"
     *     ),
     * )
     */

    public function GetDataApprCP()
    {
        $cpData = DB::table('APPREC_H_TBL')
            ->join('TEC_CP_HREC_TBL', 'APPREC_H_TBL.TEC_CPORRF_HREC_ID', '=', 'TEC_CP_HREC_TBL.TEC_CPHREC_ID')
            ->join('OPR_HREC_TBL', 'TEC_CP_HREC_TBL.OPR_HREC_ID', '=', 'OPR_HREC_TBL.OPR_HREC_ID')
            ->select('APPREC_H_TBL.*', 'TEC_CP_HREC_TBL.TEC_CPHREC_ID', 'OPR_HREC_TBL.*')
            ->get();

        return response()->json($cpData);
    }

    /**
     * @OA\Get(
     *     path="/get/cus",
     *     summary="Get customer list",
     *     tags={"Customers"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=400,
     *        description="Invalid request"
     *     ),
     * )
     */

    public function GetDataCustomer()
    {
        $data_cus = DB::table('VWORLIST')
            ->select('BGCD')
            ->distinct()
            ->get();
        return response()->json($data_cus);
    }

    /**
     * @OA\Get(
     *     path="/get/issueno",
     *     summary="Get Issue number",
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

    public function GetIssueNo()
    {
        $m = date('m');
        $issue_id = '';

        $findPrevious = DB::table('OPR_HREC_TBL')
            ->select('OPR_HREC_ID')
            ->orderBy('OPR_HREC_ID', 'DESC')
            ->get();

        if (empty($findPrevious[0])) {
            $issue_id = 'MCRS' . $m . '-000001';
        } else {
            $issue_id = AutogenerateKey('MCRS', $findPrevious[0]->OPR_HREC_ID);
        }

        return response()->json($issue_id);
    }

    /**
     * @OA\Get(
     *     path="/get/settings",
     *     summary="Get Issue number",
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

    public function GetSettings()
    {
        $get_db = DB::table('SAG_MASTER_TBL')->get();
        $get_glue = DB::table('SAG_MASTER_TBL')
            ->where('SAG_CATEGROY', '=', 'Glue')
            ->get();

        $get_solder = DB::table('SAG_MASTER_TBL')
            ->where('SAG_CATEGROY', '!=', 'Glue')
            ->get();

        return response()->json([
            'get_db' => $get_db,
            'glue' => $get_glue,
            'solder' => $get_solder,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/get/alerts",
     *     summary="Alert Data for Approve",
     *     tags={"Notification"},
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
    public function GetNotification()
    {
        // ดึงรายการ ID ทั้งหมดจาก APPREC_H_TBL
        $cp_id = DB::table('APPREC_H_TBL')
            ->pluck('OPR_HREC_ID');   // ได้เป็น array/collection ของ ID

        // ดึงสถานะจาก OPR_HREC_TBL เฉพาะ ID ที่มี
        $cp_std_app = DB::table('OPR_HREC_TBL')
            ->whereIn('OPR_HREC_ID', $cp_id)
            ->pluck('OPR_HREC_SENDAPP_STD');


        $cp_al = DB::table('APPREC_H_TBL')
            ->where('APP_REC_LEVEL', $cp_std_app)
            ->where('APP_REC_STAMP_STD', 0)
            ->get();

        return response()->json($cp_al);
    }


    /**
     * @OA\Get(
     *     path="/get/reports",
     *     summary="Report Model Change Record",
     *     tags={"Report"},
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

    public function GetReports(Request $request)
    {

        $page = $request->page ?? 1;
        $perPage = $request->per_page ?? 10;
        $query = DB::table('APPREC_H_TBL')
            ->join('TEC_RF_HREC_TBL', 'APPREC_H_TBL.TEC_CPORRF_HREC_ID', '=', 'TEC_RF_HREC_TBL.TEC_RFHREC_ID')
            ->join('OPR_HREC_TBL', 'TEC_RF_HREC_TBL.OPR_HREC_ID', '=', 'OPR_HREC_TBL.OPR_HREC_ID')
            ->where('OPR_HREC_TBL.OPR_HREC_STD', 1)
            ->where('OPR_HREC_TBL.OPR_HREC_RM_STD', 0)
            ->where('OPR_HREC_TBL.OPR_HREC_PROCS', 'RF')
            ->where('OPR_HREC_TBL.OPR_HREC_SENDAPP_STD', 4);
        // $query_by_id = DB::table('APPREC_H_TBL')
        //     ->join('TEC_RF_HREC_TBL', 'APPREC_H_TBL.TEC_CPORRF_HREC_ID', '=', 'TEC_RF_HREC_TBL.TEC_RFHREC_ID')
        //     ->join('OPR_HREC_TBL', 'TEC_RF_HREC_TBL.OPR_HREC_ID', '=', 'OPR_HREC_TBL.OPR_HREC_ID')
        //     ->where('OPR_HREC_TBL.OPR_HREC_STD', 1)
        //     ->where('OPR_HREC_TBL.OPR_HREC_RM_STD', 0)
        //     ->where('OPR_HREC_TBL.OPR_HREC_PROCS', 'RF')
        //     ->where('OPR_HREC_TBL.OPR_HREC_SENDAPP_STD', 4)
        //     ->select('OPR_HREC_TBL.*', 'APPREC_H_TBL.*', 'TEC_RF_HREC_TBL.*')
        //     ->get();
        $query_cp = DB::table('APPREC_H_TBL')
            ->join('TEC_CP_HREC_TBL', 'APPREC_H_TBL.TEC_CPORRF_HREC_ID', '=', 'TEC_CP_HREC_TBL.TEC_CPHREC_ID')
            ->join('OPR_HREC_TBL', 'TEC_CP_HREC_TBL.OPR_HREC_ID', '=', 'OPR_HREC_TBL.OPR_HREC_ID')
            ->where('OPR_HREC_TBL.OPR_HREC_STD', 1)
            ->where('OPR_HREC_TBL.OPR_HREC_RM_STD', 0)
            ->where('OPR_HREC_TBL.OPR_HREC_PROCS', 'CP')
            ->where('OPR_HREC_TBL.OPR_HREC_SENDAPP_STD', '<=', 4);
        $applyFilters = function ($query) use ($request) {

            if ($request->date_start) {
                $query->whereDate(
                    'OPR_HREC_TBL.OPR_HREC_DATEREC',
                    '>=',
                    $request->date_start
                );
            }

            if ($request->date_end) {
                $query->whereDate(
                    'OPR_HREC_TBL.OPR_HREC_DATEREC',
                    '<=',
                    $request->date_end
                );
            }

            if ($request->line) {
                $query->where(
                    'OPR_HREC_TBL.OPR_HREC_LINE',
                    $request->line
                );
            }

            if ($request->customer) {
                $query->where(
                    'OPR_HREC_TBL.OPR_HREC_CUS',
                    $request->customer
                );
            }

            return $query;
        };

        $applyFilters($query);
        $applyFilters($query_cp);
        $rep_rf = $query
            ->select([
                'APPREC_H_TBL.APP_REC_LEVEL',
                'APPREC_H_TBL.APP_REC_EMPAPP',
                'APPREC_H_TBL.APP_REC_STAMP_STD',
                'APPREC_H_TBL.APP_REC_TIMESTAMP',
                'OPR_HREC_TBL.*',
                'TEC_RF_HREC_TBL.*',
            ])
            ->orderByDesc('OPR_HREC_TBL.OPR_HREC_ID')
            ->paginate($perPage);


        $rep_cp = $query_cp
            ->select([
                'APPREC_H_TBL.APP_REC_LEVEL',
                'APPREC_H_TBL.APP_REC_EMPAPP',
                'APPREC_H_TBL.APP_REC_STAMP_STD',
                'APPREC_H_TBL.APP_REC_TIMESTAMP',
                'OPR_HREC_TBL.*',
                'TEC_CP_HREC_TBL.*',


            ])
            ->orderByDesc('OPR_HREC_TBL.OPR_HREC_ID')
            ->paginate($perPage);
        return response()->json(['rep_rf' => $rep_rf, 'rep_cp' => $rep_cp,]);
    }

    /**
     * @OA\Get(
     *     path="/get/reports/byid",
     *     summary="Report Model Change Record by ID",
     *     tags={"Report"},
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

    public function GetReportsById()
    {

        $query = DB::table('APPREC_H_TBL')
            ->join('TEC_RF_HREC_TBL', 'APPREC_H_TBL.TEC_CPORRF_HREC_ID', '=', 'TEC_RF_HREC_TBL.TEC_RFHREC_ID')
            ->join('OPR_HREC_TBL', 'TEC_RF_HREC_TBL.OPR_HREC_ID', '=', 'OPR_HREC_TBL.OPR_HREC_ID')
            ->where('OPR_HREC_TBL.OPR_HREC_STD', 1)
            ->where('OPR_HREC_TBL.OPR_HREC_RM_STD', 0)
            ->where('OPR_HREC_TBL.OPR_HREC_PROCS', 'RF')
            ->where('OPR_HREC_TBL.OPR_HREC_SENDAPP_STD', '<=', 4)
            ->select('OPR_HREC_TBL.*', 'APPREC_H_TBL.*', 'TEC_RF_HREC_TBL.*')
            ->get();
        return response()->json(['rep_rf_by_id' => $query]);
    }

    /**
     * @OA\Get(
     *     path="/get/reports/nocomplete",
     *     summary="Report Model Change Record No Complete",
     *     tags={"Report"},
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

    public function GetReportsNoComplete()
    {
        $rep_cp = DB::table('APPREC_H_TBL')
            ->join('TEC_CP_HREC_TBL', 'APPREC_H_TBL.TEC_CPORRF_HREC_ID', '=', 'TEC_CP_HREC_TBL.TEC_CPHREC_ID')
            ->join('OPR_HREC_TBL', 'TEC_CP_HREC_TBL.OPR_HREC_ID', '=', 'OPR_HREC_TBL.OPR_HREC_ID')
            ->where('OPR_HREC_TBL.OPR_HREC_STD', '=', 1)
            ->where('OPR_HREC_TBL.OPR_HREC_RM_STD', '=', 0)

            ->where('OPR_HREC_TBL.OPR_HREC_PROCS', '=', 'CP')
            ->where('OPR_HREC_TBL.OPR_HREC_SENDAPP_STD', '<', 4)
            ->select('APPREC_H_TBL.APP_REC_LEVEL', 'APPREC_H_TBL.APP_REC_EMPAPP', 'APPREC_H_TBL.APP_REC_STAMP_STD', 'APPREC_H_TBL.APP_REC_TIMESTAMP', 'TEC_CP_HREC_TBL.*', 'OPR_HREC_TBL.*')
            ->orderBy('OPR_HREC_TBL.OPR_HREC_ID', 'desc')
            ->get();

        $rep_rf = DB::table('APPREC_H_TBL')
            ->join('TEC_RF_HREC_TBL', 'APPREC_H_TBL.TEC_CPORRF_HREC_ID', '=', 'TEC_RF_HREC_TBL.TEC_RFHREC_ID')
            ->join('OPR_HREC_TBL', 'TEC_RF_HREC_TBL.OPR_HREC_ID', '=', 'OPR_HREC_TBL.OPR_HREC_ID')
            ->where('OPR_HREC_TBL.OPR_HREC_STD', '=', 1)
            ->where('OPR_HREC_TBL.OPR_HREC_RM_STD', '=', 0)
            ->where('OPR_HREC_TBL.OPR_HREC_PROCS', '=', 'RF')
            ->where('OPR_HREC_TBL.OPR_HREC_SENDAPP_STD', '<', 4)
            ->select('APPREC_H_TBL.APP_REC_LEVEL', 'APPREC_H_TBL.APP_REC_EMPAPP', 'APPREC_H_TBL.APP_REC_STAMP_STD', 'APPREC_H_TBL.APP_REC_TIMESTAMP', 'TEC_RF_HREC_TBL.*', 'OPR_HREC_TBL.*')
            ->orderBy('OPR_HREC_TBL.OPR_HREC_ID', 'desc')
            ->get();

        return response()->json([
            'rep_cp_no_complete' => $rep_cp,
            'rep_rf_no_complete' => $rep_rf,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/get/rejectedData",
     *     summary="Rejected Data Record",
     *     tags={"Report"},
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

    public function GetRejectedData()
    {
        $cpData_rejected = DB::table('OPR_HREC_TBL')
            ->join('TEC_CP_HREC_TBL', 'OPR_HREC_TBL.OPR_HREC_ID', '=', 'TEC_CP_HREC_TBL.OPR_HREC_ID')
            ->where('OPR_HREC_TBL.OPR_HREC_PROCS', '=', 'CP')
            ->where('OPR_HREC_TBL.OPR_HREC_TECH_RJ_STD', '=', 1)
            ->select('OPR_HREC_TBL.*', 'TEC_CP_HREC_TBL.*')
            ->get();

        // Process RF
        $rfData_rejected = DB::table('OPR_HREC_TBL')
            ->join('TEC_RF_HREC_TBL', 'OPR_HREC_TBL.OPR_HREC_ID', '=', 'TEC_RF_HREC_TBL.OPR_HREC_ID')
            ->where('OPR_HREC_TBL.OPR_HREC_PROCS', '=', 'RF')
            ->where('OPR_HREC_TBL.OPR_HREC_TECH_RJ_STD', '=', 1)
            ->select('OPR_HREC_TBL.*', 'TEC_RF_HREC_TBL.*')
            ->get();

        $operator_rejected = DB::table('OPR_HREC_TBL')
            ->where('OPR_HREC_TBL.OPR_HREC_OPRATOR_RJ_STD', '=', 1)
            ->select('OPR_HREC_TBL.*')
            ->get();

        return response()->json([
            'cpData_rejected' => $cpData_rejected,
            'rfData_rejected' => $rfData_rejected,
            'operator_rejected' => $operator_rejected,
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
