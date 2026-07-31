@extends('admin.admin_master')
@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-xl-8 col-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h4 class="box-title">Parent Dashboard</h4>
                        </div>
                        <div class="box-body">
                            <div class="mb-3">
                                <a href="{{ route('parent.dashboard') }}" class="btn btn-sm {{ $activeTab === 'dashboard' ? 'btn-primary' : 'btn-outline-primary' }}">My Children</a>
                                <a href="{{ route('parent.results') }}" class="btn btn-sm {{ $activeTab === 'results' ? 'btn-primary' : 'btn-outline-primary' }}">Results</a>
                                <a href="{{ route('parent.fees') }}" class="btn btn-sm {{ $activeTab === 'fees' ? 'btn-primary' : 'btn-outline-primary' }}">Fees</a>
                                <a href="{{ route('parent.shop') }}" class="btn btn-sm {{ $activeTab === 'shop' ? 'btn-primary' : 'btn-outline-primary' }}">School Shop</a>
                            </div>

                            <form method="GET" action="{{ $activeTab === 'results' ? route('parent.results') : ($activeTab === 'fees' ? route('parent.fees') : ($activeTab === 'shop' ? route('parent.shop') : route('parent.dashboard'))) }}">
                                <div class="row">
                                    <div class="col-md-3">
                                        <h5>Child</h5>
                                        <select name="child_id" class="form-control">
                                            @foreach($children as $child)
                                                <option value="{{ $child->id }}" {{ optional($selectedChild)->id == $child->id ? 'selected' : '' }}>
                                                    {{ $child->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <h5>Session</h5>
                                        <select name="session_id" class="form-control">
                                            @foreach($sessions as $session)
                                                <option value="{{ $session->id }}" {{ ($filters['session_id'] ?? optional($currentSession)->id) == $session->id ? 'selected' : '' }}>
                                                    {{ $session->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <h5>Term</h5>
                                        <select name="term" class="form-control">
                                            <option value="">Select Term</option>
                                            <option value="1st Term" {{ ($filters['term'] ?? '') == '1st Term' ? 'selected' : '' }}>1st Term</option>
                                            <option value="2nd Term" {{ ($filters['term'] ?? '') == '2nd Term' ? 'selected' : '' }}>2nd Term</option>
                                            <option value="3rd Term" {{ ($filters['term'] ?? '') == '3rd Term' ? 'selected' : '' }}>3rd Term</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <h5>Subject</h5>
                                        <select name="subject_id" class="form-control">
                                            <option value="">All Subjects</option>
                                            @foreach($subjects as $subject)
                                                <option value="{{ $subject->id }}" {{ ($filters['subject_id'] ?? null) == $subject->id ? 'selected' : '' }}>
                                                    {{ $subject->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-1" style="padding-top: 25px;">
                                        <button class="btn btn-primary" type="submit">Load</button>
                                    </div>
                                </div>
                            </form>

                            @if(in_array($activeTab, ['dashboard', 'fees']))
                                <hr>
                                <h5>Fee Summary</h5>
                                <p>Total Fees: <strong>{{ number_format($feeSummary['total_fees'], 2) }}</strong></p>
                                <p>Paid Amount: <strong>{{ number_format($feeSummary['paid'], 2) }}</strong></p>
                                <p>Outstanding Balance: <strong>{{ number_format($feeSummary['balance'], 2) }}</strong></p>

                                <h6 class="mt-3">Payment History</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Fee Type</th>
                                                <th>Paid Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($paymentHistory as $payment)
                                                <tr>
                                                    <td>{{ $payment->date ?? optional($payment->created_at)->format('Y-m-d') }}</td>
                                                    <td>{{ optional($payment->fee_category)->name ?? 'N/A' }}</td>
                                                    <td>{{ number_format((float) $payment->amount, 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3">No payments found for selected child/session.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            @if(in_array($activeTab, ['dashboard', 'results']))
                                <hr>
                                <h5>Results</h5>
                                @if($results->count() > 0 && !empty($filters['term']))
                                    <div class="mb-3">
                                        <a href="{{ route('report.marksheet.get', [
                                            'year_id' => $filters['session_id'] ?? optional($currentSession)->id,
                                            'class_id' => $results->first()->class_id,
                                            'section_id' => $results->first()->section_id,
                                            'term' => $filters['term'],
                                            'id_no' => $selectedChild->id_no
                                        ]) }}" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fa fa-download"></i> View Full Report Card ({{ $filters['term'] }})
                                        </a>
                                    </div>
                                @elseif($results->count() > 0)
                                    <div class="alert alert-info py-1">
                                        <small>Select a <strong>Term</strong> above to enable full report card download.</small>
                                    </div>
                                @endif
                                <div class="table-responsive">
                                    <table class="table table-bordered">
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
                                                <tr><td colspan="7">No result found for selected child/session.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            @if($activeTab === 'shop')
                                <hr>
                                <h5>School Shop</h5>
                                <p>Browse available products, add to cart, and place orders.</p>
                                <a href="{{ route('shop.index') }}" class="btn btn-primary btn-sm">View Products</a>
                                <a href="{{ route('shop.cart') }}" class="btn btn-info btn-sm">Open Cart</a>
                                <a href="{{ route('orders.index') }}" class="btn btn-success btn-sm">Order History</a>
                            @endif
                        </div>
                    </div>
                </div>
                @include('admin.body.events_widget')
            </div>
        </section>
    </div>
</div>


@endsection
