<?php


namespace App\Exports;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;


class AdminExport implements FromView
{
    public function view(): View
    {

        $admins = DB::table('admins')
            ->where('deleted_at', null)
            ->get();

        return view('admin.report.admin-report-excel', [
            'admins' => $admins
        ]);
    }
}
