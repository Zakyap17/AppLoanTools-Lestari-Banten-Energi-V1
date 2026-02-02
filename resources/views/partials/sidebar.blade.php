@php
   $currentRoute = Route::currentRouteName();

   $masterToolsRoutes = [
      'locations',
      'tools',
      'tools-type',
      'tools-mat-group',
      'tools-mat-sub-group',
      'vendors-calibration',
      'manufacture',
   ];

   $transactionRoutes = [
      'loan',
      'damage-lost',
      'calibration-issuing',
      'mutation',
   ];

   $isMasterToolsActive = false;
   if ($currentRoute) {
      foreach ($masterToolsRoutes as $route) {
         if (str_starts_with($currentRoute, $route)) {
            $isMasterToolsActive = true;
            break;
         }
      }
   }

   $isTransactionActive = false;
   if ($currentRoute) {
      foreach ($transactionRoutes as $route) {
         if (str_starts_with($currentRoute, $route)) {
            $isTransactionActive = true;
            break;
         }
      }
   }

   $reportRoutes = [
      'reporting-center',
   ];
@endphp

<div class="sidebar" id="mainSidebar">

   <div class="sidebar-header d-flex justify-content-between align-items-center px-3 mb-3" style="padding-top: 20px;">

      {{-- Sidebar Logo (Visible only when Expanded) --}}
      <div id="sidebar-logo-container" class="d-flex align-items-center gap-2 overflow-hidden">
         <img src="{{ asset('assets/images/logo-new.png') }}" alt="Logo" class="img-fluid rounded-3 flex-shrink-0"
            style="width: 28px; height: 28px;">
         <div class="d-flex flex-column" style="line-height: 1.1;">
            <span class="fw-bold sidebar-logo-text text-truncate"
               style="font-family: 'Inter', sans-serif; letter-spacing: -0.5px; color: #ffffff; font-size: 15px; max-width: 140px; display: block;">
               Management Tools.
            </span>
         </div>
      </div>

      {{-- Toggle button removed - now in topbar --}}
   </div>

   {{-- DASHBOARD --}}
   <a href="{{ route('dashboard') }}" class="menu-item {{ $currentRoute === 'dashboard' ? 'active' : '' }}">
      <i class="bi bi-speedometer2 me-2"></i>
      Dashboard
   </a>

   <hr class="divider">

   <div id="sidebarAccordion">

      {{-- ================= MASTER TOOLS ================= --}}
      <a class="menu-item {{ $isMasterToolsActive ? 'active' : '' }}" data-bs-toggle="collapse" href="#masterTools"
         aria-expanded="{{ $isMasterToolsActive ? 'true' : 'false' }}">
         <i class="bi bi-box-seam me-2"></i>
         Master Tools
      </a>

      <div class="collapse {{ $isMasterToolsActive ? 'show' : '' }}" id="masterTools"
         data-bs-parent="#sidebarAccordion">

         <a class="submenu {{ str_contains($currentRoute, 'locations') ? 'active' : '' }}"
            href="{{ route('locations.index') }}">Location</a>

         <a class="submenu {{ str_contains($currentRoute, 'tools-type') ? 'active' : '' }}"
            href="{{ route('tools-type.index') }}">Tools Type</a>

         <a class="submenu {{ str_contains($currentRoute, 'tools-mat-group') ? 'active' : '' }}"
            href="{{ route('tools-mat-group.index') }}">Material Groups</a>

         {{-- MERGED INTO MATERIAL GROUPS --}}
         {{-- <a class="submenu {{ str_contains($currentRoute, 'tools-mat-sub-group') ? 'active' : '' }}"
            href="{{ route('tools-mat-sub-group.index') }}">Tools MAT Sub Group</a> --}}

         <a class="submenu {{ $currentRoute === 'vendors-calibration' ? 'active' : '' }}"
            href="{{ route('vendors-calibration') }}">Vendor Calibration</a>

         <a class="submenu {{ str_contains($currentRoute, 'manufacture') ? 'active' : '' }}"
            href="{{ route('manufacture.index') }}">Manufacture</a>

         <a class="submenu {{ str_contains($currentRoute, 'tools') ? 'active' : '' }}"
            href="{{ route('tools.index') }}">Tools</a>
      </div>

      <hr class="divider">

      {{-- ================= TOOLS TRANSACTION ================= --}}
      <a class="menu-item {{ $isTransactionActive ? 'active' : '' }}" data-bs-toggle="collapse" href="#toolsTransaction"
         aria-expanded="{{ $isTransactionActive ? 'true' : 'false' }}">
         <i class="bi bi-arrow-left-right me-2"></i>
         Tools Transaction
      </a>

      <div class="collapse {{ $isTransactionActive ? 'show' : '' }}" id="toolsTransaction"
         data-bs-parent="#sidebarAccordion">

         <a class="submenu {{ str_contains($currentRoute, 'loan') ? 'active' : '' }}"
            href="{{ route('loan.index') }}">Loan</a>

         <a class="submenu {{ $currentRoute === 'damage-lost.index' ? 'active' : '' }}"
            href="{{ route('damage-lost.index') }}">Damage
            / Lost</a>

         {{-- <a class="submenu {{ $currentRoute === 'receive' ? 'active' : '' }}"
            href="{{ route('receive') }}">Receive</a> --}}

         <a class="submenu {{ $currentRoute === 'calibration-issuing' ? 'active' : '' }}"
            href="{{ route('calibration-issuing') }}">Calibration Issuing</a>

         <a class="submenu {{ $currentRoute === 'mutation' ? 'active' : '' }}"
            href="{{ route('mutation') }}">Mutation</a>
      </div>

      <hr class="divider">

      {{-- ================= REPORTING CENTER ================= --}}
      <a href="{{ route('reporting-center') }}"
         class="menu-item {{ in_array($currentRoute, $reportRoutes) ? 'active' : '' }}">
         <i class="bi bi-graph-up-arrow me-2"></i>
         Reporting Center
      </a>

      <hr class="divider">

      {{-- ================= EMPLOYEE ================= --}}
      <a href="{{ route('employee.index') }}"
         class="menu-item {{ str_contains($currentRoute, 'employee') ? 'active' : '' }}">
         <i class="bi bi-people me-2"></i>
         Employee
      </a>

   </div> {{-- END sidebarAccordion --}}




</div>

{{-- LOGOUT CONFIRMATION MODAL --}}
{{-- LOGOUT CONFIRMATION MODAL --}}