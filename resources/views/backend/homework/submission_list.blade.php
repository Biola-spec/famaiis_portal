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
				  <h3 class="box-title">Submissions for: {{ $homework->title }}</h3>
				</div>
				<!-- /.box-header -->
				<div class="box-body">
					<div class="table-responsive">
					  <table id="example1" class="table table-bordered table-striped">
						<thead>
			<tr>
				<th width="5%">SL</th>  
				<th>Student Name</th> 
                <th>ID No</th>
				<th>Answer</th>
                <th>File</th>
                <th>Submitted At</th>
				<th width="15%">Action</th>
				 
			</tr>
		</thead>
		<tbody>
			@foreach($submissions as $key => $submission )
			<tr>
				<td>{{ $key+1 }}</td>
				<td> {{ $submission['student']['name'] }}</td>				 
                <td> {{ $submission['student']['id_no'] }}</td>				 
                <td> {{ Str::limit($submission->answer, 50) }}</td>				 
                <td> 
                    @if($submission->file)
                        <a href="{{ route('homework.submission.download', $submission->id) }}" class="btn btn-sm btn-primary">Download</a>
                    @else
                        No File
                    @endif
                </td>
                <td> {{ date('d-m-Y H:i', strtotime($submission->created_at)) }}</td>
				<td>
                    @if($submission->answer)
                        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#answerModal{{ $submission->id }}">View Answer</button>
                        
                        <!-- Answer Modal -->
                        <div class="modal fade" id="answerModal{{ $submission->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                          <div class="modal-dialog" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">Answer from {{ $submission['student']['name'] }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                  <span aria-hidden="true">&times;</span>
                                </button>
                              </div>
                              <div class="modal-body">
                                {{ $submission->answer }}
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
