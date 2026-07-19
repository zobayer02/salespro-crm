<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SalesPro' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <script>
        const savedSidebarState = localStorage.getItem('salespro-sidebar-collapsed');
        if (savedSidebarState === '1' || (savedSidebarState === null && window.matchMedia('(max-width: 900px)').matches)) {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    </script>
    <style>
        * { box-sizing: border-box; }
        :root {
            --font-primary: "Plus Jakarta Sans", "Inter", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            --font-secondary: "Inter", ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: var(--font-primary);
            color: #07132d;
            background: #f7fbff;
        }
        button, input, select { font: inherit; }
        .app { display: grid; grid-template-columns: 248px 1fr; min-height: 100vh; border: 1px solid #d5deeb; background: #fff; }
        .sidebar { background: linear-gradient(180deg, #061634 0%, #061a3f 48%, #082957 100%); color: #eaf3ff; padding: 28px 16px 24px; }
        .brand { display: flex; align-items: center; gap: 12px; padding: 0 8px 24px; }
        .brand-mark { width: 48px; height: 48px; display: grid; place-items: center; border-radius: 10px; color: #c7d2fe; background: rgba(99, 102, 241, .18); box-shadow: inset 0 1px 0 rgba(255, 255, 255, .08); }
        .brand-mark img { width: 100%; height: 100%; object-fit: contain; display: block; }
        .brand-name { font-size: 20px; font-weight: 900; line-height: 1.1; }
        .brand-name span { display: block; margin-top: 4px; font-size: 13px; font-weight: 700; color: #dbeafe; }
        .nav-title { margin: 20px 12px 12px; color: #8ea4c8; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .02em; }
        .nav-item { display: flex; align-items: center; gap: 12px; min-height: 44px; padding: 11px 12px; border-radius: 8px; color: #f4f8ff; text-decoration: none; font-size: 15px; font-weight: 750; }
        .nav-item svg { width: 20px; height: 20px; stroke-width: 2; }
        .nav-item.active { background: linear-gradient(135deg, #4f46e5, #6366f1); box-shadow: 0 14px 28px rgba(79, 70, 229, .26); }
        .nav-item:hover { background: rgba(125, 211, 252, .12); }
        .sidebar-logout { width: 100%; border: 0; cursor: pointer; text-align: left; background: transparent; font: inherit; }
        .main { min-width: 0; background: #fbfdff; }
        .topbar { position: relative; z-index: 110; min-height: 80px; display: flex; align-items: center; justify-content: flex-end; gap: 20px; padding: 0 32px; border-bottom: 1px solid #e3ebf6; background: rgba(255, 255, 255, .92); }
        h1 { margin: 0; font-family: var(--font-primary); font-size: 27px; font-weight: 800; line-height: 1; letter-spacing: 0; }
        .top-actions { display: flex; align-items: center; gap: 18px; }
        .profile-menu { position: relative; display: flex; align-items: center; gap: 10px; }
        .date-filter { display: inline-flex; align-items: center; gap: 10px; min-height: 38px; padding: 0 14px; border: 1px solid #d9e2ef; border-radius: 8px; color: #12213d; background: #fff; font-size: 13px; font-weight: 800; white-space: nowrap; }
        .icon-button { position: relative; width: 38px; height: 38px; display: grid; place-items: center; border: 0; border-radius: 10px; color: #344767; background: transparent; cursor: pointer; }
        .badge { position: absolute; top: 2px; right: 2px; width: 18px; height: 18px; display: grid; place-items: center; border-radius: 999px; color: #fff; background: #ef4444; font-size: 10px; font-weight: 900; }
        .profile { display: flex; align-items: center; gap: 12px; color: #0c1833; }
        .avatar { width: 42px; height: 42px; display: grid; place-items: center; border-radius: 50%; color: #fff; font-weight: 900; background: linear-gradient(135deg, #075985, #38bdf8); box-shadow: inset 0 0 0 3px rgba(255, 255, 255, .9); }
        .profile strong { display: block; font-size: 14px; }
        .profile span { display: block; margin-top: 2px; color: #64748b; font-size: 12px; font-weight: 700; }
        .profile-caret { width: 34px; height: 34px; display: grid; place-items: center; border-radius: 8px; color: #334155; cursor: pointer; }
        .profile-dropdown { position: absolute; top: calc(100% + 12px); right: 0; z-index: 40; width: 190px; border: 1px solid #dbe5f2; border-radius: 18px; padding: 8px; background: rgba(255, 255, 255, .98); box-shadow: 0 22px 52px rgba(15, 23, 42, .14); opacity: 0; pointer-events: none; transform: translateY(-8px) scale(.98); transition: opacity .16s ease, transform .16s ease; }
        .profile-menu.open .profile-dropdown { opacity: 1; pointer-events: auto; transform: translateY(0) scale(1); }
        .profile-dropdown a,
        .profile-dropdown button { width: 100%; min-height: 42px; display: flex; align-items: center; gap: 10px; border: 0; border-radius: 12px; padding: 0 12px; color: #172033; background: transparent; text-decoration: none; font: inherit; font-size: 13px; font-weight: 900; cursor: pointer; }
        .profile-dropdown a:hover,
        .profile-dropdown button:hover { background: #f1f5ff; color: #4f46e5; }
        .profile-dropdown form button:hover { background: #fff1f2; color: #dc2626; }
        .profile-dropdown svg { width: 18px; height: 18px; stroke-width: 2; }
        .mobile-sidebar-backdrop { display: none; }
        .content { padding: 24px 32px 34px; }
        .cards { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 18px; margin-bottom: 18px; }
        .card, .panel { border: 1px solid #dde7f4; border-radius: 8px; background: #fff; box-shadow: 0 14px 38px rgba(15, 23, 42, .035); }
        a.card { color: inherit; text-decoration: none; transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease; }
        a.card:hover { transform: translateY(-2px); border-color: #93c5fd; box-shadow: 0 18px 44px rgba(37, 99, 235, .1); }
        .stat-card { min-height: 124px; display: flex; align-items: center; gap: 20px; padding: 24px; }
        .stat-icon { flex: 0 0 auto; width: 64px; height: 64px; display: grid; place-items: center; border-radius: 14px; }
        .stat-icon svg { width: 34px; height: 34px; }
        .purple { color: #4f46e5; background: linear-gradient(135deg, #eef2ff, #e0e7ff); }
        .green { color: #16a34a; background: linear-gradient(135deg, #ecfdf5, #dcfce7); }
        .orange { color: #f59e0b; background: linear-gradient(135deg, #fff7ed, #ffedd5); }
        .blue { color: #2563eb; background: linear-gradient(135deg, #eff6ff, #dbeafe); }
        .red { color: #ef4444; background: linear-gradient(135deg, #fff1f2, #fee2e2); }
        .stat-body span { color: #41516c; font-size: 13px; font-weight: 800; }
        .stat-body strong { display: block; margin-top: 8px; color: #07132d; font-size: 26px; line-height: 1.1; }
        .stat-body small { display: block; margin-top: 10px; color: #059669; font-size: 13px; font-weight: 900; }
        .stat-body small.warning { color: #f97316; }
        .analytics-grid { display: grid; grid-template-columns: minmax(420px, 1.35fr) minmax(310px, .72fr) minmax(330px, .82fr); gap: 18px; margin-bottom: 18px; }
        .bottom-grid { display: grid; grid-template-columns: 1.1fr 1fr; gap: 18px; }
        .panel { padding: 20px; }
        .panel-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 18px; }
        h2 { margin: 0; color: #07132d; font-size: 17px; letter-spacing: 0; }
        .filter-button { min-height: 32px; display: inline-flex; align-items: center; gap: 8px; border: 1px solid #d9e2ef; border-radius: 7px; padding: 0 13px; color: #21314f; background: #fff; font-size: 12px; font-weight: 850; }
        a.filter-button { text-decoration: none; }
        .chart { height: 256px; }
        .sales-chart { position: relative; }
        .chart svg { width: 100%; height: 100%; overflow: visible; }
        .axis-label { fill: #6b7a99; font-size: 12px; font-weight: 700; }
        .sales-chart-line {
            transition: stroke-width .16s ease, filter .16s ease;
        }
        .sales-chart-line-hit {
            fill: none;
            stroke: transparent;
            stroke-width: 18;
            stroke-linecap: round;
            stroke-linejoin: round;
            cursor: pointer;
            pointer-events: stroke;
        }
        .sales-chart.is-line-hovering .sales-chart-line {
            stroke-width: 5;
            filter: drop-shadow(0 8px 12px rgba(79, 70, 229, .2));
        }
        .sales-chart-tooltip {
            position: absolute;
            z-index: 4;
            min-width: 136px;
            border: 1px solid #dce5f2;
            border-radius: 18px;
            padding: 11px 14px;
            color: #12182b;
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 18px 42px rgba(30, 41, 59, .15);
            opacity: 0;
            pointer-events: none;
            text-align: center;
            transform: translate(-50%, calc(-100% - 14px)) scale(.96);
            transition: opacity .16s ease, transform .16s ease;
        }
        .sales-chart-tooltip.active {
            opacity: 1;
            transform: translate(-50%, calc(-100% - 14px)) scale(1);
        }
        .sales-chart-tooltip strong {
            display: block;
            color: #12182b;
            font-size: 13px;
            font-weight: 950;
        }
        .sales-chart-tooltip span {
            display: block;
            margin-top: 4px;
            color: #64748b;
            font-size: 13px;
            font-weight: 850;
            white-space: nowrap;
        }
        .branch-layout { display: grid; grid-template-columns: 168px 1fr; align-items: center; gap: 20px; min-height: 248px; }
        .donut { width: 150px; aspect-ratio: 1; border-radius: 50%; background: conic-gradient(#4049a3 0 40%, #ff7a2f 40% 50%, #c6be58 50% 70%, #4b85e9 70% 100%); display: grid; place-items: center; }
        .donut::after { content: ""; width: 78px; height: 78px; border-radius: 50%; background: #fff; }
        .legend { display: grid; gap: 15px; }
        .legend-item { display: grid; grid-template-columns: 12px 1fr; gap: 9px; color: #4a5873; font-size: 13px; line-height: 1.35; }
        .dot { width: 10px; height: 10px; margin-top: 3px; border-radius: 999px; }
        .legend-item strong { display: block; color: #26334f; font-size: 13px; }
        .product-list { display: grid; gap: 16px; }
        .product-row { display: grid; grid-template-columns: 24px 46px 1fr 54px; align-items: center; gap: 14px; }
        .rank { color: #07132d; font-weight: 900; text-align: center; }
        .product-img { width: 36px; height: 42px; display: grid; place-items: center; border-radius: 7px; background: linear-gradient(135deg, #eff6ff, #dbeafe); color: #1d4ed8; font-weight: 900; box-shadow: inset 0 0 0 1px #d6e4f4; }
        .product-info strong { display: block; color: #111d36; font-size: 14px; line-height: 1.2; }
        .product-info span { display: block; margin-top: 3px; color: #6b7a99; font-size: 12px; font-weight: 750; }
        .sold { width: 54px; justify-self: center; text-align: center; color: #07132d; font-size: 12px; font-weight: 750; }
        .sold strong { display: block; font-size: 20px; line-height: 1; }
        table { width: 100%; border-collapse: collapse; color: #1c2b46; font-size: 13px; }
        th, td { padding: 12px 10px; border-bottom: 1px solid #e3ebf6; text-align: left; vertical-align: middle; white-space: nowrap; }
        th.text-center,
        td.text-center {
            text-align: center;
        }
        td.text-center .table-actions {
            justify-content: center;
        }
        th { color: #334155; font-size: 12px; font-weight: 900; }
        .table-scroll { max-height: 640px; overflow: auto; border-radius: 8px; }
        .table-scroll thead th { position: sticky; top: 0; z-index: 1; background: #fff; }
        tbody tr:last-child td { border-bottom: 0; }
        .status { display: inline-flex; align-items: center; justify-content: center; min-width: 78px; min-height: 26px; padding: 0 10px; border-radius: 7px; color: #047857; background: #dcfce7; font-size: 12px; font-weight: 850; }
        .pending { color: #ea580c; background: #ffedd5; }
        .status.pending { color: #047857; background: #d9fbe8; border-color: #a7f3d0; }
        .action { min-height: 28px; min-width: 86px; border: 1px solid #8b8cf6; border-radius: 6px; color: #4f46e5; background: #f8f8ff; font-size: 12px; font-weight: 850; }
        .page-header { display: flex; align-items: center; justify-content: space-between; gap: 18px; margin-bottom: 20px; }
        .page-header p { margin: 8px 0 0; color: #64748b; font-size: 14px; font-weight: 700; }
        .primary-button, .secondary-button, .danger-button { min-height: 40px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; border-radius: 8px; padding: 0 16px; text-decoration: none; font-size: 13px; font-weight: 900; cursor: pointer; }
        .primary-button { border: 0; color: #fff; background: linear-gradient(135deg, #0284c7, #38bdf8); box-shadow: 0 12px 24px rgba(2, 132, 199, .2); }
        .secondary-button { border: 1px solid #d9e2ef; color: #21314f; background: #fff; }
        .danger-button { border: 1px solid #fecaca; color: #dc2626; background: #fff5f5; }
        .toolbar { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-bottom: 16px; }
        .filters { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .field-group { display: grid; gap: 8px; margin-bottom: 18px; }
        .field-group label { color: #334155; font-size: 13px; font-weight: 900; }
        .field-control { width: 100%; min-height: 44px; border: 1px solid #d9e2ef; border-radius: 8px; padding: 0 13px; color: #10203b; background: #fff; outline: none; }
        textarea.field-control { min-height: 110px; padding-top: 12px; resize: vertical; }
        .field-control:focus { border-color: #38bdf8; box-shadow: 0 0 0 4px rgba(56, 189, 248, .14); }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 8px; }
        .error-text { color: #dc2626; font-size: 12px; font-weight: 800; }
        .alert { margin-bottom: 16px; border-radius: 8px; padding: 13px 14px; font-size: 13px; font-weight: 850; }
        .alert-success { color: #047857; background: #dcfce7; border: 1px solid #bbf7d0; }
        .alert-error { color: #b91c1c; background: #fee2e2; border: 1px solid #fecaca; }
        .empty-state { padding: 34px; text-align: center; color: #64748b; font-weight: 800; }
        .table-actions { display: flex; align-items: center; justify-content: center; gap: 8px; }
        .link-action { color: #2563eb; text-decoration: none; font-size: 13px; font-weight: 900; }
        .status.inactive { color: #475569; background: #e2e8f0; }
        .sale-items { display: grid; gap: 12px; margin-bottom: 18px; }
        .sale-item-row { display: grid; grid-template-columns: minmax(260px, 1fr) 130px 130px 130px 44px; gap: 10px; align-items: end; }
        .sale-meta { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin-bottom: 18px; }
        .summary-box { border: 1px solid #dbeafe; border-radius: 8px; padding: 16px; background: #f8fbff; }
        .summary-box span { display: block; color: #64748b; font-size: 12px; font-weight: 850; }
        .summary-box strong { display: block; margin-top: 7px; color: #07132d; font-size: 22px; }
        .icon-danger { width: 40px; min-height: 40px; border: 1px solid #fecaca; border-radius: 8px; color: #dc2626; background: #fff5f5; cursor: pointer; font-size: 20px; font-weight: 900; }
        .api-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; margin-bottom: 18px; }
        .api-card { display: grid; grid-template-columns: 60px 1fr; align-items: center; gap: 16px; }
        .api-card-icon { width: 58px; height: 58px; display: grid; place-items: center; border-radius: 14px; }
        .api-card-icon svg { width: 30px; height: 30px; }
        .api-label { display: block; color: #64748b; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: .02em; }
        .api-card strong { display: block; margin-top: 7px; color: #07132d; font-size: 18px; line-height: 1.25; word-break: break-word; }
        .api-card small { display: block; margin-top: 7px; color: #64748b; font-size: 12px; font-weight: 750; line-height: 1.45; }
        .api-note { margin-bottom: 18px; }
        .api-note-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
        .api-note-grid > div { border: 1px solid #e3ebf6; border-radius: 18px; padding: 16px; background: linear-gradient(180deg, #ffffff, #f8fbff); }
        .api-note-grid strong { display: block; color: #16233c; font-size: 14px; font-weight: 950; }
        .api-note-grid p { margin: 8px 0 0; color: #64748b; font-size: 13px; font-weight: 750; line-height: 1.55; }
        .api-note-grid code { color: #2563eb; font-family: "Inter", ui-monospace, SFMono-Regular, Consolas, monospace; font-weight: 900; }
        .api-doc-grid { display: grid; grid-template-columns: .9fr 1.1fr; gap: 18px; margin: 18px 0; }
        .endpoint-list { display: grid; gap: 10px; }
        .endpoint-row { display: grid; grid-template-columns: 64px 1fr; align-items: center; gap: 12px; min-height: 44px; border: 1px solid #e3ebf6; border-radius: 8px; padding: 0 12px; background: #fbfdff; }
        .endpoint-row span { display: inline-flex; align-items: center; justify-content: center; min-height: 26px; border-radius: 6px; color: #0369a1; background: #e0f2fe; font-size: 12px; font-weight: 950; }
        .endpoint-row code, .inline-code { color: #0f172a; font-family: "Inter", ui-monospace, SFMono-Regular, Consolas, monospace; font-size: 12px; font-weight: 800; }
        .inline-code { display: inline-flex; align-items: center; min-height: 32px; border: 1px solid #dbeafe; border-radius: 7px; padding: 0 10px; background: #f8fbff; }
        .code-block { margin: 0; min-height: 120px; overflow: auto; border: 1px solid #dbeafe; border-radius: 8px; padding: 16px; color: #dbeafe; background: #071a34; font-family: "Inter", ui-monospace, SFMono-Regular, Consolas, monospace; font-size: 13px; line-height: 1.65; }
        .compose-backdrop { position: fixed; inset: 0; z-index: 4200; display: none; background: rgba(15, 23, 42, .18); }
        .compose-backdrop.open { display: block; }
        .compose-card { position: fixed; right: auto; bottom: auto; left: 50%; top: 50%; z-index: 4300; width: min(560px, calc(100vw - 40px)); max-height: calc(100vh - 32px); border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; box-shadow: 0 24px 70px rgba(15, 23, 42, .24); overflow: hidden; transform: translate(-50%, -50%); }
        .compose-header { min-height: 48px; display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 0 16px; color: #fff; background: #0f172a; }
        .compose-header strong { font-size: 14px; font-weight: 900; }
        .compose-close { width: 32px; height: 32px; border: 0; color: #e2e8f0; background: transparent; cursor: pointer; font-size: 22px; line-height: 1; }
        .compose-body { padding: 16px; }
        .compose-body .field-group { margin-bottom: 12px; }
        .compose-body textarea.field-control { min-height: 160px; }
        .compose-footer { display: flex; align-items: center; justify-content: flex-end; gap: 10px; padding: 0 16px 16px; }
        .pagination { display: flex; justify-content: flex-end; margin-top: 18px; }
        .salespro-pagination { display: inline-flex; align-items: center; gap: 14px; border: 1px solid #dbeafe; border-radius: 999px; padding: 8px 10px 8px 18px; background: rgba(255, 255, 255, .92); box-shadow: 0 16px 36px rgba(37, 99, 235, .08); }
        .salespro-pagination__summary { margin: 0; color: #64748b; font-size: 13px; font-weight: 850; white-space: nowrap; }
        .salespro-pagination__summary strong { color: #16233c; font-weight: 950; }
        .salespro-pagination__controls { display: inline-flex; align-items: center; gap: 6px; border: 1px solid #dbeafe; border-radius: 999px; padding: 4px; background: #f8fbff; }
        .salespro-pagination__link,
        .salespro-pagination__ellipsis { width: 36px; height: 36px; display: inline-flex; align-items: center; justify-content: center; border: 1px solid transparent; border-radius: 999px; color: #334155; text-decoration: none; font-size: 13px; font-weight: 950; line-height: 1; background: transparent; transition: color .16s ease, border-color .16s ease, background .16s ease, box-shadow .16s ease; }
        .salespro-pagination__link:hover { color: #2563eb; border-color: #bfdbfe; background: #eff6ff; }
        .salespro-pagination__link.is-active { color: #fff; border-color: #0ea5e9; background: linear-gradient(135deg, #2563eb 0%, #38bdf8 100%); box-shadow: 0 12px 26px rgba(37, 99, 235, .2); }
        .salespro-pagination__link.is-disabled { color: #94a3b8; cursor: not-allowed; opacity: .55; }
        .salespro-pagination__ellipsis { color: #94a3b8; }
        @media (max-width: 720px) {
            .pagination { justify-content: center; }
            .salespro-pagination { width: 100%; flex-direction: column; align-items: stretch; border-radius: 24px; padding: 12px; }
            .salespro-pagination__summary { text-align: center; }
            .salespro-pagination__controls { justify-content: center; }
        }
        body {
            padding: 28px;
            color: #171b2d;
            background:
                radial-gradient(circle at 10% 0%, rgba(124, 58, 237, .12), transparent 28%),
                radial-gradient(circle at 90% 12%, rgba(14, 165, 233, .14), transparent 30%),
                linear-gradient(135deg, #edf3fb 0%, #f7f8ff 48%, #eef7fb 100%);
        }
        .app {
            grid-template-columns: 292px minmax(0, 1fr);
            min-height: calc(100vh - 56px);
            overflow: hidden;
            border: 1px solid rgba(203, 213, 225, .9);
            border-radius: 22px;
            background: rgba(255, 255, 255, .86);
            box-shadow: 0 28px 80px rgba(47, 65, 104, .16);
            backdrop-filter: blur(18px);
        }
        .sidebar {
            display: flex;
            flex-direction: column;
            color: #2f3446;
            border-right: 1px solid #e3e8f3;
            background: rgba(255, 255, 255, .78);
            padding: 22px 18px;
        }
        .brand {
            min-height: 70px;
            padding: 0 4px 18px;
            border-bottom: 1px solid #eef1f7;
        }
        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            color: #fff;
            background: linear-gradient(135deg, #4568f0, #62c9ef);
            box-shadow: 0 12px 24px rgba(69, 104, 240, .25);
        }
        .brand-name {
            color: #171b2d;
            font-family: var(--font-primary);
            font-size: 19px;
            letter-spacing: -.01em;
        }
        .brand-name span {
            color: #7a8498;
            font-size: 12px;
            font-weight: 800;
        }
        .nav-title {
            margin: 20px 8px 10px;
            color: #8b94a8;
            font-size: 11px;
            letter-spacing: .04em;
        }
        .nav-item {
            min-height: 42px;
            margin: 2px 0;
            border-radius: 12px;
            color: #3e465a;
            font-size: 14px;
            font-weight: 820;
            transition: background .18s ease, color .18s ease, transform .18s ease, box-shadow .18s ease;
        }
        .nav-item svg {
            width: 19px;
            height: 19px;
            color: #5f687d;
        }
        .nav-item.active {
            color: #fff;
            background: linear-gradient(135deg, #5b5ff0 0%, #7658e8 100%);
            box-shadow: 0 14px 28px rgba(91, 95, 240, .25);
        }
        .nav-item.active svg {
            color: #fff;
        }
        .nav-item:hover {
            color: #1f2a44;
            background: #f1f5fb;
            transform: translateX(2px);
        }
        .nav-item:not(.active):hover {
            color: #2563eb;
            background: #eaf3ff;
        }
        .nav-item:not(.active):hover svg {
            color: #2563eb;
        }
        .sidebar form {
            margin-top: auto;
            padding-top: 18px;
        }
        .sidebar-logout {
            color: #ef4444;
            background: #fff5f5;
        }
        .sidebar-logout svg {
            color: #ef4444;
        }
        .main {
            background: linear-gradient(180deg, #f8fbff 0%, #f4f7fb 100%);
        }
        .topbar {
            min-height: 78px;
            padding: 0 28px;
            border-bottom: 1px solid #e6ebf4;
            background: rgba(255, 255, 255, .72);
            backdrop-filter: blur(14px);
        }
        .topbar-left {
            display: flex;
            align-items: center;
            gap: 26px;
            min-width: 0;
        }
        h1 {
            color: #171b2d;
            font-family: var(--font-primary);
            font-size: 25px;
            font-weight: 800;
            letter-spacing: 0;
        }
        .top-search {
            width: min(390px, 34vw);
            min-height: 42px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #e1e7f0;
            border-radius: 12px;
            padding: 0 12px;
            color: #7b8498;
            background: rgba(255, 255, 255, .88);
            box-shadow: 0 8px 22px rgba(16, 24, 40, .04);
        }
        .top-search input {
            min-width: 0;
            flex: 1;
            border: 0;
            outline: 0;
            color: #252b3a;
            background: transparent;
            font-size: 14px;
        }
        .top-search button {
            border-left: 1px solid #e7ecf5;
            border-top: 0;
            border-right: 0;
            border-bottom: 0;
            padding-left: 10px;
            color: #8a93a6;
            background: transparent;
            font-size: 12px;
            font-weight: 850;
            cursor: pointer;
        }
        .date-filter, .icon-button, .profile-caret {
            border: 1px solid #e1e7f0;
            border-radius: 12px;
            background: rgba(255, 255, 255, .86);
            box-shadow: 0 8px 22px rgba(16, 24, 40, .04);
        }
        .date-filter select {
            border: 0;
            outline: 0;
            color: #18233b;
            background: transparent;
            font: inherit;
            font-weight: 900;
            cursor: pointer;
        }
        .icon-button {
            color: #242b3b;
        }
        .badge {
            top: -4px;
            right: -3px;
            background: #ef4444;
            box-shadow: 0 0 0 3px #fff;
        }
        .profile {
            min-height: 44px;
            border-radius: 14px;
            padding: 4px 8px 4px 4px;
            background: rgba(255, 255, 255, .72);
        }
        .avatar {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #4568f0, #47c1eb);
        }
        .avatar img {
            width: 100%;
            height: 100%;
            border-radius: inherit;
            object-fit: cover;
        }
        .content {
            padding: 28px;
        }
        .cards {
            grid-template-columns: repeat(5, minmax(190px, 1fr));
            gap: 16px;
            margin-bottom: 18px;
        }
        .card, .panel {
            border: 1px solid rgba(224, 231, 242, .96);
            border-radius: 14px;
            background: rgba(255, 255, 255, .88);
            box-shadow: 0 18px 42px rgba(60, 72, 100, .07);
        }
        a.card:hover {
            border-color: #bfc7ff;
            box-shadow: 0 22px 52px rgba(82, 92, 224, .14);
        }
        .stat-card {
            position: relative;
            min-height: 124px;
            overflow: hidden;
            gap: 14px;
            padding: 22px 18px;
        }
        .stat-card::before {
            content: none;
        }
        .purple-metric { color: #5b5ff0; }
        .green-metric { color: #16a379; }
        .orange-metric { color: #d99023; }
        .blue-metric { color: #2d6cdf; }
        .red-metric { color: #ef4444; }
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .86);
        }
        .stat-icon svg {
            width: 28px;
            height: 28px;
        }
        .purple { color: #5b5ff0; background: #eef0ff; }
        .green { color: #16a379; background: #e9fbf4; }
        .orange { color: #d99023; background: #fff5e6; }
        .blue { color: #2d6cdf; background: #eaf3ff; }
        .red { color: #ef4444; background: #fff0f2; }
        .stat-body span {
            color: #657086;
            font-size: 12px;
            font-weight: 850;
        }
        .stat-body strong {
            margin-top: 7px;
            color: #171b2d;
            font-size: 24px;
            font-weight: 950;
            letter-spacing: -.02em;
        }
        .stat-body small {
            margin-top: 8px;
            color: #0f9f72;
            font-size: 12px;
            font-weight: 900;
        }
        .stat-arrow {
            display: none;
        }
        .analytics-grid {
            grid-template-columns: minmax(520px, 1.28fr) minmax(300px, .72fr) minmax(330px, .82fr);
            gap: 16px;
        }
        .bottom-grid {
            grid-template-columns: minmax(560px, 1.12fr) minmax(430px, .88fr);
            gap: 16px;
        }
        .panel {
            padding: 22px;
        }
        .panel-header {
            margin-bottom: 18px;
        }
        h2 {
            color: #171b2d;
            font-size: 18px;
            font-weight: 950;
            letter-spacing: -.01em;
        }
        .filter-button, .secondary-button {
            border-color: #e0e7f1;
            border-radius: 11px;
            color: #334155;
            background: #fff;
            box-shadow: 0 8px 18px rgba(15, 23, 42, .04);
        }
        .primary-button {
            border-radius: 11px;
            background: linear-gradient(135deg, #4568f0, #5b5ff0);
            box-shadow: 0 14px 28px rgba(69, 104, 240, .24);
        }
        .danger-button {
            border-radius: 11px;
            background: #fff5f5;
        }
        .chart {
            height: 265px;
            border-radius: 13px;
            background:
                linear-gradient(180deg, rgba(91, 95, 240, .06), transparent 48%),
                #fff;
            padding: 4px;
        }
        .axis-label {
            fill: #7b8498;
            font-size: 11px;
            font-weight: 800;
        }
        .branch-layout {
            min-height: 260px;
            grid-template-columns: 150px 1fr;
        }
        .donut {
            width: 142px;
            box-shadow: 0 12px 26px rgba(69, 104, 240, .12);
        }
        .donut::after {
            width: 72px;
            height: 72px;
            box-shadow: inset 0 0 0 1px #edf1f7;
        }
        .legend {
            gap: 12px;
        }
        .legend-item {
            color: #667085;
            font-size: 12px;
        }
        .legend-item strong {
            color: #252b3a;
            font-size: 12px;
            font-weight: 920;
        }
        .product-list {
            gap: 12px;
        }
        .product-row {
            min-height: 50px;
            grid-template-columns: 22px 42px 1fr 50px;
            border-radius: 12px;
            padding: 6px 8px;
            transition: background .18s ease;
        }
        .product-row:hover {
            background: #f7f8fc;
        }
        .rank {
            color: #667085;
        }
        .product-img {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            color: #4568f0;
            background: #edf3ff;
            box-shadow: none;
        }
        .product-info strong {
            color: #22283a;
            font-size: 13px;
            font-weight: 920;
        }
        .product-info span {
            color: #8a93a6;
        }
        .sold strong {
            font-size: 18px;
        }
        table {
            border-collapse: separate;
            border-spacing: 0;
            color: #30384c;
            font-size: 13px;
        }
        th {
            padding: 14px 12px;
            color: #677085;
            background: #f1f4fa;
            font-size: 12px;
            font-weight: 900;
        }
        th:first-child {
            border-radius: 12px 0 0 12px;
        }
        th:last-child {
            border-radius: 0 12px 12px 0;
        }
        td {
            padding: 14px 12px;
            border-bottom: 1px solid #eef2f7;
            color: #252b3a;
        }
        tbody tr {
            transition: background .18s ease;
        }
        tbody tr:hover {
            background: #fafbff;
        }
        .table-scroll {
            max-height: 650px;
            border-radius: 13px;
        }
        .invoice-table-scroll {
            max-height: 650px;
            overflow: auto;
        }
        .table-scroll thead th {
            background: #f1f4fa;
        }
        .status {
            min-width: 82px;
            min-height: 28px;
            border-radius: 9px;
            color: #0f8f68;
            background: #dcf9ec;
            font-size: 12px;
            font-weight: 900;
        }
        .pending {
            color: #d97706;
            background: #fff1d6;
        }
        .status.pending {
            color: #047857;
            border-color: #a7f3d0;
            background: #d9fbe8;
        }
        .status.inactive {
            color: #667085;
            background: #e9edf4;
        }
        .action {
            min-height: 32px;
            border-color: #cfd7ff;
            border-radius: 9px;
            color: #4d55e8;
            background: #f6f7ff;
        }
        .page-header {
            margin-bottom: 22px;
            border: 1px solid #e5ebf5;
            border-radius: 14px;
            padding: 18px 20px;
            background: rgba(255, 255, 255, .72);
            box-shadow: 0 14px 32px rgba(60, 72, 100, .055);
        }
        .page-header h2 {
            font-size: 20px;
        }
        .page-header p {
            color: #707a90;
        }
        .toolbar {
            min-height: 58px;
            border-radius: 13px;
            padding: 8px;
            background: #f8fafc;
        }
        .field-control {
            min-height: 46px;
            border-color: #dce4f0;
            border-radius: 12px;
            color: #252b3a;
            background: #fff;
        }
        .field-control:focus {
            border-color: #8aa3ff;
            box-shadow: 0 0 0 4px rgba(69, 104, 240, .13);
        }
        .summary-box {
            border-color: #e1e8f3;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 12px 26px rgba(60, 72, 100, .05);
        }
        .summary-box span {
            color: #667085;
        }
        .summary-box strong {
            color: #171b2d;
            font-size: 20px;
        }
        .api-card, .endpoint-row, .inline-code {
            border-radius: 14px;
        }
        .code-block {
            border-radius: 14px;
            background: #0b1b33;
        }
        .salespro-pagination,
        .salespro-pagination__controls,
        .salespro-pagination__link,
        .salespro-pagination__ellipsis {
            border-radius: 999px;
        }
        body {
            padding: 46px;
            background:
                radial-gradient(circle at 12% 18%, rgba(151, 129, 255, .18), transparent 28%),
                radial-gradient(circle at 88% 6%, rgba(186, 230, 253, .4), transparent 24%),
                linear-gradient(135deg, #e3e9f2 0%, #f3f5fb 100%);
        }
        .app {
            max-width: 1760px;
            margin: 0 auto;
            grid-template-columns: 315px minmax(0, 1fr);
            border-radius: 24px;
            background: rgba(255, 255, 255, .92);
        }
        .sidebar {
            padding: 24px 18px 20px;
            background: rgba(255, 255, 255, .92);
        }
        .brand {
            min-height: 78px;
            padding-bottom: 20px;
        }
        .brand-mark {
            border-radius: 8px;
            background: linear-gradient(135deg, #4568f0 0%, #4e7df5 55%, #41c6ee 100%);
        }
        .brand-name {
            font-size: 21px;
            font-family: var(--font-primary);
            font-weight: 800;
        }
        .nav-title {
            margin-top: 22px;
            margin-bottom: 9px;
            color: #8b95a7;
            font-size: 12px;
            font-weight: 800;
        }
        .nav-item {
            min-height: 48px;
            padding: 0 14px;
            border-radius: 9px;
            color: #3d4658;
            font-size: 15px;
            font-weight: 780;
        }
        .nav-item.active {
            background: linear-gradient(135deg, #eff2f8, #f7f7fb);
            box-shadow: none;
            color: #171b2d;
        }
        .nav-item.active svg {
            color: #171b2d;
        }
        .nav-item:hover {
            transform: none;
            color: #2563eb;
            background: #eaf3ff;
        }
        .nav-item:not(.active):hover svg {
            color: #2563eb;
        }
        .topbar {
            min-height: 78px;
            padding: 0 30px;
            background: rgba(255, 255, 255, .82);
        }
        .topbar-left {
            flex: 1;
        }
        .top-search {
            width: min(430px, 42vw);
            border-radius: 9px;
            box-shadow: 0 10px 26px rgba(47, 65, 104, .05);
        }
        .top-actions {
            gap: 14px;
        }
        .date-filter {
            min-height: 44px;
            border-radius: 9px;
        }
        .profile {
            background: transparent;
        }
        .profile strong {
            font-size: 15px;
            font-weight: 900;
        }
        .profile span {
            color: #7b8498;
            font-weight: 700;
        }
        .content {
            padding: 44px 38px 38px;
        }
        .dashboard-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 22px;
        }
        .dashboard-heading h2 {
            color: #12182b;
            font-family: var(--font-primary);
            font-size: 33px;
            font-weight: 800;
            line-height: 1.08;
            letter-spacing: 0;
        }
        .dashboard-heading p {
            margin: 10px 0 0;
            color: #6f788b;
            font-size: 14px;
            font-weight: 750;
        }
        .dashboard-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .dashboard-actions .primary-button {
            min-height: 46px;
            padding: 0 20px;
            border-radius: 9px;
            background: linear-gradient(135deg, #4568f0, #4c5bea);
        }
        .dashboard-actions .filter-button {
            min-height: 46px;
            padding: 0 18px;
        }
        .cards {
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 20px;
        }
        .stat-card {
            min-height: 136px;
            align-items: flex-end;
            padding: 20px;
            border-radius: 12px;
        }
        .purple-metric { background: linear-gradient(135deg, #fff 0%, #f1f2ff 100%); }
        .green-metric { background: linear-gradient(135deg, #fff 0%, #edfdf8 100%); }
        .orange-metric { background: linear-gradient(135deg, #fff 0%, #fff7ed 100%); }
        .blue-metric { background: linear-gradient(135deg, #fff 0%, #eff6ff 100%); }
        .red-metric { background: linear-gradient(135deg, #fff 0%, #fff1f2 100%); }
        .stat-card::before {
            content: none;
        }
        .stat-icon {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            opacity: .9;
        }
        .stat-arrow {
            display: none;
        }
        .stat-body span {
            font-size: 13px;
            font-weight: 760;
        }
        .stat-body strong {
            font-size: 26px;
        }
        .stat-body small {
            display: inline-flex;
            align-items: center;
            min-height: 26px;
            border: 1px solid #bdebdc;
            border-radius: 7px;
            padding: 0 8px;
            background: #effdf7;
        }
        .stat-body small.warning {
            border-color: #fecaca;
            background: #fff1f2;
        }
        .analytics-grid {
            grid-template-columns: 1.05fr 1.05fr .78fr;
            gap: 18px;
            margin-bottom: 20px;
        }
        .bottom-grid {
            grid-template-columns: 1.08fr .92fr;
            gap: 18px;
        }
        .panel {
            border-radius: 12px;
            padding: 24px;
            background: rgba(255, 255, 255, .94);
            box-shadow: 0 15px 35px rgba(47, 65, 104, .07);
        }
        .panel-header h2 {
            font-size: 22px;
        }
        .filter-button {
            min-height: 42px;
            border-radius: 9px;
            padding: 0 18px;
        }
        .chart {
            height: 290px;
            padding: 6px 0 0;
            background: transparent;
        }
        .chart svg {
            max-width: 100%;
        }
        .axis-label {
            text-anchor: start;
            dominant-baseline: middle;
        }
        .axis-label-x {
            text-anchor: middle;
        }
        .branch-layout {
            min-height: 290px;
            grid-template-columns: 1fr;
            justify-items: center;
            gap: 16px;
        }
        .donut {
            width: 174px;
        }
        .donut::after {
            width: 94px;
            height: 94px;
        }
        .legend {
            width: 100%;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px 14px;
        }
        .legend-item {
            align-items: start;
            border-radius: 10px;
            padding: 8px;
            background: #f8fafc;
        }
        .branch-donut {
            position: relative;
            width: 220px;
            aspect-ratio: 1;
            display: grid;
            place-items: center;
        }
        .branch-donut svg {
            width: 100%;
            height: 100%;
            overflow: visible;
        }
        .donut-track,
        .donut-slice {
            fill: none;
            stroke-width: 22;
            transform: rotate(-90deg);
            transform-origin: 60px 60px;
        }
        .donut-track {
            stroke: #eef2f7;
        }
        .donut-slice {
            cursor: pointer;
            stroke-linecap: round;
            transition: stroke-width .18s ease, opacity .18s ease;
        }
        .branch-donut.is-filtering .donut-slice {
            opacity: 0;
        }
        .branch-donut.is-filtering .donut-slice.is-hovered {
            opacity: 1;
        }
        .branch-donut.is-filtering .donut-slice.is-hovered {
            stroke-width: 26;
        }
        .branch-donut-hole {
            position: absolute;
            width: 94px;
            height: 94px;
            border-radius: 50%;
            background: #fff;
            box-shadow: inset 0 0 0 1px #edf2f8;
            pointer-events: none;
        }
        .branch-tooltip {
            position: absolute;
            left: 50%;
            top: 50%;
            z-index: 3;
            min-width: 150px;
            border: 1px solid #dce5f2;
            border-radius: 16px;
            padding: 10px 12px;
            color: #12182b;
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 18px 42px rgba(30, 41, 59, .16);
            opacity: 0;
            pointer-events: none;
            text-align: center;
            transform: translate(-50%, -50%) scale(.96);
            transition: opacity .16s ease, transform .16s ease;
        }
        .branch-tooltip.active {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
        .branch-tooltip strong {
            display: block;
            color: #12182b;
            font-size: 13px;
            font-weight: 900;
        }
        .branch-tooltip span {
            display: block;
            margin-top: 4px;
            color: #64748b;
            font-size: 12px;
            font-weight: 800;
        }
        .product-list {
            gap: 10px;
        }
        .product-row {
            min-height: 58px;
            border: 1px solid transparent;
            background: #fff;
        }
        .product-row:hover {
            border-color: #e6ebf4;
        }
        .product-img {
            border-radius: 9px;
        }
        th {
            height: 48px;
            background: #f0f3f9;
            color: #697386;
            font-weight: 760;
        }
        td {
            height: 58px;
            color: #252b3a;
        }
        .status {
            border: 1px solid rgba(15, 143, 104, .12);
            border-radius: 8px;
        }
        .page-header {
            border-radius: 12px;
            background: rgba(255, 255, 255, .92);
        }
        .page-header h2 {
            font-family: var(--font-primary);
            font-size: 27px;
            font-weight: 800;
            letter-spacing: 0;
        }
        .toolbar {
            border: 1px solid #edf1f7;
            background: #f8fafc;
        }
        .app {
            border-radius: 30px;
        }
        .brand-mark,
        .avatar,
        .product-img,
        .api-card-icon,
        .stat-icon {
            border-radius: 18px;
        }
        .nav-item,
        .top-search,
        .date-filter,
        .icon-button,
        .profile,
        .profile-caret,
        .filter-button,
        .primary-button,
        .secondary-button,
        .danger-button,
        .action,
        .icon-danger,
        .salespro-pagination,
        .salespro-pagination__controls,
        .salespro-pagination__link,
        .salespro-pagination__ellipsis,
        .endpoint-row,
        .inline-code,
        .field-control,
        .alert,
        .summary-box {
            border-radius: 999px;
        }
        textarea.field-control,
        .code-block,
        .toolbar,
        .compose-card,
        .page-header,
        .table-scroll,
        .card,
        .panel {
            border-radius: 22px;
        }
        .stat-card {
            border-radius: 24px;
        }
        .status,
        .endpoint-row span {
            border-radius: 999px;
        }
        .product-row,
        .legend-item {
            border-radius: 18px;
        }
        .compose-modal {
            border-radius: 26px;
        }
        .profile-panel {
            width: 100%;
        }
        .profile-form-grid {
            display: grid;
            grid-template-columns: minmax(240px, 330px) minmax(0, 1fr);
            gap: clamp(20px, 3vw, 42px);
            align-items: start;
            width: 100%;
            min-width: 0;
        }
        .profile-form-grid > div:last-child {
            width: 100%;
            min-width: 0;
        }
        .profile-form-grid .form-grid {
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 270px), 1fr));
            gap: clamp(16px, 2vw, 24px);
            min-width: 0;
        }
        .profile-form-grid .field-group,
        .profile-form-grid .field-control,
        .profile-form-grid .password-field {
            min-width: 0;
        }
        .profile-photo-card {
            display: grid;
            justify-items: center;
            gap: 12px;
            border: 1px solid #e4ebf5;
            border-radius: 24px;
            padding: 24px;
            text-align: center;
            background: linear-gradient(135deg, #f8fbff, #eef8ff);
            min-height: 300px;
            width: 100%;
        }
        .profile-photo-preview {
            width: 132px;
            height: 132px;
            display: grid;
            place-items: center;
            overflow: hidden;
            border-radius: 50%;
            color: #fff;
            background: linear-gradient(135deg, #4568f0, #47c1eb);
            box-shadow: inset 0 0 0 5px rgba(255, 255, 255, .86), 0 18px 42px rgba(69, 104, 240, .22);
            font-size: 46px;
            font-weight: 900;
        }
        .profile-photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-photo-card strong {
            color: #12182b;
            font-size: 18px;
            font-weight: 900;
        }
        .profile-photo-card span {
            color: #6f788b;
            font-size: 13px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }
        .profile-upload {
            margin-top: 8px;
            cursor: pointer;
        }
        @media (max-width: 1180px) {
            .profile-form-grid {
                grid-template-columns: 1fr;
            }

            .profile-photo-card {
                max-width: 420px;
            }
        }
        .password-field {
            position: relative;
        }
        .password-field .field-control {
            padding-right: 62px;
        }
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 18px;
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border: 0;
            border-radius: 50%;
            color: #6f7c94;
            background: transparent;
            cursor: pointer;
            transform: translateY(-50%);
        }
        .password-toggle:hover {
            color: #2563eb;
            background: #eef6ff;
        }
        .password-toggle svg {
            width: 19px;
            height: 19px;
            stroke-width: 1.9;
        }
        .password-toggle .hidden {
            display: none;
        }
        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
        }
        .cards {
            grid-template-columns: repeat(auto-fit, minmax(310px, 1fr));
        }
        .stat-card {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            align-items: stretch;
            gap: 16px;
            min-width: 0;
            min-height: 148px;
            overflow: hidden;
        }
        .stat-body {
            display: grid;
            grid-template-rows: auto 1fr auto;
            align-self: stretch;
            min-width: 0;
            padding-right: 0;
        }
        .stat-body span,
        .stat-body strong,
        .stat-body small {
            max-width: 100%;
        }
        .stat-body span {
            display: block;
            overflow-wrap: anywhere;
        }
        .stat-body strong {
            align-self: center;
            overflow: visible;
            text-overflow: clip;
            white-space: nowrap;
            overflow-wrap: normal;
            font-size: clamp(20px, 1.35vw, 26px);
            letter-spacing: 0;
        }
        .stat-body small {
            width: fit-content;
            white-space: normal;
            overflow-wrap: anywhere;
        }
        .stat-icon {
            position: static;
            grid-column: 2;
            grid-row: 1;
            width: 54px;
            height: 54px;
        }
        .stat-arrow {
            display: none;
        }
        .analytics-grid {
            grid-template-columns: repeat(auto-fit, minmax(min(360px, 100%), 1fr));
        }
        .bottom-grid {
            grid-template-columns: repeat(auto-fit, minmax(min(430px, 100%), 1fr));
        }
        .panel {
            min-width: 0;
            overflow: hidden;
        }
        .panel-header {
            flex-wrap: wrap;
        }
        .panel-filter-form {
            position: relative;
            margin: 0;
        }
        .panel-filter-form::after {
            content: "";
            position: absolute;
            top: 50%;
            right: 22px;
            width: 10px;
            height: 10px;
            border-right: 2.5px solid #3d638b;
            border-bottom: 2.5px solid #3d638b;
            pointer-events: none;
            transform: translateY(-68%) rotate(45deg);
            transition: transform .16s ease, border-color .16s ease;
        }
        .panel-filter-form:focus-within::after {
            border-color: #2563eb;
            transform: translateY(-35%) rotate(225deg);
        }
        .panel-filter-form.has-custom-filter::after {
            content: none;
        }
        .date-filter.has-custom-filter {
            position: relative;
            gap: 10px;
            min-height: 46px;
            border: 0;
            padding: 0;
            background: transparent;
            box-shadow: none;
        }
        .date-filter.has-custom-filter > svg {
            position: absolute;
            left: 16px;
            z-index: 2;
            width: 16px;
            height: 16px;
            color: #48617f;
            pointer-events: none;
        }
        .filter-select-native {
            display: none;
        }
        .filter-select {
            min-width: 174px;
            min-height: 58px;
            appearance: none;
            border: 2px solid #9fd2ff;
            border-radius: 999px;
            padding: 0 54px 0 24px;
            color: #16233c;
            background: linear-gradient(180deg, #fbfdff 0%, #f0f8ff 100%);
            box-shadow: 0 14px 30px rgba(37, 99, 235, .12), inset 0 1px 0 rgba(255, 255, 255, .92);
            font-size: 14px;
            font-weight: 900;
            cursor: pointer;
            outline: 0;
            transition: border-color .16s ease, box-shadow .16s ease, background .16s ease;
        }
        .filter-select:hover,
        .filter-select:focus {
            border-color: #7cc4ff;
            background: linear-gradient(180deg, #ffffff 0%, #ecf7ff 100%);
            box-shadow: 0 16px 34px rgba(37, 99, 235, .16), 0 0 0 4px rgba(147, 197, 253, .16);
        }
        .filter-select option {
            color: #1d2a44;
            background: #fff;
            font-weight: 800;
        }
        .custom-filter {
            position: relative;
            z-index: 220;
            min-width: 174px;
        }
        .field-group .custom-filter,
        .sale-item-row .custom-filter {
            width: 100%;
        }
        .filters .custom-filter {
            flex: 0 0 auto;
        }
        .date-filter .custom-filter {
            min-width: 188px;
        }
        .custom-filter-button {
            width: 100%;
            min-height: 46px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border: 2px solid #9fd2ff;
            border-radius: 999px;
            padding: 0 18px 0 20px;
            color: #16233c;
            background: linear-gradient(180deg, #fbfdff 0%, #f0f8ff 100%);
            box-shadow: 0 14px 30px rgba(37, 99, 235, .12), inset 0 1px 0 rgba(255, 255, 255, .92);
            font: inherit;
            font-size: 13px;
            font-weight: 900;
            cursor: pointer;
        }
        .date-filter .custom-filter-button {
            padding-left: 42px;
        }
        .custom-filter-button svg {
            color: #3d638b;
            stroke-width: 2.5;
            transition: transform .16s ease, color .16s ease;
        }
        .custom-filter.open .custom-filter-button {
            border-color: #7cc4ff;
            background: linear-gradient(180deg, #ffffff 0%, #ecf7ff 100%);
            box-shadow: 0 16px 34px rgba(37, 99, 235, .16), 0 0 0 4px rgba(147, 197, 253, .16);
        }
        .custom-filter.open .custom-filter-button svg {
            color: #2563eb;
            transform: rotate(180deg);
        }
        .custom-filter-menu {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 5000;
            width: var(--filter-menu-width, 174px);
            max-height: min(320px, calc(100vh - 24px));
            overflow-y: auto;
            overflow-x: hidden;
            border: 1px solid #b9d8f5;
            border-radius: 20px;
            padding: 8px;
            background: rgba(255, 255, 255, .98);
            box-shadow: 0 24px 54px rgba(30, 64, 175, .16);
            opacity: 0;
            pointer-events: none;
            transform: translateY(-8px) scale(.98);
            transition: opacity .16s ease, transform .16s ease;
        }
        .custom-filter-menu.open {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0) scale(1);
        }
        .custom-filter-option {
            width: 100%;
            min-height: 40px;
            display: flex;
            align-items: center;
            border: 0;
            border-radius: 14px;
            padding: 0 16px;
            color: #16233c;
            background: transparent;
            font: inherit;
            font-size: 14px;
            font-weight: 900;
            text-align: left;
            cursor: pointer;
        }
        .custom-filter-option:hover {
            color: #2563eb;
            background: #eaf3ff;
        }
        .custom-filter-option.active {
            color: #fff;
            background: linear-gradient(135deg, #2563eb, #38bdf8);
        }
        .link-action,
        .action,
        .secondary-button,
        .danger-button {
            min-height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border-radius: 999px;
            padding: 0 14px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 950;
            transition: color .16s ease, background .16s ease, border-color .16s ease, box-shadow .16s ease, transform .16s ease;
        }
        .link-action,
        .action,
        .secondary-button {
            border: 1px solid #bfd7ff;
            color: #2563eb;
            background: #f7fbff;
        }
        .danger-button {
            border: 1px solid #ffc4c4;
            color: #dc2626;
            background: #fff5f5;
        }
        .link-action:hover,
        .action:hover,
        .secondary-button:hover {
            color: #fff;
            border-color: #2563eb;
            background: linear-gradient(135deg, #2563eb, #38bdf8);
            box-shadow: 0 12px 26px rgba(37, 99, 235, .22);
            transform: translateY(-1px);
        }
        .danger-button:hover {
            color: #fff;
            border-color: #dc2626;
            background: linear-gradient(135deg, #dc2626, #ef4444);
            box-shadow: 0 12px 26px rgba(220, 38, 38, .2);
            transform: translateY(-1px);
        }
        .table-actions .link-action,
        .table-actions .danger-button,
        .table-actions .secondary-button,
        .table-actions .action {
            min-width: 78px;
            min-height: 38px;
            padding-inline: 14px;
        }
        .app,
        .sidebar,
        .brand,
        .brand-name,
        .nav-title,
        .nav-item,
        .nav-item svg {
            transition: grid-template-columns .22s ease, width .22s ease, padding .22s ease, margin .22s ease, opacity .16s ease, gap .22s ease, color .16s ease, background .16s ease, transform .18s ease;
        }
        .sidebar {
            position: relative;
            overflow: hidden;
        }
        .sidebar-toggle {
            width: 44px;
            height: 44px;
            flex: 0 0 auto;
            display: grid;
            place-items: center;
            border: 1px solid #8ecbff;
            border-radius: 999px;
            margin: 0 8px 16px auto;
            color: #2563eb;
            background: linear-gradient(135deg, #eef7ff, #dbeeff);
            box-shadow: 0 12px 28px rgba(37, 99, 235, .16), inset 0 1px 0 rgba(255, 255, 255, .9);
            cursor: pointer;
            transition: transform .2s ease, color .16s ease, background .16s ease, border-color .16s ease;
        }
        .sidebar-toggle:hover {
            color: #fff;
            border-color: #2563eb;
            background: linear-gradient(135deg, #2563eb, #38bdf8);
        }
        .sidebar-toggle svg {
            width: 22px;
            height: 22px;
            stroke-width: 2.4;
            transition: transform .22s ease;
        }
        html.sidebar-collapsed .app {
            grid-template-columns: 92px minmax(0, 1fr);
        }
        html.sidebar-collapsed .sidebar {
            padding-inline: 14px;
        }
        html.sidebar-collapsed .brand {
            justify-content: center;
            gap: 0;
            padding-inline: 0;
            flex-direction: column;
            gap: 12px;
        }
        html.sidebar-collapsed .brand-mark {
            width: 50px;
            height: 50px;
        }
        html.sidebar-collapsed .brand-name {
            width: 0;
            opacity: 0;
            overflow: hidden;
            white-space: nowrap;
        }
        html.sidebar-collapsed .sidebar-toggle {
            margin: 0 auto 18px;
        }
        html.sidebar-collapsed .sidebar-toggle svg {
            transform: rotate(180deg);
        }
        html.sidebar-collapsed .nav-title {
            height: 1px;
            margin: 12px 0;
            color: transparent;
            background: rgba(147, 197, 253, .18);
            overflow: hidden;
        }
        html.sidebar-collapsed .nav-item {
            justify-content: center;
            gap: 0;
            padding-inline: 0;
            font-size: 0;
        }
        html.sidebar-collapsed .nav-item svg {
            width: 22px;
            height: 22px;
            flex: 0 0 auto;
        }
        .analytics-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            align-items: start;
        }
        .bottom-grid {
            grid-template-columns: 1fr;
        }
        .dashboard-table-wrap {
            width: 100%;
            overflow-x: auto;
            border-radius: 22px;
        }
        .dashboard-table-wrap table {
            min-width: 760px;
        }
        .sales-overview-panel {
            grid-column: 1 / -1;
        }
        .branch-sales-panel {
            grid-column: 1 / -1;
        }
        .top-products-panel,
        .inactive-customers-panel {
            min-width: 0;
            min-height: 520px;
            display: flex;
            flex-direction: column;
        }
        .top-products-panel .product-list,
        .inactive-customers-panel .dashboard-table-wrap {
            flex: 1;
        }
        .top-products-panel .product-list {
            align-content: center;
            max-height: 390px;
            overflow-y: auto;
            padding-right: 6px;
        }
        .inactive-customers-panel .dashboard-table-wrap {
            display: flex;
            align-items: center;
            max-height: 390px;
            overflow-y: auto;
        }
        .inactive-customers-panel .dashboard-table-wrap table {
            min-width: 0;
        }
        .inactive-customers-panel th,
        .inactive-customers-panel td {
            padding-inline: 8px;
        }
        .sales-overview-panel .chart {
            height: 360px;
        }
        @media (max-width: 1280px) {
            .cards { grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); }
            .analytics-grid, .bottom-grid { grid-template-columns: 1fr; }
            .api-grid, .api-doc-grid, .api-note-grid { grid-template-columns: 1fr; }
            .sales-overview-panel {
                grid-column: auto;
            }
            .branch-sales-panel {
                grid-column: auto;
            }
            .sales-overview-panel .chart {
                height: 300px;
            }
            .branch-sales-panel,
            .top-products-panel,
            .inactive-customers-panel {
                min-height: auto;
            }
            .inactive-customers-panel .dashboard-table-wrap table {
                min-width: 760px;
            }
        }
        @media (max-width: 1180px) {
            body { padding: 24px; }
            .app { grid-template-columns: 260px minmax(0, 1fr); }
            .content { padding: 30px 24px; }
            .dashboard-heading { align-items: flex-start; flex-direction: column; }
            .dashboard-actions { width: 100%; justify-content: flex-start; flex-wrap: wrap; }
            .topbar { align-items: stretch; flex-direction: column; height: auto; padding: 20px 24px; }
            .topbar-left { width: 100%; }
            .top-search { width: 100%; }
            .top-actions { width: 100%; justify-content: flex-start; flex-wrap: wrap; }
        }
        @media (max-width: 900px) {
            body { padding: 0; }
            .app { grid-template-columns: 1fr; }
            .app { min-height: 100vh; border: 0; border-radius: 0; }
            .sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                z-index: 80;
                width: 260px;
                height: 100vh;
                overflow-y: auto;
                background: #fff;
                box-shadow: 24px 0 60px rgba(15, 23, 42, .24);
            }
            html.sidebar-collapsed .sidebar {
                width: 0;
                padding: 0;
                overflow: visible;
                box-shadow: none;
            }
            html.sidebar-collapsed .sidebar-toggle {
                width: 46px;
                height: 46px;
                position: fixed;
                top: 52px;
                left: 18px;
                z-index: 90;
                color: #fff;
                background: linear-gradient(135deg, #2563eb, #38bdf8);
                box-shadow: 0 14px 30px rgba(37, 99, 235, .22);
            }
            html.sidebar-collapsed .brand,
            html.sidebar-collapsed .nav-title,
            html.sidebar-collapsed .nav-item {
                opacity: 0;
                pointer-events: none;
            }
            .mobile-sidebar-backdrop {
                position: fixed;
                inset: 0 0 0 260px;
                z-index: 70;
                display: block;
                background: rgba(15, 23, 42, .28);
                backdrop-filter: blur(2px);
            }
            html.sidebar-collapsed .mobile-sidebar-backdrop {
                display: none;
            }
            html.sidebar-collapsed .app {
                grid-template-columns: 1fr;
            }
            .main {
                margin-left: 0;
                transition: margin-left .22s ease;
            }
            html:not(.sidebar-collapsed) .main {
                margin-left: 0;
            }
            .topbar { align-items: flex-start; flex-direction: column; padding: 22px 22px 22px 72px; }
            .topbar-left { width: 100%; align-items: stretch; flex-direction: column; gap: 14px; }
            .top-search { width: 100%; }
            .top-actions { width: 100%; justify-content: space-between; flex-wrap: wrap; }
            .content { padding: 20px; }
            .cards { grid-template-columns: 1fr; }
            .stat-card { min-height: 128px; }
            .stat-body strong { font-size: clamp(18px, 6vw, 25px); }
            .dashboard-heading h2 { font-size: 30px; }
            .panel { padding: 18px; }
            .branch-layout { grid-template-columns: 1fr; justify-items: center; }
            table { min-width: 860px; }
            .sale-item-row, .sale-meta { grid-template-columns: 1fr; }
            .profile-form-grid { grid-template-columns: 1fr; }
            .page-header, .toolbar, .form-actions { align-items: stretch; flex-direction: column; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="app">
        @include('admin.partials.sidebar')
        <div class="mobile-sidebar-backdrop" data-sidebar-backdrop></div>

        <main class="main">
            @include('admin.partials.topbar', ['pageTitle' => $pageTitle ?? 'Dashboard'])
            @yield('content')
        </main>
    </div>
    <script>
        (() => {
            let filterId = 0;

            const getFilterMenu = (wrapper) => {
                return document.querySelector(`.custom-filter-menu[data-filter-menu="${wrapper.dataset.filterId}"]`);
            };

            const closeSalesProFilters = (except = null) => {
                document.querySelectorAll('.custom-filter.open').forEach((dropdown) => {
                    if (dropdown === except) {
                        return;
                    }

                    dropdown.classList.remove('open');
                    getFilterMenu(dropdown)?.classList.remove('open');
                });
            };

            const positionFilterMenu = (wrapper) => {
                const menu = getFilterMenu(wrapper);
                const button = wrapper.querySelector('.custom-filter-button');

                if (!menu || !button) {
                    return;
                }

                const rect = button.getBoundingClientRect();
                const gap = 8;
                const viewportPadding = 12;
                const menuWidth = Math.max(rect.width, 174);
                const maxLeft = window.innerWidth - menuWidth - viewportPadding;
                const left = Math.max(viewportPadding, Math.min(rect.left, maxLeft));
                const menuHeight = Math.min(menu.scrollHeight, 320, window.innerHeight - (viewportPadding * 2));
                const spaceBelow = window.innerHeight - rect.bottom - viewportPadding;
                const top = spaceBelow >= menuHeight + gap
                    ? rect.bottom + gap
                    : Math.max(viewportPadding, rect.top - menuHeight - gap);

                menu.style.setProperty('--filter-menu-width', `${menuWidth}px`);
                menu.style.left = `${left}px`;
                menu.style.top = `${top}px`;
            };

            window.enhanceSalesProFilters = () => {
                document.querySelectorAll('.custom-filter-menu[data-filter-menu]').forEach((menu) => {
                    if (!document.querySelector(`.custom-filter[data-filter-id="${menu.dataset.filterMenu}"]`)) {
                        menu.remove();
                    }
                });

                document.querySelectorAll('select:not([data-filter-enhanced]):not([multiple])').forEach((select) => {
                    const ownerForm = select.closest('.panel-filter-form') || select.closest('.date-filter') || select.closest('.field-group') || select.closest('.filters') || select.parentElement;
                    const wrapper = document.createElement('div');
                    const button = document.createElement('button');
                    const menu = document.createElement('div');
                    const selectWidth = select.style.width;
                    const currentFilterId = `filter-${++filterId}`;

                    select.dataset.filterEnhanced = 'true';
                    select.classList.add('filter-select-native');
                    ownerForm?.classList.add('has-custom-filter');
                    wrapper.className = 'custom-filter';
                    wrapper.dataset.filterId = currentFilterId;
                    wrapper.style.width = selectWidth || (select.classList.contains('field-control') ? '100%' : '');
                    menu.className = 'custom-filter-menu';
                    menu.dataset.filterMenu = currentFilterId;
                    button.className = 'custom-filter-button';
                    button.type = 'button';
                    button.innerHTML = `<span>${select.options[select.selectedIndex]?.text || ''}</span><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="m6 9 6 6 6-6"/></svg>`;

                    Array.from(select.options).forEach((option) => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'custom-filter-option';
                        item.textContent = option.text;
                        item.dataset.value = option.value;

                        if (option.selected) {
                            item.classList.add('active');
                        }

                        item.addEventListener('click', () => {
                            select.value = item.dataset.value;
                            button.querySelector('span').textContent = item.textContent;
                            menu.querySelectorAll('.custom-filter-option').forEach((entry) => {
                                entry.classList.toggle('active', entry === item);
                            });
                            wrapper.classList.remove('open');
                            menu.classList.remove('open');
                            select.dispatchEvent(new Event('change', { bubbles: true }));
                        });

                        menu.appendChild(item);
                    });

                    button.addEventListener('click', () => {
                        const willOpen = !wrapper.classList.contains('open');
                        closeSalesProFilters(wrapper);
                        wrapper.classList.toggle('open', willOpen);
                        menu.classList.toggle('open', willOpen);

                        if (willOpen) {
                            positionFilterMenu(wrapper);
                        }
                    });

                    wrapper.append(button);
                    select.after(wrapper);
                    document.body.appendChild(menu);
                });
            };

            document.addEventListener('click', (event) => {
                const toggle = event.target.closest('[data-profile-toggle]');
                const activeMenu = document.querySelector('[data-profile-menu].open');

                if (toggle) {
                    toggle.closest('[data-profile-menu]')?.classList.toggle('open');
                    return;
                }

                if (activeMenu && !event.target.closest('[data-profile-menu]')) {
                    activeMenu.classList.remove('open');
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    document.querySelector('[data-profile-menu].open')?.classList.remove('open');
                }
            });

            document.addEventListener('click', (event) => {
                const toggle = event.target.closest('[data-sidebar-toggle]');

                if (!toggle) {
                    if (
                        window.matchMedia('(max-width: 900px)').matches
                        && !document.documentElement.classList.contains('sidebar-collapsed')
                        && !event.target.closest('.sidebar')
                    ) {
                        document.documentElement.classList.add('sidebar-collapsed');
                        localStorage.setItem('salespro-sidebar-collapsed', '1');
                    }

                    return;
                }

                const collapsed = document.documentElement.classList.toggle('sidebar-collapsed');
                localStorage.setItem('salespro-sidebar-collapsed', collapsed ? '1' : '0');
            });

            document.addEventListener('click', (event) => {
                const link = event.target.closest('.sidebar .nav-item[href]');

                if (
                    !link
                    || !window.matchMedia('(max-width: 900px)').matches
                    || document.documentElement.classList.contains('sidebar-collapsed')
                ) {
                    return;
                }

                document.documentElement.classList.add('sidebar-collapsed');
                localStorage.setItem('salespro-sidebar-collapsed', '1');
            });

            document.addEventListener('click', (event) => {
                if (!event.target.closest('.custom-filter') && !event.target.closest('.custom-filter-menu')) {
                    closeSalesProFilters();
                }
            });

            window.addEventListener('resize', () => {
                document.querySelectorAll('.custom-filter.open').forEach(positionFilterMenu);
            });

            window.addEventListener('scroll', () => {
                document.querySelectorAll('.custom-filter.open').forEach(positionFilterMenu);
            }, true);

            window.closeSalesProFilters = closeSalesProFilters;
            window.enhanceSalesProFilters();
        })();
    </script>
</body>
</html>
