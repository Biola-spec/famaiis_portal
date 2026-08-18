<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ChatConnection;
use App\Models\User;
use Illuminate\Http\Request;

class ChatConnectionController extends Controller
{
    public function index()
    {
        return view('backend.chat.connections.index', [
            'users' => User::where('id', '!=', auth()->id())->orderBy('name')->get(),
            'connections' => ChatConnection::with(['user', 'connectedUser', 'connectedBy'])
                ->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'different:connected_user_id', 'exists:users,id'],
            'connected_user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        ChatConnection::connect((int) $data['user_id'], (int) $data['connected_user_id'], (int) auth()->id());

        return back()->with(['message' => 'Chat connection created successfully.', 'alert-type' => 'success']);
    }

    public function destroy(ChatConnection $connection)
    {
        $connection->delete();

        return back()->with(['message' => 'Chat connection removed successfully.', 'alert-type' => 'success']);
    }
}
