@extends('admin.admin_master')
@section('admin')

@php
    $user = Auth::user();
    $is_admin = ($user->role == 'Admin' || $user->hasRole('Admin'));
    $is_teacher = ($user->role == 'Teacher' || $user->hasRole('Teacher'));
    $is_student = ($user->role == 'Student' || $user->hasRole('Student'));
@endphp

 <div class="content-wrapper">
	  <div class="container-full">
		<!-- Content Header (Page header) -->
		<!-- Main content -->
		<section class="content">
		  <div class="row">

			<div class="col-12">

			 <div class="box">
				<div class="box-header with-border">
				  <h3 class="box-title">Home Work / Note List</h3>
                  @if($is_admin || $is_teacher)
	                <a href="{{ route('homework.add') }}" style="float: right;" class="btn btn-rounded btn-success mb-5"> Add Home Work / Note</a>			  
                  @endif
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class="table-responsive">
					  <table id="example1" class="table table-bordered table-striped">
						<thead>
			<tr>
				<th width="5%">SL</th>  
				<th>Title</th>
                <th>Class</th>
                <th>Subject</th>
				<th>Type</th>
                <th>Uploaded On</th>
                <th>Due Date</th>
                @if($is_student)
                <th>Status</th>
                @endif
				<th width="25%">Action</th>
				 
			</tr>
		</thead>
		<tbody>
			@foreach($allData as $key => $homework )
			<tr>
				<td>{{ $key+1 }}</td>
				<td>{{ $homework->title }}</td>				 
                <td>{{ $homework['student_class']['name'] }}</td>				 
                <td>{{ $homework['school_subject']['name'] }}</td>				 
                <td>
                    @if($homework->type == 'homework')
                        <span class="badge badge-primary">Homework</span>
                    @else
                        <span class="badge badge-info">Lesson Note</span>
                    @endif
                </td>
                <td>
                    {{ $homework->created_at ? $homework->created_at->format('d-m-Y h:i A') : '-' }}
                </td>
                <td>
                    @if($homework->due_date)
                        {{ date('d-m-Y', strtotime($homework->due_date)) }}
                        @if($homework->type == 'homework' && strtotime($homework->due_date) < time())
                            <br><small class="text-danger">Late</small>
                        @endif
                    @else
                        -
                    @endif
                </td>

                @if($is_student)
                <td>
                    @if($homework->type == 'homework')
                        @php
                            $submission = $homework->submissions->where('student_id', Auth::user()->id)->first();
                        @endphp
                        @if($submission)
                            <span class="badge badge-success">Submitted</span>
                        @else
                            <span class="badge badge-warning">Pending</span>
                        @endif
                    @else
                        -
                    @endif
                </td>
                @endif

				<td>
                    @if($homework->file)
                        <a href="{{ route('homework.download', $homework->id) }}" class="btn btn-sm btn-primary" title="Download Attachment"><i class="fa fa-download"></i></a>
                    @endif

                    @if($is_admin || $is_teacher)
                        <a href="{{ route('homework.edit', $homework->id) }}" class="btn btn-sm btn-info" title="Edit"><i class="fa fa-edit"></i></a>
                        <a href="{{ route('homework.delete', $homework->id) }}" class="btn btn-sm btn-danger" id="delete" title="Delete"><i class="fa fa-trash"></i></a>
                        @if($homework->type == 'homework')
                            <a href="{{ route('homework.submission.view', $homework->id) }}" class="btn btn-sm btn-secondary" title="View Submissions"><i class="fa fa-users"></i></a>
                        @endif
                    @endif

                    @if($is_student && $homework->type == 'homework')
                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#submitModal{{ $homework->id }}">
                          {{ $homework->submissions->where('student_id', Auth::user()->id)->first() ? 'Re-submit' : 'Submit' }}
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="submitModal{{ $homework->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Submit Answer: {{ $homework->title }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <form action="{{ route('homework.submission.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="homework_id" value="{{ $homework->id }}">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <h5>Text Answer <span class="text-danger"></span></h5>
                                        <div class="controls">
                                            <textarea name="answer" class="form-control" rows="5">{{ $homework->submissions->where('student_id', Auth::user()->id)->first() ? $homework->submissions->where('student_id', Auth::user()->id)->first()->answer : '' }}</textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <h5>Attach File (PDF, Word, Excel) <span class="text-danger"></span></h5>
                                        <div class="controls">
                                            <input type="file" name="file" class="form-control">
                                            <small class="text-muted">Allowed: pdf, doc, docx, xls, xlsx (Max 2MB)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Submit Answer</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                    @endif

                    @if($homework->description)
                        <button type="button" class="btn btn-sm btn-dark" data-toggle="modal" data-target="#descModal{{ $homework->id }}" title="View Description">
                            <i class="fa fa-eye"></i>
                        </button>
                        <!-- Desc Modal -->
                        <div class="modal fade" id="descModal{{ $homework->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">{{ $homework->title }} - Description</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                {{ $homework->description }}
                              </div>
                            </div>
                          </div>
                        </div>
                    @endif

				</td>
				 
			</tr>
			@endforeach
							 
						</tbody>
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
