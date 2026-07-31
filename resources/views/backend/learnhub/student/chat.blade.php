@extends('admin.admin_master')
@section('admin')
@php $messages = session("famaiis_studyhub_chat_{$lesson->id}", []); @endphp
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">AI Tutor — {{ $lesson->title }}</h3>
                            <a href="{{ route('learnhub.lesson', $lesson->id) }}" class="btn btn-sm btn-default" style="float:right">← Back</a>
                        </div>
                        <div class="box-body" style="min-height:300px;max-height:450px;overflow-y:auto" id="chatBox">
                            @if(empty($messages))
                                <p class="text-muted">Hi! Ask me anything about this lesson and I'll help you understand it better.</p>
                            @endif
                            @foreach($messages as $msg)
                                <div class="mb-3 {{ $msg['role'] === 'user' ? 'text-right' : '' }}">
                                    <span class="badge badge-{{ $msg['role'] === 'user' ? 'primary' : 'secondary' }}">
                                        {{ $msg['role'] === 'user' ? 'You' : 'Tutor' }}
                                    </span>
                                    <div class="p-2 mt-1 rounded bg-light d-inline-block text-left" style="max-width:85%">
                                        {{ $msg['content'] }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="box-footer">
                            <form action="{{ route('learnhub.chat.send', $lesson->id) }}" method="POST" class="form-inline">
                                @csrf
                                <input type="text" name="message" class="form-control" style="width:80%" placeholder="Ask about this lesson..." required autofocus>
                                <button type="submit" class="btn btn-primary ml-2">Send</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
