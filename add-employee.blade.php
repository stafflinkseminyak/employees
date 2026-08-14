@extends('admin.layouts.app')
@section('title', 'Add Employee')
@section('page-title', 'Linkers Hub')
@section('content')
<style>
    /* ── Dropdown Arrow for all selects ── */
    select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 12px center / 14px !important;
        padding-right: 34px !important;
    }
    /* ── Arrow / Stepper Tabs ── */
    .ae-stepper { display: flex; justify-content: center; margin-bottom: 32px; width: 100%; flex-shrink: 0; }
    .ae-stepper-inner { display: flex; gap: 6px; width: 100%; }
    .ae-step {
        position: relative;
        padding: 12px 28px 12px 28px;
        font-size: 14px;
        font-weight: 600;
        cursor: default;
        border: none;
        white-space: nowrap;
        transition: background 0.2s, color 0.2s;
        background: #f5e6c8;
        color: #7a6840;
        /* Arrow shape: notch on left, point on right */
        clip-path: polygon(0% 0%, calc(100% - 16px) 0%, 100% 50%, calc(100% - 16px) 100%, 0% 100%, 16px 50%);
        padding-left: 34px;
        padding-right: 28px;
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 1;
        pointer-events: none;
    }
    /* First tab: flat left edge, arrow right */
    .ae-step:first-child {
        clip-path: polygon(0% 0%, calc(100% - 16px) 0%, 100% 50%, calc(100% - 16px) 100%, 0% 100%);
        padding-left: 20px;
        border-radius: 4px 0 0 4px;
    }
    /* Last tab: notch left, flat right */
    .ae-step:last-child {
        clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 0% 100%, 16px 50%);
        padding-right: 20px;
        border-radius: 0 4px 4px 0;
    }
    .ae-step.active { background: #2e7d5e; color: #fff; cursor: default; }
    .ae-step.locked { opacity: 0.5; cursor: default; }

    /* ── Form Inputs ── */
    .ae-name-input {
        border: 1.5px solid #d1d5db;
        border-radius: 6px;
        padding: 12px 14px;
        font-size: 15px;
        color: #333;
        width: 100%;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
        background: #fff;
    }
    .ae-name-input:focus { border-color: #c9a84c; box-shadow: 0 0 0 2px rgba(201,168,76,0.15); }
    .ae-name-input::placeholder { color: #555; font-weight: 500; }

    /* ── Save Button ── */
    .ae-save-btn {
        width: 42px; height: 42px;
        border-radius: 50%;
        background: #c9a84c;
        color: #fff;
        border: none;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .ae-save-btn:hover:not(:disabled) { background: #8a6d2b; transform: scale(1.06); }
    .ae-save-btn:disabled { background: #a68b3c; cursor: not-allowed; opacity: 0.7; }

    /* ── Bulk Import Button ── */
    .ae-bulk-btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 10px 24px;
        border: 2px solid #e63364;
        color: #e63364;
        border-radius: 6px;
        font-size: 14px; font-weight: 600;
        background: #fff;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .ae-bulk-btn:hover { background: #fef1f5; }


    /* ── Form Card ── */
    .ae-form-card { background: #fafbfc; border-radius: 8px; border: 1px solid #e5e7eb; padding: 20px 24px; }

    /* ── Tab Content ── */
    .ae-tab-content { display: none; }
    .ae-tab-content.active { display: flex; flex-direction: column; flex: 1; min-height: 0; }

    /* ── Chat Widget ── */
    .ae-chat-wrap { position: fixed; bottom: 85px; right: 24px; z-index: 50; display: flex; flex-direction: column; align-items: flex-end; gap: 8px; }
    .ae-chat-bubble {
        background: #fff; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.12);
        padding: 10px 16px; font-size: 13px; color: #333;
        display: flex; align-items: center; gap: 10px; white-space: nowrap;
    }
    .ae-chat-bubble .close-x { background: none; border: none; cursor: pointer; color: #999; font-size: 16px; padding: 0; line-height: 1; }
    .ae-chat-bubble .close-x:hover { color: #666; }
    .ae-chat-bot {
        width: 50px; height: 50px; border-radius: 50%;
        background: linear-gradient(135deg, #4dd0e1, #26c6da);
        color: #fff; border: none; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 14px rgba(38,198,218,0.35);
        transition: transform 0.2s;
    }
    .ae-chat-bot:hover { transform: scale(1.08); }
    .ae-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    /* ── Confirm Modal ── */
    .ae-modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.45);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        animation: aeModalFadeIn 0.2s ease;
    }
    .ae-modal-overlay.active { display: flex; }
    .ae-modal-box {
        background: #fff;
        border-radius: 12px;
        width: 440px;
        max-width: 90vw;
        box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        overflow: hidden;
        animation: aeModalSlideIn 0.25s ease;
    }
    .ae-modal-header {
        background: #1a4d3e;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
    }
    .ae-modal-header h3 {
        font-size: 16px;
        font-weight: 700;
        margin: 0;
    }
    .ae-modal-close {
        background: none;
        border: none;
        color: rgba(255,255,255,0.8);
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
    }
    .ae-modal-close:hover {
        color: #fff;
        background: rgba(255,255,255,0.15);
    }
    .ae-modal-body {
        padding: 24px 20px;
    }
    .ae-modal-body p {
        font-size: 14px;
        color: #4b5563;
        margin: 0;
        line-height: 1.6;
    }
    .ae-modal-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px 20px;
    }
    .ae-modal-cancel {
        border: 2px solid #c9a84c;
        color: #c9a84c;
        background: #fff;
        border-radius: 6px;
        padding: 10px 24px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .ae-modal-cancel:hover {
        background: #fdf8ed;
        border-color: #b8953d;
        color: #b8953d;
    }
    .ae-modal-confirm {
        background: #e63364;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 10px 28px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .ae-modal-confirm:hover {
        background: #d12a57;
    }
    @keyframes aeModalFadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    @keyframes aeModalSlideIn {
        from { transform: translateY(-20px) scale(0.96); opacity: 0; }
        to { transform: translateY(0) scale(1); opacity: 1; }
    }
    #filledState {
        display: none;
        flex-direction: column;
        flex: 1;
        min-height: 0;
    }
    .ae-filled-layout {
        display: flex;
        gap: 0;
        flex: 1;
        min-height: 0;
        overflow: hidden;
    }
    .ae-filled-sidebar {
        width: 340px; flex-shrink: 0;
        border: 1px solid #e5e7eb; border-radius: 10px;
        padding: 20px; background: #fff;
        display: flex; flex-direction: column;
        margin-right: 24px;
        height: 100%;
        box-sizing: border-box;
    }
    #employeeList {
        flex: 1;
        overflow-y: auto;
        padding-right: 4px;
        margin-bottom: 8px;
    }
    .ae-filled-main {
        flex: 1;
        min-width: 0;
        overflow-y: auto;
        padding-right: 8px;
    }
    .ae-form-footer {
        flex-shrink: 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0 0 0;
        margin-top: 12px;
        border-top: 1px solid #e5e7eb;
        background: #fff;
    }
    .ae-sidebar-add {
        flex-shrink: 0;
        display: flex; gap: 8px; align-items: center;
        margin-top: 16px; padding-top: 12px;
        border-top: 1px solid #f3f4f6;
    }
    .ae-sidebar-add input { flex: 1; min-width: 0; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 10px; font-size: 13px; }
    .ae-sidebar-add .ae-save-btn { width: 34px; height: 34px; min-width: 34px; flex-shrink: 0; }
    @media (max-width: 900px) {
        .ae-form-grid { grid-template-columns: 1fr; }
        .ae-filled-layout { flex-direction: column; height: auto; overflow: visible; }
        .ae-filled-sidebar { width: 100%; margin-right: 0; margin-bottom: 16px; height: auto; }
        #employeeList { overflow-y: visible; height: auto; }
        .ae-filled-main { overflow-y: visible; height: auto; padding-right: 0; }
        .ae-form-footer { position: static; margin-top: 16px; }
    }
    /* ── Step 2 and 3 Styles ── */
    .ae-tab-section { width: 100%; height: auto; }
    .emp-type-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 12px; }
    @media (max-width: 768px) { .emp-type-grid { grid-template-columns: 1fr; } }
    .emp-type-card {
        border: 1.5px solid #e5e7eb; border-radius: 8px; padding: 20px; background: #fff;
        cursor: pointer; display: flex; gap: 16px; align-items: flex-start; transition: all 0.2s;
    }
    .emp-type-card:hover { border-color: #cbd5e1; background: #f8fafc; }
    .emp-type-card.selected { border-color: #2e7d5e; background: #f0fdf4; }
    .emp-type-radio-circle {
        width: 20px; height: 20px; border-radius: 50%; border: 2px solid #cbd5e1;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; transition: all 0.2s;
    }
    .emp-type-card.selected .emp-type-radio-circle { border-color: #2e7d5e; }
    .emp-type-radio-inner { width: 10px; height: 10px; border-radius: 50%; background: #2e7d5e; transform: scale(0); transition: transform 0.2s; }
    .emp-type-card.selected .emp-type-radio-inner { transform: scale(1); }
    .emp-type-card-title { font-size: 15px; font-weight: 700; color: #1f2937; margin-bottom: 6px; }
    .emp-type-card-desc { font-size: 13px; color: #4b5563; line-height: 1.5; }

    /* Base style for employee item in narrow view */
    .ae-emp-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 14px;
        border-radius: 8px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }

    /* ── Close Panel & Full-width Table View Styles ── */
    .ae-list-header {
        display: none;
        grid-template-columns: 1fr 1fr 1fr 40px;
        gap: 16px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 700;
        color: #4b5563;
        border-bottom: 1.5px solid #e5e7eb;
        margin-bottom: 12px;
        align-items: center;
    }
    .ae-filled-layout.details-closed .ae-list-header {
        display: grid;
    }
    .ae-filled-layout.details-closed .ae-filled-sidebar {
        width: 100% !important;
        margin-right: 0 !important;
    }
    .ae-filled-layout.details-closed .ae-filled-main {
        display: none !important;
    }
    .ae-filled-layout.details-closed .ae-emp-item {
        display: grid !important;
        grid-template-columns: 1fr 1fr 1fr 40px !important;
        gap: 16px !important;
        justify-content: stretch !important;
        align-items: center !important;
    }
    .ae-emp-item .emp-name {
        font-size: 14px;
        font-weight: 500;
        color: #333;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ae-emp-item .emp-email,
    .ae-emp-item .emp-job-title {
        display: none;
        font-size: 13px;
        color: #4b5563;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .ae-filled-layout.details-closed .ae-emp-item .emp-email,
    .ae-filled-layout.details-closed .ae-emp-item .emp-job-title {
        display: inline-block !important;
    }
    .ae-filled-layout.details-closed .ae-sidebar-add {
        max-width: 600px;
    }
    #sbAddEnterText {
        display: none;
    }
    .ae-filled-layout.details-closed #sbAddEnterText {
        display: inline-block !important;
    }
    @media (max-width: 640px) {
        .ae-list-header {
            grid-template-columns: 1fr 40px !important;
        }
        .ae-list-header span:nth-child(2),
        .ae-list-header span:nth-child(3) {
            display: none !important;
        }
        .ae-filled-layout.details-closed .ae-emp-item {
            grid-template-columns: 1fr 40px !important;
        }
        .ae-filled-layout.details-closed .ae-emp-item .emp-email,
        .ae-filled-layout.details-closed .ae-emp-item .emp-job-title {
            display: none !important;
        }
    }
</style>

<div class="bg-white rounded-lg border border-gray-200 shadow-sm flex flex-col -mt-2" style="height: calc(100vh - 6rem); overflow: hidden;">


    {{-- ═══ Main Content ═══ --}}
    <div class="flex-1 px-8 py-6 w-full flex flex-col" style="min-height: 0;">

        {{-- Arrow Stepper Tabs --}}
        <div class="ae-stepper">
            <div class="ae-stepper-inner">
                <div class="ae-step active" id="stepDetailsBtn">Employee details</div>
                <div class="ae-step locked" id="stepEmployment">Employment details</div>
                <div class="ae-step locked" id="stepSummary">Summary</div>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-5 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2 flex-shrink-0" id="successMsg">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-[14px] font-medium">{{ session('success') }}</span>
        </div>
        @endif

        {{-- Empty State (Initial form) --}}
        <div id="emptyState" class="flex-grow flex flex-col justify-between">
            <div style="border: 1.5px solid #e5e7eb; border-radius: 12px; padding: 30px 30px 24px; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
                <p class="text-center text-[15px] text-gray-500 mb-6">No employees added, please start entering your first employee below to get started.</p>
                <div class="ae-form-card mb-0">
                    <form action="{{ route('admin.linkers-hub.store-employee') }}" method="POST" id="addEmployeeForm">
                        @csrf
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <input type="text" name="first_name" class="ae-name-input" placeholder="First name" required id="firstNameInput" value="{{ old('first_name') }}">
                                <p class="text-red-500 text-[12px] mt-1 font-medium hidden" id="firstNameError"></p>
                            </div>
                            <div class="flex-1">
                                <input type="text" name="last_name" class="ae-name-input" placeholder="Last name" required id="lastNameInput" value="{{ old('last_name') }}">
                                <p class="text-red-500 text-[12px] mt-1 font-medium hidden" id="lastNameError"></p>
                            </div>
                            <button type="submit" class="ae-save-btn" title="Save employee" id="saveBtn" disabled>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </button>
                            <div class="text-[13px] text-gray-600 leading-tight whitespace-nowrap font-semibold">or press enter to<br>save</div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="flex flex-col items-center text-center pb-8" style="margin-top: 120px;">
                <div style="margin-bottom: 16px;">
                    <svg width="90" height="90" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="15" y="8" width="52" height="68" rx="6" fill="#e8edf5" stroke="#c5cfe0" stroke-width="1.5"/>
                        <rect x="30" y="3" width="22" height="12" rx="3" fill="#d0daea" stroke="#b0bdd0" stroke-width="1"/>
                        <line x1="26" y1="30" x2="56" y2="30" stroke="#b0bdd0" stroke-width="2.5" stroke-linecap="round"/>
                        <line x1="26" y1="40" x2="50" y2="40" stroke="#b0bdd0" stroke-width="2.5" stroke-linecap="round"/>
                        <line x1="26" y1="50" x2="53" y2="50" stroke="#b0bdd0" stroke-width="2.5" stroke-linecap="round"/>
                        <line x1="26" y1="60" x2="43" y2="60" stroke="#b0bdd0" stroke-width="2.5" stroke-linecap="round"/>
                        <circle cx="74" cy="70" r="18" fill="#dbeafe" stroke="#93c5fd" stroke-width="1.5"/>
                        <path d="M74 61v18M65 70h18" stroke="#60a5fa" stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </div>
                <p class="text-[14px] text-gray-700 mb-6 leading-relaxed font-semibold">Enter your employees details above to get started,<br>or bulk import up to 1,000 employees at once.</p>
                <a href="#" class="ae-bulk-btn">Bulk import employees</a>
            </div>
        </div>

        {{-- Filled State (Stepper contents when there are employees) --}}
        <div id="filledState" style="display:none; flex: 1; flex-direction: column; min-height: 0;">
            <div class="ae-filled-layout">
                <!-- Sidebar -->
                <div class="ae-filled-sidebar">
                    <div style="display:flex;gap:8px;margin-bottom:12px;">
                        <div style="flex:1;display:flex;align-items:center;border:1px solid #d1d5db;border-radius:6px;padding:8px 10px;background:#fff;">
                            <svg style="width:16px;height:16px;color:#9ca3af;margin-right:8px;flex-shrink:0;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                            <input type="text" id="sbSearchInput" placeholder="Search" style="border:none;outline:none;font-size:13px;width:100%;background:transparent;">
                            <button id="sbClearSearch" style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:0;margin-left:4px;flex-shrink:0;" title="Clear"><svg style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg></button>
                        </div>
                        <select id="sbFilter" style="border:1px solid #d1d5db;border-radius:6px;padding:8px 12px;background:#fff;cursor:pointer;font-size:13px;color:#374151;outline:none;font-weight:600;">
                            <option value="added">Added</option>
                            <option value="first_name">First name</option>
                            <option value="last_name">Last name</option>
                        </select>
                    </div>
                    <div style="text-align:right;font-size:12px;color:#6b7280;margin-bottom:8px;" id="recordCount">1 record</div>
                    <div id="employeeListHeader" class="ae-list-header">
                        <span>Name</span>
                        <span>Email address</span>
                        <span>Job title</span>
                        <span></span>
                    </div>
                    <div id="employeeList">
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border:2px solid #c9a84c;border-radius:8px;background:#fffdf5;margin-bottom:8px;cursor:pointer;">
                            <span id="empListName" style="font-size:14px;font-weight:500;color:#333;"></span>
                            <button onclick="startOver()" style="background:none;border:none;cursor:pointer;color:#9ca3af;padding:2px;" title="Delete"><svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button>
                        </div>
                    </div>
                    <div class="ae-sidebar-add">
                        <input type="text" placeholder="First name" id="sbFirstName">
                        <input type="text" placeholder="Last name" id="sbLastName">
                        <button class="ae-save-btn" disabled id="sbSaveBtn"><svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg></button>
                        <div id="sbAddEnterText" style="font-size:12px;color:#4b5563;white-space:nowrap;font-weight:600;margin-left:4px;">or press enter to save</div>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div class="ae-filled-main">
                    <div id="empDetailHeader" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;padding-bottom:12px;border-bottom:2px solid #d1d5db;">
                        <h2 id="empDetailName" style="font-size:20px;font-weight:700;color:#1f2937;"></h2>
                        <button onclick="closeDetailsPanel()" style="background:none;border:none;color:#c9a84c;font-size:14px;font-weight:600;cursor:pointer;">Close</button>
                    </div>

                    <!-- Step 1 Section: Employee Details -->
                    <div class="ae-tab-section" id="section-employee-details">
                        <div style="border:1px solid #e5e7eb;border-radius:10px;padding:24px;margin-bottom:16px;background:#fff;"><h3 style="font-size:16px;font-weight:700;color:#1f2937;margin-bottom:20px;">Basic details</h3><div class="ae-form-grid"><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Title</label><select id="empTitle" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;background:#fff;"><option value="">Select Title</option><option>Mr</option><option>Mrs</option><option>Ms</option><option>Miss</option><option>Dr</option></select></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">First name <span style="color:#ef4444;font-size:12px;float:right;">Required</span></label><input type="text" id="empFirstName" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Middle name</label><input type="text" id="empMiddleName" placeholder="Middle name" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Last name <span style="color:#ef4444;font-size:12px;float:right;">Required</span></label><input type="text" id="empLastName" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Gender</label><select id="empGender" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;background:#fff;"><option>Unspecified</option><option>Male</option><option>Female</option></select></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Date of birth <span style="color:#ef4444;font-size:12px;float:right;">Required</span></label><input type="date" id="empDob" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"><div id="dobDuplicateWarning" style="display:none;margin-top:6px;padding:8px 12px;background:#fef2f2;border:1px solid #fca5a5;border-radius:6px;color:#e63364;font-size:12px;font-weight:500;"></div></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Email address <span style="color:#ef4444;font-size:12px;float:right;">Required</span></label><input type="email" id="empEmail" placeholder="Email address" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Mobile number</label><input type="text" id="empMobile" placeholder="Mobile number" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Work phone</label><input type="text" id="empWorkPhone" placeholder="Work phone" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Job title</label><input type="text" id="empJobTitle" placeholder="Job title" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div></div><div style="margin-top:16px;"><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Employment start date <span style="color:#ef4444;font-size:12px;">Required</span></label><input type="date" id="empStartDate" style="width:calc(50% - 8px);border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div></div>
                        <div style="border:1px solid #e5e7eb;border-radius:10px;padding:24px;margin-bottom:16px;background:#fff;"><h3 style="font-size:16px;font-weight:700;color:#1f2937;margin-bottom:20px;">Address details</h3><div class="ae-form-grid"><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Address 1</label><input type="text" id="empAddr1" placeholder="Address 1" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Address 2</label><input type="text" id="empAddr2" placeholder="Address 2" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Address 3</label><input type="text" id="empAddr3" placeholder="Address 3" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Suburb/City</label><input type="text" id="empCity" placeholder="Suburb/City" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Territory</label><input type="text" id="empTerritory" placeholder="Territory" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Postcode</label><input type="text" id="empPostcode" placeholder="Postcode" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div></div></div>
                        <div style="border:1px solid #e5e7eb;border-radius:10px;padding:24px;margin-bottom:16px;background:#fff;" id="emergencyContactSection"><h3 style="font-size:16px;font-weight:700;color:#1f2937;margin-bottom:16px;">Emergency contact</h3><div id="ecSavedContacts"></div><div id="ecInfoBanner" style="display:flex;align-items:center;gap:10px;padding:12px 16px;background:#f0f9ff;border-radius:8px;margin-bottom:16px;"><svg style="width:20px;height:20px;color:#3b82f6;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span style="font-size:13px;color:#374151;">Add an emergency contact in case something unexpected happens.</span></div><button id="ecAddBtn" onclick="showEmergencyContactForm()" style="background:#e63364;color:#fff;border:none;border-radius:6px;padding:10px 20px;font-size:13px;font-weight:600;cursor:pointer;transition:background .2s;">Add Emergency Contact</button><div id="ecFormContainer" style="display:none;margin-top:16px;border:1px solid #e5e7eb;border-radius:10px;padding:24px;background:#fafafa;"><h4 style="font-size:15px;font-weight:700;color:#1f2937;margin-bottom:16px;" id="ecFormTitle">Add emergency contact</h4><div class="ae-form-grid"><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">First name <span style="color:#ef4444;font-size:12px;float:right;">Required</span></label><input type="text" id="ecFirstName" placeholder="First name" oninput="updateEcSaveBtnState()" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;box-sizing:border-box;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Last name</label><input type="text" id="ecLastName" placeholder="Last name" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;box-sizing:border-box;"></div></div><p style="font-size:12px;color:#e63364;font-weight:600;margin:12px 0 8px;">At least one contact number required</p><div class="ae-form-grid"><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Mobile phone</label><input type="text" id="ecMobile" placeholder="Mobile phone" oninput="updateEcSaveBtnState()" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;box-sizing:border-box;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Home phone</label><input type="text" id="ecHomePhone" placeholder="Home phone" oninput="updateEcSaveBtnState()" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;box-sizing:border-box;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Work phone</label><input type="text" id="ecWorkPhone" placeholder="Work phone" oninput="updateEcSaveBtnState()" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;box-sizing:border-box;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Address 1</label><input type="text" id="ecAddr1" placeholder="Address 1" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;box-sizing:border-box;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Address 2</label><input type="text" id="ecAddr2" placeholder="Address 2" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;box-sizing:border-box;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Address 3</label><input type="text" id="ecAddr3" placeholder="Address 3" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;box-sizing:border-box;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Suburb/City</label><input type="text" id="ecCity" placeholder="Suburb/City" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;box-sizing:border-box;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Territory</label><input type="text" id="ecTerritory" placeholder="Territory" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;box-sizing:border-box;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Postcode</label><input type="text" id="ecPostcode" placeholder="Postcode" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;box-sizing:border-box;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Country</label><select id="ecCountry" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;background:#fff;box-sizing:border-box;"><option value="">Country</option><option>Indonesia</option><option>Australia</option><option>United Kingdom</option><option>United States</option><option>Malaysia</option><option>Singapore</option><option>Other</option></select></div></div><div style="margin-top:12px;"><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Relationship</label><select id="ecRelationship" style="width:calc(50% - 8px);border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;background:#fff;box-sizing:border-box;"><option value="">Select relationship</option><option>Spouse</option><option>Parent</option><option>Sibling</option><option>Child</option><option>Friend</option><option>Other</option></select></div><div style="margin-top:20px;display:flex;gap:10px;"><button onclick="cancelEmergencyContact()" style="background:#fff;color:#e63364;border:2px solid #e63364;border-radius:6px;padding:9px 20px;font-size:13px;font-weight:600;cursor:pointer;transition:background .2s;">Cancel</button><button onclick="saveEmergencyContact()" id="ecSaveBtn" style="background:#2e7d5e;color:#fff;border:none;border-radius:6px;padding:10px 20px;font-size:13px;font-weight:600;cursor:not-allowed;transition:background .2s,opacity .2s;opacity:0.5;">Save contact</button></div></div></div>
                        <div style="border:1px solid #e5e7eb;border-radius:10px;padding:24px;margin-bottom:16px;background:#fff;"><h3 style="font-size:16px;font-weight:700;color:#1f2937;margin-bottom:20px;">Bank details</h3><div class="ae-form-grid"><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Name on account</label><input type="text" id="empBankAccName" placeholder="Account name" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"><span style="font-size:11px;color:#9ca3af;">Account name. Max 60 chars</span></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Name of bank</label><input type="text" id="empBankName" placeholder="Bank name" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"><span style="font-size:11px;color:#9ca3af;">Bank name. Max 60 chars</span></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Bank branch</label><input type="text" id="empBankBranch" placeholder="Bank branch" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"><span style="font-size:11px;color:#9ca3af;">Bank branch location</span></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Account number</label><input type="text" id="empBankAccNo" placeholder="5-20 digit number" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"><span style="font-size:11px;color:#9ca3af;">5-20 digit number</span></div></div><div style="margin-top:16px;"><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Bank BSB</label><input type="text" id="empBankBsb" placeholder="000-000" style="width:calc(50% - 8px);border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"><span style="font-size:11px;color:#9ca3af;display:block;">E.g. 000-000</span></div></div>
                        <div style="border:1px solid #e5e7eb;border-radius:10px;padding:24px;margin-bottom:16px;background:#fff;"><h3 style="font-size:16px;font-weight:700;color:#1f2937;margin-bottom:20px;">Salary details</h3><div class="ae-form-grid"><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Salary</label><input type="number" id="empSalary" value="0" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Rate</label><select id="empRate" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;background:#fff;"><option>Select rate</option><option>Per hour</option><option>Per day</option><option>Per month</option><option>Per year</option></select></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Payment frequency</label><select id="empPayFreq" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;background:#fff;"><option>Select frequency</option><option>Weekly</option><option>Fortnightly</option><option>Monthly</option></select></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Effective from</label><input type="date" id="empEffectiveFrom" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Reason</label><select id="empReason" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;background:#fff;"><option>Select reason</option><option>New hire</option><option>Promotion</option><option>Review</option></select></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Payroll number</label><input type="text" id="empPayrollNo" placeholder="ABC123" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div></div></div>
                        <div style="border:1px solid #e5e7eb;border-radius:10px;padding:24px;margin-bottom:16px;background:#fff;"><h3 style="font-size:16px;font-weight:700;color:#1f2937;margin-bottom:6px;">Sensitive details</h3><h4 style="font-size:14px;font-weight:600;color:#374151;margin-bottom:20px;">Tax, work rights and record checks</h4><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Tax File Number (TFN)</label><input type="text" id="empTfn" placeholder="Tax File Number (TFN)" style="width:calc(50% - 8px);border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><h4 style="font-size:15px;font-weight:700;color:#1f2937;margin-top:24px;margin-bottom:16px;">Passport</h4><div class="ae-form-grid"><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Passport number</label><input type="text" id="empPassportNo" placeholder="Passport number" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Country of issue</label><select id="empPassportCountry" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;background:#fff;"><option value="">Country of issue</option><option>Afghanistan</option><option>Albania</option><option>Algeria</option><option>Andorra</option><option>Angola</option><option>Antigua and Barbuda</option><option>Argentina</option><option>Armenia</option><option>Australia</option><option>Austria</option><option>Azerbaijan</option><option>Bahamas</option><option>Bahrain</option><option>Bangladesh</option><option>Barbados</option><option>Belarus</option><option>Belgium</option><option>Belize</option><option>Benin</option><option>Bhutan</option><option>Bolivia</option><option>Bosnia and Herzegovina</option><option>Botswana</option><option>Brazil</option><option>Brunei</option><option>Bulgaria</option><option>Burkina Faso</option><option>Burundi</option><option>Cabo Verde</option><option>Cambodia</option><option>Cameroon</option><option>Canada</option><option>Central African Republic</option><option>Chad</option><option>Chile</option><option>China</option><option>Colombia</option><option>Comoros</option><option>Congo (Brazzaville)</option><option>Congo (Kinshasa)</option><option>Costa Rica</option><option>Croatia</option><option>Cuba</option><option>Cyprus</option><option>Czechia</option><option>Denmark</option><option>Djibouti</option><option>Dominica</option><option>Dominican Republic</option><option>Ecuador</option><option>Egypt</option><option>El Salvador</option><option>Equatorial Guinea</option><option>Eritrea</option><option>Estonia</option><option>Eswatini</option><option>Ethiopia</option><option>Fiji</option><option>Finland</option><option>France</option><option>Gabon</option><option>Gambia</option><option>Georgia</option><option>Germany</option><option>Ghana</option><option>Greece</option><option>Grenada</option><option>Guatemala</option><option>Guinea</option><option>Guinea-Bissau</option><option>Guyana</option><option>Haiti</option><option>Honduras</option><option>Hungary</option><option>Iceland</option><option>India</option><option>Indonesia</option><option>Iran</option><option>Iraq</option><option>Ireland</option><option>Israel</option><option>Italy</option><option>Jamaica</option><option>Japan</option><option>Jordan</option><option>Kazakhstan</option><option>Kenya</option><option>Kiribati</option><option>Kuwait</option><option>Kyrgyzstan</option><option>Laos</option><option>Latvia</option><option>Lebanon</option><option>Lesotho</option><option>Liberia</option><option>Libya</option><option>Liechtenstein</option><option>Lithuania</option><option>Luxembourg</option><option>Madagascar</option><option>Malawi</option><option>Malaysia</option><option>Maldives</option><option>Mali</option><option>Malta</option><option>Marshall Islands</option><option>Mauritania</option><option>Mauritius</option><option>Mexico</option><option>Micronesia</option><option>Moldova</option><option>Monaco</option><option>Mongolia</option><option>Montenegro</option><option>Morocco</option><option>Mozambique</option><option>Myanmar</option><option>Namibia</option><option>Nauru</option><option>Nepal</option><option>Netherlands</option><option>New Zealand</option><option>Nicaragua</option><option>Niger</option><option>Nigeria</option><option>North Korea</option><option>North Macedonia</option><option>Norway</option><option>Oman</option><option>Pakistan</option><option>Palau</option><option>Palestine</option><option>Panama</option><option>Papua New Guinea</option><option>Paraguay</option><option>Peru</option><option>Philippines</option><option>Poland</option><option>Portugal</option><option>Qatar</option><option>Romania</option><option>Russia</option><option>Rwanda</option><option>Saint Kitts and Nevis</option><option>Saint Lucia</option><option>Saint Vincent and the Grenadines</option><option>Samoa</option><option>San Marino</option><option>Sao Tome and Principe</option><option>Saudi Arabia</option><option>Senegal</option><option>Serbia</option><option>Seychelles</option><option>Sierra Leone</option><option>Singapore</option><option>Slovakia</option><option>Slovenia</option><option>Solomon Islands</option><option>Somalia</option><option>South Africa</option><option>South Korea</option><option>South Sudan</option><option>Spain</option><option>Sri Lanka</option><option>Sudan</option><option>Suriname</option><option>Sweden</option><option>Switzerland</option><option>Syria</option><option>Taiwan</option><option>Tajikistan</option><option>Tanzania</option><option>Thailand</option><option>Timor-Leste</option><option>Togo</option><option>Tonga</option><option>Trinidad and Tobago</option><option>Tunisia</option><option>Turkey</option><option>Turkmenistan</option><option>Tuvalu</option><option>Uganda</option><option>Ukraine</option><option>United Arab Emirates</option><option>United Kingdom</option><option>United States</option><option>Uruguay</option><option>Uzbekistan</option><option>Vanuatu</option><option>Vatican City</option><option>Venezuela</option><option>Vietnam</option><option>Yemen</option><option>Zambia</option><option>Zimbabwe</option></select></div></div><div style="margin-top:16px;"><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Passport expiry date</label><input type="date" id="empPassportExpiry" style="width:calc(50% - 8px);border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><h4 style="font-size:15px;font-weight:700;color:#1f2937;margin-top:24px;margin-bottom:16px;">Driving licence</h4><div class="ae-form-grid"><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Licence number</label><input type="text" id="empLicenceNo" placeholder="Licence number" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Country of issue</label><select id="empLicenceCountry" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;background:#fff;"><option value="">Country of issue</option><option>Afghanistan</option><option>Albania</option><option>Algeria</option><option>Andorra</option><option>Angola</option><option>Antigua and Barbuda</option><option>Argentina</option><option>Armenia</option><option>Australia</option><option>Austria</option><option>Azerbaijan</option><option>Bahamas</option><option>Bahrain</option><option>Bangladesh</option><option>Barbados</option><option>Belarus</option><option>Belgium</option><option>Belize</option><option>Benin</option><option>Bhutan</option><option>Bolivia</option><option>Bosnia and Herzegovina</option><option>Botswana</option><option>Brazil</option><option>Brunei</option><option>Bulgaria</option><option>Burkina Faso</option><option>Burundi</option><option>Cabo Verde</option><option>Cambodia</option><option>Cameroon</option><option>Canada</option><option>Central African Republic</option><option>Chad</option><option>Chile</option><option>China</option><option>Colombia</option><option>Comoros</option><option>Congo (Brazzaville)</option><option>Congo (Kinshasa)</option><option>Costa Rica</option><option>Croatia</option><option>Cuba</option><option>Cyprus</option><option>Czechia</option><option>Denmark</option><option>Djibouti</option><option>Dominica</option><option>Dominican Republic</option><option>Ecuador</option><option>Egypt</option><option>El Salvador</option><option>Equatorial Guinea</option><option>Eritrea</option><option>Estonia</option><option>Eswatini</option><option>Ethiopia</option><option>Fiji</option><option>Finland</option><option>France</option><option>Gabon</option><option>Gambia</option><option>Georgia</option><option>Germany</option><option>Ghana</option><option>Greece</option><option>Grenada</option><option>Guatemala</option><option>Guinea</option><option>Guinea-Bissau</option><option>Guyana</option><option>Haiti</option><option>Honduras</option><option>Hungary</option><option>Iceland</option><option>India</option><option>Indonesia</option><option>Iran</option><option>Iraq</option><option>Ireland</option><option>Israel</option><option>Italy</option><option>Jamaica</option><option>Japan</option><option>Jordan</option><option>Kazakhstan</option><option>Kenya</option><option>Kiribati</option><option>Kuwait</option><option>Kyrgyzstan</option><option>Laos</option><option>Latvia</option><option>Lebanon</option><option>Lesotho</option><option>Liberia</option><option>Libya</option><option>Liechtenstein</option><option>Lithuania</option><option>Luxembourg</option><option>Madagascar</option><option>Malawi</option><option>Malaysia</option><option>Maldives</option><option>Mali</option><option>Malta</option><option>Marshall Islands</option><option>Mauritania</option><option>Mauritius</option><option>Mexico</option><option>Micronesia</option><option>Moldova</option><option>Monaco</option><option>Mongolia</option><option>Montenegro</option><option>Morocco</option><option>Mozambique</option><option>Myanmar</option><option>Namibia</option><option>Nauru</option><option>Nepal</option><option>Netherlands</option><option>New Zealand</option><option>Nicaragua</option><option>Niger</option><option>Nigeria</option><option>North Korea</option><option>North Macedonia</option><option>Norway</option><option>Oman</option><option>Pakistan</option><option>Palau</option><option>Palestine</option><option>Panama</option><option>Papua New Guinea</option><option>Paraguay</option><option>Peru</option><option>Philippines</option><option>Poland</option><option>Portugal</option><option>Qatar</option><option>Romania</option><option>Russia</option><option>Rwanda</option><option>Saint Kitts and Nevis</option><option>Saint Lucia</option><option>Saint Vincent and the Grenadines</option><option>Samoa</option><option>San Marino</option><option>Sao Tome and Principe</option><option>Saudi Arabia</option><option>Senegal</option><option>Serbia</option><option>Seychelles</option><option>Sierra Leone</option><option>Singapore</option><option>Slovakia</option><option>Slovenia</option><option>Solomon Islands</option><option>Somalia</option><option>South Africa</option><option>South Korea</option><option>South Sudan</option><option>Spain</option><option>Sri Lanka</option><option>Sudan</option><option>Suriname</option><option>Sweden</option><option>Switzerland</option><option>Syria</option><option>Taiwan</option><option>Tajikistan</option><option>Tanzania</option><option>Thailand</option><option>Timor-Leste</option><option>Togo</option><option>Tonga</option><option>Trinidad and Tobago</option><option>Tunisia</option><option>Turkey</option><option>Turkmenistan</option><option>Tuvalu</option><option>Uganda</option><option>Ukraine</option><option>United Arab Emirates</option><option>United Kingdom</option><option>United States</option><option>Uruguay</option><option>Uzbekistan</option><option>Vanuatu</option><option>Vatican City</option><option>Venezuela</option><option>Vietnam</option><option>Yemen</option><option>Zambia</option><option>Zimbabwe</option></select></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Licence class</label><input type="text" id="empLicenceClass" placeholder="Licence class" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Date of expiry</label><input type="date" id="empLicenceExpiry" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div></div><h4 style="font-size:15px;font-weight:700;color:#1f2937;margin-top:24px;margin-bottom:16px;">Visa</h4><div class="ae-form-grid"><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Visa number</label><input type="text" id="empVisaNo" placeholder="Visa number" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div><div><label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Visa expiry date</label><input type="date" id="empVisaExpiry" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;"></div></div></div>
                    </div>

                    <!-- Step 2 Section: Employment Details -->
                    <div class="ae-tab-section" id="section-employment-details" style="display: none;">
                        <div style="border:1px solid #e5e7eb;border-radius:10px;padding:24px;margin-bottom:16px;background:#fff;">
                            <h3 style="font-size:16px;font-weight:700;color:#1f2937;margin-bottom:20px;">Location</h3>
                            <div class="ae-form-grid">
                                <div>
                                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">
                                        Public holidays observed for <span style="color:#ef4444;font-size:12px;float:right;">Required</span>
                                    </label>
                                    <select id="empJurisdiction" onchange="onJurisdictionChange()" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;background:#fff;outline:none;">
                                        <option value="">Select jurisdiction</option>
                                        <option value="Bali (Indonesia)">Bali (Indonesia)</option>
                                        <option value="New South Wales (Australia)">New South Wales (Australia)</option>
                                        <option value="Victoria (Australia)">Victoria (Australia)</option>
                                        <option value="Queensland (Australia)">Queensland (Australia)</option>
                                        <option value="Western Australia (Australia)">Western Australia (Australia)</option>
                                        <option value="South Australia (Australia)">South Australia (Australia)</option>
                                        <option value="Tasmania (Australia)">Tasmania (Australia)</option>
                                        <option value="Australian Capital Territory (Australia)">Australian Capital Territory (Australia)</option>
                                        <option value="Northern Territory (Australia)">Northern Territory (Australia)</option>
                                        <option value="United Kingdom">United Kingdom</option>
                                        <option value="United States">United States</option>
                                    </select>
                                    <span style="font-size:12px;color:#6b7280;margin-top:6px;display:block;">This does not include local public holidays</span>
                                </div>
                                <div id="placeOfWorkContainer" style="display:none;">
                                    <input type="hidden" id="empPlaceOfWork" value="">
                                    <input type="hidden" id="empWorkCountry" value="">
                                    <label style="font-size:13px;font-weight:600;color:#374151;display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                                        Place of work
                                        <button type="button" onclick="openPowModal()" style="background:#2e7d5e;color:#fff;border:none;border-radius:4px;padding:3px 10px;font-size:11px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:3px;">+ Add new</button>
                                    </label>
                                    <div style="position:relative;" id="powDropdownWrap">
                                        <div onclick="togglePowDropdown()" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;box-sizing:border-box;outline:none;cursor:pointer;background:#fff;display:flex;align-items:center;gap:8px;min-height:42px;">
                                            <svg style="width:16px;height:16px;color:#9ca3af;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                            <span id="powSelectedLabel" style="color:#6b7280;">Choose a location</span>
                                        </div>
                                        <div id="powDropdownPanel" style="display:none;position:absolute;top:100%;left:0;right:0;z-index:50;background:#fff;border:1px solid #d1d5db;border-top:none;border-radius:0 0 6px 6px;box-shadow:0 4px 12px rgba(0,0,0,.1);max-height:200px;overflow-y:auto;">
                                            <div style="padding:8px;border-bottom:1px solid #e5e7eb;">
                                                <input type="text" id="powSearchInput" placeholder="Search locations..." oninput="filterPowList()" style="width:100%;border:1px solid #d1d5db;border-radius:4px;padding:6px 10px;font-size:13px;box-sizing:border-box;outline:none;">
                                            </div>
                                            <div id="powListItems"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="border:1px solid #e5e7eb;border-radius:10px;padding:24px;margin-bottom:16px;background:#fff;">
                            <h3 style="font-size:16px;font-weight:700;color:#1f2937;margin-bottom:6px;">Employment details</h3>
                            <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:12px;">
                                Employee type <span style="color:#ef4444;font-size:12px;float:right;">Required</span>
                            </label>
                            <input type="hidden" id="empEmployeeType" value="">
                            <div class="emp-type-grid">
                                <div class="emp-type-card" data-val="Fixed, full or part time" onclick="selectEmployeeTypeCard(this)">
                                    <div class="emp-type-radio-circle">
                                        <div class="emp-type-radio-inner"></div>
                                    </div>
                                    <div>
                                        <div class="emp-type-card-title">Fixed, full or part time</div>
                                        <div class="emp-type-card-desc">Employees on a repeating working time pattern who work fixed, predictable numbers of hours.</div>
                                    </div>
                                </div>
                                
                                <div class="emp-type-card" data-val="Short hours or variable" onclick="selectEmployeeTypeCard(this)">
                                    <div class="emp-type-radio-circle">
                                        <div class="emp-type-radio-inner"></div>
                                    </div>
                                    <div>
                                        <div class="emp-type-card-title">Short hours or variable</div>
                                        <div class="emp-type-card-desc">Employees on a contract who work a different number of hours or days from week to week.</div>
                                    </div>
                                </div>
                                
                                <div class="emp-type-card" data-val="Casual" onclick="selectEmployeeTypeCard(this)">
                                    <div class="emp-type-radio-circle">
                                        <div class="emp-type-radio-inner"></div>
                                    </div>
                                    <div>
                                        <div class="emp-type-card-title">Casual</div>
                                        <div class="emp-type-card-desc">Employees who have no guaranteed hours and have the ability to accept and decline shifts.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Working Time Pattern (shown for Fixed type) -->
                            <div id="workingTimePatternSection" style="display:none;margin-top:20px;">
                                <label style="font-size:13px;font-weight:600;color:#374151;display:flex;align-items:center;gap:6px;margin-bottom:6px;width:calc(50% - 8px);">
                                    Working time pattern
                                    <button type="button" onclick="openWtpModal()" style="background:#2e7d5e;color:#fff;border:none;border-radius:4px;padding:3px 10px;font-size:11px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:3px;">+ Add new</button>
                                    <span style="color:#ef4444;font-size:12px;margin-left:auto;">Required</span>
                                </label>
                                <select id="empWorkingPattern" style="width:calc(50% - 8px);border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;background:#fff;outline:none;">
                                    <option value="">Select a working pattern</option>
                                    <option value="Standard (Mon-Fri)">Standard (Mon-Fri)</option>
                                    <option value="Part Time (Mon-Wed)">Part Time (Mon-Wed)</option>
                                    <option value="Shift Work">Shift Work</option>
                                    <option value="Flexible Hours">Flexible Hours</option>
                                </select>
                            </div>
                        </div>

                        <!-- Contract Details (shown for Fixed type) -->
                        <div id="contractDetailsSection" style="display:none;border:1px solid #e5e7eb;border-radius:10px;padding:24px;margin-bottom:16px;background:#fff;">
                            <h3 style="font-size:16px;font-weight:700;color:#1f2937;margin-bottom:16px;">Contract details</h3>

                            <!-- Info Banner -->
                            <div id="contractInfoBanner" style="display:none;align-items:flex-start;gap:12px;padding:16px 20px;background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;margin-bottom:20px;">
                                <svg style="width:22px;height:22px;color:#d97706;flex-shrink:0;margin-top:2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div style="flex:1;">
                                    <div id="contractBannerTitle" style="font-size:14px;font-weight:700;color:#92400e;"></div>
                                    <div id="contractBannerDesc" style="font-size:13px;color:#78350f;margin-top:2px;"></div>
                                </div>
                                <button onclick="this.parentElement.style.display='none'" style="background:none;border:none;color:#d97706;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;">Dismiss</button>
                            </div>

                            <!-- Leave Unit -->
                            <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:12px;">
                                Leave unit <span style="color:#ef4444;font-size:12px;float:right;">Required</span>
                            </label>
                            <input type="hidden" id="empLeaveUnit" value="">
                            <div class="emp-type-grid">
                                <div class="emp-type-card" data-val="Days" onclick="selectLeaveUnitCard(this)">
                                    <div class="emp-type-radio-circle">
                                        <div class="emp-type-radio-inner"></div>
                                    </div>
                                    <div>
                                        <div class="emp-type-card-title">Days</div>
                                        <div class="emp-type-card-desc">The employee can take leave in day or half day units. They will be able to book appointments in hours and minutes. Leave, absence and balance will be shown in days.</div>
                                    </div>
                                </div>
                                <div class="emp-type-card" data-val="Hours" onclick="selectLeaveUnitCard(this)">
                                    <div class="emp-type-radio-circle">
                                        <div class="emp-type-radio-inner"></div>
                                    </div>
                                    <div>
                                        <div class="emp-type-card-title">Hours</div>
                                        <div class="emp-type-card-desc">The employee can take leave in smaller increments. They will be able to book appointments in hours and minutes. Leave, absence and balance will be shown in hours.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Short hours or variable fields container -->
                            <div id="variableHoursSection" style="display:none;margin-top:20px;">
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">
                                    <!-- Contracted hours per week -->
                                    <div>
                                        <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">
                                            Employee contracted hours per week
                                        </label>
                                        <div style="display:flex;gap:10px;">
                                            <div style="display:flex;align-items:center;border:1px solid #d1d5db;border-radius:6px;padding:6px 12px;background:#f9fafb;width:110px;box-sizing:border-box;">
                                                <input type="number" id="empContractedHours" value="0" min="0" oninput="calculateVariableAnnualLeaveRecommended()" style="width:100%;border:none;background:transparent;outline:none;font-size:14px;text-align:right;padding-right:6px;">
                                                <span style="font-size:13px;color:#9ca3af;">hrs</span>
                                            </div>
                                            <div style="display:flex;align-items:center;border:1px solid #d1d5db;border-radius:6px;padding:6px 12px;background:#f9fafb;width:110px;box-sizing:border-box;">
                                                <input type="number" id="empContractedMinutes" value="0" min="0" max="59" oninput="calculateVariableAnnualLeaveRecommended()" style="width:100%;border:none;background:transparent;outline:none;font-size:14px;text-align:right;padding-right:6px;">
                                                <span style="font-size:13px;color:#9ca3af;">mins</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Contracted days per week (shown for Days) -->
                                    <div id="contractedDaysSection" style="display:none;">
                                        <label style="font-size:13px;font-weight:600;color:#374151;display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                                            Contracted days per week
                                            <span style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;border-radius:50%;background:#e5e7eb;color:#4b5563;font-size:11px;cursor:help;font-weight:bold;" title="Enter the number of days contracted to work per week.">i</span>
                                        </label>
                                        <div style="display:flex;align-items:center;border:1px solid #d1d5db;border-radius:6px;padding:6px 12px;background:#f9fafb;width:100%;box-sizing:border-box;">
                                            <input type="text" id="empContractedDays" placeholder="Enter contracted days" style="width:100%;border:none;background:transparent;outline:none;font-size:14px;">
                                            <span style="font-size:13px;color:#9ca3af;">days</span>
                                        </div>
                                    </div>

                                    <!-- Average working day (shown for Hours) -->
                                    <div id="averageWorkingDaySection" style="display:none;">
                                        <label style="font-size:13px;font-weight:600;color:#374151;display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                                            Average working day
                                            <span style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;border-radius:50%;background:#e5e7eb;color:#4b5563;font-size:11px;cursor:help;font-weight:bold;" title="This is used to calculate the value of a day's leave for this employee.">i</span>
                                        </label>
                                        <div style="display:flex;gap:10px;">
                                            <div style="display:flex;align-items:center;border:1px solid #d1d5db;border-radius:6px;padding:6px 12px;background:#f9fafb;width:110px;box-sizing:border-box;">
                                                <input type="number" id="empAverageHours" value="0" min="0" oninput="calculateVariableAnnualLeaveRecommended()" style="width:100%;border:none;background:transparent;outline:none;font-size:14px;text-align:right;padding-right:6px;">
                                                <span style="font-size:13px;color:#9ca3af;">hrs</span>
                                            </div>
                                            <div style="display:flex;align-items:center;border:1px solid #d1d5db;border-radius:6px;padding:6px 12px;background:#f9fafb;width:110px;box-sizing:border-box;">
                                                <input type="number" id="empAverageMinutes" value="0" min="0" max="59" oninput="calculateVariableAnnualLeaveRecommended()" style="width:100%;border:none;background:transparent;outline:none;font-size:14px;text-align:right;padding-right:6px;">
                                                <span style="font-size:13px;color:#9ca3af;">mins</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Accrual Rate (shown when Hours selected) -->
                            <div id="accrualRateSection" style="display:none;margin-top:20px;">
                                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">
                                    Accrual rate <span style="color:#ef4444;font-size:12px;float:right;">Required</span>
                                </label>
                                <select id="empAccrualRate" onchange="calculateVariableAnnualLeaveRecommended()" style="width:calc(50% - 8px);border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;background:#fff;outline:none;">
                                    <option value="">Select accrual rate</option>
                                    <option value="1 week">1 week</option>
                                    <option value="2 weeks">2 weeks</option>
                                    <option value="3 weeks">3 weeks</option>
                                    <option value="4 weeks">4 weeks</option>
                                    <option value="5 weeks">5 weeks</option>
                                    <option value="6 weeks">6 weeks</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Annual Leave Balance Section (shown for Short hours or variable + Hours) -->
                        <div id="annualLeaveBalanceSection" style="display:none;border:1px solid #e5e7eb;border-radius:10px;padding:24px;margin-bottom:16px;background:#fff;">
                            <h3 style="font-size:16px;font-weight:700;color:#1f2937;margin-bottom:16px;">Annual leave balance</h3>
                            
                            <!-- Info banner -->
                            <div style="display:flex;align-items:flex-start;gap:12px;padding:16px 20px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;margin-bottom:20px;">
                                <svg style="width:20px;height:20px;color:#2563eb;flex-shrink:0;margin-top:2px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div style="font-size:13px;color:#1e3a8a;line-height:1.5;flex:1;">
                                    We have calculated your employee's annual leave balance based on the information you have given us, you can change this by entering into the fields below. Annual leave balance includes any annual leave accrued today.
                                </div>
                            </div>
                            
                            <div>
                                <label style="font-size:13px;font-weight:600;color:#374151;display:flex;align-items:center;gap:6px;margin-bottom:6px;">
                                    Annual leave balance
                                    <span style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;border-radius:50%;background:#e5e7eb;color:#4b5563;font-size:11px;cursor:help;font-weight:bold;" title="Annual leave balance includes any annual leave accrued today.">i</span>
                                </label>
                                <div style="display:flex;gap:10px;">
                                    <div style="display:flex;align-items:center;border:1px solid #d1d5db;border-radius:6px;padding:6px 12px;background:#f9fafb;width:110px;box-sizing:border-box;">
                                        <input type="number" id="empAnnualLeaveHours" value="0" min="0" style="width:100%;border:none;background:transparent;outline:none;font-size:14px;text-align:right;padding-right:6px;">
                                        <span style="font-size:13px;color:#9ca3af;">hrs</span>
                                    </div>
                                    <div style="display:flex;align-items:center;border:1px solid #d1d5db;border-radius:6px;padding:6px 12px;background:#f9fafb;width:110px;box-sizing:border-box;">
                                        <input type="number" id="empAnnualLeaveMinutes" value="0" min="0" max="59" style="width:100%;border:none;background:transparent;outline:none;font-size:14px;text-align:right;padding-right:6px;">
                                        <span style="font-size:13px;color:#9ca3af;">mins</span>
                                    </div>
                                </div>
                                <div id="empAnnualLeaveRecommendedLabel" style="font-size:12px;color:#6b7280;margin-top:6px;">Recommended: 0 hrs 0 mins</div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 Section: Summary -->
                    <div class="ae-tab-section" id="section-summary" style="display: none;">
                        <!-- Summary Overview Card (6 Cards) -->
                        <div id="summary-overview-container" style="border:1.5px solid #e5e7eb;border-radius:12px;padding:30px;background:#ffffff;box-shadow:0 1px 3px rgba(0,0,0,0.06);margin-bottom:16px;">
                            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid #f3f4f6;">
                                <div id="summaryAvatar" style="width: 60px; height: 60px; border-radius: 50%; color: #fff; font-size: 24px; font-weight: 700; display: flex; align-items: center; justify-content: center; background: #5c6bc0;">
                                    JD
                                </div>
                                <div>
                                    <h3 id="summaryFullName" style="font-size: 20px; font-weight: 700; color: #111827;">John Doe</h3>
                                    <p id="summaryJobTitle" style="font-size: 14px; color: #6b7280; margin-top: 2px;">Software Engineer</p>
                                </div>
                            </div>
                            
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                                <!-- Column 1 -->
                                <div style="display: flex; flex-direction: column; gap: 20px;">
                                    <!-- Card 1: Employee Details -->
                                    <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; background: #fbfbfb;">
                                        <h4 style="font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 12px; border-left: 3px solid #2e7d5e; padding-left: 8px;">Employee Details</h4>
                                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Email:</td><td id="sumEmail" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Mobile:</td><td id="sumPhone" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Work Phone:</td><td id="sumWorkPhone" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Gender / DOB:</td><td id="sumGenderDob" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">- / -</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Start Date:</td><td id="sumStartDate" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                        </table>
                                    </div>

                                    <!-- Card 2: Address & Emergency Contact -->
                                    <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; background: #fbfbfb;">
                                        <h4 style="font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 12px; border-left: 3px solid #2e7d5e; padding-left: 8px;">Address & Emergency Contacts</h4>
                                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500; vertical-align: top;">Home Address:</td><td id="sumAddress" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right; line-height: 1.4;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Emergency Contact:</td><td id="sumEmergencyName" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Relationship:</td><td id="sumEmergencyRelation" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Emergency Phone:</td><td id="sumEmergencyPhone" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                        </table>
                                    </div>

                                    <!-- Card 3: Bank Details -->
                                    <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; background: #fbfbfb;">
                                        <h4 style="font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 12px; border-left: 3px solid #2e7d5e; padding-left: 8px;">Bank Account Details</h4>
                                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Account Name:</td><td id="sumBankAccName" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Bank / Branch:</td><td id="sumBankNameBranch" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">BSB / Account No:</td><td id="sumBankAccNoBsb" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                        </table>
                                    </div>
                                </div>
                                
                                <!-- Column 2 -->
                                <div style="display: flex; flex-direction: column; gap: 20px;">
                                    <!-- Card 4: Employment & Salary Details -->
                                    <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; background: #fbfbfb;">
                                        <h4 style="font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 12px; border-left: 3px solid #2e7d5e; padding-left: 8px;">Employment Details</h4>
                                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Location / Jurisdiction:</td><td id="sumJurisdiction" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Employee Type:</td><td id="sumEmployeeType" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Place of Work:</td><td id="sumPlaceOfWork" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Salary details:</td><td id="sumSalary" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">0</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Payment Frequency:</td><td id="sumPayFreq" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Effective Date / Reason:</td><td id="sumSalaryEffective" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Payroll Number:</td><td id="sumPayrollNo" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                        </table>
                                    </div>

                                    <!-- Card 5: Leave & Working Hours Details -->
                                    <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; background: #fbfbfb;">
                                        <h4 style="font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 12px; border-left: 3px solid #2e7d5e; padding-left: 8px;">Leave & Working Time</h4>
                                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Leave Unit:</td><td id="sumLeaveUnit" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Working Pattern:</td><td id="sumWorkingPattern" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Weekly Working Hours:</td><td id="sumContractedHours" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Contract details info:</td><td id="sumContractDetails" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Accrual Rate:</td><td id="sumAccrualRate" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Annual Leave Balance:</td><td id="sumAnnualLeaveBalance" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                        </table>
                                    </div>

                                    <!-- Card 6: Sensitive Details -->
                                    <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; background: #fbfbfb;">
                                        <h4 style="font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 12px; border-left: 3px solid #2e7d5e; padding-left: 8px;">Sensitive & Identity Details</h4>
                                        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Tax File Number (TFN):</td><td id="sumTfn" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Passport:</td><td id="sumPassport" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Driving Licence:</td><td id="sumLicence" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                            <tr style="border-bottom: 1px solid #f3f4f6;"><td style="padding: 8px 0; color: #6b7280; font-weight: 500;">Visa details:</td><td id="sumVisa" style="padding: 8px 0; color: #111827; font-weight: 600; text-align: right;">-</td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submission Status Container (Initially hidden, BrightHR style) -->
                        <div id="summary-status-container" style="display: none; border:1.5px solid #e5e7eb; border-radius:12px; padding:30px; background:#ffffff; box-shadow:0 1px 3px rgba(0,0,0,0.06); margin-bottom:16px;">
                            <!-- Tabs Header -->
                            <div style="display:flex; border-bottom: 2px solid #e5e7eb; margin-bottom: 20px;">
                                <button id="tabAddedBtn" onclick="switchSubmissionTab('added')" style="padding: 12px 24px; font-weight: 600; font-size: 14px; border: none; background: none; cursor: pointer; border-bottom: 3px solid #2e7d5e; color: #2e7d5e; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
                                    Added <span id="addedBadge" style="background: #2e7d5e; color: #fff; border-radius: 12px; padding: 2px 8px; font-size: 11px; font-weight: 700;">0</span>
                                </button>
                                <button id="tabFailedBtn" onclick="switchSubmissionTab('failed')" style="padding: 12px 24px; font-weight: 600; font-size: 14px; border: none; background: none; cursor: pointer; border-bottom: 3px solid transparent; color: #6b7280; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
                                    Failed <span id="failedBadge" style="background: #ef4444; color: #fff; border-radius: 12px; padding: 2px 8px; font-size: 11px; font-weight: 700;">0</span>
                                </button>
                            </div>

                            <!-- Tab Content: Added -->
                            <div id="subContentAdded" style="overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 13px; min-width: 900px;">
                                    <thead>
                                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; text-align: left;">
                                            <th style="padding: 12px 16px; color: #475569; font-weight: 700;">Name</th>
                                            <th style="padding: 12px 16px; color: #475569; font-weight: 700;">Email address</th>
                                            <th style="padding: 12px 16px; color: #475569; font-weight: 700; text-align: center;">Personal Information <span style="display:inline-block; width:14px; height:14px; border-radius:50%; border:1px solid #94a3b8; color:#94a3b8; font-size:10px; text-align:center; line-height:12px; cursor:help; font-weight:normal;" title="Includes: Name, DOB, Gender">i</span></th>
                                            <th style="padding: 12px 16px; color: #475569; font-weight: 700; text-align: center;">Contact Information <span style="display:inline-block; width:14px; height:14px; border-radius:50%; border:1px solid #94a3b8; color:#94a3b8; font-size:10px; text-align:center; line-height:12px; cursor:help; font-weight:normal;" title="Includes: Phone, Address, Email">i</span></th>
                                            <th style="padding: 12px 16px; color: #475569; font-weight: 700; text-align: center;">Sensitive Information <span style="display:inline-block; width:14px; height:14px; border-radius:50%; border:1px solid #94a3b8; color:#94a3b8; font-size:10px; text-align:center; line-height:12px; cursor:help; font-weight:normal;" title="Includes: TFN, Passport, Licence, Visa">i</span></th>
                                            <th style="padding: 12px 16px; color: #475569; font-weight: 700; text-align: center;">Emergency Contact <span style="display:inline-block; width:14px; height:14px; border-radius:50%; border:1px solid #94a3b8; color:#94a3b8; font-size:10px; text-align:center; line-height:12px; cursor:help; font-weight:normal;" title="Emergency contacts count">i</span></th>
                                            <th style="padding: 12px 16px; color: #475569; font-weight: 700; text-align: center;">Bank details <span style="display:inline-block; width:14px; height:14px; border-radius:50%; border:1px solid #94a3b8; color:#94a3b8; font-size:10px; text-align:center; line-height:12px; cursor:help; font-weight:normal;" title="Bank details completeness">i</span></th>
                                            <th style="padding: 12px 16px; color: #475569; font-weight: 700; text-align: center;">Salary Information <span style="display:inline-block; width:14px; height:14px; border-radius:50%; border:1px solid #94a3b8; color:#94a3b8; font-size:10px; text-align:center; line-height:12px; cursor:help; font-weight:normal;" title="Salary and Rate info">i</span></th>
                                            <th style="padding: 12px 16px; color: #475569; font-weight: 700; text-align: center;">Payroll number <span style="display:inline-block; width:14px; height:14px; border-radius:50%; border:1px solid #94a3b8; color:#94a3b8; font-size:10px; text-align:center; line-height:12px; cursor:help; font-weight:normal;" title="Payroll identification number">i</span></th>
                                        </tr>
                                    </thead>
                                    <tbody id="submissionAddedRows">
                                        <!-- Dynamically populated -->
                                    </tbody>
                                </table>
                                
                                <!-- Legend -->
                                <div style="display: flex; gap: 24px; align-items: center; margin-top: 24px; padding-top: 16px; border-top: 1px solid #e2e8f0; font-size: 13px; color: #475569;">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <svg style="width: 18px; height: 18px; color: #10b981;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Successful</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <svg style="width: 18px; height: 18px; color: #ef4444;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Unsuccessful</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <svg style="width: 18px; height: 18px; color: #6b7280;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Skipped</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Tab Content: Failed -->
                            <div id="subContentFailed" style="display: none; overflow-x: auto;">
                                <table style="width: 100%; border-collapse: collapse; font-size: 13px; min-width: 900px;">
                                    <thead>
                                        <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; text-align: left;">
                                            <th style="padding: 12px 16px; color: #475569; font-weight: 700; width: 250px;">Name</th>
                                            <th style="padding: 12px 16px; color: #475569; font-weight: 700; width: 250px;">Email address</th>
                                            <th style="padding: 12px 16px; color: #475569; font-weight: 700;">Description</th>
                                        </tr>
                                    </thead>
                                    <tbody id="submissionFailedRows">
                                        <!-- Dynamically populated -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sticky Footer inside Filled State -->
            <div class="ae-form-footer">
                <button onclick="startOver()" style="border:2px solid #e63364;color:#e63364;background:#fff;border-radius:6px;padding:10px 20px;font-size:13px;font-weight:600;cursor:pointer;">Start over</button>
                
                <div style="display: flex; gap: 12px; align-items: center;">
                    <!-- Step 1 Footer Buttons -->
                    <button id="saveAndContinueBtn" class="step1-btn" style="background:#2e7d5e;color:#fff;border:none;border-radius:6px;padding:10px 24px;font-size:13px;font-weight:600;cursor:pointer;opacity:1;transition:all 0.2s;">Save and continue</button>
                    
                    <!-- Step 2 Footer Buttons -->
                    <button id="backToStep1Btn" class="step2-btn" style="display: none; border:2px solid #e63364; color:#e63364; background:#fff; border-radius:6px; padding:10px 20px; font-size:13px; font-weight:600; cursor:pointer;">Back</button>
                    <button id="saveAndContinueBtn2" class="step2-btn" style="display: none; background:#2e7d5e; color:#fff; border:none; border-radius:6px; padding:10px 24px; font-size:13px; font-weight:600; cursor:not-allowed; opacity:0.4; transition:all 0.2s;">Save and continue</button>
                    
                    <!-- Step 3 Footer Buttons -->
                    <button id="backToStep2Btn" class="step3-btn" style="display: none; border:2px solid #e63364; color:#e63364; background:#fff; border-radius:6px; padding:10px 20px; font-size:13px; font-weight:600; cursor:pointer;">Back</button>
                    <button id="finishBtn" class="step3-btn" style="display: none; background:#2e7d5e; color:#fff; border:none; border-radius:6px; padding:10px 24px; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s;">Add all data</button>

                    <!-- Submission Status Footer Buttons -->
                    <button id="subBackBtn" class="step-sub-btn" disabled style="display: none; border:1px solid #d1d5db; color:#9ca3af; background:#f3f4f6; border-radius:6px; padding:10px 20px; font-size:13px; font-weight:600; cursor:not-allowed;">Back</button>
                    <button id="subNextBtn" class="step-sub-btn" style="display: none; background:#2e7d5e; color:#fff; border:none; border-radius:6px; padding:10px 24px; font-size:13px; font-weight:600; cursor:pointer; transition:all 0.2s;" onclick="finishSubmissionRedirect()">Next</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ═══ Registration Email Modal ═══ --}}
<div class="ae-modal-overlay" id="regEmailModal" style="display: none; align-items: center; justify-content: center; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 99999;">
    <div class="ae-modal-box" id="regEmailModalBox" style="width: 700px; max-width: 90vw; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.25);">
        
        <!-- STEP 1: Select & Send -->
        <div id="regEmailStep1">
            <!-- Dark Green Header matching StaffLink theme -->
            <div style="background: #1a4d3e; color: #fff; padding: 20px 24px;">
                <h3 id="regEmailModalTitle" style="font-size: 18px; font-weight: 700; margin: 0;">Success! You have 1 new employee in StaffLink</h3>
            </div>
            
            <div style="padding: 24px;">
                <p style="font-size: 14px; color: #4b5563; margin-bottom: 20px;">Why not also send them a registration email, select from below...</p>
                
                <!-- Search & Toggle row -->
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 16px;">
                    <!-- Search box -->
                    <div style="position: relative; flex: 1; display: flex; align-items: center; border: 1px solid #d1d5db; border-radius: 6px; padding: 6px 12px; background: #f9fafb;">
                        <svg style="width: 16px; height: 16px; color: #9ca3af; margin-right: 8px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input type="text" id="regEmailSearch" placeholder="Search..." oninput="filterRegEmailList()" style="width: 100%; border: none; background: transparent; outline: none; font-size: 13px;">
                        <button onclick="clearRegEmailSearch()" style="background: none; border: none; color: #9ca3af; cursor: pointer; display: flex; align-items: center; padding: 2px;"><svg style="width:14px; height:14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                    </div>
                    
                    <!-- Deselect all -->
                    <button id="regEmailToggleAllBtn" onclick="toggleAllRegEmails()" style="background: none; border: none; color: #2e7d5e; font-size: 13px; font-weight: 600; cursor: pointer;">Deselect all</button>
                </div>
                
                <!-- Employee List container -->
                <div id="regEmailList" style="max-height: 200px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 24px;">
                    <!-- Dynamically populated -->
                </div>
                
                <!-- Footer controls -->
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <!-- Pagination Dots -->
                    <div style="display: flex; gap: 8px;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #2e7d5e;"></span>
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #cbd5e1;"></span>
                    </div>
                    
                    <!-- Buttons -->
                    <div style="display: flex; gap: 12px;">
                        <button onclick="skipRegEmails()" style="border: 2px solid #2e7d5e; color: #2e7d5e; background: #fff; border-radius: 6px; padding: 8px 20px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;">Skip</button>
                        <button onclick="sendRegEmails()" style="background: #2e7d5e; color: #fff; border: none; border-radius: 6px; padding: 10px 24px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;">Send & continue</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- STEP 2: Success & Actions -->
        <div id="regEmailStep2" style="display: none;">
            <!-- Dark Green Header matching StaffLink theme -->
            <div style="background: #1a4d3e; color: #fff; padding: 20px 24px;">
                <h3 id="regEmailStep2Title" style="font-size: 18px; font-weight: 700; margin: 0;">You have sent 1 registration email(s) to your new employee</h3>
            </div>
            
            <div style="padding: 24px;">
                <p style="font-size: 14px; color: #4b5563; margin-bottom: 20px; font-weight: 500;">Why not also do one of the following...</p>
                
                <!-- 2x2 Grid Layout -->
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 20px;">
                    <!-- Option 1: Set up permissions -->
                    <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; display: flex; flex-direction: column; justify-content: space-between; min-height: 130px; background: #fff;">
                        <div>
                            <h4 style="font-size: 13px; font-weight: 700; color: #1a4d3e; margin: 0 0 4px 0;">Set up permissions</h4>
                            <p style="font-size: 11px; color: #6b7280; line-height: 1.3; margin: 0 0 10px 0;">Set up your admins and managers and specify who they manage</p>
                        </div>
                        <a href="#" onclick="event.preventDefault();" style="display: inline-block; border: 1.5px solid #2e7d5e; color: #2e7d5e; background: #fff; border-radius: 6px; padding: 5px 10px; font-size: 11px; font-weight: 600; text-align: center; text-decoration: none; cursor: pointer;">Set up permissions</a>
                    </div>
                    
                    <!-- Option 2: Create teams -->
                    <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; display: flex; flex-direction: column; justify-content: space-between; min-height: 130px; background: #fff;">
                        <div>
                            <h4 style="font-size: 13px; font-weight: 700; color: #1a4d3e; margin: 0 0 4px 0;">Create teams</h4>
                            <p style="font-size: 11px; color: #6b7280; line-height: 1.3; margin: 0 0 10px 0;">Group your employees into teams to reflect your company's internal structures</p>
                        </div>
                        <a href="{{ route('admin.linkers-hub.index') }}?tab=manage-teams" style="display: inline-block; border: 1.5px solid #2e7d5e; color: #2e7d5e; background: #fff; border-radius: 6px; padding: 5px 10px; font-size: 11px; font-weight: 600; text-align: center; text-decoration: none; cursor: pointer;">Create teams</a>
                    </div>
                    
                    <!-- Option 3: Add documents -->
                    <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; display: flex; flex-direction: column; justify-content: space-between; min-height: 130px; background: #fff;">
                        <div>
                            <h4 style="font-size: 13px; font-weight: 700; color: #1a4d3e; margin: 0 0 4px 0;">Add some documents</h4>
                            <p style="font-size: 11px; color: #6b7280; line-height: 1.3; margin: 0 0 10px 0;">Upload and manage your company's documents, employee contracts and much more</p>
                        </div>
                        <a id="addDocumentsLink" href="#" style="display: inline-block; border: 1.5px solid #2e7d5e; color: #2e7d5e; background: #fff; border-radius: 6px; padding: 5px 10px; font-size: 11px; font-weight: 600; text-align: center; text-decoration: none; cursor: pointer;">Add documents</a>
                    </div>
                    
                    <!-- Option 4: Go to dashboard -->
                    <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; display: flex; flex-direction: column; justify-content: space-between; min-height: 130px; background: #fff;">
                        <div>
                            <h4 style="font-size: 13px; font-weight: 700; color: #1a4d3e; margin: 0 0 4px 0;">Go to the dashboard</h4>
                            <p style="font-size: 11px; color: #6b7280; line-height: 1.3; margin: 0 0 10px 0;">Go to the dashboard to find out what else you can do with StaffLink</p>
                        </div>
                        <a href="{{ route('admin.dashboard') }}" style="display: inline-block; border: 1.5px solid #2e7d5e; color: #2e7d5e; background: #fff; border-radius: 6px; padding: 5px 10px; font-size: 11px; font-weight: 600; text-align: center; text-decoration: none; cursor: pointer;">Go to dashboard</a>
                    </div>
                </div>
                
                <!-- Step 2 Footer controls -->
                <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid #e5e7eb; padding-top: 16px;">
                    <!-- Pagination Dots -->
                    <div style="display: flex; gap: 8px;">
                        <span onclick="goBackToStep1()" title="Kembali ke pengiriman email registrasi" style="width: 8px; height: 8px; border-radius: 50%; background: #cbd5e1; cursor: pointer; transition: background 0.2s;" onmouseenter="this.style.background='#94a3b8'" onmouseleave="this.style.background='#cbd5e1'"></span>
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #2e7d5e;"></span>
                    </div>
                    
                    <!-- Buttons -->
                    <button onclick="closeRegEmailModal()" style="background: #2e7d5e; color: #fff; border: none; border-radius: 6px; padding: 10px 24px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s;">Go to Linkers Hub</button>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- ═══ Confirm Modal ═══ --}}
<div class="ae-modal-overlay" id="aeConfirmModal">
    <div class="ae-modal-box">
        <div class="ae-modal-header">
            <h3 id="aeModalTitle">Delete record</h3>
            <button class="ae-modal-close" onclick="closeConfirmModal()">
                <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="ae-modal-body">
            <p id="aeModalMessage">This action cannot be reversed. Are you sure you want to delete this record?</p>
        </div>
        <div class="ae-modal-footer">
            <button class="ae-modal-cancel" onclick="closeConfirmModal()">Cancel</button>
            <button class="ae-modal-confirm" id="aeModalConfirmBtn" onclick="executeConfirmAction()">Delete</button>
        </div>
    </div>
</div>
{{-- ═══ Warning / Validation Modal ═══ --}}
<div class="ae-modal-overlay" id="aeWarningModal">
    <div class="ae-modal-box">
        <div class="ae-modal-header" style="background:#b91c1c;">
            <h3>Missing information</h3>
            <button class="ae-modal-close" onclick="closeAeWarningModal()">
                <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 6L6 18M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="ae-modal-body" style="display:flex;align-items:flex-start;gap:12px;">
            <svg style="width:22px;height:22px;color:#b91c1c;flex-shrink:0;margin-top:1px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
            <p id="aeWarningMessage" style="margin:0;">Please fill in the required fields.</p>
        </div>
        <div class="ae-modal-footer" style="justify-content:flex-end;">
            <button class="ae-modal-confirm" style="background:#2e7d5e;" onclick="closeAeWarningModal()">OK</button>
        </div>
    </div>
</div>
{{-- ═══ Place of Work Modal ═══ --}}
<div id="powModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;background:rgba(0,0,0,.5);align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;width:520px;max-width:92vw;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.3);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #e5e7eb;background:#2e7d5e;border-radius:12px 12px 0 0;">
            <h3 id="powModalTitle" style="font-size:16px;font-weight:700;color:#fff;margin:0;">Create new place of work</h3>
            <button onclick="closePowModal()" style="background:none;border:none;color:#fff;font-size:22px;cursor:pointer;line-height:1;padding:0;">✕</button>
        </div>
        <div style="padding:24px;">
            <div style="margin-bottom:16px;">
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Name <span style="color:#ef4444;font-size:12px;float:right;">Required</span></label>
                <input type="text" id="powName" placeholder="Name" oninput="updatePowSaveBtn()" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;box-sizing:border-box;">
            </div>
            <div style="margin-bottom:16px;">
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Address line 1</label>
                <input type="text" id="powAddr1" placeholder="Address line 1" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;box-sizing:border-box;">
            </div>
            <div style="margin-bottom:16px;">
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Address line 2</label>
                <input type="text" id="powAddr2" placeholder="Address line 2" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;box-sizing:border-box;">
            </div>
            <div class="ae-form-grid">
                <div>
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Suburb/City</label>
                    <input type="text" id="powCity" placeholder="Suburb/City" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;box-sizing:border-box;">
                </div>
                <div>
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Postcode</label>
                    <input type="text" id="powPostcode" placeholder="Postcode" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;box-sizing:border-box;">
                </div>
            </div>
            <div class="ae-form-grid" style="margin-top:16px;">
                <div>
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Country <span style="color:#ef4444;font-size:12px;float:right;">Required</span></label>
                    <select id="powCountry" onchange="updatePowSaveBtn()" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;background:#fff;box-sizing:border-box;">
                        <option value="">Select a country</option>
                        <option>Indonesia</option><option>Australia</option><option>United Kingdom</option><option>United States</option><option>Malaysia</option><option>Singapore</option><option>Other</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Territory <span style="color:#ef4444;font-size:12px;float:right;">Required</span></label>
                    <select id="powTerritory" onchange="updatePowSaveBtn()" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;background:#fff;box-sizing:border-box;">
                        <option value="">Select a territory</option>
                        <option>Bali</option><option>Jakarta</option><option>New South Wales</option><option>Victoria</option><option>Queensland</option><option>Western Australia</option><option>South Australia</option><option>Tasmania</option><option>ACT</option><option>Northern Territory</option><option>England</option><option>Scotland</option><option>Wales</option><option>Other</option>
                    </select>
                </div>
            </div>
            <div style="margin-top:24px;display:flex;gap:10px;justify-content:flex-start;">
                <button onclick="closePowModal()" style="background:#fff;color:#e63364;border:2px solid #e63364;border-radius:6px;padding:9px 20px;font-size:13px;font-weight:600;cursor:pointer;">Cancel</button>
                <button id="powSaveBtn" onclick="savePowModal()" style="background:#2e7d5e;color:#fff;border:none;border-radius:6px;padding:10px 20px;font-size:13px;font-weight:600;cursor:not-allowed;opacity:0.5;transition:opacity .2s;">Save</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══ Working Time Pattern Modal ═══ --}}
<div id="wtpModal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;background:rgba(0,0,0,.5);align-items:center;justify-content:center;">
    <style>
        .wtp-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        .wtp-switch input:checked + .wtp-slider {
            background-color: #2e7d5e !important;
        }
        .wtp-switch input:focus + .wtp-slider {
            box-shadow: 0 0 1px #2e7d5e;
        }
        .wtp-switch input:checked + .wtp-slider:before {
            transform: translateX(20px);
        }
        .wtp-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        .wtp-day-row-checked {
            display: grid;
            grid-template-columns: 140px 140px 140px 140px 1fr;
            gap: 12px;
            align-items: center;
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fff;
            transition: background 0.2s;
        }
        .wtp-day-row-unchecked {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            background: #f0fdf4;
            color: #166534;
            transition: background 0.2s;
        }
        .wtp-time-input {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px 10px;
            font-size: 14px;
            box-sizing: border-box;
            background: #fff;
            outline: none;
        }
        .wtp-split-btn {
            color: #2e7d5e;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            border: none;
            background: none;
            padding: 0;
        }
        .wtp-split-btn:hover {
            color: #b89047;
            text-decoration: underline;
        }
        .wtp-split-row {
            display: grid;
            grid-template-columns: 140px 140px 140px 140px 1fr;
            gap: 12px;
            align-items: center;
            padding: 6px 12px;
            margin-top: -6px;
            border-left: 2px solid #2e7d5e;
            background: #fafafa;
            border-radius: 0 0 8px 8px;
        }
    </style>
    <div style="background:#fff;border-radius:12px;width:780px;max-width:95vw;max-height:92vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.3);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #e5e7eb;background:#2e7d5e;border-radius:12px 12px 0 0;flex-shrink:0;">
            <h3 style="font-size:16px;font-weight:700;color:#fff;margin:0;">Create new working time pattern</h3>
            <button onclick="closeWtpModal()" style="background:none;border:none;color:#fff;font-size:22px;cursor:pointer;line-height:1;padding:0;">✕</button>
        </div>
        <div style="padding:24px;overflow-y:auto;flex:1;">
            <h4 style="font-size:16px;font-weight:700;color:#1f2937;margin:0 0 4px 0;">Add working time pattern</h4>
            <p style="font-size:13px;color:#6b7280;margin:0 0 20px 0;line-height:1.4;">
                Please note: You cannot edit a pattern after it has been added so make sure you are happy before finalising it.
            </p>

            <!-- Top Fields -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                <div>
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Pattern name <span style="color:#ef4444;font-size:12px;float:right;">Required</span></label>
                    <input type="text" id="wtpName" placeholder="Pattern name" oninput="updateWtpSaveBtn()" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;box-sizing:border-box;">
                </div>
                <div style="display:flex;align-items:center;gap:12px;padding-top:24px;">
                    <label style="font-size:13px;font-weight:600;color:#374151;display:flex;align-items:center;gap:6px;cursor:pointer;">
                        Make default
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;border-radius:50%;background:#e5e7eb;color:#4b5563;font-size:11px;cursor:help;font-weight:bold;" title="This pattern will be automatically selected for new employees.">i</span>
                    </label>
                    <label class="wtp-switch" style="position:relative;display:inline-block;width:44px;height:24px;">
                        <input type="checkbox" id="wtpMakeDefault" style="opacity:0;width:0;height:0;">
                        <span class="wtp-slider"></span>
                    </label>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
                <div>
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Copy existing pattern?</label>
                    <select id="wtpCopyPattern" onchange="copyExistingWtpPattern()" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:10px 12px;font-size:14px;background:#fff;box-sizing:border-box;">
                        <option value="">Add new pattern</option>
                    </select>
                </div>
                <div>
                    <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Pattern start date</label>
                    <input type="date" id="wtpStartDate" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:9px 12px;font-size:14px;box-sizing:border-box;background:#fff;">
                </div>
            </div>

            <!-- Time and breaks -->
            <h5 style="font-size:14px;font-weight:700;color:#1f2937;margin:0 0 4px 0;">Time and breaks</h5>
            <p style="font-size:13px;color:#6b7280;margin:0 0 16px 0;">Enter start and end times for your working time pattern.</p>

            <!-- Table Headers -->
            <div style="display:grid;grid-template-columns:140px 140px 140px 140px 1fr;gap:12px;padding:0 12px 8px 12px;border-bottom:1px solid #e5e7eb;font-size:11px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:12px;">
                <div>Days</div>
                <div>Start time</div>
                <div>End time</div>
                <div>Break duration</div>
                <div></div>
            </div>

            <!-- Rows Container -->
            <div id="wtpDaysContainer" style="display:flex;flex-direction:column;gap:10px;margin-bottom:24px;">
                <!-- Dynamically Rendered -->
            </div>

            <!-- Bottom Controls -->
            <div style="display:flex;align-items:center;justify-content:space-between;border-top:1px solid #e5e7eb;padding-top:20px;flex-wrap:wrap;gap:16px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <span style="font-size:13px;color:#374151;display:flex;align-items:center;gap:6px;font-weight:500;">
                        <svg style="width:18px;height:18px;color:#4b5563;flex-shrink:0;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21.5 2v6h-6M2.5 22v-6h6" />
                            <path d="M2 12a10 10 0 0 1 18-6l1.5 2M22 12a10 10 0 0 1-18 6l-1.5-2" />
                        </svg>
                        <span id="wtpRepeatsLabel">Pattern repeats every 7 days</span>
                    </span>
                    <button type="button" id="btnRemoveWtp7Days" onclick="removeWtp7Days()" style="background:#fff;color:#ef4444;border:1px solid #ef4444;border-radius:6px;padding:6px 12px;font-size:12px;font-weight:600;cursor:pointer;transition:all 0.2s;">Remove 7 days</button>
                    <button type="button" onclick="addWtp7Days()" style="background:#fff;color:#2e7d5e;border:1px solid #2e7d5e;border-radius:6px;padding:6px 12px;font-size:12px;font-weight:600;cursor:pointer;transition:all 0.2s;">Add 7 days</button>
                    <button type="button" onclick="addWtpDay()" style="background:#2e7d5e;color:#fff;border:none;border-radius:6px;padding:7px 12px;font-size:12px;font-weight:600;cursor:pointer;transition:all 0.2s;">Add day</button>
                </div>
                <div id="wtpSummaryLabel" style="font-size:13px;font-weight:700;color:#1f2937;">
                    5 working days selected totalling 40 hrs, excluding breaks
                </div>
            </div>
        </div>
        <div style="padding:20px 24px;border-top:1px solid #e5e7eb;display:flex;gap:12px;justify-content:flex-start;flex-shrink:0;">
            <button onclick="closeWtpModal()" style="background:#fff;color:#e63364;border:2px solid #e63364;border-radius:6px;padding:9px 20px;font-size:13px;font-weight:600;cursor:pointer;">Cancel</button>
            <button id="wtpSaveBtn" onclick="saveWtpModal()" style="background:#2e7d5e;color:#fff;border:none;border-radius:6px;padding:10px 20px;font-size:13px;font-weight:600;cursor:not-allowed;opacity:0.5;transition:opacity .2s;">Save</button>
        </div>
    </div>
</div>


{{-- ═══ Chat Widget ═══ --}}
<div class="ae-chat-wrap">
    <div class="ae-chat-bubble" id="chatBubble">
        <span>Have questions? Let's chat</span>
        <button class="close-x" onclick="document.getElementById('chatBubble').style.display='none'">×</button>
    </div>
    <button class="ae-chat-bot" onclick="var b=document.getElementById('chatBubble'); b.style.display=b.style.display==='none'?'flex':'none';">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a2 2 0 012 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 017 7v1h1a1 1 0 110 2h-1v1a7 7 0 01-7 7h-4a7 7 0 01-7-7v-1H2a1 1 0 110-2h1v-1a7 7 0 017-7h1V5.73c-.6-.34-1-.99-1-1.73a2 2 0 012-2zm-4 12a1.5 1.5 0 100 3 1.5 1.5 0 000-3zm8 0a1.5 1.5 0 100 3 1.5 1.5 0 000-3z"/></svg>
    </button>
</div>

<script>
window.onerror = function(message, source, lineno, colno, error) {
    alert("JavaScript Error: " + message + " at line " + lineno + ":" + colno + "\nSource: " + source);
    return false;
};
var ADDED_EMPLOYEES = [];
var SUBMITTED_SUCCESS_IDS = [];
var selectedEmpId = null;
var nextEmpId = 1;
var updateUrlTemplate = '{{ route("admin.linkers-hub.update-employee", ["id" => "__ID__"], false) }}';
var destroyUrlTemplate = '{{ route("admin.linkers-hub.destroy-employee", ["id" => "__ID__"], false) }}';
var profileUrlTemplate  = '{{ route("admin.linkers-hub.employee-profile", ["id" => "__ID__"], false) }}'

// Dropdown "Select..." placeholders that must never be sent to the server as
// literal values (they mean "nothing chosen yet" — the real value is null).
var SELECT_PLACEHOLDERS = [
    'Select rate', 'Select frequency', 'Select reason',
    'Select jurisdiction', 'Country of issue', 'Select Title'
];
function cleanSelectValue(val) {
    if (!val) return null;
    return SELECT_PLACEHOLDERS.indexOf(val) !== -1 ? null : val;
}

function updateSaveContinueBtnState() {
    var firstNameEl = document.getElementById('empFirstName');
    var lastNameEl = document.getElementById('empLastName');
    var emailEl = document.getElementById('empEmail');
    var startDateEl = document.getElementById('empStartDate');
    var saveContinueBtn = document.getElementById('saveAndContinueBtn');

    if (!saveContinueBtn) return;

    var fv = firstNameEl ? firstNameEl.value.trim() : '';
    var lv = lastNameEl ? lastNameEl.value.trim() : '';
    var ev = emailEl ? emailEl.value.trim() : '';
    var sv = startDateEl ? startDateEl.value.trim() : '';

    var isEmailValid = true;
    if (ev) {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        isEmailValid = emailRegex.test(ev);
    } else {
        isEmailValid = false;
    }

    var allFilled = fv.length > 0 && lv.length > 0 && ev.length > 0 && sv.length > 0 && isEmailValid;

    if (allFilled) {
        saveContinueBtn.style.background = '#2e7d5e';
        saveContinueBtn.style.opacity = '1';
        saveContinueBtn.style.cursor = 'pointer';
    } else {
        saveContinueBtn.style.background = '#2e7d5e';
        saveContinueBtn.style.opacity = '0.4';
        saveContinueBtn.style.cursor = 'not-allowed';
    }
}

function closeDetailsPanel() {
    if (selectedEmpId) {
        var emp = ADDED_EMPLOYEES.find(function(e) { return e.id === selectedEmpId; });
        if (emp) {
            if (!emp.details) emp.details = {};
            DETAIL_FIELD_IDS.forEach(function(fid) {
                var el = document.getElementById(fid);
                if (el) emp.details[fid] = el.value;
            });
            renderEmployeeList();
        }
        
        // Attempt to save to server in background, ignoring alerts on failure
        var url = updateUrlTemplate.replace('__ID__', selectedEmpId);
        var payload = {
            title: (emp.details['empTitle'] === 'Select Title' || !emp.details['empTitle']) ? null : emp.details['empTitle'],
            first_name: emp.details['empFirstName'] || emp.first_name,
            last_name: emp.details['empLastName'] || emp.last_name,
            gender: emp.details['empGender'] || 'Unspecified',
            dob: (emp.details['empDob'] && emp.details['empDob'].trim()) ? emp.details['empDob'].trim() : null,
            email: (emp.details['empEmail'] && emp.details['empEmail'].trim()) ? emp.details['empEmail'].trim() : null,
            mobile: (emp.details['empMobile'] && emp.details['empMobile'].trim()) ? emp.details['empMobile'].trim() : null,
            work_phone: (emp.details['empWorkPhone'] && emp.details['empWorkPhone'].trim()) ? emp.details['empWorkPhone'].trim() : null,
            job_title: (emp.details['empJobTitle'] && emp.details['empJobTitle'].trim()) ? emp.details['empJobTitle'].trim() : null,
            start_date: (emp.details['empStartDate'] && emp.details['empStartDate'].trim()) ? emp.details['empStartDate'].trim() : null,
            middle_name: (emp.details['empMiddleName'] && emp.details['empMiddleName'].trim()) ? emp.details['empMiddleName'].trim() : null,
            address_1: emp.details['empAddr1'] || null,
            address_2: emp.details['empAddr2'] || null,
            address_3: emp.details['empAddr3'] || null,
            city: emp.details['empCity'] || null,
            territory: emp.details['empTerritory'] || null,
            postcode: emp.details['empPostcode'] || null,
            bank_acc_name: emp.details['empBankAccName'] || null,
            bank_name: emp.details['empBankName'] || null,
            bank_branch: emp.details['empBankBranch'] || null,
            bank_acc_no: emp.details['empBankAccNo'] || null,
            bank_bsb: emp.details['empBankBsb'] || null,
            salary: emp.details['empSalary'] || '0',
            pay_rate: cleanSelectValue(emp.details['empRate']),
            pay_frequency: cleanSelectValue(emp.details['empPayFreq']),
            effective_from: (emp.details['empEffectiveFrom'] && emp.details['empEffectiveFrom'].trim()) ? emp.details['empEffectiveFrom'].trim() : null,
            salary_reason: cleanSelectValue(emp.details['empReason']),
            payroll_no: emp.details['empPayrollNo'] || null,
            tfn: emp.details['empTfn'] || null,
            passport_no: emp.details['empPassportNo'] || null,
            passport_country: cleanSelectValue(emp.details['empPassportCountry']),
            passport_expiry: (emp.details['empPassportExpiry'] && emp.details['empPassportExpiry'].trim()) ? emp.details['empPassportExpiry'].trim() : null,
            licence_no: emp.details['empLicenceNo'] || null,
            licence_country: cleanSelectValue(emp.details['empLicenceCountry']),
            licence_class: emp.details['empLicenceClass'] || null,
            licence_expiry: (emp.details['empLicenceExpiry'] && emp.details['empLicenceExpiry'].trim()) ? emp.details['empLicenceExpiry'].trim() : null,
            visa_no: emp.details['empVisaNo'] || null,
            visa_expiry: (emp.details['empVisaExpiry'] && emp.details['empVisaExpiry'].trim()) ? emp.details['empVisaExpiry'].trim() : null,
            jurisdiction: cleanSelectValue(emp.details['empJurisdiction']),
            employee_type: emp.details['empEmployeeType'] || null,
            working_schedule: getWorkingScheduleForPattern(emp.details['empWorkingPattern'])
        };
        makeAjaxRequest(url, 'PUT', payload, function(data) {
            console.log('Background save on close succeeded.');
        }, function(err) {
            console.warn('Background save on close failed, ignored.', err);
        });
    }

    var layout = document.querySelector('.ae-filled-layout');
    if (layout) {
        layout.classList.add('details-closed');
    }
}

function openDetailsPanel() {
    var layout = document.querySelector('.ae-filled-layout');
    if (layout) {
        layout.classList.remove('details-closed');
    }
}

function switchAddTab(el, tabId) {
    openDetailsPanel();
    if (el) {
        el.classList.remove('locked');
    }
    document.querySelectorAll('.ae-step').forEach(function(t) {
        t.classList.remove('active');
    });
    if (el) {
        el.classList.add('active');
    }
    
    // Hide all sections, show active section
    document.querySelectorAll('.ae-tab-section').forEach(function(s) {
        s.style.display = 'none';
    });
    var section = document.getElementById('section-' + tabId);
    if (section) {
        section.style.display = 'block';
    }
    
    // Toggle footer buttons
    var isDetails = (tabId === 'employee-details');
    var isEmployment = (tabId === 'employment-details');
    var isSummary = (tabId === 'summary');
    
    document.querySelectorAll('.step1-btn').forEach(function(b) { b.style.display = isDetails ? 'block' : 'none'; });
    document.querySelectorAll('.step2-btn').forEach(function(b) { b.style.display = isEmployment ? 'block' : 'none'; });
    document.querySelectorAll('.step3-btn').forEach(function(b) { b.style.display = isSummary ? 'block' : 'none'; });

    // Hide sidebar and main header for Summary, show for Details/Employment
    var sidebar = document.querySelector('.ae-filled-sidebar');
    var header = document.getElementById('empDetailHeader');
    if (sidebar) {
        sidebar.style.display = isSummary ? 'none' : 'flex';
    }
    if (header) {
        header.style.display = isSummary ? 'none' : 'flex';
    }
    
    if (isSummary) {
        renderSummary();
    }
}

function updateSaveContinueBtn2State() {
    var jurisdictionEl = document.getElementById('empJurisdiction');
    var empTypeEl = document.getElementById('empEmployeeType');
    var saveContinueBtn2 = document.getElementById('saveAndContinueBtn2');
    
    if (!saveContinueBtn2) return;
    
    var jv = jurisdictionEl ? jurisdictionEl.value : '';
    var et = empTypeEl ? empTypeEl.value : '';
    
    var allFilled = jv !== '' && et !== '';
    
    if (allFilled) {
        saveContinueBtn2.style.background = '#2e7d5e';
        saveContinueBtn2.style.opacity = '1';
        saveContinueBtn2.style.cursor = 'pointer';
    } else {
        saveContinueBtn2.style.background = '#2e7d5e';
        saveContinueBtn2.style.opacity = '0.4';
        saveContinueBtn2.style.cursor = 'not-allowed';
    }
}

function selectEmployeeTypeCard(cardEl) {
    document.querySelectorAll('.emp-type-card').forEach(function(c) {
        // Only deselect cards in the same grid (employee type grid)
        if (c.parentElement === cardEl.parentElement) {
            c.classList.remove('selected');
        }
    });
    cardEl.classList.add('selected');
    var val = cardEl.getAttribute('data-val');
    document.getElementById('empEmployeeType').value = val;
    
    // Update dynamic visibility of all sections
    updateSectionVisibility();

    if (selectedEmpId) {
        var emp = ADDED_EMPLOYEES.find(function(e) { return e.id === selectedEmpId; });
        if (emp) {
            if (!emp.details) emp.details = {};
            emp.details['empEmployeeType'] = val;
        }
    }
    updateSaveContinueBtn2State();
}

function selectLeaveUnitCard(cardEl) {
    // Only deselect cards in the Contract Details leave unit grid
    var parentGrid = cardEl.parentElement;
    parentGrid.querySelectorAll('.emp-type-card').forEach(function(c) {
        c.classList.remove('selected');
    });
    cardEl.classList.add('selected');
    var val = cardEl.getAttribute('data-val');
    document.getElementById('empLeaveUnit').value = val;

    // Show info banner
    var banner = document.getElementById('contractInfoBanner');
    var bannerTitle = document.getElementById('contractBannerTitle');
    var bannerDesc = document.getElementById('contractBannerDesc');
    if (banner && bannerTitle && bannerDesc) {
        if (val === 'Hours') {
            bannerTitle.textContent = 'Hours leave selected';
            bannerDesc.textContent = "You won't be able to change this after you complete the set up.";
        } else {
            bannerTitle.textContent = 'Days leave selected';
            bannerDesc.textContent = "Employees set up with leave taken in days, will not include annual leave balances. They will only see the number of days taken, and not what they have accrued.";
        }
        banner.style.display = 'flex';
    }

    // Update dynamic visibility of all sections
    updateSectionVisibility();

    // Save to employee details
    if (selectedEmpId) {
        var emp = ADDED_EMPLOYEES.find(function(e) { return e.id === selectedEmpId; });
        if (emp) {
            if (!emp.details) emp.details = {};
            emp.details['empLeaveUnit'] = val;
        }
    }
}

function updateSectionVisibility() {
    var val = document.getElementById('empEmployeeType').value || '';
    var leaveUnitVal = document.getElementById('empLeaveUnit').value || '';

    var wtpSection = document.getElementById('workingTimePatternSection');
    var cdSection = document.getElementById('contractDetailsSection');
    var varHoursSection = document.getElementById('variableHoursSection');
    var contractedDaysSec = document.getElementById('contractedDaysSection');
    var averageDaySec = document.getElementById('averageWorkingDaySection');
    var accrualSec = document.getElementById('accrualRateSection');
    var albSection = document.getElementById('annualLeaveBalanceSection');

    // Highlight the correct Leave Unit card visually
    var contractGrid = document.querySelector('#contractDetailsSection .emp-type-grid');
    if (contractGrid) {
        contractGrid.querySelectorAll('.emp-type-card').forEach(function(card) {
            if (card.getAttribute('data-val') === leaveUnitVal) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        });
    }

    // Default hide all variable sections
    if (varHoursSection) varHoursSection.style.display = 'none';
    if (contractedDaysSec) contractedDaysSec.style.display = 'none';
    if (averageDaySec) averageDaySec.style.display = 'none';
    if (albSection) albSection.style.display = 'none';

    if (val === 'Fixed, full or part time') {
        if (wtpSection) wtpSection.style.display = '';
        if (cdSection) cdSection.style.display = '';
        if (accrualSec) accrualSec.style.display = (leaveUnitVal === 'Hours') ? '' : 'none';
    } else if (val === 'Short hours or variable') {
        if (wtpSection) wtpSection.style.display = 'none';
        if (cdSection) cdSection.style.display = '';
        if (varHoursSection) varHoursSection.style.display = '';

        if (leaveUnitVal === 'Days') {
            if (contractedDaysSec) contractedDaysSec.style.display = '';
            if (accrualSec) accrualSec.style.display = 'none';
        } else if (leaveUnitVal === 'Hours') {
            if (averageDaySec) averageDaySec.style.display = '';
            if (accrualSec) accrualSec.style.display = '';
            if (albSection) albSection.style.display = '';
            calculateVariableAnnualLeaveRecommended();
        } else {
            if (accrualSec) accrualSec.style.display = 'none';
        }
    } else {
        // Casual or other
        if (wtpSection) wtpSection.style.display = 'none';
        if (cdSection) cdSection.style.display = 'none';
    }
}

function calculateVariableAnnualLeaveRecommended() {
    var hrsInput = document.getElementById('empContractedHours');
    var minsInput = document.getElementById('empContractedMinutes');
    var accrualInput = document.getElementById('empAccrualRate');
    
    if (!hrsInput || !minsInput || !accrualInput) return;

    var hrs = parseInt(hrsInput.value || 0);
    var mins = parseInt(minsInput.value || 0);
    var totalWeeklyHours = hrs + (mins / 60);

    var accrualVal = accrualInput.value; // e.g. "4 weeks"
    var weeks = 0;
    if (accrualVal) {
        var match = accrualVal.match(/(\d+)/);
        if (match) weeks = parseInt(match[1]);
    }

    var recTotalHours = totalWeeklyHours * weeks;
    var recHrs = Math.floor(recTotalHours);
    var recMins = Math.round((recTotalHours - recHrs) * 60);

    var recLabel = document.getElementById('empAnnualLeaveRecommendedLabel');
    if (recLabel) {
        recLabel.textContent = 'Recommended: ' + recHrs + ' hrs ' + recMins + ' mins';
    }
}

function renderSummary() {
    if (!selectedEmpId) return;
    var emp = ADDED_EMPLOYEES.find(function(e) { return e.id === selectedEmpId; });
    if (!emp) return;
    
    var details = emp.details || {};
    
    // 1. Avatar and header
    var fName = details['empFirstName'] || emp.first_name || '';
    var lName = details['empLastName'] || emp.last_name || '';
    var fullName = (fName + ' ' + lName).trim() || 'No Name';
    var jobTitle = details['empJobTitle'] || '-';
    
    document.getElementById('summaryFullName').textContent = fullName;
    document.getElementById('summaryJobTitle').textContent = jobTitle;
    
    // Initials for avatar
    var initials = '';
    if (fName) initials += fName.charAt(0).toUpperCase();
    if (lName) initials += lName.charAt(0).toUpperCase();
    if (!initials) initials = 'JD';
    document.getElementById('summaryAvatar').textContent = initials;
    
    // Generate avatar color
    var colors = ['#5c6bc0', '#ef5350', '#26a69a', '#ab47bc', '#42a5f5', '#ff7043', '#8d6e63', '#66bb6a', '#ec407a', '#78909c', '#ffa726', '#7e57c2'];
    var hash = 0;
    for (var i = 0; i < fullName.length; i++) {
        hash = fullName.charCodeAt(i) + ((hash << 5) - hash);
    }
    var colorIdx = Math.abs(hash) % colors.length;
    document.getElementById('summaryAvatar').style.background = colors[colorIdx];
    
    // Helper function to return '-' if value is empty/null/undefined
    function valOrDash(val) {
        if (val === undefined || val === null) return '-';
        var s = String(val).trim();
        return s === '' ? '-' : s;
    }

    // 2. Card 1: Employee Details
    document.getElementById('sumEmail').textContent = valOrDash(details['empEmail'] || emp.email);
    document.getElementById('sumPhone').textContent = valOrDash(details['empMobile']);
    document.getElementById('sumWorkPhone').textContent = valOrDash(details['empWorkPhone']);
    
    var dob = valOrDash(details['empDob']);
    var gender = valOrDash(details['empGender']);
    document.getElementById('sumGenderDob').textContent = (gender === '-' && dob === '-') ? '-' : gender + ' / ' + dob;
    document.getElementById('sumStartDate').textContent = valOrDash(details['empStartDate']);
    
    // 3. Card 2: Address & Emergency Contacts
    var addrParts = [];
    if (details['empAddr1']) addrParts.push(details['empAddr1']);
    if (details['empAddr2']) addrParts.push(details['empAddr2']);
    if (details['empAddr3']) addrParts.push(details['empAddr3']);
    var cityStateZip = [];
    if (details['empCity']) cityStateZip.push(details['empCity']);
    if (details['empTerritory']) cityStateZip.push(details['empTerritory']);
    if (details['empPostcode']) cityStateZip.push(details['empPostcode']);
    if (cityStateZip.length > 0) addrParts.push(cityStateZip.join(' '));
    document.getElementById('sumAddress').textContent = addrParts.join(', ') || '-';

    var emergencyContacts = emp.emergency_contacts || [];
    if (emergencyContacts.length > 0) {
        var primaryContact = emergencyContacts[0];
        var primaryName = (primaryContact.first_name + ' ' + primaryContact.last_name).trim();
        document.getElementById('sumEmergencyName').textContent = primaryName || '-';
        document.getElementById('sumEmergencyRelation').textContent = valOrDash(primaryContact.relationship);
        
        var phones = [];
        if (primaryContact.mobile) phones.push(primaryContact.mobile + ' (Mobile)');
        if (primaryContact.home_phone) phones.push(primaryContact.home_phone + ' (Home)');
        if (primaryContact.work_phone) phones.push(primaryContact.work_phone + ' (Work)');
        document.getElementById('sumEmergencyPhone').textContent = phones.join(', ') || '-';
    } else {
        document.getElementById('sumEmergencyName').textContent = '-';
        document.getElementById('sumEmergencyRelation').textContent = '-';
        document.getElementById('sumEmergencyPhone').textContent = '-';
    }

    // 4. Card 3: Bank Details
    document.getElementById('sumBankAccName').textContent = valOrDash(details['empBankAccName']);
    
    var bankName = details['empBankName'] || '';
    var bankBranch = details['empBankBranch'] || '';
    if (bankName && bankBranch) {
        document.getElementById('sumBankNameBranch').textContent = bankName + ' / ' + bankBranch;
    } else {
        document.getElementById('sumBankNameBranch').textContent = valOrDash(bankName || bankBranch);
    }
    
    var bankBsb = details['empBankBsb'] || '';
    var bankAccNo = details['empBankAccNo'] || '';
    if (bankBsb && bankAccNo) {
        document.getElementById('sumBankAccNoBsb').textContent = bankBsb + ' / ' + bankAccNo;
    } else {
        document.getElementById('sumBankAccNoBsb').textContent = valOrDash(bankBsb || bankAccNo);
    }

    // 5. Card 4: Employment Details
    document.getElementById('sumJurisdiction').textContent = valOrDash(details['empJurisdiction']);
    document.getElementById('sumEmployeeType').textContent = valOrDash(details['empEmployeeType']);
    document.getElementById('sumPlaceOfWork').textContent = valOrDash(details['empPlaceOfWork']);
    
    var salary = details['empSalary'] || '';
    var rate = details['empRate'] || '';
    var formattedSalary = '-';
    if (salary && salary !== '0') {
        formattedSalary = '$' + salary;
        if (rate && rate !== 'Select rate') {
            formattedSalary += ' (' + rate + ')';
        }
    } else if (rate && rate !== 'Select rate') {
        formattedSalary = rate;
    }
    document.getElementById('sumSalary').textContent = formattedSalary;
    
    document.getElementById('sumPayFreq').textContent = valOrDash(details['empPayFreq']);
    
    var effFrom = details['empEffectiveFrom'] || '';
    var effReason = details['empReason'] || '';
    if (effFrom && effReason) {
        document.getElementById('sumSalaryEffective').textContent = effFrom + ' / ' + effReason;
    } else {
        document.getElementById('sumSalaryEffective').textContent = valOrDash(effFrom || effReason);
    }
    
    document.getElementById('sumPayrollNo').textContent = valOrDash(details['empPayrollNo']);

    // 6. Card 5: Leave & Working Time
    document.getElementById('sumLeaveUnit').textContent = valOrDash(details['empLeaveUnit']);
    document.getElementById('sumWorkingPattern').textContent = valOrDash(details['empWorkingPattern']);
    
    var ch = details['empContractedHours'] || '';
    var cm = details['empContractedMinutes'] || '';
    var contractedHours = '-';
    if (ch || cm) {
        contractedHours = (ch || '0') + ' hrs ' + (cm || '0') + ' mins';
    }
    document.getElementById('sumContractedHours').textContent = contractedHours;
    
    var leaveUnitVal = details['empLeaveUnit'] || '';
    var contractDetails = '-';
    if (leaveUnitVal === 'Days') {
        var days = details['empContractedDays'] || '';
        contractDetails = days ? days + ' days' : '-';
    } else if (leaveUnitVal === 'Hours') {
        var avgHrs = details['empAverageHours'] || '';
        var avgMins = details['empAverageMinutes'] || '';
        if (avgHrs || avgMins) {
            contractDetails = (avgHrs || '0') + ' hrs ' + (avgMins || '0') + ' mins (Average working day)';
        }
    }
    document.getElementById('sumContractDetails').textContent = contractDetails;
    
    document.getElementById('sumAccrualRate').textContent = valOrDash(details['empAccrualRate']);
    
    var alh = details['empAnnualLeaveHours'] || '';
    var alm = details['empAnnualLeaveMinutes'] || '';
    var alb = '-';
    if (alh || alm) {
        alb = (alh || '0') + ' hrs ' + (alm || '0') + ' mins';
    }
    document.getElementById('sumAnnualLeaveBalance').textContent = alb;

    // 7. Card 6: Sensitive Details
    document.getElementById('sumTfn').textContent = valOrDash(details['empTfn']);
    
    var passNo = details['empPassportNo'] || '';
    var passCountry = details['empPassportCountry'] || '';
    var passExpiry = details['empPassportExpiry'] || '';
    var passportDetails = [];
    if (passNo) passportDetails.push(passNo);
    if (passCountry) passportDetails.push(passCountry);
    if (passExpiry) passportDetails.push(passExpiry);
    document.getElementById('sumPassport').textContent = passportDetails.join(' / ') || '-';
    
    var licNo = details['empLicenceNo'] || '';
    var licCountry = details['empLicenceCountry'] || '';
    var licClass = details['empLicenceClass'] || '';
    var licExpiry = details['empLicenceExpiry'] || '';
    var licenceDetails = [];
    if (licNo) licenceDetails.push(licNo);
    if (licCountry) licenceDetails.push(licCountry);
    if (licClass) licenceDetails.push('Class: ' + licClass);
    if (licExpiry) licenceDetails.push(licExpiry);
    document.getElementById('sumLicence').textContent = licenceDetails.join(' / ') || '-';
    
    var visaNo = details['empVisaNo'] || '';
    var visaExpiry = details['empVisaExpiry'] || '';
    var visaDetails = [];
    if (visaNo) visaDetails.push(visaNo);
    if (visaExpiry) visaDetails.push(visaExpiry);
    document.getElementById('sumVisa').textContent = visaDetails.join(' / ') || '-';
}

// Client-side name validation (mirrors server-side)
function validateName(name) {
    name = name.trim();
    var letters = name.replace(/[^a-zA-Z]/g, '');
    if (letters.length < 2) return 'Must contain at least 2 letters.';
    if (name.length > 50) return 'Too long (max 50 characters).';
    if (!/^[a-zA-ZÀ-ÿĀ-žА-яÑñ\s'\-\.]+$/.test(name)) return 'Contains invalid characters.';
    if (/(.)\1{2,}/.test(name.toLowerCase())) return 'Contains repeated characters.';

    var lower = name.toLowerCase();
    var cleaned = lower.replace(/[\s.\-']+/g, '');

    var spamPatterns = ['qwert','asdf','zxcv','qazwsx','poiuy','lkjhg','mnbvc','abcdef','zyxwvu'];
    for (var i = 0; i < spamPatterns.length; i++) {
        if (lower.indexOf(spamPatterns[i]) !== -1) return 'Does not appear to be a valid name.';
    }

    var nonsense = [
        'test','testing','tester','testtest','testy',
        'blah','blabla','bla','blahblah','bleh','bluh',
        'foo','foobar','fubar',
        'lorem','ipsum','dolor','amet',
        'null','undefined','none','nil','void','empty','blank',
        'admin','user','root','guest','system','login','password',
        'sample','example','demo','dummy','fake','temp','tmp',
        'unknown','nobody','noname','nope','anon','anonymous',
        'aaa','bbb','ccc','ddd','eee','fff','ggg','hhh','iii',
        'jjj','kkk','lll','mmm','nnn','ooo','ppp','qqq','rrr',
        'sss','ttt','uuu','vvv','www','xxx','yyy','zzz',
        'abc','xyz','qwerty','abcd','abcde',
        'hello','hey','bye','sup','yo','yep','yup','yeah','yay',
        'nah','naw','nuh','ugh','meh','hmm','huh','duh','pfft',
        'ooh','aah','eww','ew','wow','ohh','ahh','umm','err',
        'shh','tsk','psst','oof','yikes','oops','ouch',
        'poo','poop','poopy','poopoo','poopie',
        'pee','peepee','peep','pipi',
        'boo','booboo','boob','boobs','boobie',
        'butt','butts','bum','bumm','bummy',
        'fart','farty','toot','toots','tootie',
        'wee','weewee','weeee',
        'doo','doody','doodoo','doofus',
        'goo','gooey','goop','goober',
        'loo','loopy','loser',
        'barf','barfy','yuck','yucky','icky','gross',
        'snot','snotty','booger','boogers',
        'dork','dorky','nerd','nerdy','geek','geeky',
        'dumb','dumbo','dumdum','stupid','idiot','moron','fool',
        'ugly','fatty','skinny','stinky','smelly','stink',
        'lame','lameo','sucker','noob','newbie',
        'wimp','wimpy','sissy','pansy','wuss','wussy',
        'nutty','nuts','bonkers','crazy','wacko','weirdo',
        'turd','turds',
        'moo','baa','meow','woof','bark','quack','oink',
        'neigh','cluck','ribbit','hiss','roar','buzz',
        'mew','purr','caw','chirp','tweet','squawk',
        'la','da','na','ba','ga','ta','ka','pa','fa','za',
        'lala','dada','nana','baba','gaga','tata','kaka','papa',
        'mama','bibi','bobo','bubu','didi','dudu','fifi',
        'gigi','jojo','kiki','koko','lili','lolo','lulu',
        'mimi','nini','nono','pipi','pupu','riri','sisi',
        'titi','toto','tutu','zuzu','wawa','wewe',
        'haha','hehe','hihi','hoho','huhu',
        'blip','blob','blop','blub','bonk','boop','beep',
        'focus','ding','dong','ping','pong','bing','bong','bam','boom',
        'zap','zip','zop','zoop','zoom','zing',
        'yada','yadda',
        'lol','lmao','rofl','omg','wtf','brb','idk','smh',
        'fml','yolo','swag','derp','herp','kek',
        'bruh','skibidi','rizz','gyatt','sigma','chad','karen',
        'thing','stuff','nothing','something','whatever','whoever',
        'person','people','human','man','woman','boy','girl',
        'name','first','last','employee','worker','staff',
        'yes','no','ok','okay','maybe','sure','right','wrong',
        'good','bad','nice','cool','hot','cold',
        'big','small','tall','short','fat','thin',
        'red','blue','green','black','white','pink','purple',
        'one','two','three','four','five','six','seven',
        'dog','cat','pig','cow','rat','bat','bug','ant','fly',
        'eat','run','sit','hit','die','cry','lie',
        'joke','joker','friend','enemy','boss','chief','king','queen',
        'prince','lord','lady','duke','master','slave','hero','villain',
        'angel','devil','ghost','zombie','alien','robot','ninja','pirate',
        'happy','sad','angry','mad','glad','scared','brave','lazy',
        'funny','silly','smart','clever','strong','weak','fast','slow',
        'loud','quiet','soft','hard','easy','tough','rough','smooth',
        'dark','light','bright','dim','rich','poor','cheap','free',
        'real','true','false','alive','dead','lost','found','broken',
        'love','hate','kiss','hug','kill','fight','help','save',
        'work','play','stop','go','come','stay','leave','wait',
        'walk','jump','swim','sing','dance','sleep','wake','dream',
        'look','see','hear','feel','think','know','want','need',
        'give','take','make','break','open','close','push','pull',
        'food','water','milk','beer','wine','cake','bread','rice',
        'fish','meat','egg','salt','sugar','candy','fruit',
        'house','home','room','door','wall','floor','roof','window',
        'table','chair','bed','desk','lamp','phone','book','card',
        'money','cash','gold','silver','iron','steel','wood','stone',
        'fire','rain','snow','wind','storm','cloud','star','moon',
        'tree','leaf','seed','dirt','sand','rock','mud','dust',
        'hand','foot','head','face','nose','eye','ear','mouth',
        'hair','skin','bone','blood','heart','brain','back','neck',
        'baby','child','kid','teen','adult','old','young','new',
        'game','ball','team','club','group','band','gang','crew',
        'car','bus','boat','ship','bike','road','path','bridge',
        'city','town','land','farm','park','lake','hill','river',
        'world','earth','sky','space','time','life','death','soul',
        'god','king','war','peace','pain','joy','fear','hope',
        'luck','fate','power','magic','spell','trick','trap','plan',
        'rule','law','crime','jail','gun','bomb','drug','poison',
        'cup','hat','bag','box','key','ring','bell','flag',
        'song','note','word','code','sign','mark','link','page',
        'smile','laugh','grin','wink','clap','cheer','shout','scream',
        'tiger','lion','bear','wolf','fox','deer','bird','fish',
        'snake','frog','duck','goat','horse','mouse','rabbit','monkey',
        'doctor','nurse','teacher','driver','farmer','soldier','guard',
        'police','judge','lawyer','actor','singer','dancer','player',
        'captain','leader','winner','loser','killer','hunter','fighter',
        'brother','sister','mother','father','uncle','aunt','cousin',
        'husband','wife','child','daughter','son','grandma','grandpa',
        'super','mega','ultra','hyper','turbo','maxi','mini',
        'banana','apple','orange','mango','lemon','cherry','grape',
        'pizza','burger','taco','pasta','salad','sandwich','cookie',
        'chicken','turkey','bacon','steak','sushi','noodle','soup',
        'coffee','juice','soda','vodka','whiskey','cocktail',
        'morning','night','today','tomorrow','yesterday','always','never',
        'here','there','where','when','what','which','that','this',
        'upset','worried','nervous','anxious','stressed','depressed','lonely',
        'confused','bored','tired','sick','hurt','shy','proud','jealous',
        'excited','annoyed','frustrated','grumpy','moody','cranky','miserable',
        'cheerful','joyful','grateful','thankful','hopeful','peaceful','calm',
        'gentle','kind','mean','rude','polite','humble','greedy','selfish',
        'honest','loyal','guilty','innocent','sorry','ashamed','embarrassed',
        'painful','beautiful','wonderful','horrible','terrible','amazing',
        'awesome','awful','lovely','pretty','handsome','gorgeous','cute',
        'perfect','special','normal','strange','weird','odd','bizarre',
        'boring','interesting','important','dangerous','careful','careless',
        'useless','useful','helpless','hopeless','worthless','pointless',
        'harmless','fearless','endless','restless','homeless','clueless',
        'powerful','colorful','graceful','hateful','spiteful','wasteful',
        'pleasant','unpleasant','foolish','childish','selfish','clownish',
        'friendly','unfriendly','cowardly','deadly','likely','unlikely',
        'famous','customs','custom','nervous','jealous','curious','furious','serious',
        'obvious','precious','gorgeous','dangerous','enormous','ridiculous',
        'fantastic','dramatic','pathetic','romantic','sarcastic','toxic',
        'pimple','pimples','scar','scars','wound','wounds','bruise','rash',
        'blister','wart','mole','freckle','wrinkle','acne','eczema',
        'tumor','cancer','virus','germ','disease','illness','fever',
        'cough','sneeze','vomit','diarrhea','infection','swelling',
        'itch','itchy','scratch','scab','sore','bleed','bleeding',
        'blind','deaf','mute','lame','cripple','disabled',
        'sweat','sweaty','smear','stain','spill','drool','spit',
        'carpet','curtain','pillow','blanket','towel','mirror','toilet',
        'shower','kitchen','garden','garage','office','school','church',
        'bottle','basket','bucket','pencil','eraser','paper','folder',
        'computer','laptop','tablet','screen','mouse','keyboard',
        'number','letter','email','message','picture','photo','video',
        'music','movie','series','channel','stream','upload','download',
        'weather','summer','winter','spring','autumn','season','holiday',
        'dinner','lunch','breakfast','snack','dessert','recipe','menu',
        'soccer','football','tennis','boxing','cricket','hockey','golf',
        'dollar','pound','euro','bitcoin','profit','salary','income'
    ];
    for (var j = 0; j < nonsense.length; j++) {
        if (cleaned === nonsense[j]) return 'Does not appear to be a valid name.';
    }

    var profanity = ['fuck','shit','asshole','bitch','bastard','damn','dick','cock','cunt','piss',
        'bollocks','wanker','twat','slut','whore','nigger','nigga','faggot','retard','crap',
        'pussy','vagina','penis','anus','booty','dildo','orgasm','horny','sexy','nude',
        'naked','porn','hentai','tits','titty','titties','scrotum','testicle','erection','ejaculate',
        'kontol','memek','jancok','jancuk','anjing','bangsat','babi','goblok','tolol','bodoh',
        'kampret','bajingan','keparat','setan','iblis','ngentot',
        'puta','mierda','cabron','pendejo','chingada','verga','joder',
        'merde','putain','connard','salaud',
        'scheisse','arschloch','wichser','fotze','hurensohn',
        'porra','caralho','foda','buceta',
        'cazzo','stronzo','vaffanculo','minchia','puttana'];
    for (var k = 0; k < profanity.length; k++) {
        if (cleaned.indexOf(profanity[k]) !== -1) return 'Contains inappropriate language.';
    }

    var words = lower.split(/\s+/);
    if (words.length >= 2) {
        var unique = words.filter(function(item, pos) {
            return words.indexOf(item) === pos;
        });
        if (unique.length === 1 && words[0].length <= 4) return 'Does not appear to be a valid name.';
    }

    if (cleaned.length >= 4) {
        var vowelCount = (cleaned.match(/[aeiou]/gi) || []).length;
        var consonantCount = cleaned.length - vowelCount;
        if (vowelCount === 0) return 'Does not appear to be a valid name.';
        if (consonantCount === 0) return 'Does not appear to be a valid name.';
    }

    if (cleaned.length <= 3) {
        var legitShort = [
            'al','an','bo','ed','em','ev','io','jo','ki','li','lu',
            'mo','mu','nu','oz','po','qi','ri','ru','ty','vi','wu',
            'xi','xu','yu','ze',
            'ada','adi','afi','aja','aki','alf','ali','ami','ana','ane',
            'ann','ari','asa','ava','ayu','bea','ben','bob','bud','cal',
            'cam','che','cho','col','dan','deb','dee','del','den','dev',
            'dex','dom','don','dot','eda','eka','eli','ema','emi','eri',
            'eva','eve','fay','fia','fin','flo','gab','gal','gay','gem',
            'gia','gil','gus','guy','hal','han','ida','ike','ina','ira',
            'isa','iva','ivy','jae','jan','jay','jen','jet','jia','jim',
            'joe','jon','joy','jun','kai','kam','kat','kay','ken','kim',
            'kit','koa','kye','lam','lan','lea','lee','len','leo','les',
            'lex','lia','lin','liu','liv','liz','lou','luc','luz','lyn',
            'mae','mai','mak','max','may','mel','mia','moe','mor','mya',
            'nan','nat','ned','nia','nik','noa','noe','ora','ori','oto',
            'ova','own','pam','pat','pax','peg','pen','pia','pip','pop',
            'rae','raj','ram','ran','ray','ren','rex','ria','rio','rob',
            'rod','ron','ros','roy','rui','sam','san','sel','sid','sim',
            'sol','sri','sue','sun','tam','ted','tim','tom','val','van',
            'vin','viv','wai','wan','wei','wen','wil','win','yam','yan',
            'yui','yun','zen','zoe','zul',
            'adi','aji','ayu','bui','cha','dea','eka','eko','eri','evi',
            'ika','ima','ina','ira','ita','lia','lim','mas','nur','oka','oni',
            'pur','ria','rio','ris','riy','saf','sri','sui','tin','tri',
            'udi','umi','uni','uta','uun','wah','wan','yul','yun',
            'adi','agus','ary','ayu','bas','bey','cak','dar','das','dew',
            'dik','din','dwi','edi','edy','ega','eka','eko','ely','eni',
            'eny','eri','evi','evy','eza','fit','gus','har','her','ida',
            'iis','iin','ika','ima','ina','ira','isa','ita','jan','jas',
            'lia','lim','lis','mam','man','mar','mas','may','meg','min',
            'muh','mus','naf','nan','nar','net','nik','nim','nin','nit',
            'nov','nug','nuh','nur','nut','oka','oni','pur','put','rah',
            'rat','ren','ret','ria','ril','rin','rio','ris','rit','riy',
            'ros','roz','rum','rus','rut','saf','sal','sar','sat','set',
            'sir','sit','sri','sub','sug','sui','sum','sun','sup','sur',
            'sus','sut','teg','tia','tin','tir','tit','tri','tut','udi',
            'uli','umi','uni','uta','uti','uun','vir','wah','wan','war',
            'wid','wij','win','wit','yam','yan','yat','yud','yul','yun',
            'yus','yut','zai','zak','zul'
        ];
        if (legitShort.indexOf(cleaned) === -1) return 'Does not appear to be a valid name.';
    }

    return null;
}

function showFilledState(firstName, lastName) {
    var fullName = firstName + ' ' + lastName;
    document.getElementById('emptyState').style.display = 'none';
    document.getElementById('filledState').style.display = 'flex';
    
    // Reset active tab to employee-details
    var detailsTabBtn = document.getElementById('stepDetailsBtn');
    if (detailsTabBtn) {
        switchAddTab(detailsTabBtn, 'employee-details');
    }
    
    document.getElementById('empListName').textContent = fullName;
    document.getElementById('empDetailName').textContent = fullName;
    document.getElementById('empFirstName').value = firstName;
    document.getElementById('empLastName').value = lastName;
    var empTab = document.getElementById('stepEmployment');
    var sumTab = document.getElementById('stepSummary');
    if (empTab) { empTab.classList.remove('locked'); }
    if (sumTab) { sumTab.classList.remove('locked'); }
}

var DETAIL_FIELD_IDS = [
    'empTitle', 'empFirstName', 'empMiddleName', 'empLastName',
    'empGender', 'empDob', 'empEmail', 'empMobile', 'empWorkPhone', 'empJobTitle', 'empStartDate',
    'empAddr1', 'empAddr2', 'empAddr3', 'empCity', 'empTerritory', 'empPostcode',
    'empBankAccName', 'empBankName', 'empBankBranch', 'empBankAccNo', 'empBankBsb',
    'empSalary', 'empRate', 'empPayFreq', 'empEffectiveFrom', 'empReason', 'empPayrollNo',
    'empTfn', 'empPassportNo', 'empPassportCountry', 'empPassportExpiry',
    'empLicenceNo', 'empLicenceCountry', 'empLicenceClass', 'empLicenceExpiry',
    'empVisaNo', 'empVisaExpiry',
    'empJurisdiction', 'empEmployeeType', 'empPlaceOfWork', 'empWorkCountry', 'empLeaveUnit', 'empWorkingPattern', 'empAccrualRate',
    'empContractedHours', 'empContractedMinutes', 'empContractedDays', 'empAverageHours', 'empAverageMinutes', 'empAnnualLeaveHours', 'empAnnualLeaveMinutes'
];

function onJurisdictionChange() {
    var el = document.getElementById('empJurisdiction');
    var container = document.getElementById('placeOfWorkContainer');
    if (!container) return;
    container.style.display = (el && el.value) ? '' : 'none';
    if (el && el.value) renderPowList();
}

// ═══ Place of Work ═══
var PLACES_OF_WORK = JSON.parse(localStorage.getItem('pow_locations') || '[]');

function savePowLocations() {
    localStorage.setItem('pow_locations', JSON.stringify(PLACES_OF_WORK));
}

function openPowModal() {
    ['powName','powAddr1','powAddr2','powCity','powPostcode'].forEach(function(id) {
        var el = document.getElementById(id); if (el) el.value = '';
    });
    var cs = document.getElementById('powCountry'); if (cs) cs.selectedIndex = 0;
    var ts = document.getElementById('powTerritory'); if (ts) ts.selectedIndex = 0;
    var jur = document.getElementById('empJurisdiction');
    var title = document.getElementById('powModalTitle');
    if (title) title.textContent = 'Create new place of work' + (jur && jur.value ? ' for ' + jur.value : '');
    updatePowSaveBtn();
    var modal = document.getElementById('powModal');
    modal.style.display = 'flex';
}

function closePowModal() {
    document.getElementById('powModal').style.display = 'none';
}

function updatePowSaveBtn() {
    var btn = document.getElementById('powSaveBtn');
    if (!btn) return;
    var name = (document.getElementById('powName').value || '').trim();
    var country = (document.getElementById('powCountry').value || '').trim();
    var territory = (document.getElementById('powTerritory').value || '').trim();
    var valid = name.length > 0 && country.length > 0 && territory.length > 0;
    btn.style.opacity = valid ? '1' : '0.5';
    btn.style.cursor = valid ? 'pointer' : 'not-allowed';
}

function savePowModal() {
    var name = (document.getElementById('powName').value || '').trim();
    var country = (document.getElementById('powCountry').value || '').trim();
    var territory = (document.getElementById('powTerritory').value || '').trim();
    if (!name || !country || !territory) return;

    var place = {
        name: name,
        addr1: (document.getElementById('powAddr1').value || '').trim(),
        addr2: (document.getElementById('powAddr2').value || '').trim(),
        city: (document.getElementById('powCity').value || '').trim(),
        postcode: (document.getElementById('powPostcode').value || '').trim(),
        country: country,
        territory: territory
    };
    PLACES_OF_WORK.push(place);
    savePowLocations();
    closePowModal();
    selectPow(name, country);
    renderPowList();
}

function togglePowDropdown() {
    var panel = document.getElementById('powDropdownPanel');
    if (panel.style.display === 'none') {
        panel.style.display = 'block';
        renderPowList();
        var si = document.getElementById('powSearchInput');
        if (si) { si.value = ''; si.focus(); }
    } else {
        panel.style.display = 'none';
    }
}

function renderPowList(filter) {
    var container = document.getElementById('powListItems');
    if (!container) return;
    var selectedVal = (document.getElementById('empPlaceOfWork').value || '').trim();
    var html = '';
    var q = (filter || '').toLowerCase();

    // "Not set" option
    if (!q || 'not set'.indexOf(q) !== -1) {
        var isSelected = !selectedVal;
        html += '<div onclick="selectPow(\'\')" style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;cursor:pointer;font-size:13px;color:#374151;' + (isSelected ? 'background:#f0fdf4;' : '') + '" onmouseover="this.style.background=\'#f9fafb\'" onmouseout="this.style.background=\'' + (isSelected ? '#f0fdf4' : '#fff') + '\'">';
        html += '<span>Not set</span>';
        if (isSelected) html += '<svg style="width:16px;height:16px;color:#2e7d5e;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
        html += '</div>';
    }

    PLACES_OF_WORK.forEach(function(p) {
        var label = p.name + (p.city ? ', ' + p.city : '');
        if (q && label.toLowerCase().indexOf(q) === -1) return;
        var isSelected = selectedVal === p.name;
        var safeCountry = (p.country || '').replace(/'/g, "\\'");
        html += '<div onclick="selectPow(\'' + escapeHtml(p.name).replace(/'/g, "\\'") + '\', \'' + safeCountry + '\')" style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;cursor:pointer;font-size:13px;color:#374151;' + (isSelected ? 'background:#f0fdf4;' : '') + '" onmouseover="this.style.background=\'#f9fafb\'" onmouseout="this.style.background=\'' + (isSelected ? '#f0fdf4' : '#fff') + '\'">';
        html += '<span>' + escapeHtml(label) + '</span>';
        if (isSelected) html += '<svg style="width:16px;height:16px;color:#2e7d5e;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
        html += '</div>';
    });

    if (!html) html = '<div style="padding:10px 14px;font-size:13px;color:#9ca3af;">No locations found</div>';
    container.innerHTML = html;
}

function filterPowList() {
    var si = document.getElementById('powSearchInput');
    renderPowList(si ? si.value : '');
}

function selectPow(val, country) {
    document.getElementById('empPlaceOfWork').value = val;
    // Set work_country directly from the passed country parameter
    // Fallback: search PLACES_OF_WORK array if country not passed
    var workCountry = country || '';
    if (!workCountry && val) {
        var place = PLACES_OF_WORK.find(function(p) { return p.name === val; });
        workCountry = (place && place.country) ? place.country : '';
    }
    var countryEl = document.getElementById('empWorkCountry');
    if (countryEl) countryEl.value = workCountry;
    var label = document.getElementById('powSelectedLabel');
    if (label) {
        label.textContent = val || 'Not set';
        label.style.color = val ? '#1f2937' : '#6b7280';
    }
    document.getElementById('powDropdownPanel').style.display = 'none';
}

// Close POW dropdown when clicking outside
document.addEventListener('click', function(e) {
    var wrap = document.getElementById('powDropdownWrap');
    var panel = document.getElementById('powDropdownPanel');
    if (wrap && panel && !wrap.contains(e.target)) {
        panel.style.display = 'none';
    }
});

// ═══ Working Time Pattern (WTP) JavaScript logic ═══
var wtpDays = [];
var STANDARD_WTP_PATTERNS = {
    'Standard (Mon-Fri)': [
        { dayName: 'Mon', checked: true, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
        { dayName: 'Tue', checked: true, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
        { dayName: 'Wed', checked: true, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
        { dayName: 'Thu', checked: true, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
        { dayName: 'Fri', checked: true, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
        { dayName: 'Sat', checked: false, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
        { dayName: 'Sun', checked: false, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] }
    ],
    'Part Time (Mon-Wed)': [
        { dayName: 'Mon', checked: true, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
        { dayName: 'Tue', checked: true, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
        { dayName: 'Wed', checked: true, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
        { dayName: 'Thu', checked: false, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
        { dayName: 'Fri', checked: false, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
        { dayName: 'Sat', checked: false, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
        { dayName: 'Sun', checked: false, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] }
    ],
    'Shift Work': [
        { dayName: 'Mon', checked: true, intervals: [{ startTime: '08:00', endTime: '16:00', breakDuration: 0 }] },
        { dayName: 'Tue', checked: true, intervals: [{ startTime: '08:00', endTime: '16:00', breakDuration: 0 }] },
        { dayName: 'Wed', checked: true, intervals: [{ startTime: '08:00', endTime: '16:00', breakDuration: 0 }] },
        { dayName: 'Thu', checked: true, intervals: [{ startTime: '08:00', endTime: '16:00', breakDuration: 0 }] },
        { dayName: 'Fri', checked: true, intervals: [{ startTime: '08:00', endTime: '16:00', breakDuration: 0 }] },
        { dayName: 'Sat', checked: false, intervals: [{ startTime: '08:00', endTime: '16:00', breakDuration: 0 }] },
        { dayName: 'Sun', checked: false, intervals: [{ startTime: '08:00', endTime: '16:00', breakDuration: 0 }] }
    ],
    'Flexible Hours': [
        { dayName: 'Mon', checked: true, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
        { dayName: 'Tue', checked: true, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
        { dayName: 'Wed', checked: true, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
        { dayName: 'Thu', checked: true, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
        { dayName: 'Fri', checked: true, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
        { dayName: 'Sat', checked: false, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
        { dayName: 'Sun', checked: false, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] }
    ]
};

function openWtpModal() {
    document.getElementById('wtpName').value = '';
    document.getElementById('wtpMakeDefault').checked = false;
    document.getElementById('wtpStartDate').value = new Date().toISOString().split('T')[0];

    // Populate copy dropdown
    var cp = document.getElementById('wtpCopyPattern');
    if (cp) {
        cp.innerHTML = '<option value="">Add new pattern</option>';
        Object.keys(STANDARD_WTP_PATTERNS).forEach(function(key) {
            cp.innerHTML += '<option value="' + key + '">' + key + '</option>';
        });
        var custom = JSON.parse(localStorage.getItem('wtp_patterns') || '[]');
        custom.forEach(function(pat) {
            cp.innerHTML += '<option value="' + escapeHtml(pat.name) + '">' + escapeHtml(pat.name) + ' (Custom)</option>';
        });
    }

    // Default to Standard (Mon-Fri)
    wtpDays = JSON.parse(JSON.stringify(STANDARD_WTP_PATTERNS['Standard (Mon-Fri)']));
    renderWtpDays();
    updateWtpSaveBtn();
    document.getElementById('wtpModal').style.display = 'flex';
}

function closeWtpModal() {
    document.getElementById('wtpModal').style.display = 'none';
}

function copyExistingWtpPattern() {
    var cpVal = document.getElementById('wtpCopyPattern').value;
    if (!cpVal) return;

    if (STANDARD_WTP_PATTERNS[cpVal]) {
        wtpDays = JSON.parse(JSON.stringify(STANDARD_WTP_PATTERNS[cpVal]));
    } else {
        var custom = JSON.parse(localStorage.getItem('wtp_patterns') || '[]');
        var found = custom.find(function(p) { return p.name === cpVal; });
        if (found) {
            wtpDays = JSON.parse(JSON.stringify(found.days));
        }
    }
    renderWtpDays();
}

function renderWtpDays() {
    var container = document.getElementById('wtpDaysContainer');
    if (!container) return;

    var html = '';
    wtpDays.forEach(function(day, idx) {
        if (!day.checked) {
            // Unchecked (light blue-green box)
            html += '<div class="wtp-day-row-unchecked">';
            html += '  <input type="checkbox" onchange="toggleWtpDay(' + idx + ', this.checked)" id="wtp_chk_' + idx + '" style="cursor:pointer;width:16px;height:16px;">';
            html += '  <label for="wtp_chk_' + idx + '" style="font-weight:600;cursor:pointer;margin:0;display:flex;align-items:center;gap:8px;font-size:13px;width:100%;">';
            html += '    ' + day.dayName;
            html += '    <span style="font-weight:normal;color:#6b7280;font-size:12px;">Select to add as a working day</span>';
            html += '  </label>';
            if (day.isCustom) {
                html += '  <button type="button" onclick="removeWtpDay(' + idx + ')" style="width:32px;height:32px;border-radius:6px;border:1px solid #e5e7eb;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;margin-left:auto;" title="Delete">';
                html += '    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ef4444"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>';
                html += '  </button>';
            }
            html += '</div>';
        } else {
            // Checked (full input fields)
            // First interval row
            var first = day.intervals[0] || { startTime: '09:00', endTime: '17:00', breakDuration: 0 };
            html += '<div style="display:flex;flex-direction:column;gap:6px;border:1px solid #e5e7eb;border-radius:8px;padding:8px 12px;background:#fff;">';
            html += '  <div class="wtp-day-row-checked" style="border:none;padding:0;background:none;border-radius:0;">';
            html += '    <div style="display:flex;align-items:center;gap:8px;">';
            html += '      <input type="checkbox" checked onchange="toggleWtpDay(' + idx + ', this.checked)" id="wtp_chk_' + idx + '" style="cursor:pointer;width:16px;height:16px;">';
            html += '      <label for="wtp_chk_' + idx + '" style="font-weight:700;margin:0;cursor:pointer;font-size:13px;color:#1f2937;">' + day.dayName + '</label>';
            html += '    </div>';
            html += '    <div style="position:relative;display:flex;align-items:center;">';
            html += '      <input type="time" class="wtp-time-input" value="' + first.startTime + '" oninput="updateWtpInterval(' + idx + ', 0, \'startTime\', this.value)">';
            html += '    </div>';
            html += '    <div style="position:relative;display:flex;align-items:center;">';
            html += '      <input type="time" class="wtp-time-input" value="' + first.endTime + '" oninput="updateWtpInterval(' + idx + ', 0, \'endTime\', this.value)">';
            html += '    </div>';
            html += '    <div style="display:flex;align-items:center;gap:6px;">';
            html += '      <input type="number" class="wtp-time-input" style="width:70px;" value="' + first.breakDuration + '" min="0" oninput="updateWtpInterval(' + idx + ', 0, \'breakDuration\', this.value)">';
            html += '      <span style="font-size:12px;color:#6b7280;font-weight:500;">mins</span>';
            html += '    </div>';
            html += '    <div style="display:flex;align-items:center;gap:10px;">';
            html += '      <button type="button" onclick="addWtpSplit(' + idx + ')" class="wtp-split-btn">+ Split</button>';
            if (day.isCustom) {
                html += '      <button type="button" onclick="removeWtpDay(' + idx + ')" style="width:32px;height:32px;border-radius:6px;border:1px solid #e5e7eb;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;" title="Delete">';
                html += '        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ef4444"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>';
                html += '      </button>';
            }
            html += '    </div>';
            html += '  </div>';

            // Additional split interval rows
            for (var sIdx = 1; sIdx < day.intervals.length; sIdx++) {
                var split = day.intervals[sIdx];
                html += '  <div class="wtp-split-row">';
                html += '    <div></div>'; // Day label spacing
                html += '    <div>';
                html += '      <input type="time" class="wtp-time-input" value="' + split.startTime + '" oninput="updateWtpInterval(' + idx + ', ' + sIdx + ', \'startTime\', this.value)">';
                html += '    </div>';
                html += '    <div>';
                html += '      <input type="time" class="wtp-time-input" value="' + split.endTime + '" oninput="updateWtpInterval(' + idx + ', ' + sIdx + ', \'endTime\', this.value)">';
                html += '    </div>';
                html += '    <div style="display:flex;align-items:center;gap:6px;">';
                html += '      <input type="number" class="wtp-time-input" style="width:70px;" value="' + split.breakDuration + '" min="0" oninput="updateWtpInterval(' + idx + ', ' + sIdx + ', \'breakDuration\', this.value)">';
                html += '      <span style="font-size:12px;color:#6b7280;font-weight:500;">mins</span>';
                html += '    </div>';
                html += '    <div>';
                html += '      <button type="button" onclick="removeWtpSplit(' + idx + ', ' + sIdx + ')" style="background:none;border:none;color:#ef4444;cursor:pointer;font-size:12px;font-weight:600;padding:4px;display:flex;align-items:center;gap:3px;">✕ Remove</button>';
                html += '    </div>';
                html += '  </div>';
            }
            html += '</div>';
        }
    });

    container.innerHTML = html;
    calculateWtpHours();
}

function toggleWtpDay(idx, checked) {
    if (wtpDays[idx]) {
        wtpDays[idx].checked = checked;
        renderWtpDays();
    }
}

function updateWtpInterval(idx, sIdx, key, val) {
    if (wtpDays[idx] && wtpDays[idx].intervals[sIdx]) {
        wtpDays[idx].intervals[sIdx][key] = (key === 'breakDuration') ? parseInt(val || 0) : val;
        calculateWtpHours();
    }
}

function addWtpSplit(idx) {
    if (wtpDays[idx]) {
        wtpDays[idx].intervals.push({ startTime: '09:00', endTime: '17:00', breakDuration: 0 });
        renderWtpDays();
    }
}

function removeWtpSplit(idx, sIdx) {
    if (wtpDays[idx]) {
        wtpDays[idx].intervals.splice(sIdx, 1);
        renderWtpDays();
    }
}

function addWtp7Days() {
    var weekNum = Math.ceil(wtpDays.length / 7) + 1;
    var names = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    names.forEach(function(name) {
        wtpDays.push({
            dayName: name + ' (W' + weekNum + ')',
            checked: (name !== 'Sat' && name !== 'Sun'),
            intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }]
        });
    });
    renderWtpDays();
}

function removeWtp7Days() {
    if (wtpDays.length > 7) {
        wtpDays.splice(-7, 7);
        renderWtpDays();
    }
}

function addWtpDay() {
    wtpDays.push({
        dayName: 'Custom Day ' + (wtpDays.length + 1),
        checked: true,
        isCustom: true,
        intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }]
    });
    renderWtpDays();
}

function removeWtpDay(idx) {
    if (wtpDays[idx]) {
        wtpDays.splice(idx, 1);
        renderWtpDays();
    }
}

function calculateWtpHours() {
    var checkedCount = 0;
    var totalMins = 0;

    wtpDays.forEach(function(day) {
        if (day.checked) {
            checkedCount++;
            day.intervals.forEach(function(iv) {
                var start = iv.startTime || '09:00';
                var end = iv.endTime || '17:00';
                var brk = parseInt(iv.breakDuration || 0);

                var sParts = start.split(':').map(Number);
                var eParts = end.split(':').map(Number);

                if (sParts.length === 2 && eParts.length === 2) {
                    var sMin = sParts[0] * 60 + sParts[1];
                    var eMin = eParts[0] * 60 + eParts[1];
                    var diff = eMin - sMin;
                    if (diff < 0) diff += 1440; // Overnight
                    diff -= brk;
                    totalMins += Math.max(0, diff);
                }
            });
        }
    });

    var totalHours = totalMins / 60;
    var hrsText = (totalHours % 1 === 0) ? totalHours : totalHours.toFixed(1);

    var summaryLabel = document.getElementById('wtpSummaryLabel');
    if (summaryLabel) {
        summaryLabel.textContent = checkedCount + ' working days selected totalling ' + hrsText + ' hrs, excluding breaks';
    }

    var repeatsLabel = document.getElementById('wtpRepeatsLabel');
    if (repeatsLabel) {
        repeatsLabel.textContent = 'Pattern repeats every ' + wtpDays.length + ' days';
    }

    // Toggle remove 7 days button
    var btnRemove = document.getElementById('btnRemoveWtp7Days');
    if (btnRemove) {
        btnRemove.disabled = (wtpDays.length <= 7);
        btnRemove.style.opacity = (wtpDays.length <= 7) ? '0.5' : '1';
        btnRemove.style.cursor = (wtpDays.length <= 7) ? 'not-allowed' : 'pointer';
    }

    updateWtpSaveBtn();
}

function updateWtpSaveBtn() {
    var btn = document.getElementById('wtpSaveBtn');
    if (!btn) return;

    var name = (document.getElementById('wtpName').value || '').trim();
    var hasWorkingDays = wtpDays.some(function(d) { return d.checked; });
    var isValid = (name.length > 0) && hasWorkingDays;

    btn.disabled = !isValid;
    if (isValid) {
        btn.style.opacity = '1';
        btn.style.cursor = 'pointer';
    } else {
        btn.style.opacity = '0.5';
        btn.style.cursor = 'not-allowed';
    }
}

function saveWtpModal() {
    var name = (document.getElementById('wtpName').value || '').trim();
    var startDate = document.getElementById('wtpStartDate').value;
    var isDefault = document.getElementById('wtpMakeDefault').checked;

    if (!name) return;

    var newPattern = {
        name: name,
        startDate: startDate,
        isDefault: isDefault,
        days: wtpDays
    };

    var custom = JSON.parse(localStorage.getItem('wtp_patterns') || '[]');
    // Filter duplicate name
    custom = custom.filter(function(p) { return p.name !== name; });
    custom.push(newPattern);
    localStorage.setItem('wtp_patterns', JSON.stringify(custom));

    // If default is checked, update defaults for others
    if (isDefault) {
        custom.forEach(function(p) {
            if (p.name !== name) p.isDefault = false;
        });
        localStorage.setItem('wtp_patterns', JSON.stringify(custom));
    }

    initializeWtpDropdown(name);
    
    // Select custom pattern in UI
    var selectEl = document.getElementById('empWorkingPattern');
    if (selectEl) {
        selectEl.value = name;
        // Trigger manual change to save in employee object
        var evt = document.createEvent('HTMLEvents');
        evt.initEvent('change', true, true);
        selectEl.dispatchEvent(evt);
    }

    closeWtpModal();
}

/**
 * Returns a simplified schedule array for the given pattern name,
 * ready to be sent as working_schedule to the server.
 * Format: [{ day, active, start, end, break }, ...]
 */
function getWorkingScheduleForPattern(patternName) {
    if (!patternName) return null;

    // Try custom patterns from localStorage first
    var custom = [];
    try { custom = JSON.parse(localStorage.getItem('wtp_patterns') || '[]'); } catch(e) {}
    var found = custom.find(function(p) { return p.name === patternName; });

    // Fall back to standard presets
    if (!found && STANDARD_WTP_PATTERNS[patternName]) {
        found = { name: patternName, days: STANDARD_WTP_PATTERNS[patternName] };
    }

    if (!found || !found.days) return null;

    return found.days.map(function(d) {
        var interval = (d.intervals && d.intervals[0]) || {};
        return {
            day:    d.dayName,
            active: !!d.checked,
            start:  d.checked ? (interval.startTime || null) : null,
            end:    d.checked ? (interval.endTime   || null) : null,
            break:  d.checked ? (interval.breakDuration || 0) : 0
        };
    });
}


function initializeWtpDropdown(selectedVal) {
    var selectEl = document.getElementById('empWorkingPattern');
    if (!selectEl) return;

    // Initialize dummy patterns if localStorage is empty for wtp_patterns
    var customRaw = localStorage.getItem('wtp_patterns');
    if (!customRaw) {
        var dummyPatterns = [
            {
                name: 'Shift Pagi Kuta Office',
                startDate: new Date().toISOString().split('T')[0],
                isDefault: false,
                days: [
                    { dayName: 'Mon', checked: true, intervals: [{ startTime: '08:00', endTime: '16:00', breakDuration: 30 }] },
                    { dayName: 'Tue', checked: true, intervals: [{ startTime: '08:00', endTime: '16:00', breakDuration: 30 }] },
                    { dayName: 'Wed', checked: true, intervals: [{ startTime: '08:00', endTime: '16:00', breakDuration: 30 }] },
                    { dayName: 'Thu', checked: true, intervals: [{ startTime: '08:00', endTime: '16:00', breakDuration: 30 }] },
                    { dayName: 'Fri', checked: true, intervals: [{ startTime: '08:00', endTime: '16:00', breakDuration: 30 }] },
                    { dayName: 'Sat', checked: false, intervals: [{ startTime: '08:00', endTime: '16:00', breakDuration: 0 }] },
                    { dayName: 'Sun', checked: false, intervals: [{ startTime: '08:00', endTime: '16:00', breakDuration: 0 }] }
                ]
            },
            {
                name: 'Part-Time Wed-Fri',
                startDate: new Date().toISOString().split('T')[0],
                isDefault: false,
                days: [
                    { dayName: 'Mon', checked: false, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
                    { dayName: 'Tue', checked: false, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
                    { dayName: 'Wed', checked: true, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
                    { dayName: 'Thu', checked: true, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
                    { dayName: 'Fri', checked: true, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
                    { dayName: 'Sat', checked: false, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] },
                    { dayName: 'Sun', checked: false, intervals: [{ startTime: '09:00', endTime: '17:00', breakDuration: 0 }] }
                ]
            },
            {
                name: 'Overnight Security Shift',
                startDate: new Date().toISOString().split('T')[0],
                isDefault: false,
                days: [
                    { dayName: 'Mon', checked: true, intervals: [{ startTime: '22:00', endTime: '06:00', breakDuration: 60 }] },
                    { dayName: 'Tue', checked: true, intervals: [{ startTime: '22:00', endTime: '06:00', breakDuration: 60 }] },
                    { dayName: 'Wed', checked: true, intervals: [{ startTime: '22:00', endTime: '06:00', breakDuration: 60 }] },
                    { dayName: 'Thu', checked: true, intervals: [{ startTime: '22:00', endTime: '06:00', breakDuration: 60 }] },
                    { dayName: 'Fri', checked: true, intervals: [{ startTime: '22:00', endTime: '06:00', breakDuration: 60 }] },
                    { dayName: 'Sat', checked: false, intervals: [{ startTime: '22:00', endTime: '06:00', breakDuration: 0 }] },
                    { dayName: 'Sun', checked: false, intervals: [{ startTime: '22:00', endTime: '06:00', breakDuration: 0 }] }
                ]
            }
        ];
        localStorage.setItem('wtp_patterns', JSON.stringify(dummyPatterns));
        customRaw = JSON.stringify(dummyPatterns);
    }

    var currentSelected = selectedVal || selectEl.value;
    var html = '<option value="">Select a working pattern</option>';
    html += '<option value="Standard (Mon-Fri)">Standard (Mon-Fri)</option>';
    html += '<option value="Part Time (Mon-Wed)">Part Time (Mon-Wed)</option>';
    html += '<option value="Shift Work">Shift Work</option>';
    html += '<option value="Flexible Hours">Flexible Hours</option>';

    var custom = JSON.parse(customRaw);
    custom.forEach(function(pat) {
        html += '<option value="' + escapeHtml(pat.name) + '">' + escapeHtml(pat.name) + '</option>';
    });

    selectEl.innerHTML = html;
    if (currentSelected) {
        selectEl.value = currentSelected;
    } else {
        // If there's a default custom pattern, select it
        var def = custom.find(function(p) { return p.isDefault; });
        if (def) {
            selectEl.value = def.name;
        }
    }
}

// Emergency contact form field IDs
var EC_FIELD_IDS = ['ecFirstName','ecLastName','ecMobile','ecHomePhone','ecWorkPhone','ecAddr1','ecAddr2','ecAddr3','ecCity','ecTerritory','ecPostcode','ecCountry','ecRelationship'];
var ecEditIndex = -1; // -1 = adding new, >=0 = editing existing

function showEmergencyContactForm(editIdx) {
    ecEditIndex = (editIdx !== undefined && editIdx >= 0) ? editIdx : -1;
    var formContainer = document.getElementById('ecFormContainer');
    var addBtn = document.getElementById('ecAddBtn');
    var infoBanner = document.getElementById('ecInfoBanner');
    var formTitle = document.getElementById('ecFormTitle');

    // Clear form fields and reset validation borders
    EC_FIELD_IDS.forEach(function(fid) {
        var el = document.getElementById(fid);
        if (!el) return;
        el.style.borderColor = '#d1d5db';
        if (el.tagName === 'SELECT') el.selectedIndex = 0;
        else el.value = '';
    });

    // If editing, populate with existing data
    if (ecEditIndex >= 0 && selectedEmpId) {
        var emp = ADDED_EMPLOYEES.find(function(e) { return e.id === selectedEmpId; });
        if (emp && emp.emergency_contacts && emp.emergency_contacts[ecEditIndex]) {
            var ec = emp.emergency_contacts[ecEditIndex];
            EC_FIELD_IDS.forEach(function(fid) {
                var key = fid.replace('ec', '').replace(/([A-Z])/g, '_$1').toLowerCase().replace(/^_/, '');
                // Map field IDs to data keys
                var dataKey = fid === 'ecFirstName' ? 'first_name' :
                              fid === 'ecLastName' ? 'last_name' :
                              fid === 'ecMobile' ? 'mobile' :
                              fid === 'ecHomePhone' ? 'home_phone' :
                              fid === 'ecWorkPhone' ? 'work_phone' :
                              fid === 'ecAddr1' ? 'address_1' :
                              fid === 'ecAddr2' ? 'address_2' :
                              fid === 'ecAddr3' ? 'address_3' :
                              fid === 'ecCity' ? 'city' :
                              fid === 'ecTerritory' ? 'territory' :
                              fid === 'ecPostcode' ? 'postcode' :
                              fid === 'ecCountry' ? 'country' :
                              fid === 'ecRelationship' ? 'relationship' : '';
                var el = document.getElementById(fid);
                if (el && ec[dataKey]) el.value = ec[dataKey];
            });
            formTitle.textContent = 'Edit emergency contact';
        }
    } else {
        formTitle.textContent = 'Add emergency contact';
    }

    formContainer.style.display = 'block';
    addBtn.style.display = 'none';
    infoBanner.style.display = 'none';
    // Hide saved contacts list when editing
    var savedContacts = document.getElementById('ecSavedContacts');
    if (savedContacts) savedContacts.style.display = 'none';
    updateEcSaveBtnState();
    // Scroll to form
    formContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function updateEcSaveBtnState() {
    var btn = document.getElementById('ecSaveBtn');
    if (!btn) return;
    var firstName = (document.getElementById('ecFirstName').value || '').trim();
    var mobile = (document.getElementById('ecMobile').value || '').trim();
    var homePhone = (document.getElementById('ecHomePhone').value || '').trim();
    var workPhone = (document.getElementById('ecWorkPhone').value || '').trim();
    var isValid = firstName.length > 0 && (mobile.length > 0 || homePhone.length > 0 || workPhone.length > 0);
    if (isValid) {
        btn.style.background = '#2e7d5e';
        btn.style.cursor = 'pointer';
        btn.style.opacity = '1';
    } else {
        btn.style.background = '#2e7d5e';
        btn.style.cursor = 'not-allowed';
        btn.style.opacity = '0.5';
    }
}

function cancelEmergencyContact() {
    var formContainer = document.getElementById('ecFormContainer');
    var addBtn = document.getElementById('ecAddBtn');
    var infoBanner = document.getElementById('ecInfoBanner');
    formContainer.style.display = 'none';
    addBtn.style.display = '';
    // Show saved contacts list again
    var savedContacts = document.getElementById('ecSavedContacts');
    if (savedContacts) savedContacts.style.display = '';
    // Show info banner only if no contacts saved
    var emp = selectedEmpId ? ADDED_EMPLOYEES.find(function(e) { return e.id === selectedEmpId; }) : null;
    if (!emp || !emp.emergency_contacts || emp.emergency_contacts.length === 0) {
        infoBanner.style.display = 'flex';
    }
    ecEditIndex = -1;
}

function saveEmergencyContact() {
    // Validate: first name required
    var firstName = (document.getElementById('ecFirstName').value || '').trim();
    if (!firstName) {
        document.getElementById('ecFirstName').style.borderColor = '#ef4444';
        document.getElementById('ecFirstName').focus();
        return;
    }
    // Validate: at least one phone number
    var mobile = (document.getElementById('ecMobile').value || '').trim();
    var homePhone = (document.getElementById('ecHomePhone').value || '').trim();
    var workPhone = (document.getElementById('ecWorkPhone').value || '').trim();
    if (!mobile && !homePhone && !workPhone) {
        document.getElementById('ecMobile').style.borderColor = '#ef4444';
        document.getElementById('ecMobile').focus();
        return;
    }

    // Collect data
    var contactData = {
        first_name: firstName,
        last_name: (document.getElementById('ecLastName').value || '').trim(),
        mobile: mobile,
        home_phone: homePhone,
        work_phone: workPhone,
        address_1: (document.getElementById('ecAddr1').value || '').trim(),
        address_2: (document.getElementById('ecAddr2').value || '').trim(),
        address_3: (document.getElementById('ecAddr3').value || '').trim(),
        city: (document.getElementById('ecCity').value || '').trim(),
        territory: (document.getElementById('ecTerritory').value || '').trim(),
        postcode: (document.getElementById('ecPostcode').value || '').trim(),
        country: document.getElementById('ecCountry').value || '',
        relationship: document.getElementById('ecRelationship').value || ''
    };

    if (!selectedEmpId) return;
    var emp = ADDED_EMPLOYEES.find(function(e) { return e.id === selectedEmpId; });
    if (!emp) return;
    if (!emp.emergency_contacts) emp.emergency_contacts = [];

    if (ecEditIndex >= 0 && ecEditIndex < emp.emergency_contacts.length) {
        emp.emergency_contacts[ecEditIndex] = contactData;
    } else {
        emp.emergency_contacts.push(contactData);
    }

    ecEditIndex = -1;
    cancelEmergencyContact();
    renderEmergencyContacts();

    // Auto-save to server
    saveCurrentEmployeeDataToServer();
}

function deleteEmergencyContact(idx) {
    if (!selectedEmpId) return;
    var emp = ADDED_EMPLOYEES.find(function(e) { return e.id === selectedEmpId; });
    if (!emp || !emp.emergency_contacts) return;
    emp.emergency_contacts.splice(idx, 1);
    renderEmergencyContacts();
    saveCurrentEmployeeDataToServer();
}

function renderEmergencyContacts() {
    var container = document.getElementById('ecSavedContacts');
    var infoBanner = document.getElementById('ecInfoBanner');
    var addBtn = document.getElementById('ecAddBtn');
    if (!container) return;

    var emp = selectedEmpId ? ADDED_EMPLOYEES.find(function(e) { return e.id === selectedEmpId; }) : null;
    var contacts = (emp && emp.emergency_contacts) ? emp.emergency_contacts : [];

    if (contacts.length === 0) {
        container.innerHTML = '';
        if (infoBanner) infoBanner.style.display = 'flex';
        if (addBtn) addBtn.style.display = '';
        return;
    }

    if (infoBanner) infoBanner.style.display = 'none';
    if (addBtn) addBtn.style.display = 'none';
    var html = '';
    contacts.forEach(function(ec, i) {
        var name = (ec.first_name || '') + ' ' + (ec.last_name || '');
        var phone = ec.mobile || ec.home_phone || ec.work_phone || '';
        var rel = ec.relationship || '';
        html += '<div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border:1px solid #e5e7eb;border-radius:8px;margin-bottom:10px;background:#f9fafb;">';
        html += '  <div style="flex:1;min-width:0;">';
        html += '    <div style="font-size:14px;font-weight:600;color:#1f2937;">' + escapeHtml(name.trim()) + '</div>';
        html += '    <div style="font-size:12px;color:#6b7280;margin-top:2px;">' + escapeHtml(phone) + (rel ? ' &middot; ' + escapeHtml(rel) : '') + '</div>';
        html += '  </div>';
        html += '  <div style="display:flex;gap:6px;">';
        html += '    <button onclick="showEmergencyContactForm(' + i + ')" style="width:32px;height:32px;border-radius:6px;border:1px solid #e5e7eb;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;" title="Edit">';
        html += '      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#475569"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>';
        html += '    </button>';
        html += '    <button onclick="deleteEmergencyContact(' + i + ')" style="width:32px;height:32px;border-radius:6px;border:1px solid #e5e7eb;background:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;" title="Delete">';
        html += '      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#ef4444"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>';
        html += '    </button>';
        html += '  </div>';
        html += '</div>';
    });
    container.innerHTML = html;
}

function escapeHtml(str) {
    if (!str) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str));
    return div.innerHTML;
}

function makeAjaxRequest(url, method, data, onSuccess, onError) {
    var csrfTokenEl = document.querySelector('input[name="_token"]');
    var csrfToken = csrfTokenEl ? csrfTokenEl.value : '';

    var ajaxMethod = method;
    var ajaxUrl = url;

    // Use Laravel method spoofing for PUT and DELETE to prevent cPanel/ModSecurity blockages
    if (method === 'PUT' || method === 'DELETE') {
        ajaxMethod = 'POST';
        ajaxUrl = url + (url.indexOf('?') === -1 ? '?' : '&') + '_method=' + method;
    }

    fetch(ajaxUrl, {
        method: ajaxMethod,
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
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
            if (onError) onError(res.body, res.status);
        }
    })
    .catch(function(err) {
        console.error('AJAX error:', err);
        var errMsg = 'A network error occurred: ' + (err.message || err) + '. Please try again.';
        if (onError) onError({ message: errMsg }, 500);
    });
}

function saveEmployeeDataToServer(empId, onSuccess, onError, suppressAlert) {
    var emp = ADDED_EMPLOYEES.find(function(e) { return e.id === empId; });
    if (!emp) {
        if (onSuccess) onSuccess();
        return;
    }

    if (!emp.details) emp.details = {};
    DETAIL_FIELD_IDS.forEach(function(fid) {
        var el = document.getElementById(fid);
        if (el) emp.details[fid] = el.value;
    });

    var payload = {
        title: (emp.details['empTitle'] === 'Select Title' || !emp.details['empTitle']) ? null : emp.details['empTitle'],
        first_name: emp.details['empFirstName'] || emp.first_name,
        last_name: emp.details['empLastName'] || emp.last_name,
        gender: emp.details['empGender'] || 'Unspecified',
        dob: (emp.details['empDob'] && emp.details['empDob'].trim()) ? emp.details['empDob'].trim() : null,
        email: (emp.details['empEmail'] && emp.details['empEmail'].trim()) ? emp.details['empEmail'].trim() : null,
        mobile: (emp.details['empMobile'] && emp.details['empMobile'].trim()) ? emp.details['empMobile'].trim() : null,
        work_phone: (emp.details['empWorkPhone'] && emp.details['empWorkPhone'].trim()) ? emp.details['empWorkPhone'].trim() : null,
        job_title: (emp.details['empJobTitle'] && emp.details['empJobTitle'].trim()) ? emp.details['empJobTitle'].trim() : null,
        start_date: (emp.details['empStartDate'] && emp.details['empStartDate'].trim()) ? emp.details['empStartDate'].trim() : null,
        middle_name: (emp.details['empMiddleName'] && emp.details['empMiddleName'].trim()) ? emp.details['empMiddleName'].trim() : null,
        
        address_1: emp.details['empAddr1'] || null,
        address_2: emp.details['empAddr2'] || null,
        address_3: emp.details['empAddr3'] || null,
        city: emp.details['empCity'] || null,
        territory: emp.details['empTerritory'] || null,
        postcode: emp.details['empPostcode'] || null,
        
        bank_acc_name: emp.details['empBankAccName'] || null,
        bank_name: emp.details['empBankName'] || null,
        bank_branch: emp.details['empBankBranch'] || null,
        bank_acc_no: emp.details['empBankAccNo'] || null,
        bank_bsb: emp.details['empBankBsb'] || null,
        
        salary: emp.details['empSalary'] || '0',
        pay_rate: cleanSelectValue(emp.details['empRate']),
        pay_frequency: cleanSelectValue(emp.details['empPayFreq']),
        effective_from: (emp.details['empEffectiveFrom'] && emp.details['empEffectiveFrom'].trim()) ? emp.details['empEffectiveFrom'].trim() : null,
        salary_reason: cleanSelectValue(emp.details['empReason']),
        payroll_no: emp.details['empPayrollNo'] || null,
        
        tfn: emp.details['empTfn'] || null,
        passport_no: emp.details['empPassportNo'] || null,
        passport_country: cleanSelectValue(emp.details['empPassportCountry']),
        passport_expiry: (emp.details['empPassportExpiry'] && emp.details['empPassportExpiry'].trim()) ? emp.details['empPassportExpiry'].trim() : null,
        licence_no: emp.details['empLicenceNo'] || null,
        licence_country: cleanSelectValue(emp.details['empLicenceCountry']),
        licence_class: emp.details['empLicenceClass'] || null,
        licence_expiry: (emp.details['empLicenceExpiry'] && emp.details['empLicenceExpiry'].trim()) ? emp.details['empLicenceExpiry'].trim() : null,
        visa_no: emp.details['empVisaNo'] || null,
        visa_expiry: (emp.details['empVisaExpiry'] && emp.details['empVisaExpiry'].trim()) ? emp.details['empVisaExpiry'].trim() : null,
        
        jurisdiction: cleanSelectValue(emp.details['empJurisdiction']),
        employee_type: emp.details['empEmployeeType'] || null,
        place_of_work: emp.details['empPlaceOfWork'] || null,
        work_country: emp.details['empWorkCountry'] || null,
        leave_unit: emp.details['empLeaveUnit'] || null,
        working_pattern: emp.details['empWorkingPattern'] || null,
        working_schedule: getWorkingScheduleForPattern(emp.details['empWorkingPattern']),
        accrual_rate: emp.details['empAccrualRate'] || null,
        contracted_hours: emp.details['empContractedHours'] || null,
        contracted_minutes: emp.details['empContractedMinutes'] || null,
        contracted_days: emp.details['empContractedDays'] || null,
        average_hours: emp.details['empAverageHours'] || null,
        average_minutes: emp.details['empAverageMinutes'] || null,
        annual_leave_hours: emp.details['empAnnualLeaveHours'] || null,
        annual_leave_minutes: emp.details['empAnnualLeaveMinutes'] || null,

        emergency_contacts: emp.emergency_contacts || []
    };

    var url = updateUrlTemplate.replace('__ID__', empId);

    makeAjaxRequest(url, 'PUT', payload, function(data) {
        console.log('Employee ' + empId + ' saved successfully to DB.');
        if (data && data.employee) {
            emp.first_name = data.employee.first_name;
            emp.last_name  = data.employee.last_name;
            emp.db_id      = data.employee.id; // store DB id for profile links
            renderEmployeeList();
        }
        if (onSuccess) onSuccess();
    }, function(errData) {
        console.error('Error saving employee details to DB:', errData);
        if (!suppressAlert) {
            alert(errData.message || 'Error occurred while saving employee details.');
        }
        if (onError) onError(errData);
    });
}

function saveCurrentEmployeeDataToServer(onSuccess, onError, suppressAlert) {
    if (!selectedEmpId) {
        if (onSuccess) onSuccess();
        return;
    }
    saveEmployeeDataToServer(selectedEmpId, onSuccess, onError, suppressAlert);
}

function loadEmployeeToForm(empId) {
    if (selectedEmpId && selectedEmpId !== empId) {
        saveCurrentEmployeeDataToServer(function() {
            loadEmployeeToFormDirect(empId);
        }, function() {
            loadEmployeeToFormDirect(empId);
        });
        return;
    }
    loadEmployeeToFormDirect(empId);
}

function loadEmployeeToFormDirect(empId) {
    selectedEmpId = empId;
    openDetailsPanel();
    var emp = ADDED_EMPLOYEES.find(function(e) { return e.id === empId; });
    if (!emp) return;

    var fullName = emp.first_name + ' ' + emp.last_name;
    var elListName = document.getElementById('empListName');
    var elDetailName = document.getElementById('empDetailName');
    if (elListName) elListName.textContent = fullName;
    if (elDetailName) elDetailName.textContent = fullName;

    DETAIL_FIELD_IDS.forEach(function(fid) {
        var el = document.getElementById(fid);
        if (!el) return;
        if (emp.details && emp.details[fid] !== undefined) {
            el.value = emp.details[fid];
        } else if (fid === 'empFirstName') {
            el.value = emp.first_name;
        } else if (fid === 'empLastName') {
            el.value = emp.last_name;
        } else if (el.tagName === 'SELECT') {
            el.selectedIndex = 0;
        } else if (el.type === 'number') {
            el.value = '0';
        } else {
            el.value = '';
        }
    });

    // Update Place of work visibility based on jurisdiction value
    onJurisdictionChange();
    // Restore Place of Work selection label
    var powVal = (document.getElementById('empPlaceOfWork').value || '').trim();
    var powLabel = document.getElementById('powSelectedLabel');
    if (powLabel) {
        powLabel.textContent = powVal || 'Not set';
        powLabel.style.color = powVal ? '#1f2937' : '#6b7280';
    }

    // Highlight the correct employee type card visually
    var empTypeVal = document.getElementById('empEmployeeType').value || '';
    // First deselect all cards in the employee type grid only
    var empTypeGrid = document.querySelector('#section-employment-details .emp-type-grid');
    if (empTypeGrid) {
        empTypeGrid.querySelectorAll('.emp-type-card').forEach(function(card) {
            if (card.getAttribute('data-val') === empTypeVal) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        });
    }

    // Update sections visibility based on employee type and leave unit
    updateSectionVisibility();
    // Show/hide contract info banner
    var banner = document.getElementById('contractInfoBanner');
    var leaveUnitVal = document.getElementById('empLeaveUnit') ? document.getElementById('empLeaveUnit').value : '';
    if (banner && leaveUnitVal) {
        var bannerTitle = document.getElementById('contractBannerTitle');
        var bannerDesc = document.getElementById('contractBannerDesc');
        if (leaveUnitVal === 'Hours') {
            bannerTitle.textContent = 'Hours leave selected';
            bannerDesc.textContent = "You won't be able to change this after you complete the set up.";
        } else {
            bannerTitle.textContent = 'Days leave selected';
            bannerDesc.textContent = "Employees set up with leave taken in days, will not include annual leave balances. They will only see the number of days taken, and not what they have accrued.";
        }
        banner.style.display = 'flex';
    } else if (banner) {
        banner.style.display = 'none';
    }

    var mainPanel = document.querySelector('.ae-filled-main');
    if (mainPanel) mainPanel.scrollTop = 0;

    document.querySelectorAll('.ae-emp-item').forEach(function(item) {
        if (parseInt(item.getAttribute('data-id')) === empId) {
            item.style.border = '2px solid #c9a84c';
            item.style.background = '#fffdf5';
        } else {
            item.style.border = '1px solid #e5e7eb';
            item.style.background = '#fff';
        }
    });

    updateSaveContinueBtnState();
    updateSaveContinueBtn2State();

    // Reset and render emergency contacts for this employee
    cancelEmergencyContact();
    renderEmergencyContacts();
}

function renderEmployeeList() {
    var container = document.getElementById('employeeList');
    if (!container) return;
    
    var searchVal = document.getElementById('sbSearchInput').value.toLowerCase().trim();
    var filtered = ADDED_EMPLOYEES.filter(function(e) {
        var fullName = (e.first_name + ' ' + e.last_name).toLowerCase();
        return fullName.indexOf(searchVal) !== -1;
    });
    
    var filterVal = document.getElementById('sbFilter').value;
    if (filterVal === 'first_name') {
        filtered.sort(function(a, b) {
            return a.first_name.localeCompare(b.first_name, undefined, { sensitivity: 'base' });
        });
    } else if (filterVal === 'last_name') {
        filtered.sort(function(a, b) {
            return a.last_name.localeCompare(b.last_name, undefined, { sensitivity: 'base' });
        });
    } else {
        filtered.sort(function(a, b) {
            return a.id - b.id;
        });
    }
    
    var countEl = document.getElementById('recordCount');
    if (countEl) {
        countEl.textContent = filtered.length + (filtered.length === 1 ? ' record' : ' records');
    }
    
    container.innerHTML = '';
    
    if (filtered.length === 0) {
        container.innerHTML = '<div style="padding: 16px; text-align: center; color: #9ca3af; font-size: 13px;">No matching employees</div>';
        return;
    }
    
    filtered.forEach(function(emp) {
        var isSelected = (emp.id === selectedEmpId);
        
        var div = document.createElement('div');
        div.className = 'ae-emp-item';
        div.setAttribute('data-id', emp.id);
        
        if (isSelected) {
            div.style.border = '2px solid #c9a84c';
            div.style.background = '#fffdf5';
        } else {
            div.style.border = '1px solid #e5e7eb';
            div.style.background = '#fff';
        }
        
        div.addEventListener('click', function(e) {
            if (e.target.closest('.delete-btn')) return;
            loadEmployeeToForm(emp.id);
        });
        
        var nameSpan = document.createElement('span');
        nameSpan.className = 'emp-name';
        nameSpan.textContent = emp.first_name + ' ' + emp.last_name;
        
        var emailSpan = document.createElement('span');
        emailSpan.className = 'emp-email';
        emailSpan.textContent = (emp.details && emp.details['empEmail']) || '-';
        
        var jobSpan = document.createElement('span');
        jobSpan.className = 'emp-job-title';
        jobSpan.textContent = (emp.details && emp.details['empJobTitle']) || '-';
        
        var deleteBtn = document.createElement('button');
        deleteBtn.className = 'delete-btn';
        deleteBtn.style.background = 'none';
        deleteBtn.style.border = 'none';
        deleteBtn.style.cursor = 'pointer';
        deleteBtn.style.color = '#9ca3af';
        deleteBtn.style.padding = '2px';
        deleteBtn.style.display = 'flex';
        deleteBtn.style.alignItems = 'center';
        deleteBtn.style.justifyContent = 'center';
        deleteBtn.title = 'Delete';
        
        deleteBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            deleteEmployee(emp.id);
        });
        
        deleteBtn.innerHTML = '<svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';
        
        div.appendChild(nameSpan);
        div.appendChild(emailSpan);
        div.appendChild(jobSpan);
        div.appendChild(deleteBtn);
        container.appendChild(div);
    });
}

function addSidebarEmployee() {
    var firstVal = document.getElementById('sbFirstName').value.trim();
    var lastVal = document.getElementById('sbLastName').value.trim();
    if (!firstVal || !lastVal || validateName(firstVal) || validateName(lastVal)) {
        return;
    }
    
    saveCurrentEmployeeDataToServer(function() {
        var sbSave = document.getElementById('sbSaveBtn');
        sbSave.disabled = true;
        
        makeAjaxRequest('{{ route("admin.linkers-hub.store-employee", [], false) }}', 'POST', {
            first_name: firstVal,
            last_name: lastVal
        }, function(data) {
            sbSave.disabled = false;
            if (data.success && data.employee) {
                var newEmp = {
                    id: data.employee.id,
                    first_name: data.employee.first_name,
                    last_name: data.employee.last_name,
                    details: {}
                };
                
                ADDED_EMPLOYEES.push(newEmp);
                
                document.getElementById('sbFirstName').value = '';
                document.getElementById('sbLastName').value = '';
                document.getElementById('sbSaveBtn').disabled = true;
                document.getElementById('sbFirstName').style.borderColor = '#d1d5db';
                document.getElementById('sbLastName').style.borderColor = '#d1d5db';
                
                selectedEmpId = newEmp.id;
                
                renderEmployeeList();
                loadEmployeeToFormDirect(newEmp.id);
                
                var listEl = document.getElementById('employeeList');
                if (listEl) {
                    listEl.scrollTop = listEl.scrollHeight;
                }
            }
        }, function(errData) {
            sbSave.disabled = false;
            alert(errData.message || 'Error saving employee.');
        });
    }, function(err) {
        console.error('Sidebar navigation blocked: previous employee save failed');
    });
}

var pendingConfirmAction = null;

function showConfirmModal(title, message, confirmText, onConfirm) {
    document.getElementById('aeModalTitle').textContent = title;
    document.getElementById('aeModalMessage').textContent = message;
    document.getElementById('aeModalConfirmBtn').textContent = confirmText;
    pendingConfirmAction = onConfirm;
    document.getElementById('aeConfirmModal').classList.add('active');
}

function closeConfirmModal() {
    document.getElementById('aeConfirmModal').classList.remove('active');
    pendingConfirmAction = null;
}

function showAeWarning(message) {
    document.getElementById('aeWarningMessage').textContent = message;
    document.getElementById('aeWarningModal').classList.add('active');
}

function closeAeWarningModal() {
    document.getElementById('aeWarningModal').classList.remove('active');
}

function executeConfirmAction() {
    var action = pendingConfirmAction;
    closeConfirmModal();
    if (action) {
        action();
    }
}

function deleteEmployee(empId) {
    var emp = ADDED_EMPLOYEES.find(function(e) { return e.id === empId; });
    var empName = emp ? (emp.first_name + ' ' + emp.last_name) : 'this employee';
    showConfirmModal(
        'Delete record',
        'This action cannot be reversed. Are you sure you want to delete "' + empName + '"?',
        'Delete',
        function() {
            var url = destroyUrlTemplate.replace('__ID__', empId);
            makeAjaxRequest(url, 'DELETE', {}, function(data) {
                if (data.success) {
                    var idx = ADDED_EMPLOYEES.findIndex(function(e) { return e.id === empId; });
                    if (idx !== -1) {
                        ADDED_EMPLOYEES.splice(idx, 1);
                    }
                    
                    if (ADDED_EMPLOYEES.length === 0) {
                        startOverDirect();
                    } else {
                        if (selectedEmpId === empId) {
                            selectedEmpId = ADDED_EMPLOYEES[0].id;
                        }
                        renderEmployeeList();
                        loadEmployeeToFormDirect(selectedEmpId);
                    }
                }
            }, function(errData) {
                alert(errData.message || 'Error occurred while deleting employee.');
            });
        }
    );
}

function startOverDirect() {
    ADDED_EMPLOYEES = [];
    selectedEmpId = null;
    
    document.getElementById('filledState').style.display = 'none';
    document.getElementById('emptyState').style.display = 'block';
    
    var firstInput = document.getElementById('firstNameInput');
    var lastInput = document.getElementById('lastNameInput');
    if (firstInput) { firstInput.value = ''; firstInput.style.borderColor = '#d1d5db'; }
    if (lastInput) { lastInput.value = ''; lastInput.style.borderColor = '#d1d5db'; }
    
    var firstErr = document.getElementById('firstNameError');
    var lastErr = document.getElementById('lastNameError');
    if (firstErr) { firstErr.textContent = ''; firstErr.classList.add('hidden'); }
    if (lastErr) { lastErr.textContent = ''; lastErr.classList.add('hidden'); }
    
    var saveBtn = document.getElementById('saveBtn');
    if (saveBtn) saveBtn.disabled = true;
    
    var empTab = document.getElementById('stepEmployment');
    var sumTab = document.getElementById('stepSummary');
    if (empTab) { empTab.classList.add('locked'); }
    if (sumTab) { sumTab.classList.add('locked'); }
    
    // Show sidebar and header again
    var sidebar = document.querySelector('.ae-filled-sidebar');
    var header = document.getElementById('empDetailHeader');
    if (sidebar) sidebar.style.display = 'flex';
    if (header) header.style.display = 'flex';
    
    if (firstInput) firstInput.focus();
}

function startOver() {
    showConfirmModal(
        'Start over',
        'This action cannot be reversed. Are you sure you want to start over? All added employees will be removed.',
        'Confirm',
        function() {
            startOverDirect();
        }
    );
}

function validateSidebarForm() {
    var sbFirst = document.getElementById('sbFirstName');
    var sbLast = document.getElementById('sbLastName');
    var sbSave = document.getElementById('sbSaveBtn');
    
    var fv = sbFirst.value.trim();
    var lv = sbLast.value.trim();
    
    var fe = fv.length > 0 ? validateName(fv) : null;
    var le = lv.length > 0 ? validateName(lv) : null;
    
    if (fe) { sbFirst.style.borderColor = '#ef4444'; }
    else { sbFirst.style.borderColor = fv.length > 0 ? '#333' : '#d1d5db'; }
    
    if (le) { sbLast.style.borderColor = '#ef4444'; }
    else { sbLast.style.borderColor = lv.length > 0 ? '#333' : '#d1d5db'; }
    
    var bothFilled = fv.length > 0 && lv.length > 0;
    var noErrors = !fe && !le;
    sbSave.disabled = !(bothFilled && noErrors);
}

document.addEventListener('DOMContentLoaded', function() {
    var detailsTabBtn = document.getElementById('stepDetailsBtn');
    var empTab = document.getElementById('stepEmployment');
    var sumTab = document.getElementById('stepSummary');

    var firstInput = document.getElementById('firstNameInput');
    var lastInput = document.getElementById('lastNameInput');
    var saveBtn = document.getElementById('saveBtn');
    var form = document.getElementById('addEmployeeForm');
    var firstErr = document.getElementById('firstNameError');
    var lastErr = document.getElementById('lastNameError');

    function validateForm() {
        var fv = firstInput.value.trim();
        var lv = lastInput.value.trim();
        var fe = fv.length > 0 ? validateName(fv) : null;
        var le = lv.length > 0 ? validateName(lv) : null;

        if (fe) { firstErr.textContent = fe; firstErr.classList.remove('hidden'); firstInput.style.borderColor = '#ef4444'; }
        else { firstErr.textContent = ''; firstErr.classList.add('hidden'); firstInput.style.borderColor = fv.length > 0 ? '#333' : '#d1d5db'; }

        if (le) { lastErr.textContent = le; lastErr.classList.remove('hidden'); lastInput.style.borderColor = '#ef4444'; }
        else { lastErr.textContent = ''; lastErr.classList.add('hidden'); lastInput.style.borderColor = lv.length > 0 ? '#333' : '#d1d5db'; }

        var bothFilled = fv.length > 0 && lv.length > 0;
        var noErrors = !fe && !le;
        saveBtn.disabled = !(bothFilled && noErrors);
    }

    if (firstInput) firstInput.addEventListener('input', validateForm);
    if (lastInput) lastInput.addEventListener('input', validateForm);

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var fv = firstInput.value.trim();
            var lv = lastInput.value.trim();
            if (!fv || !lv || validateName(fv) || validateName(lv)) {
                validateForm();
                return false;
            }
            
            saveBtn.disabled = true;
            
            makeAjaxRequest('{{ route("admin.linkers-hub.store-employee", [], false) }}', 'POST', {
                first_name: fv,
                last_name: lv
            }, function(data) {
                saveBtn.disabled = false;
                if (data.success && data.employee) {
                    var firstEmp = {
                        id: data.employee.id,
                        first_name: data.employee.first_name,
                        last_name: data.employee.last_name,
                        details: {}
                    };
                    ADDED_EMPLOYEES.push(firstEmp);
                    selectedEmpId = firstEmp.id;
                    
                    document.getElementById('emptyState').style.display = 'none';
                    document.getElementById('filledState').style.display = 'flex';
                    
                    renderEmployeeList();
                    loadEmployeeToFormDirect(firstEmp.id);
                    
                    var empTab = document.getElementById('stepEmployment');
                    var sumTab = document.getElementById('stepSummary');
                    if (empTab) { empTab.classList.remove('locked'); }
                    if (sumTab) { sumTab.classList.remove('locked'); }
                }
            }, function(errData) {
                saveBtn.disabled = false;
                if (errData && errData.errors) {
                    if (errData.errors.first_name) {
                        firstErr.textContent = errData.errors.first_name;
                        firstErr.classList.remove('hidden');
                        firstInput.style.borderColor = '#ef4444';
                    }
                    if (errData.errors.last_name) {
                        lastErr.textContent = errData.errors.last_name;
                        lastErr.classList.remove('hidden');
                        lastInput.style.borderColor = '#ef4444';
                    }
                } else {
                    alert(errData.message || 'Error saving employee.');
                }
            });
        });
    }

    if (firstInput) {
        firstInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (lastInput) lastInput.focus();
            }
        });
    }
    if (lastInput) {
        lastInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (form) {
                    form.dispatchEvent(new Event('submit', { cancelable: true }));
                }
            }
        });
    }

    var sbFirst = document.getElementById('sbFirstName');
    var sbLast = document.getElementById('sbLastName');
    var sbSave = document.getElementById('sbSaveBtn');
    
    if (sbFirst) sbFirst.addEventListener('input', validateSidebarForm);
    if (sbLast) sbLast.addEventListener('input', validateSidebarForm);
    if (sbSave) {
        sbSave.addEventListener('click', function(e) {
            e.preventDefault();
            addSidebarEmployee();
        });
    }

    if (sbFirst) {
        sbFirst.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (sbLast) sbLast.focus();
            }
        });
    }
    if (sbLast) {
        sbLast.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addSidebarEmployee();
            }
        });
    }

    var searchInput = document.getElementById('sbSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', renderEmployeeList);
    }
    var clearSearch = document.getElementById('sbClearSearch');
    if (clearSearch) {
        clearSearch.addEventListener('click', function() {
            searchInput.value = '';
            renderEmployeeList();
            searchInput.focus();
        });
    }

    var filterSelect = document.getElementById('sbFilter');
    if (filterSelect) {
        filterSelect.addEventListener('change', renderEmployeeList);
    }

    var saveContinueBtn = document.getElementById('saveAndContinueBtn');
    if (saveContinueBtn) {
        saveContinueBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var firstNameEl = document.getElementById('empFirstName');
            var lastNameEl = document.getElementById('empLastName');
            var emailEl = document.getElementById('empEmail');
            var startDateEl = document.getElementById('empStartDate');
            var dobEl2 = document.getElementById('empDob');
            
            var fv = firstNameEl ? firstNameEl.value.trim() : '';
            var lv = lastNameEl ? lastNameEl.value.trim() : '';
            var ev = emailEl ? emailEl.value.trim() : '';
            var sv = startDateEl ? startDateEl.value.trim() : '';
            var dv = dobEl2 ? dobEl2.value.trim() : '';
            
            var missing = [];
            if (!fv) missing.push('First name');
            if (!lv) missing.push('Last name');
            if (!dv) missing.push('Date of birth');
            if (!ev) missing.push('Email address');
            if (!sv) missing.push('Employment start date');
            
            if (missing.length > 0) {
                showAeWarning('Please fill in the following required fields: ' + missing.join(', ') + '.');
                if (!fv && firstNameEl) firstNameEl.focus();
                else if (!lv && lastNameEl) lastNameEl.focus();
                else if (!dv && dobEl2) dobEl2.focus();
                else if (!ev && emailEl) emailEl.focus();
                else if (!sv && startDateEl) startDateEl.focus();
                return;
            }

            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(ev)) {
                showAeWarning('Please enter a valid email address structure.');
                if (emailEl) emailEl.focus();
                return;
            }

            saveCurrentEmployeeDataToServer(function() {
                var empTab = document.getElementById('stepEmployment');
                if (empTab) {
                    empTab.classList.remove('locked');
                    switchAddTab(empTab, 'employment-details');
                }
            });
        });
    }

    /* ── Duplicate DOB check ─────────────────────────────────────────── */
    var CHECK_DUPLICATE_URL = '{{ route("admin.linkers-hub.check-duplicate-employee") }}';
    var dobDuplicateBlocked = false;

    function showDobWarning(msg) {
        var el = document.getElementById('dobDuplicateWarning');
        if (!el) return;
        el.textContent = msg;
        el.style.display = msg ? 'block' : 'none';
        dobDuplicateBlocked = !!msg;
        updateSaveBtnState();
    }

    function updateSaveBtnState() {
        var btn = document.getElementById('saveAndContinueBtn');
        if (!btn) return;
        if (dobDuplicateBlocked) {
            btn.disabled = true;
            btn.style.opacity = '0.4';
            btn.style.cursor  = 'not-allowed';
        } else {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor  = 'pointer';
        }
    }

    function runDuplicateCheck() {
        var fn  = (document.getElementById('empFirstName') || {}).value || '';
        var ln  = (document.getElementById('empLastName')  || {}).value || '';
        var dob = (document.getElementById('empDob')       || {}).value || '';

        // Only check when all three have values
        if (!fn.trim() || !ln.trim() || !dob) {
            showDobWarning('');
            return;
        }

        var csrfEl = document.querySelector('input[name="_token"]');
        var csrf   = csrfEl ? csrfEl.value : '';

        fetch(CHECK_DUPLICATE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ first_name: fn.trim(), last_name: ln.trim(), dob: dob })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            showDobWarning(data.duplicate ? (data.message || 'Duplicate employee detected.') : '');
        })
        .catch(function() { showDobWarning(''); });
    }

    // Trigger check on DOB change and on name field blur (in case DOB was filled first)
    var dobEl = document.getElementById('empDob');
    if (dobEl) dobEl.addEventListener('change', runDuplicateCheck);

    var fnEl = document.getElementById('empFirstName');
    var lnEl = document.getElementById('empLastName');
    if (fnEl) fnEl.addEventListener('blur', runDuplicateCheck);
    if (lnEl) lnEl.addEventListener('blur', runDuplicateCheck);
    var saveContinueBtn2 = document.getElementById('saveAndContinueBtn2');
    if (saveContinueBtn2) {
        saveContinueBtn2.addEventListener('click', function(e) {
            e.preventDefault();
            var jVal = document.getElementById('empJurisdiction').value;
            var etVal = document.getElementById('empEmployeeType').value;
            
            if (!jVal || !etVal) {
                showAeWarning('Please fill in the required fields for location and employee type.');
                return;
            }
            
            saveCurrentEmployeeDataToServer(function() {
                var sumTab = document.getElementById('stepSummary');
                if (sumTab) {
                    sumTab.classList.remove('locked');
                    switchAddTab(sumTab, 'summary');
                }
            });
        });
    }

    var backToStep1Btn = document.getElementById('backToStep1Btn');
    if (backToStep1Btn) {
        backToStep1Btn.addEventListener('click', function(e) {
            e.preventDefault();
            var detailsTabBtn = document.getElementById('stepDetailsBtn');
            if (detailsTabBtn) {
                switchAddTab(detailsTabBtn, 'employee-details');
            }
        });
    }

    var backToStep2Btn = document.getElementById('backToStep2Btn');
    if (backToStep2Btn) {
        backToStep2Btn.addEventListener('click', function(e) {
            e.preventDefault();
            var empTab = document.getElementById('stepEmployment');
            if (empTab) {
                switchAddTab(empTab, 'employment-details');
            }
        });
    }

    var finishBtn = document.getElementById('finishBtn');
    if (finishBtn) {
        finishBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Show loading state
            var originalText = finishBtn.textContent;
            finishBtn.textContent = 'Saving...';
            finishBtn.disabled = true;
            finishBtn.style.opacity = '0.7';
            finishBtn.style.cursor = 'not-allowed';
            
            saveCurrentEmployeeDataToServer(function() {
                // Success path
                finishBtn.textContent = originalText;
                finishBtn.disabled = false;
                finishBtn.style.opacity = '';
                finishBtn.style.cursor = '';
                
                // Track successfully submitted employee ID
                if (SUBMITTED_SUCCESS_IDS.indexOf(selectedEmpId) === -1) {
                    SUBMITTED_SUCCESS_IDS.push(selectedEmpId);
                }

                var emp = ADDED_EMPLOYEES.find(function(e) { return e.id === selectedEmpId; });
                if (emp) {
                    var addedRow = generateSubmissionStatusRow(emp);
                    document.getElementById('submissionAddedRows').innerHTML = addedRow;
                    document.getElementById('submissionFailedRows').innerHTML = '<tr><td colspan="3" style="padding: 16px; text-align: center; color: #9ca3af;">No failed submissions</td></tr>';
                    
                    document.getElementById('addedBadge').textContent = '1';
                    document.getElementById('failedBadge').textContent = '0';
                }
                
                // Hide overview, show status container
                document.getElementById('summary-overview-container').style.display = 'none';
                document.getElementById('summary-status-container').style.display = 'block';
                
                // Switch active tab to 'added'
                switchSubmissionTab('added');
                
                // Show/hide footer buttons
                document.querySelectorAll('.step3-btn').forEach(function(b) { b.style.display = 'none'; });
                document.querySelectorAll('.step-sub-btn').forEach(function(b) { b.style.display = 'block'; });
                
            }, function(errData) {
                // Failure path
                finishBtn.textContent = originalText;
                finishBtn.disabled = false;
                finishBtn.style.opacity = '';
                finishBtn.style.cursor = '';
                
                var errorMsg = (errData && errData.message) ? errData.message : 'Database save failed';
                
                var emp = ADDED_EMPLOYEES.find(function(e) { return e.id === selectedEmpId; });
                if (emp) {
                    var failedRow = generateFailedStatusRow(emp, errorMsg);
                    document.getElementById('submissionFailedRows').innerHTML = failedRow;
                    document.getElementById('submissionAddedRows').innerHTML = '<tr><td colspan="9" style="padding: 16px; text-align: center; color: #9ca3af;">No successful submissions</td></tr>';
                    
                    document.getElementById('addedBadge').textContent = '0';
                    document.getElementById('failedBadge').textContent = '1';
                }
                
                // Hide overview, show status container
                document.getElementById('summary-overview-container').style.display = 'none';
                document.getElementById('summary-status-container').style.display = 'block';
                
                // Switch active tab to 'failed'
                switchSubmissionTab('failed');
                
                // Show/hide footer buttons
                document.querySelectorAll('.step3-btn').forEach(function(b) { b.style.display = 'none'; });
                document.querySelectorAll('.step-sub-btn').forEach(function(b) { b.style.display = 'block'; });
            }, true); // suppressAlert = true
        });
    }

    // Submission Status tab helper functions
    window.switchSubmissionTab = function(tab) {
        var tabAddedBtn = document.getElementById('tabAddedBtn');
        var tabFailedBtn = document.getElementById('tabFailedBtn');
        var subContentAdded = document.getElementById('subContentAdded');
        var subContentFailed = document.getElementById('subContentFailed');
        
        if (tab === 'added') {
            tabAddedBtn.style.borderBottomColor = '#2e7d5e';
            tabAddedBtn.style.color = '#2e7d5e';
            tabFailedBtn.style.borderBottomColor = 'transparent';
            tabFailedBtn.style.color = '#6b7280';
            subContentAdded.style.display = 'block';
            subContentFailed.style.display = 'none';
        } else {
            tabAddedBtn.style.borderBottomColor = 'transparent';
            tabAddedBtn.style.color = '#6b7280';
            tabFailedBtn.style.borderBottomColor = '#ef4444';
            tabFailedBtn.style.color = '#ef4444';
            subContentAdded.style.display = 'none';
            subContentFailed.style.display = 'block';
        }
    };

    window.showStaffLinkNotification = function(message, type) {
        type = type || 'success';
        var container = document.getElementById('stafflink-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'stafflink-toast-container';
            container.style.position = 'fixed';
            container.style.top = '24px';
            container.style.right = '24px';
            container.style.zIndex = '999999';
            container.style.display = 'flex';
            container.style.flexDirection = 'column';
            container.style.gap = '12px';
            document.body.appendChild(container);
        }
        
        var toast = document.createElement('div');
        toast.style.background = type === 'success' ? '#2e7d5e' : (type === 'error' ? '#ef4444' : '#3b82f6');
        toast.style.color = '#fff';
        toast.style.padding = '14px 20px';
        toast.style.borderRadius = '8px';
        toast.style.fontSize = '14px';
        toast.style.fontWeight = '600';
        toast.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)';
        toast.style.display = 'flex';
        toast.style.alignItems = 'center';
        toast.style.gap = '10px';
        toast.style.minWidth = '280px';
        toast.style.transform = 'translateX(120%)';
        toast.style.transition = 'all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        
        var icon = document.createElement('span');
        icon.style.display = 'flex';
        icon.style.alignItems = 'center';
        icon.innerHTML = type === 'success' 
            ? '<svg style="width:20px;height:20px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            : '<svg style="width:20px;height:20px;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        toast.appendChild(icon);
        
        var text = document.createElement('span');
        text.textContent = message;
        toast.appendChild(text);
        
        container.appendChild(toast);
        
        setTimeout(function() {
            toast.style.transform = 'translateX(0)';
        }, 10);
        
        setTimeout(function() {
            toast.style.transform = 'translateX(120%)';
            toast.style.opacity = '0';
            setTimeout(function() {
                toast.remove();
            }, 300);
        }, 4000);
    };

    window.finishSubmissionRedirect = function() {
        if (SUBMITTED_SUCCESS_IDS.length > 0) {
            openRegEmailModal();
        } else {
            window.location.href = '{{ route("admin.linkers-hub.index") }}';
        }
    };

    window.openRegEmailModal = function() {
        var modal = document.getElementById('regEmailModal');
        if (modal) {
            modal.style.display = 'flex';
            
            // Reset to Step 1
            document.getElementById('regEmailStep1').style.display = 'block';
            document.getElementById('regEmailStep2').style.display = 'none';

            var title = document.getElementById('regEmailModalTitle');
            if (title) {
                var count = SUBMITTED_SUCCESS_IDS.length;
                title.textContent = 'Success! You have ' + count + ' new employee' + (count > 1 ? 's' : '') + ' in StaffLink';
            }
            var search = document.getElementById('regEmailSearch');
            if (search) search.value = '';
            
            var toggleBtn = document.getElementById('regEmailToggleAllBtn');
            if (toggleBtn) toggleBtn.textContent = 'Deselect all';
            allChecked = true;
            
            renderRegEmailList();
        }
    };

    window.closeRegEmailModal = function() {
        var modal = document.getElementById('regEmailModal');
        if (modal) modal.style.display = 'none';
        window.location.href = '{{ route("admin.linkers-hub.index") }}';
    };

    window.skipRegEmails = function() {
        // Transition directly to Step 2
        document.getElementById('regEmailStep1').style.display = 'none';
        document.getElementById('regEmailStep2').style.display = 'block';

        // Set header message for skipped/success
        var step2Title = document.getElementById('regEmailStep2Title');
        if (step2Title) {
            var count = SUBMITTED_SUCCESS_IDS.length;
            step2Title.textContent = 'Success! You have ' + count + ' new employee' + (count > 1 ? 's' : '') + ' in StaffLink';
        }

        // Wire up "Add documents" button to the first successfully added employee's profile#documents
        var link = document.getElementById('addDocumentsLink');
        if (link && SUBMITTED_SUCCESS_IDS.length > 0) {
            // Use the DB employee ID from ADDED_EMPLOYEES (matched by local id)
            var localId = SUBMITTED_SUCCESS_IDS[0];
            var emp = ADDED_EMPLOYEES.find(function(e) { return e.id === localId; });
            if (emp && emp.db_id) {
                link.href = profileUrlTemplate.replace('__ID__', emp.db_id) + '#documents';
            }
        }
    };

    // Kembali dari Step 2 (pilihan aksi) ke Step 1 (pengiriman email registrasi)
    window.goBackToStep1 = function() {
        document.getElementById('regEmailStep2').style.display = 'none';
        document.getElementById('regEmailStep1').style.display = 'block';

        // Pulihkan judul Step 1 sesuai jumlah employee baru
        var title = document.getElementById('regEmailModalTitle');
        if (title) {
            var count = SUBMITTED_SUCCESS_IDS.length;
            title.textContent = 'Success! You have ' + count + ' new employee' + (count > 1 ? 's' : '') + ' in StaffLink';
        }
    };

    window.filterRegEmailList = function() {
        renderRegEmailList();
    };

    window.clearRegEmailSearch = function() {
        var search = document.getElementById('regEmailSearch');
        if (search) search.value = '';
        renderRegEmailList();
    };

    var allChecked = true;
    window.toggleAllRegEmails = function() {
        var btn = document.getElementById('regEmailToggleAllBtn');
        var checkboxes = document.querySelectorAll('.reg-email-checkbox');
        if (allChecked) {
            checkboxes.forEach(function(cb) { cb.checked = false; });
            btn.textContent = 'Select all';
            allChecked = false;
        } else {
            checkboxes.forEach(function(cb) { cb.checked = true; });
            btn.textContent = 'Deselect all';
            allChecked = true;
        }
    };

    window.sendRegEmails = function() {
        var checkedEmpIds = [];
        document.querySelectorAll('.reg-email-checkbox:checked').forEach(function(cb) {
            checkedEmpIds.push(parseInt(cb.getAttribute('data-id')));
        });
        
        if (checkedEmpIds.length === 0) {
            showStaffLinkNotification('No employees selected.', 'error');
            return;
        }

        // Show loading state on the Send & continue button
        var sendBtn = document.querySelector('#regEmailModal button[onclick="sendRegEmails()"]');
        var originalText = sendBtn ? sendBtn.textContent : 'Send & continue';
        if (sendBtn) {
            sendBtn.textContent = 'Sending...';
            sendBtn.disabled = true;
            sendBtn.style.opacity = '0.7';
            sendBtn.style.cursor = 'not-allowed';
        }

        var url = '{{ route("admin.linkers-hub.send-registration-email", [], false) }}';
        makeAjaxRequest(url, 'POST', { employee_ids: checkedEmpIds }, function(data) {
            if (sendBtn) {
                sendBtn.textContent = originalText;
                sendBtn.disabled = false;
                sendBtn.style.opacity = '';
                sendBtn.style.cursor = '';
            }
            
            // Show custom toast notification
            showStaffLinkNotification(data.message || 'Registration email(s) sent successfully!', 'success');
            
            // Transition directly to Step 2
            document.getElementById('regEmailStep1').style.display = 'none';
            document.getElementById('regEmailStep2').style.display = 'block';

            // Set header message for sent emails
            var step2Title = document.getElementById('regEmailStep2Title');
            if (step2Title) {
                var sentCount = checkedEmpIds.length;
                step2Title.textContent = 'You have sent ' + sentCount + ' registration email(s) to your new employee' + (sentCount > 1 ? 's' : '');
            }
        }, function(errData) {
            if (sendBtn) {
                sendBtn.textContent = originalText;
                sendBtn.disabled = false;
                sendBtn.style.opacity = '';
                sendBtn.style.cursor = '';
            }
            // Show custom toast notification
            showStaffLinkNotification(errData.message || 'Error occurred while sending registration email.', 'error');
        });
    };

    window.renderRegEmailList = function() {
        var container = document.getElementById('regEmailList');
        if (!container) return;
        
        var query = (document.getElementById('regEmailSearch').value || '').toLowerCase().trim();
        var successEmps = ADDED_EMPLOYEES.filter(function(e) {
            return SUBMITTED_SUCCESS_IDS.indexOf(e.id) !== -1;
        });
        
        if (successEmps.length === 0) {
            container.innerHTML = '<div style="padding: 16px; text-align: center; color: #9ca3af; font-size: 13px;">No successfully saved employees</div>';
            return;
        }
        
        var html = '';
        var matchCount = 0;
        
        successEmps.forEach(function(emp) {
            var details = emp.details || {};
            var fName = details['empFirstName'] || emp.first_name || '';
            var lName = details['empLastName'] || emp.last_name || '';
            var fullName = (fName + ' ' + lName).trim() || 'No Name';
            var email = details['empEmail'] || emp.email || '';
            
            if (query && fullName.toLowerCase().indexOf(query) === -1 && email.toLowerCase().indexOf(query) === -1) {
                return;
            }
            
            matchCount++;
            html += '<div style="display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-bottom: 1px solid #e5e7eb; background: #f0fdf4;">';
            html += '  <div>';
            html += '    <div style="font-size: 14px; font-weight: 700; color: #1e293b;">' + escapeHtml(fullName) + '</div>';
            html += '    <div style="font-size: 12px; color: #64748b; margin-top: 2px;">' + escapeHtml(email) + '</div>';
            html += '  </div>';
            html += '  <label style="display: flex; align-items: center; cursor: pointer;">';
            html += '    <input type="checkbox" class="reg-email-checkbox" data-id="' + emp.id + '" checked style="width: 18px; height: 18px; accent-color: #2e7d5e;">';
            html += '  </label>';
            html += '</div>';
        });
        
        if (matchCount === 0) {
            container.innerHTML = '<div style="padding: 16px; text-align: center; color: #9ca3af; font-size: 13px;">No matching employees</div>';
        } else {
            container.innerHTML = html;
        }
    };

    function generateSubmissionStatusRow(emp) {
        var details = emp.details || {};
        var fName = details['empFirstName'] || emp.first_name || '';
        var lName = details['empLastName'] || emp.last_name || '';
        var fullName = (fName + ' ' + lName).trim() || 'No Name';
        var email = details['empEmail'] || emp.email || '-';

        // SVGs
        var greenCheck = '<svg style="width: 20px; height: 20px; color: #10b981; margin: 0 auto;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        var redCross = '<svg style="width: 20px; height: 20px; color: #ef4444; margin: 0 auto;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        var skippedDash = '<svg style="width: 20px; height: 20px; color: #6b7280; margin: 0 auto;" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';

        // 1. Personal Information (required, so successful)
        var personalStatus = greenCheck;

        // 2. Contact Information: Email, Mobile, Address 1, City, Postcode
        var contactStatus = skippedDash;
        if (details['empMobile'] || details['empAddr1'] || details['empCity']) {
            contactStatus = greenCheck;
        } else if (email && email !== '-') {
            contactStatus = greenCheck;
        }

        // 3. Sensitive Information: TFN, Passport No, Licence No, Visa No
        var sensitiveStatus = skippedDash;
        if (details['empTfn'] || details['empPassportNo'] || details['empLicenceNo'] || details['empVisaNo']) {
            sensitiveStatus = greenCheck;
        }

        // 4. Emergency Contact: emergency_contacts
        var emergencyStatus = skippedDash;
        if (emp.emergency_contacts && emp.emergency_contacts.length > 0) {
            emergencyStatus = greenCheck;
        }

        // 5. Bank details: empBankAccName, empBankName, empBankAccNo, empBankBsb
        var bankStatus = skippedDash;
        var bankFilledCount = 0;
        var bankFields = ['empBankAccName', 'empBankName', 'empBankAccNo', 'empBankBsb'];
        bankFields.forEach(function(f) {
            if (details[f] && details[f].trim() !== '') bankFilledCount++;
        });
        if (bankFilledCount === bankFields.length) {
            bankStatus = greenCheck;
        } else if (bankFilledCount > 0) {
            bankStatus = redCross; // Unsuccessful due to incomplete bank details
        }

        // 6. Salary Information: empSalary, empRate, empPayFreq
        var salaryStatus = skippedDash;
        if (details['empSalary'] && details['empSalary'] !== '0' && details['empSalary'].trim() !== '') {
            salaryStatus = greenCheck;
        }

        // 7. Payroll number: empPayrollNo
        var payrollStatus = skippedDash;
        if (details['empPayrollNo'] && details['empPayrollNo'].trim() !== '') {
            payrollStatus = greenCheck;
        }

        var html = '<tr style="border-bottom: 1px solid #e2e8f0; text-align: left;">';
        html += '  <td style="padding: 12px 16px; color: #1e293b; font-weight: 600;">' + escapeHtml(fullName) + '</td>';
        html += '  <td style="padding: 12px 16px; color: #475569;">' + escapeHtml(email) + '</td>';
        html += '  <td style="padding: 12px 16px; text-align: center;">' + personalStatus + '</td>';
        html += '  <td style="padding: 12px 16px; text-align: center;">' + contactStatus + '</td>';
        html += '  <td style="padding: 12px 16px; text-align: center;">' + sensitiveStatus + '</td>';
        html += '  <td style="padding: 12px 16px; text-align: center;">' + emergencyStatus + '</td>';
        html += '  <td style="padding: 12px 16px; text-align: center;">' + bankStatus + '</td>';
        html += '  <td style="padding: 12px 16px; text-align: center;">' + salaryStatus + '</td>';
        html += '  <td style="padding: 12px 16px; text-align: center;">' + payrollStatus + '</td>';
        html += '</tr>';
        return html;
    }

    function generateFailedStatusRow(emp, errorMsg) {
        var details = emp.details || {};
        var fName = details['empFirstName'] || emp.first_name || '';
        var lName = details['empLastName'] || emp.last_name || '';
        var fullName = (fName + ' ' + lName).trim() || 'No Name';
        var email = details['empEmail'] || emp.email || '-';

        var html = '<tr style="border-bottom: 1px solid #e2e8f0; text-align: left;">';
        html += '  <td style="padding: 12px 16px; color: #1e293b; font-weight: 600;">' + escapeHtml(fullName) + '</td>';
        html += '  <td style="padding: 12px 16px; color: #475569;">' + escapeHtml(email) + '</td>';
        html += '  <td style="padding: 12px 16px; color: #ef4444; font-weight: 500;">' + escapeHtml(errorMsg) + '</td>';
        html += '</tr>';
        return html;
    }

    // Jurisdiction dropdown change listener
    var jSel = document.getElementById('empJurisdiction');
    if (jSel) {
        jSel.addEventListener('change', function() {
            var val = this.value;
            if (selectedEmpId) {
                var emp = ADDED_EMPLOYEES.find(function(e) { return e.id === selectedEmpId; });
                if (emp) {
                    if (!emp.details) emp.details = {};
                    emp.details['empJurisdiction'] = val;
                }
            }
            updateSaveContinueBtn2State();
        });
    }

    var reqFields = ['empFirstName', 'empLastName', 'empEmail', 'empStartDate'];
    reqFields.forEach(function(fid) {
        var el = document.getElementById(fid);
        if (el) {
            el.addEventListener('input', updateSaveContinueBtnState);
            el.addEventListener('change', updateSaveContinueBtnState);
        }
    });

    var reqFieldsStep2 = ['empJurisdiction', 'empEmployeeType'];
    reqFieldsStep2.forEach(function(fid) {
        var el = document.getElementById(fid);
        if (el) {
            el.addEventListener('input', updateSaveContinueBtn2State);
            el.addEventListener('change', updateSaveContinueBtn2State);
        }
    });

    initializeWtpDropdown();
    validateForm();
    if (firstInput) firstInput.focus();
});
</script>
@endsection
