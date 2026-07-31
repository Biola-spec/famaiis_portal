@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif
            <div class="row">
                <div class="col-md-8">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Questions for: {{ $quiz->title }}</h3>
                        </div>
                        <div class="box-body">
                             <!-- Passages Section -->
                             @if($quiz->passages->count() > 0)
                             <div class="mb-5">
                                 <h4 class="text-info mb-3"><i class="fa fa-file-text-o"></i> Quiz Passages</h4>
                                 @foreach($quiz->passages as $passage)
                                 <div class="mb-4 p-4 bg-light border-left border-info position-relative" style="border-left-width: 8px !important; border-radius: 4px;">
                                     <div class="d-flex justify-content-between align-items-start">
                                         <h6 class="text-info font-weight-bold">
                                             Reading Passage (Questions {{ $passage->start_number }} - {{ $passage->end_number }})
                                         </h6>
                                         <div class="d-flex">
                                             <a href="{{ route('academic.cbt.editPassage', $passage->id) }}" class="btn btn-xs btn-primary mr-1"><i class="fa fa-edit"></i></a>
                                             <form method="post" action="{{ route('academic.cbt.deletePassage', $passage->id) }}" onsubmit="return confirm('Delete this passage?')">
                                                 @csrf
                                                 @method('DELETE')
                                                 <button type="submit" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>
                                             </form>
                                         </div>
                                     </div>
                                     <div class="mt-2">
                                         {!! $passage->content !!}
                                     </div>
                                     @if($passage->image)
                                     <div class="mt-3">
                                         <img src="{{ asset('upload/questions/' . $passage->image) }}" class="img-fluid rounded shadow-sm" style="max-height: 300px;" alt="Passage Image">
                                     </div>
                                     @endif
                                 </div>
                                 @endforeach
                                 <hr>
                             </div>
                             @endif

                             <!-- Questions List -->
                             <h4 class="mb-3">Quiz Questions</h4>
                             @foreach($quiz->questions as $key => $question)
                             @php $qNum = $key + 1; @endphp
                              <div class="mb-4 p-3 border rounded shadow-sm bg-white">
                                  <!-- Check if a passage starts here or covers this question -->
                                  @foreach($quiz->passages as $passage)
                                      @if($qNum == $passage->start_number)
                                          <div class="mb-3 p-2 bg-info text-white rounded">
                                              <small><i class="fa fa-info-circle"></i> This question starts a passage section ({{ $passage->start_number }}-{{ $passage->end_number }})</small>
                                          </div>
                                      @endif
                                  @endforeach

                                  <h5>Q{{ $qNum }}. {!! $question->question !!}</h5>
                                  @if($question->image)
                                  <div class="mb-3">
                                      <img src="{{ asset('upload/questions/' . $question->image) }}" style="max-height: 200px; border-radius: 8px;" alt="Question Image">
                                  </div>
                                  @endif
                                  
                                  <div class="row mt-3">
                                      @foreach(['a', 'b', 'c', 'd', 'e'] as $opt)
                                      @php 
                                          $optText = $question->{"option_$opt"};
                                          $optImg = $question->{"image_$opt"};
                                          if(!$optText && !$optImg && $opt == 'e') continue;
                                      @endphp
                                      <div class="col-md-6 mb-3">
                                          <div class="p-2 border rounded {{ $question->correct_answer == strtoupper($opt) ? 'bg-success-light border-success' : '' }}">
                                              <strong>{{ strtoupper($opt) }}.</strong> {!! $optText !!}
                                              @if($optImg)
                                              <div class="mt-2">
                                                  <img src="{{ asset('upload/questions/' . $optImg) }}" class="img-fluid rounded" style="max-height: 100px;">
                                              </div>
                                              @endif
                                          </div>
                                      </div>
                                      @endforeach
                                  </div>

                                 <div class="mt-2 text-right d-flex justify-content-end">
                                     <a href="{{ route('academic.cbt.editQuestion', $question->id) }}" class="btn btn-sm btn-primary mr-2">Edit</a>
                                     <form method="post" action="{{ route('academic.cbt.deleteQuestion', $question->id) }}">
                                         @csrf
                                         @method('DELETE')
                                         <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this question?')">Delete</button>
                                     </form>
                                 </div>
                             </div>
                             @endforeach
                             @if($quiz->questions->isEmpty())
                                 <p class="text-center text-muted">No questions added yet.</p>
                             @endif
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Quiz Actions</h3>
                        </div>
                        <div class="box-body">
                            <p><strong>Status:</strong> <span class="badge badge-{{ $quiz->status == 'published' ? 'success' : 'warning' }}">{{ ucfirst($quiz->status) }}</span></p>
                            <p><strong>Retake Limit:</strong> {{ $quiz->retake_limit }}</p>
                            
                            <form method="post" action="{{ route('academic.cbt.updateStatus', $quiz->id) }}">
                                @csrf
                                <input type="hidden" name="status" value="{{ $quiz->status == 'published' ? 'locked' : 'published' }}">
                                <button type="submit" class="btn btn-block btn-{{ $quiz->status == 'published' ? 'warning' : 'success' }}">
                                    {{ $quiz->status == 'published' ? 'Lock Quiz' : 'Publish Quiz' }}
                                </button>
                            </form>

                            <a href="{{ route('academic.cbt.import', $quiz->id) }}" class="btn btn-block btn-info mt-2">
                                <i class="fa fa-upload"></i> Import Questions (Word)
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <!-- Passage Management Box -->
                    <div class="box box-solid bg-info-light">
                        <div class="box-header with-border">
                            <h3 class="box-title text-info"><i class="fa fa-file-text-o"></i> Add Independent Passage</h3>
                        </div>
                        <div class="box-body">
                            <form id="add-passage-form" method="post" action="{{ route('academic.cbt.addPassage', $quiz->id) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Passage Content</label>
                                            <textarea name="content" id="passage-editor" class="form-control" rows="4"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>Start Q#</label>
                                                    <input type="number" name="start_number" class="form-control" required placeholder="e.g. 2">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <label>End Q#</label>
                                                    <input type="number" name="end_number" class="form-control" required placeholder="e.g. 5">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Passage Image (Optional)</label>
                                            <input type="file" name="image" class="form-control" onchange="showPreview(this, 'passage-preview')">
                                            <img id="passage-preview" src="#" alt="Preview" class="mt-2 img-fluid rounded shadow-sm" style="display:none; max-height: 150px;">
                                        </div>
                                        <button type="submit" class="btn btn-info btn-block mt-4">Save Passage</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Question Management Box -->
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Add New Question</h3>
                        </div>
                        <div class="box-body">
                            <form id="add-question-form" method="post" action="{{ route('academic.cbt.addQuestion', $quiz->id) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>Question Text</label>
                                    <textarea name="question" id="question-editor" class="form-control" rows="5"></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Question Image</label>
                                            <input type="file" name="image" class="form-control" onchange="showPreview(this, 'q-image-preview')">
                                            <img id="q-image-preview" src="#" alt="Preview" class="mt-2 img-fluid rounded shadow-sm" style="display:none; max-height: 150px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Correct Answer</label>
                                            <select name="correct_answer" class="form-control" required>
                                                <option value="A">Option A</option>
                                                <option value="B">Option B</option>
                                                <option value="C">Option C</option>
                                                <option value="D">Option D</option>
                                                <option value="E">Option E</option>
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
                                            <textarea name="option_{{ $opt }}" id="option-{{ $opt }}-editor" class="form-control mb-2" rows="2"></textarea>
                                            <div class="form-group mb-0">
                                                <label class="small">Image for Option {{ strtoupper($opt) }}</label>
                                                <input type="file" name="image_{{ $opt }}" class="form-control form-control-sm" onchange="showPreview(this, 'preview-{{ $opt }}')">
                                                <img id="preview-{{ $opt }}" src="#" alt="Preview" class="mt-2 img-fluid rounded shadow-sm" style="display:none; max-height: 100px;">
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg px-5 mt-3">Add Question</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
                
                <div class="col-12">
                    <div class="box mt-4">
                        <div class="box-header with-border">
                            <h3 class="box-title">Student Results Leaderboard</h3>
                        </div>
                        <div class="box-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Score</th>
                                        <th>Status</th>
                                        <th>Submitted At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($quiz->attempts as $attempt)
                                    <tr>
                                        <td>{{ $attempt->student->name }}</td>
                                        <td>{{ $attempt->score }} / {{ $quiz->questions->count() }}</td>
                                        <td>
                                            <span class="badge badge-{{ $attempt->status == 'completed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($attempt->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $attempt->submitted_at ? $attempt->submitted_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                        <td>
                                            <form method="post" action="{{ route('academic.cbt.allowRetake', $attempt->id) }}" onsubmit="return confirm('Allow this student to retake the quiz? This will delete their current record.')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning">Grant Retake</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @if($quiz->attempts->isEmpty())
                                        <tr><td colspan="5" class="text-center text-muted">No attempts yet.</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="box mt-4">
                        <div class="box-header with-border">
                            <h3 class="box-title">Retake History Log</h3>
                        </div>
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Teacher (Authorized By)</th>
                                            <th>Date & Time Granted</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($quiz->retakes as $retake)
                                        <tr>
                                            <td>{{ $retake->student->name }}</td>
                                            <td>{{ $retake->teacher->name }}</td>
                                            <td>{{ $retake->granted_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        @endforeach
                                        @if($quiz->retakes->isEmpty())
                                            <tr><td colspan="3" class="text-center text-muted">No retakes granted yet.</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('scripts')
<script>
console.log('Loading TinyMCE...');
</script>
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

console.log('TinyMCE script loaded, checking if tinymce is available...');
console.log('typeof tinymce:', typeof tinymce);

// Wait for TinyMCE to be available
function initTinyMCE() {
    console.log('initTinyMCE called, typeof tinymce:', typeof tinymce);
    if (typeof tinymce !== 'undefined') {
        console.log('TinyMCE is available, initializing...');
        
        // Check if math plugin exists
        console.log('Math plugin available:', tinymce.PluginManager.get('math') !== undefined);
        
        try {
        // TinyMCE configuration
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
                    
                    xhr.upload.onprogress = function (e) {
                        progress(e.loaded / e.total * 100);
                    };
                    
                    xhr.onload = function () {
                        if (xhr.status === 403) {
                            reject({ message: 'HTTP Error: ' + xhr.status, remove: true });
                            return;
                        }
                        if (xhr.status < 200 || xhr.status >= 300) {
                            reject('HTTP Error: ' + xhr.status);
                            return;
                        }
                        const json = JSON.parse(xhr.responseText);
                        if (!json || typeof json.location != 'string') {
                            reject('Invalid JSON: ' + xhr.responseText);
                            return;
                        }
                        resolve(json.location);
                    };
                    
                    xhr.onerror = function () {
                        reject('Image upload failed due to a XHR Transport error.');
                    };
                    
                    const formData = new FormData();
                    formData.append('file', blobInfo.blob(), blobInfo.filename());
                    formData.append('_token', '{{ csrf_token() }}');
                    
                    xhr.send(formData);
                });
            },
            file_picker_types: 'image',
            file_picker_callback: function (cb, value, meta) {
                var input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.setAttribute('accept', 'image/*');
                
                input.onchange = function () {
                    var file = this.files[0];
                    var reader = new FileReader();
                    
                    reader.onload = function () {
                        var id = 'blobid' + (new Date()).getTime();
                        var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                        var base64 = reader.result.split(',')[1];
                        var blobInfo = blobCache.create(id, file, base64);
                        blobCache.add(blobInfo);
                        cb(blobInfo.blobUri(), { title: file.name });
                    };
                    reader.readAsDataURL(file);
                };
                
                input.click();
            },
            height: 200,
            menubar: false,
            statusbar: true,
            branding: false,
            content_style: 'body { font-family: Arial, sans-serif; font-size: 14px; } .math-equation { display: inline-block; margin: 0 2px; }'
        };

        // Initialize TinyMCE for passage editor
        tinymce.init(Object.assign({}, tinymceConfig, {
            selector: '#passage-editor',
            height: 300,
            placeholder: 'Enter passage here if this question starts a new passage section...'
        }));

        // Initialize TinyMCE for question editor
        tinymce.init(Object.assign({}, tinymceConfig, {
            selector: '#question-editor',
            height: 350
        }));

        // Initialize TinyMCE for option editors
        tinymce.init(Object.assign({}, tinymceConfig, {
            selector: '#option-a-editor, #option-b-editor, #option-c-editor, #option-d-editor, #option-e-editor',
            height: 120,
            toolbar: tinymceConfig.toolbar1 + ' | image | link'
        }));

        // Handle form submission to ensure TinyMCE content is saved
        const forms = ['add-question-form', 'add-passage-form'];
        forms.forEach(formId => {
            const form = document.getElementById(formId);
            if (form) {
                form.addEventListener('submit', function(e) {
                    tinymce.triggerSave();
                });
            }
        });
        
        console.log('TinyMCE initialization completed successfully');
        } catch (error) {
            console.error('Error initializing TinyMCE:', error);
        }
    } else {
        console.log('TinyMCE not available, retrying in 100ms...');
        // If TinyMCE is not loaded, wait and try again
        setTimeout(initTinyMCE, 100);
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTinyMCE);
} else {
    initTinyMCE();
}
</script>
@endpush
