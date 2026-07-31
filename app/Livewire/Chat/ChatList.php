<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\User;
use App\Models\ChatGroup;
use App\Models\Message;
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
        $this->dispatch('chatSelected', conversationId: $id, type: $type);
    }

    public function createGroup()
    {
        $this->validate([
            'groupName' => 'required|min:3',
            'selectedUsers' => 'required|array|min:1'
        ]);

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

        if ($this->search) {
            $users = User::where(function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('role', 'like', '%' . $this->search . '%')
                      ->orWhere('id_no', 'like', '%' . $this->search . '%');
                })
                ->where('id', '!=', $authUserId)
                ->limit(25)
                ->get();
            
            $groups = Auth::user()->chatGroups()->where('name', 'like', '%' . $this->search . '%')->get();
        } else {
            // Get all groups I am a member of
            $groups = Auth::user()->chatGroups;

            // Get users with history first
            $usersWithHistory = User::where('id', '!=', $authUserId)
                ->where(function($q) use ($authUserId) {
                    $q->whereHas('sentMessages', function($sq) use ($authUserId) {
                        $sq->where('receiver_id', $authUserId);
                    })
                    ->orWhereHas('receivedMessages', function($rq) use ($authUserId) {
                        $rq->where('sender_id', $authUserId);
                    });
                })->get();

            // If history is empty, just get some users
            if ($usersWithHistory->isEmpty()) {
                $users = User::where('id', '!=', $authUserId)->limit(30)->get();
            } else {
                // Merge with some non-history users to ensure the list is never empty
                $otherUsers = User::where('id', '!=', $authUserId)
                    ->whereNotIn('id', $usersWithHistory->pluck('id'))
                    ->limit(10)
                    ->get();
                $users = $usersWithHistory->concat($otherUsers);
            }
        }

        $modalUsersQuery = User::where('id', '!=', Auth::id());
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
