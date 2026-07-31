@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Edit Question - {{ $quiz->title }}</h3>
                            <a href="{{ route('academic.cbt.show', $quiz->id) }}" class="btn btn-sm btn-secondary pull-right">Back to Quiz</a>
                        </div>
                        <div class="box-body">
                            <form id="edit-question-form" method="post" action="{{ route('academic.cbt.updateQuestion', $question->id) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>Question Text</label>
                                    <textarea name="question" id="question-editor" class="form-control" rows="5">{{ $question->question }}</textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Question Image</label>
                                            <input type="file" name="image" class="form-control" onchange="showPreview(this, 'q-image-preview')">
                                            <div class="mt-2">
                                                <img id="q-image-preview" src="{{ $question->image ? asset('upload/questions/'.$question->image) : '#' }}" 
                                                     style="{{ $question->image ? '' : 'display:none;' }} max-height: 150px; border-radius: 8px;" class="shadow-sm">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Correct Answer</label>
                                            <select name="correct_answer" class="form-control" required>
                                                @foreach(['A','B','C','D','E'] as $opt)
                                                    <option value="{{ $opt }}" {{ $question->correct_answer == $opt ? 'selected' : '' }}>Option {{ $opt }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <div class="row">
                                    @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                                    <div class="{{ $opt == 'e' ? 'col-md-12' : 'col-md-6' }} mb-4">
                                        <div class="p-3 border rounded bg-light">
                                            <label class="font-weight-bold">Option {{ strtoupper($opt) }} {{ $opt == 'e' ? '(Optional)' : '' }}</label>
                                            <textarea name="option_{{ $opt }}" id="option-{{ $opt }}-editor" class="form-control mb-2" rows="2">{{ $question->{"option_$opt"} }}</textarea>
                                            <div class="form-group mb-0">
                                                <label class="small">Image for Option {{ strtoupper($opt) }}</label>
                                                <input type="file" name="image_{{ $opt }}" class="form-control form-control-sm" onchange="showPreview(this, 'preview-{{ $opt }}')">
                                                <div class="mt-2">
                                                    <img id="preview-{{ $opt }}" src="{{ $question->{"image_$opt"} ? asset('upload/questions/'.$question->{"image_$opt"}) : '#' }}" 
                                                         style="{{ $question->{"image_$opt"} ? '' : 'display:none;' }} max-height: 100px; border-radius: 4px;" class="shadow-sm">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg px-5 mt-3">Update Question</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ URL::asset('tinymce_8.4.0/tinymce/js/tinymce/tinymce.min.js') }}"></script>
<script>
function showPreview(input, targetId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById(targetId).src = e.target.result;
            document.getElementById(targetId).style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function initTinyMCE() {
    if (typeof tinymce !== 'undefined') {
        const tinymceConfig = {
            license_key: 'gpl',
            relative_urls: false,
            remove_script_host: false,
            convert_urls: true,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount',
                'autoresize'
            ],
            toolbar1: 'undo redo | formatselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
            toolbar2: 'link unlink | image media | code | fullscreen | help',
            toolbar_mode: 'sliding',
            image_advtab: true,
            image_uploadtab: true,
            automatic_uploads: true,
            images_upload_url: '{{ route("tinymce.upload.image") }}',
            images_upload_handler: function (blobInfo, progress) {
                return new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    xhr.withCredentials = false;
                    xhr.open('POST', '{{ route("tinymce.upload.image") }}');
                    xhr.upload.onprogress = (e) => progress(e.loaded / e.total * 100);
                    xhr.onload = () => {
                        if (xhr.status < 200 || xhr.status >= 300) { reject('HTTP Error: ' + xhr.status); return; }
                        const json = JSON.parse(xhr.responseText);
                        if (!json || typeof json.location != 'string') { reject('Invalid JSON: ' + xhr.responseText); return; }
                        resolve(json.location);
                    };
                    xhr.onerror = () => reject('Image upload failed');
                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    formData.append('_token', '{{ csrf_token() }}');
                    xhr.send(formData);
                });
            },
            height: 200,
            menubar: false,
            statusbar: true,
            branding: false,
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
        };

        tinymce.init(Object.assign({}, tinymceConfig, { selector: '#question-editor', height: 350 }));
        tinymce.init(Object.assign({}, tinymceConfig, { 
            selector: '#option-a-editor, #option-b-editor, #option-c-editor, #option-d-editor, #option-e-editor', 
            height: 120,
            toolbar: tinymceConfig.toolbar1 + ' | image | link'
        }));

        document.getElementById('edit-question-form').addEventListener('submit', function() {
            tinymce.triggerSave();
        });
    } else {
        setTimeout(initTinyMCE, 100);
    }
}
initTinyMCE();
</script>
@endpush
