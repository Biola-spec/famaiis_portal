@extends('admin.admin_master')

@section('admin')
<div class="content-wrapper">
    <section class="content">
        <div class="box">
            <div class="box-header"><h4 class="box-title">School Writing Settings</h4></div>
            <div class="box-body">
                <div class="alert {{ $geminiConfigured ? 'alert-success' : 'alert-warning' }}">
                    Gemini AI: <strong>{{ $geminiConfigured ? 'Configured' : 'Not configured' }}</strong>.
                    Set <code>GEMINI_API_KEY</code> in the application <code>.env</code> file, then clear config cache.
                </div>
                <form method="POST" action="{{ route('ai.settings.update') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 form-group"><label>School name</label><input class="form-control" name="school_name" value="{{ old('school_name', $school->school_name) }}" required></div>
                        <div class="col-md-6 form-group"><label>Motto</label><input class="form-control" name="motto" value="{{ old('motto', $school->motto) }}"></div>
                        <div class="col-md-6 form-group"><label>Address</label><input class="form-control" name="address" value="{{ old('address', $school->address) }}"></div>
                        <div class="col-md-6 form-group"><label>Report tone</label><input class="form-control" name="report_tone" value="{{ old('report_tone', $school->report_tone) }}" required></div>
                        <div class="col-md-4 form-group"><label>Primary export color</label><input class="form-control" name="primary_color" value="{{ old('primary_color', $school->primary_color) }}" pattern="#[0-9a-fA-F]{6}" required></div>
                        <div class="col-md-6 form-group"><label>School logo</label><input class="form-control" type="file" name="logo" accept="image/*">@if($school->logo_path)<small class="text-muted">Current logo: {{ $school->logo_path }}</small>@endif</div>
                    </div>
                    <button class="btn btn-primary" type="submit">Save settings</button>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
