@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Import Questions for Quiz: {{ $quiz->title }}</h3>
                            <a href="{{ route('academic.cbt.show', $quiz->id) }}" class="btn btn-secondary float-right">Back to Quiz</a>
                        </div>
                        <div class="box-body">
                            <form method="post" action="{{ route('academic.cbt.processImport', $quiz->id) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Select Word File (.docx) <span class="text-danger">*</span></label>
                                            <input type="file" name="word_file" class="form-control" required accept=".docx">
                                            <small class="form-text text-muted">Maximum file size: 10MB</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <h4>Format Instructions:</h4>
                                            <ul>
                                                <li>The document should be a standard <strong>.docx</strong> file.</li>
                                                <li>Each question should start with a number followed by a dot (e.g., <strong>1.</strong>).</li>
                                                <li>Each option should start with a letter followed by a dot (e.g., <strong>A.</strong>, <strong>B.</strong>, <strong>C.</strong>, <strong>D.</strong>, <strong>E.</strong>).</li>
                                                <li>The <strong>Correct Answer</strong> should be <strong>BOLD</strong> or have a <strong>different font color</strong> than the rest of the options.</li>
                                                <li>Mathematics and shapes should be embedded directly into the document.</li>
                                            </ul>
                                            <p><strong>Example:</strong></p>
                                            <p>
                                                1. What is the capital of France?<br>
                                                <strong>A. Paris</strong><br>
                                                B. London<br>
                                                C. Berlin<br>
                                                D. Madrid
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-xs-right mt-3">
                                    <button type="submit" class="btn btn-info btn-rounded">Start Import</button>
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
