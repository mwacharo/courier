<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Response;

class DownloadController extends Controller
{
    public function getImportTemplateRider(){
        $path = public_path().'/uploads/documents/boxleo-rider-upload-template.xls';
        return Response::download($path);
    }

    public function getImportTemplateMerchant(){
        $path = public_path().'/uploads/documents/boxleo-merchant-upload-template.xls';
        return Response::download($path);
    }

    public function getImportTemplateOrder(){
        $path = public_path().'/uploads/documents/boxleo-order-upload-template.xls';
        return Response::download($path);
    }

    public function getImportTemplateInventory(){
        $path = public_path().'/uploads/documents/boxleo-inventory-upload-template.xls';
        return Response::download($path);
    }

}
