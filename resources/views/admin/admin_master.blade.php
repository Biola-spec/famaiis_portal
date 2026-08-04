<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if(app()->getLocale() == 'ar') dir="rtl" @endif>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{ (!empty($setting->logo))? url($setting->logo) : asset('backend/images/favicon.ico') }}">

    <title>{{ $setting->school_name ?? 'Easy School' }} - {{ __('ui.dashboard') }}</title>
    
	<!-- Vendors Style-->
	<link rel="stylesheet" href="{{ asset('backend/css/vendors_css.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/icons/flag-icon-css/css/flag-icon.min.css') }}">
	  
	<!-- Style-->  
	<link rel="stylesheet" href="{{ asset('backend/css/style.css') }}">
	<link rel="stylesheet" href="{{ asset('backend/css/skin_color.css') }}">
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('backend/css/style_rtl.css') }}">
    @endif
   
 <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" >
 @livewireStyles
      
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

        /* Root Font Scaling and Base Typography */
        html {
            font-size: 14px !important;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif !important;
        }

        h1, .h1 { font-size: 1.75rem !important; font-weight: 700 !important; }
        h2, .h2 { font-size: 1.5rem !important; font-weight: 700 !important; }
        h3, .h3 { font-size: 1.25rem !important; font-weight: 600 !important; }
        h4, .h4 { font-size: 1.1rem !important; font-weight: 600 !important; }
        h5, .h5 { font-size: 0.95rem !important; font-weight: 600 !important; }
        h6, .h6 { font-size: 0.85rem !important; font-weight: 600 !important; }

        /* Font Size Utilities Override for Professional Balance */
        .font-size-36 { font-size: 22px !important; }
        .font-size-28 { font-size: 18px !important; }
        .font-size-24 { font-size: 16px !important; }
        .font-size-18 { font-size: 14px !important; }
        .font-size-16 { font-size: 13px !important; }
        .font-size-14 { font-size: 12px !important; }
        .font-size-12 { font-size: 11px !important; }

        /* Semantic Design Tokens (Variables) */
        body.light-skin {
            --bg-body: #F5F7FA;
            --card-bg: #ffffff;
            --card-border: #E4E9F0;
            --text-main: #1E2E4A;
            --text-muted: #62728D;
            --heading-color: #0B2447;
            
            --primary-color: #2E86DE;      /* Sky Blue */
            --primary-light: #E8F2FC;      /* Sky Blue Tint */
            --warning-color: #1F3E6C;      /* Navy Blue */
            --warning-light: #EBEFF6;      /* Navy Blue Tint */
            --success-color: #0B2447;      /* Navy Blue */
            --success-light: #EBEFF6;      /* Navy Blue Tint */
            --danger-color: #E66767;       /* Brand Red */
            --danger-light: #FDEAEA;       /* Red Tint */
            --info-color: #2E86DE;         /* Sky Blue */
            --info-light: #E8F2FC;         /* Sky Blue Tint */
            --teal-color: #0f766e;
            --teal-light: #ccfbf1;
            --indigo-color: #4f46e5;
            --indigo-light: #e0e7ff;
            --emerald-color: #059669;
            --emerald-light: #d1fae5;
            --amber-color: #d97706;
            --amber-light: #fef3c7;
            --pink-color: #db2777;
            --pink-light: #fce7f3;
            --cyan-color: #0891b2;
            --cyan-light: #cffafe;

            background-color: var(--bg-body) !important;
            color: var(--text-main) !important;
        }

        body.dark-skin {
            --bg-body: #0a0f1d;
            --card-bg: #111827;
            --card-border: #1f2937;
            --text-main: #f3f4f6;
            --text-muted: #9ca3af;
            --heading-color: #f9fafb;
            
            --primary-color: #818cf8;
            --primary-light: rgba(129, 140, 248, 0.15);
            --warning-color: #fbbf24;
            --warning-light: rgba(251, 191, 36, 0.15);
            --success-color: #34d399;
            --success-light: rgba(52, 211, 153, 0.15);
            --danger-color: #f87171;
            --danger-light: rgba(248, 113, 113, 0.15);
            --info-color: #38bdf8;
            --info-light: rgba(56, 189, 248, 0.15);
            --teal-color: #2dd4bf;
            --teal-light: rgba(45, 212, 191, 0.15);
            --indigo-color: #818cf8;
            --indigo-light: rgba(129, 140, 248, 0.15);
            --emerald-color: #34d399;
            --emerald-light: rgba(52, 211, 153, 0.15);
            --amber-color: #fbbf24;
            --amber-light: rgba(251, 191, 36, 0.15);
            --pink-color: #f472b6;
            --pink-light: rgba(244, 114, 182, 0.15);
            --cyan-color: #22d3ee;
            --cyan-light: rgba(34, 211, 238, 0.15);

            background-color: var(--bg-body) !important;
            color: var(--text-main) !important;
        }

        /* Sidebar Styling (Stays dark for both modes to remain premium) */
        .main-sidebar {
            background: linear-gradient(180deg, #0f0f12 0%, #1e1e24 100%) !important; /* Premium charcoal gray gradient */
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.3);
            border-right: 1px solid rgba(255, 255, 255, 0.03) !important;
        }

        .main-sidebar .sidebar,
        .main-sidebar .user-profile,
        .main-sidebar .ulogo {
            background: transparent !important;
        }

        .main-sidebar .ulogo h3,
        .main-sidebar .sidebar-menu > li > a,
        .main-sidebar .sidebar-menu > li.header,
        .main-sidebar .sidebar-menu li a span {
            color: #94a3b8 !important;
            font-weight: 500 !important;
            font-size: 13px;
            letter-spacing: 0.2px;
        }

        .main-sidebar .ulogo h3 b {
            color: #ffffff !important;
        }

        .main-sidebar .sidebar-menu li a i,
        .main-sidebar [data-feather] {
            color: #94a3b8 !important;
            stroke: #94a3b8 !important;
            width: 18px;
            height: 18px;
            margin-right: 10px;
            transition: all 0.2s ease;
        }

        .sidebar-menu > li > a {
            border-left: 4px solid transparent !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
            padding: 12px 16px !important;
        }

        .sidebar-menu > li.header {
            font-size: 11px !important;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748b !important;
            padding: 16px 16px 8px !important;
            background: transparent !important;
        }

        /* Distinct Hover State */
        .sidebar-menu > li:hover > a {
            background-color: rgba(255, 255, 255, 0.07) !important;
            color: #ffffff !important;
            border-left-color: rgba(255, 255, 255, 0.3) !important;
        }

        .sidebar-menu > li:hover > a i,
        .sidebar-menu > li:hover > a [data-feather] {
            color: #ffffff !important;
            stroke: #ffffff !important;
        }

        /* Active/Selected State — solid brand accent (no gradient) */
        .sidebar-menu > li.active,
        .sidebar-menu > li.active.menu-open {
            background: transparent !important;
            box-shadow: none !important;
        }

        .sidebar-menu > li.active > a,
        .sidebar-menu > li.active.treeview > a,
        .sidebar-menu > li.menu-open > a {
            background-color: rgba(46, 134, 222, 0.16) !important;
            background-image: none !important;
            color: #ffffff !important;
            border-left-color: #2E86DE !important;
            font-weight: 600 !important;
            box-shadow: none !important;
        }

        .sidebar-menu > li.active > a i,
        .sidebar-menu > li.active > a [data-feather],
        .sidebar-menu > li.menu-open > a i,
        .sidebar-menu > li.menu-open > a [data-feather] {
            color: #5eb3ff !important;
            stroke: #5eb3ff !important;
            filter: none !important;
        }

        .sidebar-menu .treeview-menu {
            background-color: rgba(0, 0, 0, 0.15) !important;
            padding-left: 10px;
        }

        .sidebar-menu .treeview-menu > li > a {
            color: #94a3b8 !important;
            font-size: 12.5px;
            padding: 8px 10px !important;
        }

        .sidebar-menu .treeview-menu > li > a:hover,
        .sidebar-menu .treeview-menu > li.active > a {
            color: #ffffff !important;
            background: transparent !important;
        }

        /* Header Styling */
        .main-header {
            background-color: var(--card-bg) !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
            border-bottom: 1px solid var(--card-border) !important;
        }

        .main-header .navbar {
            background-color: transparent !important;
        }

        .main-header .nav-link-icon,
        .main-header .navbar-custom-menu .nav > li > a {
            color: var(--text-main) !important;
            font-weight: 500;
        }

        /* General Dashboard Layout and Box (Card) Styling */
        .content-wrapper,
        section.content {
            background-color: var(--bg-body) !important;
        }

        .box, .card {
            background-color: var(--card-bg) !important;
            border: 1px solid var(--card-border) !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -1px rgba(0, 0, 0, 0.02) !important;
            border-radius: 12px !important;
            color: var(--text-main) !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .box.pull-up:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02) !important;
        }

        .box-header, .card-header {
            border-bottom: 1px solid var(--card-border) !important;
            background-color: transparent !important;
            padding: 16px 20px !important;
        }

        .box-title, .card-title {
            color: var(--heading-color) !important;
            font-weight: 700 !important;
            font-size: 15px !important;
        }

        .box-body, .card-body {
            padding: 20px !important;
        }

        /* Text and Typography Overrides to prevent generic text override hacks */
        .text-fade, .text-mute {
            color: var(--text-muted) !important;
        }

        /* Soft, Cohesive Theme-aware Table Styling */
        .table {
            color: var(--text-main) !important;
        }

        .table thead th {
            background-color: var(--bg-body) !important;
            color: var(--text-muted) !important;
            font-weight: 600 !important;
            font-size: 11px !important;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--card-border) !important;
            border-top: none !important;
        }

        .table td {
            border-top: 1px solid var(--card-border) !important;
            font-size: 13px !important;
            vertical-align: middle !important;
            color: var(--text-main) !important;
        }

        /* Cohesive modern statistics card colors & formatting */
        .stat-card-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .stat-card-title {
            font-size: 11px !important;
            font-weight: 700 !important;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted) !important;
            margin-bottom: 6px;
        }

        .stat-card-number {
            font-size: 24px !important;
            font-weight: 800 !important;
            color: var(--heading-color) !important;
            line-height: 1 !important;
        }

        /* Semantic icon settings */
        .stat-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        /* Card Type 1: Primary (Students) */
        .stat-card-primary .stat-icon-box {
            background-color: var(--primary-light) !important;
        }
        .stat-card-primary .stat-icon-box i {
            color: var(--primary-color) !important;
        }

        /* Card Type 2: Warning (Teachers) */
        .stat-card-warning .stat-icon-box {
            background-color: var(--warning-light) !important;
        }
        .stat-card-warning .stat-icon-box i {
            color: var(--warning-color) !important;
        }

        /* Card Type 3: Success (Parents / Children) */
        .stat-card-success .stat-icon-box {
            background-color: var(--success-light) !important;
        }
        .stat-card-success .stat-icon-box i {
            color: var(--success-color) !important;
        }

        /* Card Type 4: Danger (Attendance) */
        .stat-card-danger .stat-icon-box {
            background-color: var(--danger-light) !important;
        }
        .stat-card-danger .stat-icon-box i {
            color: var(--danger-color) !important;
        }

        /* Card Type 5: Info */
        .stat-card-info .stat-icon-box {
            background-color: var(--info-light) !important;
        }
        .stat-card-info .stat-icon-box i {
            color: var(--info-color) !important;
        }

        @media (min-width: 1200px) {
            .dashboard-stat-col {
                flex: 0 0 20%;
                max-width: 20%;
            }
        }

        .stat-card-teal .stat-icon-box { background-color: var(--teal-light) !important; }
        .stat-card-teal .stat-icon-box i { color: var(--teal-color) !important; }
        .stat-card-indigo .stat-icon-box { background-color: var(--indigo-light) !important; }
        .stat-card-indigo .stat-icon-box i { color: var(--indigo-color) !important; }
        .stat-card-emerald .stat-icon-box { background-color: var(--emerald-light) !important; }
        .stat-card-emerald .stat-icon-box i { color: var(--emerald-color) !important; }
        .stat-card-amber .stat-icon-box { background-color: var(--amber-light) !important; }
        .stat-card-amber .stat-icon-box i { color: var(--amber-color) !important; }
        .stat-card-pink .stat-icon-box { background-color: var(--pink-light) !important; }
        .stat-card-pink .stat-icon-box i { color: var(--pink-color) !important; }
        .stat-card-cyan .stat-icon-box { background-color: var(--cyan-light) !important; }
        .stat-card-cyan .stat-icon-box i { color: var(--cyan-color) !important; }

        .dashboard-compact-table {
            max-height: 18rem;
            overflow: auto;
        }

        .dashboard-compact-table .table th,
        .dashboard-compact-table .table td {
            padding: 0.55rem 0.75rem !important;
        }

        .dashboard-student-avatar {
            width: 32px;
            height: 32px;
            object-fit: cover;
        }

        .dashboard-chart-wrap {
            min-height: 210px;
            position: relative;
        }

        .dashboard-chart-panel {
            background:
                linear-gradient(135deg, rgba(129, 140, 248, 0.10), rgba(34, 211, 238, 0.06)),
                var(--card-bg) !important;
        }

        .dashboard-chart-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .dashboard-chart-card {
            min-height: 280px;
            padding: 16px;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            background-color: rgba(255, 255, 255, 0.02);
        }

        .dashboard-chart-card h5 {
            color: var(--heading-color) !important;
            font-size: 13px !important;
            margin-bottom: 12px;
        }

        .dashboard-insight-strip {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .dashboard-insight {
            border-radius: 12px;
            padding: 12px;
            color: #fff;
            overflow: hidden;
        }

        .dashboard-insight span {
            display: block;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.7px;
            text-transform: uppercase;
            opacity: 0.82;
        }

        .dashboard-insight strong {
            display: block;
            font-size: 20px;
            line-height: 1.15;
            margin-top: 5px;
        }

        .dashboard-insight-purple { background: linear-gradient(135deg, #7c3aed, #ec4899); }
        .dashboard-insight-cyan { background: linear-gradient(135deg, #0891b2, #22d3ee); }
        .dashboard-insight-amber { background: linear-gradient(135deg, #d97706, #facc15); }
        .dashboard-insight-green { background: linear-gradient(135deg, #059669, #84cc16); }

        @media (max-width: 991px) {
            .dashboard-chart-grid,
            .dashboard-insight-strip {
                grid-template-columns: 1fr;
            }
        }

        @media (min-width: 992px) and (max-width: 1399px) {
            .dashboard-chart-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        /* Button & Form Overrides */
        .btn {
            border-radius: 8px !important;
            font-weight: 600 !important;
            padding: 8px 16px !important;
            font-size: 13px !important;
        }

        .btn-sm {
            padding: 5px 10px !important;
            font-size: 11.5px !important;
        }

        .form-control, select.form-control {
            background-color: var(--card-bg) !important;
            color: var(--text-main) !important;
            border: 1px solid var(--card-border) !important;
            border-radius: 8px !important;
            height: auto !important;
            padding: 8px 12px !important;
            font-size: 13px !important;
            transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
        }

        .form-control:focus, select.form-control:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15) !important;
            outline: 0 !important;
        }

        /* Themed Circle Buttons Override (sweet hover) */
        .btn-circle,
        .btn-info.btn-circle {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #ffffff !important;
            border-radius: 50% !important;
            width: 36px !important;
            height: 36px !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        .btn-circle:hover,
        .btn-info.btn-circle:hover {
            background-color: #3730a3 !important; /* Darker indigo */
            border-color: #3730a3 !important;
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3) !important;
        }

        /* Search input lookup circle theme */
        .lookup-circle input {
            border-radius: 20px !important;
            border: 1px solid var(--card-border) !important;
            background-color: var(--bg-body) !important;
            color: var(--text-main) !important;
            transition: all 0.3s ease !important;
        }

        .lookup-circle input:hover,
        .lookup-circle input:focus {
            border-color: var(--primary-color) !important;
            background-color: var(--card-bg) !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15) !important;
        }

        /* Mobile Responsive Overrides */
        @media (max-width: 767px) {
            html {
                font-size: 13.5px !important;
                height: auto !important;
                min-height: 100% !important;
                overflow-y: auto !important;
            }

            body {
                height: auto !important;
                min-height: 100% !important;
                overflow-x: hidden !important;
                overflow-y: auto !important;
                -webkit-overflow-scrolling: touch;
            }

            .fixed .wrapper,
            .wrapper {
                height: auto !important;
                min-height: 100vh !important;
                overflow-x: hidden !important;
                overflow-y: visible !important;
            }

            .content-wrapper {
                min-height: 100vh !important;
                overflow: visible !important;
            }

            .main-header .navbar {
                padding-left: 10px !important;
                padding-right: 10px !important;
                margin-left: 0 !important;
            }

            /* Ensure navigation fits on single line without scrollbar */
            .main-header .navbar-custom-menu {
                max-width: none !important;
                overflow-x: visible !important;
                float: right;
            }

            .main-header .navbar-custom-menu .navbar-nav {
                display: flex !important;
                flex-direction: row !important;
                align-items: center;
                margin: 0;
            }

            .main-header .navbar-custom-menu .navbar-nav > li {
                display: inline-block;
            }

            .main-header .navbar-custom-menu .navbar-nav > li > a {
                padding: 0 6px !important;
                min-width: 36px !important;
                height: 36px !important;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }

            /* Reduce header height slightly for compact screen real estate */
            .main-header {
                min-height: 50px !important;
            }

            .content-wrapper {
                padding-top: 50px !important;
            }

            .main-sidebar {
                margin-top: 50px !important;
                height: calc(100vh - 50px) !important;
                max-height: calc(100vh - 50px) !important;
                overflow: hidden !important;
            }

            .main-sidebar .sidebar,
            .main-sidebar .slimScrollDiv {
                height: calc(100vh - 98px) !important;
                max-height: calc(100vh - 98px) !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                -webkit-overflow-scrolling: touch;
            }

            .main-sidebar .slimScrollBar,
            .main-sidebar .slimScrollRail {
                display: none !important;
            }

            .sidebar-menu .treeview.active:not(.menu-open) > .treeview-menu {
                display: none !important;
            }

            .sidebar-menu .treeview.menu-open > .treeview-menu {
                display: block !important;
}

            .content, section.content {
                padding: 10px !important;
            }
        }

        /* ========================================== */
        /* Light Skin Sidebar Customization           */
        /* ========================================== */
        .light-skin .main-sidebar {
            background: linear-gradient(180deg, #121215 0%, #222228 100%) !important; /* Premium charcoal gray gradient */
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.25);
            border-right: 1px solid rgba(255, 255, 255, 0.03) !important;
        }

        .light-skin .main-sidebar .ulogo h3,
        .light-skin .main-sidebar .sidebar-menu > li > a,
        .light-skin .main-sidebar .sidebar-menu > li.header,
        .light-skin .main-sidebar .sidebar-menu li a span {
            color: #a1a1aa !important; /* Soft medium gray for readability */
        }

        .light-skin .main-sidebar .sidebar-menu li a i,
        .light-skin .main-sidebar [data-feather] {
            color: #a1a1aa !important;
            stroke: #a1a1aa !important;
        }

        /* Sidebar hover state in light skin */
        .light-skin .sidebar-menu > li:hover > a {
            background-color: rgba(255, 255, 255, 0.08) !important;
            color: #ffffff !important;
            border-left-color: rgba(255, 255, 255, 0.3) !important;
        }

        .light-skin .sidebar-menu > li:hover > a i,
        .light-skin .sidebar-menu > li:hover > a [data-feather] {
            color: #ffffff !important;
            stroke: #ffffff !important;
        }

        /* Override theme-primary purple gradient from color_theme.css */
        body.theme-primary.light-skin .sidebar-menu > li.active,
        body.theme-primary.light-skin .sidebar-menu > li.active.menu-open,
        body.theme-primary.dark-skin .sidebar-menu > li.active,
        body.theme-primary.dark-skin .sidebar-menu > li.active.menu-open {
            background: transparent !important;
            box-shadow: none !important;
        }

        body.theme-primary .sidebar-menu > li.active > a,
        body.theme-primary .sidebar-menu > li.active.treeview > a,
        body.theme-primary .sidebar-menu > li.active.treeview.treeview > a,
        body.theme-primary .sidebar-menu > li.menu-open > a {
            background-color: rgba(46, 134, 222, 0.16) !important;
            background-image: none !important;
            color: #ffffff !important;
            border-left-color: #2E86DE !important;
            box-shadow: none !important;
        }

        body.theme-primary .sidebar-menu > li.active > a > i,
        body.theme-primary .sidebar-menu > li.active > a > svg,
        body.theme-primary .sidebar-menu > li.active > a [data-feather],
        body.theme-primary .sidebar-menu > li.active.treeview > a > i,
        body.theme-primary .sidebar-menu > li.menu-open > a > i,
        body.theme-primary .sidebar-menu > li.menu-open > a [data-feather] {
            color: #5eb3ff !important;
            stroke: #5eb3ff !important;
        }

        body.theme-primary.sidebar-mini.sidebar-collapse .sidebar-menu > li.active > a > span {
            background-color: #2E86DE !important;
            background-image: none !important;
            color: #ffffff !important;
        }

        body.theme-primary .sidebar-menu > li.active .treeview-menu li.active a {
            color: #5eb3ff !important;
        }

        body.theme-primary .sidebar-menu > li.active.treeview > a:after {
            display: none !important;
        }

        .light-skin .sidebar-menu > li.active,
        .light-skin .sidebar-menu > li.active.menu-open {
            background: transparent !important;
            box-shadow: none !important;
        }

        .light-skin .sidebar-menu > li.active > a,
        .light-skin .sidebar-menu > li.active.treeview > a,
        .light-skin .sidebar-menu > li.menu-open > a {
            background-color: rgba(46, 134, 222, 0.16) !important;
            background-image: none !important;
            color: #ffffff !important;
            border-left-color: #2E86DE !important;
            box-shadow: none !important;
        }

        .light-skin .sidebar-menu > li.active > a i,
        .light-skin .sidebar-menu > li.active > a [data-feather],
        .light-skin .sidebar-menu > li.menu-open > a i,
        .light-skin .sidebar-menu > li.menu-open > a [data-feather] {
            color: #5eb3ff !important;
            stroke: #5eb3ff !important;
            filter: none !important;
        }

        .light-skin .sidebar-menu .treeview-menu > li > a {
            color: #a1a1aa !important;
        }

        .light-skin .sidebar-menu .treeview-menu > li > a:hover,
        .light-skin .sidebar-menu .treeview-menu > li.active > a {
            color: #ffffff !important;
        }

        /* ========================================== */
        /* Light Skin Buttons and Content Links       */
        /* ========================================== */
        .light-skin .btn-primary,
        .light-skin .btn-primary.disabled,
        .light-skin .btn-primary:disabled {
            background-color: #2E86DE !important;
            border-color: #2E86DE !important;
            color: #ffffff !important;
        }

        .light-skin .btn-primary:hover,
        .light-skin .btn-primary:active,
        .light-skin .btn-primary:focus,
        .light-skin .btn-primary:not(:disabled):not(.disabled):active {
            background-color: #1b74d1 !important;
            border-color: #1b74d1 !important;
            color: #ffffff !important;
        }

        /* Circular primary buttons hover states */
        .light-skin .btn-circle:hover,
        .light-skin .btn-info.btn-circle:hover {
            background-color: #1b74d1 !important;
            border-color: #1b74d1 !important;
            box-shadow: 0 4px 12px rgba(46, 134, 222, 0.3) !important;
        }

        /* Content links styling in light skin */
        .light-skin .content-wrapper a:not(.btn):not(.sidebar-menu a):not(.navbar a) {
            color: #2E86DE;
            transition: color 0.15s ease;
        }

        .light-skin .content-wrapper a:not(.btn):not(.sidebar-menu a):not(.navbar a):hover {
            color: #1b74d1;
            text-decoration: underline;
        }

        .light-skin .hover-primary:hover {
            color: #2E86DE !important;
        }

        /* ========================================== */
        /* Typography Overrides                       */
        /* ========================================== */
        .light-skin h1, .light-skin .h1, 
        .light-skin h2, .light-skin .h2, 
        .light-skin h3, .light-skin .h3, 
        .light-skin h4, .light-skin .h4, 
        .light-skin h5, .light-skin .h5, 
        .light-skin h6, .light-skin .h6 {
            color: var(--heading-color) !important;
        }

    </style>

    <script>
        // Apply theme immediately to prevent flashing
        (function() {
            const savedTheme = localStorage.getItem('app-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
            const skinClass = savedTheme === 'dark' ? 'dark-skin' : 'light-skin';
            // We'll apply this to body in the next chunk
        })();
    </script>
  </head>

<body class="hold-transition sidebar-mini theme-primary fixed">
    <script>
        // Set initial body class and sidebar state
        (function() {
            const savedTheme = localStorage.getItem('app-theme') || 'dark';
            document.body.classList.add(savedTheme === 'dark' ? 'dark-skin' : 'light-skin');

            const sidebarCollapsed = localStorage.getItem('sidebar-collapsed');
            const isMobile = window.matchMedia('(max-width: 767px)').matches;
            if (!isMobile && sidebarCollapsed === 'true') {
                document.body.classList.add('sidebar-collapse');
            } else if (!isMobile && sidebarCollapsed === 'false') {
                document.body.classList.remove('sidebar-collapse');
            } else {
                document.body.classList.remove('sidebar-collapse');
            }

            const sidebarOpen = localStorage.getItem('sidebar-open');
            if (!isMobile && sidebarOpen === 'true') {
                document.body.classList.add('sidebar-open');
            } else {
                document.body.classList.remove('sidebar-open');
                localStorage.removeItem('sidebar-open');
            }
        })();
    </script>
	
<div class="wrapper">

  @include('admin.body.header')
  <!-- Left side column. contains the logo and sidebar -->
  @include('admin.body.sidebar')

  <!-- Content Wrapper. Contains page content -->
  @if ($errors->any())
    <div class="alert alert-danger" style="margin: 20px 30px 0 30px; border-radius: 8px;">
        <ul style="margin-bottom: 0;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
  @endif
  @yield('admin')
  <!-- /.content-wrapper -->

 @include('admin.body.footer')

  <div class="control-sidebar-bg"></div>
  
</div>
<!-- ./wrapper -->
  	
	  
	<!-- Vendor JS -->
	<script src="{{ asset('backend/js/vendors.min.js') }}"></script>
    <script src="{{ asset('../assets/icons/feather-icons/feather.min.js') }}"></script>	
	<script src="{{ asset('../assets/vendor_components/easypiechart/dist/jquery.easypiechart.js') }}"></script>
	<script src="{{ asset('../assets/vendor_components/apexcharts-bundle/irregular-data-series.js') }}"></script>
	<script src="{{ asset('../assets/vendor_components/apexcharts-bundle/dist/apexcharts.js') }}"></script>
	

<script src="{{asset('../assets/vendor_components/datatable/datatables.min.js')}}"></script>
  <script src="{{asset('backend/js/pages/data-table.js')}}?v=2"></script>


	<!-- Sunny Admin App -->
	<script src="{{ asset('backend/js/template.js') }}"></script>
	<script src="{{ asset('backend/js/pages/dashboard.js') }}"></script>
	

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script type="text/javascript">
  $(function(){
    $(document).on('click','#delete',function(e){
        e.preventDefault();
        var link = $(this).attr("href");

  
                  Swal.fire({
                    title: 'Are you sure?',
                    text: "Delete This Data?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1a237e',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                  }).then((result) => {
                    if (result.isConfirmed) {
                      window.location.href = link
                      Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                      )
                    }
                  }) 


    });

  });


</script> 


<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

 <script>
  @if(Session::has('message'))
  var type = "{{ Session::get('alert-type','info') }}"
  switch(type){
     case 'info':
     toastr.info(" {{ Session::get('message') }} ");
     break;
 
     case 'success':
     toastr.success(" {{ Session::get('message') }} ");
     break;
 
     case 'warning':
     toastr.warning(" {{ Session::get('message') }} ");
     break;
 
     case 'error':
     toastr.error(" {{ Session::get('message') }} ");
     break; 
  }
  @endif 
 </script>
 
 <script>
    $(document).ready(function() {
        const themeToggle = $('#theme-toggle');
        const themeIcon = $('#theme-icon');
        const body = $('body');

        // Initial icon setup
        const currentTheme = localStorage.getItem('app-theme') || 'dark';
        if (currentTheme === 'light') {
            themeIcon.removeClass('mdi-weather-night').addClass('mdi-weather-sunny');
        }

        themeToggle.on('click', function() {
            if (body.hasClass('dark-skin')) {
                // Switch to light
                body.removeClass('dark-skin').addClass('light-skin');
                themeIcon.removeClass('mdi-weather-night').addClass('mdi-weather-sunny');
                localStorage.setItem('app-theme', 'light');
            } else {
                // Switch to dark
                body.removeClass('light-skin').addClass('dark-skin');
                themeIcon.removeClass('mdi-weather-sunny').addClass('mdi-weather-night');
                localStorage.setItem('app-theme', 'dark');
            }
        });

        // Restore sidebar scroll position
        const sidebarScroll = localStorage.getItem('sidebar-scroll');
        if (sidebarScroll) {
            $('.sidebar').scrollTop(parseInt(sidebarScroll, 10));
        }

        // Save scroll position and sidebar states when navigating
        $(document).on('click', '.sidebar-menu a', function() {
            const scrollTop = $('.sidebar').scrollTop();
            localStorage.setItem('sidebar-scroll', scrollTop);

            if (window.matchMedia('(max-width: 767px)').matches) {
                localStorage.removeItem('sidebar-open');
                localStorage.removeItem('sidebar-collapsed');
            } else {
                const isCollapsed = $('body').hasClass('sidebar-collapse');
                const isOpen = $('body').hasClass('sidebar-open');
                localStorage.setItem('sidebar-collapsed', isCollapsed);
                localStorage.setItem('sidebar-open', isOpen);
            }
        });

        // Listen to sidebar toggle clicks to update state
        $(document).on('click', '[data-toggle="push-menu"]', function() {
            setTimeout(function() {
                const isMobile = window.matchMedia('(max-width: 767px)').matches;
                const isCollapsed = $('body').hasClass('sidebar-collapse');
                const isOpen = $('body').hasClass('sidebar-open');
                if (isMobile) {
                    localStorage.removeItem('sidebar-open');
                    localStorage.removeItem('sidebar-collapsed');
                } else {
                    localStorage.setItem('sidebar-collapsed', isCollapsed);
                    localStorage.setItem('sidebar-open', isOpen);
                }
            }, 150);
        });

        $(window).on('resize orientationchange', function() {
            if (window.matchMedia('(max-width: 767px)').matches) {
                $('body').removeClass('sidebar-collapse sidebar-open');
                localStorage.removeItem('sidebar-open');
                localStorage.removeItem('sidebar-collapsed');
            }
        });
    });
 </script>


	
 @livewireScripts

 <audio id="chat-notification-sound" src="{{ asset('backend/audio/notification.mp3') }}" preload="auto"></audio>

 <script>
    document.addEventListener('livewire:initialized', () => {
        @if(Auth::check())
            const authUserId = {{ Auth::id() }};
            
            // This is a simplified Echo setup. 
            // In a real app, you'd use Laravel Echo and Pusher.
            // For now, we'll listen for the custom event dispatched by Livewire.
            
            window.addEventListener('play-notification-sound', event => {
                const audio = document.getElementById('chat-notification-sound');
                if (audio) {
                    audio.play().catch(e => console.log('Audio play failed:', e));
                }
                
                toastr.info('New message received', 'Chat');
            });

            // If Echo is available (configured in app.js or similar)
            if (window.Echo) {
                window.Echo.private(`chat.user.${authUserId}`)
                    .listen('.message.sent', (e) => {
                        console.log('New Message:', e);
                        Livewire.dispatch('refresh');
                        
                        // Only play sound if not on the chat page or chat window is inactive
                        if (!window.location.href.includes('/chat')) {
                            const audio = document.getElementById('chat-notification-sound');
                            if (audio) audio.play();
                            toastr.info(e.message.message, 'New Message from ' + e.message.sender.name);
                        }
                    });
            }
        @endif
    });
 </script>
@stack('scripts')
</body>
</html>
