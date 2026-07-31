@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Registrations for: <strong>{{ $event->title }}</strong></h3>
                            <a href="{{ route('event.view') }}" style="float: right;" class="btn btn-rounded btn-info mb-5"> Back to Events</a>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">SL</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone Number</th>
                                            <th>Date Registered</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($registrations as $key => $reg)
                                        <tr>
                                            <td>{{ $key+1 }}</td>
                                            <td>{{ $reg->name }}</td>
                                            <td>{{ $reg->email }}</td>
                                            <td>{{ $reg->phone }}</td>
                                            <td>{{ $reg->created_at->format('d M Y, h:i A') }}</td>
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

@endsection
