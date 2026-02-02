<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

    {{-- Preload critical assets --}}
    <link rel="preload" href="{{ asset('assets/images/logo-new.png') }}" as="image">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom Sidebar CSS -->
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}?v=1015&t={{ time() }}">

    <style>
        .blink-badge {
            animation: blinker 1.5s linear infinite;
        }

        @keyframes blinker {
            50% {
                opacity: 0.5;
            }
        }
    </style>
    <style>
        /* TOP BAR REMOVAL OVERRIDES */
        .topbar {
            display: none !important;
        }

        .content {
            margin-top: 0 !important;
            padding-top: 1rem !important;
        }

        /* Floating Sidebar Toggle */
        /* Internal Sidebar Toggle */
        .btn-toggle-internal {
            background: transparent;
            border: none;
            box-shadow: none;
            width: 40px;
            height: 40px;
            /* Border radius kept for hover effect */
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
        }

        .btn-toggle-internal:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            transform: none;
        }

        .btn-toggle-internal i {
            font-size: 1.25rem;
        }

        /* Adjust content strictly */
        .page-header-modern {
            margin-top: 1.5rem;
            padding-left: 0;
        }

        /* Move toggle when sidebar is collapsed (Desktop) */
        @media (min-width: 1201px) {
            /* If sidebar is expanded, button is hidden or moved? 
               User wants it floating. 
               Let's keep it visible always. */
        }

        /* Move button with sidebar on desktop if needed, or keep fixed */
        body.sidebar-collapsed #sidebarToggle {
            left: 20px;
            /* Stay in place or move? */
        }

        @media (max-width: 1200px) {
            #sidebarToggle {
                display: flex !important;
            }

            .page-header-modern {
                padding-left: 5rem;
            }
        }
    </style>
    @yield('styles')
</head>

<body>

    @yield('full_width_header')

    @include('partials.sidebar')



    {{-- ================= CONTENT ================= --}}
    {{-- ================= CONTENT ================= --}}
    <div class="content">
        <div style="height: 70px;"></div> {{-- Spacer to clear fixed Topbar --}}
        @yield('content')
    </div>

    {{-- ================= DELETE CONFIRMATION MODAL (PREMIUM) ================= --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header border-0 p-0 position-relative">
                    <button type="button" class="btn-close position-absolute top-0 end-0 m-3 z-1"
                        data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="w-100 bg-danger bg-gradient p-4 text-center">
                        <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center mb-0"
                            style="width: 64px; height: 64px; backdrop-filter: blur(5px);">
                            <i class="bi bi-trash3-fill text-white fs-2"></i>
                        </div>
                    </div>
                </div>
                <div class="modal-body text-center px-4 pt-4 pb-0">
                    <h5 class="fw-bold text-dark mb-2">Delete Item?</h5>
                    <p class="text-muted small mb-4">
                        This action is permanent and cannot be undone. Please confirm by entering your password.
                    </p>

                    <div class="mb-3 text-start bg-light p-3 rounded-3 border" id="passwordConfirmationContainer">
                        <label for="passwordConfirmation"
                            class="form-label small fw-bold text-uppercase text-secondary mb-1"
                            style="font-size: 0.7rem; letter-spacing: 0.5px;">Security Check</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i
                                    class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control border-start-0 border-end-0 ps-0 shadow-none z-0"
                                name="password" id="passwordConfirmation" placeholder="Enter your password" required>
                            <button class="btn btn-outline-secondary border-start-0 bg-white shadow-none" type="button"
                                id="togglePasswordBtn" style="border-color: #dee2e6;">
                                <i class="bi bi-eye-slash-fill text-secondary" id="togglePasswordIcon"></i>
                            </button>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const toggleBtn = document.getElementById('togglePasswordBtn');
                                const passInput = document.getElementById('passwordConfirmation');
                                const toggleIcon = document.getElementById('togglePasswordIcon');

                                if (toggleBtn && passInput && toggleIcon) {
                                    toggleBtn.addEventListener('click', function () {
                                        const type = passInput.getAttribute('type') === 'password' ? 'text' : 'password';
                                        passInput.setAttribute('type', type);

                                        // Toggle Icon
                                        if (type === 'text') {
                                            toggleIcon.classList.remove('bi-eye-slash-fill');
                                            toggleIcon.classList.add('bi-eye-fill');
                                            toggleIcon.classList.replace('text-secondary', 'text-primary');
                                        } else {
                                            toggleIcon.classList.remove('bi-eye-fill');
                                            toggleIcon.classList.add('bi-eye-slash-fill');
                                            toggleIcon.classList.replace('text-primary', 'text-secondary');
                                        }
                                    });
                                }
                            });
                        </script>
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center px-4 pb-4 pt-2">
                    <div class="d-grid gap-2 w-100">
                        <form id="deleteForm" method="POST" class="d-grid">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="password_confirmation_val" id="password_confirmation_val">

                            <button type="submit" class="btn btn-danger fw-bold py-2 rounded-3 shadow-sm"
                                id="confirmDeleteBtn">
                                Yes, Delete It
                            </button>
                        </form>
                        <button type="button" class="btn btn-light text-muted fw-bold py-2 rounded-3"
                            data-bs-dismiss="modal">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    {{-- ================= DATETIME SCRIPT ================= --}}
    <script>
        function updateDateTime() {
            const now = new Date();

            // Time: HH:MM (24h)
            const timeStr = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });

            // Date: Day, DD MMM YYYY
            const dateStr = now.toLocaleDateString('en-GB', {
                weekday: 'long',
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });

            // Update all clock instances
            // Update all clock instances
            document.querySelectorAll('.center-clock-time').forEach(el => el.innerText = timeStr);
            document.querySelectorAll('.center-clock-date').forEach(el => el.innerText = dateStr);

            // Update Greeting based on hour
            const hour = now.getHours();
            let greeting = 'Good Morning';
            if (hour >= 12 && hour < 17) {
                greeting = 'Good Afternoon';
            } else if (hour >= 17 && hour < 21) {
                greeting = 'Good Evening';
            } else if (hour >= 21 || hour < 5) {
                greeting = 'Good Night';
            }
            document.querySelectorAll('.center-greeting-text').forEach(el => el.innerText = greeting);
        }

        setInterval(updateDateTime, 1000);
        updateDateTime();

        // Sidebar Toggle Script
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const body = document.body;

            // Check localStorage for Desktop Collapse state
            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (isCollapsed && window.innerWidth > 1200) {
                body.classList.add('sidebar-collapsed');
            }

            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function (e) {
                    e.stopPropagation(); // Prevent immediate close

                    if (window.innerWidth > 1200) {
                        // Desktop: Toggle Collapse
                        body.classList.toggle('sidebar-collapsed');
                        const collapsed = body.classList.contains('sidebar-collapsed');
                        localStorage.setItem('sidebar-collapsed', collapsed);
                    } else {
                        // Mobile: Toggle Offcanvas
                        sidebar.classList.toggle('show');
                    }
                });

                // Close sidebar when clicking outside on small screens
                document.addEventListener('click', function (e) {
                    if (window.innerWidth <= 1200) {
                        if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target) && sidebar.classList.contains('show')) {
                            sidebar.classList.remove('show');
                        }
                    }
                });
            }
        });

        // Global Delete Modal Script
        function openDeleteModal(actionUrl, requirePassword = false) {
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = actionUrl;

            // Handle Password Visibility
            const passContainer = document.getElementById('passwordConfirmationContainer');
            const passInput = document.getElementById('passwordConfirmation');

            if (passContainer) {
                passContainer.style.display = requirePassword ? 'block' : 'none';
            }

            // Clear password
            if (passInput) {
                passInput.value = '';
                passInput.required = requirePassword; // Toggle required attribute
            }

            const hiddenInput = document.getElementById('password_confirmation_val');
            if (hiddenInput) hiddenInput.value = '';

            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        // Handle Form Submit to include password
        document.addEventListener('DOMContentLoaded', function () {
            const deleteForm = document.getElementById('deleteForm');
            if (deleteForm) {
                deleteForm.addEventListener('submit', function (e) {
                    const passInput = document.getElementById('passwordConfirmation');
                    const hiddenInput = document.getElementById('password_confirmation_val');

                    if (passInput && passInput.required && passInput.value.trim() === '') {
                        e.preventDefault();
                        alert('Please enter your password to confirm deletion.');
                        passInput.focus();
                        return;
                    }

                    if (passInput && hiddenInput) {
                        hiddenInput.value = passInput.value;
                    }
                });
            }
        });


        // ========================================
        // AGGRESSIVE SESSION CONTROL - DISABLED
        // Causing persistent logout issues - using simple server-side timeout instead
        // ========================================
        /*
        (function () {
            // Generate TRULY unique fingerprint for THIS SPECIFIC TAB/WINDOW
            if (!window.tabFingerprint) {
                window.tabFingerprint = Date.now() + '-' + Math.random().toString(36).substring(2, 15) + '-' + Math.random().toString(36).substring(2, 15);
            }

            const tabFingerprint = window.tabFingerprint;
            const SESSION_KEY = '_app_active_sessions';
            const HEARTBEAT_KEY = '_app_last_heartbeat';

            // Track this tab in localStorage
            function registerTab() {
                const sessions = JSON.parse(localStorage.getItem(SESSION_KEY) || '[]');
                if (!sessions.includes(tabFingerprint)) {
                    sessions.push(tabFingerprint);
                    localStorage.setItem(SESSION_KEY, JSON.stringify(sessions));
                }
                localStorage.setItem(HEARTBEAT_KEY, Date.now().toString());
            }

            // Remove this tab from localStorage
            function unregisterTab() {
                const sessions = JSON.parse(localStorage.getItem(SESSION_KEY) || '[]');
                const filtered = sessions.filter(s => s !== tabFingerprint);

                if (filtered.length === 0) {
                    // Last tab closing - trigger logout
                    localStorage.removeItem(SESSION_KEY);
                    localStorage.removeItem(HEARTBEAT_KEY);

                    // Send logout request
                    navigator.sendBeacon('/logout', new URLSearchParams({
                        '_token': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }));
                } else {
                    localStorage.setItem(SESSION_KEY, JSON.stringify(filtered));
                }
            }

            // Register this tab
            registerTab();

            // Heartbeat to detect if all tabs are closed
            setInterval(registerTab, 1000);

            // Cleanup on tab close
            window.addEventListener('beforeunload', unregisterTab);
            window.addEventListener('unload', unregisterTab);

            // Intercept ALL fetch requests to add fingerprint
            const originalFetch = window.fetch;
            window.fetch = function (...args) {
                const [url, options = {}] = args;
                options.headers = options.headers || {};

                if (options.headers instanceof Headers) {
                    options.headers.set('X-Tab-Fingerprint', tabFingerprint);
                } else {
                    options.headers['X-Tab-Fingerprint'] = tabFingerprint;
                }

                return originalFetch(url, options);
            };

            // Intercept ALL XMLHttpRequest
            const originalXHRSend = XMLHttpRequest.prototype.send;
            XMLHttpRequest.prototype.send = function () {
                this.setRequestHeader('X-Tab-Fingerprint', tabFingerprint);
                return originalXHRSend.apply(this, arguments);
            };

            // Add to ALL form submissions
            document.addEventListener('submit', function (e) {
                const form = e.target;
                let input = form.querySelector('input[name="tab_fingerprint"]');
                if (!input) {
                    input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'tab_fingerprint';
                    form.appendChild(input);
                }
                input.value = tabFingerprint;
            }, true);

            // Periodic fingerprint verification
            function verifyFingerprint() {
                fetch('/keep-alive', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Tab-Fingerprint': tabFingerprint,
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin'
                }).then(response => {
                    if (response.status === 401 || response.redirected) {
                        window.location.href = '/login';
                    }
                }).catch(() => { });
            }

            // Verify on page load and every 30 seconds
            verifyFingerprint();
            setInterval(verifyFingerprint, 30000);
        })();
        */
        // END DISABLED SESSION CONTROL
        // SESSION TIMEOUT HANDLER
        // ========================================
        (function () {
            const TIMEOUT_DURATION = 60 * 60 * 1000; // 1 hour in milliseconds
            const WARNING_BEFORE = 5 * 60 * 1000; // Show warning 5 minutes before timeout

            let timeoutTimer;
            let warningTimer;
            let warningShown = false;

            function resetTimers() {
                clearTimeout(timeoutTimer);
                clearTimeout(warningTimer);
                warningShown = false;

                // Hide warning modal if shown
                const warningModal = bootstrap.Modal.getInstance(document.getElementById('sessionWarningModal'));
                if (warningModal) {
                    warningModal.hide();
                }

                // Set warning timer (55 minutes)
                warningTimer = setTimeout(showWarning, TIMEOUT_DURATION - WARNING_BEFORE);

                // Set logout timer (60 minutes)
                timeoutTimer = setTimeout(autoLogout, TIMEOUT_DURATION);
            }

            function showWarning() {
                if (warningShown) return;
                warningShown = true;

                const modal = new bootstrap.Modal(document.getElementById('sessionWarningModal'));
                modal.show();

                // Start countdown
                let secondsLeft = WARNING_BEFORE / 1000;
                const countdownEl = document.getElementById('sessionCountdown');

                const countdownInterval = setInterval(() => {
                    secondsLeft--;
                    const minutes = Math.floor(secondsLeft / 60);
                    const seconds = secondsLeft % 60;
                    countdownEl.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;

                    if (secondsLeft <= 0) {
                        clearInterval(countdownInterval);
                    }
                }, 1000);
            }

            function autoLogout() {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('logout') }}";

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                if (csrfToken) {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = '_token';
                    hiddenInput.value = csrfToken;
                    form.appendChild(hiddenInput);
                }

                document.body.appendChild(form);
                form.submit();
            }

            function keepSessionAlive() {
                // Ping server to keep session alive
                fetch('/keep-alive', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json',
                    }
                }).catch(() => {
                    // Silently fail
                });
            }

            // Track user activity
            const activityEvents = ['mousedown', 'keydown', 'scroll', 'touchstart', 'click'];

            activityEvents.forEach(event => {
                document.addEventListener(event, () => {
                    resetTimers();
                    keepSessionAlive();
                }, { passive: true });
            });

            // Initialize timers on page load
            resetTimers();

            // Handle "Continue Session" button
            document.getElementById('continueSessionBtn')?.addEventListener('click', function () {
                resetTimers();
                keepSessionAlive();
            });
        })();

    </script>

    <!-- Session Timeout Warning Modal -->
    <div class="modal fade" id="sessionWarningModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 bg-warning bg-opacity-10">
                    <h5 class="modal-title fw-bold text-warning">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Session Timeout Warning
                    </h5>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="bi bi-clock-history text-warning" style="font-size: 3rem;"></i>
                    <h6 class="mt-3 mb-2">Your session will expire soon!</h6>
                    <p class="text-muted small mb-3">
                        You will be automatically logged out in <strong id="sessionCountdown">5:00</strong> due to
                        inactivity.
                    </p>
                    <p class="text-muted small mb-0">
                        Click "Continue" to stay logged in.
                    </p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn btn-warning px-4 shadow-sm" id="continueSessionBtn"
                        data-bs-dismiss="modal">
                        <i class="bi bi-check-lg me-1"></i> Continue Session
                    </button>
                </div>
            </div>
        </div>
    </div>





    <!-- Sidebar Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('🔧 Sidebar Toggle Script Initialized');

            const sidebar = document.getElementById('mainSidebar');
            const body = document.body;
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');

            // Restore collapsed state from localStorage on desktop
            const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
            if (window.innerWidth >= 1200 && isCollapsed) {
                body.classList.add('sidebar-collapsed');
                console.log('📱 Desktop mode: Sidebar collapsed from localStorage');
            }

            // Sidebar toggle (desktop - collapse/expand)
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function (e) {
                    e.stopPropagation();
                    // Inline onclick will handle it
                });
            }

            // Topbar toggle (mobile - show/hide)
            if (sidebarCollapseBtn) {
                sidebarCollapseBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    // Inline onclick will handle it
                });
            }

            // Close sidebar on outside click (mobile only)
            document.addEventListener('click', function (event) {
                if (window.innerWidth < 1200 && sidebar && sidebar.classList.contains('show')) {
                    const isClickInsideSidebar = sidebar.contains(event.target);
                    const isClickOnToggle = (sidebarCollapseBtn && sidebarCollapseBtn.contains(event.target)) ||
                        (sidebarToggle && sidebarToggle.contains(event.target));

                    if (!isClickInsideSidebar && !isClickOnToggle) {
                        sidebar.classList.remove('show');
                        console.log('📱 Sidebar closed by outside click');
                    }
                }
            });

            // Handle resize - reset mobile state when switching to desktop
            window.addEventListener('resize', function () {
                if (window.innerWidth >= 1200 && sidebar) {
                    sidebar.classList.remove('show');
                }
            });

            console.log('✅ Sidebar toggle script loaded');
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>

</html>