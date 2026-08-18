<div class="chat-list-wrapper h-100 d-flex flex-column" wire:poll.5000ms>
    <!-- Sidebar Header -->
    <div class="sidebar-header d-flex justify-content-between align-items-center p-15" style="background: #ededed; flex-shrink: 0;">
        <div class="d-flex align-items-center">
            <img src="{{ (!empty(Auth::user()->image ?? '')) ? url('upload/user_images/'.Auth::user()->image) : url('upload/no_image.jpg') }}" class="rounded-circle mr-10" style="width: 40px; height: 40px; object-fit: cover;">
            <h5 class="mb-0 font-weight-600">Chats</h5>
        </div>
        <div class="d-flex gap-15">
            <button class="btn btn-sm text-muted p-0" data-toggle="modal" data-target="#createGroupModal" title="New Group">
                <i class="fa fa-users font-size-18"></i>
            </button>
            <button class="btn btn-sm text-muted p-0" title="More Options">
                <i class="fa fa-ellipsis-v font-size-18"></i>
            </button>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="p-10" style="background: #fff; flex-shrink: 0;">
        @if(session('chat_error'))
            <div class="alert alert-warning py-5 px-10 mb-10 font-size-12">{{ session('chat_error') }}</div>
        @endif
        <div class="search-container position-relative">
            <i class="fa fa-search position-absolute text-muted" style="left: 15px; top: 10px;"></i>
            <input type="text" wire:model.live="search" placeholder="Search or start new chat" 
                   class="form-control pl-40" 
                   style="border-radius: 20px; border: none; background: #f0f2f5; font-size: 14px;">
        </div>
    </div>

    <!-- Conversations List -->
    <div class="conversations-list flex-grow-1" style="background: #fff; overflow-y: auto;">
        @if($groups->count() > 0)
            @foreach($groups as $group)
                <div class="wa-chat-item {{ ($selectedConversation == $group->id && $type == 'group') ? 'active' : '' }}" 
                     wire:click="selectChat({{ $group->id }}, 'group')" wire:key="group-{{ $group->id }}">
                    <div class="wa-avatar-container">
                        <img src="{{ $group->image ? url('storage/'.$group->image) : url('upload/no_image.jpg') }}" alt="">
                    </div>
                    <div class="wa-chat-info">
                        <div class="wa-chat-top">
                            <span class="wa-name">{{ $group->name }}</span>
                            <span class="wa-time">Group</span>
                        </div>
                        <div class="wa-chat-bottom">
                            <span class="wa-msg"><i class="fa fa-users mr-5"></i> {{ $group->members->count() }} members</span>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

        @foreach($users as $user)
            <div class="wa-chat-item {{ ($selectedConversation == $user->id && $type == 'private') ? 'active' : '' }}" 
                 wire:click="selectChat({{ $user->id }}, 'private')" wire:key="user-{{ $user->id }}">
                <div class="wa-avatar-container">
                    @php
                        $userType = strtolower($user->role ?? '');
                        $folder = match($userType) {
                            'student' => 'student_images',
                            'teacher' => 'employee_images',
                            'parent' => 'parent_images',
                            default => 'user_images',
                        };
                        $imagePath = (!empty($user->image)) ? url('upload/'.$folder.'/'.$user->image) : url('upload/no_image.jpg');
                    @endphp
                    <img src="{{ $imagePath }}" alt="">
                </div>
                <div class="wa-chat-info">
                    <div class="wa-chat-top">
                        <span class="wa-name">{{ $user->name }}</span>
                        @php
                            $lastMsg = \App\Models\Message::where(function($q) use ($user) {
                                $q->where('sender_id', Auth::id())->where('receiver_id', $user->id);
                            })->orWhere(function($q) use ($user) {
                                $q->where('sender_id', $user->id)->where('receiver_id', Auth::id());
                            })->latest()->first();
                        @endphp
                        <span class="wa-time">{{ $lastMsg ? $lastMsg->created_at->format('H:i') : '' }}</span>
                    </div>
                    <div class="wa-chat-bottom">
                        <span class="wa-msg">{{ $user->role }}</span>
                        @php
                            $unreadCount = \App\Models\Message::where('sender_id', $user->id)
                                ->where('receiver_id', Auth::id())
                                ->whereNull('seen_at')
                                ->count();
                        @endphp
                        @if($unreadCount > 0)
                            <span class="wa-badge">{{ $unreadCount }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach

        @if($users->count() == 0 && $groups->count() == 0)
            <div class="p-40 text-center text-muted">
                <i class="fa fa-comments font-size-50 mb-10" style="opacity: 0.1;"></i>
                <p>No connected conversations found.</p>
                <small>Ask an administrator to connect the users who should be able to chat.</small>
            </div>
        @endif
    </div>

    <!-- Group Modal (WhatsApp Style) -->
    <div wire:ignore.self class="modal fade" id="createGroupModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 0; border: none;">
                <div class="modal-header" style="background: #008069; color: #fff;">
                    <h5 class="modal-title">New Group</h5>
                    <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <form wire:submit.prevent="createGroup">
                    <div class="modal-body">
                        <div class="form-group mb-15">
                            <input type="text" wire:model="groupName" class="form-control" placeholder="Group Subject" style="border: none; border-bottom: 2px solid #00a884; border-radius: 0; padding-left: 0; font-size: 16px;">
                            @error('groupName') <small class="text-danger mt-5 d-block">{{ $message }}</small> @enderror
                        </div>
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-10">
                                <label class="text-muted font-size-12 font-weight-600 mb-0">SELECT MEMBERS</label>
                                @if(count($selectedUsers) > 0)
                                    <span class="badge badge-success rounded-pill" style="background: #00a884;">{{ count($selectedUsers) }} selected</span>
                                @endif
                            </div>
                            
                            <!-- Modal Search -->
                            <div class="position-relative mb-15">
                                <i class="fa fa-search position-absolute text-muted" style="left: 10px; top: 10px;"></i>
                                <input type="text" wire:model.live="userSearch" class="form-control form-control-sm pl-30" placeholder="Search members..." style="border-radius: 20px; background: #f0f2f5; border: none;">
                            </div>

                            @error('selectedUsers') <div class="alert alert-danger py-5 px-10 font-size-13 mb-10">{{ $message }}</div> @enderror
                            
                            <div class="member-selector" style="max-height: 280px; overflow-y: auto; overflow-x: hidden;">
                                @if($modalUsers->count() == 0)
                                    <div class="text-center text-muted py-20 font-size-13">No users found.</div>
                                @endif
                                @foreach($modalUsers as $u)
                                    <label class="d-flex align-items-center p-10 mb-5 rounded cursor-pointer wa-member-item" style="border: 1px solid {{ in_array($u->id, $selectedUsers) ? '#00a884' : '#f0f2f5' }}; background: {{ in_array($u->id, $selectedUsers) ? '#f2fbf7' : '#fff' }}; transition: all 0.2s;" wire:key="modal-user-{{ $u->id }}">
                                        <div class="mr-15" style="width: 20px;">
                                            <input type="checkbox" wire:model="selectedUsers" value="{{ $u->id }}" style="width: 18px; height: 18px; cursor: pointer; accent-color: #00a884;">
                                        </div>
                                        <img src="{{ (!empty($u->image)) ? url('upload/user_images/'.$u->image) : url('upload/no_image.jpg') }}" class="rounded-circle mr-15 shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <div class="font-size-15 font-weight-600" style="color: #111b21;">{{ $u->name }}</div>
                                            <div class="text-muted font-size-12">{{ $u->role ?? 'User' }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="background: #f0f2f5;">
                        <button type="submit" class="btn" style="background: #00a884; color: #fff; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                            <i class="fa fa-check"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .wa-chat-item {
            display: flex;
            padding: 12px 15px;
            cursor: pointer;
            transition: background 0.2s;
            border-bottom: 1px solid #f2f2f2;
            align-items: center;
        }
        .wa-chat-item:hover { background: #f5f6f6; }
        .wa-chat-item.active { background: #f0f2f5; }
        
        .wa-avatar-container img {
            width: 49px;
            height: 49px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
        }

        .wa-chat-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }

        .wa-chat-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2px;
        }

        .wa-name {
            font-size: 16px;
            font-weight: 500;
            color: #111b21;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .wa-time {
            font-size: 12px;
            color: #667781;
        }

        .wa-chat-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .wa-msg {
            font-size: 14px;
            color: #667781;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .wa-badge {
            background: #25d366;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            min-width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 5px;
        }

        .hover-bg-light:hover { background: #f8f9fa; }
        .cursor-pointer { cursor: pointer; }

        /* Dark Mode */
        .dark-skin .sidebar-header { background: #202c33 !important; color: #d1d7db !important; }
        .dark-skin .wa-chat-item { border-color: #222d34 !important; }
        .dark-skin .wa-chat-item:hover { background: #202c33 !important; }
        .dark-skin .wa-chat-item.active { background: #2a3942 !important; }
        .dark-skin .wa-name { color: #e9edef !important; }
        .dark-skin .wa-time, .dark-skin .wa-msg { color: #8696a0 !important; }
        .dark-skin .conversations-list, .dark-skin .p-10 { background: #111b21 !important; }
        .dark-skin .form-control { background: #2a3942 !important; color: #d1d7db !important; }
    </style>
</div>
