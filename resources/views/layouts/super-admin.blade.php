<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page_title', 'Dashboard') — ServiceRDC Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --sidebar-w: 220px;
            --topbar-h: 60px;
            --bg-sidebar: #0f172a;
            --bg-sidebar-hover: #1e293b;
            --bg-sidebar-active: #1d4ed8;
            --bg-main: #f1f5f9;
            --bg-card: #ffffff;
            --border: #e2e8f0;
            --border-light: #f1f5f9;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --text-sidebar: #94a3b8;
            --text-sidebar-active: #ffffff;
            --accent: #2563eb;
            --accent-light: #eff6ff;
            --accent-hover: #1d4ed8;
            --green: #16a34a;
            --green-bg: #f0fdf4;
            --red: #dc2626;
            --red-bg: #fef2f2;
            --amber: #d97706;
            --amber-bg: #fffbeb;
            --radius: 8px;
            --radius-sm: 6px;
            --shadow: 0 1px 3px rgba(0, 0, 0, .08), 0 1px 2px rgba(0, 0, 0, .06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, .07), 0 2px 4px -1px rgba(0, 0, 0, .05);
        }

        html,
        body {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--bg-main);
            color: var(--text-primary);
        }

        [x-cloak] {
            display: none !important;
        }

        /* ─── SIDEBAR ─── */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: var(--sidebar-w);
            background: var(--bg-sidebar);
            display: flex;
            flex-direction: column;
            z-index: 50;
            transition: transform .25s ease;
        }

        .sidebar-logo {
            height: var(--topbar-h);
            padding: 0 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, .06);
            flex-shrink: 0;
        }

        .sidebar-logo .logo-icon {
            width: 30px;
            height: 30px;
            background: var(--accent);
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .sidebar-logo .logo-text {
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -.3px;
        }

        .sidebar-logo .logo-badge {
            margin-left: auto;
            font-size: 10px;
            font-weight: 600;
            color: var(--accent);
            background: rgba(37, 99, 235, .15);
            padding: 2px 7px;
            border-radius: 20px;
        }

        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 12px 10px;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 3px;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, .1);
            border-radius: 10px;
        }

        .nav-section {
            margin-bottom: 24px;
        }

        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(148, 163, 184, .5);
            padding: 0 10px;
            margin-bottom: 4px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 10px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            color: var(--text-sidebar);
            text-decoration: none;
            cursor: pointer;
            transition: background .15s, color .15s;
            position: relative;
        }

        .nav-item:hover {
            background: var(--bg-sidebar-hover);
            color: #e2e8f0;
        }

        .nav-item.active {
            background: var(--bg-sidebar-active);
            color: var(--text-sidebar-active);
        }

        .nav-item .nav-icon {
            width: 16px;
            text-align: center;
            font-size: 13px;
            flex-shrink: 0;
        }

        .nav-item .nav-badge {
            margin-left: auto;
            font-size: 10px;
            font-weight: 600;
            padding: 1px 6px;
            border-radius: 20px;
            background: rgba(255, 255, 255, .12);
            color: rgba(255, 255, 255, .7);
        }

        .nav-item.active .nav-badge {
            background: rgba(255, 255, 255, .2);
            color: #fff;
        }

        .sidebar-footer {
            padding: 12px 10px;
            border-top: 1px solid rgba(255, 255, 255, .06);
            flex-shrink: 0;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 10px;
            border-radius: var(--radius-sm);
            margin-bottom: 4px;
        }

        .sidebar-user .avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--accent);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .sidebar-user .user-info {
            min-width: 0;
        }

        .sidebar-user .user-name {
            font-size: 12px;
            font-weight: 600;
            color: #e2e8f0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-user .user-role {
            font-size: 10px;
            color: var(--text-muted);
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 8px 10px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            color: #64748b;
            text-decoration: none;
            cursor: pointer;
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            transition: background .15s, color .15s;
        }

        .logout-btn:hover {
            background: rgba(220, 38, 38, .1);
            color: #f87171;
        }

        /* ─── TOPBAR ─── */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            gap: 16px;
            z-index: 40;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .mobile-menu-btn {
            display: none;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: none;
            cursor: pointer;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .topbar-breadcrumb {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .topbar-breadcrumb span {
            color: var(--text-muted);
            font-weight: 400;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .search-wrap {
            position: relative;
        }

        .search-input {
            height: 36px;
            padding: 0 12px 0 36px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--bg-main);
            font-size: 13px;
            color: var(--text-primary);
            width: 220px;
            outline: none;
            transition: border-color .15s, background .15s;
        }

        .search-input::placeholder {
            color: var(--text-muted);
        }

        .search-input:focus {
            border-color: var(--accent);
            background: #fff;
        }

        .search-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 12px;
            pointer-events: none;
        }

        .icon-btn {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: none;
            cursor: pointer;
            color: var(--text-secondary);
            font-size: 14px;
            position: relative;
            text-decoration: none;
            transition: border-color .15s, background .15s;
        }

        .icon-btn:hover {
            background: var(--bg-main);
            border-color: #cbd5e1;
        }

        .icon-btn .notif-dot {
            position: absolute;
            top: 7px;
            right: 7px;
            width: 6px;
            height: 6px;
            background: var(--red);
            border-radius: 50%;
            border: 1.5px solid #fff;
        }

        .user-menu-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 10px 4px 4px;
            border: 1px solid var(--border);
            border-radius: 20px;
            background: none;
            cursor: pointer;
            transition: background .15s;
        }

        .user-menu-btn:hover {
            background: var(--bg-main);
        }

        .user-menu-btn .avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--accent);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-menu-btn .name {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .user-menu-btn .fa-chevron-down {
            font-size: 10px;
            color: var(--text-muted);
        }

        /* ─── MAIN CONTENT ─── */
        .main-wrap {
            margin-left: var(--sidebar-w);
            padding-top: var(--topbar-h);
            min-height: 100vh;
        }

        .page-content {
            padding: 28px;
        }

        /* ─── CARDS ─── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .card-body {
            padding: 20px;
        }

        /* ─── STAT CARDS ─── */
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
        }

        .stat-label {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 26px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -.5px;
            line-height: 1;
        }

        .stat-meta {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 8px;
        }

        .stat-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            margin-bottom: 16px;
        }

        /* ─── BADGES ─── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .badge-green {
            background: var(--green-bg);
            color: var(--green);
        }

        .badge-green .badge-dot {
            background: var(--green);
        }

        .badge-red {
            background: var(--red-bg);
            color: var(--red);
        }

        .badge-red .badge-dot {
            background: var(--red);
        }

        .badge-amber {
            background: var(--amber-bg);
            color: var(--amber);
        }

        .badge-amber .badge-dot {
            background: var(--amber);
        }

        .badge-blue {
            background: var(--accent-light);
            color: var(--accent);
        }

        .badge-blue .badge-dot {
            background: var(--accent);
        }

        .badge-gray {
            background: #f1f5f9;
            color: #64748b;
        }

        .badge-gray .badge-dot {
            background: #94a3b8;
        }

        /* ─── TABLE ─── */
        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            padding: 10px 14px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--text-muted);
            text-align: left;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
            cursor: pointer;
            user-select: none;
        }

        th:hover {
            color: var(--text-primary);
        }

        th .sort-icon {
            margin-left: 4px;
            opacity: .4;
            font-size: 10px;
        }

        th.sorted .sort-icon {
            opacity: 1;
            color: var(--accent);
        }

        td {
            padding: 12px 14px;
            font-size: 13px;
            color: var(--text-primary);
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: #fafafa;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--accent-light);
            color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .user-name {
            font-weight: 600;
            font-size: 13px;
        }

        .user-email {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ─── BUTTONS ─── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
            transition: background .15s, border-color .15s, color .15s;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }

        .btn-primary:hover {
            background: var(--accent-hover);
            border-color: var(--accent-hover);
        }

        .btn-secondary {
            background: var(--bg-card);
            color: var(--text-primary);
            border-color: var(--border);
        }

        .btn-secondary:hover {
            background: var(--bg-main);
        }

        .btn-danger {
            background: var(--red-bg);
            color: var(--red);
            border-color: #fecaca;
        }

        .btn-danger:hover {
            background: #fee2e2;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .btn-icon {
            padding: 7px 8px;
        }

        /* ─── ACTION MENU ─── */
        .action-menu {
            position: relative;
        }

        .action-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 4px);
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow-md);
            min-width: 160px;
            z-index: 100;
            overflow: hidden;
        }

        .action-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 9px 14px;
            font-size: 13px;
            color: var(--text-primary);
            cursor: pointer;
            text-decoration: none;
            transition: background .12s;
        }

        .action-item:hover {
            background: var(--bg-main);
        }

        .action-item.danger {
            color: var(--red);
        }

        .action-item.danger:hover {
            background: var(--red-bg);
        }

        .action-item i {
            width: 14px;
            text-align: center;
            font-size: 12px;
            color: var(--text-muted);
        }

        .action-item.danger i {
            color: var(--red);
        }

        /* ─── SLIDE-OVER MODAL ─── */
        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .4);
            z-index: 200;
            display: flex;
            justify-content: flex-end;
        }

        .slideover {
            width: 440px;
            max-width: 100%;
            background: var(--bg-card);
            height: 100%;
            overflow-y: auto;
            box-shadow: -4px 0 24px rgba(0, 0, 0, .12);
            display: flex;
            flex-direction: column;
        }

        .slideover-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .slideover-title {
            font-size: 15px;
            font-weight: 700;
        }

        .close-btn {
            width: 32px;
            height: 32px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 14px;
            transition: background .15s;
        }

        .close-btn:hover {
            background: var(--bg-main);
        }

        .slideover-body {
            padding: 24px;
            flex: 1;
        }

        .slideover-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            flex-shrink: 0;
        }

        /* ─── FORM ─── */
        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        .form-input,
        .form-select {
            width: 100%;
            height: 38px;
            padding: 0 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 13px;
            color: var(--text-primary);
            background: var(--bg-card);
            outline: none;
            transition: border-color .15s;
            font-family: inherit;
        }

        .form-input:focus,
        .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
        }

        /* ─── SEARCH/FILTER BAR ─── */
        .filter-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
        }

        .filter-input {
            height: 34px;
            padding: 0 10px 0 32px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 13px;
            width: 240px;
            background: var(--bg-main);
            outline: none;
            font-family: inherit;
            transition: border-color .15s, background .15s;
        }

        .filter-input:focus {
            border-color: var(--accent);
            background: #fff;
        }

        .filter-input-wrap {
            position: relative;
        }

        .filter-input-wrap i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 12px;
            pointer-events: none;
        }

        .filter-select {
            height: 34px;
            padding: 0 10px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            font-size: 13px;
            background: var(--bg-main);
            color: var(--text-primary);
            outline: none;
            cursor: pointer;
            font-family: inherit;
        }

        /* ─── PAGINATION ─── */
        .pagination {
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 14px 20px;
            justify-content: flex-end;
            border-top: 1px solid var(--border);
        }

        .pagination .page-info {
            font-size: 12px;
            color: var(--text-muted);
            margin-right: 8px;
        }

        .page-btn {
            height: 30px;
            min-width: 30px;
            padding: 0 8px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--bg-card);
            font-size: 12px;
            color: var(--text-secondary);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: background .15s, border-color .15s;
        }

        .page-btn:hover {
            background: var(--bg-main);
        }

        .page-btn.active {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        .page-btn:disabled,
        .page-btn.disabled {
            opacity: .4;
            pointer-events: none;
        }

        /* ─── ACTIVITY FEED ─── */
        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border-light);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .activity-content {
            flex: 1;
            min-width: 0;
        }

        .activity-text {
            font-size: 13px;
            color: var(--text-primary);
            line-height: 1.4;
        }

        .activity-text strong {
            font-weight: 600;
        }

        .activity-time {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 3px;
        }

        /* ─── QUICK ACTIONS ─── */
        .quick-action {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            cursor: pointer;
            text-decoration: none;
            transition: border-color .15s, background .15s;
            background: var(--bg-card);
        }

        .quick-action:hover {
            border-color: var(--accent);
            background: var(--accent-light);
        }

        .quick-action .qa-icon {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-sm);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .quick-action .qa-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .quick-action .qa-desc {
            font-size: 11px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        /* ─── ALERT ─── */
        .alert {
            padding: 12px 16px;
            border-radius: var(--radius);
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: var(--green-bg);
            color: var(--green);
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: var(--red-bg);
            color: var(--red);
            border: 1px solid #fecaca;
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .topbar {
                left: 0;
            }

            .main-wrap {
                margin-left: 0;
            }

            .mobile-menu-btn {
                display: flex;
            }

            .stat-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .search-input {
                width: 160px;
            }
        }

        @media (max-width: 640px) {
            .stat-grid {
                grid-template-columns: 1fr;
            }

            .page-content {
                padding: 16px;
            }

            .topbar {
                padding: 0 16px;
            }
        }
    </style>

    @stack('styles')
</head>

<body x-data="{
    sidebarOpen: false,
    editModal: false,
    editUser: {},
    confirmModal: false,
    confirmAction: null,
    openEdit(user) { this.editUser = JSON.parse(JSON.stringify(user)); this.editModal = true; },
    closeEdit() { this.editModal = false; },
    confirmDo(action) { this.confirmAction = action; this.confirmModal = true; },
}">

    {{-- ─── MOBILE OVERLAY ─── --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
        style="position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:45;" x-transition.opacity></div>
    {{-- ─── SIDEBAR ─── --}}
    <aside class="sidebar" :class="sidebarOpen ? 'open' : ''">
        {{-- Logo --}}
        <div class="sidebar-logo">
            <div class="logo-icon" style="background: white; padding: 4px;">
                <img src="/assets/img/logo.png?v=1.1" alt="S" style="width: 100%; height: 100%; object-contain;">
            </div>
            <span class="logo-text">ServiceRDC</span>
            <span class="logo-badge">Admin</span>
        </div>

        {{-- Nav --}}
        <nav class="sidebar-nav">

            {{-- Overview --}}
            <div class="nav-section">
                <div class="nav-section-label">Overview</div>
                <a href="{{ route('super-admin.dashboard') }}"
                    class="nav-item {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-grid-2"></i> Dashboard
                </a>
            </div>

            {{-- User Management --}}
            <div class="nav-section">
                <div class="nav-section-label">User Management</div>
                <a href="{{ route('super-admin.users.index') }}"
                    class="nav-item {{ request()->routeIs('super-admin.users.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-users"></i> Users
                </a>
                <a href="{{ route('super-admin.roles') }}"
                    class="nav-item {{ request()->routeIs('super-admin.roles*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-shield-halved"></i> Roles & Permissions
                </a>
                <a href="{{ route('super-admin.sessions') }}"
                    class="nav-item {{ request()->routeIs('super-admin.sessions') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-clock-rotate-left"></i> Sessions
                </a>
            </div>

            {{-- Platform --}}
            <div class="nav-section">
                <div class="nav-section-label">Platform</div>
                <a href="{{ route('super-admin.services.index') }}"
                    class="nav-item {{ request()->routeIs('super-admin.services.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-briefcase"></i> Services
                </a>
                <a href="{{ route('super-admin.organizations.index') }}"
                    class="nav-item {{ request()->routeIs('super-admin.organizations.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-building"></i> Organizations
                </a>
                <a href="{{ route('super-admin.plans.index') }}"
                    class="nav-item {{ request()->routeIs('super-admin.plans.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-layer-group"></i> Plans & Features
                </a>

            </div>

            {{-- Finance --}}
            <div class="nav-section">
                <div class="nav-section-label">Finance</div>
                <a href="{{ route('super-admin.billing.index') }}"
                    class="nav-item {{ request()->routeIs('super-admin.billing.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-credit-card"></i> Overview
                </a>
                <a href="{{ route('super-admin.billing.transactions') }}"
                    class="nav-item {{ request()->routeIs('super-admin.billing.transactions') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-arrow-right-arrow-left"></i> Transactions
                </a>
                <a href="{{ route('super-admin.billing.payouts') }}"
                    class="nav-item {{ request()->routeIs('super-admin.billing.payouts') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-money-bill-transfer"></i> Payouts
                </a>
            </div>

            {{-- System --}}
            <div class="nav-section">
                <div class="nav-section-label">System</div>
                <a href="{{ route('super-admin.system.settings.index') }}"
                    class="nav-item {{ request()->routeIs('super-admin.system.settings.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-gear"></i> Settings
                </a>
                <a href="{{ route('super-admin.logs') }}"
                    class="nav-item {{ request()->routeIs('super-admin.logs') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-file-shield"></i> Audit Trail
                    <span class="nav-badge" style="background:var(--accent);color:white;">Live</span>
                </a>
                <a href="{{ route('super-admin.system.api-keys.index') }}"
                    class="nav-item {{ request()->routeIs('super-admin.system.api-keys.*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-key"></i> API Keys
                </a>
                <a href="{{ route('super-admin.system.health') }}"
                    class="nav-item {{ request()->routeIs('super-admin.system.health') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-heart-pulse"></i> System Health
                </a>
            </div>
        </nav>

        {{-- Footer --}}
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-role">Super Admin</div>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-arrow-right-from-bracket"
                        style="font-size:13px;width:16px;text-align:center;color:#64748b;"></i>
                    Sign out
                </button>
            </form>
        </div>
    </aside>

    {{-- ─── TOPBAR ─── --}}
    <header class="topbar">
        <div class="topbar-left">
            <button class="mobile-menu-btn" @click="sidebarOpen = !sidebarOpen">
                <i class="fas fa-bars"></i>
            </button>
            <div class="topbar-breadcrumb">
                <span>ServiceRDC</span> / @yield('page_title', 'Dashboard')
            </div>
        </div>
        <div class="topbar-right">
            <div class="search-wrap" style="display:none;" id="search-wrap">
                <i class="fas fa-magnifying-glass search-icon"></i>
                <input type="text" placeholder="Search..." class="search-input">
            </div>
            <button class="icon-btn" title="Search"
                onclick="document.getElementById('search-wrap').style.display='block';this.style.display='none';">
                <i class="fas fa-magnifying-glass"></i>
            </button>
            <a href="#" class="icon-btn" title="Notifications">
                <i class="fas fa-bell"></i>
                <span class="notif-dot"></span>
            </a>
            <div x-data="{ open: false }" @click.outside="open = false" style="position:relative;">
                <button class="user-menu-btn" @click="open = !open">
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                    <span class="name"
                        style="display:none;max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ auth()->user()->name }}</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div x-show="open" x-cloak x-transition class="action-dropdown"
                    style="min-width:180px;right:0;top:calc(100% + 8px);">
                    <div class="action-item"
                        style="border-bottom:1px solid var(--border);padding-bottom:10px;margin-bottom:4px;">
                        <div>
                            <div style="font-weight:600;font-size:13px;">{{ auth()->user()->name }}</div>
                            <div style="font-size:11px;color:var(--text-muted);">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <a href="#" class="action-item"><i class="fas fa-user"></i> Profile</a>
                    <a href="#" class="action-item"><i class="fas fa-gear"></i> Settings</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="action-item danger"
                            style="width:100%;background:none;border:none;font-family:inherit;cursor:pointer;">
                            <i class="fas fa-arrow-right-from-bracket"></i> Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- ─── MAIN ─── --}}
    <div class="main-wrap">
        <main class="page-content">
            @if(session('success'))
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error"><i class="fas fa-triangle-exclamation"></i> {{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>

    {{-- ─── EDIT USER SLIDE-OVER ─── --}}
    <div x-show="editModal" x-cloak @keydown.escape.window="closeEdit()" style="position:fixed;inset:0;z-index:200;"
        x-transition.opacity>
        <div class="overlay" @click.self="closeEdit()">
            <div class="slideover" @click.stop>
                <div class="slideover-header">
                    <span class="slideover-title">Edit User</span>
                    <button class="close-btn" @click="closeEdit()"><i class="fas fa-xmark"></i></button>
                </div>
                <div class="slideover-body">
                    <form method="POST" :action="'/super-admin/users/' + editUser.id + '/promote'">
                        @csrf
                        @method('POST')
                        <div
                            style="display:flex;align-items:center;gap:14px;margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid var(--border);">
                            <div class="user-avatar" style="width:48px;height:48px;font-size:16px;"
                                x-text="editUser.name ? editUser.name.charAt(0).toUpperCase() : '?'"></div>
                            <div>
                                <div style="font-weight:700;font-size:15px;" x-text="editUser.name"></div>
                                <div style="font-size:12px;color:var(--text-muted);" x-text="editUser.email"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-input" :value="editUser.name" readonly
                                style="background:var(--bg-main);color:var(--text-muted);">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-input" :value="editUser.email" readonly
                                style="background:var(--bg-main);color:var(--text-muted);">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" x-model="editUser.role">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                        <div class="slideover-footer" style="padding:0;margin-top:8px;border:none;">
                            <button type="button" class="btn btn-secondary" @click="closeEdit()">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>

</html>