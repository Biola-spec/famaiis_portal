@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

 <div class="content-wrapper">
	  <div class="container-full">
		<section class="content">
		  <div class="box">
			<div class="box-header with-border">
			  <h4 class="box-title">Edit Parent</h4>
			</div>
			<div class="box-body">
			  <div class="row">
				<div class="col">
	 <form method="post" action="{{ route('parent.update', $editData->id) }}" enctype="multipart/form-data">
	 	@csrf
					  <div class="row">
						<div class="col-12">	
<div class="row">
	<div class="col-md-4">
		<div class="form-group">
			<h5>Parent Name <span class="text-danger">*</span></h5>
			<div class="controls">
				<input type="text" name="name" class="form-control" value="{{ $editData->name }}" required="">
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<h5>Parent Email <span class="text-danger">*</span></h5>
			<div class="controls">
				<input type="email" name="email" class="form-control" value="{{ $editData->email }}" required="">
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<div class="form-group">
			<h5>Password <span class="text-secondary">(Leave blank to keep current)</span></h5>
			<div class="controls">
				<input type="password" name="password" class="form-control">
			</div>
		</div>
	</div>
</div>

<div class="row">
    <div class="col-md-12">
        <h5>Assign Children (Students)</h5>
        <div class="controls">
            <select name="student_id[]" multiple class="form-control select2" style="width: 100%;">
                @foreach($students as $student)
                <option value="{{ $student->id }}" {{ $editData->children->contains($student->id) ? 'selected' : '' }}>
                    {{ $student->name }} ({{ $student->id_no }})
                </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <h5>Profile Image</h5>
            <div class="controls">
                <input type="file" name="image" class="form-control" id="image">
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <div class="controls">
                <img id="showImage" src="{{ (!empty($editData->image))? url('upload/parent_images/'.$editData->image):url('upload/no_image.jpg') }}" style="width: 100px; height: 100px; border: 1px solid #000000;">
            </div>
        </div>
    </div>
</div>

<br>
						<div class="text-xs-right">
	 <input type="submit" class="btn btn-rounded btn-info mb-5" value="Update">
						</div>
					</form>
				</div>
			  </div>
			</div>
		  </div>
		</section>
	  </div>
  </div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#image').change(function(e){
			var reader = new FileReader();
			reader.onload = function(e){
				$('#showImage').attr('src',e.target.result);
			}
			reader.readAsDataURL(e.target.files[0]);
		});
	});
</script>
@endsection
