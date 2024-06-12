@extends('layout.index')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .sync-button{
        float: right;
    }
    .view{
        font-size: 10px !important;
    }
    .select2-container{
        width: 100% !important;
    }
    .size_button{
        font-size: 13px !important;
    }
    .btn_size{
        font-size: 12px !important;
    }
    .input-group > :not(:first-child):not(.dropdown-menu):not(.valid-tooltip):not(.valid-feedback):not(.invalid-tooltip):not(.invalid-feedback){
        margin-left: 2px !important;
    }
    .display_none
    {
        display: none;
    }
    .pop_up{
        display: none;
    }
    span.select2.select2-container.select2-container--default {
        width: 150px !important;
        padding: 2px;
        background-color: #fff;
        border: 1px solid #dadcde;
    }
    .theme-light{
        overflow-x: hidden;
    }
    .error-alert{
        background: #d94343 !important;
        color: white !important;
    }
    .error_icon{
        color: white !important;
    }

</style>
@section('content')
    @if(session()->has('message'))
        <div class="alert alert-important alert-success alert-dismissible "  role="alert" id="alertSuccess">
            <div class="d-flex">
                <div>
                    <!-- Download SVG icon from http://tabler-icons.io/i/check -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>
                </div>
                <div id="alertSuccessText">
                    {{ session()->get('message') }}
                </div>
            </div>
            {{--            <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>--}}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert  alert-error error-alert alert-dismissible "  role="alert" id="alertSuccess2">
            <div class="d-flex">
                <div>
                    <!-- Download SVG icon from http://tabler-icons.io/i/check -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon error_icon alert-icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M5 12l5 5l10 -10"></path></svg>
                </div>
                <div id="alertSuccessText">
                    {{ session()->get('error') }}
                </div>
            </div>
            {{--            <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>--}}
        </div>
    @endif





    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">

                <h1 class="page-title">
                    Companies
                </h1>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <button type="button" data-bs-toggle="modal"
                            data-bs-target="#add-sku"  class="btn sync-button btn-primary size_button ml-1">Add Company</button>
                </div>

                <div class="modal modal-blur fade" id="add-sku"  role="dialog" aria-hidden="true">
                    <form method="post" action="{{route('add.company')}}">
                        @sessionToken
                        <div class="modal-dialog  modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Company</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>

                                <div class="modal-body">

                                    <div class="row">
                                        <div class="col-lg-12 ">
                                            <div class="mb-3">
                                                <label class="form-label">Company Name</label>
                                                <input type="text"
                                                       name="name" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-12 ">
                                            <div class="mb-3">
                                                <label class="form-label">Company ID</label>
                                                <input type="text"
                                                       name="shopify_id" class="form-control">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary ms-auto">

                                        Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>




        </div>
    </div>



    <div class="page-body">
        <div class="">
            <div class="row row-cards">
                <div class="col-12">
                    <div class="card">
                        <div class="table-responsive">
                            @if (count($companies) > 0)
                                <table
                                    class="table table-vcenter card-table">
                                    <thead>
                                    <tr>
                                        <th>Company ID</th>
                                        <th>Company Name</th>
                                        <th>Action</th>

                                    </tr>
                                    </thead>
                                    <tbody>

                                    @foreach($companies as $company)
                                        <tr>
                                            <td> {{$company->shopify_id}}</td>
                                            <td>{{$company->name}}</td>
                                            <td style="display: flex">
                                                <a data-bs-toggle="modal" data-bs-target="#edit-sku{{$company->id}}" type="button" class="btn btn-primary mx-1">Edit</a>
                                                <a  type="button" href="{{URL::tokenRoute('delete.company',$company->id)}}" class="btn btn-danger mx-1">Delete</a>

                                                <div class="modal modal-blur fade" id="edit-sku{{$company->id}}"  role="dialog" aria-hidden="true">
                                                    <form method="post" action="{{route('edit.company',$company->id)}}">
                                                        @sessionToken
                                                        <div class="modal-dialog  modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Edit Company</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>

                                                                <div class="modal-body">

                                                                    <div class="row">
                                                                        <div class="col-lg-12 ">
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Company Name</label>
                                                                                <input type="text"   value="{{$company->name}}"
                                                                                       name="name" class="form-control">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-12 ">
                                                                            <div class="mb-3">
                                                                                <label class="form-label">Company ID</label>
                                                                                <input type="text"   value="{{$company->shopify_id}}"
                                                                                       name="shopify_id" class="form-control">
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <a href="#" class="btn btn-link link-secondary" data-bs-dismiss="modal">
                                                                        Cancel
                                                                    </a>
                                                                    <button type="submit" class="btn btn-primary ms-auto">

                                                                        Update
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </td>

                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <h3 class="mx-3 my-3">No Company Found</h3>
                            @endif

                            <div class="pagination">
                                {{ $companies->appends(\Illuminate\Support\Facades\Request::except('page'))->links("pagination::bootstrap-4") }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>

        $(document).ready(function (){

            $('.export_button').click(function(){
                $('#export_form').submit();
            });

            setTimeout(function() { $(".alert-success").hide(); }, 2000);
            setTimeout(function() { $(".error-alert").hide(); }, 2000);
            $('.js-example').select2();

            $('select[name=date]').on('change',function(){
                if($(this).val()=='custom')
                {
                    $('.date_range').removeClass('display_none');
                }else
                {
                    $('.date_range').addClass('display_none');
                }
            });


            $('body').on('click','.single_check',function(){


                if($('.single_check:checked').length >0){

                    $('.export_button').show();
                }
                else{
                    $('.export_button').hide();
                }
                var val = [];
                $('.single_check:checked').each(function(i){
                    val[i] = $(this).val();
                });


                var order_ids= val.join(',');
                $('#order_ids').val(order_ids);

            });



            $('body').on('click','.submit_loader',function (){
                $('body').append('<div class="LockOn"> </div>');
            });


            $("#checkAll").change(function(){

                if($('#checkAll').prop('checked')) {
                    $('.single_check').prop('checked', true)
                    $('.export_button').show();
                    var val = [];
                    $('.single_check:checked').each(function(i){
                        val[i] = $(this).val();
                    });

                    var order_ids= val.join(',');
                    $('#order_ids').val(order_ids);

                }else {
                    $('.single_check').prop('checked', false);
                    $('.export_button').hide();
                    $('#order_ids').val('');
                }
            });
        })
    </script>
@endsection

