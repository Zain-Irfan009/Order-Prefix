@extends('layout.index')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.css"
      integrity="sha512-xmGTNt20S0t62wHLmQec2DauG9T+owP9e6VU8GigI0anN7OXLip9i7IwEhelasml2osdxX71XcYm6BQunTQeQg=="
      crossorigin="anonymous"/>
<style>
    .sync-button{
        float: right;
        margin-right: 1px;
    }
    .setting_heading{
        text-decoration: underline;
    }
    .bootstrap-tagsinput {
        width: 100%;
    }
    .label-info {
        background-color: #17a2b8;
    }
    .label {
        display: inline-block;
        padding: .25em .4em;
        font-size: 75%;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: .25rem;
        transition: color .15s ease-in-out, background-color .15s ease-in-out,
        border-color .15s ease-in-out, box-shadow .15s ease-in-out;
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
    <div class="page-header d-print-none">
        <div class="row g-2 align-items-center">
            <div class="col-md-6">

                <h1 class="page-title">
                    Settings
                </h1>
            </div>

        </div>
    </div>

    <div class="page-body">
        <div class="">



                <div class="row row-cards">
                    <div class="col-12">
                        <form method="post" action="{{route('settings.save')}}">
                            @sessionToken
                            <div class="card">
                                <div class="card-body">

{{--                                    <div class="row mt-2">--}}
{{--                                        <div class="col-12">--}}
{{--                                            <h3 style="text-decoration: underline;">Order Prefix</h3>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <div class="row mt-2">

                                        <div class="col-4 ">
                                            <label class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" name="update_prefix" @if(isset($setting) && $setting->update_prefix == 1) checked @endif value="1">
                                                <span class="form-check-label">Update Prefix</span>
                                            </label>
                                        </div>

                                    </div>

                                    <div class="row mt-2">
                                        <div class="col-6">
                                        <label class="form-label">Company Ids to be excluded</label>
                                        <input type="text" data-role="tagsinput" value="@if(isset($setting) && $setting->company_ids_excluded){{$setting->company_ids_excluded}}@endif"
                                               name="company_ids_excluded" class="form-control">
                                        </div>
                                    </div>


                                </div>



                                <div class="row mt-1">
                                    <div class="col-6"></div>
                                    <div class="col-6">
                                        <button type="submit" class="btn sync-button btn-primary mx-4 mb-3">Save</button>

                                    </div>
                                </div>

                            </div>
                    </form>
                </div>

        </div>

    </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput.js"
            integrity="sha512-VvWznBcyBJK71YKEKDMpZ0pCVxjNuKwApp4zLF3ul+CiflQi6aIJR+aZCP/qWsoFBA28avL5T5HA+RE+zrGQYg=="
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.8.0/bootstrap-tagsinput-angular.min.js"
            integrity="sha512-KT0oYlhnDf0XQfjuCS/QIw4sjTHdkefv8rOJY5HHdNEZ6AmOh1DW/ZdSqpipe+2AEXym5D0khNu95Mtmw9VNKg=="
            crossorigin="anonymous"></script>
    <script>
        $(document).ready(function(){

            setTimeout(function() { $(".alert-success").hide(); }, 2000);
        });

    </script>
@endsection

