<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Models\User;
use App\Models\ChatGroup;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;

class ChatBox extends Component
{
    use WithFileUploads;

    public $selectedConversation;
    public $type;
    public $message;
    public $attachment;
    
    // Group Edit Props
    public $showGroupInfo = false;
    public $groupName;
    public $groupImage;
    public $groupMembers = [];
    
    // Message Edit Props
    public $editingMessageId = null;
    public $editingMessageText = '';
    
    #[On('refresh')]
    public function refresh() {}

    #[On('chatSelected')]
    public function chatSelected($conversationId, $type)
    {
        $this->selectedConversation = $conversationId;
        $this->type = $type;
        $this->reset('message', 'attachment', 'showGroupInfo', 'groupImage');
        
        if ($this->type == 'group') {
            $group = ChatGroup::find($this->selectedConversation);
            $this->groupName = $group->name;
        }

        $this->dispatch('refresh')->to(ChatList::class);
        
        // Mark as seen
        if ($this->type == 'private') {
            Message::where('sender_id', $this->selectedConversation)
                ->where('receiver_id', Auth::id())
                ->whereNull('seen_at')
                ->update(['seen_at' => now()]);
        }
    }

    public function sendMessage()
    {
        if (!$this->message && !$this->attachment) return;

        $filePath = null;
        $fileType = null;

        if ($this->attachment) {
            $this->validate([
                'attachment' => 'max:20480', // 20MB
            ]);
            $filePath = $this->attachment->store('messages', 'public');
            $extension = $this->attachment->getClientOriginalExtension();
            
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $fileType = 'image';
            } elseif (in_array($extension, ['mp4', 'avi', 'mov'])) {
                $fileType = 'video';
            } else {
                $fileType = 'document';
            }
        }

        $newMessage = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->type == 'private' ? $this->selectedConversation : null,
            'group_id' => $this->type == 'group' ? $this->selectedConversation : null,
            'message' => $this->message,
            'file_path' => $filePath,
            'file_type' => $fileType,
        ]);

        $this->reset('message', 'attachment');

        // Dispatch Event for Broadcasting
        broadcast(new MessageSent($newMessage))->toOthers();

        $this->dispatch('refresh')->to(ChatList::class);
    }

    public function toggleGroupInfo()
    {
        $this->showGroupInfo = !$this->showGroupInfo;
    }

    public function updateGroupInfo()
    {
        if ($this->type != 'group') return;
        
        $group = ChatGroup::findOrFail($this->selectedConversation);
        
        // Only creator or admin can edit
        if ($group->created_by != Auth::id() && !Auth::user()->hasRole('Admin')) {
            $this->dispatch('error', ['message' => 'Only group creator can edit info']);
            return;
        }

        $this->validate([
            'groupName' => 'required|string|max:255',
            'groupImage' => 'nullable|image|max:1024',
        ]);

        $updateData = ['name' => $this->groupName];

        if ($this->groupImage) {
            $path = $this->groupImage->store('groups', 'public');
            $updateData['image'] = $path;
        }

        $group->update($updateData);
        $this->reset('groupImage');
        $this->dispatch('refresh')->to(ChatList::class);
        $this->dispatch('success', ['message' => 'Group updated successfully']);
    }

    public function startEditMessage($id)
    {
        $msg = Message::find($id);
        if ($msg && $msg->sender_id == Auth::id() && $msg->file_path == null) {
            $this->editingMessageId = $id;
            $this->editingMessageText = $msg->message;
        }
    }

    public function cancelEdit()
    {
        $this->reset('editingMessageId', 'editingMessageText');
    }

    public function saveMessageEdit()
    {
        $msg = Message::find($this->editingMessageId);
        if ($msg && $msg->sender_id == Auth::id()) {
            $this->validate(['editingMessageText' => 'required']);
            $msg->update([
                'message' => $this->editingMessageText,
                'is_edited' => true
            ]);
            $this->cancelEdit();
        }
    }

    public function deleteMessage($id)
    {
        $msg = Message::find($id);
        if ($msg && $msg->sender_id == Auth::id()) {
            $msg->delete();
        }
    }

    public function deleteGroup()
    {
        if ($this->type != 'group') return;

        $group = ChatGroup::findOrFail($this->selectedConversation);
        
        if ($group->created_by == Auth::id() || Auth::user()->hasRole('Admin')) {
            // Delete all messages
            Message::where('group_id', $group->id)->delete();
            // Delete group members
            $group->members()->detach();
            // Delete group
            $group->delete();

            $this->reset('selectedConversation', 'type', 'showGroupInfo');
            $this->dispatch('refresh')->to(ChatList::class);
            $this->dispatch('success', ['message' => 'Group deleted successfully']);
        }
    }

    public function deleteChat()
    {
        if ($this->type == 'private') {
            Message::where(function($q) {
                $q->where('sender_id', Auth::id())
                  ->where('receiver_id', $this->selectedConversation);
            })->orWhere(function($q) {
                $q->where('sender_id', $this->selectedConversation)
                  ->where('receiver_id', Auth::id());
            })->delete();

            $this->dispatch('refresh')->to(ChatList::class);
            $this->dispatch('success', ['message' => 'Chat history deleted successfully']);
        }
    }

    public function downloadFile($id)
    {
        $msg = Message::find($id);
        if ($msg && $msg->file_path) {
            return response()->download(storage_path('app/public/' . $msg->file_path));
        }
    }

    public function render()
    {
        $messages = [];
        $receiver = null;

        if ($this->selectedConversation) {
            // Mark as seen during polling if active
            if ($this->type == 'private') {
                Message::where('sender_id', $this->selectedConversation)
                    ->where('receiver_id', Auth::id())
                    ->whereNull('seen_at')
                    ->update(['seen_at' => now()]);

                $receiver = User::find($this->selectedConversation);
                $messages = Message::where(function($query) {
                        $query->where('sender_id', Auth::id())
                              ->where('receiver_id', $this->selectedConversation);
                    })
                    ->orWhere(function($query) {
                        $query->where('sender_id', $this->selectedConversation)
                              ->where('receiver_id', Auth::id());
                    })
                    ->orderBy('created_at', 'asc')
                    ->get();
            } else {
                $receiver = ChatGroup::find($this->selectedConversation);
                $messages = Message::where('group_id', $this->selectedConversation)
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        return view('livewire.chat.chat-box', [
            'messages' => $messages,
            'receiver' => $receiver
        ]);
    }
}
