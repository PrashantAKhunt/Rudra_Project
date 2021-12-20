<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vendors;
use App\Vendors_bank;



class VendorsBankReportController extends Controller
{
    public $data;

    public function __construct() {
        $this->data['module_title'] = "Vendors Bank Report";
       
    }

    public function index() {

        $this->data['page_title'] = "Vendors Bank Report";

        $this->data['records'] = $records= Vendors_bank::join('vendor', 'vendors_bank.vendor_id','=','vendor.id')
            ->join('company', 'vendors_bank.company_id','=','company.id')
            ->get(['vendors_bank.*','vendors_bank.detail AS bank_detail','vendor.*','company.company_name']);

           
        return view('admin.vendorsBank_report.index', $this->data);    

    }
}
