@extends('admin.admin_master')
@section('admin')
<style>
    #jitsi-container { height: calc(100vh - 180px); min-height: 500px; border-radius: 8px; overflow: hidden; }
    .live-room-header {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: #fff; padding: 16px 20px; border-radius: 8px 8px 0 0;
    }
    .live-badge { background: #ff4757; animation: pulse 1.5s infinite; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; }
    @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.6; } }
</style>

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="box mb-0">
                <div class="live-room-header d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <span class="live-badge">● LIVE</span>
                        <h4 class="mb-0 mt-2">{{ $session->title }}</h4>
                        <small>{{ $session->subject->name }}
                            @if($session->lesson) · {{ $session->lesson->title }} @endif
                            · Host: {{ $session->teacher->name ?? 'Teacher' }}
                        </small>
                    </div>
                    <div class="mt-2 mt-md-0">
                        @if(Auth::user()->id === $session->teacher_id)
                            <form action="{{ route('learnhub.live.end', [$session->subject_id, $session->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('End this live session for all participants?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-danger">End Session</button>
                            </form>
                        @endif
                        @php
                            $leaveUrl = Auth::user()->hasRole('Student')
                                ? route('learnhub.student.subject', $session->subject_id)
                                : route('learnhub.manage', ['id' => $session->subject_id, 'tab' => 'live']);
                        @endphp
                        <a href="{{ $leaveUrl }}" class="btn btn-sm btn-light">← Leave</a>
                    </div>
                </div>
                <div class="box-body p-0">
                    <div id="jitsi-container"></div>
                </div>
            </div>
            <p class="text-muted small text-center mt-2">
                Video powered by Jitsi Meet. Allow camera and microphone when prompted for live interaction with your teacher.
            </p>
        </section>
    </div>
</div>

<script src="https://meet.jit.si/external_api.js"></script>
<script>
(function() {
    const domain = 'meet.jit.si';
    const options = {
        roomName: @json($session->room_name),
        parentNode: document.querySelector('#jitsi-container'),
        userInfo: {
            displayName: @json(Auth::user()->name),
        },
        configOverwrite: {
            startWithAudioMuted: {{ Auth::user()->id === $session->teacher_id ? 'false' : 'true' }},
            startWithVideoMuted: false,
            prejoinPageEnabled: true,
        },
        interfaceConfigOverwrite: {
            SHOW_JITSI_WATERMARK: false,
            SHOW_WATERMARK_FOR_GUESTS: false,
            MOBILE_APP_PROMO: false,
        },
    };

    @if(Auth::user()->id === $session->teacher_id)
    options.configOverwrite.prejoinPageEnabled = false;
    @endif

    new JitsiMeetExternalAPI(domain, options);
})();
</script>
@endsection
