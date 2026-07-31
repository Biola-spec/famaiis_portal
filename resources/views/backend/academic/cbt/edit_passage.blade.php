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
                            <h3 class="box-title">Edit Passage - {{ $quiz->title }}</h3>
                            <a href="{{ route('academic.cbt.show', $quiz->id) }}" class="btn btn-sm btn-secondary pull-right">Back to Quiz</a>
                        </div>
                        <div class="box-body">
                            <form id="edit-passage-form" method="post" action="{{ route('academic.cbt.updatePassage', $passage->id) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Passage Content</label>
                                            <textarea name="content" id="passage-editor" class="form-control" rows="8">{{ $passage->content }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>Start Q#</label>
                                                    <input type="number" name="start_number" class="form-control" required value="{{ $passage->start_number }}">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>End Q#</label>
                                                    <input type="number" name="end_number" class="form-control" required value="{{ $passage->end_number }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Passage Image (Optional)</label>
                                            <input type="file" name="image" class="form-control" onchange="showPreview(this, 'passage-preview')">
                                            <div class="mt-2 text-center">
                                                <img id="passage-preview" src="{{ $passage->image ? asset('upload/questions/'.$passage->image) : '#' }}" 
                                                     alt="Preview" class="img-fluid rounded shadow-sm" 
                                                     style="{{ $passage->image ? '' : 'display:none;' }} max-height: 200px;">
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-info btn-block btn-lg mt-4">Update Passage</button>
                                    </div>
                                </div>
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
        tinymce.init({
            license_key: 'gpl',
            selector: '#passage-editor',
            relative_urls: false,
            remove_script_host: false,
            convert_urls: true,
            plugins: [
                'advlist autolink lists link image charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'insertdatetime media table paste code help wordcount',
                'autoresize'
            ],
            toolbar: 'undo redo | formatselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink | image media | code | fullscreen | help',
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
            height: 400,
            menubar: false,
            statusbar: true,
            branding: false,
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; }'
        });

        document.getElementById('edit-passage-form').addEventListener('submit', function() {
            tinymce.triggerSave();
        });
    } else {
        setTimeout(initTinyMCE, 100);
    }
}
initTinyMCE();
</script>
@endpush
