@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Global Academic Settings</h4>
                        </div>
                        <div class="box-body">
                            <form method="POST" action="{{ route('academic.settings.update') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h5>Active Session <span class="text-danger">*</span></h5>
                                            <select name="current_session_id" id="current_session_id" class="form-control" required>
                                                <option value="">Select Session</option>
                                                @foreach($sessions as $session)
                                                    <option value="{{ $session->id }}" {{ optional($setting)->current_session_id == $session->id ? 'selected' : '' }}>
                                                        {{ $session->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="padding-top: 25px;">
                                        <button type="submit" class="btn btn-primary btn-block">Save Active Session</button>
                                    </div>
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
