@extends('admin.admin_master')
@section('admin')


 <div class="content-wrapper">
	  <div class="container-full">
		<!-- Content Header (Page header) -->
	

<section class="content">

		 <!-- Basic Forms --> 
		  <div class="box">
			<div class="box-header with-border">
			  <h4 class="box-title">Update User</h4>
			  
			</div>
			<!-- /.box-header -->
			<div class="box-body">
			  <div class="row">
				<div class="col">

	 <form method="post" action="{{ route('users.update',$editData->id) }}">
	 	@csrf
					  <div class="row">
						<div class="col-12">	


<div class="row">
	<div class="col-md-6" >
		<div class="form-group">
	<h5>User Role (Legacy) <span class="text-danger">*</span></h5>
	<div class="controls">
	 <input type="text" name="role_name" class="form-control" value="{{ $editData->role }}" required="">
	 </div>
          </div>
	</div> <!-- End Col Md-6 -->

	<div class="col-md-6" >		
	<div class="form-group">
		<h5>User Name <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="name" class="form-control" value="{{ $editData->name }}" required="">  </div>
	</div>
	</div><!-- End Col Md-6 -->
</div> <!-- End Row -->

<div class="row">
    <div class="col-md-12">
        <h5>Assign Roles</h5>
        <div class="controls">
            <div class="row">
                @foreach($roles as $role)
                <div class="col-md-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" id="role_{{ $role->id }}"
                        {{ $editData->roles->contains($role->id) ? 'checked' : '' }}>
                        <label class="form-check-label" for="role_{{ $role->id }}">
                            {{ $role->name }}
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>



    <div class="row">
	<div class="col-md-6" >

		<div class="form-group">
		<h5>User Email <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="email" name="email" class="form-control" value="{{ $editData->email }}" required="">  </div>
		 
	</div>

	</div> <!-- End Col Md-6 -->

	<div class="col-md-6" >
		<div class="form-group">
		<h5>Password <span class="text-secondary">(Leave blank to keep current)</span></h5>
		<div class="controls">
	 <input type="password" name="password" class="form-control">  </div>
	</div>
	</div><!-- End Col Md-6 -->
	

</div> <!-- End Row -->

 
  
							 
						<div class="text-xs-right">
	 <input type="submit" class="btn btn-rounded btn-info mb-5" value="Update">
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





@endsection
