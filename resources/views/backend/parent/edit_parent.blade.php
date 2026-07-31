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
					</div>
					</div>
					</form>
				</div>
			  </div>
			</div>
		  </div>

		  <div class="box" id="results-link">
			<div class="box-header with-border">
			  <h4 class="box-title">Share results link (no login)</h4>
			</div>
			<div class="box-body">
				<p class="text-muted">Generate a short web link parents can open on any phone or browser to view and download report cards.</p>

				@if(session('parent_result_link'))
					<div class="alert alert-success">
						<strong>New link:</strong>
						<div class="input-group mt-2">
							<input type="text" class="form-control" id="generated-parent-link" readonly value="{{ session('parent_result_link') }}">
							<div class="input-group-append">
								<button type="button" class="btn btn-primary" onclick="copyParentLink()">Copy</button>
								<a href="{{ session('parent_result_link') }}" target="_blank" class="btn btn-info">Open</a>
							</div>
						</div>
					</div>
				@endif

				@if(isset($activeLinks) && $activeLinks->count())
					<h5 class="mt-3">Active links</h5>
					<ul class="list-group mb-3">
						@foreach($activeLinks as $link)
							<li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
								<span>
									<a href="{{ $link->shortUrl() }}" target="_blank"><code>{{ $link->shortUrl() }}</code></a>
									<small class="text-muted d-block">
										@if($link->expires_at) Expires {{ $link->expires_at->format('M j, Y') }} @else No expiry @endif
										· Opens: {{ $link->access_count }}
									</small>
								</span>
								<form method="post" action="{{ route('parent.result.link.destroy', $link->id) }}" class="ml-2">
									@csrf
									<button type="submit" class="btn btn-sm btn-outline-danger">Deactivate</button>
								</form>
							</li>
						@endforeach
					</ul>
				@endif

				<form method="post" action="{{ route('parent.result.link.store', $editData->id) }}">
					@csrf
					<div class="row">
						<div class="col-md-3">
							<label>Student (optional)</label>
							<select name="student_id" class="form-control">
								<option value="">All linked children</option>
								@foreach($editData->children as $child)
									<option value="{{ $child->id }}">{{ $child->name }} ({{ $child->id_no }})</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-3">
							<label>Default session</label>
							<select name="year_id" class="form-control">
								<option value="">Current session</option>
								@foreach($sessions as $session)
									<option value="{{ $session->id }}">{{ $session->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-3">
							<label>Default term</label>
							<select name="term" class="form-control">
								<option value="">Any term</option>
								@foreach(['1st Term', '2nd Term', '3rd Term'] as $term)
									<option value="{{ $term }}">{{ $term }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-3">
							<label>Expires in (days)</label>
							<input type="number" name="expires_in_days" class="form-control" min="1" max="365" placeholder="90 (optional)">
						</div>
					</div>
					<div class="mt-3">
						<button type="submit" class="btn btn-success">Generate new link</button>
						<small class="text-muted ml-2">Creating a new link deactivates previous active links for this parent.</small>
					</div>
				</form>
			</div>
		  </div>
		</section>
	  </div>
  </div>

<script type="text/javascript">
	function copyParentLink() {
		var input = document.getElementById('generated-parent-link');
		input.select();
		input.setSelectionRange(0, 99999);
		document.execCommand('copy');
	}
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
