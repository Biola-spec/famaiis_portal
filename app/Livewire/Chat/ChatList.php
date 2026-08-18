<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\User;
use App\Models\ChatGroup;
use App\Models\Message;
use App\Models\ChatConnection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class ChatList extends Component
{
    public $search;
    public $groupName;
    public $selectedUsers = [];
    public $unreadCount = 0;
    public $selectedConversation;
    public $type;
    public $userSearch = '';
    
    #[On('refresh')]
    public function refresh() {}

    #[On('chatSelected')]
    public function chatSelected($conversationId, $type)
    {
        $this->selectedConversation = $conversationId;
        $this->type = $type;
    }

    public function mount()
    {
        $this->unreadCount = Message::where('receiver_id', Auth::id())->whereNull('seen_at')->count();
    }

    public function selectChat($id, $type)
    {
        if ($type === 'private' && !ChatConnection::areConnected(Auth::id(), (int) $id)) {
            session()->flash('chat_error', 'This user is not connected for chat. Contact an administrator.');
            return;
        }

        if ($type === 'group' && !Auth::user()->chatGroups()->whereKey($id)->exists()) {
            return;
        }

        $this->dispatch('chatSelected', conversationId: $id, type: $type);
    }

    public function createGroup()
    {
        $this->validate([
            'groupName' => 'required|min:3',
            'selectedUsers' => 'required|array|min:1'
        ]);

        $connectedIds = ChatConnection::connectedUserIds(Auth::id());
        if (collect($this->selectedUsers)->diff($connectedIds)->isNotEmpty()) {
            $this->addError('selectedUsers', 'You can only add users connected by an administrator.');
            return;
        }

        $group = ChatGroup::create([
            'name' => $this->groupName,
            'created_by' => Auth::id()
        ]);

        $group->members()->attach(Auth::id());
        $group->members()->attach($this->selectedUsers);

        $this->reset('groupName', 'selectedUsers');
        $this->dispatch('refresh');
        $this->dispatch('close-modal');
        
        // If it's a broadcast, maybe tag it or just treat it as a group where only admin sends?
        // For now, standard group is fine.
        
        session()->flash('message', 'Group created successfully.');
    }

    public function render()
    {
        $authUserId = Auth::id();

        // Check for new unread messages to trigger sound
        $currentUnread = Message::where('receiver_id', $authUserId)->whereNull('seen_at')->count();
        if ($currentUnread > $this->unreadCount) {
            $this->dispatch('play-notification-sound');
        }
        $this->unreadCount = $currentUnread;

        $connectedUserIds = ChatConnection::connectedUserIds($authUserId);

        if ($this->search) {
            $users = User::whereIn('id', $connectedUserIds)->where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('role', 'like', '%' . $this->search . '%')
                      ->orWhere('id_no', 'like', '%' . $this->search . '%');
                })
                ->limit(25)
                ->get();
            
            $groups = Auth::user()->chatGroups()->where('name', 'like', '%' . $this->search . '%')->get();
        } else {
            // Get all groups I am a member of
            $groups = Auth::user()->chatGroups;

            $users = User::whereIn('id', $connectedUserIds)->orderBy('name')->get();
        }

        $modalUsersQuery = User::whereIn('id', $connectedUserIds);
        if ($this->userSearch) {
            $modalUsersQuery->where(function($q) {
                $q->where('name', 'like', '%' . $this->userSearch . '%')
                  ->orWhere('role', 'like', '%' . $this->userSearch . '%');
            });
        }
        $modalUsers = $modalUsersQuery->orderBy('name')->limit(50)->get();

        return view('livewire.chat.chat-list', [
            'users' => $users,
            'groups' => $groups,
            'modalUsers' => $modalUsers
        ]);
    }
}
