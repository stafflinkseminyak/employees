{{-- ============================================================
     StaffLink — Linkers Hub
     Complete Blade template with inline CSS & JavaScript
     ============================================================ --}}

@extends('admin.layout')
@section('title', 'Linkers Hub')
@section('page-title', 'Employees')
@section('page-description', 'Manage employee profiles, contracts, and status in one place.')

@section('content')

{{-- Google Fonts --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

{{-- ===================== INLINE CSS ===================== --}}
<style>
/* ---- Dropdown Arrow for all selects ---- */
select {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 12px center / 14px !important;
    padding-right: 34px !important;
}
/* ---- 1. Base ---- */
.lh-wrap{font-family:'Inter',sans-serif;color:#1e293b;min-height:100vh}

/* ---- 2. Cards ---- */
.lh-card{background:#fff;border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;transition:box-shadow .2s,transform .15s;min-width:0}
.lh-card:hover{box-shadow:0 4px 12px rgba(0,0,0,.06);transform:translateY(-1px)}
.lh-card.terminated{opacity:.55}
.lh-card.joining-soon{opacity:.75}
.lh-card.on-leave{opacity:.8}
.lh-card-info{flex:1;min-width:0}
.lh-card .lh-name{font-size:13px;font-weight:700;color:#0f172a;margin:0 0 2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.lh-card .lh-pos{font-size:12px;color:#64748b;margin-bottom:4px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}

/* ---- 3. Toggle / Collapse ---- */
.lh-toggle{display:flex;align-items:center;gap:8px;cursor:pointer;user-select:none;padding:6px 0;margin-bottom:8px}
.lh-toggle h3{font-size:16px;font-weight:700;color:#0f172a;margin:0}
.lh-toggle .lh-count{font-size:13px;color:#64748b;font-weight:400}
.chv{width:18px;height:18px;transition:transform .25s;flex-shrink:0}
.chv.collapsed{transform:rotate(-90deg)}
.lh-section-body{overflow:hidden;transition:max-height .35s ease}

/* ---- 4. Tabs ---- */
.lh-tabs{display:flex;gap:0;border-bottom:2px solid #e2e8f0;margin-bottom:20px}
.lh-tab{padding:10px 24px;font-size:15px;font-weight:600;color:#64748b;cursor:pointer;border:none;background:none;position:relative;transition:color .2s;font-family:'Inter',sans-serif}
.lh-tab:hover{color:#0f172a}
.lh-tab.active{color:#1f5f46}
.lh-tab.active::after{content:'';position:absolute;bottom:-2px;left:0;right:0;height:3px;background:#1f5f46;border-radius:3px 3px 0 0}

/* ---- 5. Avatar ---- */
.lh-av{width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:18px;color:#fff;flex-shrink:0}

/* ---- 6. Status dot ---- */
.lh-dot{width:9px;height:9px;border-radius:50%;display:inline-block;flex-shrink:0}
.lh-dot.active{background:#16a34a}
.lh-dot.probation{background:#d97706}
.lh-dot.on-leave{background:#2563eb}
.lh-dot.joining-soon{background:#7c3aed}
.lh-dot.terminated{background:#dc2626}

/* ---- 7. Profile link ---- */
.lh-link{font-size:13px;color:#1f5f46;text-decoration:none;font-weight:500;transition:color .2s}
.lh-link:hover{color:#163f2f;text-decoration:underline}

/* ---- 8. Status badge ---- */
.lh-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 12px;border-radius:999px;font-size:12px;font-weight:600}
.lh-badge.active{background:#dcfce7;color:#15803d}
.lh-badge.probation{background:#fef3c7;color:#92400e}
.lh-badge.on-leave{background:#dbeafe;color:#1d4ed8}
.lh-badge.joining-soon{background:#ede9fe;color:#5b21b6}
.lh-badge.terminated{background:#fee2e2;color:#b91c1c}

/* ---- 9. Card status ---- */
.lh-card-status{margin-left:auto;display:flex;flex-direction:column;align-items:center;gap:4px;flex-shrink:0}

/* ---- 10. Toggle switch (terminated) ---- */
.terminated-toggle-wrap{display:flex;align-items:center;gap:10px;font-size:13px;color:#475569;font-weight:500}
.terminated-switch{position:relative;width:48px;height:26px;cursor:pointer;flex-shrink:0}
.terminated-switch input{opacity:0;width:0;height:0;position:absolute}
.terminated-switch .slider{position:absolute;inset:0;background:#cbd5e1;border-radius:26px;transition:background .25s}
.terminated-switch .slider::before{content:'';position:absolute;width:20px;height:20px;left:3px;top:3px;background:#fff;border-radius:50%;transition:transform .25s;box-shadow:0 1px 3px rgba(0,0,0,0.25)}
.terminated-switch input:checked+.slider{background:#1a4d3e}
.terminated-switch input:checked+.slider::before{transform:translateX(22px)}

/* ---- 11. View switcher ---- */
.view-sw{display:flex;align-items:center}
.view-sw-btn{display:flex;align-items:center;justify-content:center;gap:8px;height:40px;padding:0 20px;font-size:13px;font-weight:600;border:1.5px solid #1f5f46;cursor:pointer;transition:all .2s;white-space:nowrap;font-family:'Inter',sans-serif}
.view-sw-btn:first-child{border-radius:8px 0 0 8px;border-right:none}
.view-sw-btn:last-child{border-radius:0 8px 8px 0}
.view-sw-btn.active{background:#1f5f46;color:#fff}
.view-sw-btn.active svg{stroke:#fff}
.view-sw-btn:not(.active){background:#fff;color:#1f5f46}
.view-sw-btn:not(.active) svg{stroke:#1f5f46}
.view-sw-btn:not(.active):hover{background:#eaf6f0}

/* ---- 12. Toolbar ---- */
.lh-toolbar{display:flex;flex-direction:column;gap:14px;margin-bottom:22px}
.lh-toolbar-row{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
.lh-toolbar-filters-row{align-items:flex-end}
.lh-toolbar-left{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.lh-toolbar-right{display:flex;align-items:center;gap:12px;flex-wrap:wrap}
.lh-add-btn{display:inline-flex;align-items:center;gap:8px;padding:9px 22px;background:#1f5f46;color:#fff;border:none;border-radius:999px;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none;transition:background .2s;font-family:'Inter',sans-serif}
.lh-add-btn:hover{background:#163f2f;color:#fff}
.lh-add-btn svg{width:16px;height:16px}

/* ---- 13. Filter inputs / selects ---- */
.lh-filter-group{display:flex;flex-direction:column;align-items:flex-start;gap:8px}
.lh-filter-group label{font-size:11px;font-weight:700;color:#4a5568;text-transform:uppercase;letter-spacing:0.5px;white-space:nowrap}
.lh-filter-input,.lh-filter-select{height:40px;padding:0 12px;font-size:13px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;color:#1e293b;font-family:'Inter',sans-serif;min-width:130px;outline:none;transition:border-color .2s;box-sizing:border-box}
.lh-filter-input:focus,.lh-filter-select:focus{border-color:#1f5f46}
.lh-filter-input{min-width:200px}

/* ---- 14. List view table ---- */
.lh-table-wrap{overflow-x:auto}
.lh-table{width:100%;border-collapse:collapse;font-size:14px}
.lh-table thead{position:sticky;top:0;z-index:2}
.lh-table th{background:#f8fafc;padding:12px 16px;text-align:left;font-weight:600;color:#475569;font-size:13px;border-bottom:2px solid #e2e8f0;white-space:nowrap}
.lh-table td{padding:12px 16px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
.lh-table tbody tr:nth-child(even){background:#fafbfc}
.lh-table tbody tr:hover{background:#f1f5f9}
.lh-table tbody tr.terminated-row{opacity:.55}

/* ---- 15. Table avatar & name ---- */
.lh-table-name{display:flex;align-items:center;gap:12px}
.lh-table-name .lh-av{width:36px;height:36px;font-size:13px}
.lh-table-name span{font-weight:600;color:#0f172a}

/* ---- 16. View profile button ---- */
.lh-view-profile-btn{display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:6px 14px;border:1.5px solid #1f5f46;border-radius:8px;background:#fff;color:#1f5f46;font-size:12px;font-weight:600;cursor:pointer;text-decoration:none;transition:background .2s,color .2s;font-family:'Inter',sans-serif;white-space:nowrap}
.lh-view-profile-btn:hover{background:#1f5f46;color:#fff}
.lh-view-profile-btn svg{width:14px;height:14px;flex-shrink:0}

/* ---- 17. Pagination ---- */
.lh-pagination{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;padding:14px 0;margin-top:8px;border-top:1px solid #e2e8f0;font-size:13px;color:#475569}
.lh-pagination-left{display:flex;align-items:center;gap:8px}
.lh-pagination-left select{padding:5px 10px;border:1px solid #cbd5e1;border-radius:6px;font-size:13px;font-family:'Inter',sans-serif;background:#fff;color:#1e293b;outline:none;cursor:pointer;appearance:none;-webkit-appearance:none;-moz-appearance:none;text-align:left;width:64px;box-sizing:border-box;transition:border-color .2s}
.lh-pagination-left select:focus{border-color:#1f5f46}
.lh-pagination-right{display:flex;align-items:center;gap:6px}
.lh-pagination-info{margin-right:10px;font-weight:500}
.lh-pg-btn{padding:6px 12px;border:1px solid #cbd5e1;border-radius:6px;background:#fff;cursor:pointer;font-size:13px;font-weight:500;transition:background .2s,border-color .2s;font-family:'Inter',sans-serif}
.lh-pg-btn:hover:not(:disabled){background:#f1f5f9;border-color:#94a3b8}
.lh-pg-btn:disabled{opacity:.4;cursor:not-allowed}
.lh-pg-btn.active{background:#1f5f46;color:#fff;border-color:#1f5f46}

/* ---- 18. Manage teams ---- */
.mt-toolbar{display:flex;flex-direction:column;align-items:flex-start;gap:16px;margin-bottom:22px}
.mt-search{display:flex;flex-direction:column;align-items:flex-start;gap:8px;width:100%;max-width:320px}
.mt-search label{font-size:11px;font-weight:700;color:#4a5568;text-transform:uppercase;letter-spacing:0.5px}
.mt-search input{height:40px;padding:0 12px;font-size:13px;border:1px solid #cbd5e1;border-radius:8px;width:100%;max-width:320px;outline:none;font-family:'Inter',sans-serif;transition:border-color .2s;box-sizing:border-box}
.mt-search input:focus{border-color:#1f5f46}
.mt-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:18px}
.mt-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px;display:flex;align-items:center;gap:16px;transition:box-shadow .2s,transform .2s}
.mt-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.07);transform:translateY(-1px)}
.mt-card-av{width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:17px;color:#fff;flex-shrink:0}
.mt-card-info{flex:1;min-width:0}
.mt-card-name{font-size:15px;font-weight:700;color:#0f172a;margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.mt-card-count{font-size:13px;color:#64748b}
.mt-card-actions{display:flex;gap:8px}
.mt-card-actions button{width:34px;height:34px;border-radius:8px;border:1px solid #e2e8f0;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .2s,border-color .2s}
.mt-card-actions button:hover{background:#f1f5f9;border-color:#94a3b8}
.mt-card-actions button svg{width:16px;height:16px;color:#475569}

/* ---- 19. Manage teams empty state ---- */
.mt-empty{text-align:center;padding:48px 20px;color:#94a3b8;font-size:15px}

/* ---- 20. Modal overlay & dialog ---- */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:1000;opacity:0;visibility:hidden;transition:opacity .25s,visibility .25s}
.modal-overlay.open{opacity:1;visibility:visible}
.modal-dialog{background:#fff;border-radius:16px;width:520px;max-width:94vw;max-height:88vh;display:flex;flex-direction:column;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:modalSlideIn .3s ease}
.modal-header{background:#1a4d3e;color:#fff;padding:18px 24px;display:flex;align-items:center;justify-content:space-between}
.modal-header h3{margin:0;font-size:17px;font-weight:700}
.modal-close-btn{background:none;border:none;color:#fff;cursor:pointer;padding:4px;border-radius:6px;transition:background .2s;display:flex;align-items:center;justify-content:center}
.modal-close-btn:hover{background:rgba(255,255,255,.15)}
.modal-close-btn svg{width:20px;height:20px}
.modal-body{padding:24px;overflow-y:auto;flex:1}
.modal-footer{padding:16px 24px;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:10px}

/* ---- 21. Modal buttons ---- */
.modal-btn{padding:9px 22px;border-radius:999px;font-size:14px;font-weight:600;cursor:pointer;transition:background .2s,color .2s,border-color .2s;font-family:'Inter',sans-serif}
.modal-btn.outline{background:#fff;color:#1f5f46;border:1.5px solid #1f5f46}
.modal-btn.outline:hover{background:#eaf6f0}
.modal-btn.filled{background:#1f5f46;color:#fff;border:1.5px solid #1f5f46}
.modal-btn.filled:hover{background:#163f2f;border-color:#163f2f}

/* ---- 22. Modal input group ---- */
.modal-input-group{margin-bottom:18px}
.modal-input-group label{display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px}
.modal-input-group input{width:100%;padding:10px 14px;border:1px solid #cbd5e1;border-radius:8px;font-size:14px;font-family:'Inter',sans-serif;outline:none;transition:border-color .2s;box-sizing:border-box}
.modal-input-group input:focus{border-color:#1f5f46}

/* ---- 23. Employee selection (step 2) ---- */
.emp-sel-toolbar{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:14px;flex-wrap:wrap}
.emp-sel-search{padding:7px 12px;border:1px solid #cbd5e1;border-radius:8px;font-size:13px;min-width:180px;outline:none;font-family:'Inter',sans-serif;transition:border-color .2s}
.emp-sel-search:focus{border-color:#1f5f46}
.emp-sel-actions{display:flex;gap:8px}
.emp-sel-actions button{padding:5px 14px;border:1px solid #cbd5e1;border-radius:6px;background:#fff;cursor:pointer;font-size:12px;font-weight:500;font-family:'Inter',sans-serif;transition:background .2s}
.emp-sel-actions button:hover{background:#f1f5f9}
.emp-group{margin-bottom:14px}
.emp-group-header{display:flex;align-items:center;gap:6px;cursor:pointer;user-select:none;padding:6px 0;font-size:14px;font-weight:600;color:#334155}
.emp-group-header .chv{width:14px;height:14px}
.emp-group-body{display:grid;grid-template-columns:repeat(2,1fr);gap:8px;padding:6px 0}
.emp-sel-card{display:flex;align-items:center;gap:10px;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:10px;cursor:pointer;transition:border-color .2s,background .2s;user-select:none}
.emp-sel-card:hover{border-color:#94a3b8}
.emp-sel-card.selected{border-color:#16a34a;background:#f0fdf4}
.emp-sel-card .emp-sel-av{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:#fff;flex-shrink:0}
.emp-sel-card .emp-sel-info{flex:1;min-width:0}
.emp-sel-card .emp-sel-name{font-size:13px;font-weight:600;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.emp-sel-card .emp-sel-pos{font-size:11px;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.emp-sel-check{width:22px;height:22px;border-radius:50%;border:2px solid #cbd5e1;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .2s,border-color .2s}
.emp-sel-card.selected .emp-sel-check{background:#16a34a;border-color:#16a34a}
.emp-sel-card.selected .emp-sel-check svg{display:block}
.emp-sel-check svg{display:none;width:13px;height:13px;color:#fff}

/* ---- 24. Confirm modal ---- */
.confirm-overlay{position:fixed;inset:0;background:rgba(0,0,0,.55);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:1100;opacity:0;visibility:hidden;transition:opacity .25s,visibility .25s}
.confirm-overlay.open{opacity:1;visibility:visible}
.confirm-modal{background:#fff;border-radius:16px;width:420px;max-width:92vw;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2);animation:modalSlideIn .3s ease}
.confirm-header{background:#1a4d3e;color:#fff;padding:16px 24px;display:flex;align-items:center;justify-content:space-between}
.confirm-header h3{margin:0;font-size:16px;font-weight:700}
.confirm-close-btn{background:none;border:none;color:#fff;cursor:pointer;padding:4px;border-radius:6px;transition:background .2s;display:flex;align-items:center;justify-content:center}
.confirm-close-btn:hover{background:rgba(255,255,255,.15)}
.confirm-close-btn svg{width:18px;height:18px}
.confirm-body{padding:24px}
.confirm-message{margin:0;font-size:14px;color:#475569;line-height:1.6}
.confirm-footer{padding:16px 24px;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:10px}
.confirm-btn{padding:9px 22px;border-radius:999px;font-size:14px;font-weight:600;cursor:pointer;transition:background .2s,color .2s;font-family:'Inter',sans-serif}
.confirm-btn.cancel{background:#fff;color:#1f5f46;border:1.5px solid #1f5f46}
.confirm-btn.cancel:hover{background:#eaf6f0}
.confirm-btn.delete{background:#1f5f46;color:#fff;border:1.5px solid #1f5f46}
.confirm-btn.delete:hover{background:#163f2f;border-color:#163f2f}

/* ---- 25. Toast ---- */
.lh-toast{position:fixed;bottom:32px;left:50%;transform:translateX(-50%) translateY(20px);background:#1a4d3e;color:#fff;padding:12px 28px;border-radius:10px;font-size:14px;font-weight:500;box-shadow:0 8px 30px rgba(0,0,0,.18);z-index:1200;opacity:0;visibility:hidden;transition:opacity .3s,transform .3s,visibility .3s;font-family:'Inter',sans-serif}
.lh-toast.show{opacity:1;visibility:visible;transform:translateX(-50%) translateY(0)}

/* ---- 26. Animations ---- */
@keyframes fadeSlideIn{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
@keyframes modalSlideIn{from{opacity:0;transform:scale(.95) translateY(-10px)}to{opacity:1;transform:scale(1) translateY(0)}}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}

/* ---- 27. Responsive ---- */
@media(max-width:1024px){
    .mt-grid{grid-template-columns:1fr}
    .emp-group-body{grid-template-columns:1fr}
}
@media(max-width:768px){
    .lh-toolbar-row{flex-direction:column;align-items:stretch}
    .lh-toolbar-right{justify-content:flex-end}
    .lh-filter-input{min-width:120px}
    .lh-pagination{flex-direction:column;align-items:flex-start}
    .cards-grid{grid-template-columns:repeat(2,minmax(0,1fr))!important}
}
@media(max-width:480px){
    .cards-grid{grid-template-columns:1fr!important}
    .view-sw{flex-direction:column;width:100%}
    .view-sw-btn{width:100%}
    .view-sw-btn:first-child{border-radius:8px 8px 0 0;border-right:1.5px solid #1f5f46;border-bottom:none}
    .view-sw-btn:last-child{border-radius:0 0 8px 8px}
}

/* ---- Utility ---- */
.cards-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px;margin-bottom:24px;padding-right:20px}
.division-heading{font-size:14px;font-weight:600;color:#64748b;margin:18px 0 10px;text-transform:uppercase;letter-spacing:.5px}
.hidden{display:none!important}

/* ---- 28. Page header ---- */
/* (header now handled by admin.layout's page-title/page-description) */
</style>

{{-- ===================== PAGE CONTENT ===================== --}}
<div class="lh-wrap">

    {{-- ===== TABS ===== --}}
    <div class="lh-tabs">
        <button class="lh-tab active" data-tab="employees" onclick="switchTab('employees')">Employees</button>
        <button class="lh-tab" data-tab="manage-teams" onclick="switchTab('manage-teams')">Manage teams</button>
    </div>

    {{-- ===============================================
         TAB 1: EMPLOYEES
         =============================================== --}}
    <div id="tab-employees">

        {{-- Toolbar --}}
        <div class="lh-toolbar">
            {{-- Row 1 --}}
            <div class="lh-toolbar-row">
                <div class="lh-toolbar-left">
                    <a href="{{ route('admin.linkers-hub.add-employee') }}" class="lh-add-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Add employee
                    </a>
                </div>
                <div class="lh-toolbar-right">
                    <div class="terminated-toggle-wrap">
                        <label class="terminated-switch">
                            <input type="checkbox" id="terminated-toggle" onchange="toggleTerminated()">
                            <span class="slider"></span>
                        </label>
                        <span>Include terminated employees</span>
                    </div>
                </div>
            </div>

            {{-- Row 2 --}}
            <div class="lh-toolbar-row lh-toolbar-filters-row">
                <div class="lh-toolbar-left">
                    <div class="lh-filter-group">
                        <label for="search-input">FIND</label>
                        <input type="text" id="search-input" class="lh-filter-input" placeholder="Name, job title" oninput="applySearch()">
                    </div>
                    <div class="lh-filter-group">
                        <label for="filter-by">FILTER BY</label>
                        <select id="filter-by" class="lh-filter-select" onchange="applySearch()">
                            <option value="">All</option>
                            @foreach($groupedEmployees as $divisionName => $employees)
                                <option value="{{ $divisionName }}">{{ $divisionName }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="lh-filter-group">
                        <label for="sort-by">SORT BY</label>
                        <select id="sort-by" class="lh-filter-select" onchange="applySearch()">
                            <option value="name-asc">Name A–Z</option>
                            <option value="name-desc">Name Z–A</option>
                            <option value="recent">Recently added</option>
                        </select>
                    </div>
                    <div class="lh-filter-group">
                        <label for="status-filter">STATUS</label>
                        <select id="status-filter" class="lh-filter-select" onchange="applySearch()">
                            <option value="">All</option>
                            <option value="active">Active</option>
                            <option value="probation">Probation</option>
                            <option value="on-leave">On Leave</option>
                            <option value="joining-soon">Joining Soon</option>
                            <option value="terminated">Terminated</option>
                        </select>
                    </div>
                </div>
                <div class="lh-toolbar-right">
                    <div class="view-sw">
                        <button class="view-sw-btn active" data-view="teams" onclick="switchView('teams')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                            Teams View
                        </button>
                        <button class="view-sw-btn" data-view="list" onclick="switchView('list')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                            List View
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== TEAMS VIEW ===== --}}
        <div id="teams-view">

            {{-- Active Employees Section --}}
            <div class="lh-toggle" onclick="toggleSection('active-section')">
                <svg class="chv" id="chv-active-section" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                <h3>Active Employees <span class="lh-count">({{ $totalActive }})</span></h3>
            </div>
            <div class="lh-section-body" id="active-section">
                @foreach($groupedEmployees as $divisionName => $employees)
                    @php $activeEmps = $employees->whereNotIn('status', ['terminated', 'joining-soon']); @endphp
                    @if($activeEmps->count())
                        <div class="division-heading" data-division="{{ $divisionName }}">{{ $divisionName }}</div>
                        <div class="cards-grid">
                            @foreach($activeEmps as $employee)
                                @php
                                    $empStatus = $employee->status ?? 'active';
                                    if (!in_array($empStatus, ['active','probation','on-leave'])) {
                                        $empStatus = 'active';
                                    }
                                    $statusLabels = [
                                        'active'    => 'Active',
                                        'probation' => 'Probation',
                                        'on-leave'  => 'On Leave',
                                    ];
                                    $statusLabel = $statusLabels[$empStatus];
                                @endphp
                                <div class="lh-card {{ $empStatus !== 'active' ? $empStatus : '' }}" data-id="{{ $employee->id }}" data-name="{{ strtolower($employee->full_name) }}" data-team="{{ $divisionName }}" data-status="{{ $empStatus }}">
                                    <div class="lh-av" style="background:{{ $employee->avatar_color }}">
                                        @if($employee->avatar_path)
                                            <img src="{{ route('admin.linkers-hub.serve-avatar', $employee->id) }}" alt="{{ $employee->full_name }}" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                                        @else
                                            {{ $employee->initials }}
                                        @endif
                                    </div>
                                    <div class="lh-card-info">
                                        <div class="lh-name">{{ $employee->full_name }}</div>
                                        <div class="lh-pos">{{ $employee->position_title ?? 'No position' }}</div>
                                        <a href="{{ route('admin.linkers-hub.employee-profile', $employee->id) }}" class="lh-link">View full profile</a>
                                    </div>
                                    <div class="lh-card-status">
                                        <span class="lh-dot {{ $empStatus }}"></span>
                                        <span class="lh-badge {{ $empStatus }}">{{ $statusLabel }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @endforeach
            </div>

            {{-- Joining Soon Section --}}
            @if(isset($joiningEmployees) && $joiningEmployees->count())
            <div class="lh-toggle" onclick="toggleSection('joining-section')" style="margin-top:16px">
                <svg class="chv" id="chv-joining-section" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                <h3>Joining Soon <span class="lh-count">({{ $joiningEmployees->count() }})</span></h3>
            </div>
            <div class="lh-section-body" id="joining-section">
                <div class="cards-grid">
                    @foreach($joiningEmployees as $employee)
                        <div class="lh-card joining-soon" data-id="{{ $employee->id }}" data-name="{{ strtolower($employee->full_name) }}" data-team="" data-status="joining-soon">
                            <div class="lh-av" style="background:{{ $employee->avatar_color }}">
                                @if($employee->avatar_path)
                                    <img src="{{ route('admin.linkers-hub.serve-avatar', $employee->id) }}" alt="{{ $employee->full_name }}" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                                @else
                                    {{ $employee->initials }}
                                @endif
                            </div>
                            <div class="lh-card-info">
                                <div class="lh-name">{{ $employee->full_name }}</div>
                                <div class="lh-pos">{{ $employee->position_title ?? 'No position' }}</div>
                                <a href="{{ route('admin.linkers-hub.employee-profile', $employee->id) }}" class="lh-link">View full profile</a>
                            </div>
                            <div class="lh-card-status">
                                <span class="lh-dot joining-soon"></span>
                                <span class="lh-badge joining-soon">Joining Soon</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Terminated Employees Section --}}
            <div id="terminated-section-wrap" class="hidden">
                <div class="lh-toggle" onclick="toggleSection('terminated-section')">
                    <svg class="chv" id="chv-terminated-section" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    <h3>Terminated <span class="lh-count">({{ $totalTerminated }})</span></h3>
                </div>
                <div class="lh-section-body" id="terminated-section">
                    <div class="cards-grid">
                        @foreach($terminatedEmployees as $employee)
                            <div class="lh-card terminated" data-id="{{ $employee->id }}" data-name="{{ strtolower($employee->full_name) }}" data-team="" data-status="terminated">
                                <div class="lh-av" style="background:{{ $employee->avatar_color }}">
                                    @if($employee->avatar_path)
                                        <img src="{{ route('admin.linkers-hub.serve-avatar', $employee->id) }}" alt="{{ $employee->full_name }}" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                                    @else
                                        {{ $employee->initials }}
                                    @endif
                                </div>
                                <div class="lh-card-info">
                                    <div class="lh-name">{{ $employee->full_name }}</div>
                                    <div class="lh-pos">{{ $employee->position_title ?? 'No position' }}</div>
                                    <a href="{{ route('admin.linkers-hub.employee-profile', $employee->id) }}" class="lh-link">View full profile</a>
                                </div>
                                <div class="lh-card-status">
                                    <span class="lh-dot terminated"></span>
                                    <span class="lh-badge terminated">Terminated</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== LIST VIEW ===== --}}
        <div id="list-view" class="hidden">
            <div class="lh-table-wrap">
                <table class="lh-table">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Job Title</th>
                            <th>Team(s)</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="list-tbody">
                        @foreach($groupedEmployees as $divisionName => $employees)
                            @foreach($employees as $employee)
                                @php
                                    $empStatus = $employee->status ?? 'active';
                                    if (!in_array($empStatus, ['active','probation','on-leave','joining-soon','terminated'])) {
                                        $empStatus = 'active';
                                    }
                                    $isTerminated = $empStatus === 'terminated';
                                    $statusLabels = [
                                        'active'       => 'Active',
                                        'probation'    => 'Probation',
                                        'on-leave'     => 'On Leave',
                                        'joining-soon' => 'Joining Soon',
                                        'terminated'   => 'Terminated',
                                    ];
                                    $statusLabel = $statusLabels[$empStatus];
                                @endphp
                                <tr class="{{ $isTerminated ? 'terminated-row' : '' }}" data-id="{{ $employee->id }}" data-name="{{ strtolower($employee->full_name) }}" data-team="{{ $divisionName }}" data-status="{{ $empStatus }}">
                                    <td>
                                        <div class="lh-table-name">
                                            <div class="lh-av" style="background:{{ $employee->avatar_color }}">
                                                @if($employee->avatar_path)
                                                    <img src="{{ route('admin.linkers-hub.serve-avatar', $employee->id) }}" alt="{{ $employee->full_name }}" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">
                                                @else
                                                    {{ $employee->initials }}
                                                @endif
                                            </div>
                                            <span>{{ $employee->full_name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $employee->position_title ?? 'No position' }}</td>
                                    <td>{{ $divisionName }}</td>
                                    <td>
                                        <span class="lh-badge {{ $empStatus }}">
                                            <span class="lh-dot {{ $empStatus }}"></span>
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.linkers-hub.employee-profile', $employee->id) }}" class="lh-view-profile-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            View Profile
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="lh-pagination" id="pagination-bar">
                <div class="lh-pagination-left">
                    <span>Show</span>
                    <select id="per-page" onchange="changePerPage()">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                    <span>per page</span>
                </div>
                <div class="lh-pagination-right">
                    <span class="lh-pagination-info" id="pagination-info"></span>
                    <div id="pagination-buttons"></div>
                </div>
            </div>
        </div>

    </div>

    {{-- ===============================================
         TAB 2: MANAGE TEAMS
         =============================================== --}}
    <div id="tab-manage-teams" class="hidden">
        <div class="mt-toolbar">
            <button class="lh-add-btn" onclick="openAddTeamModal()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add a new team
            </button>
            <div class="mt-search">
                <label for="team-search">FIND</label>
                <input type="text" id="team-search" placeholder="Team name" oninput="filterTeams()">
            </div>
        </div>
        <div class="mt-grid" id="teams-grid"></div>
    </div>

</div>

{{-- ===== Modal container ===== --}}
<div class="modal-overlay" id="modal-overlay">
    <div class="modal-dialog" id="modal-dialog"></div>
</div>

{{-- ===== Confirm Modal ===== --}}
<div class="confirm-overlay" id="confirm-overlay">
    <div class="confirm-modal">
        <div class="confirm-header">
            <h3 class="confirm-title" id="confirm-title">Are you sure?</h3>
            <button class="confirm-close-btn" id="confirm-close-top" onclick="closeConfirmModal()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="confirm-body">
            <p class="confirm-message" id="confirm-message">This action cannot be undone.</p>
        </div>
        <div class="confirm-footer">
            <button class="confirm-btn cancel" id="confirm-cancel" onclick="closeConfirmModal()">Cancel</button>
            <button class="confirm-btn delete" id="confirm-delete">Delete</button>
        </div>
    </div>
</div>

{{-- ===== Toast ===== --}}
<div class="lh-toast" id="lh-toast"></div>


{{-- ===================== INLINE JAVASCRIPT ===================== --}}
<script>
// ================================================================
// DATA — built from Laravel server-side
// ================================================================
var TEAMS = [
@foreach($allDivisions as $division)
@php
    $divEmployees = $groupedEmployees[$division->name] ?? null;
    $divMemberCount = $divEmployees ? $divEmployees->count() : 0;
@endphp
    { id: {{ $division->id }}, name: {!! json_encode($division->name) !!}, color: '{{ ['#D4A017','#1a6b4f','#FF9800','#2196F3','#9C27B0','#00BCD4','#795548','#607D8B'][$loop->index % 8] }}', memberCount: {{ $divMemberCount }} },
@endforeach
];

var ALL_EMPLOYEES = [
@foreach($groupedEmployees as $divisionName => $employees)
@foreach($employees as $emp)
    { id: {{ $emp->id }}, name: {!! json_encode($emp->full_name) !!}, position: {!! json_encode($emp->position_title ?? 'No position') !!}, team: {!! json_encode($divisionName) !!}, initials: {!! json_encode($emp->initials) !!}, color: {!! json_encode($emp->avatar_color) !!} },
@endforeach
@endforeach
];

// ================================================================
// STATE
// ================================================================
var currentView = 'teams';       // 'teams' | 'list'
var currentTab  = 'employees';   // 'employees' | 'manage-teams'
var listPage    = 1;
var perPage     = 10;

// Modal state
var modalMode        = null;     // 'add' | 'edit'
var modalStep        = 1;        // 1 | 2
var modalTeamName    = '';
var modalEditTeamId  = null;
var selectedEmployees = [];      // array of employee IDs

// AJAX URLs for team CRUD
var storeTeamUrl   = '{{ route("admin.linkers-hub.store-team", [], false) }}';
var updateTeamUrl  = '{{ route("admin.linkers-hub.update-team", ["id" => "__ID__"], false) }}';
var deleteTeamUrl  = '{{ route("admin.linkers-hub.delete-team", ["id" => "__ID__"], false) }}';
var csrfToken      = '{{ csrf_token() }}';

// ================================================================
// 1. TAB SWITCHING
// ================================================================
function switchTab(tab) {
    currentTab = tab;
    document.querySelectorAll('.lh-tab').forEach(function(t) {
        t.classList.toggle('active', t.getAttribute('data-tab') === tab);
    });
    document.getElementById('tab-employees').classList.toggle('hidden', tab !== 'employees');
    document.getElementById('tab-manage-teams').classList.toggle('hidden', tab !== 'manage-teams');

    if (tab === 'manage-teams') {
        renderManageTeams();
    }
}

// ================================================================
// 2. VIEW SWITCHING (Teams / List)
// ================================================================
function switchView(view) {
    currentView = view;
    document.querySelectorAll('.view-sw-btn').forEach(function(b) {
        b.classList.toggle('active', b.getAttribute('data-view') === view);
    });
    document.getElementById('teams-view').classList.toggle('hidden', view !== 'teams');
    document.getElementById('list-view').classList.toggle('hidden', view !== 'list');

    if (view === 'list') {
        listPage = 1;
        renderPagination();
    }
    applySearch();
}

// ================================================================
// 3. TERMINATED TOGGLE
// ================================================================
function toggleTerminated() {
    var on = document.getElementById('terminated-toggle').checked;

    // Teams view: show/hide terminated section
    var termWrap = document.getElementById('terminated-section-wrap');
    if (termWrap) termWrap.classList.toggle('hidden', !on);

    // List view: show/hide terminated rows
    document.querySelectorAll('#list-tbody tr[data-status="terminated"]').forEach(function(row) {
        row.classList.toggle('hidden', !on);
    });

    if (currentView === 'list') {
        listPage = 1;
        renderPagination();
    }
}

// ================================================================
// SORTING LOGIC
// ================================================================
function applySort() {
    var sortBy = document.getElementById('sort-by').value;

    // 1. Sort List View rows
    var tbody = document.getElementById('list-tbody');
    if (tbody) {
        var rows = Array.prototype.slice.call(tbody.querySelectorAll('tr'));
        rows.sort(function(a, b) {
            var valA, valB;
            if (sortBy === 'name-asc' || sortBy === 'name-desc') {
                valA = a.getAttribute('data-name') || '';
                valB = b.getAttribute('data-name') || '';
                if (valA < valB) return sortBy === 'name-asc' ? -1 : 1;
                if (valA > valB) return sortBy === 'name-asc' ? 1 : -1;
                return 0;
            } else if (sortBy === 'recent') {
                valA = parseInt(a.getAttribute('data-id'), 10) || 0;
                valB = parseInt(b.getAttribute('data-id'), 10) || 0;
                return valB - valA; // Descending (largest ID first)
            }
            return 0;
        });
        rows.forEach(function(row) {
            tbody.appendChild(row);
        });
    }

    // 2. Sort Teams View cards
    document.querySelectorAll('#teams-view .cards-grid').forEach(function(grid) {
        var cards = Array.prototype.slice.call(grid.querySelectorAll('.lh-card'));
        cards.sort(function(a, b) {
            var valA, valB;
            if (sortBy === 'name-asc' || sortBy === 'name-desc') {
                valA = a.getAttribute('data-name') || '';
                valB = b.getAttribute('data-name') || '';
                if (valA < valB) return sortBy === 'name-asc' ? -1 : 1;
                if (valA > valB) return sortBy === 'name-asc' ? 1 : -1;
                return 0;
            } else if (sortBy === 'recent') {
                valA = parseInt(a.getAttribute('data-id'), 10) || 0;
                valB = parseInt(b.getAttribute('data-id'), 10) || 0;
                return valB - valA; // Descending
            }
            return 0;
        });
        cards.forEach(function(card) {
            grid.appendChild(card);
        });
    });
}

// ================================================================
// 4. SEARCH / FILTER
// ================================================================
function applySearch() {
    var query  = (document.getElementById('search-input').value || '').toLowerCase().trim();
    var team   = document.getElementById('filter-by').value;
    var status = document.getElementById('status-filter').value;
    var showTerminated = document.getElementById('terminated-toggle').checked;

    // Apply client-side sorting
    applySort();

    if (currentView === 'teams') {
        // Filter cards in active section
        document.querySelectorAll('#teams-view .lh-card').forEach(function(card) {
            var name       = card.getAttribute('data-name') || '';
            var cardTeam   = card.getAttribute('data-team') || '';
            var cardStatus = card.getAttribute('data-status') || '';

            var matchName   = !query || name.indexOf(query) !== -1;
            var matchTeam   = !team || cardTeam === team;
            var matchStatus = !status || cardStatus === status;
            var termVisible = cardStatus !== 'terminated' || showTerminated;

            card.classList.toggle('hidden', !(matchName && matchTeam && matchStatus && termVisible));
        });

        // Hide division headings if all cards in that division are hidden
        document.querySelectorAll('#teams-view .division-heading').forEach(function(heading) {
            var grid = heading.nextElementSibling;
            if (grid && grid.classList.contains('cards-grid')) {
                var visibleCards = grid.querySelectorAll('.lh-card:not(.hidden)');
                heading.classList.toggle('hidden', visibleCards.length === 0);
                grid.classList.toggle('hidden', visibleCards.length === 0);
            }
        });
    }

    if (currentView === 'list') {
        document.querySelectorAll('#list-tbody tr').forEach(function(row) {
            var name      = row.getAttribute('data-name') || '';
            var rowTeam   = row.getAttribute('data-team') || '';
            var rowStatus = row.getAttribute('data-status') || '';

            var matchName   = !query || name.indexOf(query) !== -1;
            var matchTeam   = !team || rowTeam === team;
            var matchStatus = !status || rowStatus === status;
            var termVisible = rowStatus !== 'terminated' || showTerminated;

            row.classList.toggle('hidden', !(matchName && matchTeam && matchStatus && termVisible));
        });
        listPage = 1;
        renderPagination();
    }
}

// ================================================================
// 5. LIST VIEW PAGINATION
// ================================================================
function getVisibleRows() {
    var rows = [];
    document.querySelectorAll('#list-tbody tr').forEach(function(row) {
        if (!row.classList.contains('hidden')) {
            rows.push(row);
        }
    });
    return rows;
}

function renderPagination() {
    var allRows     = document.querySelectorAll('#list-tbody tr');
    var visibleRows = getVisibleRows();
    var total       = visibleRows.length;
    var totalPages  = Math.max(1, Math.ceil(total / perPage));

    if (listPage > totalPages) listPage = totalPages;

    var start = (listPage - 1) * perPage;
    var end   = start + perPage;

    // Hide all, then show only current page
    allRows.forEach(function(row) { row.style.display = 'none'; });
    visibleRows.forEach(function(row, i) {
        row.style.display = (i >= start && i < end) ? '' : 'none';
    });

    // Info text
    var infoEl = document.getElementById('pagination-info');
    if (total === 0) {
        infoEl.textContent = '0 records';
    } else {
        infoEl.textContent = (start + 1) + ' \u2013 ' + Math.min(end, total) + ' of ' + total + ' records';
    }

    // Buttons
    var btnsEl = document.getElementById('pagination-buttons');
    var html = '';
    html += '<button class="lh-pg-btn" onclick="goToPage(' + (listPage - 1) + ')" ' + (listPage <= 1 ? 'disabled' : '') + '>Previous</button>';
    for (var p = 1; p <= totalPages; p++) {
        html += '<button class="lh-pg-btn ' + (p === listPage ? 'active' : '') + '" onclick="goToPage(' + p + ')">' + p + '</button>';
    }
    html += '<button class="lh-pg-btn" onclick="goToPage(' + (listPage + 1) + ')" ' + (listPage >= totalPages ? 'disabled' : '') + '>Next</button>';
    btnsEl.innerHTML = html;
}

function goToPage(page) {
    var total      = getVisibleRows().length;
    var totalPages = Math.max(1, Math.ceil(total / perPage));
    if (page < 1 || page > totalPages) return;
    listPage = page;
    renderPagination();
}

function changePerPage() {
    perPage  = parseInt(document.getElementById('per-page').value, 10);
    listPage = 1;
    renderPagination();
}

// ================================================================
// COLLAPSIBLE SECTIONS
// ================================================================
function toggleSection(sectionId) {
    var body = document.getElementById(sectionId);
    var chv  = document.getElementById('chv-' + sectionId);
    if (!body) return;

    if (body.style.maxHeight && body.style.maxHeight !== '0px') {
        body.style.maxHeight = '0px';
        body.style.overflow  = 'hidden';
        if (chv) chv.classList.add('collapsed');
    } else {
        body.style.maxHeight = body.scrollHeight + 'px';
        body.style.overflow  = 'visible';
        if (chv) chv.classList.remove('collapsed');
    }
}

// Initialize sections as expanded
document.addEventListener('DOMContentLoaded', function() {
    ['active-section', 'terminated-section'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            el.style.maxHeight = el.scrollHeight + 'px';
            el.style.overflow  = 'visible';
        }
    });
});

// ================================================================
// 6. MANAGE TEAMS — RENDER
// ================================================================
function renderManageTeams() {
    var grid = document.getElementById('teams-grid');
    if (TEAMS.length === 0) {
        grid.innerHTML = '<div class="mt-empty" style="grid-column:1/-1">No teams created yet. Click "Add a new team" to get started.</div>';
        return;
    }

    var html = '';
    TEAMS.forEach(function(team) {
        var initials   = getTeamInitials(team.name);
        var lightColor = lightenColor(team.color, 40);
        html += '<div class="mt-card" data-team-id="' + team.id + '" data-team-name="' + escapeHTML(team.name).toLowerCase() + '">';
        html += '  <div class="mt-card-av" style="background:linear-gradient(135deg,' + team.color + ',' + lightColor + ')">' + escapeHTML(initials) + '</div>';
        html += '  <div class="mt-card-info">';
        html += '    <div class="mt-card-name">' + escapeHTML(team.name) + '</div>';
        html += '    <div class="mt-card-count">' + team.memberCount + ' member' + (team.memberCount !== 1 ? 's' : '') + '</div>';
        html += '  </div>';
        html += '  <div class="mt-card-actions">';
        html += '    <button onclick="openEditTeamModal(' + team.id + ')" title="Edit team">';
        html += '      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>';
        html += '    </button>';
        html += '    <button onclick="confirmDeleteTeam(' + team.id + ')" title="Delete team">';
        html += '      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>';
        html += '    </button>';
        html += '  </div>';
        html += '</div>';
    });
    grid.innerHTML = html;
}

// ================================================================
// 7. TEAM SEARCH (Manage Teams)
// ================================================================
function filterTeams() {
    var query = (document.getElementById('team-search').value || '').toLowerCase().trim();
    document.querySelectorAll('#teams-grid .mt-card').forEach(function(card) {
        var name = card.getAttribute('data-team-name') || '';
        card.classList.toggle('hidden', query && name.indexOf(query) === -1);
    });
}

// ================================================================
// 8. GET TEAM INITIALS
// ================================================================
function getTeamInitials(name) {
    if (!name) return '?';
    var words = name.trim().split(/\s+/);
    if (words.length === 1) return words[0].substring(0, 2).toUpperCase();
    return (words[0][0] + words[1][0]).toUpperCase();
}

// ================================================================
// 9. LIGHTEN COLOR
// ================================================================
function lightenColor(hex, percent) {
    hex = hex.replace('#', '');
    if (hex.length === 3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
    var r = parseInt(hex.substring(0, 2), 16);
    var g = parseInt(hex.substring(2, 4), 16);
    var b = parseInt(hex.substring(4, 6), 16);
    r = Math.min(255, Math.round(r + (255 - r) * percent / 100));
    g = Math.min(255, Math.round(g + (255 - g) * percent / 100));
    b = Math.min(255, Math.round(b + (255 - b) * percent / 100));
    return '#' + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1);
}

// ================================================================
// 10. OPEN ADD TEAM MODAL
// ================================================================
function openAddTeamModal() {
    modalMode       = 'add';
    modalStep       = 1;
    modalTeamName   = '';
    modalEditTeamId = null;
    selectedEmployees = [];
    renderModalStep();
    document.getElementById('modal-overlay').classList.add('open');
}

// ================================================================
// 11. OPEN EDIT TEAM MODAL
// ================================================================
function openEditTeamModal(teamId) {
    var team = TEAMS.find(function(t) { return t.id === teamId; });
    if (!team) return;
    modalMode       = 'edit';
    modalStep       = 1;
    modalTeamName   = team.name;
    modalEditTeamId = teamId;

    // Pre-select employees from this team
    selectedEmployees = ALL_EMPLOYEES.filter(function(e) {
        return e.team === team.name;
    }).map(function(e) { return e.id; });

    renderModalStep();
    document.getElementById('modal-overlay').classList.add('open');
}

// ================================================================
// 12. RENDER MODAL STEP
// ================================================================
function renderModalStep() {
    var dialog = document.getElementById('modal-dialog');
    var isAdd  = modalMode === 'add';
    var html   = '';

    if (modalStep === 1) {
        // Step 1: Team name
        html += '<div class="modal-header">';
        html += '  <h3>' + (isAdd ? 'Add a new team' : 'Edit team') + '</h3>';
        html += '  <button class="modal-close-btn" onclick="closeModal()">';
        html += '    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
        html += '  </button>';
        html += '</div>';
        html += '<div class="modal-body">';
        html += '  <div class="modal-input-group">';
        html += '    <label for="modal-team-name">Team name</label>';
        html += '    <input type="text" id="modal-team-name" placeholder="Enter team name" value="' + escapeHTML(modalTeamName) + '" oninput="modalTeamName=this.value">';
        html += '  </div>';
        html += '</div>';
        html += '<div class="modal-footer">';
        html += '  <button class="modal-btn outline" onclick="closeModal()">Close</button>';
        html += '  <button class="modal-btn filled" onclick="goToStep2()">Select employees</button>';
        html += '</div>';
    } else {
        // Step 2: Employee selection
        var title = isAdd
            ? 'Assign employees to \u201C' + escapeHTML(modalTeamName) + '\u201D'
            : 'Edit members of \u201C' + escapeHTML(modalTeamName) + '\u201D';

        html += '<div class="modal-header">';
        html += '  <h3>' + title + '</h3>';
        html += '  <button class="modal-close-btn" onclick="closeModal()">';
        html += '    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
        html += '  </button>';
        html += '</div>';
        html += '<div class="modal-body" id="modal-emp-body">';
        html += renderEmployeeSelection();
        html += '</div>';
        html += '<div class="modal-footer">';
        html += '  <button class="modal-btn outline" onclick="goToStep1()">Back</button>';
        html += '  <button class="modal-btn filled" id="modal-save-btn" onclick="saveTeam()">Save (' + selectedEmployees.length + ')</button>';
        html += '</div>';
    }

    dialog.innerHTML = html;
}

function goToStep2() {
    modalTeamName = (document.getElementById('modal-team-name') || {}).value || modalTeamName;
    if (!modalTeamName.trim()) {
        var input = document.getElementById('modal-team-name');
        if (input) { input.style.borderColor = '#dc2626'; input.focus(); }
        return;
    }
    modalStep = 2;
    renderModalStep();
}

function goToStep1() {
    modalStep = 1;
    renderModalStep();
}

// ================================================================
// 13. RENDER EMPLOYEE SELECTION
// ================================================================
function renderEmployeeSelection(searchQuery) {
    searchQuery = (searchQuery || '').toLowerCase();
    var html = '';

    // Toolbar: search + select all / deselect all (rendered only once)
    html += '<div class="emp-sel-toolbar">';
    html += '  <input type="text" class="emp-sel-search" id="emp-sel-search-input" placeholder="Search employees" oninput="refreshEmpSelectionList()" value="' + escapeHTML(searchQuery) + '">';
    html += '  <div class="emp-sel-actions">';
    html += '    <button onclick="selectAllEmployees()">Select all</button>';
    html += '    <button onclick="deselectAllEmployees()">Deselect all</button>';
    html += '  </div>';
    html += '</div>';

    // Employee list container (this part gets refreshed)
    html += '<div id="emp-sel-list-container">';
    html += renderEmployeeList(searchQuery);
    html += '</div>';

    return html;
}

function renderEmployeeList(searchQuery) {
    searchQuery = (searchQuery || '').toLowerCase();
    var html = '';

    // Group employees by team
    var groups = {};
    ALL_EMPLOYEES.forEach(function(emp) {
        var team = emp.team || 'Unassigned';
        if (!groups[team]) groups[team] = [];
        // Filter by search
        if (!searchQuery || emp.name.toLowerCase().indexOf(searchQuery) !== -1 || emp.position.toLowerCase().indexOf(searchQuery) !== -1) {
            groups[team].push(emp);
        }
    });

    var groupKeys = Object.keys(groups);
    if (groupKeys.length === 0) {
        html += '<div class="mt-empty">No employees found.</div>';
        return html;
    }

    groupKeys.forEach(function(teamName) {
        var emps = groups[teamName];
        if (emps.length === 0) return;

        html += '<div class="emp-group">';
        html += '  <div class="emp-group-header" onclick="toggleEmpGroup(this)">';
        html += '    <svg class="chv" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>';
        html += '    <span>' + escapeHTML(teamName) + ' (' + emps.length + ')</span>';
        html += '  </div>';
        html += '  <div class="emp-group-body">';
        emps.forEach(function(emp) {
            html += renderEmpSelectCard(emp, selectedEmployees.indexOf(emp.id) !== -1);
        });
        html += '  </div>';
        html += '</div>';
    });

    return html;
}

// ================================================================
// 14. RENDER SINGLE EMPLOYEE SELECT CARD
// ================================================================
function renderEmpSelectCard(emp, isSelected) {
    var cls  = 'emp-sel-card' + (isSelected ? ' selected' : '');
    var html = '';
    html += '<div class="' + cls + '" onclick="toggleEmployee(' + emp.id + ')">';
    html += '  <div class="emp-sel-av" style="background:' + emp.color + '">' + escapeHTML(emp.initials) + '</div>';
    html += '  <div class="emp-sel-info">';
    html += '    <div class="emp-sel-name">' + escapeHTML(emp.name) + '</div>';
    html += '    <div class="emp-sel-pos">' + escapeHTML(emp.position) + '</div>';
    html += '  </div>';
    html += '  <div class="emp-sel-check">';
    html += '    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>';
    html += '  </div>';
    html += '</div>';
    return html;
}

function toggleEmployee(empId) {
    var idx = selectedEmployees.indexOf(empId);
    if (idx === -1) {
        selectedEmployees.push(empId);
    } else {
        selectedEmployees.splice(idx, 1);
    }
    refreshEmpSelection();
}

function selectAllEmployees() {
    ALL_EMPLOYEES.forEach(function(emp) {
        if (selectedEmployees.indexOf(emp.id) === -1) {
            selectedEmployees.push(emp.id);
        }
    });
    refreshEmpSelection();
}

function deselectAllEmployees() {
    selectedEmployees = [];
    refreshEmpSelection();
}

function refreshEmpSelection(searchVal) {
    var searchInput = document.getElementById('emp-sel-search-input');
    var query = searchVal !== undefined ? searchVal : (searchInput ? searchInput.value : '');
    // Only update the list container, NOT the whole body (preserves search input)
    var listContainer = document.getElementById('emp-sel-list-container');
    if (listContainer) {
        listContainer.innerHTML = renderEmployeeList(query);
    } else {
        // Fallback: full re-render
        var body = document.getElementById('modal-emp-body');
        if (body) body.innerHTML = renderEmployeeSelection(query);
    }
    updateSaveButton();
}

// Called by search input oninput — only refreshes employee list, NOT the search bar
function refreshEmpSelectionList() {
    var searchInput = document.getElementById('emp-sel-search-input');
    var query = searchInput ? searchInput.value : '';
    var listContainer = document.getElementById('emp-sel-list-container');
    if (listContainer) {
        listContainer.innerHTML = renderEmployeeList(query);
    }
    updateSaveButton();
}

function toggleEmpGroup(header) {
    var chv  = header.querySelector('.chv');
    var body = header.nextElementSibling;
    if (!body) return;
    if (body.style.display === 'none') {
        body.style.display = '';
        if (chv) chv.classList.remove('collapsed');
    } else {
        body.style.display = 'none';
        if (chv) chv.classList.add('collapsed');
    }
}

// ================================================================
// 15. UPDATE SAVE BUTTON
// ================================================================
function updateSaveButton() {
    var btn = document.getElementById('modal-save-btn');
    if (btn) btn.textContent = 'Save (' + selectedEmployees.length + ')';
}

// ================================================================
// 16. SAVE TEAM (AJAX)
// ================================================================
function teamAjax(url, method, data, onSuccess, onError) {
    var ajaxMethod = method;
    var ajaxUrl = url;
    // Use Laravel method spoofing for PUT and DELETE
    if (method === 'PUT' || method === 'DELETE') {
        ajaxMethod = 'POST';
        ajaxUrl = url + (url.indexOf('?') === -1 ? '?' : '&') + '_method=' + method;
    }
    fetch(ajaxUrl, {
        method: ajaxMethod,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(function(response) {
        return response.json().then(function(jsonData) {
            return { status: response.status, body: jsonData };
        });
    })
    .then(function(res) {
        if (res.status >= 200 && res.status < 300) {
            if (onSuccess) onSuccess(res.body);
        } else {
            if (onError) onError(res.body);
        }
    })
    .catch(function(err) {
        console.error('AJAX error:', err);
        if (onError) onError({ message: 'A network error occurred.' });
    });
}

function saveTeam() {
    var saveBtn = document.getElementById('modal-save-btn');
    if (saveBtn) { saveBtn.disabled = true; saveBtn.textContent = 'Saving...'; }

    var payload = {
        name: modalTeamName,
        employee_ids: selectedEmployees
    };

    if (modalMode === 'add') {
        teamAjax(storeTeamUrl, 'POST', payload, function(data) {
            if (data.success && data.team) {
                var colors = ['#D4A017','#1a6b4f','#FF9800','#2196F3','#9C27B0','#00BCD4','#795548','#607D8B'];
                TEAMS.push({
                    id: data.team.id,
                    name: data.team.name,
                    color: colors[(data.team.id - 1) % colors.length],
                    memberCount: data.team.memberCount
                });
                // Update ALL_EMPLOYEES team assignments locally
                ALL_EMPLOYEES.forEach(function(emp) {
                    if (selectedEmployees.indexOf(emp.id) !== -1) {
                        emp.team = data.team.name;
                    }
                });
                showToast('Team \u201C' + data.team.name + '\u201D created successfully!');
                closeModal();
                renderManageTeams();
            }
        }, function(errData) {
            alert(errData.message || 'Error creating team.');
            if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = 'Save (' + selectedEmployees.length + ')'; }
        });
    } else {
        var url = updateTeamUrl.replace('__ID__', modalEditTeamId);
        teamAjax(url, 'PUT', payload, function(data) {
            if (data.success && data.team) {
                var team = TEAMS.find(function(t) { return t.id === modalEditTeamId; });
                if (team) {
                    var oldName = team.name;
                    team.name = data.team.name;
                    team.memberCount = data.team.memberCount;
                    // Update ALL_EMPLOYEES team assignments locally
                    ALL_EMPLOYEES.forEach(function(emp) {
                        if (emp.team === oldName) emp.team = 'No team';
                        if (selectedEmployees.indexOf(emp.id) !== -1) {
                            emp.team = data.team.name;
                        }
                    });
                }
                showToast('Team \u201C' + data.team.name + '\u201D updated successfully!');
                closeModal();
                renderManageTeams();
            }
        }, function(errData) {
            alert(errData.message || 'Error updating team.');
            if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = 'Save (' + selectedEmployees.length + ')'; }
        });
    }
}

// ================================================================
// 17. CLOSE MODAL
// ================================================================
function closeModal() {
    document.getElementById('modal-overlay').classList.remove('open');
}

// Close modal on overlay click
document.getElementById('modal-overlay').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// ================================================================
// 18. CONFIRM MODAL (Delete)
// ================================================================
var confirmCallback = null;

function showConfirmModal(title, message, actionLabel, onConfirm) {
    document.getElementById('confirm-title').textContent   = title;
    document.getElementById('confirm-message').textContent = message;
    document.getElementById('confirm-delete').textContent  = actionLabel;
    confirmCallback = onConfirm;
    document.getElementById('confirm-overlay').classList.add('open');
}

function closeConfirmModal() {
    document.getElementById('confirm-overlay').classList.remove('open');
    confirmCallback = null;
}

document.getElementById('confirm-delete').addEventListener('click', function() {
    if (typeof confirmCallback === 'function') confirmCallback();
    closeConfirmModal();
});

document.getElementById('confirm-overlay').addEventListener('click', function(e) {
    if (e.target === this) closeConfirmModal();
});

function confirmDeleteTeam(teamId) {
    var team = TEAMS.find(function(t) { return t.id === teamId; });
    if (!team) return;
    showConfirmModal(
        'Delete ' + team.name + '?',
        'Are you sure you want to delete \u201C' + team.name + '\u201D? This action cannot be undone.',
        'Delete',
        function() {
            var url = deleteTeamUrl.replace('__ID__', teamId);
            teamAjax(url, 'DELETE', {}, function(data) {
                if (data.success) {
                    // Update ALL_EMPLOYEES: unassign employees from this team
                    ALL_EMPLOYEES.forEach(function(emp) {
                        if (emp.team === team.name) emp.team = 'No team';
                    });
                    TEAMS = TEAMS.filter(function(t) { return t.id !== teamId; });
                    renderManageTeams();
                    showToast('Team \u201C' + team.name + '\u201D deleted successfully!');
                }
            }, function(errData) {
                alert(errData.message || 'Error deleting team.');
            });
        }
    );
}

// ================================================================
// 19. TOAST NOTIFICATION
// ================================================================
function showToast(message) {
    var toast = document.getElementById('lh-toast');
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(function() {
        toast.classList.remove('show');
    }, 3000);
}

// ================================================================
// 20. ESCAPE HTML
// ================================================================
function escapeHTML(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

// ================================================================
// INIT
// ================================================================
document.addEventListener('DOMContentLoaded', function() {
    switchTab('employees');
    switchView('teams');
});
</script>

@endsection
