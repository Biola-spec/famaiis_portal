<div class="wa-chat-container d-flex h-100" style="position: relative;" wire:poll.5000ms>
    @if($selectedConversation)
        <div class="wa-chat-main flex-grow-1 d-flex flex-column {{ $showGroupInfo ? 'wa-info-open' : '' }}">
            <!-- Chat Header -->
            <div class="wa-chat-header d-flex justify-content-between align-items-center px-20 py-10" style="background: #ededed; border-left: 1px solid rgba(0,0,0,0.05);">
                <div class="d-flex align-items-center">
                    <img src="{{ $type == 'private' ? ((!empty($receiver->image ?? '')) ? url('upload/user_images/'.$receiver->image) : url('upload/no_image.jpg')) : (($receiver->image ?? '') ? url('storage/'.$receiver->image) : url('upload/no_image.jpg')) }}" class="rounded-circle mr-15" style="width: 40px; height: 40px; object-fit: cover; cursor: pointer;" wire:click="toggleGroupInfo">
                    <div style="cursor: pointer;" wire:click="toggleGroupInfo">
                        <h6 class="mb-0 font-weight-600" style="color: #111b21;">{{ $receiver->name ?? 'User' }}</h6>
                        <small style="color: #667781;">{{ $type == 'private' ? 'click here for contact info' : 'click here for group info' }}</small>
                    </div>
                </div>
                <div class="d-flex gap-15">
                    <button class="btn btn-sm text-muted p-0"><i class="fa fa-search font-size-18"></i></button>
                    @if($type == 'group')
                        <button class="btn btn-sm text-muted p-0" wire:click="toggleGroupInfo"><i class="fa fa-ellipsis-v font-size-18"></i></button>
                    @endif
                </div>
            </div>

            <!-- Chat Messages Area -->
            <div class="wa-chat-body flex-grow-1 p-20 position-relative" id="chat-messages" style="overflow-y: auto; background-color: #f4f6f9; min-height: 0;">
                <div class="d-flex flex-column justify-content-end min-h-100">
                    @foreach($messages as $msg)
                        <div class="wa-message-wrapper {{ $msg->sender_id == Auth::id() ? 'sent' : 'received' }}" wire:key="msg-{{ $msg->id }}">
                            <div class="wa-message-bubble position-relative group">
                                @if($msg->group_id && $msg->sender_id != Auth::id())
                                    <div class="wa-message-sender" style="color: #1f7aec; font-size: 12px; font-weight: 500; margin-bottom: 2px;">{{ $msg->sender->name }}</div>
                                @endif
                                
                                @if($msg->message)
                                    @if($editingMessageId == $msg->id)
                                        <div class="d-flex flex-column gap-10 mt-5">
                                            <input type="text" wire:model="editingMessageText" class="form-control form-control-sm" style="min-width: 200px;">
                                            <div class="d-flex justify-content-end gap-5">
                                                <button class="btn btn-xs btn-light" wire:click="cancelEdit">Cancel</button>
                                                <button class="btn btn-xs btn-success" wire:click="saveMessageEdit">Save</button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="wa-message-text" style="color: #111b21; font-size: 14.2px; line-height: 19px;">{{ $msg->message }}</div>
                                    @endif
                                @endif

                                @if($msg->file_path)
                                    <div class="wa-message-media mt-5 position-relative">
                                        @if($msg->file_type == 'image')
                                            <a href="{{ url('storage/'.$msg->file_path) }}" target="_blank">
                                                <img src="{{ url('storage/'.$msg->file_path) }}" class="img-fluid rounded" style="max-height: 250px; border-radius: 6px;">
                                            </a>
                                            <button type="button" wire:click="downloadFile({{ $msg->id }})" class="btn btn-xs mt-5 d-block text-center" style="background: rgba(0,0,0,0.05); width: 100%; border-radius: 4px; color: #111b21; border: none;">
                                                <i class="fa fa-download mr-5"></i> Download
                                            </button>
                                        @elseif($msg->file_type == 'video')
                                            <video controls class="img-fluid rounded" style="max-height: 250px; border-radius: 6px;">
                                                <source src="{{ url('storage/'.$msg->file_path) }}">
                                            </video>
                                            <button type="button" wire:click="downloadFile({{ $msg->id }})" class="btn btn-xs mt-5 d-block text-center" style="background: rgba(0,0,0,0.05); width: 100%; border-radius: 4px; color: #111b21; border: none;">
                                                <i class="fa fa-download mr-5"></i> Download
                                            </button>
                                        @else
                                            <button type="button" wire:click="downloadFile({{ $msg->id }})" class="btn btn-sm d-flex align-items-center p-10" style="background: rgba(0,0,0,0.05); border-radius: 6px; border: none; text-align: left; width: 100%;">
                                                <i class="fa fa-file-text-o font-size-24 mr-10 text-muted"></i>
                                                <span class="font-size-14 text-truncate" style="max-width: 200px;">Download Attachment</span>
                                            </button>
                                        @endif
                                    </div>
                                @endif

                                <div class="wa-message-meta d-flex justify-content-end align-items-center mt-1" style="color: #667781; font-size: 11px;">
                                    @if($msg->is_edited)
                                        <span class="mr-5 font-italic">(edited)</span>
                                    @endif
                                    <span class="mr-5">{{ $msg->created_at->format('H:i') }}</span>
                                    @if($msg->sender_id == Auth::id())
                                        <i class="fa {{ $msg->seen_at ? 'fa-check text-info' : 'fa-check' }}"></i>
                                        @if($msg->seen_at)<i class="fa fa-check text-info" style="margin-left: -5px;"></i>@endif
                                        
                                        <!-- Edit/Delete Dropdown -->
                                        @if($editingMessageId != $msg->id)
                                            <div class="dropdown ml-5">
                                                <i class="fa fa-angle-down cursor-pointer text-muted" data-toggle="dropdown" style="opacity: 0.5;"></i>
                                                <div class="dropdown-menu dropdown-menu-right" style="min-width: 100px; padding: 0;">
                                                    @if($msg->file_path == null)
                                                        <button class="dropdown-item py-10 font-size-12" style="border: none; background: transparent; width: 100%; text-align: left;" wire:click="startEditMessage({{ $msg->id }})">Edit Message</button>
                                                    @endif
                                                    <button class="dropdown-item py-10 font-size-12 text-danger" style="border: none; background: transparent; width: 100%; text-align: left;" wire:click="deleteMessage({{ $msg->id }})">Delete</button>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Chat Footer (Input) -->
            <div class="wa-chat-footer px-20 py-10" style="background: #f0f2f5;">
                @if($attachment)
                    <div class="wa-attachment-preview p-10 mb-10 rounded bg-white shadow-sm d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-file-o text-muted mr-10 font-size-20"></i>
                            <span class="font-size-14 text-truncate" style="max-width: 200px;">{{ $attachment->getClientOriginalName() }}</span>
                        </div>
                        <button type="button" wire:click="$set('attachment', null)" class="btn btn-sm btn-link text-danger p-0"><i class="fa fa-times"></i></button>
                    </div>
                @endif
                <form wire:submit.prevent="sendMessage" class="d-flex align-items-center gap-15">
                    <button type="button" class="btn btn-sm text-muted p-0" title="Emoji"><i class="fa fa-smile-o font-size-24"></i></button>
                    <label class="btn btn-sm text-muted p-0 mb-0 cursor-pointer" title="Attach">
                        <i class="fa fa-paperclip font-size-22" style="transform: rotate(-45deg);"></i>
                        <input type="file" wire:model="attachment" style="display: none;">
                    </label>
                    <div class="flex-grow-1 position-relative">
                        <input type="text" wire:model="message" class="form-control px-20 py-10" placeholder="Type a message" style="border-radius: 8px; border: none; background: #fff; box-shadow: 0 1px 1px rgba(0,0,0,0.05); font-size: 15px;">
                    </div>
                    @if($message || $attachment)
                        <button type="submit" class="btn btn-sm text-muted p-0"><i class="fa fa-send font-size-20" style="color: #00a884;"></i></button>
                    @else
                        <button type="button" class="btn btn-sm text-muted p-0"><i class="fa fa-microphone font-size-22"></i></button>
                    @endif
                </form>
            </div>
        </div>

        <!-- Info Sidebar (Right) -->
        @if($showGroupInfo)
            <div class="wa-info-sidebar d-flex flex-column h-100" style="width: 300px; background: #f0f2f5; border-left: 1px solid rgba(0,0,0,0.1); position: absolute; right: 0; top: 0; bottom: 0; z-index: 1050; box-shadow: -2px 0 10px rgba(0,0,0,0.05); animation: slideLeft 0.3s ease;">
                <div class="wa-info-header d-flex align-items-center px-20 py-15" style="background: #ededed; height: 60px;">
                    <button class="btn btn-sm p-0 text-muted mr-20" wire:click="toggleGroupInfo"><i class="fa fa-times font-size-18"></i></button>
                    <h6 class="mb-0 font-weight-600">{{ $type == 'group' ? 'Group info' : 'Contact info' }}</h6>
                </div>
                
                <div class="flex-grow-1 overflow-y-auto">
                    <!-- Profile Section -->
                    <div class="wa-info-profile text-center py-30 px-20 mb-10" style="background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                        <div class="position-relative d-inline-block mb-15">
                            <img src="{{ $receiver->image ? url('storage/'.$receiver->image) : url('upload/no_image.jpg') }}" class="rounded-circle" style="width: 200px; height: 200px; object-fit: cover;">
                            @if($type == 'group' && ($receiver->created_by == Auth::id() || Auth::user()->hasRole('Admin')))
                                <label class="wa-camera-btn position-absolute cursor-pointer d-flex align-items-center justify-content-center" style="bottom: 10px; right: 10px; width: 40px; height: 40px; background: #00a884; color: #fff; border-radius: 50%; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                                    <i class="fa fa-camera"></i>
                                    <input type="file" wire:model="groupImage" style="display: none;">
                                </label>
                            @endif
                        </div>
                        
                        @if($type == 'group' && ($receiver->created_by == Auth::id() || Auth::user()->hasRole('Admin')))
                            <div class="form-group mb-0">
                                <input type="text" wire:model="groupName" class="form-control text-center font-weight-bold" style="border: none; background: transparent; font-size: 20px; color: #111b21;">
                                @if($groupName != $receiver->name || $groupImage)
                                    <button class="btn btn-sm mt-10" style="background: #00a884; color: #fff;" wire:click="updateGroupInfo">Save Changes</button>
                                @endif
                                <button class="btn btn-sm btn-outline-danger mt-10 ml-5" wire:click="deleteGroup" onclick="confirm('Are you sure you want to delete this group? All messages will be lost.') || event.stopImmediatePropagation()">Delete Group</button>
                            </div>
                        @else
                            <h4 class="mb-5 font-weight-bold" style="color: #111b21;">{{ $receiver->name ?? 'User' }}</h4>
                            <button class="btn btn-sm btn-outline-danger mt-15" wire:click="deleteChat" onclick="confirm('Are you sure you want to delete all messages in this chat? This cannot be undone.') || event.stopImmediatePropagation()">Delete Chat History</button>
                        @endif
                        <div class="text-muted font-size-14">{{ $type == 'group' ? 'Group · '.$receiver->members->count().' participants' : $receiver->role }}</div>
                    </div>

                    <!-- Members Section -->
                    @if($type == 'group')
                        <div class="wa-info-members py-15" style="background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.08);">
                            <div class="px-20 mb-15 text-muted font-size-14">{{ $receiver->members->count() }} participants</div>
                            <div class="member-list">
                                @foreach($receiver->members as $member)
                                    <div class="d-flex align-items-center px-20 py-10 wa-member-item cursor-pointer" 
                                         wire:click="$dispatch('chatSelected', { conversationId: {{ $member->id }}, type: 'private' })" style="transition: background 0.2s;">
                                        <img src="{{ (!empty($member->image)) ? url('upload/user_images/'.$member->image) : url('upload/no_image.jpg') }}" class="rounded-circle mr-15" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div class="flex-grow-1 border-bottom pb-10" style="border-color: #f2f2f2 !important;">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="font-weight-600 font-size-15" style="color: #111b21;">{{ $member->name }} {{ $member->id == Auth::id() ? '(You)' : '' }}</div>
                                                @if($member->id == $receiver->created_by)
                                                    <span class="badge" style="background: #e7f5e8; color: #1fa855; font-size: 11px; font-weight: 500;">Group Admin</span>
                                                @endif
                                            </div>
                                            <div class="text-muted font-size-13 text-truncate">{{ $member->role ?? 'Member' }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    @else
        <!-- Empty State -->
        <div class="flex-grow-1 d-flex flex-column align-items-center justify-content-center" style="background: #f0f2f5; border-left: 1px solid rgba(0,0,0,0.05);">
            <div style="width: 150px; height: 150px; border-radius: 50%; background: #e0e0e0; display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                <i class="fa fa-comments text-white" style="font-size: 80px;"></i>
            </div>
            <h3 class="mt-20 font-weight-300" style="color: #41525d;">School Messaging System</h3>
            <p style="color: #667781; max-width: 400px; text-align: center; margin-top: 10px; font-size: 14px;">Select a conversation from the sidebar to start messaging with students, teachers, or parents.</p>
        </div>
    @endif

    <style>
        .wa-message-wrapper {
            display: flex;
            margin-bottom: 2px;
            width: 100%;
        }
        .wa-message-wrapper.sent { justify-content: flex-end; }
        .wa-message-wrapper.received { justify-content: flex-start; }
        
        .wa-message-bubble {
            max-width: 65%;
            padding: 6px 7px 8px 9px;
            border-radius: 7.5px;
            box-shadow: 0 1px 0.5px rgba(11,20,26,.13);
            position: relative;
        }

        .wa-message-wrapper.sent .wa-message-bubble { background-color: #dcf8c6; border-top-right-radius: 0; }
        .wa-message-wrapper.received .wa-message-bubble { background-color: #ffffff; border-top-left-radius: 0; }

        /* Little Tail for bubbles */
        .wa-message-wrapper.sent .wa-message-bubble::after {
            content: '';
            position: absolute;
            top: 0;
            right: -8px;
            width: 0;
            height: 0;
            border-top: 0px solid transparent;
            border-bottom: 15px solid transparent;
            border-left: 15px solid #dcf8c6;
        }
        .wa-message-wrapper.received .wa-message-bubble::after {
            content: '';
            position: absolute;
            top: 0;
            left: -8px;
            width: 0;
            height: 0;
            border-top: 0px solid transparent;
            border-bottom: 15px solid transparent;
            border-right: 15px solid #ffffff;
        }

        .wa-member-item:hover { background-color: #f5f6f6; }
        
        @keyframes slideLeft {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Dark Mode Overrides */
        .dark-skin .wa-chat-header { background: #202c33 !important; border-color: #2a3942 !important; }
        .dark-skin .wa-chat-header h6 { color: #e9edef !important; }
        .dark-skin .wa-chat-body { background-color: #1a2228 !important; opacity: 1; }
        .dark-skin .wa-message-wrapper.sent .wa-message-bubble { background-color: #005c4b !important; }
        .dark-skin .wa-message-wrapper.received .wa-message-bubble { background-color: #202c33 !important; }
        .dark-skin .wa-message-wrapper.sent .wa-message-bubble::after { border-left-color: #005c4b !important; }
        .dark-skin .wa-message-wrapper.received .wa-message-bubble::after { border-right-color: #202c33 !important; }
        .dark-skin .wa-message-text { color: #e9edef !important; }
        .dark-skin .wa-chat-footer { background: #202c33 !important; }
        .dark-skin .wa-chat-footer .form-control { background: #2a3942 !important; color: #d1d7db !important; }
        .dark-skin .wa-info-sidebar { background: #111b21 !important; border-color: #2a3942 !important; }
        .dark-skin .wa-info-header { background: #202c33 !important; color: #e9edef !important; }
        .dark-skin .wa-info-profile, .dark-skin .wa-info-members { background: #111b21 !important; }
        .dark-skin .wa-info-profile h4, .dark-skin .wa-info-profile input { color: #e9edef !important; }
        .dark-skin .wa-member-item:hover { background: #202c33 !important; }
        .dark-skin .wa-member-item .border-bottom { border-color: #222d34 !important; }
        .dark-skin .wa-member-item .font-weight-600 { color: #e9edef !important; }
        .dark-skin .flex-grow-1.d-flex.align-items-center.justify-content-center { background: #222d34 !important; }
    </style>

    <script>
        document.addEventListener('livewire:initialized', () => {
            const chatMessages = document.getElementById('chat-messages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            Livewire.on('refresh', () => {
                setTimeout(() => {
                    const chatMessages = document.getElementById('chat-messages');
                    if (chatMessages) {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                }, 100);
            });
        });
    </script>
</div>
