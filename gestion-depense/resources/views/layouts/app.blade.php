<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gestion de Dépenses')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.min.js"></script>
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #f1f5f9;
            --accent: #10b981;
            --danger: #ef4444;
            --sidebar-width: 240px;
            --topbar-height: 62px;
        }

        * { font-family: 'Inter', sans-serif; }
        body { background: #f8fafc; min-height: 100vh; }

        /* ═══════════════ SIDEBAR ═══════════════ */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(160deg, #1e1b4b 0%, #312e81 100%);
            padding: 1.5rem 1rem;
            z-index: 1000;
            box-shadow: 4px 0 24px rgba(99,102,241,.15);
            overflow-y: auto;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: .75rem;
            color: #fff;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            padding: .5rem .75rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.12);
            margin-bottom: 1rem;
        }

        .sidebar-brand .brand-icon {
            width: 36px; height: 36px;
            background: var(--primary);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: .65rem;
            color: rgba(255,255,255,.65);
            padding: .6rem .85rem;
            border-radius: 10px;
            font-size: .9rem;
            font-weight: 500;
            text-decoration: none;
            transition: all .2s;
            margin-bottom: .25rem;
        }

        .nav-link-custom:hover,
        .nav-link-custom.active {
            background: rgba(255,255,255,.12);
            color: #fff;
        }

        .nav-link-custom i { font-size: 1rem; }

        /* ═══════════════ TOPBAR ═══════════════ */
        .topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--topbar-height);
            background: #fff;
            border-bottom: 1px solid #f1f5f9;
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
            z-index: 999;
            transition: left .3s;
        }

        .topbar-inner {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            gap: 1rem;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .hamburger {
            background: none;
            border: none;
            padding: .4rem .5rem;
            border-radius: 8px;
            font-size: 1.3rem;
            color: #475569;
            cursor: pointer;
            line-height: 1;
            transition: background .2s, color .2s;
        }

        .hamburger:hover { background: #f1f5f9; color: #1e293b; }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: .85rem;
        }

        .topbar-greeting { font-size: .85rem; color: #64748b; }
        .topbar-divider { width: 1px; height: 24px; background: #e2e8f0; }

        .user-avatar-btn {
            background: none;
            border: none;
            display: flex;
            align-items: center;
            gap: .5rem;
            cursor: pointer;
            padding: .35rem .6rem;
            border-radius: 50px;
            border: 1.5px solid #e2e8f0;
            transition: all .2s;
        }

        .user-avatar-btn:hover,
        .user-avatar-btn[aria-expanded="true"] {
            background: #f8fafc;
            border-color: #c7d2fe;
        }

        .user-avatar-btn::after { display: none; }

        .avatar-circle {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #fff;
            font-size: .82rem;
            font-weight: 700;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .avatar-name {
            font-size: .875rem;
            font-weight: 600;
            color: #334155;
            max-width: 130px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .avatar-caret { font-size: .65rem; color: #94a3b8; transition: transform .2s; }
        .user-avatar-btn[aria-expanded="true"] .avatar-caret { transform: rotate(180deg); }

        .user-dropdown {
            min-width: 230px;
            border: none;
            border-radius: 14px;
            box-shadow: 0 10px 40px rgba(0,0,0,.12);
            padding: .5rem;
            margin-top: .35rem;
        }

        .dropdown-header-user {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .65rem .75rem;
        }

        .dropdown-avatar-lg {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: #fff;
            font-size: .95rem;
            font-weight: 700;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .dropdown-user-name { font-weight: 700; font-size: .875rem; color: #1e293b; }
        .dropdown-user-email { font-size: .75rem; color: #94a3b8; }

        .dropdown-item-custom {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: .875rem;
            font-weight: 500;
            color: #475569;
            padding: .55rem .75rem;
            border-radius: 9px;
            transition: background .15s, color .15s;
        }

        .dropdown-item-custom:hover { background: #f1f5f9; color: #1e293b; }
        .dropdown-item-custom i { font-size: .95rem; color: #6366f1; }

        .dropdown-item-danger { color: #ef4444 !important; }
        .dropdown-item-danger i { color: #ef4444 !important; }
        .dropdown-item-danger:hover { background: #fef2f2 !important; color: #dc2626 !important; }

        /* ═══════════════ MAIN CONTENT ═══════════════ */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: calc(var(--topbar-height) + 1.5rem) 2rem 2rem;
            min-height: 100vh;
        }

        /* ═══════════════ PAGE HEADER ═══════════════ */
        .page-header {
            background: #fff;
            border-radius: 16px;
            padding: 1.4rem 2rem;
            margin-bottom: 1.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .75rem;
        }

        .page-header h1 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1e1b4b;
            margin: 0;
        }

        .badge-count {
            background: var(--primary);
            color: #fff;
            font-size: .75rem;
            font-weight: 600;
            padding: .3rem .7rem;
            border-radius: 20px;
        }

        /* ═══════════════ CARDS ═══════════════ */
        .card-custom {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            border: none;
            overflow: hidden;
        }

        .card-header-custom {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .card-header-custom h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1rem;
            color: #1e293b;
        }

        .card-header-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: .9rem;
        }

        /* ═══════════════ STAT CARDS ═══════════════ */
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 1.4rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            display: flex;
            align-items: center;
            gap: 1rem;
            border-left: 4px solid transparent;
        }

        .stat-card.card-total   { border-left-color: var(--primary); }
        .stat-card.card-count   { border-left-color: var(--accent); }
        .stat-card.card-avg     { border-left-color: #f59e0b; }
        .stat-card.card-revenue { border-left-color: var(--accent); }
        .stat-card.card-expense { border-left-color: var(--danger); }
        .stat-card.card-balance { border-left-color: var(--primary); }

        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .stat-label { font-size: .78rem; font-weight: 500; color: #94a3b8; text-transform: uppercase; letter-spacing: .05em; }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: #1e293b; line-height: 1.2; }

        /* ═══════════════ TABLE CARD ═══════════════ */
        .table-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            overflow: hidden;
        }

        .table-card-header {
            padding: 1.1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .table-card-header h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1rem;
            color: #1e293b;
        }

        .data-table { margin: 0; }

        .data-table thead th {
            background: #f8fafc;
            font-size: .78rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #64748b;
            padding: .9rem 1.25rem;
            border: none;
            border-bottom: 1px solid #f1f5f9;
            white-space: nowrap;
        }

        .data-table tbody td {
            padding: .95rem 1.25rem;
            vertical-align: middle;
            font-size: .9rem;
            color: #334155;
            border-color: #f1f5f9;
        }

        .data-table tbody tr { transition: background .15s; }
        .data-table tbody tr:hover { background: #fafafa; }

        /* ═══════════════ BADGES ═══════════════ */
        .cat-badge {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            background: #ede9fe;
            color: #5b21b6;
            font-size: .78rem;
            font-weight: 600;
            padding: .3rem .75rem;
            border-radius: 50px;
        }

        .id-chip {
            background: #f1f5f9;
            color: #64748b;
            font-size: .75rem;
            font-weight: 600;
            padding: .25rem .6rem;
            border-radius: 6px;
        }

        .amount-cell { font-weight: 700; color: #1e293b; white-space: nowrap; }
        .amount-cell.text-success { color: #16a34a !important; }
        .amount-currency { font-size: .75rem; font-weight: 500; color: #94a3b8; margin-left: .2rem; }

        .date-pill { font-size: .8rem; color: #64748b; white-space: nowrap; }
        .date-pill i { color: #94a3b8; }

        /* ═══════════════ BUTTONS ═══════════════ */
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 10px;
            padding: .65rem 1.5rem;
            font-weight: 600;
            font-size: .9rem;
            color: #fff;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
        }

        .btn-primary-custom:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99,102,241,.35);
            color: #fff;
        }

        .btn-add {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 10px;
            padding: .6rem 1.25rem;
            font-weight: 600;
            font-size: .875rem;
            color: #fff;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            text-decoration: none;
        }

        .btn-add:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99,102,241,.35);
            color: #fff;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            color: #64748b;
            font-size: .875rem;
            font-weight: 500;
            text-decoration: none;
            padding: .5rem 1rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            transition: all .2s;
        }

        .btn-back:hover { background: #f1f5f9; color: #334155; }

        .btn-delete {
            background: #fff5f5;
            color: var(--danger);
            border: 1.5px solid #fecaca;
            border-radius: 8px;
            padding: .35rem .75rem;
            font-size: .8rem;
            font-weight: 600;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: .3rem;
        }

        .btn-delete:hover {
            background: var(--danger);
            color: #fff;
            border-color: var(--danger);
            box-shadow: 0 4px 12px rgba(239,68,68,.3);
        }

        /* ═══════════════ FORM CARD ═══════════════ */
        .form-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06);
            overflow: hidden;
            max-width: 700px;
            margin: 0 auto;
        }

        .form-card-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 1.8rem 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .form-card-icon {
            width: 52px; height: 52px;
            background: rgba(255,255,255,.18);
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            color: #fff;
            flex-shrink: 0;
        }

        .form-card-header h2 { color: #fff; font-size: 1.2rem; font-weight: 700; margin: 0; }
        .form-card-header p { color: rgba(255,255,255,.75); font-size: .85rem; margin: .2rem 0 0; }

        .form-body { padding: 2rem; }

        .field-group { margin-bottom: 1.4rem; }

        .field-label {
            display: flex;
            align-items: center;
            gap: .4rem;
            font-size: .83rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: .45rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .field-label i { color: var(--primary); font-size: .9rem; }
        .required-star { color: #ef4444; }

        .form-label { font-weight: 500; font-size: .85rem; color: #475569; }

        .form-control, .form-select {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: .75rem 1rem;
            font-size: .92rem;
            color: #1e293b;
            transition: border-color .2s, box-shadow .2s, background .2s;
            background: #fafafa;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
            background: #fff;
            outline: none;
        }

        .form-control.is-invalid, .form-select.is-invalid { border-color: #ef4444; background-image: none; }
        .form-control.is-invalid:focus, .form-select.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239,68,68,.15); }

        .invalid-feedback { font-size: .8rem; font-weight: 500; margin-top: .35rem; }

        .field-hint {
            font-size: .77rem;
            color: #94a3b8;
            margin-top: .35rem;
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        .amount-wrapper { position: relative; }
        .amount-wrapper .form-control { padding-right: 3.5rem; }
        .amount-suffix {
            position: absolute;
            right: 1rem; top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: .85rem;
            font-weight: 600;
            pointer-events: none;
        }

        .amount-preview { display: none; margin-top: .4rem; font-size: .85rem; font-weight: 600; color: var(--primary); }

        .form-divider { border: none; border-top: 1.5px dashed #e2e8f0; margin: 1.5rem 0; }

        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 12px;
            padding: .85rem 1.5rem;
            font-weight: 700;
            font-size: 1rem;
            color: #fff;
            transition: all .2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            cursor: pointer;
        }

        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(99,102,241,.35); }
        .btn-submit:active { transform: translateY(0); }

        /* ═══════════════ MISC ═══════════════ */
        .empty-state { text-align: center; padding: 3rem 1rem; color: #94a3b8; }
        .empty-state i { font-size: 2.5rem; margin-bottom: .75rem; display: block; }

        .chart-card { background: #fff; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,.06); overflow: hidden; }
        .chart-card-header { padding: 1.1rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
        .chart-card-header h5 { margin: 0; font-weight: 600; font-size: 1rem; color: #1e293b; }
        .chart-body { padding: 1.4rem 1.5rem; position: relative; }
        .chart-empty { text-align: center; padding: 3rem 1rem; color: #94a3b8; }
        .chart-empty i { font-size: 2.5rem; display: block; margin-bottom: .5rem; }

        .modal-content { border: none; border-radius: 16px; }
        .modal-header  { border-bottom: 1px solid #f1f5f9; padding: 1.25rem 1.5rem; }
        .modal-footer  { border-top: 1px solid #f1f5f9; }

        .btn-danger-custom {
            background: var(--danger); border: none; border-radius: 9px;
            padding: .6rem 1.25rem; font-weight: 600; color: #fff; transition: all .2s;
        }
        .btn-danger-custom:hover { background: #dc2626; box-shadow: 0 4px 12px rgba(239,68,68,.35); }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .alert-custom { border: none; border-radius: 12px; font-size: .875rem; font-weight: 500; padding: .85rem 1.25rem; animation: slideDown .3s ease; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .table-row-animate { animation: fadeIn .25s ease; }

        /* Category pill list */
        .category-list { list-style: none; padding: 0; margin: 0; display: flex; flex-wrap: wrap; gap: .6rem; }
        .category-pill {
            display: inline-flex; align-items: center; gap: .45rem;
            background: #f1f5f9; color: #334155; font-size: .875rem; font-weight: 500;
            padding: .45rem 1rem; border-radius: 50px; border: 1.5px solid #e2e8f0; transition: all .2s;
        }
        .category-pill:hover { background: #ede9fe; border-color: #c4b5fd; color: var(--primary-dark); }
        .category-num {
            background: var(--primary); color: #fff; font-size: .7rem; font-weight: 700;
            width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
        }

        /* Sortable headers */
        .sort-th { cursor: pointer; user-select: none; white-space: nowrap; }
        .sort-th:hover { background: #f1f5f9 !important; }
        .sort-th.active { background: #ede9fe !important; color: var(--primary-dark) !important; }
        .sort-icon { display: inline-flex; flex-direction: column; align-items: center; margin-left: .3rem; gap: 1px; vertical-align: middle; opacity: .35; transition: opacity .2s; }
        .sort-icon .ci-up, .sort-icon .ci-down { font-size: .6rem; line-height: 1; }
        .sort-th.active .sort-icon { opacity: 1; }
        .sort-th.sort-asc .sort-icon .ci-up   { color: var(--primary); opacity: 1; }
        .sort-th.sort-asc .sort-icon .ci-down  { opacity: .25; }
        .sort-th.sort-desc .sort-icon .ci-down { color: var(--primary); opacity: 1; }
        .sort-th.sort-desc .sort-icon .ci-up   { opacity: .25; }

        /* Desc truncate */
        .desc-cell { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* Filter bar */
        .filter-bar { background: #fff; border-radius: 14px; padding: 1rem 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,.06); margin-bottom: 1.25rem; }
        .filter-bar .form-control, .filter-bar .form-select { border: 1.5px solid #e2e8f0; border-radius: 10px; font-size: .875rem; padding: .55rem .9rem; }
        .filter-bar .form-control:focus, .filter-bar .form-select:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,.15); }

        /* Legend list */
        .legend-list { list-style: none; padding: 0; margin: 0; }
        .legend-item { display: flex; align-items: center; justify-content: space-between; padding: .5rem .6rem; border-radius: 10px; margin-bottom: .35rem; font-size: .84rem; transition: background .15s; }
        .legend-item:hover { background: #f8fafc; }
        .legend-item.top-1 { background: #fff7ed; border-left: 3px solid #f97316; }
        .legend-item.top-2 { background: #fefce8; border-left: 3px solid #eab308; }
        .legend-item.top-3 { background: #f0fdf4; border-left: 3px solid #22c55e; }
        .legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; margin-right: .5rem; }
        .legend-name { flex: 1; font-weight: 500; color: #334155; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-right: .5rem; }
        .legend-amount { font-weight: 700; color: #1e293b; white-space: nowrap; font-size: .82rem; }
        .high-badge { font-size: .65rem; font-weight: 700; background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; padding: .15rem .45rem; border-radius: 20px; margin-left: .4rem; }

        /* Mobile sidebar overlay */
        .sidebar-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 998; }
        .sidebar-overlay.active { display: block; }

        @media (max-width: 991.98px) {
            .topbar { left: 0; }
            .sidebar { transform: translateX(-100%); transition: transform .3s; }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0 !important; padding: calc(var(--topbar-height) + 1rem) 0.75rem 1rem; }
            .page-header { padding: 1rem 1.25rem; border-radius: 0; margin: -1rem -0.75rem 1.25rem; }
            .page-header h1 { font-size: 1.1rem; }
            .stat-card { padding: 1.25rem; }
            .stat-value { font-size: 1.25rem; }
            .form-card { border-radius: 0; margin: -1rem -0.75rem; max-width: none; }
            .table-card { border-radius: 12px; }
            .btn-add { padding: 0.5rem 0.75rem; font-size: 0.8rem; }
        }

        /* Utility for responsive tables */
        .table-responsive-custom {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 12px;
        }

        .select-hint { display: flex; align-items: center; gap: .35rem; font-size: .77rem; color: #94a3b8; margin-top: .35rem; }
        textarea.form-control { resize: vertical; min-height: 90px; }
    </style>
    @stack('styles')
</head>
<body x-data="{ sidebarOpen: false }" :class="{ 'overflow-hidden': sidebarOpen }">

@auth
    @include('includes.sidebar')
    @include('includes.header')

    <main class="main-content">
        @yield('content')
    </main>
@else
    <main>
        @yield('content')
    </main>
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Global touch-friendly table wrapper
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('table:not(.no-resp)').forEach(table => {
            if (!table.parentElement.classList.contains('table-responsive')) {
                const wrapper = document.createElement('div');
                wrapper.className = 'table-responsive-custom';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }
        });
    });

    // Global Money Input Formatter
    document.addEventListener('input', function(e) {
        if (e.target && e.target.classList.contains('money-input')) {
            let rawValue = e.target.value.replace(/\s+/g, '').replace(/[^0-9.]/g, '');
            if (!rawValue) {
                e.target.value = '';
                return;
            }
            let parts = rawValue.split('.');
            parts[0] = parseInt(parts[0], 10).toLocaleString('fr-FR').replace(/ /g, ' ');
            if (parts.length > 1) {
                e.target.value = parts[0] + '.' + parts[1].substring(0, 2);
            } else {
                e.target.value = parts[0];
            }
        }
    });
</script>
@stack('scripts')
</body>
</html>
