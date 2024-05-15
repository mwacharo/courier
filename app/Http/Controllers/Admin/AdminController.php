<?php

namespace App\Http\Controllers\Admin;

use App\Admin;
use App\Exports\AdminExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use App\Http\Controllers\Admin\LogController;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $log_controller = new LogController();
        $log_controller->adminListLog();

        $admins = DB::table('admins')
            ->where('deleted_at', null)
            ->orderBy('first_name', 'ASC')
            ->get();


        $data = [
            'admins' => $admins
        ];

        return view('admin.admin.index', $data);
    }

    public function create()
    {
        return view('admin.admin.admin-create');
    }

    public function adminActivity()
    {
        return view('admin.admin.admin-activity');
    }

    public function details(Request $request)
    {

        $admin_id = $request->id;
        $result_logs= [];
        $logs = DB::table('activity_log')
            ->where('causer_id', $admin_id)
            ->orderByDesc('created_at')
            ->get();

        foreach ($logs as $log){

            $property_id = "";
            $property_name = "";
            $properties = json_decode($log->properties, true);
            if(!empty($properties)){
                $property_id = $properties['id'];
                $property_name = $properties['name'];
            }

            array_push($result_logs,
                array(
                    'id' => $log->id,
                    'log_name' => $log->log_name,
                    'description' => $log->description,
                    'created_at' => $log->created_at,
                    'property_id' => $property_id,
                    'property_name' => $property_name,
                ));
        }


        $permissions = DB::table('permissions')
            ->get();
        $result_permission = array();
        foreach ($permissions as $permission){

            // Check if user has permission
            $has_permission = 0;
            $user_permission = DB::table('model_has_permissions')
                ->where('model_id', $admin_id)
                ->where('permission_id', $permission->id)
                ->first();
            if($user_permission){
                $has_permission = 1;
            }

            array_push($result_permission,
                array(
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'guard_name' => $permission->guard_name,
                    'has_permission' => $has_permission,
                ));

        }

        $admin = DB::table('admins')
            ->where('id', $admin_id)
            ->first();

        $log_controller = new LogController();
        $log_controller->adminDetailsLog($admin_id, $admin->first_name);

        $data = [
            'id' => $admin_id,
            'logs' => $result_logs,
            'permissions' => $result_permission,
        ];
        return view('admin.admin.admin-details', $data);
    }

    public function permissionsEdit(Request $request)
    {

        Artisan::call('cache:clear');
        $id = $request->id;

        $delete = DB::table('model_has_permissions')
            ->where('model_id', $id)
            ->delete();

        $post_permissions = $request->input('user_permissions');
        $user = Admin::where('id', $id)
            ->first();
        $user->syncPermissions($post_permissions);

        return redirect()->route('admin.admin.details', ['id' => $id]);
    }

    public function reportExcel(){
        return Excel::download(new AdminExport, 'admin-report-excel.xls');
    }

    public function reportPdf(){

        $admins = DB::table('admins')
            ->where('deleted_at', null)
            ->orderBy('first_name', 'ASC')
            ->get();

        $data = [
            'admins' => $admins
        ];

        $pdf = PDF::loadView('admin.report.admin-report-pdf', $data);
        return $pdf->stream('admin.report.admin-report-pdf');
    }
}
