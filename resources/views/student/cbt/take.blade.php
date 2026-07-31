@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            @livewire('student.take-quiz', ['quiz' => $quiz])
        </section>
    </div>
</div>
@endsection
