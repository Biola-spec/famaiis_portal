<?php

namespace App\Livewire\Chat;

use Livewire\Component;
use App\Models\User;
use App\Models\ChatGroup;

class Chat extends Component
{
    public $selectedConversation;
    public $type; // 'private' or 'group'
    public $query;

    protected $listeners = ['chatSelected', 'refresh' => '$refresh'];

    public function chatSelected($conversationId, $type)
    {
        $this->selectedConversation = $conversationId;
        $this->type = $type;
    }

    public function render()
    {
        return view('livewire.chat.chat')->extends('admin.admin_master')->section('admin');
    }
}
