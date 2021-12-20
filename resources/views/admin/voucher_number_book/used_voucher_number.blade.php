@extends('layouts.admin_app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.css" integrity="sha256-JHRpjLIhLC03YGajXw6DoTtjpo64HQbY5Zu6+iiwRIc=" crossorigin="anonymous" />
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">{{ $page_title }}</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12">
            @if (session('error'))
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{ session('error') }}
            </div>
            @endif
            @if (session('success'))
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{ session('success') }}
            </div>
            @endif
            <div class="white-box">
            <!-- {{ route('admin.add_rtgs_register') }} -->
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="company_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Voucher Book Ref No</th>
                                <th>Voucher Number</th>
                                <th>Company</th>
                                <th>Expense/Cash-Entry Code</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>

            <!--row -->

        </div>
        @endsection
        @section('script')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/8.11.8/sweetalert2.js" integrity="sha256-FmcrRIeUicq2hy0eo5tD5h2Iv76IBfc3A51x8r9xeIY=" crossorigin="anonymous"></script>
        <script>
            $(document).ready(function () {
                var table = $('#company_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'excel'
                    ],
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    // "order": [[1, "DESC"]],
                    stateSave: true,
                    "ajax": {
                        url: "<?php echo route('admin.get_used_voucher_number_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, 'data': 'voucher_ref_no'
                        },
                        {"taregts": 1, 'data': 'voucher_no'
                        },
                        {"taregts": 2, 'data': 'company_name'
                        },
                        {"taregts":3,"searchable": false, "orderable": false,'render' : function(data, type , row){
                                if(row.expense_code){
                                    return '<span style="display:none" id="copy_'+row.id+'" >'+row.expense_code+'</span><button type="button" class="btn btn-light" onclick="myFunction(copy_'+row.id+')" title="Click to copy">'+row.expense_code+'</button>';
                                }else if(row.entry_code){
                                    // return "Used in cash payment";
                                    return '<span style="display:none" id="copy_'+row.id+'" >'+row.entry_code+'</span><button type="button" class="btn btn-light" onclick="myFunction(copy_'+row.id+')" title="Click to copy">'+row.entry_code+'</button>';
                                }else{
                                    return "";
                                }
                            }
                        }
                    ]

                });

            })
            function myFunction(element){
                // alert(element);
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val($(element).text()).select();
                document.execCommand("copy");
                $temp.remove();
                /* $.toast({
                    heading: 'Copy Code',
                    position: 'top-right',
                    loaderBg:'#ff6849',
                    icon: 'success',
                    hideAfter: 3500,
                    stack: 6
                }); */
                const Toast = Swal.mixin({
                toast: true,
                position: 'center',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                onOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
                })

                Toast.fire({
                icon: 'success',
                title: 'Copy successfully'
                })
            }
        </script>
        @endsection
