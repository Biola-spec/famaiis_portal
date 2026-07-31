@extends('admin.admin_master')
@section('admin')
<style>
    .report-card {
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 30px;
        border: none;
        transition: transform 0.2s;
    }
    .report-card:hover {
        transform: translateY(-5px);
    }
    .report-header {
        padding: 20px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
    }
    .teacher-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 15px;
        object-fit: cover;
    }
    .teacher-info h5 {
        margin: 0;
        font-weight: 600;
        color: #333;
    }
    .report-meta {
        font-size: 12px;
        color: #888;
    }
    .report-body {
        padding: 20px;
    }
    .report-title {
        font-size: 20px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 15px;
    }
    .report-type-badge {
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-left: 10px;
    }
    .description {
        line-height: 1.6;
        color: #555;
    }
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
        margin-top: 20px;
    }
    .media-item {
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        position: relative;
    }
    .media-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
        transition: scale 0.3s;
    }
    .media-item:hover img {
        scale: 1.1;
    }
    .video-container {
        margin-top: 20px;
        border-radius: 10px;
        overflow: hidden;
        background: #000;
    }
    .document-list {
        margin-top: 15px;
        background: #f9f9f9;
        padding: 15px;
        border-radius: 10px;
    }
    .document-item {
        display: flex;
        align-items: center;
        padding: 8px 0;
        color: #444;
        text-decoration: none;
    }
    .document-item i {
        margin-right: 10px;
        font-size: 18px;
    }
    .document-item:hover {
        color: #007bff;
    }
    .timeline-filter {
        background: #fff;
        padding: 20px;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
</style>

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <h2 class="mb-4">Child Activity Feed</h2>

                    <!-- Filters -->
                    <div class="timeline-filter">
                        <form action="{{ route('parent.report.index') }}" method="GET" class="row">
                            <div class="col-md-4">
                                <select name="report_type" class="form-control" onchange="this.form.submit()">
                                    <option value="">All Types</option>
                                    <option value="daily" {{ request('report_type') == 'daily' ? 'selected' : '' }}>Daily Reports</option>
                                    <option value="weekly" {{ request('report_type') == 'weekly' ? 'selected' : '' }}>Weekly Reports</option>
                                    <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>Monthly Reports</option>
                                    <option value="yearly" {{ request('report_type') == 'yearly' ? 'selected' : '' }}>Yearly Reports</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="date" name="date" class="form-control" value="{{ request('date') }}" onchange="this.form.submit()">
                            </div>
                            <div class="col-md-4">
                                <a href="{{ route('parent.report.index') }}" class="btn btn-secondary btn-block">Clear Filters</a>
                            </div>
                        </form>
                    </div>

                    @forelse($reports as $report)
                    <div class="card report-card" data-report-id="{{ $report->id }}">
                        <div class="report-header">
                            <img src="{{ (!empty($report->teacher->image))? url('upload/employee_images/'.$report->teacher->image):url('upload/no_image.jpg') }}" class="teacher-avatar" alt="">
                            <div class="teacher-info">
                                <h5>{{ $report->teacher->name }}</h5>
                                <div class="report-meta">
                                    <span>{{ $report->created_at->diffForHumans() }}</span> • 
                                    <span>{{ $report->studentClass->name }}</span>
                                    @if($report->subject) • <span>{{ $report->subject->name }}</span> @endif
                                </div>
                            </div>
                            <span class="report-type-badge badge {{ 
                                $report->report_type == 'daily' ? 'badge-primary' : 
                                ($report->report_type == 'weekly' ? 'badge-success' : 
                                ($report->report_type == 'monthly' ? 'badge-warning' : 'badge-danger')) 
                            }}">{{ $report->report_type }}</span>
                        </div>
                        <div class="report-body">
                            <h3 class="report-title">{{ $report->title }}</h3>
                            <div class="description">
                                {!! $report->description !!}
                            </div>

                            @if($report->video_path)
                            <div class="video-container">
                                <video width="100%" controls preload="metadata">
                                    <source src="{{ url('storage/'.$report->video_path) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                            @endif

                            @php
                                $images = $report->media->where('file_type', 'image');
                                $docs = $report->media->where('file_type', 'document');
                            @endphp

                            @if($images->count() > 0)
                            <div class="media-grid">
                                @foreach($images as $image)
                                <div class="media-item">
                                    <a href="{{ url('storage/'.$image->file_path) }}" target="_blank">
                                        <img src="{{ url('storage/'.$image->file_path) }}" alt="Activity image">
                                    </a>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            @if($docs->count() > 0)
                            <div class="document-list">
                                <h6 class="mb-2">Attached Documents:</h6>
                                @foreach($docs as $doc)
                                @php
                                    $ext = pathinfo($doc->file_path, PATHINFO_EXTENSION);
                                    $icon = 'fa-file-o';
                                    if($ext == 'pdf') $icon = 'fa-file-pdf-o text-danger';
                                    elseif(in_array($ext, ['doc', 'docx'])) $icon = 'fa-file-word-o text-primary';
                                    elseif(in_array($ext, ['xls', 'xlsx'])) $icon = 'fa-file-excel-o text-success';
                                @endphp
                                <a href="{{ url('storage/'.$doc->file_path) }}" target="_blank" class="document-item">
                                    <i class="fa {{ $icon }}"></i> 
                                    <span>Download {{ strtoupper($ext) }} Document</span>
                                </a>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <img src="{{ url('upload/no_data.png') }}" style="width: 200px; opacity: 0.5;">
                        <h4 class="mt-4 text-muted">No activities found for your children yet.</h4>
                    </div>
                    @endforelse

                    <div class="mt-4">
                        {{ $reports->links() }}
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const reportId = entry.target.dataset.reportId;
                    fetch("{{ url('activity-reports/parent/seen') }}/" + reportId, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        document.querySelectorAll('.report-card').forEach(card => {
            observer.observe(card);
        });
    });
</script>
@endsection
