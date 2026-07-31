@if(isset($active_live_sessions) && $active_live_sessions->isNotEmpty())
<div class="col-12 mb-4">
    <div class="box live-sessions-card b-1">
        <div class="box-header with-border">
            <h4 class="box-title text-danger d-flex align-items-center">
                <span class="live-indicator mr-2"></span>
                <span>{{ __('ui.active_live_classes') }}</span>
            </h4>
        </div>
        <div class="box-body p-0">
            <div class="media-list media-list-hover media-list-divided">
                @foreach($active_live_sessions as $session)
                <div class="media align-items-center justify-content-between p-3 flex-wrap">
                    <div class="d-flex align-items-center">
                        <div class="live-icon-container mr-3">
                            <i class="fa fa-video-camera"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 font-weight-600">{{ $session->title }}</h5>
                            <p class="text-fade mb-0 font-size-13">
                                <strong>{{ $session->subject->name }}</strong>
                                @if($session->lesson) · {{ __('ui.description') }}: {{ $session->lesson->title }} @endif
                                · {{ __('ui.host') }}: {{ $session->teacher->name ?? __('ui.teacher') }}
                            </p>
                            @if($session->scheduled_at && $session->status === 'scheduled')
                            <small class="text-info font-size-11">
                                <i class="fa fa-clock-o"></i> {{ __('ui.scheduled') }}: {{ $session->scheduled_at->format('d M Y, h:i A') }}
                            </small>
                            @endif
                        </div>
                    </div>
                    <div class="mt-2 mt-md-0">
                         @if($session->status === 'live')
                        <span class="badge badge-danger mr-2 px-3 py-1 font-weight-700">{{ __('ui.live_now') }}</span>
                        <a href="{{ route('learnhub.live.join', $session->id) }}" class="btn btn-sm btn-success px-4 py-2 font-weight-600">{{ __('ui.join_video_room') }}</a>
                        @else
                        <span class="badge badge-info mr-2 px-3 py-1 font-weight-700">{{ __('ui.upcoming') }}</span>
                        @if(Auth::user()->id === $session->teacher_id)
                        <a href="{{ route('learnhub.live.join', $session->id) }}" class="btn btn-sm btn-primary px-4 py-2 font-weight-600">{{ __('ui.start_session') }}</a>
                        @else
                        <button class="btn btn-sm btn-secondary px-4 py-2 font-weight-600" disabled>{{ __('ui.waiting_to_start') }}</button>
                        @endif
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
    .live-sessions-card {
        border-color: rgba(239, 68, 68, 0.2);
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.05);
    }
    
    .live-indicator {
        width: 10px;
        height: 10px;
        background-color: #ef4444;
        border-radius: 50%;
        display: inline-block;
        box-shadow: 0 0 0 rgba(239, 68, 68, 0.4);
        animation: pulseLive 1.5s infinite;
    }

    @keyframes pulseLive {
        0% {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        }
        70% {
            box-shadow: 0 0 0 8px rgba(239, 68, 68, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
        }
    }

    .live-icon-container {
        width: 40px;
        height: 40px;
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 18px;
    }

    body.dark-skin .live-sessions-card {
        background: #1e253f !important;
        border-color: rgba(255, 255, 255, 0.12);
    }

    body.dark-skin .media-list-divided > .media {
        border-color: rgba(255, 255, 255, 0.08);
    }
</style>
@endif
