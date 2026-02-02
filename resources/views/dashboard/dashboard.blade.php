@extends('layouts.app')

@section('title', 'Dashboard')

@section('styles')
    <style>
        :root {
            --primary-theme: #0f172a;
            /* Slate 900 */
            --primary-theme-dark: #1e293b;
            /* Slate 800 */
            --bg-gray: #f8fafc;
            --card-radius: 12px;
            /* More rounded */
            /* CHANGED from 20px to 12px matches reference better */
            --card-border: #e2e8f0;
        }

        body {
            background-color: var(--bg-gray);
        }

        /* ANIMATIONS */
        @keyframes fadeInUp {

            /* ... existing animations ... */
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-up {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }

        /* HEADER */
        .dashboard-header-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: -1px;
        }

        .dashboard-subtitle {
            color: #64748b;
            font-size: 1rem;
        }

        /* KPI CARDS */
        .kpi-row {
            margin-top: 1.5rem;
        }

        .kpi-card-new {
            background: white;
            border-radius: var(--card-radius);
            padding: 1.5rem;
            height: 100%;
            border: 1px solid white;
            /* clean look */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .kpi-card-new:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            /* Softer shadow */
        }

        /* Primary Theme Card */
        .kpi-card-new.primary {
            background: linear-gradient(145deg, var(--primary-theme-dark), var(--primary-theme));
            color: white;
            border: none;
        }

        .kpi-card-new.primary .kpi-label {
            color: rgba(255, 255, 255, 0.9);
        }

        .kpi-card-new.primary .kpi-value {
            color: white;
        }

        .kpi-card-new.primary .kpi-sub {
            color: rgba(255, 255, 255, 0.8);
        }

        .kpi-card-new.primary .kpi-icon-bg {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        /* Secondary White Cards */
        .kpi-label {
            font-size: 0.95rem;
            font-weight: 600;
            color: #334155;
            /* Darker Slate 700 */
            margin-bottom: 0.5rem;
        }

        .kpi-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: #0f172a;
            /* Darker Slate 900 */
            line-height: 1;
            margin-bottom: 0.75rem;
        }

        .kpi-icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f1f5f9;
            color: #64748b;
            font-size: 1.25rem;
            transition: all 0.2s;
        }

        .kpi-card-new:hover .kpi-icon-circle {
            /* Icon interaction */
            background: #1e293b;
            color: white;
        }

        .kpi-sub {
            font-size: 0.85rem;
            /* Slightly larger */
            color: #64748b;
            /* Darker Slate 500 */
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-weight: 500;
        }

        .kpi-badge {
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* CONTENT GRID */
        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .content-card {
            background: white;
            border-radius: var(--card-radius);
            padding: 1.5rem;
            border: 1px solid var(--card-border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            /* Minimal shadow */
            height: 100%;
        }

        /* LIST ITEMS (Like "Project" in Ref) */
        .action-list-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-radius: 12px;
            transition: background 0.2s;
            text-decoration: none;
            color: inherit;
            margin-bottom: 0.5rem;
        }

        .action-list-item:hover {
            background: #f8fafc;
        }

        .action-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        /* Colors for Actions */
        .bg-soft-primary {
            background: #eff6ff;
            color: #3b82f6;
        }

        /* Blue */
        .bg-soft-success {
            background: #f0fdf4;
            color: #22c55e;
        }

        /* Green */
        .bg-soft-warning {
            background: #fefce8;
            color: #eab308;
        }

        /* Yellow */
        .bg-soft-danger {
            background: #fef2f2;
            color: #ef4444;
        }

        /* Red */
        .bg-soft-purple {
            background: #faf5ff;
            color: #a855f7;
        }

        /* Purple */

        .action-title {
            font-weight: 600;
            color: #334155;
            font-size: 1rem;
        }

        .action-desc {
            font-size: 0.8rem;
            color: #94a3b8;
        }

        /* CHARTS */
        .chart-container {
            position: relative;
            height: 180px;
        }

        /* PENDING ITEMS MINI LIST */
        .pending-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .pending-item:last-child {
            border-bottom: none;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.8rem;
            color: #64748b;
        }
    </style>
@endsection

@section('full_width_header')
    @include('partials.topbar', ['title' => 'Management Tools.'])
@endsection

@section('content')

    {{-- HEADER SECTION --}}
    <div class="mb-4 p-4 rounded-4 text-white position-relative shadow-sm d-flex align-items-center"
        style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); overflow: hidden; border-radius: 20px; min-height: 120px;">

        {{-- Background Pattern Abstract --}}
        <div class="position-absolute end-0 top-50 translate-middle-y pe-4" style="opacity: 0.1;">
            <i class="bi bi-grid-fill" style="font-size: 8rem; color: white;"></i>
        </div>

        <div class="position-relative z-1 w-100 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="fw-bold display-6 mb-1" style="font-size: 2.2rem;">Dashboard</h1>
                <p class="mb-0 text-white-50">Overview of your tool inventory.</p>
            </div>
            
            {{-- Date Badge Removed as per request --}}
        </div>
    </div>

    {{-- KPI ROW --}}
    <div class="row g-4 mb-5">

        {{-- CARD 1: TOTAL ASSETS (Primary) --}}
        <div class="col-xl-3 col-md-6 animate-up" style="animation-delay: 0.2s;">
            <div class="kpi-card-new primary">
                <div class="position-relative z-index-10">
                    <div class="kpi-label">Total Inventory</div>
                    <div class="kpi-value">{{ number_format($totalAssets, 0) }}</div>
                    <div class="kpi-sub d-flex flex-column align-items-start mt-2">
                        <span class="badge bg-white text-dark border border-white fw-bold mb-1">
                            <i class="bi bi-box-seam me-1"></i> Asset
                        </span>
                        <span class="text-white opacity-75 small">Registered in system</span>
                    </div>
                </div>
                {{-- Background Icon (Moved away to prevent collision) --}}
                <div class="position-absolute top-0 end-0 opacity-10 mt-4 me-4" style="transform: rotate(-15deg);">
                    <i class="bi bi-box-seam" style="font-size: 6rem;"></i>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <div class="kpi-icon-circle kpi-icon-bg">
                        <i class="bi bi-arrow-up-right"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 2: AVAILABLE --}}
        <div class="col-xl-3 col-md-6 animate-up" style="animation-delay: 0.3s;">
            <div class="kpi-card-new">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Available</div>
                        <div class="kpi-value">{{ number_format($available, 0) }}</div>
                    </div>
                    <div class="kpi-icon-circle">
                        <i class="bi bi-check-lg"></i>
                    </div>
                </div>
                <div class="kpi-sub mt-auto">
                    <span class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Ready</span>
                    <span class="ms-2">to be loaned</span>
                </div>
            </div>
        </div>

        {{-- CARD 3: ACTIVE LOANS --}}
        <div class="col-xl-3 col-md-6 animate-up" style="animation-delay: 0.4s;">
            <div class="kpi-card-new">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label">Active Loans</div>
                        <div class="kpi-value">{{ number_format($onLoan, 0) }}</div>
                    </div>
                    <div class="kpi-icon-circle">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </div>
                </div>
                <div class="kpi-sub mt-auto">
                    <span class="text-primary fw-bold">{{ $pendingReturnsCount }}</span>
                    <span class="ms-1">loans pending return</span>
                </div>
            </div>
        </div>

        {{-- CARD 4: CRITICAL (Damage/Lost) --}}
        <div class="col-xl-3 col-md-6 animate-up" style="animation-delay: 0.5s;">
            <div class="kpi-card-new">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-label text-danger">Attention Needed</div>
                        <div class="kpi-value">{{ number_format($damageLostCount, 0) }}</div>
                    </div>
                    <div class="kpi-icon-circle text-danger bg-danger bg-opacity-10">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                </div>
                <div class="kpi-sub mt-auto">
                    <div class="d-flex gap-3">
                        <span class="text-danger"><i class="bi bi-circle-fill"
                                style="font-size: 6px; vertical-align: middle;"></i> {{ $damageCount }} Dmg</span>
                        <span class="text-dark"><i class="bi bi-circle-fill"
                                style="font-size: 6px; vertical-align: middle;"></i> {{ $lostCount }} Lost</span>
                        <span class="text-secondary opacity-75"><i class="bi bi-circle-fill"
                                style="font-size: 6px; vertical-align: middle;"></i> {{ $unrepairableCount }}
                            Unrepairable</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- GRID SECTION --}}
    <div class="row g-4">

        {{-- LEFT COL: CHARTS & STATS --}}
        <div class="col-xl-8">
            <div class="row g-4">
                {{-- PROJECT ANALYTICS (DAMAGE/LOST CHART) --}}
                <div class="col-md-7 animate-up" style="animation-delay: 0.1s;">
                    <div class="content-card">
                        <div class="section-title">
                            Inventory Health
                            <span class="badge bg-light text-muted border fw-normal">Real-time</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-center h-100 pb-3">
                            <div style="width: 130px; height: 130px; position: relative;">
                                <canvas id="damageDistChart"></canvas>
                                <!-- Center Text Overlay -->
                                <div class="position-absolute top-50 start-50 translate-middle text-center">
                                    <div class="small text-muted" style="font-size: 0.7rem;">Total</div>
                                    <div class="fw-bold fs-5">{{ $damageLostCount }}</div>
                                </div>
                            </div>

                            {{-- Custom Legend --}}
                            <div class="ms-5 d-flex flex-column gap-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="rounded-1"
                                            style="width: 12px; height: 12px; background: #ef4444;"></span>
                                        <span class="fw-bold text-dark">Damaged</span>
                                    </div>
                                    <small
                                        class="text-muted d-block ms-4">{{ number_format(($damageLostCount > 0 ? $damageCount / $damageLostCount * 100 : 0), 1) }}%
                                        of issues</small>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="rounded-1"
                                            style="width: 12px; height: 12px; background: #1e293b;"></span>
                                        <span class="fw-bold text-dark">Lost</span>
                                    </div>
                                    <small
                                        class="text-muted d-block ms-4">{{ number_format(($damageLostCount > 0 ? $lostCount / $damageLostCount * 100 : 0), 1) }}%
                                        of issues</small>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="rounded-1"
                                            style="width: 12px; height: 12px; background: #94a3b8;"></span>
                                        <span class="fw-bold text-dark">Unrepairable</span>
                                    </div>
                                    <small
                                        class="text-muted d-block ms-4">{{ number_format(($damageLostCount > 0 ? $unrepairableCount / $damageLostCount * 100 : 0), 1) }}%
                                        of issues</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PROGRESS (DONUT or CALIBRATION) --}}
                <div class="col-md-5 animate-up" style="animation-delay: 0.7s;">
                    <div class="content-card text-center d-flex flex-column justify-content-center">
                        <div class="section-title justify-content-center mb-4">Calibration Status</div>

                        <div class="mb-3">
                            <div class="display-4 fw-bold text-dark">{{ $calibrationDueCount }}</div>
                            <div class="text-muted">Tools Due</div>
                        </div>

                        <div class="progress rounded-pill mb-3" style="height: 10px;">
                            <div class="progress-bar bg-warning" role="progressbar"
                                style="width: {{ $totalAssets > 0 ? ($calibrationDueCount / $totalAssets) * 100 : 0 }}%">
                            </div>
                        </div>

                        <a href="{{ route('calibration-issuing') }}" class="btn btn-outline-dark btn-sm rounded-pill mt-2">
                            Check Items
                        </a>
                    </div>
                </div>

                {{-- RECENT ACTIVITY / TEAM COLLAB (Now: Pending Returns List) --}}
                <div class="col-12 animate-up" style="animation-delay: 0.8s;">
                    <div class="content-card">
                        <div class="section-title">
                            Loan Tools Must Be Returned
                            <a href="{{ route('loan.index') }}"
                                class="btn btn-light btn-sm text-muted border rounded-pill px-3">View All</a>
                        </div>

                        <div class="row g-3">
                            @forelse($pendingLoans as $loan)
                                <div class="col-md-4">
                                    <div class="p-3 border rounded-3 bg-light h-100">
                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            <div class="user-avatar bg-white border">
                                                {{ substr($loan->borrower->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="fw-bold text-truncate">{{ $loan->borrower->name ?? 'Unknown' }}
                                                </div>
                                                <div class="small text-muted">{{ $loan->loan_code }}</div>
                                            </div>
                                        </div>
                                        <div class="mt-3 d-flex justify-content-between align-items-end">
                                            @if(isset($loan->days_remaining))
                                                @if($loan->days_remaining > 0)
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                                        {{ $loan->days_remaining }} Days Left
                                                    </span>
                                                @elseif($loan->days_remaining == 0)
                                                    <span class="badge bg-warning text-dark border border-warning">Due Today</span>
                                                @else
                                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                                        Overdue {{ abs($loan->days_remaining) }} Days
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                                    {{ $loan->days_pending }} Days Active
                                                </span>
                                            @endif
                                            <small class="text-muted">Due:
                                                {{ $loan->expected_return_date ? \Carbon\Carbon::parse($loan->expected_return_date)->format('d M') : '-' }}</small>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center text-muted py-4">No pending returns active.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- ITEMS IN SERVICE --}}
                <div class="col-12 animate-up" style="animation-delay: 0.2s;">
                    <div class="content-card">
                        <div class="section-title">
                            Items In Service
                            <a href="{{ route('damage-lost.index') }}?type=damage"
                                class="btn btn-light btn-sm text-muted border rounded-pill px-3">View All</a>
                        </div>

                        <div class="row g-3">
                            @forelse($itemsInService as $item)
                                <div class="col-md-4">
                                    <div class="p-3 border rounded-3 bg-light h-100">
                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            <div class="user-avatar bg-white border text-info">
                                                <i class="bi bi-wrench"></i>
                                            </div>
                                            <div class="overflow-hidden">
                                                <div class="fw-bold text-truncate">{{ $item->tool->name ?? '-' }}</div>
                                                <div class="small text-muted">SN: {{ $item->item->serial_number ?? '-' }}</div>
                                            </div>
                                        </div>
                                        <div class="mt-3 d-flex justify-content-between align-items-end">
                                            <span class="badge bg-white border text-dark">
                                                {{ $item->report_code }}
                                            </span>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($item->report_date)->format('d M') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-center text-muted py-4">No items currently in service.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- RIGHT COL: QUICK ACTIONS ("Projects" style) --}}
        <div class="col-xl-4 animate-up" style="animation-delay: 0.3s;">
            <div class="content-card h-100">
                <div class="section-title">
                    Quick Actions
                </div>

                <div class="d-flex flex-column gap-2">

                    {{-- Action 1 --}}
                    <a href="{{ route('loan.index') }}" class="action-list-item">
                        <div class="action-icon bg-soft-primary">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </div>
                        <div>
                            <div class="action-title">Loan Transaction</div>
                            <div class="action-desc">Process new loans & returns</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>

                    {{-- Action 2 --}}
                    <a href="{{ route('mutation') }}" class="action-list-item">
                        <div class="action-icon bg-soft-warning">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                        <div>
                            <div class="action-title">Mutation</div>
                            <div class="action-desc">Transfer tools between locations</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>

                    {{-- Action 3 --}}
                    <a href="{{ route('damage-lost.index') }}" class="action-list-item">
                        <div class="action-icon bg-soft-danger">
                            <i class="bi bi-exclamation-octagon"></i>
                        </div>
                        <div>
                            <div class="action-title">Report Issue</div>
                            <div class="action-desc">Log damaged or lost items</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>

                    {{-- Action 4 --}}
                    <a href="{{ route('tools.index') }}" class="action-list-item">
                        <div class="action-icon bg-soft-success">
                            <i class="bi bi-tools"></i>
                        </div>
                        <div>
                            <div class="action-title">Master Tools</div>
                            <div class="action-desc">Manage inventory database</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>

                    {{-- Action 5 --}}
                    <a href="{{ route('reporting-center') }}" class="action-list-item">
                        <div class="action-icon bg-soft-purple">
                            <i class="bi bi-file-bar-graph"></i>
                        </div>
                        <div>
                            <div class="action-title">Reports</div>
                            <div class="action-desc">View analytics and export data</div>
                        </div>
                        <i class="bi bi-chevron-right ms-auto text-muted"></i>
                    </a>

                </div>

                {{-- REMINDERS SECTION (Bottom of Right Col) --}}
                <div class="mt-5">
                    <div class="section-title">Reminders</div>

                    <div class="p-3 rounded-3 bg-light border">
                        <div class="d-flex gap-3">
                            <div class="d-flex flex-column align-items-center justify-content-center bg-white border rounded p-2 text-center"
                                style="width: 50px; height: 50px;">
                                <span class="fw-bold text-dark small" style="line-height:1">{{ date('d') }}</span>
                                <span class="text-muted" style="font-size: 0.6rem;">{{ date('M') }}</span>
                            </div>
                            <div>
                                <div class="fw-bold text-dark">System Check</div>
                                <div class="small text-muted">Review pending calibrations</div>
                            </div>
                        </div>
                        <a href="{{ route('calibration-issuing') }}"
                            class="btn btn-dark w-100 mt-3 btn-sm rounded-pill">Start Review</a>
                    </div>
                </div>

            </div>
        </div>

    </div>

    {{-- SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('damageDistChart').getContext('2d');

            // Re-render chart with new styles
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Damage', 'Lost', 'Unrepairable'],
                    datasets: [{
                        data: [{{ $damageCount }}, {{ $lostCount }}, {{ $unrepairableCount }}],
                        backgroundColor: [
                            '#ef4444', // Red-500
                            '#1e293b',  // Slate-800
                            '#94a3b8'   // Slate-400 (Unrepairable)
                        ],
                        borderWidth: 0,
                        hoverOffset: 4,
                        cutout: '85%', // Thinner ring
                        borderRadius: 20
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: true }
                    }
                }
            });
        });
    </script>

@endsection