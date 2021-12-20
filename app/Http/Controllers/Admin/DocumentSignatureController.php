<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Job_opening;
use App\Job_opening_consultant;
use App\Interview;
use App\User;
use App\DocumentSignature;
use App\InterviewResult;
use App\Department;
use App\Email_format;
use App\Mail\Mails;
use Exception;
use App\Recruitment_consultant;
use DB;
use Illuminate\Support\Facades\Mail;
use Auth;
use App\Lib\Permissions;
use App\Role_module;

class DocumentSignatureController extends Controller {

    public function __construct() {
        $this->data['module_title'] = "Documnet Signature";
        // $this->data['module_link'] = "admin.docmentsignature";
    }


    public function index() {
        // dd("Inn");
        $this->data['page_title'] = "Document Signature";
        return view('admin.document_signature.index', $this->data);
    }

    public function add_modules() {
        $this->data['page_title'] = 'Add Document Signature';
        $this->data['user'] = User::getUser();
        return view('admin.document_signature.add_modules', $this->data);
    }



}
