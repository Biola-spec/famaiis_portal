@extends('admin.admin_master')
@section('admin')
<style>
	.parent-list-table th {
		white-space: nowrap;
		font-size: 12px;
		text-transform: uppercase;
		letter-spacing: .02em;
	}
	.parent-name {
		display: inline-block;
		padding: 4px 9px;
		border-radius: 4px;
		background: #111827;
		font-weight: 900;
		color: #ffffff;
		margin-bottom: 5px;
		font-size: 16px;
		line-height: 1.2;
	}
	.parent-email,
	.child-id {
		color: #6b7280;
		font-size: 11px;
	}
	.parent-code {
		display: inline-block;
		margin-top: 5px;
		padding: 4px 8px;
		border-radius: 4px;
		background: #fff7ed;
		color: #9a3412;
		font-size: 13px;
		font-weight: 800;
		letter-spacing: .02em;
	}
	.children-summary {
		min-width: 260px;
	}
	.children-count {
		display: inline-block;
		padding: 3px 8px;
		border-radius: 4px;
		background: #eef5ff;
		color: #1f5f9f;
		font-size: 11px;
		font-weight: 700;
		margin-bottom: 8px;
	}
	.child-list {
		display: grid;
		gap: 6px;
	}
	.child-item {
		display: flex;
		align-items: center;
		gap: 8px;
		padding: 5px 7px;
		border: 1px solid #e5e7eb;
		border-radius: 6px;
		background: #fff;
	}
	.child-avatar {
		width: 24px;
		height: 24px;
		border-radius: 50%;
		background: #2f80ed;
		color: #fff;
		display: inline-flex;
		align-items: center;
		justify-content: center;
		font-weight: 700;
		font-size: 10px;
		flex: 0 0 24px;
	}
	.child-name {
		font-weight: 600;
		color: #1f2937;
		line-height: 1.2;
		font-size: 12px;
	}
	.no-children {
		padding: 8px 10px;
		border: 1px dashed #d1d5db;
		border-radius: 6px;
		color: #6b7280;
		background: #fafafa;
		font-size: 12px;
	}
	.result-link-box {
		min-width: 300px;
	}
	.result-link-actions,
	.row-actions {
		display: flex;
		flex-wrap: wrap;
		gap: 6px;
		align-items: center;
	}
	.result-link-input {
		font-size: 11px;
		background: #f9fafb;
		color: #6b7280;
	}
	.row-actions {
		margin-top: 8px;
		padding-top: 8px;
		border-top: 1px solid #edf0f2;
	}
</style>

 <div class="content-wrapper">
	  <div class="container-full">
		<section class="content">
		  <div class="row">
			<div class="col-12">
			 <div class="box">
				<div class="box-header with-border">
				  <h3 class="box-title">Parent List</h3>
	<a href="{{ route('parent.add') }}" style="float: right;" class="btn btn-rounded btn-success mb-5"> Add Parent</a>			  
				</div>
				<div class="box-body">
					@if(session('parent_result_link'))
						<div class="alert alert-success">
							<strong>Results link created:</strong>
							<div class="input-group mt-2" style="max-width: 760px;">
								<input type="text" class="form-control" id="generated-parent-link" readonly value="{{ session('parent_result_link') }}">
								<div class="input-group-append">
									<button type="button" class="btn btn-primary" onclick="copyParentLink('generated-parent-link')">Copy</button>
									<a href="{{ session('parent_result_link') }}" target="_blank" class="btn btn-info">Open</a>
								</div>
							</div>
						</div>
					@endif
					<div class="table-responsive">
					  <table id="example1" class="table table-bordered table-hover parent-list-table">
						<thead>
			<tr>
				<th width="5%">SL</th>
				<th width="24%">Parent</th>
				<th width="36%">Children</th>
				<th width="28%">Results Link</th>
				<th width="12%">Actions</th>
			</tr>
		</thead>
		<tbody>
			@foreach($allData as $key => $parent )
			<tr>
				<td>{{ $key+1 }}</td>
				<td>
					<div class="parent-name">{{ $parent->name }}</div>
					<div class="parent-email">{{ $parent->email }}</div>
					@if($parent->code)
						<div class="parent-code">Code: {{ $parent->code }}</div>
					@endif
				</td>
				<td>
					<div class="children-summary">
						@if($parent->children->count())
							<span class="children-count">{{ $parent->children->count() }} {{ \Illuminate\Support\Str::plural('child', $parent->children->count()) }}</span>
							<div class="child-list">
								@foreach($parent->children as $child)
									<div class="child-item">
										<span class="child-avatar">{{ strtoupper(substr($child->name ?? 'S', 0, 1)) }}</span>
										<span>
											<span class="child-name">{{ $child->name ?? 'Unnamed Student' }}</span>
											<span class="child-id d-block">ID No: {{ $child->id_no ?? 'N/A' }}</span>
										</span>
									</div>
								@endforeach
							</div>
						@else
							<div class="no-children">No linked student yet</div>
						@endif
					</div>
                </td>
				<td>
@php
	$activeLink = $parent->resultLinks->first();
@endphp
<div class="result-link-box">
@if($activeLink)
	<div class="result-link-actions mb-2">
		<a href="{{ $activeLink->shortUrl() }}" target="_blank" class="btn btn-success btn-sm">Open results link</a>
		<button type="button" class="btn btn-primary btn-sm" onclick="copyParentLink('parent-link-{{ $parent->id }}')">Copy</button>
	</div>
	<input type="text" class="form-control form-control-sm result-link-input" id="parent-link-{{ $parent->id }}" readonly value="{{ $activeLink->shortUrl() }}">
@else
	<form method="post" action="{{ route('parent.result.link.store', $parent->id) }}" style="display:inline-block; margin:0;">
		@csrf
		<input type="hidden" name="redirect_to" value="parent_list">
		<button type="submit" class="btn btn-warning btn-sm">Create results link</button>
	</form>
@endif
</div>
				</td>
				<td>
					<div class="row-actions">
						<a href="{{ route('parent.edit',$parent->id) }}" class="btn btn-info btn-sm">Edit</a>
						<a href="{{ route('parent.delete',$parent->id) }}" class="btn btn-danger btn-sm" id="delete">Delete</a>
					</div>
				</td>
			</tr>
			@endforeach
						</tbody>
					  </table>
					</div>
				</div>
			  </div>
			</div>
		  </div>
		</section>
	  </div>
  </div>

<script type="text/javascript">
	function copyParentLink(inputId) {
		var input = document.getElementById(inputId);
		if (!input) return;
		input.select();
		input.setSelectionRange(0, 99999);
		document.execCommand('copy');
	}
</script>

@endsection
