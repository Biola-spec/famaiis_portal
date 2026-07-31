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
			  <h4 class="box-title">Edit Home Work / Note</h4>
			  
			</div>
			<!-- /.box-header -->
			<div class="box-body">
			  <div class="row">
				<div class="col">

	 <form method="post" action="{{ route('homework.update', $editData->id) }}" enctype="multipart/form-data">
	 	@csrf
					  <div class="row">
						<div class="col-12">	
 

 		<div class="row"> <!-- 1st Row -->

 			<div class="col-md-4">
 				 <div class="form-group">
		<h5>Title <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="title" class="form-control" required="" value="{{ $editData->title }}"> 
	  </div>		 
	</div>
 			</div> <!-- End Col md 4 -->

			<div class="col-md-4">
 				 <div class="form-group">
		<h5>Class <span class="text-danger">*</span></h5>
		<div class="controls">
	 <select name="class_id" required="" class="form-control">
			<option value="" selected="" disabled="">Select Class</option>
			@foreach($classes as $class)
			<option value="{{ $class->id }}" {{ ($editData->class_id == $class->id)? "selected":"" }}>{{ $class->name }}</option>
			@endforeach
		</select>
	  </div>		 
	</div>
 			</div> <!-- End Col md 4 -->

             <div class="col-md-4">
 				 <div class="form-group">
		<h5>Subject <span class="text-danger">*</span></h5>
		<div class="controls">
	 <select name="subject_id" required="" class="form-control">
			<option value="" selected="" disabled="">Select Subject</option>
			@foreach($subjects as $subject)
			<option value="{{ $subject->id }}" {{ ($editData->subject_id == $subject->id)? "selected":"" }}>{{ $subject->name }}</option>
			@endforeach
		</select>
	  </div>		 
	</div>
 			</div> <!-- End Col md 4 -->
 			
 		</div> <!-- End 1st Row -->


<div class="row"> <!-- 2nd Row -->

 			<div class="col-md-4">
 				 <div class="form-group">
		<h5>Type <span class="text-danger">*</span></h5>
		<div class="controls">
	 <select name="type" required="" class="form-control">
			<option value="" selected="" disabled="">Select Type</option>
			<option value="homework" {{ ($editData->type == 'homework')? "selected":"" }}>Homework</option>
            <option value="note" {{ ($editData->type == 'note')? "selected":"" }}>Lesson Note</option>
		</select>
	  </div>		 
	</div>
 			</div> <!-- End Col md 4 -->

			<div class="col-md-4">
 				 <div class="form-group">
		<h5>Due Date (Only for Homework) <span class="text-danger"></span></h5>
		<div class="controls">
	 <input type="date" name="due_date" class="form-control" value="{{ $editData->due_date }}"> 
	  </div>		 
	</div>
 			</div> <!-- End Col md 4 -->

             <div class="col-md-4">
 				 <div class="form-group">
		<h5>File (PDF, Word, Excel) <span class="text-danger"></span></h5>
		<div class="controls">
	 <input type="file" name="file" class="form-control" > 
     @if($editData->file)
        <small class="text-info">Current file: {{ $editData->file }}</small>
     @endif
	  </div>		 
	</div>
 			</div> <!-- End Col md 4 -->
 			
 		</div> <!-- End 2nd Row -->

         <div class="row"> <!-- 3rd Row -->
            <div class="col-md-12">
                <div class="form-group">
                    <h5>Description <span class="text-danger"></span></h5>
                    <div class="controls">
                        <textarea name="description" class="form-control" rows="5">{{ $editData->description }}</textarea>
                    </div>
                </div>
            </div>
         </div> <!-- End 3rd Row -->

 
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
