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
			  <h4 class="box-title">Add Event </h4>
			  
			</div>
			<!-- /.box-header -->
			<div class="box-body">
			  <div class="row">
				<div class="col">

	 <form method="post" action="{{ route('event.store') }}" enctype="multipart/form-data">
	 	@csrf
					  <div class="row">
						<div class="col-12">	
 

 	
 		<div class="row"> <!-- 1st Row -->
 			
 			<div class="col-md-4">

 		 <div class="form-group">
		<h5>Event Title <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="text" name="title" class="form-control" required="" > 
	  </div>		 
	  </div>

 			</div> <!-- End Col md 4 -->


	<div class="col-md-4">

 		 <div class="form-group">
		<h5>Event Date <span class="text-danger">*</span></h5>
		<div class="controls">
	 <input type="date" name="event_date" class="form-control" required="" > 
	  </div>		 
	  </div>
	  
 			</div> <!-- End Col md 4 -->
            
    <div class="col-md-4">

        <div class="form-group">
       <h5>Event Time <span class="text-danger">*</span></h5>
       <div class="controls">
    <input type="time" name="event_time" class="form-control" required="" > 
     </div>		 
     </div>
     
            </div> <!-- End Col md 4 -->
 			
 		</div> <!-- End 1stRow -->






	<div class="row"> <!-- 2nd Row -->
 			
 			<div class="col-md-6">

 		 <div class="form-group">
		<h5>Location </h5>
		<div class="controls">
	 <input type="text" name="location" class="form-control" > 
     <small class="text-info">Note: Providing a specific location helps users find the event (e.g., School Hall, Sports Ground).</small>
	  </div>		 
	  </div>

 			</div> <!-- End Col md 6 -->


	<div class="col-md-6">

 		 <div class="form-group">
		<h5>Section (Optional)</h5>
		<div class="controls">
	 <select name="section_id" class="form-control">
			<option value="" selected="">All Sections</option>
			 @foreach($sections as $section)
			<option value="{{ $section->id }}">{{ $section->name }}</option>
		 	@endforeach
		</select>
	  </div>		 
	  </div>
	  
 			</div> <!-- End Col md 6 -->
 
 			
 		</div> <!-- End 2nd Row -->



<div class="row"> <!-- 3rd Row -->

<div class="col-md-6">
    <div class="form-group">
        <h5>Button Text (CTA) <span class="text-danger">*</span></h5>
        <div class="controls">
            <input type="text" name="cta_text" class="form-control" required="" placeholder="e.g. Register, Show Interest, Join Us">
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <h5>Notification Reminder (Date & Time)</h5>
        <div class="controls">
            <input type="datetime-local" name="reminder_at" class="form-control">
            <small class="text-info">Set the exact date and time to send email reminders to registrants.</small>
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="form-group">
        <h5>Description </h5>
        <div class="controls">
            <textarea name="description" class="form-control" rows="3"></textarea>
        </div>
    </div>
</div>
	  
</div> <!-- End 3rd Row -->


<div class="row"> <!-- 4th Row -->

<div class="col-md-6">
    <div class="form-group">
        <h5>Event Image <span class="text-danger">(Max 20MB)</span></h5>
        <div class="controls">
            <input type="file" name="image" class="form-control" id="image">
        </div>
    </div>
</div>

<div class="col-md-6">
    <div class="form-group">
        <div class="controls">
            <img id="showImage" src="{{ url('upload/no_image.jpg') }}" style="width: 100px; height: 100px; border: 1px solid #000000;">
        </div>
    </div>
</div>

</div> <!-- End 4th Row -->


<div class="row"> <!-- 5th Row -->

<div class="col-md-12">

 		 <div class="form-group">
		<div class="controls">
			<fieldset>
				<input type="checkbox" id="checkbox_1" name="notify_all" value="1" class="filled-in chk-col-primary">
				<label for="checkbox_1">Notify All Relevant Users (Send Notification)</label>
			</fieldset>
	  </div>		 
	  </div>
	  
 			</div> <!-- End Col md 12 --> 
 
 			
 		</div> <!-- End 5th Row -->

 
							 
						<div class="text-xs-right">
	 <input type="submit" class="btn btn-rounded btn-info mb-5" value="Submit">
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
	$(document).ready(function(){
		$('#image').change(function(e){
			var reader = new FileReader();
			reader.onload = function(e){
				$('#showImage').attr('src',e.target.result);
			}
			reader.readAsDataURL(e.target.files['0']);
		});
	});
</script>

@endsection
