@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="content-wrapper">
    <div class="container-full">
        <!-- Content Header (Page header) -->

        <section class="content">

            <!-- Basic Forms -->
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Update Global Site Setting</h4>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="row">
                        <div class="col">

                            <form method="post" action="{{ route('update.site.setting') }}" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" value="{{ $setting->id }}">

                                <div class="row">
                                    <div class="col-12">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <h5>School Name <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <input type="text" name="school_name" class="form-control" value="{{ $setting->school_name }}" required="">
                                                    </div>
                                                </div>
                                            </div> <!-- end col-md-6 -->

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <h5>Current Academic Session <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <input type="text" name="current_session" class="form-control" value="{{ $setting->current_session }}" required="">
                                                    </div>
                                                </div>
                                            </div> <!-- end col-md-6 -->
                                        </div> <!-- end row -->

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <h5>School Email <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <input type="email" name="school_email" class="form-control" value="{{ $setting->school_email }}" required="">
                                                    </div>
                                                </div>
                                            </div> <!-- end col-md-6 -->

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <h5>Contact Mobile (Primary) <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <input type="text" name="school_mobile_one" class="form-control" value="{{ $setting->school_mobile_one }}" required="">
                                                    </div>
                                                </div>
                                            </div> <!-- end col-md-6 -->
                                        </div> <!-- end row -->

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <h5>School Address <span class="text-danger">*</span></h5>
                                                    <div class="controls">
                                                        <input type="text" name="school_address" class="form-control" value="{{ $setting->school_address }}" required="">
                                                    </div>
                                                </div>
                                            </div> <!-- end col-md-6 -->

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <h5>Contact Mobile (Secondary)</h5>
                                                    <div class="controls">
                                                        <input type="text" name="school_mobile_two" class="form-control" value="{{ $setting->school_mobile_two }}">
                                                    </div>
                                                </div>
                                            </div> <!-- end col-md-6 -->
                                        </div> <!-- end row -->

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <h5>Copyright Text</h5>
                                                    <div class="controls">
                                                        <input type="text" name="copyright" class="form-control" value="{{ $setting->copyright }}">
                                                    </div>
                                                </div>
                                            </div> <!-- end col-md-6 -->
                                            
                                            <div class="col-md-6">
                                                 <div class="form-group">
                                                    <h5>School Logo</h5>
                                                    <div class="controls">
                                                        <input type="file" name="logo" class="form-control" id="image">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <img id="showImage" src="{{ (!empty($setting->logo))? url($setting->logo):url('upload/no_image.jpg') }}" style="width: 100px; width: 100px; border: 1px solid #000000;">
                                                    </div>
                                                </div>
                                            </div> <!-- end col-md-6 -->
                                        </div> <!-- end row -->

                                        <div class="row">
                                            <div class="col-md-6">
                                                 <div class="form-group">
                                                    <h5>Login Page Image</h5>
                                                    <div class="controls">
                                                        <input type="file" name="login_image" class="form-control" id="login_image_input">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="controls">
                                                        <img id="showLoginImage" src="{{ (!empty($setting->login_image))? url($setting->login_image):url('upload/no_image.jpg') }}" style="width: 100px; width: 100px; border: 1px solid #000000;">
                                                    </div>
                                                </div>
                                            </div> <!-- end col-md-6 -->
                                        </div> <!-- end row -->



                                        <div class="text-xs-right">
                                            <input type="submit" class="btn btn-rounded btn-info mb-5" value="Update Settings">
                                        </div>
                                    </div>
                                </div>
                            </form>

                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

        </section>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#image').change(function(e) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#showImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files['0']);
        });

        $('#login_image_input').change(function(e) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#showLoginImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files['0']);
        });
    });
</script>

@endsection
