<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RiderExport;
use App\Http\Controllers\Controller;
use App\Imports\PayoutImport;
use App\Imports\RiderImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use niklasravnsborg\LaravelPdf\Facades\Pdf;

class CallAgentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $call_agents = DB::table('call_agents')
            ->where('deleted_at', null)
            ->orderBy('client_name', 'ASC')
            ->get();

        $json_results = array();
        foreach ($call_agents as $call_agent){

            $first_name = "";
            $last_name = "";
            $email = "";
            $role = "";

            $admin = DB::table('admins')
                ->where('id', $call_agent->admin_id)
                ->where('deleted_at', null)
                ->first();

            if($admin){
                $first_name = $admin->first_name;
                $last_name = $admin->last_name;
                $email = $admin->email;
                $role = $admin->role;
            }

            array_push($json_results,
                array(
                    'id' => $call_agent->id,
                    'phone_number' => $call_agent->phone_number,
                    'client_name' => $call_agent->client_name,
                    'admin_id' => $call_agent->admin_id,
                    'status' => $call_agent->status,
                    'sessionId' => $call_agent->sessionId,
                    'token' => $call_agent->token,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'role' => $role,
                    'created_at' => $call_agent->created_at,
                    'updated_at' => $call_agent->updated_at,
                ));

        }

        $data = [
            'call_agents' => $json_results
        ];

        return view('admin.call-agent.index', $data);
    }

    public function create()
    {
        return view('admin.call-agent.call-agent-create');
    }

    public function details(Request $request)
    {
        $call_agent = DB::table('call_agents')
            ->where('id', $request->id)
            ->first();

        $data = [
            'id' => $request->id
        ];
        return view('admin.call-agent.call-agent-details', $data);
    }

}
