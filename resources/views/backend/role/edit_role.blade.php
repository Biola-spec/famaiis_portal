@extends('admin.admin_master')
@section('admin')

 <div class="content-wrapper">
	  <div class="container-full">
		<section class="content">
		  <div class="box">
			<div class="box-header with-border">
			  <h4 class="box-title">Edit Role</h4>
			</div>
			<div class="box-body">
			  <div class="row">
				<div class="col">
	 <form method="post" action="{{ route('role.update', $editData->id) }}">
	 	@csrf
					  <div class="row">
						<div class="col-12">	
<div class="row">
	<div class="col-md-6">
		<div class="form-group">
			<h5>Role Name <span class="text-danger">*</span></h5>
			<div class="controls">
				<input type="text" name="name" class="form-control" value="{{ $editData->name }}" required="">
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<h5>Description</h5>
			<div class="controls">
				<input type="text" name="description" class="form-control" value="{{ $editData->description }}">
			</div>
		</div>
	</div>
</div>

<div class="row">
    <div class="col-md-12">
        <h5 class="mb-15">Assign Permissions</h5>
        
        @foreach($permissions as $module => $modulePermissions)
        @php
            $allModuleChecked = $modulePermissions->every(fn($p) => $editData->permissions->contains($p->id));
            $someModuleChecked = $modulePermissions->some(fn($p) => $editData->permissions->contains($p->id));
        @endphp
        <div class="card border mb-15">
            <div class="card-header bg-light py-2">
                <div class="checkbox">
                    <input type="checkbox" id="module_{{ Str::slug($module) }}" class="module-checkbox" 
                    {{ $allModuleChecked ? 'checked' : '' }}>
                    <label for="module_{{ Str::slug($module) }}" class="font-weight-bold text-primary">{{ $module }}</label>
                </div>
            </div>
            <div class="card-body py-3">
                <div class="row">
                    @foreach($modulePermissions as $permission)
                    <div class="col-md-3">
                        <div class="checkbox">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="perm_{{ $permission->id }}" class="permission-checkbox" data-module="module_{{ Str::slug($module) }}"
                            {{ $editData->permissions->contains($permission->id) ? 'checked' : '' }}>
                            <label for="perm_{{ $permission->id }}">{{ $permission->name }}</label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initial indeterminate state
        document.querySelectorAll('.module-checkbox').forEach(function(moduleBox) {
            const moduleId = moduleBox.id;
            const siblings = document.querySelectorAll(`.permission-checkbox[data-module="${moduleId}"]`);
            const allChecked = Array.from(siblings).every(s => s.checked);
            const someChecked = Array.from(siblings).some(s => s.checked);
            if (someChecked && !allChecked) {
                moduleBox.indeterminate = true;
            }
        });

        // Module "Check All" functionality
        document.querySelectorAll('.module-checkbox').forEach(function(moduleBox) {
            moduleBox.addEventListener('change', function() {
                const moduleId = this.id;
                document.querySelectorAll(`.permission-checkbox[data-module="${moduleId}"]`).forEach(function(permBox) {
                    permBox.checked = moduleBox.checked;
                });
            });
        });

        // Update module checkbox state when individual permissions change
        document.querySelectorAll('.permission-checkbox').forEach(function(permBox) {
            permBox.addEventListener('change', function() {
                const moduleId = this.getAttribute('data-module');
                const moduleBox = document.getElementById(moduleId);
                const siblings = document.querySelectorAll(`.permission-checkbox[data-module="${moduleId}"]`);
                const allChecked = Array.from(siblings).every(s => s.checked);
                const someChecked = Array.from(siblings).some(s => s.checked);
                
                moduleBox.checked = allChecked;
                moduleBox.indeterminate = someChecked && !allChecked;
            });
        });
    });
</script>

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

@endsection
