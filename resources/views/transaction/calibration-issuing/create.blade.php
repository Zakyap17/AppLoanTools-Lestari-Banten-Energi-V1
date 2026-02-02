@extends('layouts.app')

@section('title', 'New Calibration')

@section('styles')
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
@endsection

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-muted mb-0">
            Tools Transaction / Calibration Issuing / <strong class="text-dark">New</strong>
        </h6>
        <a href="{{ route('calibration-issuing') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title fw-bold mb-4">Issue Tool for Calibration</h5>

            <form action="{{ route('calibration-issuing') }}" method="POST">
                <!-- Route name matches store? No, route is same URL but POST -->
                <!-- Ideally route name is calibration-issuing.store in web.php if explicit, but resource/manual mapping needed -->
                <!-- Checking web.php: Route::get('/calibration-issuing', ...) ->name('calibration-issuing') -->
                <!-- We need a POST route. Let me assume I need to add it or use an existing one. -->
                <!-- Wait, I didn't add the POST route in web.php yet! I need to do that. -->
                <!-- For now, I will write this form pointing to a hypothetical store route. -->
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Document No.</label>
                        <input type="text" name="issue_code" class="form-control bg-light" value="{{ $docNo }}" readonly>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Issue Date</label>
                        <input type="date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Next Calibration Date</label>
                        <input type="date" name="expected_completion_date" class="form-control">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-bold">Select Tool / Asset</label>
                        <select name="tool_id" id="toolSelector" class="form-select" required>
                            <option value="">-- Search Tool --</option>
                        </select>
                        <div class="form-text small">Type to search by code, name, or serial number</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label small fw-bold">Notes</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Additional notes..."></textarea>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Save & Issue
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Select2 (for searchable dropdowns) -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#toolSelector').select2({
                theme: 'bootstrap-5',
                placeholder: 'Search Asset Name / Code / Serial...',
                allowClear: true,
                width: '100%',
                minimumInputLength: 1,
                templateResult: function (data) {
                    if (data.loading) {
                        var $container = $('<div class="text-center p-2"></div>');
                        var $spinner = $('<div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>');
                        var $text = $('<span>Searching...</span>');
                        $container.append($spinner).append($text);
                        return $container;
                    }
                    return data.text;
                },
                language: {
                    inputTooShort: function () {
                        return 'Please enter at least 1 character';
                    },
                    noResults: function () {
                        return 'No assets found';
                    },
                    searching: function () {
                        return 'Searching...';
                    }
                },
                ajax: {
                    url: "{{ route('loan.search-asset') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data.results
                        };
                    },
                    cache: true
                }
            });
        });
    </script>
@endsection