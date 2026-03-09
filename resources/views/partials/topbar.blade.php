<style>
    /* CAKE STYLE TOPBAR CSS */
    .hover-scale {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .hover-scale:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .hover-icon {
        transition: color 0.2s;
    }

    .hover-icon:hover {
        color: #3b82f6;
    }

    /* FULL WIDTH HEADER LAYOUT OVERRIDES */
    /* Only applies when this dashboard is active */
    :root {
        --header-height: 60px;
    }

    /* Navbar Fixed Top handled in sidebar.css now for dynamic behavior */


    /* Mobile Adjustments */
    @media (max-width: 1200px) {
        .cake-navbar {
            padding-left: 3.5rem;
            /* Space for toggle */
        }
    }
</style>

{{-- ================= HEADER (FULL WIDTH) ================= --}}
<div class="cake-navbar shadow-sm d-flex flex-wrap align-items-center justify-content-between gap-3">

    {{-- LEFT: BRAND & CONTEXT --}}
    <div class="d-flex align-items-center gap-3">
        {{-- Toggle Sidebar (Works on all screen sizes) --}}
        <button id="sidebarCollapseBtn" class="btn-toggle-sidebar" style="display: block !important;" onclick="
                if (window.innerWidth < 1200) {
                    document.getElementById('mainSidebar').classList.toggle('show');
                    console.log('📱 Mobile: Sidebar show =', document.getElementById('mainSidebar').classList.contains('show'));
                } else {
                    document.body.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('sidebar-collapsed', document.body.classList.contains('sidebar-collapsed'));
                    console.log('🖥️ Desktop: Sidebar collapsed =', document.body.classList.contains('sidebar-collapsed'));
                }
            ">
            <i class="bi bi-list"></i>
        </button>

        {{-- Logo (Visible only when Sidebar is Collapsed) --}}
        <div id="topbar-logo-container" class="d-flex align-items-center gap-2">
            <img src="{{ asset('assets/images/logo-new.png') }}" alt="Logo" class="img-fluid rounded-3"
                style="width: 32px; height: 32px;">
            <h5 class="mb-0 fw-bold d-none d-sm-block"
                style="font-family: 'Inter', sans-serif; letter-spacing: -0.5px; color: #1e293b;">
                Management Tools.
            </h5>
        </div>
    </div>

    {{-- CENTER: STATUS PILLS --}}
    <div class="d-flex align-items-center gap-3 d-none d-xl-flex flex-grow-1 justify-content-center">
        {{-- Greeting Pill --}}
        <div class="text-muted small fw-bold me-2">
            <span class="center-greeting-text">Good Morning</span>, {{ auth()->user()->name ?? 'User' }}
        </div>

        {{-- Clock Pill --}}
        <div class="bg-light rounded-pill px-3 py-1 small d-flex align-items-center gap-2 fw-medium border">
            <i class="bi bi-clock"></i>
            <span class="center-clock-time">{{ date('H:i') }}</span>
            <span class="text-muted mx-1">|</span>
            <span class="center-clock-date">{{ date('d M Y') }}</span>
        </div>
    </div>

    {{-- RIGHT: ACTIONS --}}
    <div class="d-flex align-items-center gap-3">

        {{-- Use Flex for Icons to Ensure Perfect Center --}}
        <div class="d-flex align-items-center gap-4">
            {{-- Notification Bell --}}
            {{-- Notification Bell --}}
            <div class="dropdown d-flex align-items-center justify-content-center">
                <div class="position-relative cursor-pointer hover-icon d-flex align-items-center justify-content-center"
                    style="width: 32px; height: 32px;" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-bell" style="font-size: 1.25rem;"></i>
                    @if (isset($totalNotifications) && $totalNotifications > 0)
                        <span id="notif-badge"
                            class="position-absolute border border-white rounded-circle bg-danger d-flex align-items-center justify-content-center text-white"
                            style="top: 2px; right: 0px; width: 16px; height: 16px; font-size: 0.65rem; font-weight: bold;">
                            {{ $totalNotifications > 9 ? '9+' : $totalNotifications }}
                        </span>
                    @endif
                </div>

                {{-- Dropdown Menu --}}
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-0 overflow-hidden"
                    style="width: 320px;">
                    <li class="bg-light p-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark">Notifications</h6>
                        <div>
                            @if (isset($totalNotifications) && $totalNotifications > 0)
                                <span id="notif-count-badge" class="badge bg-primary rounded-pill me-2">{{ $totalNotifications }} New</span>
                                <button class="btn btn-sm btn-outline-secondary" onclick="clearNotifications()" style="font-size: 0.70rem;">
                                    <i class="bi bi-x-circle me-1"></i>Clear
                                </button>
                            @endif
                        </div>
                    </li>

                    <div id="notif-list-container" style="max-height: 400px; overflow-y: auto;">
                        @if (isset($totalNotifications) && $totalNotifications > 0)

                            {{-- Section: Overdue Loans --}}
                            @if(isset($notifOverdueCount) && $notifOverdueCount > 0)
                                <li class="dropdown-header text-uppercase small fw-bold text-danger mt-2">Overdue Loans</li>
                                @foreach($notifOverdueLoans as $loan)
                                    <li>
                                        <a class="dropdown-item py-2 px-3 border-bottom border-light"
                                            href="{{ route('loan.index', ['search' => $loan->loan_code]) }}">
                                            <div class="d-flex align-items-start gap-2">
                                                <div class="bg-danger-subtle text-danger rounded-circle p-1 d-flex align-items-center justify-content-center"
                                                    style="width: 32px; height: 32px; flex-shrink: 0;">
                                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                                </div>
                                                <div>
                                                    <p class="mb-0 small fw-bold text-dark">
                                                        {{ $loan->tool->name ?? 'Unknown Tool' }}
                                                    </p>
                                                    <p class="mb-0 small text-muted">Borrower:
                                                        {{ $loan->borrower->name ?? 'Unknown' }}
                                                    </p>
                                                    <p class="mb-0 x-small text-danger">Due:
                                                        {{ $loan->expected_return_date ? $loan->expected_return_date->format('d M') : '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            @endif

                            {{-- Section: Low Stock --}}
                            @if(isset($notifLowStockCount) && $notifLowStockCount > 0)
                                <li class="dropdown-header text-uppercase small fw-bold text-warning mt-2">Low Stock</li>
                                @foreach($notifLowStockTools as $tool)
                                    <li>
                                        <a class="dropdown-item py-2 px-3 border-bottom border-light"
                                            href="{{ route('tools.index', ['search' => $tool->code]) }}">
                                            <div class="d-flex align-items-start gap-2">
                                                <div class="bg-warning-subtle text-warning rounded-circle p-1 d-flex align-items-center justify-content-center"
                                                    style="width: 32px; height: 32px; flex-shrink: 0;">
                                                    <i class="bi bi-box-seam"></i>
                                                </div>
                                                <div>
                                                    <p class="mb-0 small fw-bold text-dark">{{ $tool->name }}</p>
                                                    <p class="mb-0 small text-muted">Stock: <span
                                                            class="fw-bold text-danger">{{ $tool->quantity }}</span></p>
                                                    <p class="mb-0 x-small text-muted">{{ $tool->location->name ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            @endif

                            {{-- Section: Calibration Needed --}}
                            @if(isset($notifCalibrationCount) && $notifCalibrationCount > 0)
                                <li class="dropdown-header text-uppercase small fw-bold text-info mt-2">Calibration Needed</li>
                                @foreach($notifCalibrationTools as $tool)
                                    <li>
                                        <a class="dropdown-item py-2 px-3 border-bottom border-light"
                                            href="{{ route('calibration-issuing') }}">
                                            <div class="d-flex align-items-start gap-2">
                                                <div class="bg-info-subtle text-info rounded-circle p-1 d-flex align-items-center justify-content-center"
                                                    style="width: 32px; height: 32px; flex-shrink: 0;">
                                                    <i class="bi bi-tools"></i>
                                                </div>
                                                <div>
                                                    <p class="mb-0 small fw-bold text-dark">{{ $tool->name ?? 'Unknown Tool' }}</p>
                                                    <p class="mb-0 small text-muted">ID: {{ $tool->code ?? '-' }}</p>
                                                    <p class="mb-0 x-small text-info">Calib. Date:
                                                        {{ $tool->next_calibration_date ? \Carbon\Carbon::parse($tool->next_calibration_date)->format('d M') : '-' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            @endif

                        @else
                            <li class="p-4 text-center">
                                <div class="text-muted mb-2">
                                    <i class="bi bi-bell-slash" style="font-size: 2rem;"></i>
                                </div>
                                <p class="mb-0 small text-muted">No new notifications</p>
                            </li>
                        @endif
                    </div>
                </ul>
            </div>

            {{-- User Profile --}}
            <div class="dropdown d-flex align-items-center">
                <div class="d-flex align-items-center gap-2 cursor-pointer" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <div class="bg-primary bg-gradient text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 36px; height: 36px; border: 2px solid white; outline: 1px solid #e5e7eb;">
                        <span class="fw-bold small">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</span>
                    </div>
                    <i class="bi bi-chevron-down text-muted" style="font-size: 0.75rem;"></i>
                </div>

                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2">
                    <li>
                        <h6 class="dropdown-header">Signed in as
                            <br><strong>{{ auth()->user()->name ?? 'User' }}</strong>
                        </h6>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('password.change') }}">
                            Change Password
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Logout
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
            </div>
        </div>
    </div>
    
    <script>
    function clearNotifications() {
        const ids = {
            overdue: @json(isset($notifOverdueLoans) ? $notifOverdueLoans->pluck('id') : []),
            low_stock: @json(isset($notifLowStockTools) ? $notifLowStockTools->pluck('id') : []),
            calibration: @json(isset($notifCalibrationTools) ? $notifCalibrationTools->pluck('id') : [])
        };
    
        fetch('{{ route('notifications.clear') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ type: 'all', ids: ids })
        }).then(response => {
            if(response.ok) {
                // Instantly remove DOM elements to reflect cleared status smoothly
                const notifBadge = document.getElementById('notif-badge');
                if (notifBadge) notifBadge.remove();
                
                const notifCountBadge = document.getElementById('notif-count-badge');
                if (notifCountBadge) notifCountBadge.remove();
                
                const notifContainer = document.getElementById('notif-list-container');
                if (notifContainer) {
                    notifContainer.innerHTML = `
                        <li class="p-4 text-center">
                            <div class="text-muted mb-2">
                                <i class="bi bi-bell-slash" style="font-size: 2rem;"></i>
                            </div>
                            <p class="mb-0 small text-muted">No new notifications</p>
                        </li>
                    `;
                }
            }
        });
    }
    </script>
</div>