@extends('admin.admin_master')
@section('admin')


  <div class="content-wrapper">
	  <div class="container-full">
		<!-- Content Header (Page header) -->
		 

		<!-- Main content -->
		<section class="content">
		  <div class="row">
			  
			 

			<div class="col-12">

			 <div class="box">
				<div class="box-header with-border">
				  <h3 class="box-title">Upcoming Events List</h3>
	<a href="{{ route('event.add') }}" style="float: right;" class="btn btn-rounded btn-success mb-5"> Add Event</a>			  

				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class="table-responsive">
					  <table id="example1" class="table table-bordered table-striped">
						<thead>
			<tr>
				<th width="5%">SL</th>  
				<th>Title</th>
				<th>Date</th>
				<th>Location</th>
				<th>Section</th>
				<th>Notified</th>
				<th width="25%">Action</th>
				 
			</tr>
		</thead>
		<tbody>
			@foreach($allData as $key => $event )
			<tr>
				<td>{{ $key+1 }}</td>
				<td> {{ $event->title }}</td>
				<td> 
                    {{ date('d M Y', strtotime($event->event_date)) }} <br>
                    <small class="text-muted">{{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('h:i A') : '' }}</small>
                </td>
				<td> {{ $event->location }}</td>
				<td> {{ $event->section->name ?? 'All Sections' }}</td>
				<td>
					@if($event->is_notified)
					<span class="badge badge-success">Yes</span>
					@else
					<span class="badge badge-danger">No</span>
					@endif
				</td>
				<td>
<a href="{{ route('event.edit',$event->id) }}" class="btn btn-info" title="Edit"><i class="fa fa-edit"></i></a>
<a href="{{ route('event.registrations.view',$event->id) }}" class="btn btn-primary" title="View Registrations"><i class="fa fa-users"></i></a>
<a href="{{ route('event.delete',$event->id) }}" class="btn btn-danger" id="delete" title="Delete"><i class="fa fa-trash"></i></a>

				</td>
				 
			</tr>
			@endforeach
							 
						</tbody>
						<tfoot>
							 
						</tfoot>
					  </table>
					</div>
				</div>
				<!-- /.box-body -->
			  </div>
			  <!-- /.box -->

			       
			</div>
			<!-- /.col -->
		  </div>
		  <!-- /.row -->
		</section>
		<!-- /.content -->
	  
	  </div>
  </div>





@endsection
