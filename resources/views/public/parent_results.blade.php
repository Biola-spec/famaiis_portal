<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $setting->school_name ?? 'School' }} — Student Results</title>
    <link rel="icon" href="{{ (!empty($setting->logo)) ? url($setting->logo) : asset('backend/images/favicon.ico') }}">
    <style>
        :root {
            --primary: #2E86DE;
            --primary-dark: #1F3E6C;
            --bg: #F5F7FA;
            --card: #ffffff;
            --text: #1E2E4A;
            --muted: #62728D;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
        }
        .wrap { max-width: 960px; margin: 0 auto; padding: 24px 16px 48px; }
        .hero {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: #fff;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(31, 62, 108, 0.2);
        }
        .hero h1 { margin: 0 0 6px; font-size: 1.35rem; }
        .hero p { margin: 0; opacity: 0.9; font-size: 0.95rem; }
        .card {
            background: var(--card);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            border: 1px solid #E4E9F0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        label { display: block; font-size: 0.8rem; font-weight: 600; color: var(--muted); margin-bottom: 6px; }
        select, button {
            font: inherit;
            border-radius: 8px;
            border: 1px solid #d0d7e2;
            padding: 10px 12px;
            width: 100%;
        }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px; align-items: end; }
        .btn {
            background: var(--primary);
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-outline {
            background: #fff;
            color: var(--primary);
            border: 1px solid var(--primary);
            text-decoration: none;
            display: inline-block;
            text-align: center;
            padding: 10px 16px;
            border-radius: 8px;
            font-weight: 600;
        }
        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        th, td { border: 1px solid #E4E9F0; padding: 10px; text-align: left; }
        th { background: #EBEFF6; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.03em; }
        .alert { padding: 12px 14px; border-radius: 8px; margin-bottom: 16px; font-size: 0.9rem; }
        .alert-info { background: #E8F2FC; color: #1F3E6C; }
        .alert-error { background: #FDEAEA; color: #9b2c2c; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; margin: 16px 0; }
        @media (max-width: 600px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="hero">
        <h1>{{ $setting->school_name ?? 'School Portal' }}</h1>
        <p>Results for <strong>{{ $selectedChild->name }}</strong> (ID: {{ $selectedChild->id_no }})</p>
    </div>

    @if(session('message'))
        <div class="alert alert-error">{{ session('message') }}</div>
    @endif

    <div class="card">
        <form method="GET" action="{{ route('parent.result.link', $link->token) }}">
            <div class="grid">
                @if($children->count() > 1)
                <div>
                    <label>Child</label>
                    <select name="student_id">
                        @foreach($children as $child)
                            <option value="{{ $child->id }}" {{ $selectedChild->id == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label>Session</label>
                    <select name="session_id">
                        @foreach($sessions as $session)
                            <option value="{{ $session->id }}" {{ ($filters['session_id'] ?? '') == $session->id ? 'selected' : '' }}>{{ $session->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Term</label>
                    <select name="term">
                        <option value="">All terms</option>
                        @foreach(['1st Term', '2nd Term', '3rd Term'] as $termOption)
                            <option value="{{ $termOption }}" {{ ($filters['term'] ?? '') == $termOption ? 'selected' : '' }}>{{ $termOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Subject</label>
                    <select name="subject_id">
                        <option value="">All subjects</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ ($filters['subject_id'] ?? '') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn">Load results</button>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <h2 style="margin-top:0;font-size:1.1rem;">Subject scores</h2>

        @if($results->count() > 0 && !empty($filters['term']))
            <div class="actions">
                <a class="btn-outline" target="_blank" rel="noopener"
                   href="{{ route('parent.result.link.report', [
                       'token' => $link->token,
                       'student_id' => $selectedChild->id,
                       'session_id' => $filters['session_id'],
                       'term' => $filters['term'],
                   ]) }}">
                    Download / print report card ({{ $filters['term'] }})
                </a>
            </div>
        @elseif($results->count() > 0)
            <div class="alert alert-info">Select a <strong>term</strong> and click Load to enable the full report card download.</div>
        @endif

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Term</th>
                        <th>CA</th>
                        <th>Exam</th>
                        <th>Total</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $result)
                        <tr>
                            <td>{{ optional($result->subject)->name }}</td>
                            <td>{{ $result->term }}</td>
                            <td>{{ $result->ca_score }}</td>
                            <td>{{ $result->exam_score }}</td>
                            <td>{{ $result->total_score }}</td>
                            <td>{{ $result->grade }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6">No results for this selection yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <p style="text-align:center;color:var(--muted);font-size:0.8rem;">Private link for {{ optional($link->parent)->name }}. Do not share publicly.</p>
</div>
</body>
</html>
