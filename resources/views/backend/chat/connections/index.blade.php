@extends('admin.admin_master')

@section('admin')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Chat Connections</h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-5">
                <div class="box">
                    <div class="box-header with-border"><h3 class="box-title">Connect users</h3></div>
                    <form method="POST" action="{{ route('chat.connections.store') }}">
                        @csrf
                        <div class="box-body">
                            <p class="text-muted">Only connected users can find and message each other.</p>
                            <div class="form-group">
                                <label for="user_id">First user</label>
                                <select id="user_id" name="user_id" class="form-control" required>
                                    <option value="">Select a user</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role ?? 'User' }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="connected_user_id">Can message</label>
                                <select id="connected_user_id" name="connected_user_id" class="form-control" required>
                                    <option value="">Select a user</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role ?? 'User' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="box-footer"><button class="btn btn-primary"><i class="fa fa-link"></i> Connect</button></div>
                    </form>
                </div>
            </div>
            <div class="col-md-7">
                <div class="box">
                    <div class="box-header with-border"><h3 class="box-title">Active connections</h3></div>
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-hover">
                            <thead><tr><th>User</th><th>Connected user</th><th>Added by</th><th></th></tr></thead>
                            <tbody>
                            @forelse($connections as $connection)
                                <tr>
                                    <td>{{ $connection->user->name }} <small class="text-muted">({{ $connection->user->role ?? 'User' }})</small></td>
                                    <td>{{ $connection->connectedUser->name }} <small class="text-muted">({{ $connection->connectedUser->role ?? 'User' }})</small></td>
                                    <td>{{ $connection->connectedBy->name ?? 'Admin' }}</td>
                                    <td class="text-right">
                                        <form method="POST" action="{{ route('chat.connections.destroy', $connection) }}" onsubmit="return confirm('Remove this chat connection?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-xs btn-danger" title="Remove connection"><i class="fa fa-unlink"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">No chat connections have been created.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
