@extends('admin.layout')

@section('title', $employee->full_name . ' - Profile')
@section('page-title', session('_my_profile') ? 'My Profile' : 'Employee Profile')

@section('content')
@if(!session('_my_profile'))
<div style="margin-bottom:16px;">
    <a href="{{ route('admin.linkers-hub.index') }}" style="display:inline-flex;align-items:center;gap:6px;color:#2e7d5e;text-decoration:none;font-size:14px;font-weight:600;">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Back to Linkers Hub
    </a>
</div>
@endif
@php session()->forget('_my_profile'); @endphp
<style>
    /* ===== Profile Page Styles (BrightHR Style) ===== */
    .bhr-profile-container {
        background: #f5f7fa;
        min-height: 100%;
        padding: 0;
        font-family: 'Inter', 'Segoe UI', -apple-system, sans-serif;
    }
    /* -- Profile Header -- */
    .bhr-profile-header {
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        padding: 32px 40px 24px 40px;
    }
    .bhr-profile-header-inner {
        display: flex;
        align-items: flex-start;
        gap: 28px;
    }
    .bhr-avatar-wrap {
        position: relative;
        flex-shrink: 0;
    }
    .bhr-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: linear-gradient(135deg, #287854 0%, #1f5f46 100%);
        border: 5px solid #c8a84e;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.6rem;
        font-weight: 700;
        color: #fff;
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
    }
    .bhr-avatar-cam {
        position: absolute;
        bottom: 4px;
        right: 4px;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #D4A017;
        border: 3px solid #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s;
        box-shadow: 0 2px 6px rgba(0,0,0,0.18);
    }
    .bhr-avatar-cam:hover { background: #b5870f; }
    .bhr-avatar-cam svg { width: 15px; height: 15px; color: #fff; }
    .bhr-avatar-cam.is-delete { background: #e74c5e; }
    .bhr-avatar-cam.is-delete:hover { background: #d63c4e; }
    .bhr-avatar img {
        width: 100%; height: 100%; border-radius: 50%; object-fit: cover;
    }

    .bhr-profile-info { flex: 1; position: relative; }
    .bhr-profile-name {
        font-size: 1.45rem;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0 0 2px 0;
    }
    .bhr-profile-role {
        font-size: 0.92rem;
        color: #555;
        margin: 0 0 3px 0;
    }
    .bhr-profile-location {
        font-size: 0.88rem;
        color: #666;
        display: flex;
        align-items: center;
        gap: 4px;
        margin: 0 0 14px 0;
    }
    .bhr-profile-location svg { width: 14px; height: 14px; color: #888; }

    .bhr-contact-row {
        display: flex;
        align-items: flex-start;
        gap: 40px;
        margin-top: 8px;
    }
    .bhr-contact-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.88rem;
        color: #2e7d5e;
    }
    .bhr-contact-item svg { width: 16px; height: 16px; color: #2e7d5e; flex-shrink: 0; }

    .bhr-working-status {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 18px;
        background: #f8f9fa;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        position: absolute;
        top: 0;
        right: 0;
    }
    .bhr-working-status svg { width: 20px; height: 20px; color: #666; }
    .bhr-working-status-text {
        font-size: 0.88rem;
        color: #444;
    }
    .bhr-working-status-link {
        font-size: 0.85rem;
        color: #2e7d5e;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }
    .bhr-working-status-link:hover { text-decoration: underline; }
    .bhr-working-status-link.status-active      { color: #15803d; }
    .bhr-working-status-link.status-probation   { color: #92400e; }
    .bhr-working-status-link.status-on-leave    { color: #1d4ed8; }
    .bhr-working-status-link.status-joining-soon{ color: #5b21b6; }
    .bhr-working-status-link.status-terminated  { color: #b91c1c; }

    /* -- Profile progress ring -- */
    .bhr-progress-wrap {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        background: #f8f9fa;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
    }
    .bhr-progress-text { font-size: 0.78rem; color: #444; font-weight: 500; }
    .bhr-progress-ring-wrap { position: relative; width: 62px; height: 62px; }
    .bhr-progress-ring-wrap svg { transform: rotate(-90deg); }
    .bhr-progress-ring-bg { fill: none; stroke: #e5e7eb; stroke-width: 5; }
    .bhr-progress-ring-fill { fill: none; stroke-width: 5; stroke-linecap: round; transition: stroke-dashoffset 0.6s ease; }
    .bhr-progress-ring-label {
        position: absolute; inset: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 11px; font-weight: 700; color: #1a4d3e;
    }
    .bhr-progress-complete .bhr-progress-ring-label { color: #2e7d5e; }

    /* -- Tabs -- */
    .bhr-tabs {
        display: flex;
        background: #fff;
        border-bottom: 2px solid #e5e7eb;
        padding: 0 40px;
    }
    .bhr-tab {
        padding: 14px 24px;
        font-size: 0.92rem;
        font-weight: 500;
        color: #666;
        cursor: pointer;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        transition: all 0.2s;
        text-decoration: none;
        user-select: none;
    }
    .bhr-tab:hover { color: #1a1a2e; }
    .bhr-tab.active {
        color: #2e7d5e;
        border-bottom-color: #2e7d5e;
        font-weight: 600;
    }

    /* -- Tab Content -- */
    .bhr-tab-content {
        padding: 28px 40px 40px 40px;
    }
    .bhr-tab-pane { display: none; }
    .bhr-tab-pane.active { display: block; }

    /* -- Absence Section -- */
    .bhr-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .bhr-section-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: #1a1a2e;
    }
    .bhr-select {
        padding: 8px 32px 8px 14px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.88rem;
        color: #333;
        background: #fff;
        appearance: auto;
        cursor: pointer;
        outline: none;
    }
    .bhr-select:focus { border-color: #2e7d5e; }

    /* -- Stats Cards -- */
    .bhr-absences-title {
        text-align: center;
        font-size: 1.15rem;
        font-weight: 700;
        color: #1a1a2e;
        margin: 20px 0 24px 0;
    }
    .bhr-stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 32px;
    }
    .bhr-stat-card {
        text-align: center;
        padding: 24px 16px 20px 16px;
        border-right: 1px solid #e5e7eb;
    }
    .bhr-stat-card:last-child { border-right: none; }
    .bhr-stat-label {
        font-size: 0.82rem;
        color: #666;
        margin-bottom: 12px;
    }
    .bhr-stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 2px;
    }
    .bhr-stat-unit {
        font-size: 0.8rem;
        color: #888;
        margin-bottom: 14px;
    }
    .bhr-btn-pink {
        display: inline-block;
        padding: 8px 18px;
        background: #e74c5e;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .bhr-btn-pink:hover { background: #d63c4e; }
    .bhr-btn-outline {
        display: inline-block;
        padding: 7px 18px;
        background: #fff;
        color: #e74c5e;
        border: 2px solid #e74c5e;
        border-radius: 4px;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .bhr-btn-outline:hover { background: #fef2f2; }

    /* -- Absence History -- */
    .bhr-history-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 8px 0 20px 0;
    }
    .bhr-history-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1a1a2e;
    }
    .bhr-list-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: #fff;
        font-size: 0.85rem;
        color: #333;
        cursor: pointer;
    }
    .bhr-list-btn:hover { background: #f9fafb; }

    /* -- Absence filter pills (StaffLink green theme) -- */
    .bhr-pills { display: flex; flex-wrap: wrap; gap: 10px; }
    .bhr-pill {
        padding: 8px 18px;
        border: 1px solid #d1d5db;
        border-radius: 999px;
        background: #fff;
        font-size: 0.85rem;
        font-weight: 600;
        color: #2e7d5e;
        cursor: pointer;
        transition: all 0.15s;
    }
    .bhr-pill:hover { background: #eef6f2; }
    .bhr-pill.active { background: #eef6f2; border-color: #2e7d5e; color: #1b4332; }

    /* -- Year navigation -- */
    .bhr-year-nav { display: flex; align-items: center; gap: 8px; }
    .bhr-year-btn {
        width: 34px; height: 34px;
        display: flex; align-items: center; justify-content: center;
        border: 1px solid #d1d5db; border-radius: 6px; background: #fff; cursor: pointer; color: #444;
    }
    .bhr-year-btn:hover { background: #f9fafb; }
    .bhr-year-btn svg { width: 16px; height: 16px; }
    .bhr-year-label {
        display: flex; align-items: center; gap: 8px;
        font-size: 0.88rem; color: #333;
        border: 1px solid #d1d5db; border-radius: 6px; padding: 7px 14px;
    }
    .bhr-year-label svg { width: 16px; height: 16px; color: #666; }

    /* -- Stat card icon -- */
    .bhr-stat-icon {
        width: 36px; height: 36px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 10px auto;
    }
    .bhr-stat-icon svg { width: 18px; height: 18px; }

    /* -- Segmented toggle (List / Month / Year) -- */
    .bhr-seg { display: inline-flex; border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden; }
    .bhr-seg-btn {
        padding: 7px 18px; background: #fff; border: none;
        font-size: 0.85rem; font-weight: 600; color: #444; cursor: pointer;
    }
    .bhr-seg-btn:not(:last-child) { border-right: 1px solid #d1d5db; }
    .bhr-seg-btn.active { background: #1e3a5f; color: #fff; }

    /* -- Period dropdown (Personal only) -- */
    .abs-period { display: none; align-items: center; gap: 8px; }
    .abs-period.show { display: flex; }
    .abs-period-label { display: flex; align-items: center; gap: 6px; font-size: 0.88rem; color: #333; font-weight: 500; }

    /* -- Absence filtered panes -- */
    .abs-pane { display: none; }
    .abs-pane.active { display: block; }
    .abs-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 28px 24px;
        margin-bottom: 32px;
    }
    .abs-card-head {
        display: flex; align-items: center; justify-content: center; gap: 10px;
        margin-bottom: 20px;
    }
    .abs-card-head .bhr-stat-icon { margin: 0; }
    .abs-card-title { font-size: 1.05rem; font-weight: 600; color: #1a1a2e; }
    .abs-center { text-align: center; }
    .abs-sub { font-size: 0.9rem; color: #666; margin-bottom: 6px; }
    .abs-big { font-size: 2rem; font-weight: 700; color: #1a1a2e; }
    .abs-big small { font-size: 1rem; color: #888; font-weight: 400; }
    .abs-actions { margin-top: 20px; text-align: center; }
    /* three-column layout (lateness, other) */
    .abs-3col { display: grid; grid-template-columns: 1fr 1fr 1fr; align-items: center; gap: 16px; }
    .abs-col { text-align: center; }
    /* personal split layout */
    .abs-split { display: grid; grid-template-columns: 2fr 1fr; gap: 0; }
    .abs-split-left { padding-right: 24px; }
    .abs-split-right { border-left: 1px solid #e5e7eb; padding-left: 24px; text-align: center; display: flex; flex-direction: column; justify-content: center; }
    .abs-2col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin: 12px 0 18px; text-align: center; }
    .abs-reasons-title { font-size: 1rem; font-weight: 600; color: #1a1a2e; margin-bottom: 14px; }
    .abs-reasons-empty { color: #888; font-size: 0.9rem; }

    /* -- Collapsible Sections -- */
    .bhr-collapse-section {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        margin-bottom: 12px;
        overflow: hidden;
    }
    .bhr-collapse-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 16px 20px;
        background: #fff;
        cursor: pointer;
        user-select: none;
        transition: background 0.15s;
    }
    .bhr-collapse-header:hover { background: #f9fafb; }
    .bhr-collapse-arrow {
        width: 20px;
        height: 20px;
        color: #e74c5e;
        transition: transform 0.25s;
        flex-shrink: 0;
    }
    .bhr-collapse-header.open .bhr-collapse-arrow {
        transform: rotate(90deg);
    }
    .bhr-collapse-label {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1a1a2e;
    }
    .bhr-collapse-body {
        display: none;
        padding: 20px 24px 24px 50px;
        background: #fff;
        border-top: 1px solid #f0f0f0;
    }
    .bhr-collapse-body.open { display: block; }
    .bhr-nothing {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #888;
        font-size: 0.92rem;
    }
    .bhr-nothing svg { width: 22px; height: 22px; color: #ccc; }

    /* -- Employment Tab -- */
    .bhr-form-group {
        margin-bottom: 20px;
    }
    .bhr-form-label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 6px;
        font-size: 0.9rem;
    }
    .bhr-form-input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 0.92rem;
        color: #333;
        outline: none;
        transition: border-color 0.2s;
        box-sizing: border-box;
        background: #fff;
    }
    .bhr-form-input:focus { border-color: #2e7d5e; box-shadow: 0 0 0 3px rgba(46,125,94,0.08); }
    .bhr-form-input:disabled { background: #f3f4f6; color: #888; }
    .bhr-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .bhr-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 8px rgba(0,0,0,0.05);
        padding: 28px;
        margin-bottom: 20px;
    }
    .bhr-card-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: #1f5f46;
        margin: 0 0 20px 0;
        padding-bottom: 12px;
        border-bottom: 1px solid #f0f0f0;
    }
    .bhr-btn-save {
        padding: 10px 28px;
        background: #287854;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 0.92rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .bhr-btn-save:hover { background: #1f5f46; }

    .bhr-btn-gold {
        padding: 10px 28px;
        background: #c8a84e;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 0.92rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }
    .bhr-btn-gold:hover { background: #b49640; }

    /* ===== Employment tab (BrightHR style, green-gold theme) ===== */
    .emp-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; align-items: start; }
    .emp-card { border: 1px solid #e5e7eb; border-radius: 12px; margin-bottom: 24px; background: #fff; overflow: hidden; }
    .emp-card-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 22px; cursor: pointer; }
    .emp-card-title { font-size: 1.05rem; font-weight: 700; color: #1b4332; }
    .emp-card-sub { font-size: 0.82rem; color: #8a9b93; margin-top: 2px; }
    .emp-chev { width: 20px; height: 20px; color: #D4A017; transition: transform 0.2s; flex-shrink: 0; }
    .emp-card.collapsed .emp-chev { transform: rotate(-90deg); }
    .emp-card-body { padding: 0 22px 20px; }
    .emp-card.collapsed .emp-card-body { display: none; }

    .emp-keybox { background: #f0f7f4; border: 1px solid #d7e9e1; border-radius: 10px; padding: 14px 16px; margin-bottom: 14px; }
    .emp-keybox-title { font-weight: 700; color: #1b4332; font-size: 1rem; }

    .emp-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 0.92rem; }
    .emp-row:last-child { border-bottom: none; }
    .emp-k { color: #556; }
    .emp-v { color: #1a1a2e; font-weight: 500; text-align: right; }
    .emp-v.muted { color: #b0b8c4; font-weight: 400; }
    .emp-v.gold { color: #9a7d12; font-weight: 600; }
    .emp-v-link {
        background: none; border: none; padding: 0; font-family: inherit;
        color: #9a7d12; font-weight: 700; text-align: right; cursor: pointer;
        text-decoration: underline; font-size: inherit;
    }
    .emp-v-link:hover { color: #7a6209; }

    .emp-section-title { font-size: 0.95rem; font-weight: 700; color: #1b4332; margin: 20px 0 6px; }

    @media (max-width: 900px) { .emp-grid { grid-template-columns: 1fr; } }

    /* Employment panel forms (BrightHR style) */
    .emp-act-row { display: flex; justify-content: flex-end; gap: 18px; margin-bottom: 12px; }
    .emp-act-link { display: inline-flex; align-items: center; gap: 5px; color: #2e7d5e; font-size: 0.88rem; font-weight: 600; text-decoration: none; cursor: pointer; }
    .emp-act-link:hover { color: #1b4332; }
    .emp-act-link svg { width: 15px; height: 15px; }
    .emp-subhead { font-size: 1.05rem; font-weight: 700; color: #1b4332; margin: 4px 0 14px; }
    .emp-form-row { display: grid; grid-template-columns: 190px 1fr; gap: 16px; align-items: start; margin-bottom: 16px; }
    .emp-form-row > label { font-size: 0.9rem; color: #333; padding-top: 10px; display: flex; align-items: center; gap: 6px; }
    .emp-input, .emp-select { width: 100%; max-width: 430px; padding: 10px 13px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; color: #1a1a2e; background: #fff; outline: none; box-sizing: border-box; }
    .emp-input:focus, .emp-select:focus { border-color: #2e7d5e; box-shadow: 0 0 0 3px rgba(46,125,94,0.10); }
    select.emp-input {
        appearance: none; -webkit-appearance: none; -moz-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23667085' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat; background-position: right 12px center; background-size: 16px; padding-right: 38px; cursor: pointer;
    }
    .emp-help { font-size: 0.78rem; color: #9aa3af; margin-top: 4px; }
    .emp-info { width: 16px; height: 16px; color: #2e7d5e; }
    .emp-yn { display: inline-flex; gap: 10px; }
    .emp-yn-opt { display: inline-flex; align-items: center; gap: 8px; border: 1.5px solid #d1d5db; border-radius: 8px; padding: 9px 20px; cursor: pointer; font-weight: 600; color: #555; font-size: 0.88rem; user-select: none; }
    .emp-yn-opt input { accent-color: #2e7d5e; margin: 0; }
    .emp-yn-opt.sel { border-color: #2e7d5e; background: #eef6f2; color: #1b4332; }
    .emp-save-row { display: flex; gap: 10px; margin-top: 18px; }
    .emp-btn-save { padding: 9px 22px; border: none; border-radius: 8px; font-weight: 600; font-size: 0.9rem; background: #2e7d5e; color: #fff; cursor: pointer; transition: background 0.15s; }
    .emp-btn-save:hover { background: #1b5e44; }
    .emp-btn-save:disabled { background: #cbd5e1; color: #fff; cursor: not-allowed; }
    .emp-btn-cancel { padding: 9px 22px; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 600; font-size: 0.9rem; background: #fff; color: #374151; cursor: pointer; transition: background 0.15s; }
    .emp-btn-cancel:hover { background: #f9fafb; }
    .emp-btn-cancel:disabled { color: #999; cursor: not-allowed; }
    /* Toast notification */
    #empToast { position: fixed; bottom: 28px; right: 28px; z-index: 9999; display: flex; align-items: center; gap: 10px; padding: 12px 20px; border-radius: 10px; font-size: 0.9rem; font-weight: 600; color: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.15); opacity: 0; transform: translateY(10px); transition: opacity 0.2s, transform 0.2s; pointer-events: none; min-width: 220px; }
    #empToast.show { opacity: 1; transform: translateY(0); }
    #empToast.success { background: #2e7d5e; }
    #empToast.error { background: #e74c5e; }
    .emp-note-inline { font-size: 0.8rem; color: #b45309; background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 7px 11px; margin-top: 14px; }

    /* Personal / Emergencies pairs */
    .emp-pairs { display: grid; grid-template-columns: 1fr 1fr; gap: 16px 40px; }
    .emp-pair-label { font-weight: 700; color: #1b4332; font-size: 0.9rem; margin-bottom: 3px; }
    .emp-pair-val { color: #555; font-size: 0.92rem; }
    .emp-pair-val.muted { color: #aab2bd; }
    .emp-copy { width: 15px; height: 15px; color: #2e7d5e; vertical-align: middle; }
    .emp-badge { display: inline-block; border: 1px solid #cbd5e1; border-radius: 999px; padding: 2px 12px; font-size: 0.75rem; color: #555; font-weight: 600; margin-left: 8px; }
    .emp-add-btn { display: inline-flex; align-items: center; gap: 6px; background: #2e7d5e; color: #fff; border: none; border-radius: 8px; padding: 10px 18px; font-size: 0.9rem; font-weight: 600; cursor: pointer; margin-bottom: 18px; }
    .emp-add-btn:hover { background: #1b4332; }
    .emp-empty { text-align: center; color: #9aa3af; padding: 40px 0; font-size: 0.95rem; }

    /* Documents */
    .doc-bread { font-size: 0.95rem; color: #555; margin-bottom: 16px; }
    .doc-bread a { color: #2e7d5e; text-decoration: none; font-weight: 600; }
    .doc-toolbar { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 16px; }
    .doc-search { display: flex; gap: 8px; }
    .doc-search input { width: 320px; max-width: 60vw; padding: 10px 13px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.9rem; outline: none; }
    .doc-search input:focus { border-color: #2e7d5e; box-shadow: 0 0 0 3px rgba(46,125,94,0.1); }
    .doc-btn-grey { background: #cbd5e1; color: #fff; border: none; border-radius: 8px; padding: 10px 18px; font-weight: 600; font-size: 0.9rem; cursor: pointer; }
    .doc-right { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; }
    .doc-perpage { font-size: 0.9rem; color: #555; display: flex; align-items: center; gap: 8px; }
    .doc-perpage select { padding: 6px 26px 6px 10px; border: 1px solid #cbd5e1; border-radius: 6px; min-width: 68px; }
    .doc-actions { display: flex; gap: 10px; }
    .doc-btn-gold { display: inline-flex; align-items: center; gap: 6px; background: #D4A017; color: #fff; border: none; border-radius: 8px; padding: 10px 18px; font-weight: 600; font-size: 0.9rem; cursor: pointer; }
    .doc-btn-gold:hover { background: #b8890f; }
    .doc-btn-gold:disabled { background: #D4A017; cursor: not-allowed; }
    .doc-btn-green { display: inline-flex; align-items: center; gap: 6px; background: #2e7d5e; color: #fff; border: none; border-radius: 8px; padding: 10px 18px; font-weight: 600; font-size: 0.9rem; cursor: pointer; }
    .doc-btn-green:hover { background: #1b4332; }
    .doc-table { width: 100%; border-collapse: collapse; }
    .doc-table th { text-align: left; font-size: 0.85rem; color: #555; padding: 12px 10px; background: #f0f7f4; border-bottom: 1px solid #e5e7eb; }
    .doc-table td { padding: 14px 10px; border-bottom: 1px solid #f1f5f9; font-size: 0.92rem; color: #333; }
    .doc-empty { text-align: center; color: #9aa3af; padding: 40px 0; }

    .doc-upload-panel { border: 1px solid #e5e7eb; border-radius: 12px; padding: 22px; margin-bottom: 18px; display: none; }
    .doc-upload-panel.show { display: block; }
    .doc-dropzone { border: 2px dashed #cbd5e1; border-radius: 10px; padding: 38px; text-align: center; color: #777; }
    .doc-dropzone svg { display: inline-block; width: 44px; height: 44px; color: #9aa3af; margin: 0 auto 8px; }
    .doc-browse { display: inline-block; margin-top: 10px; border: 1.5px solid #2e7d5e; color: #2e7d5e; border-radius: 8px; padding: 8px 18px; font-weight: 600; cursor: pointer; }

    .doc-modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 1000; align-items: center; justify-content: center; }
    .doc-modal-overlay.show { display: flex; }
    .doc-modal { background: #fff; border-radius: 12px; width: 560px; max-width: 92vw; overflow: hidden; }
    .doc-modal-head { background: #1b4332; color: #fff; padding: 16px 22px; display: flex; align-items: center; justify-content: space-between; font-size: 1.1rem; font-weight: 700; }
    .doc-modal-head .x { cursor: pointer; font-size: 1.3rem; line-height: 1; }
    .doc-modal-body { padding: 22px; }
    .doc-modal-body > label { display: block; font-size: 0.9rem; color: #333; margin-bottom: 8px; }
    .doc-modal-body input[type=text] { width: 100%; padding: 11px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; outline: none; box-sizing: border-box; }
    .doc-colours { display: flex; gap: 12px; margin-top: 16px; flex-wrap: wrap; }
    .doc-colour { width: 44px; height: 44px; border-radius: 50%; border: 2px solid transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; }
    .doc-colour.sel { border-color: #1b4332; }
    .doc-colour svg { width: 24px; height: 24px; }
    .doc-modal-foot { display: flex; align-items: center; justify-content: space-between; padding: 16px 22px; background: #f8fafc; }

    /* ===== Absence calendar (Month / Year) ===== */
    .cal-top { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
    .cal-nav-btn { width: 30px; height: 30px; border: none; background: none; cursor: pointer; color: #2e7d5e; display: flex; align-items: center; justify-content: center; }
    .cal-nav-btn svg { width: 20px; height: 20px; }
    .cal-today { color: #2e7d5e; font-weight: 700; cursor: pointer; font-size: 0.92rem; }
    .cal-title { font-weight: 700; color: #1a1a2e; font-size: 1.1rem; flex: 1; text-align: center; }
    .cal-legend { display: flex; flex-wrap: wrap; gap: 12px 22px; margin-bottom: 14px; align-items: center; }
    .cal-leg { display: flex; align-items: center; gap: 7px; font-size: 0.86rem; color: #444; }
    .cal-dot { width: 13px; height: 13px; border-radius: 50%; flex-shrink: 0; }
    .cal-toggles { display: flex; gap: 20px; margin-left: auto; }
    .cal-toggle { display: flex; align-items: center; gap: 8px; font-size: 0.84rem; color: #444; cursor: pointer; }
    .cal-switch { position: relative; width: 38px; height: 22px; display: inline-block; }
    .cal-switch input { opacity: 0; width: 0; height: 0; }
    .cal-sl { position: absolute; inset: 0; background: #cbd5e1; border-radius: 999px; transition: .2s; cursor: pointer; }
    .cal-sl:before { content: ""; position: absolute; height: 16px; width: 16px; left: 3px; top: 3px; background: #fff; border-radius: 50%; transition: .2s; }
    .cal-switch input:checked + .cal-sl { background: #2e7d5e; }
    .cal-switch input:checked + .cal-sl:before { transform: translateX(16px); }

    .cal-weekhead { display: grid; grid-template-columns: repeat(7,1fr); }
    .cal-weekhead.no-weekend { grid-template-columns: repeat(5,1fr); }
    .cal-weekhead > div { padding: 8px; font-size: 0.85rem; color: #555; }
    .cal-grid { display: grid; grid-template-columns: repeat(7,1fr); border-left: 1px solid #e5e7eb; border-top: 1px solid #e5e7eb; }
    .cal-grid.no-weekend { grid-template-columns: repeat(5,1fr); }
    .cal-cell { min-height: 92px; border-right: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; padding: 6px 8px; }
    .cal-cell.out { background: #f6f8fa; }
    .cal-num { font-size: 0.85rem; color: #333; }
    .cal-cell.out .cal-num { color: #bbb; }
    .cal-cell.today .cal-num { background: #2e7d5e; color: #fff; border-radius: 50%; width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; }
    .cal-grid.no-weekend .cal-weekend, .cal-weekhead.no-weekend .cal-weekend { display: none; }
    .cal-hol-bar { margin-top: 8px; background: #7c5cd6; border-radius: 6px; height: 16px; display: flex; align-items: center; }
    .cal-hol-label { font-size: 0.7rem; color: #fff; padding: 0 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* Year view */
    .cal-year-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 22px; }
    @media (max-width: 1100px) { .cal-year-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width: 700px) { .cal-year-grid { grid-template-columns: 1fr; } }
    .cal-mini h4 { text-align: center; font-size: 0.95rem; color: #1a1a2e; margin: 0 0 8px; font-weight: 700; }
    .cal-mini-head { display: grid; grid-template-columns: repeat(7,1fr); }
    .cal-mini-head.no-weekend { grid-template-columns: repeat(5,1fr); }
    .cal-mini-head > div { font-size: 0.66rem; color: #888; text-align: center; padding: 2px 0; }
    .cal-mini-grid { display: grid; grid-template-columns: repeat(7,1fr); }
    .cal-mini-grid.no-weekend { grid-template-columns: repeat(5,1fr); }
    .cal-mini-cell { text-align: center; font-size: 0.72rem; padding: 4px 0; color: #444; }
    .cal-mini-cell.out { color: #ccc; }
    .cal-mini-cell.today span { background: #2e7d5e; color: #fff; border-radius: 50%; padding: 1px 5px; }
    .cal-mini-cell.hol span { background: #7c5cd6; color: #fff; border-radius: 8px; padding: 1px 6px; }
    .cal-mini-grid.no-weekend .cal-weekend, .cal-mini-head.no-weekend .cal-weekend { display: none; }

    /* Absence list items */
    .abs-item { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 14px; border: 1px solid #eef1f4; border-radius: 8px; margin-bottom: 8px; }
    .abs-item-main { display: flex; flex-direction: column; gap: 3px; }
    .abs-item-type { font-weight: 700; color: #1b4332; font-size: 0.92rem; }
    .abs-item-meta { font-size: 0.82rem; color: #777; }
    .abs-item-right { display: flex; align-items: center; gap: 12px; }
    .abs-item-badge { font-size: 0.72rem; font-weight: 600; color: #2e7d5e; background: #eef6f2; border-radius: 999px; padding: 3px 10px; }
    .abs-del { background: none; border: none; color: #e74c5e; cursor: pointer; font-size: 0.82rem; font-weight: 600; padding: 0; }
    .abs-del:hover { text-decoration: underline; }

    /* ===== Employment Modals (shared base) ===== */
    .emc-overlay {
        display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.55);
        z-index: 1100; align-items: center; justify-content: center; padding: 20px;
    }
    .emc-overlay.open { display: flex; }
    .emc-box { background: #fff; border-radius: 14px; width: 100%; max-width: 640px; max-height: 88vh;
        overflow-y: auto; box-shadow: 0 20px 40px rgba(0,0,0,0.25); }
    .emc-box.wide { max-width: 720px; }
    .emc-header {
        background: #1b4332; color: #fff; padding: 18px 24px;
        display: flex; align-items: center; justify-content: space-between;
        border-bottom: 3px solid #D4A017; position: sticky; top: 0; z-index: 2;
    }
    .emc-header h3 { margin: 0; font-size: 1.1rem; font-weight: 700; }
    .emc-close { background: none; border: none; color: #fff; font-size: 1.4rem; line-height: 1; cursor: pointer; opacity: 0.85; padding: 0 2px; }
    .emc-close:hover { opacity: 1; }
    .emc-body { padding: 24px; }
    .emc-footer { display: flex; justify-content: flex-end; gap: 12px; padding: 16px 24px; border-top: 1px solid #eef1f4; }
    .emc-footer.split { justify-content: space-between; }
    .emc-footer-left { display: flex; gap: 12px; }

    .emc-btn-outline {
        border: 1.5px solid #D4A017; color: #a3790f; background: #fff;
        border-radius: 8px; padding: 9px 20px; font-size: 0.88rem; font-weight: 700; cursor: pointer;
    }
    .emc-btn-outline:hover { background: #fdf6e7; }
    .emc-btn-primary {
        border: none; background: #2e7d5e; color: #fff;
        border-radius: 8px; padding: 9px 22px; font-size: 0.88rem; font-weight: 700; cursor: pointer;
    }
    .emc-btn-primary:hover { background: #1b4332; }
    .emc-btn-primary:disabled { background: #cbd5e1; cursor: not-allowed; }

    /* Step tabs (Contract / Summary) */
    .emc-steps { display: flex; position: relative; }
    .emc-step {
        flex: 1; text-align: center; padding: 14px 10px; font-weight: 700; font-size: 0.95rem;
        background: #eef6f2; color: #1b4332; position: relative; z-index: 1;
    }
    .emc-step.active { background: #2e7d5e; color: #fff; z-index: 2; }
    .emc-step:not(:last-child) { clip-path: polygon(0 0, calc(100% - 14px) 0, 100% 50%, calc(100% - 14px) 100%, 0 100%); margin-right: -14px; }
    .emc-step:not(:first-child) { clip-path: polygon(14px 0, 100% 0, 100% 100%, 14px 100%, 0 50%); }

    /* Hours summary modal */
    .emc-pattern-meta { font-size: 0.92rem; color: #444; margin-bottom: 18px; }
    .emc-pattern-meta strong { color: #1b4332; }
    .emc-pattern-start { font-size: 0.9rem; color: #444; margin-bottom: 16px; }
    .emc-day-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eef1f4; font-size: 0.92rem; }
    .emc-day-row:last-of-type { border-bottom: none; }
    .emc-day-name { font-weight: 700; color: #1a1a2e; width: 90px; flex-shrink: 0; }
    .emc-day-time { color: #444; flex: 1; }
    .emc-day-break { color: #888; font-size: 0.85rem; }
    .emc-day-off { color: #b0b8c4; }
    .emc-today-badge { background: #eef6f2; color: #1b4332; border: 1px solid #2e7d5e; border-radius: 999px; padding: 2px 10px; font-size: 0.75rem; font-weight: 700; }
    .emc-repeat-row { display: flex; align-items: center; gap: 8px; margin-top: 16px; font-size: 0.88rem; color: #444; }
    .emc-repeat-row svg { width: 16px; height: 16px; color: #2e7d5e; }

    /* History modal (empty state) */
    .emc-tabs { display: flex; border-bottom: 1px solid #eef1f4; margin-bottom: 28px; }
    .emc-tab { padding: 12px 20px; font-weight: 700; font-size: 0.92rem; color: #888; border-bottom: 3px solid transparent; cursor: pointer; }
    .emc-tab.active { color: #1b4332; border-bottom-color: #2e7d5e; }
    .emc-empty { display: flex; flex-direction: column; align-items: center; text-align: center; padding: 30px 20px; }
    .emc-empty svg { width: 90px; height: 70px; color: #cbd5e1; margin-bottom: 18px; }
    .emc-empty-text { color: #555; font-size: 0.95rem; }
    .emc-history-item { display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border: 1px solid #eef1f4; border-radius: 8px; margin-bottom: 10px; }
    .emc-history-item-title { font-weight: 700; color: #1b4332; font-size: 0.92rem; }
    .emc-history-item-meta { font-size: 0.82rem; color: #777; margin-top: 2px; }

    /* Edit contract step 1: choice cards */
    .emc-choice-card {
        display: flex; align-items: center; gap: 16px; border: 1.5px solid #e5e7eb; border-radius: 10px;
        padding: 16px 18px; margin-bottom: 14px; cursor: pointer; transition: all .15s;
    }
    .emc-choice-card:hover { border-color: #2e7d5e; background: #f8fcfa; }
    .emc-choice-card.selected { border-color: #2e7d5e; background: #eef6f2; }
    .emc-choice-radio { width: 20px; height: 20px; border: 2px solid #cbd5e1; border-radius: 50%; flex-shrink: 0; position: relative; }
    .emc-choice-card.selected .emc-choice-radio { border-color: #2e7d5e; }
    .emc-choice-card.selected .emc-choice-radio::after { content: ''; position: absolute; inset: 3px; border-radius: 50%; background: #2e7d5e; }
    .emc-choice-icon { width: 34px; height: 34px; color: #2e7d5e; flex-shrink: 0; }
    .emc-choice-title { font-weight: 700; color: #1a1a2e; font-size: 0.95rem; margin-bottom: 2px; }
    .emc-choice-desc { font-size: 0.85rem; color: #777; }

    /* Edit contract forms */
    .emc-field { margin-bottom: 20px; }
    .emc-field-row { display: flex; align-items: center; justify-content: space-between; gap: 20px; margin-bottom: 20px; }
    .emc-field-row label { font-weight: 500; color: #333; font-size: 0.92rem; }
    .emc-field label { display: block; font-weight: 500; color: #333; font-size: 0.92rem; margin-bottom: 8px; }
    .emc-input, .emc-select, .emc-textarea {
        padding: 11px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.92rem;
        color: #1a1a2e; background: #fff; outline: none; box-sizing: border-box; min-width: 220px;
    }
    .emc-select {
        appearance: none; -webkit-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23667085' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat; background-position: right 12px center; background-size: 16px; padding-right: 38px;
    }
    .emc-input:focus, .emc-select:focus, .emc-textarea:focus { border-color: #2e7d5e; box-shadow: 0 0 0 3px rgba(46,125,94,0.10); }
    .emc-textarea { width: 100%; min-height: 80px; resize: vertical; }
    .emc-switch { position: relative; display: inline-block; width: 42px; height: 24px; flex-shrink: 0; }
    .emc-switch input { opacity: 0; width: 0; height: 0; }
    .emc-switch-slider { position: absolute; cursor: pointer; inset: 0; background-color: #cbd5e1; transition: 0.2s; border-radius: 999px; }
    .emc-switch-slider::before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: #fff; transition: 0.2s; border-radius: 50%; }
    .emc-switch input:checked + .emc-switch-slider { background-color: #2e7d5e; }
    .emc-switch input:checked + .emc-switch-slider::before { transform: translateX(18px); }
    .emc-info-banner {
        display: flex; gap: 10px; background: #eef6f2; border: 1px solid #d3e8de; border-radius: 8px;
        padding: 12px 16px; font-size: 0.86rem; color: #2c4a3d; margin: 16px 0;
    }
    .emc-info-banner svg { width: 18px; height: 18px; color: #2e7d5e; flex-shrink: 0; margin-top: 1px; }
    .emc-info-banner a { color: #1b4332; font-weight: 700; text-decoration: underline; cursor: pointer; }
    .emc-bold-note { font-weight: 700; color: #1a1a2e; font-size: 0.9rem; margin-top: 4px; }

    /* Summary step */
    .emc-summary-box { border: 1px solid #eef1f4; border-radius: 10px; padding: 18px 20px; margin-bottom: 18px; }
    .emc-summary-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
    .emc-summary-label { font-weight: 700; color: #1a1a2e; font-size: 0.88rem; margin-bottom: 4px; }
    .emc-summary-value { color: #444; font-size: 0.92rem; }
    .emc-nochange-banner { background: #fdf6e7; color: #8a6d1c; border: 1px solid #f1dca0; border-radius: 8px; padding: 12px 16px; font-size: 0.88rem; margin-bottom: 16px; }
    .emc-confirm-row { display: flex; align-items: flex-start; gap: 10px; margin-top: 14px; font-size: 0.88rem; color: #2c4a3d; }
    .emc-confirm-row input[type="checkbox"] { margin-top: 2px; width: 16px; height: 16px; accent-color: #2e7d5e; }

    /* Yes/No pill toggle (used in simple edit modals) */
    .emc-pill-group { display: flex; gap: 10px; }
    .emc-pill {
        flex: 1; text-align: center; padding: 10px 16px; border: 1.5px solid #cbd5e1; border-radius: 8px;
        font-size: 0.9rem; font-weight: 600; color: #444; cursor: pointer; background: #fff;
    }
    .emc-pill.selected { border-color: #2e7d5e; background: #eef6f2; color: #1b4332; }

    /* Generic history table */
    .emc-table { width: 100%; border-collapse: collapse; }
    .emc-table thead th {
        text-align: left; font-size: 0.82rem; font-weight: 700; color: #1b4332; text-transform: uppercase;
        background: #eef6f2; padding: 10px 14px; letter-spacing: 0.3px;
    }
    .emc-table tbody td { padding: 12px 14px; font-size: 0.9rem; color: #333; border-bottom: 1px solid #eef1f4; }
    .emc-table tbody tr:last-child td { border-bottom: none; }

</style>

<div class="bhr-profile-container">

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div style="background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:14px 40px;font-size:0.92rem;">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:14px 40px;font-size:0.92rem;">
            <ul style="margin:0;padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ======= PROFILE HEADER ======= --}}
    <div class="bhr-profile-header">
        <div class="bhr-profile-header-inner">
            {{-- Avatar --}}
            <div class="bhr-avatar-wrap">
                <div class="bhr-avatar" id="avatarDisplay" style="background:{{ $employee->avatar_color }};">
                    @if($employee->avatar_path)
                        <img src="{{ route('admin.linkers-hub.serve-avatar', $employee->id) }}" alt="{{ $employee->full_name }}">
                    @else
                        {{ $employee->initials }}
                    @endif
                </div>
                <input type="file" id="avatarFileInput" accept="image/*" style="display:none;" onchange="handleAvatarFileSelect(event)">
                <div class="bhr-avatar-cam" id="avatarCamBtn" onclick="avatarCamClick()" title="{{ $employee->avatar_path ? 'Delete profile picture' : 'Edit profile picture' }}">
                    @if($employee->avatar_path)
                        <svg id="avatarCamIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    @else
                        <svg id="avatarCamIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                    @endif
                </div>
            </div>

            {{-- Info --}}
            <div class="bhr-profile-info">
                <h1 class="bhr-profile-name">{{ $employee->full_name }}</h1>
                <p class="bhr-profile-role">{{ $employee->position_title ?? 'Staff Member' }}</p>
                <p class="bhr-profile-location">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                    {{ $employee->division?->name ?? 'No team' }}{{ $employee->subDivision ? ' · ' . $employee->subDivision->name : '' }}
                </p>

                <div class="bhr-contact-row">
                    <div style="display:flex;flex-direction:column;gap:6px;">
                        <div class="bhr-contact-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/>
                            </svg>
                            {{ $employee->email ?? '—' }}
                        </div>
                        <div class="bhr-contact-item">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                            </svg>
                            {{ $employee->phone ?? '—' }}
                        </div>
                    </div>
                    {{-- Status + Progress stacked vertically on the right --}}
                    <div style="display:flex;flex-direction:column;gap:8px;margin-left:auto;">
                        <div class="bhr-working-status">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z"/>
                            </svg>
                            <div>
                                <div class="bhr-working-status-text">Status</div>
                                @php
                                    $displayStatus = $employee->status ?? 'active';
                                    $statusLabels  = [
                                        'active'       => 'Active',
                                        'probation'    => 'Probation',
                                        'on-leave'     => 'On Leave',
                                        'joining-soon' => 'Joining Soon',
                                        'terminated'   => 'Terminated',
                                    ];
                                    $statusLabel = $statusLabels[$displayStatus] ?? ucfirst($displayStatus);
                                @endphp
                                <span class="bhr-working-status-link status-{{ $displayStatus }}" id="employeeStatusLink" onclick="openStatusModal()" title="Click to change status">{{ $statusLabel }}</span>
                            </div>
                        </div>
                        {{-- Profile completion progress ring --}}
                        @php
                            $pct      = $profileProgress['percent'] ?? 0;
                            $radius   = 26;
                            $circ     = round(2 * M_PI * $radius, 2);
                            $offset   = round($circ - ($pct / 100) * $circ, 2);
                            $ringColor = $pct === 100 ? '#2e7d5e' : '#D4A017';
                        @endphp
                        <div class="bhr-progress-wrap {{ $pct === 100 ? 'bhr-progress-complete' : '' }}">
                            <div class="bhr-progress-text">{{ $pct === 100 ? 'Complete' : 'Profile' }}</div>
                            <div class="bhr-progress-ring-wrap">
                                <svg width="62" height="62" viewBox="0 0 62 62">
                                    <circle class="bhr-progress-ring-bg" cx="31" cy="31" r="{{ $radius }}"/>
                                    <circle class="bhr-progress-ring-fill"
                                        cx="31" cy="31" r="{{ $radius }}"
                                        stroke="{{ $ringColor }}"
                                        stroke-dasharray="{{ $circ }}"
                                        stroke-dashoffset="{{ $offset }}"/>
                                </svg>
                                <div class="bhr-progress-ring-label">{{ $pct }}%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ======= TABS ======= --}}
    <div class="bhr-tabs" id="profileTabs">
        <div class="bhr-tab active" data-tab="absence">Absence</div>
        <div class="bhr-tab" data-tab="employment">Employment</div>
        <div class="bhr-tab" data-tab="personal">Personal</div>
        <div class="bhr-tab" data-tab="emergencies">Emergencies</div>
        <div class="bhr-tab" data-tab="documents">Documents</div>
        <div class="bhr-tab" data-tab="shifts">Shifts</div>
        <div class="bhr-tab" data-tab="kpi">KPI</div>
        <div class="bhr-tab" data-tab="equipment">Equipment on loan</div>
    </div>

    {{-- ======= TAB CONTENT ======= --}}
    <div class="bhr-tab-content">

        {{-- ===== ABSENCE TAB ===== --}}
        <div class="bhr-tab-pane active" id="tab-absence">
            @php
                $absences = $absences ?? collect();
                $today = \Carbon\Carbon::today();
                // Per-type collections
                $annualAbs   = $absences->where('type', 'annual');
                $personalAbs = $absences->where('type', 'personal');
                $latenessAbs = $absences->where('type', 'lateness');
                $otherAbs    = $absences->where('type', 'other');
                // Totals — days computed from start/end dates
                $calcDays = function($col) {
                    return $col->sum(function($a) {
                        if ($a->start_date && $a->end_date) {
                            return $a->start_date->diffInDays($a->end_date) + 1;
                        }
                        return 1;
                    });
                };
                $annualDays    = $calcDays($annualAbs);
                $otherDays     = $calcDays($otherAbs);
                $personalDays  = $calcDays($personalAbs);
                $personalCount = $personalAbs->count();
                $latenessCount = $latenessAbs->count();
                $lateTotalMin  = $latenessAbs->sum(function ($a) { return ((int) $a->late_hours * 60) + (int) $a->late_minutes; });
                $lateH = intdiv($lateTotalMin, 60);
                $lateM = $lateTotalMin % 60;
                // History split (current/future vs past)
                $currentFuture = $absences->filter(function ($a) use ($today) {
                    $ref = $a->end_date ?: $a->start_date;
                    return $a->ongoing || ($ref && $ref->gte($today));
                })->sortBy('start_date')->values();
                $pastAbs = $absences->filter(function ($a) use ($today) {
                    $ref = $a->end_date ?: $a->start_date;
                    return ! $a->ongoing && $ref && $ref->lt($today);
                })->sortByDesc('start_date')->values();
                // Label helper
                $typeLabels = ['annual' => 'Annual leave', 'personal' => "Personal / carer's leave", 'lateness' => 'Lateness', 'other' => 'Other'];
            @endphp
            {{-- Filter Row --}}
            <div class="bhr-section-header">
                <div class="bhr-pills">
                    <button type="button" class="bhr-pill active" data-target="all" onclick="setAbsenceFilter(this)">All absences</button>
                    <button type="button" class="bhr-pill" data-target="annual" onclick="setAbsenceFilter(this)">Annual leave</button>
                    <button type="button" class="bhr-pill" data-target="lateness" onclick="setAbsenceFilter(this)">Lateness</button>
                    <button type="button" class="bhr-pill" data-target="personal" onclick="setAbsenceFilter(this)">Personal / carer's</button>
                    <button type="button" class="bhr-pill" data-target="other" onclick="setAbsenceFilter(this)">Other</button>
                </div>
                <div style="display:flex;align-items:center;gap:16px;">
                    {{-- Period dropdown (only shown for Personal / carer's) --}}
                    <div class="abs-period" id="absPeriod">
                        <span class="abs-period-label">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#2e7d5e" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                            Period
                        </span>
                        <select class="bhr-select">
                            <option>Leave year</option>
                            <option>Past 3 months</option>
                            <option>Past 6 months</option>
                            <option>Past 12 months</option>
                        </select>
                    </div>
                    <div class="bhr-year-nav">
                        <button type="button" class="bhr-year-btn" title="Previous year">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg>
                        </button>
                        <span class="bhr-year-label">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                            01 Jan 2026 - 31 Dec 2026
                        </span>
                        <button type="button" class="bhr-year-btn" title="Next year">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            {{-- ===== ABSENCE PANES (switched by pills) ===== --}}

            {{-- Pane: ALL absences (4 cards) --}}
            <div class="abs-pane active" id="abs-all">
                <div class="bhr-stats-row">
                    {{-- Annual leave --}}
                    <div class="bhr-stat-card">
                        <div class="bhr-stat-icon" style="background:#e0f2f1;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#009688"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                        </div>
                        <div class="bhr-stat-label">Annual leave</div>
                        <div class="bhr-stat-value">{{ $annualDays }}</div>
                        <div class="bhr-stat-unit">days</div>
                        <a href="{{ route('admin.linkers-hub.add-absence', [$employee->id, 'annual']) }}" class="bhr-btn-outline">Add annual leave</a>
                    </div>
                    {{-- Personal / carer's --}}
                    <div class="bhr-stat-card">
                        <div class="bhr-stat-icon" style="background:#fff3e0;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#f59e0b"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                        </div>
                        <div class="bhr-stat-label">Personal / carer's</div>
                        <div class="bhr-stat-value">{{ $personalCount }}</div>
                        <div class="bhr-stat-unit">occurrences</div>
                        <a href="{{ route('admin.linkers-hub.add-absence', [$employee->id, 'personal']) }}" class="bhr-btn-outline">Add</a>
                    </div>
                    {{-- Lateness --}}
                    <div class="bhr-stat-card">
                        <div class="bhr-stat-icon" style="background:#fce4ec;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#ec407a"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div class="bhr-stat-label">Lateness</div>
                        <div class="bhr-stat-value">{{ $latenessCount }}</div>
                        <div class="bhr-stat-unit">occurrences</div>
                        <a href="{{ route('admin.linkers-hub.add-absence', [$employee->id, 'lateness']) }}" class="bhr-btn-outline">Add</a>
                    </div>
                    {{-- Other --}}
                    <div class="bhr-stat-card">
                        <div class="bhr-stat-icon" style="background:#e8f5e9;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#43a047"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z"/></svg>
                        </div>
                        <div class="bhr-stat-label">Other</div>
                        <div class="bhr-stat-value">{{ $otherDays }}</div>
                        <div class="bhr-stat-unit">days</div>
                        <a href="{{ route('admin.linkers-hub.add-absence', [$employee->id, 'other']) }}" class="bhr-btn-outline">Add</a>
                    </div>
                </div>
            </div>

            {{-- Pane: ANNUAL LEAVE --}}
            <div class="abs-pane" id="abs-annual">
                <div class="abs-card abs-center">
                    <div class="abs-card-head">
                        <div class="bhr-stat-icon" style="background:#e0f2f1;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#009688"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>
                        </div>
                        <span class="abs-card-title">Annual leave</span>
                    </div>
                    <div class="abs-sub">Total leave taken</div>
                    <div class="abs-big">{{ $annualDays }} <small>days</small></div>
                    <div class="abs-actions"><a href="{{ route('admin.linkers-hub.add-absence', [$employee->id, 'annual']) }}" class="bhr-btn-outline">Add annual leave</a></div>
                </div>
            </div>

            {{-- Pane: LATENESS --}}
            <div class="abs-pane" id="abs-lateness">
                <div class="abs-card">
                    <div class="abs-card-head">
                        <div class="bhr-stat-icon" style="background:#fce4ec;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#ec407a"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="abs-card-title">Lateness</span>
                    </div>
                    <div class="abs-3col">
                        <div class="abs-col">
                            <div class="abs-sub">Logged</div>
                            <div class="abs-big">{{ $latenessCount }}</div>
                        </div>
                        <div class="abs-col">
                            <a href="{{ route('admin.linkers-hub.add-absence', [$employee->id, 'lateness']) }}" class="bhr-btn-outline">Add lateness</a>
                        </div>
                        <div class="abs-col">
                            <div class="abs-sub">Total</div>
                            <div class="abs-big">{{ $lateH }}<small>h</small> {{ $lateM }}<small>m</small></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pane: PERSONAL / CARER'S --}}
            <div class="abs-pane" id="abs-personal">
                <div class="abs-card">
                    <div class="abs-split">
                        <div class="abs-split-left">
                            <div class="abs-card-head" style="justify-content:flex-start;">
                                <div class="bhr-stat-icon" style="background:#fff3e0;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#f59e0b"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                </div>
                                <span class="abs-card-title">Personal / carer's leave</span>
                            </div>
                            <div class="abs-2col">
                                <div class="abs-col">
                                    <div class="abs-sub">Occurrences</div>
                                    <div class="abs-big">{{ $personalCount }}</div>
                                </div>
                                <div class="abs-col">
                                    <div class="abs-sub">Total days (approx)</div>
                                    <div class="abs-big">{{ $personalDays }}</div>
                                </div>
                            </div>
                            <div class="abs-actions"><a href="{{ route('admin.linkers-hub.add-absence', [$employee->id, 'personal']) }}" class="bhr-btn-outline">Add personal / carer's leave</a></div>
                        </div>
                        <div class="abs-split-right">
                            <div class="abs-reasons-title">Reasons</div>
                            @php $reasons = $personalAbs->whereNotNull('reason')->groupBy('reason'); @endphp
                            @forelse($reasons as $reasonName => $items)
                                <div class="abs-reasons-empty" style="color:#1a1a2e;">{{ $reasonName }} <strong>({{ $items->count() }})</strong></div>
                            @empty
                                <div class="abs-reasons-empty">No reasons logged</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pane: OTHER --}}
            <div class="abs-pane" id="abs-other">
                <div class="abs-card">
                    <div class="abs-card-head">
                        <div class="bhr-stat-icon" style="background:#e8f5e9;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#43a047"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z"/></svg>
                        </div>
                        <span class="abs-card-title">Other</span>
                    </div>
                    <div class="abs-3col">
                        <div class="abs-col">
                            <div class="abs-sub">Logged</div>
                            <div class="abs-big">{{ $otherAbs->count() }}</div>
                        </div>
                        <div class="abs-col">
                            <a href="{{ route('admin.linkers-hub.add-absence', [$employee->id, 'other']) }}" class="bhr-btn-outline">Add other</a>
                        </div>
                        <div class="abs-col">
                            <div class="abs-sub">Total</div>
                            <div class="abs-big">{{ $otherDays }} <small>days</small></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Absence History --}}
            <div class="bhr-history-header">
                <h3 class="bhr-history-title">Absence history</h3>
                <div class="bhr-seg">
                    <button type="button" class="bhr-seg-btn active" onclick="setHistoryView(this)">List</button>
                    <button type="button" class="bhr-seg-btn" onclick="setHistoryView(this)">Month</button>
                    <button type="button" class="bhr-seg-btn" onclick="setHistoryView(this)">Year</button>
                </div>
            </div>

            <div id="absHistList">
            {{-- Collapsible: Current & future --}}
            <div class="bhr-collapse-section">
                <div class="bhr-collapse-header open" onclick="toggleCollapse(this)">
                    <svg class="bhr-collapse-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                    <span class="bhr-collapse-label">Current & future ({{ $currentFuture->count() }})</span>
                </div>
                <div class="bhr-collapse-body open">
                    @forelse($currentFuture as $a)
                        <div class="abs-item">
                            <div class="abs-item-main">
                                <span class="abs-item-type">{{ $typeLabels[$a->type] ?? ucfirst($a->type) }}@if($a->reason) — {{ $a->reason }}@endif</span>
                                <span class="abs-item-meta">
                                    @if($a->type === 'lateness')
                                        {{ $a->start_date?->format('d M Y') }} · Late by {{ $a->late_by }}
                                    @else
                                        {{ $a->start_date?->format('d M Y') }}@if($a->end_date) – {{ $a->end_date->format('d M Y') }}@elseif($a->is_ongoing) – Ongoing @endif · {{ $a->days }} day{{ $a->days == 1 ? '' : 's' }}
                                    @endif
                                    @if($a->notes) · {{ \Illuminate\Support\Str::limit($a->notes, 60) }}@endif
                                </span>
                            </div>
                            <div class="abs-item-right">
                                @if($a->is_company_paid)<span class="abs-item-badge">Paid</span>@endif
                                @if($a->is_evidenced)<span class="abs-item-badge">Evidenced</span>@endif
                                <form method="POST" action="{{ route('admin.linkers-hub.destroy-absence', $a->id) }}" id="abs-del-form-{{ $a->id }}" style="margin:0;">
                                    @csrf @method('DELETE')
                                    <button type="button" class="abs-del" onclick="confirmRemoveAbsence({{ $a->id }})">Remove</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="bhr-nothing">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                            </svg>
                            Nothing to see here.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Collapsible: Absence history --}}
            <div class="bhr-collapse-section">
                <div class="bhr-collapse-header" onclick="toggleCollapse(this)">
                    <svg class="bhr-collapse-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                    <span class="bhr-collapse-label">Absence history ({{ $pastAbs->count() }})</span>
                </div>
                <div class="bhr-collapse-body">
                    @forelse($pastAbs as $a)
                        <div class="abs-item">
                            <div class="abs-item-main">
                                <span class="abs-item-type">{{ $typeLabels[$a->type] ?? ucfirst($a->type) }}@if($a->reason) — {{ $a->reason }}@endif</span>
                                <span class="abs-item-meta">
                                    @if($a->type === 'lateness')
                                        {{ $a->start_date?->format('d M Y') }} · Late by {{ $a->late_by }}
                                    @else
                                        {{ $a->start_date?->format('d M Y') }}@if($a->end_date) – {{ $a->end_date->format('d M Y') }}@endif · {{ $a->days }} day{{ $a->days == 1 ? '' : 's' }}
                                    @endif
                                    @if($a->notes) · {{ \Illuminate\Support\Str::limit($a->notes, 60) }}@endif
                                </span>
                            </div>
                            <div class="abs-item-right">
                                @if($a->is_company_paid)<span class="abs-item-badge">Paid</span>@endif
                                @if($a->is_evidenced)<span class="abs-item-badge">Evidenced</span>@endif
                                <form method="POST" action="{{ route('admin.linkers-hub.destroy-absence', $a->id) }}" id="abs-del-form-{{ $a->id }}" style="margin:0;">
                                    @csrf @method('DELETE')
                                    <button type="button" class="abs-del" onclick="confirmRemoveAbsence({{ $a->id }})">Remove</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="bhr-nothing">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                            </svg>
                            Nothing to see here.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Collapsible: Public holidays --}}
            <div class="bhr-collapse-section">
                <div class="bhr-collapse-header" onclick="toggleCollapse(this)">
                    <svg class="bhr-collapse-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                    <span class="bhr-collapse-label" id="pubHolLabel">Public holidays</span>
                </div>
                <div class="bhr-collapse-body" id="pubHolBody">
                    <div class="bhr-nothing">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                        </svg>
                        Loading…
                    </div>
                </div>
            </div>
            </div> {{-- /#absHistList --}}

            {{-- Month view (rendered by JS) --}}
            <div id="absHistMonth" style="display:none;"></div>
            {{-- Year view (rendered by JS) --}}
            <div id="absHistYear" style="display:none;"></div>
        </div>

        {{-- ===== EMPLOYMENT TAB ===== --}}
        <div class="bhr-tab-pane" id="tab-employment">
            @php
                $ex = $employee->extra_details ?? [];
                $g = function ($k) use ($ex) {
                    return (isset($ex[$k]) && trim((string) $ex[$k]) !== '') ? $ex[$k] : null;
                };
                // Helper for payroll detail fields (reads from new table)
                $pd = $employee->payrollDetail;
                $pgf = function ($k) use ($pd) {
                    return $pd ? $pd->$k : null;
                };
                // Helper for employment detail fields (reads from new table)
                $ed = $employee->employmentDetail;
                $edf = function ($k) use ($ed) {
                    return $ed ? $ed->$k : null;
                };
                // Helper for document fields (reads from employee_documents table)
                $docs     = $employee->documents->keyBy('type');
                $passport = $docs->get('passport');
                $licence  = $docs->get('driving_licence');
                $visa     = $docs->get('visa');
                // Length of service
                $los = null;
                if ($employee->start_date) {
                    $d = $employee->start_date->diff(now());
                    $los = $d->y . ' years ' . $d->m . ' months';
                }
                // Composed working address
                $addr = collect([$employee->address_1, $employee->city, $employee->territory, $employee->postcode])->filter()->implode(', ');
                $workLoc = $edf('place_of_work') ?: ($addr ?: null);
                $startFmt = $employee->start_date ? $employee->start_date->format('d M Y') : null;
                $totalHours = $edf('contracted_hours') ?: $edf('average_hours');
            @endphp

            <div class="emp-grid">
                {{-- ===== LEFT COLUMN ===== --}}
                <div>
                    {{-- Employment information --}}
                    <div class="emp-card">
                        <div class="emp-card-head" onclick="empToggle(this)">
                            <div>
                                <div class="emp-card-title">Employment information</div>
                                <div class="emp-card-sub">Hours of work and employment start date</div>
                            </div>
                            <svg class="emp-chev" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </div>
                        <div class="emp-card-body">
                            <div class="emp-keybox" style="display:flex;align-items:center;justify-content:space-between;">
                                <span class="emp-keybox-title">Key details</span>
                                <div style="display:flex;gap:16px;">
                                    <a class="emp-act-link" href="#" onclick="openEditContractModal(); return false;" title="Edit contract">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                                        Edit
                                    </a>
                                </div>
                            </div>
                            <div class="emp-row"><span class="emp-k">Employment type</span><span class="emp-v {{ ($edf('employee_type') ?: $employee->employment_basis) ? '' : 'muted' }}">{{ $edf('employee_type') ?: ($employee->employment_basis ?: 'Not set') }}</span></div>
                            <div class="emp-row"><span class="emp-k">Entitlement unit</span><span class="emp-v {{ $edf('leave_unit') ? '' : 'muted' }}">{{ $edf('leave_unit') ?: 'Not set' }}</span></div>
                            <div class="emp-row"><span class="emp-k">Contract start date</span><span class="emp-v {{ $startFmt ? '' : 'muted' }}">{{ $startFmt ?: 'Not set' }}</span></div>
                            <div class="emp-row"><span class="emp-k">Total hours</span><span class="emp-v {{ $totalHours ? '' : 'muted' }}">{{ $totalHours ? $totalHours . ' hrs' : 'Not set' }}</span></div>
                            <div class="emp-row">
                                <span class="emp-k">Hours of work</span>
                                @if($edf('working_pattern'))
                                    <button type="button" class="emp-v emp-v-link" onclick="openHoursModal()">{{ $edf('working_pattern') }}</button>
                                @else
                                    <span class="emp-v muted">Not set</span>
                                @endif
                            </div>

                            <div class="emp-section-title">Award, agreement and classification</div>
                            <div class="emp-row"><span class="emp-k">Award notes</span><span class="emp-v {{ $g('award_notes') ? '' : 'muted' }}">{{ $g('award_notes') ?: 'Not set' }}</span></div>
                            <div class="emp-row"><span class="emp-k">Classification notes</span><span class="emp-v {{ $g('classification_notes') ? '' : 'muted' }}">{{ $g('classification_notes') ?: 'Not set' }}</span></div>

                            <div class="emp-section-title">Place of work</div>
                            <div class="emp-row"><span class="emp-k">Working location</span><span class="emp-v {{ $workLoc ? '' : 'muted' }}">{{ $workLoc ?: 'Not set' }}</span></div>
                            <div class="emp-row"><span class="emp-k">Public holidays for</span><span class="emp-v {{ $edf('jurisdiction') ? '' : 'muted' }}">{{ $edf('jurisdiction') ?: 'Not set' }}</span></div>
                            <div class="emp-row"><span class="emp-k">Leave taken in</span><span class="emp-v">{{ $edf('leave_unit') ?: 'Days' }}</span></div>
                        </div>
                    </div>

                    {{-- Employment summary --}}
                    <div class="emp-card">
                        <div class="emp-card-head" onclick="empToggle(this)">
                            <div>
                                <div class="emp-card-title">Employment summary</div>
                                <div class="emp-card-sub">Start date and length of service</div>
                            </div>
                            <svg class="emp-chev" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </div>
                        <div class="emp-card-body">
                            <div class="emp-row"><span class="emp-k">Employee start date</span><span class="emp-v {{ $startFmt ? '' : 'muted' }}">{{ $startFmt ?: 'Not set' }}</span></div>
                            <div class="emp-row"><span class="emp-k">Length of service</span><span class="emp-v {{ $los ? '' : 'muted' }}">{{ $los ?: 'Not set' }}</span></div>
                        </div>
                    </div>
                </div>

                {{-- ===== RIGHT COLUMN ===== --}}
                <div>
                    {{-- Role information --}}
                    <div class="emp-card">
                        <div class="emp-card-head" onclick="empToggle(this)">
                            <div>
                                <div class="emp-card-title">Role information</div>
                                <div class="emp-card-sub">Job title, probation and notice period</div>
                            </div>
                            <svg class="emp-chev" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </div>
                        <div class="emp-card-body">
                            <div class="emp-act-row">
                                <a class="emp-act-link" href="#" onclick="openSimpleEditModal('role'); return false;" title="Edit role information">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                                    Edit
                                </a>
                            </div>
                            <div class="emp-row"><span class="emp-k">Job title</span><span class="emp-v {{ $employee->position_title ? '' : 'muted' }}">{{ $employee->position_title ?: 'Not set' }}</span></div>
                            <div class="emp-row"><span class="emp-k">Contract type</span><span class="emp-v {{ ($employee->employment_basis ?: $edf('employee_type')) ? '' : 'muted' }}">{{ $employee->employment_basis ?: ($edf('employee_type') ?: 'Not set') }}</span></div>
                            <div class="emp-row"><span class="emp-k">Division</span><span class="emp-v {{ $employee->division ? '' : 'muted' }}">{{ $employee->division?->name ?? 'No division' }}</span></div>
                            <div class="emp-row"><span class="emp-k">Sub-division</span><span class="emp-v {{ $employee->subDivision ? '' : 'muted' }}">{{ $employee->subDivision?->name ?? 'Not set' }}</span></div>
                            <div class="emp-row"><span class="emp-k">Position</span><span class="emp-v {{ $employee->position ? '' : 'muted' }}">{{ $employee->position?->name ?? 'Not set' }}</span></div>
                            <div class="emp-row"><span class="emp-k">Reports to</span><span class="emp-v">Vida Gholami - Director</span></div>
                            @php $probReq = $employee->probation_required; @endphp
                            <div class="emp-row"><span class="emp-k">Probation required</span><span class="emp-v {{ $probReq ? '' : 'muted' }}">{{ $probReq ? 'Yes' : 'No' }}</span></div>
                            <div class="emp-row"><span class="emp-k">Probation end date</span><span class="emp-v {{ $employee->probation_end_date ? '' : 'muted' }}">{{ $employee->probation_end_date ? $employee->probation_end_date->format('d M Y') : 'Not set' }}</span></div>
                            <div class="emp-row"><span class="emp-k">Notice required during probation</span><span class="emp-v {{ $employee->notice_during_probation ? '' : 'muted' }}">{{ $employee->notice_during_probation ?: 'Not set' }}</span></div>
                            <div class="emp-row"><span class="emp-k">General notice period</span><span class="emp-v {{ $employee->notice_period ? '' : 'muted' }}">{{ $employee->notice_period ?: 'Not set' }}</span></div>
                        </div>
                    </div>

                    {{-- Pay details (collapsed) --}}
                    <div class="emp-card collapsed">
                        <div class="emp-card-head" onclick="empToggle(this)">
                            <div>
                                <div class="emp-card-title">Pay details</div>
                                <div class="emp-card-sub">Rate of pay and related details</div>
                            </div>
                            <svg class="emp-chev" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </div>
                        <div class="emp-card-body">
                            <div class="emp-act-row">
                                <a class="emp-act-link" href="#" onclick="openSimpleEditModal('pay'); return false;" title="Edit salary">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                                    Edit
                                </a>
                            </div>
                            <div class="emp-subhead">Salary</div>
                            <div class="emp-row"><span class="emp-k">Amount/rate</span><span class="emp-v {{ $pgf('salary') ? '' : 'muted' }}">{{ $pgf('salary') ?: 'Not specified' }}</span></div>
                            <div class="emp-row"><span class="emp-k">Hourly rate</span><span class="emp-v {{ $pgf('pay_rate') ? '' : 'muted' }}">{{ $pgf('pay_rate') ?: 'Not specified' }}</span></div>
                            <div class="emp-row"><span class="emp-k">Payment frequency</span><span class="emp-v {{ $pgf('pay_frequency') ? '' : 'muted' }}">{{ $pgf('pay_frequency') ?: 'Not specified' }}</span></div>
                            <div class="emp-row"><span class="emp-k">Effective date</span><span class="emp-v {{ $edf('effective_from') ? '' : 'muted' }}">{{ $edf('effective_from') ?: 'Not specified' }}</span></div>
                        </div>
                    </div>

                    {{-- Payroll information (collapsed) --}}
                    <div class="emp-card collapsed" id="payrollCard">
                        <div class="emp-card-head" onclick="empToggle(this)">
                            <div>
                                <div class="emp-card-title">Payroll information</div>
                                <div class="emp-card-sub">Payroll number and pension details</div>
                            </div>
                            <svg class="emp-chev" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </div>
                        <div class="emp-card-body">
                            <div class="emp-form-row">
                                <label>Payroll number</label>
                                <input type="text" class="emp-input" id="payroll_payroll_no" value="{{ $pgf('payroll_no') }}">
                            </div>

                            @php
                                $workCountry = $pgf('work_country') ?: 'Indonesia';
                                // Auto-detect from saved data if work_country is empty
                                if (!$pgf('work_country')) {
                                    if ($pgf('super_fund_name') || $pgf('super_member_no')) {
                                        $workCountry = 'Australia';
                                    }
                                }
                            @endphp

                            {{-- Payroll country selector --}}
                            <div class="emp-form-row">
                                <label>Payroll country</label>
                                <select class="emp-input" id="payroll_country" onchange="switchPayrollCountry(this.value)" style="max-width:300px;">
                                    <option value="Indonesia" {{ $workCountry === 'Indonesia' ? 'selected' : '' }}>Indonesia (BPJS)</option>
                                    <option value="Australia" {{ $workCountry === 'Australia' ? 'selected' : '' }}>Australia (Superannuation)</option>
                                    <option value="Other" {{ ($workCountry !== 'Indonesia' && $workCountry !== 'Australia') ? 'selected' : '' }}>Other country (Superannuation)</option>
                                </select>
                            </div>

                            {{-- BPJS Section (Indonesia) --}}
                            <div id="section_bpjs" style="{{ $workCountry !== 'Indonesia' ? 'display:none;' : '' }}">
                                <div class="emp-subhead">BPJS Ketenagakerjaan</div>
                                <div class="emp-form-row">
                                    <label>Membership number</label>
                                    <input type="text" class="emp-input" id="payroll_bpjs_ketenagakerjaan_no" placeholder="BPJS Ketenagakerjaan number" value="{{ $pgf('bpjs_ketenagakerjaan_no') }}">
                                </div>
                                <div class="emp-form-row">
                                    <label>Registration date</label>
                                    <input type="date" class="emp-input" id="payroll_bpjs_ketenagakerjaan_start" value="{{ $pgf('bpjs_ketenagakerjaan_start') ? \Carbon\Carbon::parse($pgf('bpjs_ketenagakerjaan_start'))->format('Y-m-d') : '' }}">
                                </div>
                                <div class="emp-form-row">
                                    <label>Active status</label>
                                    <div class="emp-yn">
                                        <label class="emp-yn-opt {{ $pgf('bpjs_ketenagakerjaan_active') !== false ? 'sel' : '' }}" onclick="empPickYN(this)"><input type="radio" name="bpjs_tk_active" value="1" {{ $pgf('bpjs_ketenagakerjaan_active') !== false ? 'checked' : '' }}> Yes</label>
                                        <label class="emp-yn-opt {{ $pgf('bpjs_ketenagakerjaan_active') === false ? 'sel' : '' }}" onclick="empPickYN(this)"><input type="radio" name="bpjs_tk_active" value="0" {{ $pgf('bpjs_ketenagakerjaan_active') === false ? 'checked' : '' }}> No</label>
                                    </div>
                                </div>
                                <div class="emp-subhead">BPJS Kesehatan</div>
                                <div class="emp-form-row">
                                    <label>Membership number</label>
                                    <input type="text" class="emp-input" id="payroll_bpjs_kesehatan_no" placeholder="BPJS Kesehatan number" value="{{ $pgf('bpjs_kesehatan_no') }}">
                                </div>
                                <div class="emp-form-row">
                                    <label>Class</label>
                                    <select class="emp-input" id="payroll_bpjs_kesehatan_class">
                                        <option value="">Select class</option>
                                        <option value="1" {{ $pgf('bpjs_kesehatan_class') == 1 ? 'selected' : '' }}>Class 1</option>
                                        <option value="2" {{ $pgf('bpjs_kesehatan_class') == 2 ? 'selected' : '' }}>Class 2</option>
                                        <option value="3" {{ $pgf('bpjs_kesehatan_class') == 3 ? 'selected' : '' }}>Class 3</option>
                                    </select>
                                </div>
                                <div class="emp-form-row">
                                    <label>Number of dependants</label>
                                    <div>
                                        <input type="number" class="emp-input" id="payroll_bpjs_kesehatan_dependants" min="0" max="10" value="{{ $pgf('bpjs_kesehatan_dependants') ?? 0 }}">
                                        <div class="emp-help">Number of covered family members</div>
                                    </div>
                                </div>
                                <div class="emp-form-row">
                                    <label>Registration date</label>
                                    <input type="date" class="emp-input" id="payroll_bpjs_kesehatan_start" value="{{ $pgf('bpjs_kesehatan_start') ? \Carbon\Carbon::parse($pgf('bpjs_kesehatan_start'))->format('Y-m-d') : '' }}">
                                </div>
                                <div class="emp-form-row">
                                    <label>Active status</label>
                                    <div class="emp-yn">
                                        <label class="emp-yn-opt {{ $pgf('bpjs_kesehatan_active') !== false ? 'sel' : '' }}" onclick="empPickYN(this)"><input type="radio" name="bpjs_ks_active" value="1" {{ $pgf('bpjs_kesehatan_active') !== false ? 'checked' : '' }}> Yes</label>
                                        <label class="emp-yn-opt {{ $pgf('bpjs_kesehatan_active') === false ? 'sel' : '' }}" onclick="empPickYN(this)"><input type="radio" name="bpjs_ks_active" value="0" {{ $pgf('bpjs_kesehatan_active') === false ? 'checked' : '' }}> No</label>
                                    </div>
                                </div>
                            </div>

                            {{-- Superannuation Section (Australia) --}}
                            <div id="section_super" style="{{ $workCountry !== 'Australia' ? 'display:none;' : '' }}">
                                <div class="emp-subhead">Superannuation</div>
                                <div class="emp-form-row">
                                    <label>Standard choice form completed?</label>
                                    <div class="emp-yn">
                                        <label class="emp-yn-opt" onclick="empPickYN(this)"><input type="radio" name="super_choice"> Yes</label>
                                        <label class="emp-yn-opt sel" onclick="empPickYN(this)"><input type="radio" name="super_choice" checked> No</label>
                                    </div>
                                </div>
                                <div class="emp-form-row">
                                    <label>Is a self managed super fund?</label>
                                    <div class="emp-yn">
                                        <label class="emp-yn-opt" onclick="empPickYN(this)"><input type="radio" name="super_smsf"> Yes</label>
                                        <label class="emp-yn-opt sel" onclick="empPickYN(this)"><input type="radio" name="super_smsf" checked> No</label>
                                    </div>
                                </div>
                                <div class="emp-form-row">
                                    <label>Super fund name</label>
                                    <input type="text" class="emp-input" id="payroll_super_fund_name" value="{{ $pgf('super_fund_name') }}">
                                </div>
                                <div class="emp-form-row">
                                    <label>Super fund ABN
                                        <svg class="emp-info" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                                    </label>
                                    <input type="text" class="emp-input" id="payroll_super_fund_abn" value="{{ $pgf('super_fund_abn') }}">
                                </div>
                                <div class="emp-form-row">
                                    <label>Unique Superannuation Identifier (USI)
                                        <svg class="emp-info" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                                    </label>
                                    <input type="text" class="emp-input" id="payroll_super_usi" value="{{ $pgf('super_usi') }}">
                                </div>
                                <div class="emp-form-row">
                                    <label>Member number</label>
                                    <input type="text" class="emp-input" id="payroll_super_member_no" value="{{ $pgf('super_member_no') }}">
                                </div>
                            </div>

                            <div class="emp-save-row">
                                <button type="button" class="emp-btn-save" id="payrollSaveBtn" onclick="savePayrollInfo()">Save</button>
                                <button type="button" class="emp-btn-cancel" onclick="resetPayrollInfo()">Cancel</button>
                            </div>
                        </div>
                    </div>

                    {{-- Bank details (collapsed) --}}
                    <div class="emp-card collapsed">
                        <div class="emp-card-head" onclick="empToggle(this)">
                            <div>
                                <div class="emp-card-title">Bank details</div>
                                <div class="emp-card-sub">Employee bank details</div>
                            </div>
                            <svg class="emp-chev" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </div>
                        <div class="emp-card-body">
                            <div class="emp-form-row">
                                <label>Name on account</label>
                                <div>
                                    <input type="text" class="emp-input" id="bank_bank_acc_name" placeholder="Account Name" value="{{ $pgf('bank_acc_name') }}">
                                    <div class="emp-help">Account name. Max 60 chars</div>
                                </div>
                            </div>
                            <div class="emp-form-row">
                                <label>Name of bank</label>
                                <div>
                                    <input type="text" class="emp-input" id="bank_bank_name" placeholder="Bank Name" value="{{ $pgf('bank_name') }}">
                                    <div class="emp-help">Bank name. Max 60 chars</div>
                                </div>
                            </div>
                            <div class="emp-form-row">
                                <label>Bank branch</label>
                                <div>
                                    <input type="text" class="emp-input" id="bank_bank_branch" placeholder="Bank branch" value="{{ $pgf('bank_branch') }}">
                                    <div class="emp-help">Bank branch location</div>
                                </div>
                            </div>
                            <div class="emp-form-row">
                                <label>Account number</label>
                                <div>
                                    <input type="text" class="emp-input" id="bank_bank_acc_no" placeholder="12345678" value="{{ $pgf('bank_acc_no') }}">
                                    <div class="emp-help">5-20 digit number</div>
                                </div>
                            </div>
                            <div class="emp-form-row">
                                <label>Bank BSB</label>
                                <div>
                                    <input type="text" class="emp-input" id="bank_bank_bsb" placeholder="000-000" value="{{ $pgf('bank_bsb') }}">
                                    <div class="emp-help">E.g. 000-000</div>
                                </div>
                            </div>
                            <div class="emp-save-row">
                                <button type="button" class="emp-btn-save" id="bankSaveBtn" onclick="saveBankDetails()">Save bank details</button>
                                <button type="button" class="emp-btn-cancel" onclick="resetBankDetails()">Cancel</button>
                            </div>
                        </div>
                    </div>

                    {{-- Sensitive information (collapsed) --}}
                    @php
                        $countryList = ["Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cabo Verde","Cambodia","Cameroon","Canada","Central African Republic","Chad","Chile","China","Colombia","Comoros","Congo (Brazzaville)","Congo (Kinshasa)","Costa Rica","Croatia","Cuba","Cyprus","Czechia","Denmark","Djibouti","Dominica","Dominican Republic","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Eswatini","Ethiopia","Fiji","Finland","France","Gabon","Gambia","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Montenegro","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepal","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","North Korea","North Macedonia","Norway","Oman","Pakistan","Palau","Palestine","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent and the Grenadines","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","South Korea","South Sudan","Spain","Sri Lanka","Sudan","Suriname","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Timor-Leste","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe"];
                    @endphp
                    <div class="emp-card collapsed" id="sensitiveCard">
                        <div class="emp-card-head" onclick="empToggle(this)">
                            <div>
                                <div class="emp-card-title">Sensitive information</div>
                                <div class="emp-card-sub">Tax, work rights and record checks</div>
                            </div>
                            <svg class="emp-chev" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </div>
                        <div class="emp-card-body">
                            <div class="emp-subhead">Tax</div>
                            <div class="emp-form-row">
                                <label>Tax File Number (TFN)</label>
                                <input type="text" class="emp-input" id="sensitive_tfn" placeholder="Tax File Number (TFN)" value="{{ $pgf('tfn') }}">
                            </div>
                            <div class="emp-form-row">
                                <label>Australian resident for tax purposes?</label>
                                <div class="emp-yn">
                                    <label class="emp-yn-opt" onclick="empPickYN(this)"><input type="radio" name="tax_resident"> Yes</label>
                                    <label class="emp-yn-opt sel" onclick="empPickYN(this)"><input type="radio" name="tax_resident" checked> No</label>
                                </div>
                            </div>
                            <div class="emp-form-row">
                                <label>A Working Holiday Maker?</label>
                                <div class="emp-yn">
                                    <label class="emp-yn-opt" onclick="empPickYN(this)"><input type="radio" name="tax_whm"> Yes</label>
                                    <label class="emp-yn-opt sel" onclick="empPickYN(this)"><input type="radio" name="tax_whm" checked> No</label>
                                </div>
                            </div>
                            <div class="emp-form-row">
                                <label>Claiming senior &amp; pensioners tax offset?</label>
                                <div class="emp-yn">
                                    <label class="emp-yn-opt" onclick="empPickYN(this)"><input type="radio" name="tax_senior"> Yes</label>
                                    <label class="emp-yn-opt sel" onclick="empPickYN(this)"><input type="radio" name="tax_senior" checked> No</label>
                                </div>
                            </div>

                            <div class="emp-subhead">Passport</div>
                            <div class="emp-form-row">
                                <label>Passport number</label>
                                <input type="text" class="emp-input" id="sensitive_passport_no" placeholder="Passport number" value="{{ $passport?->document_no }}">
                            </div>
                            <div class="emp-form-row">
                                <label>Country of issue</label>
                                <select class="emp-input" id="sensitive_passport_country">
                                    <option value="">Country of issue</option>
                                    @foreach($countryList as $country)
                                        <option value="{{ $country }}" @selected($passport?->country === $country)>{{ $country }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="emp-form-row">
                                <label>Date of expiry</label>
                                <input type="date" class="emp-input" id="sensitive_passport_expiry" value="{{ $passport?->expiry_date?->format('Y-m-d') }}">
                            </div>

                            <div class="emp-subhead">Driving licence</div>
                            <div class="emp-form-row">
                                <label>Licence number</label>
                                <input type="text" class="emp-input" id="sensitive_licence_no" placeholder="Licence number" value="{{ $licence?->document_no }}">
                            </div>
                            <div class="emp-form-row">
                                <label>Country of issue</label>
                                <select class="emp-input" id="sensitive_licence_country">
                                    <option value="">Country of issue</option>
                                    @foreach($countryList as $country)
                                        <option value="{{ $country }}" @selected($licence?->country === $country)>{{ $country }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="emp-form-row">
                                <label>Licence class</label>
                                <input type="text" class="emp-input" id="sensitive_licence_class" placeholder="Licence class" value="{{ $licence?->class }}">
                            </div>
                            <div class="emp-form-row">
                                <label>Date of expiry</label>
                                <input type="date" class="emp-input" id="sensitive_licence_expiry" value="{{ $licence?->expiry_date?->format('Y-m-d') }}">
                            </div>

                            <div class="emp-subhead">Visa</div>
                            <div class="emp-form-row">
                                <label>Visa number</label>
                                <input type="text" class="emp-input" id="sensitive_visa_no" placeholder="Visa number" value="{{ $visa?->document_no }}">
                            </div>
                            <div class="emp-form-row">
                                <label>Visa expiry date</label>
                                <input type="date" class="emp-input" id="sensitive_visa_expiry" value="{{ $visa?->expiry_date?->format('Y-m-d') }}">
                            </div>

                            <div class="emp-subhead">National police check</div>
                            <div class="emp-form-row">
                                <label>National police check conducted</label>
                                <input type="checkbox" id="sensitive_police_check" style="width:20px;height:20px;accent-color:#2e7d5e;margin-top:8px;" @checked($pgf('police_check_conducted'))>
                            </div>

                            <div class="emp-save-row">
                                <button type="button" class="emp-btn-save" id="sensitiveSaveBtn" onclick="saveSensitiveInfo()">Save</button>
                                <button type="button" class="emp-btn-cancel" onclick="reloadSameTab()">Cancel</button>
                            </div>
                        </div>
                    </div>

                    {{-- External employee reference (collapsed) --}}
                    <div class="emp-card collapsed">
                        <div class="emp-card-head" onclick="empToggle(this)">
                            <div>
                                <div class="emp-card-title">External employee reference</div>
                                <div class="emp-card-sub">Set a custom reference to appear in reports</div>
                            </div>
                            <svg class="emp-chev" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </div>
                        <div class="emp-card-body">
                            <div class="emp-form-row">
                                <label>Reference</label>
                                <input type="text" class="emp-input" value="{{ $employee->id_number }}">
                            </div>
                            <div class="emp-save-row">
                                <button type="button" class="emp-btn-save" disabled title="Saving coming soon">Save</button>
                                <button type="button" class="emp-btn-cancel" disabled>Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== PERSONAL TAB ===== --}}
        <div class="bhr-tab-pane" id="tab-personal">
            @php
                $persAddr = collect([$employee->address_1, $employee->address_2, $employee->city, $employee->territory, $employee->postcode])->filter()->implode(', ');
            @endphp

            {{-- Contact information --}}
            <div class="emp-card">
                <div class="emp-card-head" onclick="empToggle(this)">
                    <div><div class="emp-card-title">Contact information</div></div>
                    <svg class="emp-chev" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                </div>
                <div class="emp-card-body">
                    <div class="emp-act-row">
                        <a class="emp-act-link" href="#" onclick="openSimpleEditModal('contact'); return false;" title="Edit contact information">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                            Edit
                        </a>
                    </div>
                    <div class="emp-pairs">
                        <div><div class="emp-pair-label">Account email</div><div class="emp-pair-val {{ $employee->email ? '' : 'muted' }}">{{ $employee->email ?: 'Not specified' }}</div></div>
                        <div><div class="emp-pair-label">Personal email</div><div class="emp-pair-val {{ $employee->personal_email ? '' : 'muted' }}">{{ $employee->personal_email ?: 'Not specified' }}</div></div>
                        <div><div class="emp-pair-label">Home phone</div><div class="emp-pair-val {{ $employee->home_phone ? '' : 'muted' }}">{{ $employee->home_phone ?: 'Not specified' }}</div></div>
                        <div><div class="emp-pair-label">Mobile phone</div><div class="emp-pair-val {{ $employee->phone ? '' : 'muted' }}">{{ $employee->phone ?: 'Not specified' }}</div></div>
                        <div><div class="emp-pair-label">Work phone</div><div class="emp-pair-val {{ $employee->work_phone ? '' : 'muted' }}">{{ $employee->work_phone ?: 'Not specified' }}</div></div>
                        <div><div class="emp-pair-label">Work extension</div><div class="emp-pair-val {{ $employee->work_extension ? '' : 'muted' }}">{{ $employee->work_extension ?: 'Not specified' }}</div></div>
                    </div>
                </div>
            </div>

            {{-- System access --}}
            @php
                $hasAccount = !empty($employee->user_id);
                $activationToken = $employee->extra_details['activation_token'] ?? null;
                $activationExpiresAt = $employee->extra_details['activation_expires_at'] ?? null;
                $inviteExpired = $activationExpiresAt ? \Carbon\Carbon::parse($activationExpiresAt)->isPast() : true;
                $hasPendingInvite = !$hasAccount && $activationToken && !$inviteExpired;
            @endphp
            <div class="emp-card">
                <div class="emp-card-head" onclick="empToggle(this)">
                    <div><div class="emp-card-title">System access</div></div>
                    <svg class="emp-chev" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                </div>
                <div class="emp-card-body">
                    <div class="emp-pairs">
                        <div>
                            <div class="emp-pair-label">Account status</div>
                            <div class="emp-pair-val">
                                @if($hasAccount)
                                    <span style="color:#15803d;font-weight:600;">Active — has system login</span>
                                @elseif($hasPendingInvite)
                                    <span style="color:#92400e;font-weight:600;">Invitation sent — awaiting activation</span>
                                @else
                                    <span class="muted">Not registered</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if(!$hasAccount)
                        @if(!$employee->email)
                            <p style="margin:12px 0 0;font-size:0.85rem;color:#94a3b8;">Add an account email above before inviting this employee to the system.</p>
                        @else
                            <button type="button" class="emc-btn-primary" id="inviteSystemBtn" style="margin-top:12px;" onclick="inviteEmployeeToSystem()">{{ $hasPendingInvite ? 'Resend invite' : 'Invite to system' }}</button>
                        @endif
                    @endif
                </div>
            </div>

            {{-- Personal information --}}
            <div class="emp-card">
                <div class="emp-card-head" onclick="empToggle(this)">
                    <div><div class="emp-card-title">Personal information</div></div>
                    <svg class="emp-chev" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                </div>
                <div class="emp-card-body">
                    <div class="emp-act-row">
                        <a class="emp-act-link" href="#" onclick="openSimpleEditModal('personal'); return false;" title="Edit personal information">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                            Edit
                        </a>
                    </div>
                    <div class="emp-pairs">
                        <div><div class="emp-pair-label">Title</div><div class="emp-pair-val {{ $employee->title ? '' : 'muted' }}">{{ $employee->title ?: 'Not specified' }}</div></div>
                        <div><div class="emp-pair-label">First name</div><div class="emp-pair-val {{ $employee->first_name ? '' : 'muted' }}">{{ $employee->first_name ?: 'Not specified' }}</div></div>
                        <div><div class="emp-pair-label">Middle name</div><div class="emp-pair-val {{ $employee->middle_name ? '' : 'muted' }}">{{ $employee->middle_name ?: 'Not specified' }}</div></div>
                        <div><div class="emp-pair-label">Last name</div><div class="emp-pair-val {{ $employee->last_name ? '' : 'muted' }}">{{ $employee->last_name ?: 'Not specified' }}</div></div>
                        <div><div class="emp-pair-label">Date of birth</div><div class="emp-pair-val {{ $employee->birth_info ? '' : 'muted' }}">{{ $employee->birth_info ?: 'Not specified' }}</div></div>
                        <div><div class="emp-pair-label">Gender</div><div class="emp-pair-val {{ $employee->gender ? '' : 'muted' }}">{{ $employee->gender ? ucfirst($employee->gender) : 'Not specified' }}</div></div>
                        <div style="grid-column:1/-1;"><div class="emp-pair-label">Address</div><div class="emp-pair-val {{ ($employee->address ?: $persAddr) ? '' : 'muted' }}">{{ $employee->address ?: ($persAddr ?: 'Not specified') }}</div></div>
                    </div>
                </div>
            </div>

            {{-- Medical information (COVID removed; general medical fields) --}}
            <div class="emp-card">
                <div class="emp-card-head" onclick="empToggle(this)">
                    <div><div class="emp-card-title">Medical information</div><div class="emp-card-sub">Health details relevant to this employee</div></div>
                    <svg class="emp-chev" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                </div>
                <div class="emp-card-body">
                    <div class="emp-form-row">
                        <label>Medical conditions</label>
                        <textarea class="emp-input" id="medical_conditions" style="max-width:600px;min-height:80px;" placeholder="Any ongoing medical conditions">{{ $employee->medical_conditions }}</textarea>
                    </div>
                    <div class="emp-form-row">
                        <label>Allergies</label>
                        <input type="text" class="emp-input" id="medical_allergies" placeholder="e.g. Penicillin, peanuts" value="{{ $employee->allergies }}">
                    </div>
                    <div class="emp-form-row">
                        <label>Blood type</label>
                        <input type="text" class="emp-input" id="medical_blood_type" style="max-width:200px;" placeholder="e.g. O+" value="{{ $employee->blood_type }}">
                    </div>
                    <div class="emp-form-row">
                        <label>Add notes</label>
                        <div style="max-width:600px;">
                            <textarea class="emp-input" id="medical_notes" style="min-height:90px;" maxlength="1000" placeholder="Notes regarding medical information">{{ $employee->medical_notes }}</textarea>
                            <div style="display:inline-flex;align-items:center;gap:8px;margin-top:10px;background:#eef6f2;border-radius:8px;padding:8px 14px;font-size:0.85rem;color:#1b4332;">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="#2e7d5e" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                                Notes are visible to employee
                            </div>
                        </div>
                    </div>
                    <div class="emp-save-row">
                        <button type="button" class="emp-btn-save" id="medicalSaveBtn" onclick="saveMedicalInfo()">Save</button>
                        <button type="button" class="emp-btn-cancel" onclick="reloadSameTab()">Cancel</button>
                    </div>
                </div>
            </div>

        </div>

        {{-- ===== EMERGENCIES TAB ===== --}}
        <div class="bhr-tab-pane" id="tab-emergencies">
            @php
                $contacts = $employee->emergencyContacts->sortByDesc('is_primary')->values();
            @endphp

            <button type="button" class="emp-add-btn" title="Add new emergency contact" onclick="openAddContactModal()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add new contact
            </button>

            @forelse($contacts as $c)
                @php
                    $cName = trim($c->first_name . ' ' . $c->last_name);
                    $cName = $cName !== '' ? $cName : 'Emergency contact';
                @endphp
                <div class="emp-card">
                    <div class="emp-card-head" onclick="empToggle(this)">
                        <div style="display:flex;align-items:center;gap:8px;">
                            <span class="emp-card-title">{{ $cName }}</span>
                            @if($c->is_primary)<span class="emp-badge">Primary contact</span>@endif
                        </div>
                        <div style="display:flex;align-items:center;gap:18px;">
                            <a class="emp-act-link" href="#" onclick="event.stopPropagation(); openEditContactModal({{ $c->id }}, {{ $c->toJson() }}); return false;" title="Edit contact">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                                Edit
                            </a>
                            <a class="emp-act-link" href="#" style="color:#e74c5e;" onclick="event.stopPropagation(); deleteEmergencyContact({{ $c->id }}, '{{ addslashes($cName) }}'); return false;" title="Delete contact">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                Delete
                            </a>
                            <svg class="emp-chev" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                        </div>
                    </div>
                    <div class="emp-card-body">
                        <div class="emp-pairs">
                            <div><div class="emp-pair-label">First name</div><div class="emp-pair-val {{ $c->first_name ? '' : 'muted' }}">{{ $c->first_name ?: 'Not specified' }}</div></div>
                            <div><div class="emp-pair-label">Last name</div><div class="emp-pair-val {{ $c->last_name ? '' : 'muted' }}">{{ $c->last_name ?: 'Not specified' }}</div></div>
                            <div><div class="emp-pair-label">Relationship</div><div class="emp-pair-val {{ $c->relationship ? '' : 'muted' }}">{{ $c->relationship ?: 'Not specified' }}</div></div>
                            <div><div class="emp-pair-label">Home phone</div><div class="emp-pair-val {{ $c->home_phone ? '' : 'muted' }}">{{ $c->home_phone ?: 'Not specified' }}</div></div>
                            <div><div class="emp-pair-label">Mobile phone</div><div class="emp-pair-val {{ $c->mobile_phone ? '' : 'muted' }}">{{ $c->mobile_phone ?: 'Not specified' }}</div></div>
                            <div><div class="emp-pair-label">Work phone</div><div class="emp-pair-val {{ $c->work_phone ? '' : 'muted' }}">{{ $c->work_phone ?: 'Not specified' }}</div></div>
                            <div><div class="emp-pair-label">Country</div><div class="emp-pair-val {{ $c->country ? '' : 'muted' }}">{{ $c->country ?: 'Not specified' }}</div></div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="emp-card"><div style="padding:8px;"><div class="emp-empty">No emergency contacts have been added yet.</div></div></div>
            @endforelse
        </div>

        {{-- ===== DOCUMENTS TAB ===== --}}
        <div class="bhr-tab-pane" id="tab-documents">

            {{-- Required Documents Checklist --}}
            @if($requiredFolders->isNotEmpty())
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px 20px;margin-bottom:20px;">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
                    <h3 style="font-size:0.95rem;font-weight:700;color:#1b4332;margin:0;">Required Documents</h3>
                    @php $uploadedCount = $requiredFolders->where('has_file', true)->count(); $totalRequired = $requiredFolders->count(); @endphp
                    <span style="font-size:0.78rem;padding:2px 10px;border-radius:20px;font-weight:600;background:{{ $uploadedCount === $totalRequired ? '#dcfce7' : '#fef3c7' }};color:{{ $uploadedCount === $totalRequired ? '#15803d' : '#92400e' }};">
                        {{ $uploadedCount }}/{{ $totalRequired }} uploaded
                    </span>
                </div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @foreach($requiredFolders as $reqFolder)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-radius:8px;background:{{ $reqFolder->has_file ? '#f0fdf4' : '#fafafa' }};border:1px solid {{ $reqFolder->has_file ? '#bbf7d0' : '#e5e7eb' }};">
                        <div style="display:flex;align-items:center;gap:10px;">
                            @if($reqFolder->has_file)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="#16a34a" style="width:18px;height:18px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#d97706" style="width:18px;height:18px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                            @endif
                            <div>
                                <div style="font-size:0.88rem;font-weight:600;color:#1b4332;">{{ $reqFolder->name }}</div>
                                <div style="font-size:0.77rem;color:#6b7280;">{{ $reqFolder->has_file ? 'Document uploaded' : 'Upload required' }}</div>
                            </div>
                        </div>
                        @if($reqFolder->has_file)
                            <a href="/admin/linkers-hub/employees/{{ $employee->id }}/files/{{ $reqFolder->file_id }}/view" target="_blank"
                               style="font-size:0.8rem;font-weight:600;color:#2e7d5e;text-decoration:none;padding:4px 12px;border:1px solid #2e7d5e;border-radius:6px;">
                                View
                            </a>
                        @else
                            <a href="#" onclick="triggerRequiredUpload('{{ $reqFolder->type }}'); return false;"
                               style="font-size:0.8rem;font-weight:600;color:#2e7d5e;text-decoration:none;padding:4px 12px;border:1px solid #2e7d5e;border-radius:6px;">
                                Upload
                            </a>
                            <input type="file" id="reqFileInput_{{ $reqFolder->type }}" style="display:none;"
                                   onchange="docUploadRequiredType('{{ $reqFolder->type }}', this.files)">
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Breadcrumb --}}
            <div class="doc-bread" id="docBreadcrumb">
                <a href="#" onclick="docNavigate(null); return false;">All folders</a>
            </div>

            {{-- Toolbar --}}
            <div class="doc-toolbar">
                <div>
                    <div class="doc-search">
                        <input type="text" id="docSearchInput" placeholder="Search My documents..." oninput="docHandleSearchInput()" onkeydown="if(event.key==='Enter') docSearch();">
                        <button type="button" class="doc-btn-grey" id="docSearchBtn" onclick="docSearch()">Search</button>
                    </div>
                    <div id="docBulkBar" style="display:none; margin-top:10px;">
                        <button type="button" class="doc-btn-green" onclick="docBulkDownload()">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                            Download selected
                        </button>
                    </div>
                </div>
                <div class="doc-right">
                    <div class="doc-perpage">View
                        <select id="docPerPage" onchange="docReload()">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        per page
                    </div>
                    <div class="doc-actions">
                        <button type="button" class="doc-btn-gold" id="docToolbarUploadBtn" onclick="docToggleUpload()">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                            Upload
                        </button>
                        <button type="button" class="doc-btn-green" onclick="docOpenFolder()">New folder</button>
                    </div>
                </div>
            </div>

            {{-- Upload panel (toggled) --}}
            <div class="doc-upload-panel" id="docUploadPanel">
                <h3 style="font-size:1.1rem;font-weight:700;color:#1b4332;margin:0 0 6px;">Upload documents</h3>
                <p style="color:#666;font-size:0.9rem;margin:0 0 16px;">Choose up to <strong>100</strong> documents you'd like to upload, there is a limit of <strong>30mb</strong> per file.</p>
                <div class="doc-dropzone" id="docDropzone">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                    <div>Drag and drop files here to upload</div>
                    <label class="doc-browse">
                        Browse files...
                        <input type="file" id="docFileInput" multiple style="display:none;" onchange="docHandleFiles(this.files)">
                    </label>
                </div>
                <div id="docUploadQueue" style="margin-top:14px;display:none;">
                    <div style="font-weight:600;color:#1b4332;margin-bottom:8px;" id="docQueueLabel"></div>
                    <div id="docQueueList" style="font-size:0.88rem;color:#555;"></div>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;">
                    <button type="button" class="doc-browse" style="border-color:#e74c5e;color:#e74c5e;" onclick="docToggleUpload()">Cancel</button>
                    <button type="button" class="doc-btn-gold" id="docUploadSubmitBtn" disabled onclick="docSubmitUpload()">
                        Upload
                    </button>
                </div>
            </div>

            {{-- Progress bar (shown during upload) --}}
            <div id="docUploadProgress" style="display:none;margin-bottom:12px;">
                <div style="height:6px;background:#e5e7eb;border-radius:4px;overflow:hidden;">
                    <div id="docProgressBar" style="height:100%;background:#2e7d5e;width:0%;transition:width 0.3s;"></div>
                </div>
                <div id="docProgressLabel" style="font-size:0.82rem;color:#666;margin-top:4px;">Uploading…</div>
            </div>

            {{-- Documents table --}}
            <table class="doc-table" id="docTable">
                <thead>
                    <tr>
                        <th style="width:36px;"><input type="checkbox" id="docSelectAll" onchange="docToggleSelectAll(this)"></th>
                        <th>Name</th>
                        <th style="width:120px;">Type</th>
                        <th style="width:90px;">Size</th>
                        <th style="width:180px;">Date created</th>
                        <th style="width:90px;"></th>
                    </tr>
                </thead>
                <tbody id="docTableBody">
                    <tr><td colspan="6"><div class="doc-empty">Loading…</div></td></tr>
                </tbody>
            </table>

            {{-- Pagination --}}
            <div id="docPagination" style="display:flex;align-items:center;justify-content:space-between;margin-top:14px;font-size:0.88rem;color:#555;"></div>

        </div>{{-- /tab-documents --}}

        {{-- ===== SHIFTS TAB ===== --}}
        <div class="bhr-tab-pane" id="tab-shifts">
            @php
                $upcomingShifts = $upcomingShifts ?? collect();
                $pastShifts     = $pastShifts ?? collect();
                $openShifts     = $openShifts ?? collect();
                $availableOpenShifts = $availableOpenShifts ?? collect();
                $canAcceptDecline = $canAcceptDecline ?? false;
                $openShiftsEnabled = ($rosterSettings ?? null)?->open_shifts_enabled ?? false;
                $acceptDeclineEnabled = ($rosterSettings ?? null)?->accept_decline_enabled ?? false;

                $fmtTime = function ($t) {
                    return $t ? \Carbon\Carbon::createFromFormat('H:i:s', substr($t, 0, 8))->format('g:i A') : '';
                };
                // [background, text color, left-border color, icon color, label]
                $statusStyle = function ($status, $isPast = false) {
                    if ($status === 'pending' && $isPast) {
                        return ['#fef2f2', '#b91c1c', '#ef4444', '#b91c1c', 'No response'];
                    }
                    $map = [
                        'pending'  => ['#fffbeb', '#92400e', '#f59e0b', '#d97706', 'Awaiting response'],
                        'accepted' => ['#f0fdf4', '#15803d', '#22c55e', '#16a34a', 'Accepted'],
                        'declined' => ['#fef2f2', '#b91c1c', '#ef4444', '#b91c1c', 'Declined'],
                        'eligible' => ['#eff6ff', '#1d4ed8', '#3b82f6', '#1d4ed8', 'Open — not claimed'],
                        'claimed'  => ['#f0fdf4', '#15803d', '#22c55e', '#16a34a', 'Claimed'],
                    ];
                    return $map[$status] ?? ['#f9fafb', '#374151', '#9ca3af', '#6b7280', ucfirst($status)];
                };
                $badge = function ($status, $isPast = false) use ($statusStyle) {
                    [$bg, $fg, , , $label] = $statusStyle($status, $isPast);
                    return "<span style=\"font-size:0.74rem;padding:4px 12px;border-radius:20px;font-weight:700;background:{$bg};color:{$fg};white-space:nowrap;\">{$label}</span>";
                };
                $clockIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" style="width:15px;height:15px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                $mapPinIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" style="width:14px;height:14px;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>';
            @endphp

            @if(!$acceptDeclineEnabled || !$openShiftsEnabled)
            <div style="display:flex;align-items:flex-start;gap:10px;background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 16px;margin-bottom:20px;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#d97706" style="width:20px;height:20px;flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                <div style="font-size:0.83rem;color:#92400e;line-height:1.5;">
                    @if(!$acceptDeclineEnabled)
                        <div><strong>Accept/decline</strong> is not active — this employee can't respond to shifts until you enable it under <strong>"Ability to accept and decline shifts"</strong> in the <em>Roster Settings</em> tab.</div>
                    @endif
                    @if(!$openShiftsEnabled)
                        <div><strong>Open shifts</strong> is not active — enable <strong>"Activate open shifts"</strong> in the <em>Roster Settings</em> tab so employees can request/claim open shifts.</div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Available open shifts — browse & request --}}
            @if($openShiftsEnabled)
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px 20px;margin-bottom:20px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#D4A017" style="width:19px;height:19px;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    <h3 style="font-size:0.95rem;font-weight:700;color:#1b4332;margin:0;">Available open shifts</h3>
                    <span style="font-size:0.74rem;padding:2px 10px;border-radius:20px;font-weight:600;background:#fdf3d9;color:#a3790f;">{{ $availableOpenShifts->count() }} available</span>
                </div>
                @if($availableOpenShifts->isEmpty())
                    <div style="font-size:0.85rem;color:#6b7280;">No open shifts available to request right now.</div>
                @else
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        @foreach($availableOpenShifts as $shift)
                        @php
                            $claimedCount = $shift->employees->where('pivot.status', 'claimed')->count();
                            $capacity = $shift->capacity ?? 0;
                            $isFull = $capacity > 0 && $claimedCount >= $capacity;
                        @endphp
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-radius:10px;background:#fefbf2;border:1px solid #fde68a;border-left:4px solid #D4A017;flex-wrap:wrap;gap:12px;">
                            <div>
                                <div style="display:flex;align-items:center;gap:6px;font-size:0.9rem;font-weight:700;color:#1b4332;">
                                    {!! $clockIcon !!} {{ $shift->shift_date_carbon->format('D, d M Y') }} · {{ $fmtTime($shift->start_time) }} – {{ $fmtTime($shift->end_time) }}
                                </div>
                                <div style="display:flex;align-items:center;gap:6px;font-size:0.78rem;color:#6b7280;margin-top:4px;">
                                    {!! $mapPinIcon !!} {{ $shift->plan->name ?? 'Roster' }}{{ $shift->notes ? ' · ' . $shift->notes : '' }}
                                    @if($shift->label)
                                        <span style="font-size:0.72rem;padding:1px 8px;border-radius:12px;background:#f3f4f6;color:#374151;">{{ $shift->label }}</span>
                                    @endif
                                </div>
                            </div>
                            <div style="display:flex;align-items:center;gap:10px;">
                                @if($capacity > 0)
                                    <span style="font-size:0.74rem;padding:4px 12px;border-radius:20px;font-weight:700;background:{{ $isFull ? '#fee2e2' : '#eff6ff' }};color:{{ $isFull ? '#b91c1c' : '#1d4ed8' }};white-space:nowrap;">
                                        {{ $claimedCount }}/{{ $capacity }} filled
                                    </span>
                                @endif
                                <form method="POST" action="{{ route('admin.rosters.plans.shifts.request-open', [$shift->id, $employee->id]) }}">
                                    @csrf
                                    <button type="submit" class="doc-btn-gold" style="border-radius:8px;">Request this shift</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @endif

            {{-- Open shifts this employee has already requested / claimed --}}
            @if($openShiftsEnabled && $openShifts->isNotEmpty())
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px 20px;margin-bottom:20px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#1d4ed8" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 style="font-size:0.95rem;font-weight:700;color:#1b4332;margin:0;">My open shift requests</h3>
                </div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @foreach($openShifts as $shift)
                    @php [, , $borderColor] = $statusStyle($shift->my_status); @endphp
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-radius:10px;background:#fafafa;border:1px solid #e5e7eb;border-left:4px solid {{ $borderColor }};flex-wrap:wrap;gap:12px;">
                        <div>
                            <div style="display:flex;align-items:center;gap:6px;font-size:0.9rem;font-weight:700;color:#1b4332;">
                                {!! $clockIcon !!} {{ $shift->shift_date_carbon->format('D, d M Y') }} · {{ $fmtTime($shift->start_time) }} – {{ $fmtTime($shift->end_time) }}
                            </div>
                            <div style="display:flex;align-items:center;gap:6px;font-size:0.78rem;color:#6b7280;margin-top:4px;">
                                {!! $mapPinIcon !!} {{ $shift->plan->name ?? 'Roster' }}{{ $shift->notes ? ' · ' . $shift->notes : '' }}
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:10px;">
                            {!! $badge($shift->my_status) !!}
                            <form method="POST" action="{{ route('admin.rosters.plans.open-shifts.toggle-claim', [$shift->id, $employee->id]) }}">
                                @csrf
                                <button type="submit" class="bhr-btn-outline" style="{{ $shift->my_status === 'claimed' ? 'border-color:#e74c5e;color:#e74c5e;' : 'border-color:#2e7d5e;color:#2e7d5e;' }}">
                                    {{ $shift->my_status === 'claimed' ? 'Release shift' : 'Claim shift' }}
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Upcoming assigned shifts --}}
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px 20px;margin-bottom:20px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#2e7d5e" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                    <h3 style="font-size:0.95rem;font-weight:700;color:#1b4332;margin:0;">Upcoming shifts</h3>
                    @if($upcomingShifts->isNotEmpty())
                        <span style="font-size:0.74rem;padding:2px 10px;border-radius:20px;font-weight:600;background:#e8f5ee;color:#1f5f46;">{{ $upcomingShifts->count() }}</span>
                    @endif
                </div>
                @if($upcomingShifts->isEmpty())
                    <div style="font-size:0.85rem;color:#6b7280;">No upcoming shifts assigned.</div>
                @else
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        @foreach($upcomingShifts as $shift)
                        @php [, , $borderColor] = $statusStyle($shift->my_status); @endphp
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-radius:10px;background:#fafafa;border:1px solid #e5e7eb;border-left:4px solid {{ $borderColor }};flex-wrap:wrap;gap:12px;">
                            <div>
                                <div style="display:flex;align-items:center;gap:6px;font-size:0.9rem;font-weight:700;color:#1b4332;">
                                    {!! $clockIcon !!} {{ $shift->shift_date_carbon->format('D, d M Y') }} · {{ $fmtTime($shift->start_time) }} – {{ $fmtTime($shift->end_time) }}
                                </div>
                                <div style="display:flex;align-items:center;gap:6px;font-size:0.78rem;color:#6b7280;margin-top:4px;">
                                    {!! $mapPinIcon !!} {{ $shift->plan->name ?? 'Roster' }}{{ $shift->notes ? ' · ' . $shift->notes : '' }}
                                </div>
                            </div>
                            <div style="display:flex;align-items:center;gap:10px;">
                                {!! $badge($shift->my_status) !!}
                                @if($shift->my_status === 'pending' && $canAcceptDecline)
                                    <form method="POST" action="{{ route('admin.rosters.plans.shifts.accept', [$shift->id, $employee->id]) }}">
                                        @csrf
                                        <button type="submit" class="doc-btn-green">Accept</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.rosters.plans.shifts.decline', [$shift->id, $employee->id]) }}">
                                        @csrf
                                        <button type="submit" class="bhr-btn-outline" style="border-color:#e74c5e;color:#e74c5e;">Decline</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Past shifts (read-only) --}}
            <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px 20px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#9ca3af" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                    <h3 style="font-size:0.95rem;font-weight:700;color:#1b4332;margin:0;">Past shifts</h3>
                </div>
                @if($pastShifts->isEmpty())
                    <div style="font-size:0.85rem;color:#6b7280;">No past shifts.</div>
                @else
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        @foreach($pastShifts as $shift)
                        @php [, , $borderColor] = $statusStyle($shift->my_status, true); @endphp
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 16px;border-radius:10px;background:#fafafa;border:1px solid #e5e7eb;border-left:4px solid {{ $borderColor }};flex-wrap:wrap;gap:12px;opacity:0.85;">
                            <div>
                                <div style="display:flex;align-items:center;gap:6px;font-size:0.9rem;font-weight:700;color:#1b4332;">
                                    {!! $clockIcon !!} {{ $shift->shift_date_carbon->format('D, d M Y') }} · {{ $fmtTime($shift->start_time) }} – {{ $fmtTime($shift->end_time) }}
                                </div>
                                <div style="display:flex;align-items:center;gap:6px;font-size:0.78rem;color:#6b7280;margin-top:4px;">
                                    {!! $mapPinIcon !!} {{ $shift->plan->name ?? 'Roster' }}{{ $shift->notes ? ' · ' . $shift->notes : '' }}
                                </div>
                            </div>
                            {!! $badge($shift->my_status, true) !!}
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>{{-- /tab-shifts --}}

        {{-- ===== KPI TAB ===== --}}
        {{-- A compact "how are they doing" summary — not the full breakdown. That
             lives on the Performance page (which this links to) so admins get a
             quick read here without leaving the profile, and a single detailed
             place to dig into what's done / pending / at risk. Both pull from the
             same KpiTemplate::forEmployee() so the numbers can never disagree. --}}
        <div class="bhr-tab-pane" id="tab-kpi">
            <div style="padding:4px 4px 24px;">
                <h3 style="font-size:1.05rem;font-weight:700;color:#1b4332;margin:0 0 4px;">🎯 KPI & Performance</h3>
                <p style="font-size:0.82rem;color:#6b7280;margin:0 0 18px;">A quick summary of {{ $employee->first_name }}'s weighted KPI progress.</p>

                @php
                    $kpiFirstName = $employee->first_name;
                    $kpiOverallPct = $kpiSummary['overall_pct'] ?? null;
                    $kpiHasTemplate = $kpiSummary !== null;

                    if (!$kpiHasTemplate) {
                        $kpiEmoji = '📋'; $kpiMessage = "No KPI template has been set for {$kpiFirstName}'s position yet.";
                    } elseif ($kpiOverallPct === null) {
                        $kpiEmoji = '📋'; $kpiMessage = "Not enough progress data filled in yet to calculate {$kpiFirstName}'s overall score.";
                    } elseif ($kpiOverallPct >= 100) {
                        $kpiEmoji = '🎉'; $kpiMessage = "Amazing work, {$kpiFirstName}! Every KPI target has been achieved.";
                    } elseif ($kpiOverallPct >= 80) {
                        $kpiEmoji = '💪'; $kpiMessage = "Great job, {$kpiFirstName}! Almost at the finish line — {$kpiOverallPct}% achieved overall.";
                    } elseif ($kpiOverallPct >= 50) {
                        $kpiEmoji = '💪'; $kpiMessage = "Good job, {$kpiFirstName}! Solid progress at {$kpiOverallPct}% overall — keep it up.";
                    } else {
                        $kpiEmoji = '⚡'; $kpiMessage = "Let's pick up the pace, {$kpiFirstName} — only {$kpiOverallPct}% achieved so far.";
                    }
                @endphp

                @php
                    $kpiInProgressCount = $kpiHasTemplate
                        ? max(0, $kpiSummary['total_indicators'] - $kpiSummary['achieved_count'] - $kpiSummary['at_risk_count'] - $kpiSummary['no_data_count'])
                        : 0;
                    $kpiAccentColor = $kpiSummary['status']['color'] ?? '#9ca3af';
                @endphp

                <div style="background:#fff;border:1px solid #e5e7eb;border-top:4px solid {{ $kpiAccentColor }};border-radius:14px;padding:20px 22px;display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                    <div style="flex-shrink:0;">{!! \App\Models\KpiTemplate::gaugeSvg($kpiOverallPct ?? 0, $kpiAccentColor) !!}</div>
                    <div style="flex:1;min-width:220px;">
                        <div style="font-size:1rem;font-weight:800;color:#1b4332;margin-bottom:8px;"><span style="font-size:1.2rem;margin-right:4px;">{{ $kpiEmoji }}</span>{{ $kpiMessage }}</div>
                        @if($kpiHasTemplate)
                            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                @if($kpiSummary['achieved_count'] > 0)
                                    <span style="font-size:0.72rem;font-weight:700;padding:4px 11px;border-radius:20px;background:#eaf3de;color:#27500a;">✅ {{ $kpiSummary['achieved_count'] }} achieved</span>
                                @endif
                                @if($kpiInProgressCount > 0)
                                    <span style="font-size:0.72rem;font-weight:700;padding:4px 11px;border-radius:20px;background:#eeedfe;color:#3c3489;">💪 {{ $kpiInProgressCount }} in progress</span>
                                @endif
                                @if($kpiSummary['at_risk_count'] > 0)
                                    <span style="font-size:0.72rem;font-weight:700;padding:4px 11px;border-radius:20px;background:#fcebeb;color:#a32d2d;">⚠️ {{ $kpiSummary['at_risk_count'] }} at risk</span>
                                @endif
                                @if($kpiSummary['no_data_count'] > 0)
                                    <span style="font-size:0.72rem;font-weight:700;padding:4px 11px;border-radius:20px;background:#f3f4f6;color:#6b7280;">📋 {{ $kpiSummary['no_data_count'] }} no data yet</span>
                                @endif
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('admin.performance.index', ['employee' => $employee->id]) }}"
                       style="flex-shrink:0;display:inline-flex;align-items:center;gap:6px;padding:10px 18px;font-size:0.82rem;font-weight:700;color:#fff;background-color:#1f5f46;border-radius:10px;text-decoration:none;white-space:nowrap;transition:background-color .15s;"
                       onmouseover="this.style.backgroundColor='#287854'" onmouseout="this.style.backgroundColor='#1f5f46'">
                        🔍 Click here for more details
                        <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                {{-- The actual KPIs and how far along each one is — everything specific to
                     day-to-day work (who was recruited, which clients, revenue, vacancies…)
                     lives only on the Performance page linked above; this is just the KPI
                     targets themselves and their progress. --}}
                <div style="display:flex;align-items:center;justify-content:space-between;margin:20px 0 10px;flex-wrap:wrap;gap:8px;">
                    <h4 style="font-size:0.85rem;font-weight:700;color:#1b4332;margin:0;">📋 KPI Breakdown</h4>
                    @if($kpiHasTemplate)
                        <a href="{{ route('admin.kpi-jd.kpi-document', $employee->id) }}"
                           style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;font-size:0.76rem;font-weight:600;color:#1f5f46;background:#eaf3ee;border:1px solid #cfe3d8;border-radius:8px;text-decoration:none;white-space:nowrap;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0l-4-4m4 4l4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2"/></svg>
                            Download KPI Acknowledgement (Word)
                        </a>
                    @endif
                </div>
                @include('admin.kpi.goals-cards', ['groups' => $kpiGoalGroups ?? []])
            </div>
        </div>{{-- /tab-kpi --}}

        <div class="bhr-tab-pane" id="tab-equipment">
            @php $equipmentOnLoan = $employee->equipmentOnLoan; @endphp
            <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:14px;gap:16px;">
                <div>
                    <h3 style="font-size:0.95rem;font-weight:700;color:#1b4332;margin:0 0 4px;">Equipment on loan</h3>
                    <p style="font-size:0.82rem;color:#6b7280;margin:0;">Company assets currently assigned to {{ $employee->first_name }} — their responsibility to look after and return if they leave.</p>
                </div>
                @if($equipmentOnLoan->isNotEmpty())
                <a href="{{ route('admin.finance.inventory-assets.index') }}" target="_blank"
                   style="font-size:0.8rem;font-weight:600;color:#2e7d5e;text-decoration:none;padding:6px 14px;border:1px solid #2e7d5e;border-radius:6px;white-space:nowrap;flex-shrink:0;">
                    Manage in Inventory &rarr;
                </a>
                @endif
            </div>

            @if($equipmentOnLoan->isEmpty())
                <div style="background:#fafafa;border:1px solid #e5e7eb;border-radius:10px;padding:32px 20px;text-align:center;color:#6b7280;font-size:0.88rem;">
                    No equipment currently on loan to {{ $employee->first_name }}.
                </div>
            @else
                @php
                    $assetStatusColors = [
                        'In Use'       => ['#dcfce7', '#15803d'],
                        'In Storage'   => ['#f1f5f9', '#475569'],
                        'Under Repair' => ['#fef3c7', '#92400e'],
                        'Retired'      => ['#e5e7eb', '#6b7280'],
                        'Lost'         => ['#fee2e2', '#b91c1c'],
                    ];
                @endphp
                <div style="display:flex;flex-direction:column;gap:10px;">
                    @foreach($equipmentOnLoan as $asset)
                        @php [$assetBg, $assetColor] = $assetStatusColors[$asset->status] ?? ['#f1f5f9', '#475569']; @endphp
                        <div style="display:flex;align-items:center;gap:14px;padding:12px 16px;border-radius:8px;background:#fafafa;border:1px solid #e5e7eb;">
                            @if($asset->photo_path)
                                <img src="{{ route('admin.finance.inventory-assets.photo', $asset) }}" alt="" style="width:44px;height:44px;border-radius:8px;object-fit:cover;flex-shrink:0;border:1px solid #e5e7eb;">
                            @else
                                <div style="width:44px;height:44px;border-radius:8px;background:#eef6f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2e7d5e" style="width:22px;height:22px;"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                </div>
                            @endif
                            <div style="flex:1;min-width:0;">
                                <div style="font-size:0.88rem;font-weight:700;color:#1b4332;">{{ $asset->name }}</div>
                                <div style="font-size:0.78rem;color:#6b7280;">
                                    {{ $asset->category }}@if($asset->brand) &middot; {{ $asset->brand }}@endif @if($asset->model){{ $asset->model }}@endif@if($asset->serial_number) &middot; SN: {{ $asset->serial_number }}@endif
                                </div>
                                @if($asset->assigned_date)
                                    <div style="font-size:0.76rem;color:#9ca3af;margin-top:2px;">On loan since {{ $asset->assigned_date->format('d M Y') }} ({{ $asset->duration_used }})</div>
                                @endif
                                @if($asset->notes)
                                    <div style="font-size:0.76rem;color:#6b7280;margin-top:4px;">Note: {{ $asset->notes }}</div>
                                @endif
                            </div>
                            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px;flex-shrink:0;">
                                <span style="font-size:0.75rem;font-weight:600;padding:3px 10px;border-radius:20px;background:{{ $assetBg }};color:{{ $assetColor }};">{{ $asset->status }}</span>
                                <span style="font-size:0.72rem;color:#9ca3af;">{{ $asset->condition }} condition</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>{{-- /tab-equipment --}}

        {{-- ===== NEW FOLDER MODAL ===== --}}
        <div class="doc-modal-overlay" id="docFolderModal">
            <div class="doc-modal">
                <div class="doc-modal-head">
                    <span id="docFolderModalTitle">Create a new folder</span>
                    <span class="x" onclick="docCloseFolder()">&times;</span>
                </div>
                <div class="doc-modal-body">
                    <input type="hidden" id="docFolderEditId" value="">
                    <label>Folder name</label>
                    <input type="text" id="docFolderName" placeholder="e.g. Contracts">
                    <label style="margin-top:20px;">Colour</label>
                    <div class="doc-colours">
                        @foreach(['#42a5f5','#ef5350','#ec407a','#ffa726','#43a047','#1e6bb8','#8e244d'] as $ci => $col)
                            <div class="doc-colour {{ $ci === 0 ? 'sel' : '' }}" data-color="{{ $col }}" onclick="docPickColour(this)">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="{{ $col }}"><path d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="doc-modal-foot">
                    <button type="button" class="doc-browse" style="border-color:#e74c5e;color:#e74c5e;" onclick="docCloseFolder()">Cancel</button>
                    <button type="button" class="doc-btn-green" id="docFolderSaveBtn" onclick="docSaveFolder()">Create</button>
                </div>
            </div>
        </div>

        {{-- ===== REMOVE ABSENCE CONFIRM MODAL ===== --}}
        <div class="doc-modal-overlay" id="absDeleteModal" style="display:none;">
            <div class="doc-modal" style="max-width:440px;">
                <div class="doc-modal-head">
                    Remove absence
                    <span class="x" onclick="closeRemoveAbsenceModal()">&times;</span>
                </div>
                <div class="doc-modal-body">
                    <p style="margin:0;font-size:0.92rem;color:#333;">
                        Are you sure you want to remove this absence record? This cannot be undone.
                    </p>
                </div>
                <div class="doc-modal-foot">
                    <button type="button" class="doc-browse" style="border-color:#e74c5e;color:#e74c5e;" onclick="closeRemoveAbsenceModal()">Cancel</button>
                    <button type="button" id="absDeleteConfirmBtn" class="doc-btn-grey" style="background:#e74c5e;" onclick="doRemoveAbsence()">Remove</button>
                </div>
            </div>
        </div>

        {{-- ===== DELETE FILE CONFIRM MODAL ===== --}}
        <div class="doc-modal-overlay" id="docDeleteModal">
            <div class="doc-modal" style="max-width:440px;">
                <div class="doc-modal-head">
                    Delete file
                    <span class="x" onclick="docCloseDelete()">&times;</span>
                </div>
                <div class="doc-modal-body">
                    <p style="margin:0;font-size:0.92rem;color:#333;">
                        Are you sure you want to delete <strong id="docDeleteName"></strong>? This cannot be undone.
                    </p>
                </div>
                <div class="doc-modal-foot">
                    <button type="button" class="doc-browse" style="border-color:#e74c5e;color:#e74c5e;" onclick="docCloseDelete()">Cancel</button>
                    <button type="button" id="docDeleteConfirmBtn" class="doc-btn-grey" style="background:#e74c5e;" onclick="docConfirmDelete()">Delete</button>
                </div>
            </div>
        </div>

{{-- ===================================================================
     JAVASCRIPT — Tab Documents
     Letakkan di dalam blok <script> yang sudah ada di bagian bawah file,
     atau tambahkan di bawah blok </div> penutup bhr-profile-container.
     ===================================================================--}}
<script>
(function () {
    'use strict';

    /* ── Config ─────────────────────────────────────────── */
    var EMPLOYEE_ID   = {{ $employee->id }};
    var FILES_URL     = "{{ route('admin.linkers-hub.files.index',    $employee->id) }}";
    var UPLOAD_URL    = "{{ route('admin.linkers-hub.files.upload',   $employee->id) }}";
    var FOLDERS_URL   = "{{ route('admin.linkers-hub.folders.store',  $employee->id) }}";
    var DOWNLOAD_BASE = "/admin/linkers-hub/employees/{{ $employee->id }}/files";
    var FOLDERS_BASE  = "/admin/linkers-hub/employees/{{ $employee->id }}/folders";
    var DOC_CSRF      = document.querySelector('meta[name="csrf-token"]')
                            ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            : '{{ csrf_token() }}';

    var REQUIRED_FOLDER_ID = {{ $requiredFolderId }};
    var REQUIRED_DOC_TYPE_LABELS = {!! json_encode($requiredFolders->pluck('name', 'type')) !!};

    // Row-specific upload for a Required Document: opens the native file
    // explorer directly (no in-page panel, no document-type dropdown) and
    // uploads the file tied to that exact required type.
    window.triggerRequiredUpload = function (type) {
        var input = document.getElementById('reqFileInput_' + type);
        if (input) input.click();
    };

    window.docUploadRequiredType = function (type, fileList) {
        if (!fileList || fileList.length === 0) return;
        var file = fileList[0];

        var formData = new FormData();
        formData.append('files[]', file);
        formData.append('folder_id', REQUIRED_FOLDER_ID);
        formData.append('document_type', type);

        showToast('Uploading ' + file.name + '…', 'success');

        fetch(UPLOAD_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': DOC_CSRF, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                showToast('✓ ' + data.message, 'success');
                // Reload so the checklist, progress ring, and file list all
                // reflect the new upload immediately.
                window.location.reload();
            } else {
                showToast('Upload failed: ' + (data.message || 'Unknown error'), 'error');
            }
        })
        .catch(function () { showToast('Network error. Please try again.', 'error'); });
    };

    /* ── State ──────────────────────────────────────────── */
    var docCurrentFolder = null;   // null = root
    var docCurrentPage   = 1;
    var docSelectedIds   = {};     // { id: { type:'file'|'folder', name } }
    var docPendingDelete = null;   // { id, name }
    var docFilesQueued   = [];     // FileList as array
    var docIsUploading   = false;
    var docInitialized   = false; // track if documents have been loaded at least once

    /* ── Init: load when Documents tab is clicked (lazy, first time only) ── */
    document.querySelectorAll('.bhr-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
            if (tab.getAttribute('data-tab') === 'documents') {
                docEnsureLoaded();
            }
        });
    });

    // Exposed globally so the URL-hash tab-restore script (which runs in a
    // later <script> block, after this one) can trigger the load when the
    // page is opened directly on #documents — e.g. .../profile#documents.
    // Checking `.active` here on page load does NOT work because that class
    // is only added by the hash-restore script, which hasn't run yet at
    // this point in the page.
    function docEnsureLoaded() {
        if (!docInitialized) {
            docInitialized = true;
            docNavigate(null);
        }
    }
    window.docEnsureLoaded = docEnsureLoaded;

    /* ── Navigation ─────────────────────────────────────── */
    window.docNavigate = function (folderId, force) {
        docCurrentFolder = folderId;
        docCurrentPage   = 1;
        docSelectedIds   = {};
        updateDownloadBtn();
        updateDocTypeFieldVisibility();
        docLoadFiles();
    };

    /* ── Reload (e.g. after per-page change) ────────────── */
    window.docReload = function () {
        docCurrentPage = 1;
        docLoadFiles();
    };

    /* ── Core: fetch files + folders from server ─────────── */
    function docLoadFiles() {
        var perPage  = document.getElementById('docPerPage').value;
        var search   = (document.getElementById('docSearchInput').value || '').trim();
        var params   = new URLSearchParams({
            folder_id: docCurrentFolder || '',
            page:      docCurrentPage,
            per_page:  perPage,
            search:    search,
        });

        var tbody = document.getElementById('docTableBody');
        tbody.innerHTML = '<tr><td colspan="6"><div class="doc-empty">Loading…</div></td></tr>';

        fetch(FILES_URL + '?' + params.toString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (!data.success) { showDocError('Could not load documents.'); return; }
            renderBreadcrumb(data.breadcrumb || [], search);
            renderTableRows(data.folders || [], data.files || [], search);
            renderPagination(data.total, data.page, data.per_page);
        })
        .catch(function () { showDocError('Network error. Please try again.'); });
    }

    /* ── Render breadcrumb ──────────────────────────────── */
    function renderBreadcrumb(trail, search) {
        var el = document.getElementById('docBreadcrumb');
        var html = '<a href="#" onclick="docNavigate(null); return false;">All folders</a>';
        trail.forEach(function (crumb) {
            html += ' &nbsp;&rsaquo;&nbsp; <a href="#" onclick="docNavigate(' + crumb.id + '); return false;">' + escHtml(crumb.name) + '</a>';
        });
        if (search) html += ' &nbsp;&rsaquo;&nbsp; <strong>Search: ' + escHtml(search) + '</strong>';
        el.innerHTML = html;
    }

    /* ── Render table rows ──────────────────────────────── */
    function renderTableRows(folders, files, search) {
        var tbody = document.getElementById('docTableBody');
        if (folders.length === 0 && files.length === 0) {
            tbody.innerHTML = search
                ? '<tr><td colspan="6"><div class="doc-empty">No results found for "' + escHtml(search) + '".</div></td></tr>'
                : '<tr><td colspan="6"><div class="doc-empty">No documents or folders yet. Use "Upload" or "New folder" to get started.</div></td></tr>';
            return;
        }

        var html = '';

        // Folders first — use data-* attributes to avoid JSON.stringify in onclick breaking HTML
        folders.forEach(function (f) {
            var checked = docSelectedIds['folder_' + f.id] ? 'checked' : '';
            var safeName  = escHtml(f.name);
            var safeColor = escHtml(f.color);
            var requiredBadge = f.is_required
                ? ' <span style="font-size:0.72rem;padding:2px 8px;border-radius:20px;background:#eafaf1;color:#2e7d5e;font-weight:600;">Required</span>'
                : '';
            var actionsHtml = f.locked
                ? '<span style="color:#9ca3af;font-size:0.85rem;font-style:italic;" title="Locked — all required documents have been submitted">Locked</span>'
                : (
                    '<a href="#" data-fid="' + f.id + '" data-fname="' + safeName + '" data-fcolor="' + safeColor + '" onclick="docOpenEditFolder(parseInt(this.dataset.fid), this.dataset.fname, this.dataset.fcolor); return false;" style="color:#2e7d5e;font-size:0.85rem;font-weight:600;text-decoration:none;margin-right:10px;">Rename</a>' +
                    '<a href="#" data-fid="' + f.id + '" data-fname="' + safeName + '" onclick="docAskDeleteFolder(parseInt(this.dataset.fid), this.dataset.fname); return false;" style="color:#e74c5e;font-size:0.85rem;font-weight:600;text-decoration:none;">Delete</a>'
                );
            html += '<tr>' +
                '<td><input type="checkbox" ' + checked + ' data-type="folder" data-id="' + f.id + '" data-name="' + safeName + '" onchange="docToggleSelectFromEl(this)"></td>' +
                '<td><a href="#" data-folder-id="' + f.id + '" onclick="docNavigate(parseInt(this.dataset.folderId)); return false;" style="display:inline-flex;align-items:center;gap:8px;color:#1b4332;font-weight:600;text-decoration:none;">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="' + safeColor + '" style="width:20px;height:20px;"><path d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z"/></svg>' +
                    safeName +
                    ' <span style="font-size:0.8rem;color:#888;font-weight:400;">(' + f.file_count + ' files)</span>' +
                    requiredBadge +
                '</a></td>' +
                '<td style="color:#888;">Folder</td>' +
                '<td style="color:#888;">—</td>' +
                '<td style="color:#888;">' + escHtml(f.created_at) + '</td>' +
                '<td>' + actionsHtml + '</td>' +
                '</tr>';
        });

        // Files — use data-* attributes to avoid JSON.stringify in onclick breaking HTML
        files.forEach(function (f) {
            var checked  = docSelectedIds['file_' + f.id] ? 'checked' : '';
            var safeName = escHtml(f.name);
            var docTypeBadge = f.document_type
                ? ' <span style="font-size:0.72rem;padding:2px 8px;border-radius:20px;background:#f0fdf4;color:#15803d;font-weight:600;">' + escHtml(REQUIRED_DOC_TYPE_LABELS[f.document_type] || f.document_type) + '</span>'
                : '';
            html += '<tr>' +
                '<td><input type="checkbox" ' + checked + ' data-type="file" data-id="' + f.id + '" data-name="' + safeName + '" onchange="docToggleSelectFromEl(this)"></td>' +
                '<td style="word-break:break-all;">' +
                '<a href="' + DOWNLOAD_BASE + '/' + f.id + '/view" target="_blank" style="color:#1b4332;font-weight:500;text-decoration:none;" title="Click to preview">' + safeName + '</a>' + docTypeBadge + '</td>' +
                '<td>' + escHtml(f.file_type) + '</td>' +
                '<td>' + escHtml(f.file_size) + '</td>' +
                '<td>' + escHtml(f.created_at) + '</td>' +
                '<td>' +
                    '<a href="' + DOWNLOAD_BASE + '/' + f.id + '/download" style="color:#2e7d5e;font-size:0.85rem;font-weight:600;text-decoration:none;margin-right:10px;">Download</a>' +
                    '<a href="#" data-fid="' + f.id + '" data-fname="' + safeName + '" onclick="docAskDelete(parseInt(this.dataset.fid), this.dataset.fname); return false;" style="color:#e74c5e;font-size:0.85rem;font-weight:600;text-decoration:none;">Delete</a>' +
                '</td>' +
                '</tr>';
        });

        tbody.innerHTML = html;
    }

    /* ── Pagination ─────────────────────────────────────── */
    function renderPagination(total, page, perPage) {
        var el     = document.getElementById('docPagination');
        var pages  = Math.ceil(total / perPage);
        var from   = total === 0 ? 0 : (page - 1) * perPage + 1;
        var to     = Math.min(page * perPage, total);

        if (total === 0) { el.innerHTML = ''; return; }

        var info = '<span>Showing ' + from + '–' + to + ' of ' + total + ' item(s)</span>';
        var prev = page > 1
            ? '<button onclick="docChangePage(' + (page - 1) + ')" style="' + pageBtnStyle() + '">&laquo; Prev</button>'
            : '<button disabled style="' + pageBtnStyle(true) + '">&laquo; Prev</button>';
        var next = page < pages
            ? '<button onclick="docChangePage(' + (page + 1) + ')" style="' + pageBtnStyle() + '">Next &raquo;</button>'
            : '<button disabled style="' + pageBtnStyle(true) + '">Next &raquo;</button>';

        el.innerHTML = info + '<div style="display:flex;gap:8px;">' + prev + next + '</div>';
    }

    function pageBtnStyle(disabled) {
        return 'padding:7px 16px;border:1px solid #d1d5db;border-radius:8px;background:#fff;font-size:0.88rem;cursor:' + (disabled ? 'not-allowed;color:#aaa' : 'pointer;color:#2e7d5e;font-weight:600') + ';';
    }

    window.docChangePage = function (p) {
        docCurrentPage = p;
        docLoadFiles();
    };

    /* ── Search ─────────────────────────────────────────── */
    window.docSearch = function () {
        docCurrentPage = 1;
        docLoadFiles();
    };

    window.docHandleSearchInput = function () {
        var val = (document.getElementById('docSearchInput').value || '').trim();
        var btn = document.getElementById('docSearchBtn');
        // Toggle search button color
        if (val) {
            btn.classList.remove('doc-btn-grey');
            btn.classList.add('doc-btn-green');
        } else {
            btn.classList.remove('doc-btn-green');
            btn.classList.add('doc-btn-grey');
            // Auto-reset: clear search and reload all files when input is emptied
            docCurrentPage = 1;
            docLoadFiles();
            return;
        }
        // Live search: filter after 300ms debounce
        clearTimeout(window._docSearchTimer);
        window._docSearchTimer = setTimeout(function () {
            docCurrentPage = 1;
            docLoadFiles();
        }, 300);
    };

    /* ── Select-all & per-row checkbox ─────────────────── */
    window.docToggleSelectAll = function (cb) {
        document.querySelectorAll('#docTableBody input[type=checkbox]').forEach(function (c) {
            c.checked = cb.checked;
            // re-use the row onchange by dispatching event
            c.dispatchEvent(new Event('change'));
        });
    };

    /* Reads type/id/name from data-* attributes — avoids JSON.stringify in onclick */
    window.docToggleSelectFromEl = function (cb) {
        var type = cb.dataset.type;
        var id   = parseInt(cb.dataset.id);
        var name = cb.dataset.name;
        window.docToggleSelect(cb, type, id, name);
    };

    window.docToggleSelect = function (cb, type, id, name) {
        var key = type + '_' + id;
        if (cb.checked) {
            docSelectedIds[key] = { type: type, id: id, name: name };
        } else {
            delete docSelectedIds[key];
        }
        updateDownloadBtn();
        // Sync select-all checkbox
        var all   = document.querySelectorAll('#docTableBody input[type=checkbox]');
        var checked = document.querySelectorAll('#docTableBody input[type=checkbox]:checked');
        var sa = document.getElementById('docSelectAll');
        if (sa) sa.indeterminate = checked.length > 0 && checked.length < all.length;
        if (sa) sa.checked = all.length > 0 && checked.length === all.length;
    };

    function updateDownloadBtn() {
        var count = Object.keys(docSelectedIds).length;
        var bar = document.getElementById('docBulkBar');
        if (!bar) return;
        bar.style.display = count > 0 ? 'block' : 'none';
    }

    /* Bulk-download all currently-selected files/folders as one ZIP.
       Folders are expanded server-side (recursively, including subfolders)
       into every file they contain. */
    window.docBulkDownload = function () {
        var items = Object.values(docSelectedIds);
        if (items.length === 0) return;

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.linkers-hub.files.bulk-download', $employee->id) }}';

        var csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        items.forEach(function (item) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = (item.type === 'folder' ? 'folder_ids[]' : 'file_ids[]');
            input.value = item.id;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
        form.remove();
    };



    /* ── Upload panel toggle ────────────────────────────── */
    window.docToggleUpload = function (forceOpen) {
        var p = document.getElementById('docUploadPanel');
        if (forceOpen === true) {
            p.classList.add('show');
        } else {
            p.classList.toggle('show');
        }
        // Reset queue when closing
        if (!p.classList.contains('show')) {
            docFilesQueued = [];
            renderQueue();
            document.getElementById('docFileInput').value = '';
        }
        updateDocTypeFieldVisibility();
    };

    /* ── The general/toolbar Upload button never shows a "which required
       document" picker (that only happens per-row now, via the checklist
       above). If someone browses directly into the Required Documents
       folder, the general Upload button is disabled instead — required
       documents must be uploaded via the checklist rows so the type is
       always unambiguous. ─────────────────────────────────────────── */
    function updateDocTypeFieldVisibility() {
        var isRequiredFolder = docCurrentFolder && REQUIRED_FOLDER_ID && parseInt(docCurrentFolder) === parseInt(REQUIRED_FOLDER_ID);
        var toolbarBtn = document.getElementById('docToolbarUploadBtn');
        if (toolbarBtn) {
            toolbarBtn.disabled = !!isRequiredFolder;
            toolbarBtn.style.opacity = isRequiredFolder ? '0.5' : '';
            toolbarBtn.style.cursor = isRequiredFolder ? 'not-allowed' : '';
            toolbarBtn.title = isRequiredFolder ? 'Use the checklist above to upload required documents' : '';
        }
    }

    /* ── Drag & Drop ────────────────────────────────────── */
    (function () {
        var dz = document.getElementById('docDropzone');
        if (!dz) return;

        dz.addEventListener('dragover', function (e) {
            e.preventDefault();
            dz.style.borderColor = '#2e7d5e';
            dz.style.background = '#f0faf5';
        });
        dz.addEventListener('dragleave', function () {
            dz.style.borderColor = '';
            dz.style.background = '';
        });
        dz.addEventListener('drop', function (e) {
            e.preventDefault();
            dz.style.borderColor = '';
            dz.style.background = '';
            docHandleFiles(e.dataTransfer.files);
        });
    })();

    /* ── Handle files selected/dropped ─────────────────── */
    window.docHandleFiles = function (fileList) {
        docFilesQueued = Array.from(fileList);
        renderQueue();
        document.getElementById('docUploadSubmitBtn').disabled = docFilesQueued.length === 0;
    };

    function renderQueue() {
        var el    = document.getElementById('docUploadQueue');
        var label = document.getElementById('docQueueLabel');
        var list  = document.getElementById('docQueueList');

        if (docFilesQueued.length === 0) {
            el.style.display = 'none';
            return;
        }
        el.style.display = 'block';
        label.textContent = docFilesQueued.length + ' file(s) ready to upload:';
        list.innerHTML = docFilesQueued.map(function (f) {
            return '<div style="padding:2px 0;">📄 ' + escHtml(f.name) + ' <span style="color:#aaa;">(' + humanSize(f.size) + ')</span></div>';
        }).join('');
    }

    /* ── Submit upload ──────────────────────────────────── */
    window.docSubmitUpload = function () {
        if (docFilesQueued.length === 0 || docIsUploading) return;

        docIsUploading = true;

        var btn      = document.getElementById('docUploadSubmitBtn');
        var progWrap = document.getElementById('docUploadProgress');
        var progBar  = document.getElementById('docProgressBar');
        var progLbl  = document.getElementById('docProgressLabel');

        btn.disabled = true;
        btn.textContent = 'Uploading…';
        progWrap.style.display = 'block';
        progBar.style.width = '0%';
        progLbl.textContent = 'Uploading 0 of ' + docFilesQueued.length + '…';

        var formData = new FormData();
        docFilesQueued.forEach(function (f) { formData.append('files[]', f); });
        if (docCurrentFolder) formData.append('folder_id', docCurrentFolder);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', UPLOAD_URL);
        xhr.setRequestHeader('X-CSRF-TOKEN', DOC_CSRF);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.upload.onprogress = function (e) {
            if (e.lengthComputable) {
                var pct = Math.round((e.loaded / e.total) * 100);
                progBar.style.width = pct + '%';
                progLbl.textContent = 'Uploading… ' + pct + '%';
            }
        };

        xhr.onload = function () {
            docIsUploading = false;
            btn.disabled   = false;
            btn.textContent = 'Upload';
            setTimeout(function () { progWrap.style.display = 'none'; }, 1500);

            try {
                var data = JSON.parse(xhr.responseText);
                if (data.success || (data.uploaded && data.uploaded.length > 0)) {
                    showToast('✓ ' + data.message, 'success');
                    docFilesQueued = [];
                    renderQueue();
                    document.getElementById('docFileInput').value = '';
                    document.getElementById('docUploadSubmitBtn').disabled = true;
                    document.getElementById('docUploadPanel').classList.remove('show');
                    docLoadFiles();
                } else {
                    showToast('Upload failed: ' + (data.message || 'Unknown error'), 'error');
                }
                if (data.errors && data.errors.length) {
                    data.errors.forEach(function (e) { showToast('⚠ ' + e, 'error'); });
                }
            } catch (e) {
                showToast('Unexpected server response.', 'error');
            }
        };

        xhr.onerror = function () {
            docIsUploading = false;
            btn.disabled   = false;
            btn.textContent = 'Upload';
            showToast('Network error. Please try again.', 'error');
        };

        xhr.send(formData);
    };

    /* ── Folder modal: open (create) ────────────────────── */
    window.docOpenFolder = function () {
        document.getElementById('docFolderModalTitle').textContent = 'Create a new folder';
        document.getElementById('docFolderEditId').value = '';
        document.getElementById('docFolderName').value = '';
        document.getElementById('docFolderSaveBtn').textContent = 'Create';
        // Reset colour to first
        document.querySelectorAll('.doc-colour').forEach(function (c, i) {
            c.classList.toggle('sel', i === 0);
        });
        document.getElementById('docFolderModal').classList.add('show');
        setTimeout(function () { document.getElementById('docFolderName').focus(); }, 50);
    };

    /* ── Folder modal: open (edit/rename) ───────────────── */
    window.docOpenEditFolder = function (id, name, color) {
        document.getElementById('docFolderModalTitle').textContent = 'Rename folder';
        document.getElementById('docFolderEditId').value = id;
        document.getElementById('docFolderName').value = name;
        document.getElementById('docFolderSaveBtn').textContent = 'Save';
        // Set correct colour
        document.querySelectorAll('.doc-colour').forEach(function (c) {
            c.classList.toggle('sel', c.getAttribute('data-color') === color);
        });
        document.getElementById('docFolderModal').classList.add('show');
        setTimeout(function () { document.getElementById('docFolderName').focus(); }, 50);
    };

    window.docCloseFolder = function () {
        document.getElementById('docFolderModal').classList.remove('show');
    };

    /* ── Folder colour picker ───────────────────────────── */
    window.docPickColour = function (el) {
        document.querySelectorAll('.doc-colour').forEach(function (c) { c.classList.remove('sel'); });
        el.classList.add('sel');
    };

    /* ── Folder modal: save (create or update) ───────────── */
    window.docSaveFolder = function () {
        var name    = (document.getElementById('docFolderName').value || '').trim();
        var editId  = document.getElementById('docFolderEditId').value;
        var colorEl = document.querySelector('.doc-colour.sel');
        var color   = colorEl ? colorEl.getAttribute('data-color') : '#42a5f5';
        var btn     = document.getElementById('docFolderSaveBtn');

        if (!name) { showToast('Please enter a folder name.', 'error'); return; }

        btn.disabled    = true;
        btn.textContent = 'Saving…';

        var isEdit  = editId !== '';
        var url     = isEdit ? FOLDERS_BASE + '/' + editId : FOLDERS_URL;
        var method  = 'POST';
        var payload = isEdit
            ? { name: name, color: color }
            : { name: name, color: color, parent_id: docCurrentFolder };

        var finalUrl = isEdit ? FOLDERS_BASE + '/' + editId + '/update' : FOLDERS_URL;
        fetch(finalUrl, {
            method:  'POST',
            headers: {
                'Content-Type':     'application/json',
                'Accept':           'application/json',
                'X-CSRF-TOKEN':     DOC_CSRF,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                showToast(isEdit ? '✓ Folder renamed.' : '✓ Folder created.', 'success');
                docCloseFolder();
                docLoadFiles();
            } else {
                showToast('Error: ' + (data.message || 'Unknown error'), 'error');
            }
            btn.disabled    = false;
            btn.textContent = isEdit ? 'Save' : 'Create';
        })
        .catch(function () {
            showToast('Network error. Please try again.', 'error');
            btn.disabled    = false;
            btn.textContent = isEdit ? 'Save' : 'Create';
        });
    };

    /* ── Delete folder ──────────────────────────────────── */
    window.docAskDeleteFolder = function (id, name) {
        // Reuse the same delete modal, but store folder info with a flag
        docPendingDelete = { id: id, name: name, isFolder: true };
        document.getElementById('docDeleteName').textContent = name;
        // Update modal title to mention folder
        var modalHead = document.querySelector('#docDeleteModal .doc-modal-head');
        if (modalHead) modalHead.firstChild.textContent = 'Delete folder';
        document.querySelector('#docDeleteModal .doc-modal-body p').innerHTML =
            'Are you sure you want to delete folder <strong>' + escHtml(name) + '</strong> and ALL its contents? This cannot be undone.';
        document.getElementById('docDeleteModal').classList.add('show');
    };

    /* ── Delete file (confirm modal) ────────────────────── */
    window.docAskDelete = function (id, name) {
        docPendingDelete = { id: id, name: name };
        document.getElementById('docDeleteName').textContent = name;
        document.getElementById('docDeleteModal').classList.add('show');
    };
    /* ── File preview ──────────────────────────────────── */
    window.docOpenPreview = function (id, name, mime) {
        var url   = DOWNLOAD_BASE + '/' + id + '/download';
        var body  = document.getElementById('docPreviewBody');
        var title = document.getElementById('docPreviewTitle');
        var link  = document.getElementById('docPreviewDownloadLink');

        title.textContent = name;
        link.href         = url;
        link.download     = name;

        var m = (mime || '').toLowerCase();
        if (m.indexOf('pdf') !== -1) {
            body.innerHTML = '<iframe src="' + url + '" style="width:100%;height:70vh;border:none;"></iframe>';
        } else if (m.indexOf('image/') !== -1) {
            body.innerHTML = '<img src="' + url + '" style="max-width:100%;max-height:70vh;object-fit:contain;padding:16px;">';
        } else if (m.indexOf('text/') !== -1) {
            body.innerHTML = '<span style="color:#888;padding:32px;">Loading text…</span>';
            fetch(url).then(function(r){ return r.text(); }).then(function(t){
                body.innerHTML = '<pre style="padding:24px;font-size:0.88rem;white-space:pre-wrap;word-break:break-word;text-align:left;width:100%;box-sizing:border-box;">' + escHtml(t) + '</pre>';
            });
        } else {
            body.innerHTML = '<div style="padding:40px;text-align:center;color:#555;">' +
                '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#9ca3af" style="width:64px;height:64px;margin-bottom:16px;"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>' +
                '<div style="font-size:1rem;font-weight:600;margin-bottom:8px;">' + escHtml(name) + '</div>' +
                '<div style="color:#888;margin-bottom:20px;">Preview not available for this file type.</div>' +
                '<a href="' + url + '" download="' + escHtml(name) + '" class="doc-btn-green" style="text-decoration:none;padding:10px 22px;border-radius:8px;font-size:0.9rem;font-weight:600;display:inline-block;">Download file</a>' +
            '</div>';
        }

        document.getElementById('docPreviewModal').classList.add('show');
    };

    window.docClosePreview = function () {
        document.getElementById('docPreviewModal').classList.remove('show');
        document.getElementById('docPreviewBody').innerHTML = '<span style="color:#888;">Loading…</span>';
    };

    window.docCloseDelete = function () {
        docPendingDelete = null;
        document.getElementById('docDeleteModal').classList.remove('show');
        // Reset modal back to file delete defaults
        var modalHead = document.querySelector('#docDeleteModal .doc-modal-head');
        if (modalHead) modalHead.firstChild.textContent = 'Delete file';
        var p = document.querySelector('#docDeleteModal .doc-modal-body p');
        if (p) p.innerHTML = 'Are you sure you want to delete <strong id="docDeleteName"></strong>? This cannot be undone.';
    };
    window.docConfirmDelete = function () {
        if (!docPendingDelete) return;
        var btn = document.getElementById('docDeleteConfirmBtn');
        btn.disabled    = true;
        btn.textContent = 'Deleting…';

        var isFolder = !!docPendingDelete.isFolder;
        var url = isFolder
            ? FOLDERS_BASE + '/' + docPendingDelete.id + '/delete'
            : DOWNLOAD_BASE + '/' + docPendingDelete.id + '/delete';

        fetch(url, {
            method:  'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': DOC_CSRF, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' },
            body:    JSON.stringify({}),
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                showToast(isFolder ? '✓ Folder deleted.' : '✓ File deleted.', 'success');
                var deletedId = docPendingDelete.id;
                docCloseDelete();
                if (!isFolder) {
                    delete docSelectedIds['file_' + deletedId];
                    updateDownloadBtn();
                }
                docLoadFiles();
            } else {
                showToast('Error: ' + (data.message || 'Unknown error'), 'error');
            }
            btn.disabled    = false;
            btn.textContent = 'Delete';
        })
        .catch(function () {
            showToast('Network error. Please try again.', 'error');
            btn.disabled    = false;
            btn.textContent = 'Delete';
        });
    };

    /* ── Helpers ────────────────────────────────────────── */
    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function humanSize(bytes) {
        if (bytes < 1024)       return bytes + ' B';
        if (bytes < 1048576)    return (bytes / 1024).toFixed(1) + ' KB';
        if (bytes < 1073741824) return (bytes / 1048576).toFixed(1) + ' MB';
        return (bytes / 1073741824).toFixed(1) + ' GB';
    }

    function showDocError(msg) {
        var tbody = document.getElementById('docTableBody');
        tbody.innerHTML = '<tr><td colspan="6"><div class="doc-empty" style="color:#e74c5e;">' + escHtml(msg) + '</div></td></tr>';
    }

})();
</script>


    </div>
</div>

{{-- ===== EMPLOYMENT MODALS ===== --}}

{{-- 1. Hours of work summary modal --}}
<div class="emc-overlay" id="hoursModalOverlay">
    <div class="emc-box">
        <div class="emc-header">
            <h3 id="hoursModalTitle">{{ $edf('working_pattern') ?: 'Hours of work' }} summary</h3>
            <button type="button" class="emc-close" onclick="closeHoursModal()">&times;</button>
        </div>
        <div class="emc-body">
            <div class="emc-pattern-meta"></div>
            <div class="emc-pattern-start">Pattern start <strong id="hoursPatternStart"></strong></div>
            <div id="hoursDaysList"></div>
            <div class="emc-repeat-row">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                <span>Repeats from <strong id="hoursRepeatFrom"></strong></span>
            </div>
        </div>
    </div>
</div>

{{-- 2. History modal (empty state by default) --}}
<div class="emc-overlay" id="historyModalOverlay">
    <div class="emc-box">
        <div class="emc-header">
            <h3>{{ $employee->first_name }}'s previous contracts</h3>
            <button type="button" class="emc-close" onclick="closeHistoryModal()">&times;</button>
        </div>
        <div class="emc-body">
            <div class="emc-tabs">
                <div class="emc-tab active">Contracts</div>
            </div>
            <div id="historyContent">
                {{-- Empty state (default) — replace this block with a loop once contract history is persisted --}}
                <div class="emc-empty">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    <div class="emc-empty-text">Sorry, no previous contracts exist for {{ $employee->first_name }}</div>
                </div>

                {{-- Example of how a populated history item would render (kept for future reference, hidden for now):
                <div class="emc-history-item">
                    <div>
                        <div class="emc-history-item-title">Fixed contract — Mon-Fri 9-5</div>
                        <div class="emc-history-item-meta">Effective 01 Jan 2024 – 30 Jun 2026 · 35 hrs/week</div>
                    </div>
                </div>
                --}}
            </div>
        </div>
    </div>
</div>

{{-- 3. Edit contract modal (multi-step) --}}
<div class="emc-overlay" id="editContractOverlay">
    <div class="emc-box wide" id="editContractBox">
        {{-- Step content is fully swapped by JS via editStep() --}}
    </div>
</div>

{{-- 4. Simple edit modal (Role information / Pay details) — single form, reused by both --}}
<div class="emc-overlay" id="simpleEditOverlay">
    <div class="emc-box" id="simpleEditBox">
        {{-- Content rendered by JS via openSimpleEditModal(target) --}}
    </div>
</div>

{{-- 5. Generic history modal (Role / Pay) — table with empty state, reused --}}
<div class="emc-overlay" id="genericHistoryOverlay">
    <div class="emc-box" id="genericHistoryBox">
        {{-- Content rendered by JS via openGenericHistoryModal(target) --}}
    </div>
</div>

{{-- Toast notification --}}
<div id="empToast"></div>

{{-- Delete emergency contact confirmation modal --}}
<div class="emc-overlay" id="deleteContactOverlay">
    <div class="emc-box" style="max-width:440px;">
        <div class="emc-header">
            <h3>Delete contact</h3>
            <button type="button" class="emc-close" onclick="closeDeleteContactModal()">&times;</button>
        </div>
        <div class="emc-body">
            <p style="margin:0;font-size:0.92rem;color:#333;">Are you sure you want to delete <strong id="deleteContactName"></strong>? This cannot be undone.</p>
        </div>
        <div class="emc-footer">
            <button type="button" class="emc-btn-outline" onclick="closeDeleteContactModal()">Cancel</button>
            <button type="button" class="emc-btn-primary" id="deleteContactConfirmBtn" style="background:#e74c5e;">Delete</button>
        </div>
    </div>
</div>

{{-- 6. Delete profile picture confirmation modal --}}
<div class="emc-overlay" id="deleteAvatarOverlay">
    <div class="emc-box" style="max-width:480px;">
        <div class="emc-header">
            <h3>Delete profile picture</h3>
            <button type="button" class="emc-close" onclick="closeDeleteAvatarModal()">&times;</button>
        </div>
        <div class="emc-body">
            <p style="margin:0;font-size:0.92rem;color:#333;">Are you sure you want to delete the profile picture?</p>
        </div>
        <div class="emc-footer">
            <button type="button" class="emc-btn-outline" onclick="closeDeleteAvatarModal()">Cancel</button>
            <button type="button" class="emc-btn-primary" id="deleteAvatarConfirmBtn" style="background:#e74c5e;" onclick="confirmDeleteAvatar()">Delete</button>
        </div>
    </div>
</div>

{{-- 7. Add new emergency contact modal --}}
<div class="emc-overlay" id="addContactOverlay">
    <div class="emc-box" id="addContactBox">
        {{-- Content rendered by JS via openAddContactModal() --}}
    </div>
</div>

{{-- 8. Change employee status modal --}}
<div class="emc-overlay" id="statusModalOverlay">
    <div class="emc-box" style="max-width:480px;">
        <div class="emc-header">
            <h3>Change employee status</h3>
            <button type="button" class="emc-close" onclick="closeStatusModal()">&times;</button>
        </div>
        <div class="emc-body">
            <div class="emc-field">
                <label>Status</label>
                <select id="statusModalSelect" class="emc-select" style="width:100%;" onchange="onStatusModalChange()">
                    <option value="active">Active</option>
                    <option value="probation">Probation</option>
                    <option value="on-leave">On Leave</option>
                    <option value="joining-soon">Joining Soon</option>
                    <option value="terminated">Terminated</option>
                </select>
            </div>
            <p id="statusModalWarning" style="display:none;margin:0;font-size:0.85rem;color:#b91c1c;background:#fee2e2;border-radius:6px;padding:10px 12px;">
                Marking this employee as <strong>Terminated</strong> shows them as no longer active across the system (e.g. hidden from active rosters). Their profile and records are kept — this does not delete any data and can be reversed here at any time.
            </p>
        </div>
        <div class="emc-footer">
            <button type="button" class="emc-btn-outline" onclick="closeStatusModal()">Cancel</button>
            <button type="button" class="emc-btn-primary" id="statusModalSaveBtn" onclick="submitStatusModal()">Save</button>
        </div>
    </div>
</div>


<script>
// Tab switching
document.querySelectorAll('.bhr-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.bhr-tab').forEach(function(t) { t.classList.remove('active'); });
        document.querySelectorAll('.bhr-tab-pane').forEach(function(p) { p.classList.remove('active'); });
        this.classList.add('active');
        var tabName = this.getAttribute('data-tab');
        var target = document.getElementById('tab-' + tabName);
        if (target) target.classList.add('active');
        // Save active tab to URL hash without page reload
        history.replaceState(null, '', window.location.pathname + '#' + tabName);
    });
});

// Restore active tab from URL hash on page load
(function() {
    var hash = window.location.hash.replace('#', '');
    if (hash) {
        var tab = document.querySelector('.bhr-tab[data-tab="' + hash + '"]');
        var pane = document.getElementById('tab-' + hash);
        if (tab && pane) {
            document.querySelectorAll('.bhr-tab').forEach(function(t) { t.classList.remove('active'); });
            document.querySelectorAll('.bhr-tab-pane').forEach(function(p) { p.classList.remove('active'); });
            tab.classList.add('active');
            pane.classList.add('active');
            // If we're landing directly on the Documents tab (e.g. via a
            // bookmarked/shared link ending in #documents), the documents
            // list would otherwise never load — trigger it here.
            if (hash === 'documents' && typeof window.docEnsureLoaded === 'function') {
                window.docEnsureLoaded();
            }
        }
    }
})();

function reloadSameTab() {
    // Preserve current active tab across reload via URL hash
    var activeTab = document.querySelector('.bhr-tab.active');
    var hash = activeTab ? '#' + activeTab.getAttribute('data-tab') : '';
    var targetUrl = window.location.pathname + hash;
    var currentUrl = window.location.pathname + window.location.hash;
    if (targetUrl === currentUrl) {
        // Setting location.href to the exact same URL (same path AND hash)
        // is a no-op in most browsers — nothing actually reloads. Force a
        // real reload instead in that case.
        window.location.reload();
    } else {
        window.location.href = targetUrl;
    }
}

// Collapse toggle
function toggleCollapse(header) {
    header.classList.toggle('open');
    var body = header.nextElementSibling;
    if (body) body.classList.toggle('open');
}

// Employment card collapse/expand
function empToggle(headerEl) {
    var card = headerEl.closest('.emp-card');
    if (card) card.classList.toggle('collapsed');
}

// Yes/No pill selector (visual)
function empPickYN(optEl) {
    var group = optEl.parentElement;
    group.querySelectorAll('.emp-yn-opt').forEach(function(o) { o.classList.remove('sel'); });
    optEl.classList.add('sel');
}

// Absence filter pills - switch which absence pane is shown
function setAbsenceFilter(btn) {
    document.querySelectorAll('.bhr-pill').forEach(function(p) { p.classList.remove('active'); });
    btn.classList.add('active');

    var target = btn.getAttribute('data-target');
    document.querySelectorAll('.abs-pane').forEach(function(pane) { pane.classList.remove('active'); });
    var activePane = document.getElementById('abs-' + target);
    if (activePane) activePane.classList.add('active');

    // The "Period" dropdown only appears for Personal / carer's
    var period = document.getElementById('absPeriod');
    if (period) period.classList.toggle('show', target === 'personal');
}

// Absence history view (List / Month / Year) - toggle active state
function setHistoryView(btn) {
    var group = btn.parentElement;
    group.querySelectorAll('.bhr-seg-btn').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    var view = btn.textContent.trim().toLowerCase();
    var list = document.getElementById('absHistList');
    var month = document.getElementById('absHistMonth');
    var year = document.getElementById('absHistYear');
    if (list) list.style.display = (view === 'list') ? 'block' : 'none';
    if (month) month.style.display = (view === 'month') ? 'block' : 'none';
    if (year) year.style.display = (view === 'year') ? 'block' : 'none';
    if (view === 'month') renderMonth();
    if (view === 'year') renderYear();
}

/* ============================================================
   ABSENCE CALENDAR (Month / Year views)
   ------------------------------------------------------------
   PUBLIC HOLIDAYS — Bali, Indonesia.
   Fixed-date national holidays are reliable. Religious holidays
   (Nyepi, Idul Fitri/Adha, Waisak, Galungan, Kuningan, etc.)
   follow the Saka / Hijri / Pawukon calendars and CHANGE EVERY
   YEAR — the dates marked "VERIFY" must be confirmed against the
   official Indonesian government calendar and updated yearly.
   To add/edit a holiday, just add a line: {date:'YYYY-MM-DD', name:'...'}
   ============================================================ */
var PUBLIC_HOLIDAYS = [
    // --- Fixed-date national holidays (reliable) ---
    { date: '2026-01-01', name: 'Tahun Baru Masehi' },
    { date: '2026-05-01', name: 'Hari Buruh' },
    { date: '2026-06-01', name: 'Hari Lahir Pancasila' },
    { date: '2026-08-17', name: 'Hari Kemerdekaan RI' },
    { date: '2026-12-25', name: 'Hari Raya Natal' },
    // --- Christian holidays computed from Easter 2026 (reliable) ---
    { date: '2026-04-03', name: 'Wafat Isa Almasih' },
    { date: '2026-05-14', name: 'Kenaikan Isa Almasih' },
    // --- VERIFY: Saka / Hijri / Pawukon based — confirm & update each year ---
    { date: '2026-01-16', name: 'Isra Mikraj (VERIFY)' },
    { date: '2026-03-19', name: 'Hari Suci Nyepi (VERIFY)' },
    { date: '2026-03-20', name: 'Idul Fitri (VERIFY)' },
    { date: '2026-03-21', name: 'Idul Fitri (VERIFY)' },
    { date: '2026-05-27', name: 'Idul Adha (VERIFY)' },
    { date: '2026-05-31', name: 'Hari Raya Waisak (VERIFY)' },
    { date: '2026-06-16', name: 'Tahun Baru Islam (VERIFY)' },
    { date: '2026-08-25', name: 'Maulid Nabi Muhammad (VERIFY)' }
    // Add Bali Galungan & Kuningan here once confirmed, e.g.:
    // { date: '2026-MM-DD', name: 'Galungan (VERIFY)' },
    // { date: '2026-MM-DD', name: 'Kuningan (VERIFY)' },
];

var MONTH_NAMES = ['January','February','March','April','May','June','July','August','September','October','November','December'];
var WEEKDAYS = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];

/* Render public holidays list in the absence history section */
(function renderPublicHolidaysList() {
    var calYear = new Date().getFullYear();
    var yearHols = PUBLIC_HOLIDAYS.filter(function(h) {
        return h.date.indexOf(calYear + '-') === 0;
    }).sort(function(a, b) { return a.date.localeCompare(b.date); });

    var label = document.getElementById('pubHolLabel');
    var body  = document.getElementById('pubHolBody');
    if (!label || !body) return;

    label.textContent = 'Public holidays (' + yearHols.length + ')';

    if (yearHols.length === 0) {
        body.innerHTML = '<div class="bhr-nothing"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width:22px;height:22px;color:#ccc;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/></svg>No public holidays found for ' + calYear + '.</div>';
        return;
    }

    var dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
    var monthNamesShort = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

    var html = '';
    yearHols.forEach(function(h) {
        var d    = new Date(h.date + 'T00:00:00');
        var day  = dayNames[d.getDay()];
        var mon  = monthNamesShort[d.getMonth()];
        var date = d.getDate();
        var isPast = d < new Date();
        html += '<div class="abs-item">' +
            '<div class="abs-item-main">' +
                '<div class="abs-item-type" style="color:#7c5cd6;">' + h.name + '</div>' +
                '<div class="abs-item-meta">' + day + ', ' + date + ' ' + mon + ' ' + calYear + '</div>' +
            '</div>' +
            '<div class="abs-item-right">' +
                '<span class="abs-item-badge" style="background:#f3f0ff;color:#7c5cd6;">' + (isPast ? 'Past' : 'Upcoming') + '</span>' +
            '</div>' +
        '</div>';
    });
    body.innerHTML = html;
})();

/* Real absence data from the server */
@php
    $absencesForJs = $absences->map(function ($a) {
        return [
            'type'    => $a->type,
            'start'   => optional($a->start_date)->format('Y-m-d'),
            'end'     => optional($a->end_date)->format('Y-m-d'),
            'reason'  => $a->reason,
            'ongoing' => (bool) $a->is_ongoing,
        ];
    })->values();
@endphp
var ABSENCES = @json($absencesForJs);
var ABSENCE_COLORS = { annual: '#9cd3f0', personal: '#e0c14c', lateness: '#d9685f', other: '#8fb3a4' };
var ABSENCE_LABELS = { annual: 'Annual leave', personal: "Personal / carer's", lateness: 'Lateness', other: 'Other' };

function calDateInRange(ds, a) {
    if (!a.start) return false;
    var end = a.end || (a.ongoing ? '9999-12-31' : a.start);
    return ds >= a.start && ds <= end;
}
function calAbsencesOn(ds) { return ABSENCES.filter(function (a) { return calDateInRange(ds, a); }); }
function calRangesIntersect(aStart, aEnd, pStart, pEnd) { return aStart <= pEnd && aEnd >= pStart; }
function calCountType(type, pStart, pEnd) {
    return ABSENCES.filter(function (a) {
        if (a.type !== type || !a.start) return false;
        var aEnd = a.end || (a.ongoing ? '9999-12-31' : a.start);
        return calRangesIntersect(a.start, aEnd, pStart, pEnd);
    }).length;
}

var calNow = new Date();
var calYear = calNow.getFullYear();
var calMonth = calNow.getMonth();
var calShowWeekends = true;

function calPad(n) { return n < 10 ? '0' + n : '' + n; }
function calDateStr(y, m, d) { return y + '-' + calPad(m + 1) + '-' + calPad(d); }
function calHolidays(dateStr) { return PUBLIC_HOLIDAYS.filter(function (h) { return h.date === dateStr; }); }
function calIsToday(y, m, d) { var t = new Date(); return y === t.getFullYear() && m === t.getMonth() && d === t.getDate(); }

// Build a 6-week matrix for a month (Mon-first). Returns array of cells.
function calMatrix(year, month) {
    var first = new Date(year, month, 1);
    var startDow = (first.getDay() + 6) % 7; // Mon=0
    var daysInMonth = new Date(year, month + 1, 0).getDate();
    var cells = [];
    // leading days (previous month)
    var prevDays = new Date(year, month, 0).getDate();
    for (var i = 0; i < startDow; i++) {
        var d = prevDays - startDow + 1 + i;
        var pm = month === 0 ? 11 : month - 1;
        var py = month === 0 ? year - 1 : year;
        cells.push({ day: d, y: py, m: pm, inMonth: false });
    }
    for (var dd = 1; dd <= daysInMonth; dd++) cells.push({ day: dd, y: year, m: month, inMonth: true });
    // trailing days (next month) to complete final week
    var nd = 1;
    while (cells.length % 7 !== 0) {
        var nm = month === 11 ? 0 : month + 1;
        var ny = month === 11 ? year + 1 : year;
        cells.push({ day: nd++, y: ny, m: nm, inMonth: false });
    }
    return cells;
}

function calCountHolidaysInMonth(year, month) {
    return PUBLIC_HOLIDAYS.filter(function (h) { return h.date.indexOf(year + '-' + calPad(month + 1) + '-') === 0; }).length;
}
function calCountHolidaysInYear(year) {
    return PUBLIC_HOLIDAYS.filter(function (h) { return h.date.indexOf(year + '-') === 0; }).length;
}

function calLegendHTML(counts) {
    return '<div class="cal-legend">' +
        '<span class="cal-leg"><span class="cal-dot" style="background:#9cd3f0;"></span>Annual leave: ' + counts.annual + '</span>' +
        '<span class="cal-leg"><span class="cal-dot" style="background:#e0c14c;"></span>Personal / carer\'s leave: ' + counts.personal + '</span>' +
        '<span class="cal-leg"><span class="cal-dot" style="background:#d9685f;"></span>Lateness: ' + counts.lateness + '</span>' +
        '<span class="cal-leg"><span class="cal-dot" style="background:#8fb3a4;"></span>Other: ' + counts.other + '</span>' +
        '<span class="cal-leg"><span class="cal-dot" style="background:#7c5cd6;"></span>Public holidays: ' + counts.holidays + '</span>' +
        '<div class="cal-toggles">' +
        '<label class="cal-toggle"><span class="cal-switch"><input type="checkbox" ' + (calShowWeekends ? 'checked' : '') + ' onchange="calToggleWeekends(this)"><span class="cal-sl"></span></span>Show weekends</label>' +
        '</div></div>';
}

function renderMonth() {
    var c = document.getElementById('absHistMonth');
    if (!c) return;
    var nw = calShowWeekends ? '' : ' no-weekend';
    var html = '';
    // top nav
    html += '<div class="cal-top">' +
        '<button class="cal-nav-btn" onclick="calPrevMonth()"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg></button>' +
        '<button class="cal-nav-btn" onclick="calNextMonth()"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg></button>' +
        '<span class="cal-today" onclick="calGoToday()">Today</span>' +
        '<span class="cal-title">' + MONTH_NAMES[calMonth] + ' ' + calYear + '</span>' +
        '</div>';
    var mEnd = new Date(calYear, calMonth + 1, 0).getDate();
    var pStart = calDateStr(calYear, calMonth, 1);
    var pEnd = calDateStr(calYear, calMonth, mEnd);
    html += calLegendHTML({
        annual: calCountType('annual', pStart, pEnd),
        personal: calCountType('personal', pStart, pEnd),
        lateness: calCountType('lateness', pStart, pEnd),
        other: calCountType('other', pStart, pEnd),
        holidays: calCountHolidaysInMonth(calYear, calMonth)
    });
    // weekday header
    html += '<div class="cal-weekhead' + nw + '">';
    for (var w = 0; w < 7; w++) html += '<div class="' + (w >= 5 ? 'cal-weekend' : '') + '">' + WEEKDAYS[w] + '</div>';
    html += '</div>';
    // grid
    html += '<div class="cal-grid' + nw + '">';
    var cells = calMatrix(calYear, calMonth);
    cells.forEach(function (cell, idx) {
        var dow = idx % 7;
        var ds = calDateStr(cell.y, cell.m, cell.day);
        var hols = calHolidays(ds);
        var cls = 'cal-cell' + (cell.inMonth ? '' : ' out') + (dow >= 5 ? ' cal-weekend' : '') + (calIsToday(cell.y, cell.m, cell.day) ? ' today' : '');
        html += '<div class="' + cls + '"><span class="cal-num">' + cell.day + '</span>';
        hols.forEach(function (h) { html += '<div class="cal-hol-bar"><span class="cal-hol-label">' + h.name + '</span></div>'; });
        if (cell.inMonth) {
            calAbsencesOn(ds).forEach(function (a) {
                html += '<div class="cal-hol-bar" style="background:' + (ABSENCE_COLORS[a.type] || '#999') + ';"><span class="cal-hol-label">' + (ABSENCE_LABELS[a.type] || a.type) + (a.reason ? ' — ' + a.reason : '') + '</span></div>';
            });
        }
        html += '</div>';
    });
    html += '</div>';
    c.innerHTML = html;
}

function renderYear() {
    var c = document.getElementById('absHistYear');
    if (!c) return;
    var nw = calShowWeekends ? '' : ' no-weekend';
    var html = '';
    html += '<div class="cal-top">' +
        '<button class="cal-nav-btn" onclick="calPrevYear()"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/></svg></button>' +
        '<button class="cal-nav-btn" onclick="calNextYear()"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg></button>' +
        '<span class="cal-today" onclick="calGoToday()">Today</span>' +
        '<span class="cal-title">January \u2013 December ' + calYear + '</span>' +
        '</div>';
    var yStart = calYear + '-01-01', yEnd = calYear + '-12-31';
    html += calLegendHTML({
        annual: calCountType('annual', yStart, yEnd),
        personal: calCountType('personal', yStart, yEnd),
        lateness: calCountType('lateness', yStart, yEnd),
        other: calCountType('other', yStart, yEnd),
        holidays: calCountHolidaysInYear(calYear)
    });
    html += '<div class="cal-year-grid">';
    for (var m = 0; m < 12; m++) {
        html += '<div class="cal-mini"><h4>' + MONTH_NAMES[m] + '</h4>';
        html += '<div class="cal-mini-head' + nw + '">';
        for (var w = 0; w < 7; w++) html += '<div class="' + (w >= 5 ? 'cal-weekend' : '') + '">' + WEEKDAYS[w].charAt(0) + '</div>';
        html += '</div><div class="cal-mini-grid' + nw + '">';
        var cells = calMatrix(calYear, m);
        cells.forEach(function (cell, idx) {
            var dow = idx % 7;
            var ds = calDateStr(cell.y, cell.m, cell.day);
            var isHol = cell.inMonth && calHolidays(ds).length > 0;
            var absHere = cell.inMonth ? calAbsencesOn(ds) : [];
            var cls = 'cal-mini-cell' + (cell.inMonth ? '' : ' out') + (dow >= 5 ? ' cal-weekend' : '') + (isHol ? ' hol' : '') + (calIsToday(cell.y, cell.m, cell.day) ? ' today' : '');
            var spanStyle = (!isHol && absHere.length) ? ' style="background:' + (ABSENCE_COLORS[absHere[0].type] || '#999') + ';color:#fff;border-radius:8px;padding:1px 6px;"' : '';
            html += '<div class="' + cls + '"><span' + spanStyle + '>' + cell.day + '</span></div>';
        });
        html += '</div></div>';
    }
    html += '</div>';
    c.innerHTML = html;
}

function calPrevMonth() { calMonth--; if (calMonth < 0) { calMonth = 11; calYear--; } renderMonth(); }
function calNextMonth() { calMonth++; if (calMonth > 11) { calMonth = 0; calYear++; } renderMonth(); }
function calPrevYear() { calYear--; renderYear(); }
function calNextYear() { calYear++; renderYear(); }
function calGoToday() { var t = new Date(); calYear = t.getFullYear(); calMonth = t.getMonth(); renderMonth(); renderYear(); }
function calToggleWeekends(cb) { calShowWeekends = cb.checked; renderMonth(); renderYear(); }

/* ======================================================================
   EMPLOYMENT MODALS
   ====================================================================== */
var EMPLOYEE_FIRST_NAME = @json($employee->first_name);
var EMPLOYEE_ID = @json($employee->id);
var EMPLOYEE_UPDATE_URL = "{{ route('admin.linkers-hub.update-employee', ['id' => $employee->id]) }}";
var SEND_REG_EMAIL_URL = "{{ route('admin.linkers-hub.send-registration-email') }}";

/* Existing data for Contact information & Personal information edit modals */
@php
    $__contactData = array(
        'account_email'   => $employee->email,
        'personal_email'  => $employee->personal_email,
        'home_phone'      => $employee->home_phone,
        'mobile_phone'    => $employee->phone,
        'work_phone'      => $employee->work_phone,
        'work_extension'  => $employee->work_extension,
    );
    $__personalData = array(
        'title'       => $employee->title,
        'first_name'  => $employee->first_name,
        'middle_name' => $employee->middle_name,
        'last_name'   => $employee->last_name,
        'dob'         => $employee->birth_info,
        'gender'      => $employee->gender,
        'country'     => $employee->country,
        'address_1'   => $employee->address_1,
        'address_2'   => $employee->address_2,
        'address_3'   => $employee->address_3,
        'city'        => $employee->city,
        'territory'   => $employee->territory,
        'postcode'    => $employee->postcode,
    );
@endphp
var CONTACT_DATA = @json($__contactData);
var PERSONAL_DATA = @json($__personalData);
var PERSONAL_COUNTRY_LIST = @json($countryList);
@php
    $__roleData = array(
        'position_title'           => $employee->position_title,
        'employment_basis'         => $employee->employment_basis,
        'start_date'               => $employee->start_date ? $employee->start_date->format('Y-m-d') : '',
        'probation_required'       => $employee->probation_required,
        'probation_end_date'       => $employee->probation_end_date ? $employee->probation_end_date->format('Y-m-d') : '',
        'notice_during_probation'  => $employee->notice_during_probation,
        'notice_period'            => $employee->notice_period,
        'division_id'              => $employee->division_id,
        'sub_division_id'          => $employee->sub_division_id,
        'position_id'              => $employee->position_id,
    );
    $__payData = array(
        'salary'        => $pgf('salary'),
        'pay_rate'      => $pgf('pay_rate'),
        'pay_frequency' => $pgf('pay_frequency'),
        'effective_from'=> $edf('effective_from') ? $edf('effective_from')->format('Y-m-d') : '',
        'salary_reason' => $edf('salary_reason'),
    );
@endphp
var ROLE_DATA = @json($__roleData);
var PAY_DATA  = @json($__payData);
var ALL_DIVISIONS = @json($allDivisions);
var ALL_SUBDIVISIONS = @json($allSubDivisions);
var ALL_POSITIONS = @json($allPositions);

/* ---------- 1. Hours of work summary modal ---------- */
/* All data comes from the employee record — nothing is hardcoded. */
@php
$__rawSchedule = $edf('working_schedule');
$__schedule    = null;
if ($__rawSchedule) {
    $__decoded = is_array($__rawSchedule) ? $__rawSchedule : json_decode($__rawSchedule, true);
    if (is_array($__decoded)) $__schedule = $__decoded;
}
$__hoursData = [
    'pattern'            => $edf('working_pattern'),
    'schedule'           => $__schedule,
    'contracted_hours'   => (int) ($edf('contracted_hours') ?? 0),
    'contracted_minutes' => (int) ($edf('contracted_minutes') ?? 0),
    'start_date'         => $employee->start_date ? $employee->start_date->format('Y-m-d') : null,
    'employee_type'      => $edf('employee_type'),
    'leave_unit'         => $edf('leave_unit'),
];
@endphp
var HOURS_DATA = @json($__hoursData);

function openHoursModal() {
    var pattern   = HOURS_DATA.pattern || 'Not set';
    var chHrs     = HOURS_DATA.contracted_hours  || 0;
    var chMins    = HOURS_DATA.contracted_minutes || 0;
    var startDate = HOURS_DATA.start_date ? new Date(HOURS_DATA.start_date) : null;
    var stored    = HOURS_DATA.schedule || null; // saved per-day schedule from DB
    var today     = new Date();

    // Title
    document.getElementById('hoursModalTitle').textContent = pattern + ' summary';

    // Count working days from stored schedule (or fallback)
    var workingDayCount = 5;
    if (stored) {
        workingDayCount = stored.filter(function(d) { return d.active; }).length;
    }

    // Total hours from contracted_hours, or calculate from schedule
    var totalHrs = chHrs + Math.round(chMins / 60 * 10) / 10;
    if (totalHrs === 0 && stored) {
        stored.filter(function(d) { return d.active; }).forEach(function(d) {
            if (d.start && d.end) {
                var s = d.start.split(':'), e = d.end.split(':');
                var diff = (parseInt(e[0]) * 60 + parseInt(e[1])) - (parseInt(s[0]) * 60 + parseInt(s[1])) - (d.break || 0);
                totalHrs += Math.round(diff / 60 * 10) / 10;
            }
        });
    }

    document.querySelector('#hoursModalOverlay .emc-pattern-meta').innerHTML =
        '<strong>7 day pattern</strong> | <strong>' + workingDayCount + ' working days</strong>' +
        (totalHrs > 0 ? ' totalling <strong>' + totalHrs + ' hrs</strong> excluding breaks' : '');

    // Pattern start = employee start date
    var startFmt = startDate
        ? startDate.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })
        : 'Not set';
    document.getElementById('hoursPatternStart').textContent = startFmt;

    // Build schedule rows
    var schedule = buildSchedule(stored, startDate, today);
    var html = '';
    schedule.forEach(function (d) {
        html += '<div class="emc-day-row">';
        html += '<span class="emc-day-name">' + d.name + '</span>';
        if (d.off) {
            html += '<span class="emc-day-off">Day off</span>';
        } else {
            html += '<span class="emc-day-time">' + d.time + '</span>' +
                    '<span class="emc-day-break">' + (d.brk ? d.brk + ' mins break' : '') + '</span>';
        }
        if (d.today) html += '<span class="emc-today-badge">Today</span>';
        html += '</div>';
    });
    document.getElementById('hoursDaysList').innerHTML = html || '<div style="color:#888;padding:12px 0;">No schedule detail available.</div>';

    // Repeats from: next Monday after today
    var nextMon = new Date(today);
    nextMon.setDate(today.getDate() + ((1 + 7 - today.getDay()) % 7 || 7));
    var repeatFmt = nextMon.toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
    // Use first active day's start/end from stored schedule
    var firstActive = stored ? stored.find(function(d) { return d.active && d.start; }) : null;
    var repeatTime = firstActive ? (firstActive.start + ' - ' + firstActive.end) : '09:00 - 17:00';
    document.getElementById('hoursRepeatFrom').textContent = repeatFmt + ', ' + repeatTime;

    document.getElementById('hoursModalOverlay').classList.add('open');
}

function buildSchedule(storedDays, startDate, today) {
    var ordinals = ['th','st','nd','rd','th','th','th','th','th','th'];
    var dayOrder = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun']; // Mon-first display order
    var dayOfWeekMap = { 'Sun':0,'Mon':1,'Tue':2,'Wed':3,'Thu':4,'Fri':5,'Sat':6 };

    // Base date: start of the week containing startDate (or today)
    var base = startDate ? new Date(startDate) : new Date(today);
    var dow  = base.getDay(); // 0=Sun
    base.setDate(base.getDate() + (dow === 0 ? -6 : 1 - dow)); // rewind to Monday

    if (storedDays && storedDays.length > 0) {
        // Use stored schedule — map each day in Mon-first order
        var storedMap = {};
        storedDays.forEach(function(d) { storedMap[d.day] = d; });

        return dayOrder.map(function(dayName, i) {
            var d = new Date(base);
            d.setDate(base.getDate() + i);
            var dayNum = d.getDate();
            var ord    = ordinals[dayNum % 10] || 'th';
            var isToday = d.toDateString() === today.toDateString();
            var stored  = storedMap[dayName] || { active: false };
            return {
                name:  dayName + ' ' + dayNum + ord,
                time:  stored.active ? (stored.start + ' - ' + stored.end) : '',
                brk:   stored.active ? (stored.break || 0) : 0,
                off:   !stored.active,
                today: isToday && !!stored.active,
            };
        });
    }

    // Fallback: Mon-Fri 09:00-17:00 if no stored schedule
    return dayOrder.map(function(dayName, i) {
        var d = new Date(base);
        d.setDate(base.getDate() + i);
        var dayNum  = d.getDate();
        var ord     = ordinals[dayNum % 10] || 'th';
        var isOff   = i >= 5; // Sat, Sun
        var isToday = d.toDateString() === today.toDateString();
        return {
            name:  dayName + ' ' + dayNum + ord,
            time:  isOff ? '' : '09:00 - 17:00',
            brk:   isOff ? 0 : 60,
            off:   isOff,
            today: isToday && !isOff,
        };
    });
}

function getPatternStartTime(p) {
    var m = (p || '').match(/(\d{1,2})[:\-](\d{2})\s*(am|pm)?/i);
    if (m) {
        var h = parseInt(m[1]); var min = m[2];
        if (m[3] && m[3].toLowerCase() === 'pm' && h < 12) h += 12;
        return (h < 10 ? '0' : '') + h + ':' + min;
    }
    return '09:00'; // default
}

function getPatternEndTime(p) {
    var matches = [];
    var re = /(\d{1,2})[:\-](\d{2})\s*(am|pm)?/ig;
    var m;
    while ((m = re.exec(p || '')) !== null) matches.push(m);
    if (matches.length >= 2) {
        var last = matches[matches.length - 1];
        var h = parseInt(last[1]); var min = last[2];
        if (last[3] && last[3].toLowerCase() === 'pm' && h < 12) h += 12;
        return (h < 10 ? '0' : '') + h + ':' + min;
    }
    return '17:00'; // default
}

function closeHoursModal() { document.getElementById('hoursModalOverlay').classList.remove('open'); }

/* ---------- 2. History modal ---------- */
function openHistoryModal() { document.getElementById('historyModalOverlay').classList.add('open'); }
function closeHistoryModal() { document.getElementById('historyModalOverlay').classList.remove('open'); }

/* ---------- 3. Edit contract modal (multi-step) ---------- */
var editChoice = null; // 'change' or 'correct'
var editSelectedHours = HOURS_DATA.pattern || 'Mon-Fri 9-5';
var editNewContractType = 'Fixed';
var editNewPattern = HOURS_DATA.pattern || 'Mon-Fri 9-5';

function openEditContractModal() {
    editChoice = null;
    renderEditStep('choice');
    document.getElementById('editContractOverlay').classList.add('open');
}
function closeEditContractModal() {
    document.getElementById('editContractOverlay').classList.remove('open');
}


/* Build <option> list for working pattern dropdowns.
   Combines standard presets + custom patterns saved in localStorage. */
var STANDARD_PATTERN_NAMES = [
    'Standard (Mon-Fri)', 'Part Time (Mon-Wed)', 'Shift Work', 'Flexible Hours',
    'Mon-Fri 9-5', 'Staff Regular Hours'
];

function buildPatternOptions(selectedVal) {
    var names = STANDARD_PATTERN_NAMES.slice();
    // Add custom patterns from localStorage
    try {
        var custom = JSON.parse(localStorage.getItem('wtp_patterns') || '[]');
        custom.forEach(function(p) {
            if (names.indexOf(p.name) === -1) names.push(p.name);
        });
    } catch(e) {}
    // Always include the current DB value if not already in the list
    if (selectedVal && names.indexOf(selectedVal) === -1) names.push(selectedVal);
    return names.map(function(n) {
        return '<option value="' + n + '"' + (n === selectedVal ? ' selected' : '') + '>' + n + '</option>';
    }).join('');
}

/* Calculate working days summary from stored schedule */
function patternSummaryText(patternName) {
    var schedule = HOURS_DATA.schedule;
    if (schedule && schedule.length) {
        var activeDays = schedule.filter(function(d) { return d.active; }).length;
        var totalHrs   = 0;
        schedule.filter(function(d) { return d.active && d.start && d.end; }).forEach(function(d) {
            var s = d.start.split(':'), e = d.end.split(':');
            var mins = (parseInt(e[0]) * 60 + parseInt(e[1])) - (parseInt(s[0]) * 60 + parseInt(s[1])) - (d.break || 0);
            totalHrs += Math.round(mins / 60 * 10) / 10;
        });
        return 'Works ' + totalHrs + ' hrs over ' + activeDays + ' days.';
    }
    return 'Works 35 hrs over 5 days.';
}

function renderEditStep(step) {
    var box = document.getElementById('editContractBox');
    var name = EMPLOYEE_FIRST_NAME;

    if (step === 'choice') {
        box.innerHTML =
            '<div class="emc-header"><h3>Update ' + name + '\'s contract</h3><button type="button" class="emc-close" onclick="closeEditContractModal()">&times;</button></div>' +
            '<div class="emc-body">' +
                '<p style="font-size:0.9rem;color:#444;margin:0 0 18px;">Please select below if you would like to change their contracted hours and holiday, or correct errors with their existing contract.</p>' +
                '<div class="emc-choice-card" id="choiceChange" onclick="selectEditChoice(\'change\')">' +
                    '<svg class="emc-choice-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' +
                    '<div class="emc-choice-radio"></div>' +
                    '<div><div class="emc-choice-title">Change employee contract</div><div class="emc-choice-desc">This will change ' + name + '\'s contract from the date you specify</div></div>' +
                '</div>' +
                '<div class="emc-choice-card" id="choiceCorrect" onclick="selectEditChoice(\'correct\')">' +
                    '<svg class="emc-choice-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>' +
                    '<div class="emc-choice-radio"></div>' +
                    '<div><div class="emc-choice-title">Correct existing contract</div><div class="emc-choice-desc">Make corrections to ' + name + '\'s existing contract</div></div>' +
                '</div>' +
            '</div>' +
            '<div class="emc-footer">' +
                '<button type="button" class="emc-btn-outline" onclick="closeEditContractModal()">Cancel</button>' +
                '<button type="button" class="emc-btn-primary" id="choiceNextBtn" disabled onclick="goToEditStep2()">Next</button>' +
            '</div>';
    }

    if (step === 'correct-1') {
        box.innerHTML =
            stepsHeader('Contract', 'active') + stepsHeader('Summary', '') &&
            '';
        box.innerHTML =
            '<div class="emc-steps">' + stepTab('Contract', true) + stepTab('Summary', false) + '</div>' +
            '<div class="emc-body">' +
                '<h3 style="margin:0 0 18px;font-size:1.15rem;">' + name + '\'s working hours</h3>' +
                '<div class="emc-field-row"><label>Hours of work</label>' +
                    '<select class="emc-select" id="editHoursSelect" onchange="editSelectedHours=this.value;">' +
                        buildPatternOptions(editSelectedHours) +
                    '</select>' +
                '</div>' +
                '<div class="emc-bold-note" id="editHoursSummary">' + patternSummaryText(editSelectedHours) + '</div>' +
            '</div>' +
            '<div class="emc-footer">' +
                '<button type="button" class="emc-btn-outline" onclick="closeEditContractModal()">Cancel</button>' +
                '<button type="button" class="emc-btn-primary" onclick="renderEditStep(\'correct-2\')">Next step</button>' +
            '</div>';
    }

    if (step === 'correct-2') {
        var changed = editSelectedHours !== (HOURS_DATA.pattern || 'Mon-Fri 9-5');
        box.innerHTML =
            '<div class="emc-steps">' + stepTab('Contract', false) + stepTab('Summary', true) + '</div>' +
            '<div class="emc-body">' +
                '<h3 style="margin:0 0 18px;font-size:1.15rem;">Summary</h3>' +
                '<div class="emc-summary-box">' +
                    '<div class="emc-summary-grid">' +
                        '<div><div class="emc-summary-label">Employee total hours</div><div class="emc-summary-value">35 hrs</div></div>' +
                        '<div><div class="emc-summary-label">Public holidays for</div><div class="emc-summary-value">Indonesia<br><span style="font-size:0.8rem;color:#888;">Not deducted from entitlement</span></div></div>' +
                        '<div><div class="emc-summary-label">Employee\'s Hours of work</div><div class="emc-summary-value">' + editSelectedHours + '<br><span style="font-size:0.8rem;color:#888;">starts from 01 Jan 2026</span></div></div>' +
                    '</div>' +
                '</div>' +
                (changed ? '' : '<div class="emc-nochange-banner">No changes were made.</div>') +
            '</div>' +
            '<div class="emc-footer split">' +
                '<div class="emc-footer-left">' +
                    '<button type="button" class="emc-btn-outline" onclick="renderEditStep(\'correct-1\')">Back</button>' +
                    '<button type="button" class="emc-btn-outline" onclick="closeEditContractModal()">Cancel</button>' +
                '</div>' +
                '<button type="button" class="emc-btn-primary" id="correctSaveBtn" onclick="submitCorrectContract()">Save and update</button>' +
            '</div>';
    }

    if (step === 'change-1') {
        var curType = ROLE_DATA.employment_basis || 'Fixed';
        box.innerHTML =
            '<div class="emc-steps">' + stepTab('Contract', true) + stepTab('Summary', false) + '</div>' +
            '<div class="emc-body">' +
                '<h3 style="margin:0 0 18px;font-size:1.15rem;">New contract</h3>' +
                '<div class="emc-field"><label>Contract start date</label><input type="date" class="emc-input" id="editNewStartDate" style="width:100%;" value="' + (ROLE_DATA.start_date || '') + '"></div>' +
                '<div class="emc-field"><label>Employment type</label>' +
                    '<select class="emc-select" id="editNewType" style="width:100%;" onchange="editNewContractType=this.value;">' +
                        '<option value="Fixed"' + (curType === 'Fixed' ? ' selected' : '') + '>Fixed</option>' +
                        '<option value="Casual"' + (curType === 'Casual' ? ' selected' : '') + '>Casual</option>' +
                        '<option value="Variable"' + (curType === 'Variable' ? ' selected' : '') + '>Variable</option>' +
                    '</select>' +
                '</div>' +
                '<div class="emc-info-banner">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>' +
                    '<span>Employees on a repeating working time pattern who work fixed, predictable numbers of hours and have a fixed leave entitlement.</span>' +
                '</div>' +
                '<div class="emc-field"><label>Working time pattern</label>' +
                    '<select class="emc-select" id="editNewPattern" style="width:100%;" onchange="editNewPattern=this.value;">' +
                        buildPatternOptions(editNewPattern) +
                    '</select>' +
                '</div>' +
                '<div class="emc-bold-note">' + patternSummaryText(editNewPattern) + ' Public holidays not deducted from entitlement.</div>' +
            '</div>' +
            '<div class="emc-footer">' +
                '<button type="button" class="emc-btn-outline" onclick="closeEditContractModal()">Cancel</button>' +
                '<button type="button" class="emc-btn-primary" onclick="editNewContractType=document.getElementById(\'editNewType\').value; renderEditStep(\'change-2\')">Next step</button>' +
            '</div>';
        editNewContractType = curType;
    }

    if (step === 'change-2') {
        box.innerHTML =
            '<div class="emc-steps">' + stepTab('Contract', false) + stepTab('Summary', true) + '</div>' +
            '<div class="emc-body">' +
                '<h3 style="margin:0 0 18px;font-size:1.15rem;">Summary</h3>' +
                '<div class="emc-summary-box">' +
                    '<div class="emc-summary-grid">' +
                        '<div><div class="emc-summary-label">Employment type</div><div class="emc-summary-value">' + editNewContractType + '</div></div>' +
                        '<div><div class="emc-summary-label">Contract effective date</div><div class="emc-summary-value" id="editEffectiveDateOut">Not set</div></div>' +
                        '<div><div class="emc-summary-label">Employee contracted hours</div><div class="emc-summary-value">35 hours</div></div>' +
                        '<div><div class="emc-summary-label">Employee\'s working time pattern</div><div class="emc-summary-value">' + editNewPattern + '</div></div>' +
                    '</div>' +
                '</div>' +
                '<div class="emc-field-row"><label>Reason for change <span style="color:#e74c5e;">*</span></label>' +
                    '<select class="emc-select" id="editReasonSelect">' +
                        '<option value="" selected>Please select</option>' +
                        '<option value="Promotion">Promotion</option>' +
                        '<option value="Role change">Role change</option>' +
                        '<option value="Hours adjustment">Hours adjustment</option>' +
                        '<option value="Other">Other</option>' +
                    '</select>' +
                '</div>' +
                '<div class="emc-field"><label>Notes</label><textarea class="emc-textarea" placeholder="Add any additional notes"></textarea></div>' +
                '<div class="emc-info-banner" style="background:#eef6f2;border-color:#d3e8de;">' +
                    '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>' +
                    '<span><strong>' + name + '\'s</strong> new contract will now be added.</span>' +
                '</div>' +
                '<label class="emc-confirm-row"><input type="checkbox" id="editConfirmCheckbox" onchange="document.getElementById(\'editSaveBtn\').disabled = !this.checked;"> I understand that this will permanently change ' + name + '\'s contract</label>' +
            '</div>' +
            '<div class="emc-footer split">' +
                '<div class="emc-footer-left">' +
                    '<button type="button" class="emc-btn-outline" onclick="renderEditStep(\'change-1\')">Back</button>' +
                    '<button type="button" class="emc-btn-outline" onclick="closeEditContractModal()">Cancel</button>' +
                '</div>' +
                '<button type="button" class="emc-btn-primary" id="editSaveBtn" disabled onclick="submitChangeContract()">Save</button>' +
            '</div>';
    }
}

function stepTab(label, active) {
    return '<div class="emc-step' + (active ? ' active' : '') + '">' + label + '</div>';
}
function stepsHeader() { return ''; } // unused helper kept harmless

function selectEditChoice(choice) {
    editChoice = choice;
    document.getElementById('choiceChange').classList.toggle('selected', choice === 'change');
    document.getElementById('choiceCorrect').classList.toggle('selected', choice === 'correct');
    document.getElementById('choiceNextBtn').disabled = false;
}

function goToEditStep2() {
    if (editChoice === 'correct') renderEditStep('correct-1');
    else if (editChoice === 'change') renderEditStep('change-1');
}

/* ---------- 4. Simple edit modal (Role information / Pay details) ---------- */
var COUNTRY_LIST = ["Afghanistan","Albania","Algeria","Andorra","Angola","Antigua and Barbuda","Argentina","Armenia","Australia","Austria","Azerbaijan","Bahamas","Bahrain","Bangladesh","Barbados","Belarus","Belgium","Belize","Benin","Bhutan","Bolivia","Bosnia and Herzegovina","Botswana","Brazil","Brunei","Bulgaria","Burkina Faso","Burundi","Cabo Verde","Cambodia","Cameroon","Canada","Central African Republic","Chad","Chile","China","Colombia","Comoros","Congo (Brazzaville)","Congo (Kinshasa)","Costa Rica","Croatia","Cuba","Cyprus","Czechia","Denmark","Djibouti","Dominica","Dominican Republic","Ecuador","Egypt","El Salvador","Equatorial Guinea","Eritrea","Estonia","Eswatini","Ethiopia","Fiji","Finland","France","Gabon","Gambia","Georgia","Germany","Ghana","Greece","Grenada","Guatemala","Guinea","Guinea-Bissau","Guyana","Haiti","Honduras","Hungary","Iceland","India","Indonesia","Iran","Iraq","Ireland","Israel","Italy","Jamaica","Japan","Jordan","Kazakhstan","Kenya","Kiribati","Kuwait","Kyrgyzstan","Laos","Latvia","Lebanon","Lesotho","Liberia","Libya","Liechtenstein","Lithuania","Luxembourg","Madagascar","Malawi","Malaysia","Maldives","Mali","Malta","Marshall Islands","Mauritania","Mauritius","Mexico","Micronesia","Moldova","Monaco","Mongolia","Montenegro","Morocco","Mozambique","Myanmar","Namibia","Nauru","Nepal","Netherlands","New Zealand","Nicaragua","Niger","Nigeria","North Korea","North Macedonia","Norway","Oman","Pakistan","Palau","Palestine","Panama","Papua New Guinea","Paraguay","Peru","Philippines","Poland","Portugal","Qatar","Romania","Russia","Rwanda","Saint Kitts and Nevis","Saint Lucia","Saint Vincent and the Grenadines","Samoa","San Marino","Sao Tome and Principe","Saudi Arabia","Senegal","Serbia","Seychelles","Sierra Leone","Singapore","Slovakia","Slovenia","Solomon Islands","Somalia","South Africa","South Korea","South Sudan","Spain","Sri Lanka","Sudan","Suriname","Sweden","Switzerland","Syria","Taiwan","Tajikistan","Tanzania","Thailand","Timor-Leste","Togo","Tonga","Trinidad and Tobago","Tunisia","Turkey","Turkmenistan","Tuvalu","Uganda","Ukraine","United Arab Emirates","United Kingdom","United States","Uruguay","Uzbekistan","Vanuatu","Vatican City","Venezuela","Vietnam","Yemen","Zambia","Zimbabwe"];

function countryOptions(selected) {
    var html = '<option value="">Country of issue</option>';
    COUNTRY_LIST.forEach(function (c) {
        html += '<option value="' + c + '"' + (c === selected ? ' selected' : '') + '>' + c + '</option>';
    });
    return html;
}

/* Sub-division options for the Role information modal, filtered to the given
   division (division and sub-division are hierarchical: a sub-division always
   belongs to exactly one division, see SubDivision::division()). */
function roleSubDivisionOpts(divisionId, selectedSubDivisionId) {
    // Loose equality: division_id may be serialized as either a number
    // or a numeric string depending on the DB driver's fetch mode.
    var options = ALL_SUBDIVISIONS.filter(function(sd) { return divisionId != null && sd.division_id == divisionId; });
    if (!divisionId) {
        return '<option value="">Select a division first</option>';
    }
    if (!options.length) {
        return '<option value="">No sub-divisions for this division</option>';
    }
    return '<option value="">No sub-division</option>' + options.map(function(sd) {
        return '<option value="' + sd.id + '"' + (sd.id == selectedSubDivisionId ? ' selected' : '') + '>' + sd.name + '</option>';
    }).join('');
}

/* Position options, filtered to the given division + sub-division. A position
   tied to a specific sub-division (sub_division_id set) only shows once that
   exact sub-division is chosen; a division-level position (sub_division_id
   null) shows for any/no sub-division under that division. */
function rolePositionOpts(divisionId, subDivisionId, selectedPositionId) {
    if (!divisionId) {
        return '<option value="">Select a division first</option>';
    }
    var options = ALL_POSITIONS.filter(function(p) {
        if (p.division_id != divisionId) return false;
        if (!p.sub_division_id) return true;
        return subDivisionId != null && p.sub_division_id == subDivisionId;
    });
    if (!options.length) {
        return '<option value="">No positions set up yet</option>';
    }
    return '<option value="">No position</option>' + options.map(function(p) {
        return '<option value="' + p.id + '"' + (p.id == selectedPositionId ? ' selected' : '') + '>' + p.name + '</option>';
    }).join('');
}

function onRoleDivisionChange() {
    var divSelect = document.getElementById('role_division_id');
    var subSelect = document.getElementById('role_sub_division_id');
    var posSelect = document.getElementById('role_position_id');
    if (!divSelect || !subSelect || !posSelect) return;
    var divisionId = divSelect.value ? parseInt(divSelect.value, 10) : null;
    subSelect.innerHTML = roleSubDivisionOpts(divisionId, null);
    posSelect.innerHTML = rolePositionOpts(divisionId, null, null);
}

function onRoleSubDivisionChange() {
    var divSelect = document.getElementById('role_division_id');
    var subSelect = document.getElementById('role_sub_division_id');
    var posSelect = document.getElementById('role_position_id');
    if (!divSelect || !subSelect || !posSelect) return;
    var divisionId = divSelect.value ? parseInt(divSelect.value, 10) : null;
    var subDivisionId = subSelect.value ? parseInt(subSelect.value, 10) : null;
    posSelect.innerHTML = rolePositionOpts(divisionId, subDivisionId, null);
}

function openSimpleEditModal(target) {
    var box = document.getElementById('simpleEditBox');
    var name = EMPLOYEE_FIRST_NAME;

    if (target === 'role') {
        var r = ROLE_DATA;
        var contractOpts = ['Full-Time', 'Part-Time', 'Casual', 'Fixed'].map(function(t) {
            // Normalize DB value for comparison (handle legacy "Fixed Term" stored in DB)
            var dbVal = (r.employment_basis || '').replace(/fixed term/i, 'Fixed');
            return '<option' + (t === dbVal ? ' selected' : '') + '>' + t + '</option>';
        }).join('');
        var noticeOpts = function(sel) {
            return ['No notice period', '1 day', '1 week', '2 weeks', '1 month', '3 months'].map(function(n) {
                return '<option' + (n === sel ? ' selected' : '') + '>' + n + '</option>';
            }).join('');
        };
        box.innerHTML =
            '<div class="emc-header"><h3>Edit ' + name + '\'s role information</h3><button type="button" class="emc-close" onclick="closeSimpleEditModal()">&times;</button></div>' +
            '<div class="emc-body">' +
                '<div class="emc-field"><label>Job title</label><input type="text" id="role_position_title" class="emc-input" style="width:100%;" value="' + (r.position_title || '') + '"></div>' +
                '<div class="emc-field"><label>Contract type</label>' +
                    '<select id="role_employment_basis" class="emc-select" style="width:100%;">' + contractOpts + '</select>' +
                '</div>' +
                '<div class="emc-field"><label>Division</label>' +
                    '<select id="role_division_id" class="emc-select" style="width:100%;" onchange="onRoleDivisionChange()">' +
                        '<option value="">No division</option>' +
                        ALL_DIVISIONS.map(function(d) {
                            return '<option value="' + d.id + '"' + (d.id == r.division_id ? ' selected' : '') + '>' + d.name + '</option>';
                        }).join('') +
                    '</select>' +
                '</div>' +
                '<div class="emc-field"><label>Sub-division</label>' +
                    '<select id="role_sub_division_id" class="emc-select" style="width:100%;" onchange="onRoleSubDivisionChange()">' + roleSubDivisionOpts(r.division_id, r.sub_division_id) + '</select>' +
                '</div>' +
                '<div class="emc-field"><label>Position</label>' +
                    '<select id="role_position_id" class="emc-select" style="width:100%;">' + rolePositionOpts(r.division_id, r.sub_division_id, r.position_id) + '</select>' +
                '</div>' +
                '<div class="emc-field"><label>Probation required</label>' +
                    '<div class="emc-pill-group">' +
                        '<div class="emc-pill' + (r.probation_required ? ' selected' : '') + '" id="probYes" onclick="setProbation(true)">Yes</div>' +
                        '<div class="emc-pill' + (!r.probation_required ? ' selected' : '') + '" id="probNo" onclick="setProbation(false)">No</div>' +
                    '</div>' +
                '</div>' +
                '<div id="probationPanel" style="' + (r.probation_required ? '' : 'display:none;') + '">' +
                    '<div class="emc-field"><label>Probation end date</label><input type="date" id="role_probation_end_date" class="emc-input" style="width:100%;" value="' + (r.probation_end_date || '') + '"></div>' +
                    '<div class="emc-field"><label>Notice during probation</label>' +
                        '<select id="role_notice_during_probation" class="emc-select" style="width:100%;">' + noticeOpts(r.notice_during_probation) + '</select>' +
                    '</div>' +
                '</div>' +
                '<div class="emc-field"><label>Notice period</label>' +
                    '<select id="role_notice_period" class="emc-select" style="width:100%;">' + noticeOpts(r.notice_period) + '</select>' +
                '</div>' +
            '</div>' +
            '<div class="emc-footer">' +
                '<button type="button" class="emc-btn-outline" onclick="closeSimpleEditModal()">Cancel</button>' +
                '<button type="button" class="emc-btn-primary" id="roleModalSaveBtn" onclick="submitRoleModal()">Save</button>' +
            '</div>';
    }

    if (target === 'pay') {
        var pd = PAY_DATA;
        // Normalize stored pay_rate (handles legacy "Per month" / "Per hour" values)
        var normRate = (pd.pay_rate || '').replace(/^per /i, '').replace(/^Per /i, '');
        normRate = normRate.charAt(0).toUpperCase() + normRate.slice(1).toLowerCase();
        var rateOpts = ['Hour', 'Day', 'Week', 'Month', 'Year'].map(function(r) {
            return '<option' + (r === normRate ? ' selected' : '') + '>' + r + '</option>';
        }).join('');
        var freqOpts = ['Weekly', 'Fortnightly', 'Monthly'].map(function(f) {
            return '<option' + (f === pd.pay_frequency ? ' selected' : '') + '>' + f + '</option>';
        }).join('');
        box.innerHTML =
            '<div class="emc-header"><h3>Edit ' + name + '\'s salary</h3><button type="button" class="emc-close" onclick="closeSimpleEditModal()">&times;</button></div>' +
            '<div class="emc-body">' +
                '<div class="emc-field"><label>Salary amount</label><input type="number" id="pay_salary" class="emc-input" placeholder="0.00" style="width:100%;" value="' + (pd.salary || '') + '"></div>' +
                '<div class="emc-field"><label>Pay rate</label>' +
                    '<select id="pay_pay_rate" class="emc-select" style="width:100%;">' + rateOpts + '</select>' +
                '</div>' +
                '<div class="emc-field"><label>Payment frequency</label>' +
                    '<select id="pay_pay_frequency" class="emc-select" style="width:100%;">' + freqOpts + '</select>' +
                '</div>' +
                '<div class="emc-field"><label>Effective date</label><input type="date" id="pay_effective_from" class="emc-input" style="width:100%;" value="' + (pd.effective_from || '') + '"></div>' +
            '</div>' +
            '<div class="emc-footer">' +
                '<button type="button" class="emc-btn-outline" onclick="closeSimpleEditModal()">Cancel</button>' +
                '<button type="button" class="emc-btn-primary" id="payModalSaveBtn" onclick="submitPayModal()">Save</button>' +
            '</div>';
    }

    if (target === 'contact') {
        var c = CONTACT_DATA;
        box.innerHTML =
            '<div class="emc-header"><h3>Edit ' + name + '\'s contact information</h3><button type="button" class="emc-close" onclick="closeSimpleEditModal()">&times;</button></div>' +
            '<div class="emc-body">' +
                '<div class="emc-field"><label>Account email <span style="color:#e74c5e;">*</span></label><input type="email" class="emc-input" style="width:100%;background:#f3f4f6;color:#888;" value="' + (c.account_email || '') + '" disabled></div>' +
                '<div class="emc-field"><label>Personal email <span style="color:#e74c5e;">*</span></label><input type="email" class="emc-input" style="width:100%;" placeholder="Personal email" value="' + (c.personal_email || '') + '"></div>' +
                '<div class="emc-field"><label>Home phone</label><input type="text" class="emc-input" style="width:100%;" placeholder="Home phone" value="' + (c.home_phone || '') + '"></div>' +
                '<div class="emc-field"><label>Mobile phone <span style="color:#e74c5e;">*</span></label><input type="text" class="emc-input" style="width:100%;" placeholder="Mobile phone" value="' + (c.mobile_phone || '') + '"></div>' +
                '<div class="emc-field"><label>Work phone</label><input type="text" class="emc-input" style="width:100%;" placeholder="Work phone" value="' + (c.work_phone || '') + '"></div>' +
                '<div class="emc-field"><label>Work extension</label><input type="text" class="emc-input" style="width:100%;" placeholder="Work extension" value="' + (c.work_extension || '') + '"></div>' +
            '</div>' +
            '<div class="emc-footer">' +
                '<button type="button" class="emc-btn-outline" onclick="closeSimpleEditModal()">Cancel</button>' +
                '<button type="button" class="emc-btn-primary" id="contactModalSaveBtn" onclick="submitContactModal()">Save</button>' +
            '</div>';
    }

    if (target === 'personal') {
        var p = PERSONAL_DATA;
        var titleOpts = ['Mr', 'Mrs', 'Ms', 'Miss', 'Mx', 'Dr'].map(function (t) {
            return '<option' + (t === p.title ? ' selected' : '') + '>' + t + '</option>';
        }).join('');
        var genderOpts = ['Male', 'Female', 'Non-binary', 'Prefer not to say'].map(function (g) {
            return '<option' + (g.toLowerCase() === (p.gender || '').toLowerCase() ? ' selected' : '') + '>' + g + '</option>';
        }).join('');
        var countryOpts = '<option value="">Select country</option>' + PERSONAL_COUNTRY_LIST.map(function (c) {
            return '<option' + (c === p.country ? ' selected' : '') + '>' + c + '</option>';
        }).join('');

        box.innerHTML =
            '<div class="emc-header"><h3>Edit ' + name + '\'s personal information</h3><button type="button" class="emc-close" onclick="closeSimpleEditModal()">&times;</button></div>' +
            '<div class="emc-body">' +
                '<div class="emc-field"><label>Title <span style="color:#e74c5e;">*</span></label><select class="emc-select" style="width:100%;">' + titleOpts + '</select></div>' +
                '<div class="emc-field"><label>First name <span style="color:#e74c5e;">*</span></label><input type="text" class="emc-input" style="width:100%;" value="' + (p.first_name || '') + '"></div>' +
                '<div class="emc-field"><label>Middle name</label><input type="text" class="emc-input" style="width:100%;" placeholder="Middle name" value="' + (p.middle_name || '') + '"></div>' +
                '<div class="emc-field"><label>Last name <span style="color:#e74c5e;">*</span></label><input type="text" class="emc-input" style="width:100%;" value="' + (p.last_name || '') + '"></div>' +
                '<div class="emc-field"><label>Date of birth <span style="color:#e74c5e;">*</span></label><input type="date" class="emc-input" style="width:100%;" value="' + (p.dob || '') + '"></div>' +
                '<div class="emc-field"><label>Gender <span style="color:#e74c5e;">*</span></label><select class="emc-select" style="width:100%;">' + genderOpts + '</select></div>' +
                '<div class="emc-field"><label>Country</label><select class="emc-select" style="width:100%;">' + countryOpts + '</select></div>' +
                '<div class="emc-field"><label>Address 1 <span style="color:#e74c5e;">*</span></label><input type="text" class="emc-input" style="width:100%;" placeholder="Address 1" value="' + (p.address_1 || '') + '"></div>' +
                '<div class="emc-field"><label>Address 2</label><input type="text" class="emc-input" style="width:100%;" placeholder="Address 2" value="' + (p.address_2 || '') + '"></div>' +
                '<div class="emc-field"><label>Address 3</label><input type="text" class="emc-input" style="width:100%;" placeholder="Address 3" value="' + (p.address_3 || '') + '"></div>' +
                '<div class="emc-field"><label>Suburb/City</label><input type="text" class="emc-input" style="width:100%;" placeholder="Suburb/City" value="' + (p.city || '') + '"></div>' +
                '<div class="emc-field"><label>Territory</label><input type="text" class="emc-input" style="width:100%;" placeholder="Territory/Province" value="' + (p.territory || '') + '"></div>' +
                '<div class="emc-field"><label>Postcode</label><input type="text" class="emc-input" style="width:100%;" placeholder="Postcode" value="' + (p.postcode || '') + '"></div>' +
            '</div>' +
            '<div class="emc-footer">' +
                '<button type="button" class="emc-btn-outline" onclick="closeSimpleEditModal()">Cancel</button>' +
                '<button type="button" class="emc-btn-primary" id="personalModalSaveBtn" onclick="submitPersonalModal()">Save</button>' +
            '</div>';
    }

    document.getElementById('simpleEditOverlay').classList.add('open');
}
function closeSimpleEditModal() { document.getElementById('simpleEditOverlay').classList.remove('open'); }

/* ---------- Emergency contact modal helpers ---------- */
var EMERGENCY_STORE_URL = "{{ route('admin.linkers-hub.emergency-contacts.store', $employee->id) }}";
var EMERGENCY_BASE_URL  = "/admin/linkers-hub/employees/{{ $employee->id }}/emergency-contacts";
var CSRF_TOKEN = document.querySelector('input[name="_token"]') ? document.querySelector('input[name="_token"]').value : '{{ csrf_token() }}';

function buildContactForm(data) {
    data = data || {};
    var countryOpts = PERSONAL_COUNTRY_LIST.map(function(c) {
        return '<option' + (c === (data.country || 'Australia') ? ' selected' : '') + '>' + c + '</option>';
    }).join('');
    var relationships = ['Spouse', 'Partner', 'Parent', 'Child', 'Sibling', 'Friend', 'Other'];
    var relOpts = '<option value="">Select an option</option>' + relationships.map(function(r) {
        return '<option' + (r === data.relationship ? ' selected' : '') + '>' + r + '</option>';
    }).join('');

    return '<div class="emc-body">' +
        '<p style="margin:0 0 16px;font-size:0.85rem;color:#1b4332;background:#eef6f2;border:1px solid #d3e8de;border-radius:8px;padding:10px 14px;">Add at least one emergency contact in case something unexpected happens.</p>' +
        '<div class="emc-field"><label>First name <span style="color:#e74c5e;">*</span></label><input type="text" id="addc_first_name" class="emc-input" style="width:100%;" placeholder="First name" value="' + (data.first_name || '') + '"></div>' +
        '<div class="emc-field"><label>Last name</label><input type="text" id="addc_last_name" class="emc-input" style="width:100%;" placeholder="Last name" value="' + (data.last_name || '') + '"></div>' +
        '<div class="emc-field"><label>Home phone</label><input type="text" id="addc_home_phone" class="emc-input" style="width:100%;" placeholder="Home phone" value="' + (data.home_phone || '') + '"></div>' +
        '<div class="emc-field"><label>Mobile phone <span style="color:#e74c5e;">*</span></label><input type="text" id="addc_mobile_phone" class="emc-input" style="width:100%;" placeholder="Mobile phone" value="' + (data.mobile_phone || '') + '"></div>' +
        '<div class="emc-field"><label>Work phone</label><input type="text" id="addc_work_phone" class="emc-input" style="width:100%;" placeholder="Work phone" value="' + (data.work_phone || '') + '"></div>' +
        '<div class="emc-field"><label>Country</label><select id="addc_country" class="emc-select" style="width:100%;">' + countryOpts + '</select></div>' +
        '<div class="emc-field"><label>Address 1 <span style="color:#e74c5e;">*</span></label><input type="text" id="addc_address_1" class="emc-input" style="width:100%;" placeholder="Address 1" value="' + (data.address_1 || '') + '"></div>' +
        '<div class="emc-field"><label>Address 2</label><input type="text" id="addc_address_2" class="emc-input" style="width:100%;" placeholder="Address 2" value="' + (data.address_2 || '') + '"></div>' +
        '<div class="emc-field"><label>Address 3</label><input type="text" id="addc_address_3" class="emc-input" style="width:100%;" placeholder="Address 3" value="' + (data.address_3 || '') + '"></div>' +
        '<div class="emc-field"><label>Suburb/City</label><input type="text" id="addc_city" class="emc-input" style="width:100%;" placeholder="Suburb/City" value="' + (data.city || '') + '"></div>' +
        '<div class="emc-field"><label>Territory</label><input type="text" id="addc_territory" class="emc-input" style="width:100%;" placeholder="Territory" value="' + (data.territory || '') + '"></div>' +
        '<div class="emc-field"><label>Postcode</label><input type="text" id="addc_postcode" class="emc-input" style="width:100%;" placeholder="Postcode" value="' + (data.postcode || '') + '"></div>' +
        '<div class="emc-field"><label>Relationship <span style="color:#e74c5e;">*</span></label><select id="addc_relationship" class="emc-select" style="width:100%;">' + relOpts + '</select></div>' +
        '<div class="emc-field" style="display:flex;align-items:center;justify-content:space-between;">' +
            '<label style="margin:0;">Primary contact</label>' +
            '<label class="emc-switch"><input type="checkbox" id="addc_primary"' + (data.is_primary ? ' checked' : '') + '><span class="emc-switch-slider"></span></label>' +
        '</div>' +
    '</div>';
}

function getContactPayload() {
    return {
        first_name:   document.getElementById('addc_first_name').value,
        last_name:    document.getElementById('addc_last_name').value,
        home_phone:   document.getElementById('addc_home_phone').value,
        mobile_phone: document.getElementById('addc_mobile_phone').value,
        work_phone:   document.getElementById('addc_work_phone').value,
        country:      document.getElementById('addc_country').value,
        address_1:    document.getElementById('addc_address_1').value,
        address_2:    document.getElementById('addc_address_2').value,
        address_3:    document.getElementById('addc_address_3').value,
        city:         document.getElementById('addc_city').value,
        territory:    document.getElementById('addc_territory').value,
        postcode:     document.getElementById('addc_postcode').value,
        relationship: document.getElementById('addc_relationship').value,
        is_primary:   document.getElementById('addc_primary').checked ? 1 : 0,
    };
}

/* Required fields for an emergency contact: name, mobile phone, address, and
   relationship — these are also what count towards the 25% profile
   completion for the Emergencies tab (see computeProfileProgress on the
   backend), so keep both in sync if this list ever changes. */
function validateContactPayload(payload) {
    var missing = [];
    if (!payload.first_name.trim())   missing.push('First name');
    if (!payload.mobile_phone.trim()) missing.push('Mobile phone');
    if (!payload.address_1.trim())    missing.push('Address 1');
    if (!payload.relationship.trim()) missing.push('Relationship');
    return missing;
}

function openAddContactModal() {
    var box = document.getElementById('addContactBox');
    box.innerHTML =
        '<div class="emc-header"><h3>Add emergency contact</h3><button type="button" class="emc-close" onclick="closeAddContactModal()">&times;</button></div>' +
        buildContactForm() +
        '<div class="emc-footer">' +
            '<button type="button" class="emc-btn-outline" onclick="closeAddContactModal()">Cancel</button>' +
            '<button type="button" class="emc-btn-primary" id="addContactSaveBtn" onclick="submitAddContact()">Save</button>' +
        '</div>';
    document.getElementById('addContactOverlay').classList.add('open');
}

function submitAddContact() {
    var btn = document.getElementById('addContactSaveBtn');
    var payload = getContactPayload();
    var missing = validateContactPayload(payload);
    if (missing.length) { showToast(missing.join(', ') + (missing.length > 1 ? ' are' : ' is') + ' required', 'error'); return; }
    btn.disabled = true; btn.textContent = 'Saving…';
    fetch(EMERGENCY_STORE_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) { showToast('✓ Contact added', 'success'); closeAddContactModal(); setTimeout(function(){ reloadSameTab(); }, 800); }
        else { showToast('Save failed: ' + (data.message || 'Unknown error'), 'error'); btn.disabled = false; btn.textContent = 'Save'; }
    })
    .catch(function() { showToast('Network error. Please try again.', 'error'); btn.disabled = false; btn.textContent = 'Save'; });
}

function openEditContactModal(contactId, contactData) {
    var box = document.getElementById('addContactBox');
    box.innerHTML =
        '<div class="emc-header"><h3>Edit emergency contact</h3><button type="button" class="emc-close" onclick="closeAddContactModal()">&times;</button></div>' +
        buildContactForm(contactData) +
        '<div class="emc-footer">' +
            '<button type="button" class="emc-btn-outline" onclick="closeAddContactModal()">Cancel</button>' +
            '<button type="button" class="emc-btn-primary" id="editContactSaveBtn" onclick="submitEditContact(' + contactId + ')">Save changes</button>' +
        '</div>';
    document.getElementById('addContactOverlay').classList.add('open');
}

function submitEditContact(contactId) {
    var btn = document.getElementById('editContactSaveBtn');
    var payload = getContactPayload();
    var missing = validateContactPayload(payload);
    if (missing.length) { showToast(missing.join(', ') + (missing.length > 1 ? ' are' : ' is') + ' required', 'error'); return; }
    btn.disabled = true; btn.textContent = 'Saving…';
    var url = EMERGENCY_BASE_URL + '/' + contactId + '?_method=PUT';
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) { showToast('✓ Contact updated', 'success'); closeAddContactModal(); setTimeout(function(){ reloadSameTab(); }, 800); }
        else { showToast('Save failed: ' + (data.message || 'Unknown error'), 'error'); btn.disabled = false; btn.textContent = 'Save changes'; }
    })
    .catch(function() { showToast('Network error. Please try again.', 'error'); btn.disabled = false; btn.textContent = 'Save changes'; });
}

function deleteEmergencyContact(contactId, name) {
    document.getElementById('deleteContactName').textContent = name;
    document.getElementById('deleteContactConfirmBtn').onclick = function() {
        var btn = document.getElementById('deleteContactConfirmBtn');
        btn.disabled = true; btn.textContent = 'Deleting…';
        var url = EMERGENCY_BASE_URL + '/' + contactId + '?_method=DELETE';
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({})
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) { showToast('✓ Contact deleted', 'success'); closeDeleteContactModal(); setTimeout(function(){ reloadSameTab(); }, 800); }
            else { showToast('Delete failed: ' + (data.message || 'Unknown error'), 'error'); btn.disabled = false; btn.textContent = 'Delete'; }
        })
        .catch(function() { showToast('Network error. Please try again.', 'error'); btn.disabled = false; btn.textContent = 'Delete'; });
    };
    document.getElementById('deleteContactOverlay').classList.add('open');
}

function closeDeleteContactModal() {
    document.getElementById('deleteContactOverlay').classList.remove('open');
    var btn = document.getElementById('deleteContactConfirmBtn');
    btn.disabled = false; btn.textContent = 'Delete';
}

function closeAddContactModal() { document.getElementById('addContactOverlay').classList.remove('open'); }

/* ---------- Payroll & Bank save helpers ---------- */
function showToast(message, type) {
    var t = document.getElementById('empToast');
    t.textContent = message;
    t.className = 'show ' + (type || 'success');
    clearTimeout(t._timer);
    t._timer = setTimeout(function() { t.className = t.className.replace('show', '').trim(); }, 3000);
}

function empApiSave(payload, btnEl, successMsg, onSuccess) {
    var original = btnEl ? btnEl.textContent : '';
    if (btnEl) { btnEl.disabled = true; btnEl.style.background = '#94a3b8'; btnEl.textContent = 'Saving…'; }
    var csrfToken = (document.querySelector('input[name="_token"]') || {}).value || '{{ csrf_token() }}';
    var url = EMPLOYEE_UPDATE_URL + (EMPLOYEE_UPDATE_URL.indexOf('?') === -1 ? '?' : '&') + '_method=PUT';
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            if (btnEl) {
                btnEl.disabled = false;
                btnEl.style.background = '#16a34a';
                btnEl.textContent = '✓ Saved';
                setTimeout(function(){ btnEl.style.background = ''; btnEl.textContent = original; }, 2000);
            }
            showToast('✓ Changes saved successfully', 'success');
            if (typeof onSuccess === 'function') onSuccess(data);
        } else {
            showToast('Save failed: ' + (data.message || 'Unknown error'), 'error');
            if (btnEl) { btnEl.disabled = false; btnEl.style.background = ''; btnEl.textContent = original; }
        }
    })
    .catch(function(e) {
        showToast('Network error. Please try again.', 'error');
        if (btnEl) { btnEl.disabled = false; btnEl.style.background = ''; btnEl.textContent = original; }
    });
}

/* ---------- Change status modal ---------- */
var STATUS_LABELS = {
    'active': 'Active',
    'probation': 'Probation',
    'on-leave': 'On Leave',
    'joining-soon': 'Joining Soon',
    'terminated': 'Terminated'
};

function openStatusModal() {
    var current = '{{ $employee->status ?? "active" }}';
    var select = document.getElementById('statusModalSelect');
    if (select) select.value = current;
    onStatusModalChange();
    document.getElementById('statusModalOverlay').classList.add('open');
}

function closeStatusModal() {
    document.getElementById('statusModalOverlay').classList.remove('open');
}

function onStatusModalChange() {
    var select = document.getElementById('statusModalSelect');
    var warning = document.getElementById('statusModalWarning');
    if (warning) warning.style.display = (select && select.value === 'terminated') ? 'block' : 'none';
}

function submitStatusModal() {
    var select = document.getElementById('statusModalSelect');
    var newStatus = select ? select.value : '';
    if (!newStatus) return;

    var btn = document.getElementById('statusModalSaveBtn');
    empApiSave({ status: newStatus }, btn, 'Status updated', function() {
        var link = document.getElementById('employeeStatusLink');
        if (link) {
            link.textContent = STATUS_LABELS[newStatus] || newStatus;
            link.className = 'bhr-working-status-link status-' + newStatus;
        }
        closeStatusModal();
    });
}

/* ---------- Invite existing employee to system ---------- */
function inviteEmployeeToSystem() {
    var btn = document.getElementById('inviteSystemBtn');
    var original = btn ? btn.textContent : '';
    if (btn) { btn.disabled = true; btn.textContent = 'Sending…'; }

    fetch(SEND_REG_EMAIL_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ employee_ids: [EMPLOYEE_ID] })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('✓ Invitation sent', 'success');
            if (btn) { btn.disabled = false; btn.textContent = 'Resend invite'; }
        } else {
            showToast('Failed to send invite: ' + (data.message || 'Unknown error'), 'error');
            if (btn) { btn.disabled = false; btn.textContent = original; }
        }
    })
    .catch(function(e) {
        showToast('Network error. Please try again.', 'error');
        if (btn) { btn.disabled = false; btn.textContent = original; }
    });
}

function switchPayrollCountry(country) {
    var bpjs  = document.getElementById('section_bpjs');
    var super_ = document.getElementById('section_super');
    if (country === 'Indonesia') {
        bpjs.style.display  = '';
        super_.style.display = 'none';
    } else {
        bpjs.style.display  = 'none';
        super_.style.display = '';
    }
}

function savePayrollInfo() {
    var btn = document.getElementById('payrollSaveBtn');
    var countryEl = document.getElementById('payroll_country');
    var country = countryEl ? countryEl.value : '{{ $workCountry }}';
    var payload = {
        payroll_no: document.getElementById('payroll_payroll_no').value,
        work_country: country,
    };
    if (country === 'Australia' || country === 'Other') {
        payload.super_fund_name = document.getElementById('payroll_super_fund_name').value;
        payload.super_fund_abn  = document.getElementById('payroll_super_fund_abn').value;
        payload.super_usi       = document.getElementById('payroll_super_usi').value;
        payload.super_member_no = document.getElementById('payroll_super_member_no').value;
    } else {
        payload.bpjs_ketenagakerjaan_no     = document.getElementById('payroll_bpjs_ketenagakerjaan_no').value;
        payload.bpjs_ketenagakerjaan_start  = document.getElementById('payroll_bpjs_ketenagakerjaan_start').value;
        payload.bpjs_ketenagakerjaan_active = document.querySelector('input[name="bpjs_tk_active"]:checked') ? document.querySelector('input[name="bpjs_tk_active"]:checked').value : 1;
        payload.bpjs_kesehatan_no           = document.getElementById('payroll_bpjs_kesehatan_no').value;
        payload.bpjs_kesehatan_class        = document.getElementById('payroll_bpjs_kesehatan_class').value;
        payload.bpjs_kesehatan_dependants   = document.getElementById('payroll_bpjs_kesehatan_dependants').value;
        payload.bpjs_kesehatan_start        = document.getElementById('payroll_bpjs_kesehatan_start').value;
        payload.bpjs_kesehatan_active       = document.querySelector('input[name="bpjs_ks_active"]:checked') ? document.querySelector('input[name="bpjs_ks_active"]:checked').value : 1;
    }
    empApiSave(payload, btn, 'Payroll info saved');
}

function resetPayrollInfo() {
    reloadSameTab();
}

function saveBankDetails() {
    var btn = document.getElementById('bankSaveBtn');
    var payload = {
        bank_acc_name: document.getElementById('bank_bank_acc_name').value,
        bank_name:     document.getElementById('bank_bank_name').value,
        bank_branch:   document.getElementById('bank_bank_branch').value,
        bank_acc_no:   document.getElementById('bank_bank_acc_no').value,
        bank_bsb:      document.getElementById('bank_bank_bsb').value,
    };
    empApiSave(payload, btn, 'Bank details saved');
}

function submitCorrectContract() {
    var btn = document.getElementById('correctSaveBtn');
    var payload = {
        working_pattern: document.getElementById('editHoursSelect') ? document.getElementById('editHoursSelect').value : editSelectedHours,
    };
    if (btn) { btn.disabled = true; btn.textContent = 'Saving…'; }
    fetch(EMPLOYEE_UPDATE_URL + '?_method=PUT', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) { showToast('✓ Contract updated', 'success'); closeEditContractModal(); setTimeout(reloadSameTab, 800); }
        else { showToast('Save failed: ' + (data.message || 'Unknown error'), 'error'); if (btn) { btn.disabled = false; btn.textContent = 'Save and update'; } }
    })
    .catch(function() { showToast('Network error. Please try again.', 'error'); if (btn) { btn.disabled = false; btn.textContent = 'Save and update'; } });
}

function submitChangeContract() {
    var btn = document.getElementById('editSaveBtn');
    var reason = document.getElementById('editReasonSelect') ? document.getElementById('editReasonSelect').value : '';
    if (!reason) { showToast('Please select a reason for change', 'error'); return; }
    var startDate = document.getElementById('editNewStartDate') ? document.getElementById('editNewStartDate').value : '';
    var payload = {
        employee_type:   editNewContractType,
        working_pattern: editNewPattern,
        salary_reason:   reason,
    };
    if (startDate) payload.start_date = startDate;
    if (btn) { btn.disabled = true; btn.textContent = 'Saving…'; }
    fetch(EMPLOYEE_UPDATE_URL + '?_method=PUT', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) { showToast('✓ Contract changed', 'success'); closeEditContractModal(); setTimeout(reloadSameTab, 800); }
        else { showToast('Save failed: ' + (data.message || 'Unknown error'), 'error'); if (btn) { btn.disabled = false; btn.textContent = 'Save'; } }
    })
    .catch(function() { showToast('Network error. Please try again.', 'error'); if (btn) { btn.disabled = false; btn.textContent = 'Save'; } });
}

function submitRoleModal() {
    var btn = document.getElementById('roleModalSaveBtn');
    var probRequired = document.getElementById('probYes') && document.getElementById('probYes').classList.contains('active') ? 1 : 0;
    var payload = {
        position_title:          document.getElementById('role_position_title').value,
        employment_basis:        document.getElementById('role_employment_basis').value,
        division_id:             document.getElementById('role_division_id').value,
        sub_division_id:         document.getElementById('role_sub_division_id').value,
        position_id:             document.getElementById('role_position_id').value,
        probation_required:      probRequired,
        probation_end_date:      probRequired ? document.getElementById('role_probation_end_date').value : '',
        notice_during_probation: probRequired ? document.getElementById('role_notice_during_probation').value : '',
        notice_period:           document.getElementById('role_notice_period').value,
    };
    if (btn) { btn.disabled = true; btn.textContent = 'Saving…'; }
    fetch(EMPLOYEE_UPDATE_URL + '?_method=PUT', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) { showToast('✓ Role information saved', 'success'); closeSimpleEditModal(); setTimeout(reloadSameTab, 800); }
        else { showToast('Save failed: ' + (data.message || 'Unknown error'), 'error'); if (btn) { btn.disabled = false; btn.textContent = 'Save'; } }
    })
    .catch(function() { showToast('Network error. Please try again.', 'error'); if (btn) { btn.disabled = false; btn.textContent = 'Save'; } });
}

function submitPayModal() {
    var btn = document.getElementById('payModalSaveBtn');
    var payload = {
        salary:          document.getElementById('pay_salary').value,
        pay_rate:        document.getElementById('pay_pay_rate').value,
        pay_frequency:   document.getElementById('pay_pay_frequency').value,
        effective_from:  document.getElementById('pay_effective_from').value,
    };
    if (btn) { btn.disabled = true; btn.textContent = 'Saving…'; }
    fetch(EMPLOYEE_UPDATE_URL + '?_method=PUT', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) { showToast('✓ Pay details saved', 'success'); closeSimpleEditModal(); setTimeout(reloadSameTab, 800); }
        else { showToast('Save failed: ' + (data.message || 'Unknown error'), 'error'); if (btn) { btn.disabled = false; btn.textContent = 'Save'; } }
    })
    .catch(function() { showToast('Network error. Please try again.', 'error'); if (btn) { btn.disabled = false; btn.textContent = 'Save'; } });
}

function saveMedicalInfo() {
    var btn = document.getElementById('medicalSaveBtn');
    var payload = {
        medical_conditions: document.getElementById('medical_conditions').value,
        allergies:          document.getElementById('medical_allergies').value,
        blood_type:         document.getElementById('medical_blood_type').value,
        medical_notes:      document.getElementById('medical_notes').value,
    };
    empApiSave(payload, btn, 'Medical info saved');
}

function submitContactModal() {
    var btn = document.getElementById('contactModalSaveBtn');
    var inputs = document.querySelectorAll('#simpleEditBox .emc-input, #simpleEditBox .emc-select');
    var labels = ['account_email', 'personal_email', 'home_phone', 'mobile_phone', 'work_phone', 'work_extension'];
    var payload = {};
    var values = {};
    inputs.forEach(function(el, i) { if (labels[i]) { values[labels[i]] = el.value; if (!el.disabled) payload[labels[i]] = el.value; } });

    var missing = [];
    if (!(values.account_email || '').trim())  missing.push('Account email');
    if (!(values.personal_email || '').trim()) missing.push('Personal email');
    if (!(values.mobile_phone || '').trim())   missing.push('Mobile phone');
    if (missing.length) { showToast(missing.join(', ') + (missing.length > 1 ? ' are' : ' is') + ' required', 'error'); return; }

    if (btn) { btn.disabled = true; btn.textContent = 'Saving…'; }
    fetch(EMPLOYEE_UPDATE_URL + '?_method=PUT', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('✓ Contact info saved', 'success');
            closeSimpleEditModal();
            setTimeout(function(){ reloadSameTab(); }, 800);
        } else {
            showToast('Save failed: ' + (data.message || 'Unknown error'), 'error');
            if (btn) { btn.disabled = false; btn.textContent = 'Save'; }
        }
    })
    .catch(function() { showToast('Network error. Please try again.', 'error'); if (btn) { btn.disabled = false; btn.textContent = 'Save'; } });
}

function submitPersonalModal() {
    var btn = document.getElementById('personalModalSaveBtn');
    var box = document.getElementById('simpleEditBox');
    var inputs = box.querySelectorAll('.emc-input, .emc-select');
    var labels = ['title', 'first_name', 'middle_name', 'last_name', 'dob', 'gender', 'country', 'address_1', 'address_2', 'address_3', 'city', 'territory', 'postcode'];
    var payload = {};
    inputs.forEach(function(el, i) { if (labels[i]) payload[labels[i]] = el.value; });

    var missing = [];
    if (!(payload.title || '').trim())      missing.push('Title');
    if (!(payload.first_name || '').trim()) missing.push('First name');
    if (!(payload.last_name || '').trim())  missing.push('Last name');
    if (!(payload.dob || '').trim())        missing.push('Date of birth');
    if (!(payload.gender || '').trim())     missing.push('Gender');
    if (!(payload.address_1 || '').trim())  missing.push('Address 1');
    if (missing.length) { showToast(missing.join(', ') + (missing.length > 1 ? ' are' : ' is') + ' required', 'error'); return; }

    if (btn) { btn.disabled = true; btn.textContent = 'Saving…'; }
    fetch(EMPLOYEE_UPDATE_URL + '?_method=PUT', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('✓ Personal info saved', 'success');
            closeSimpleEditModal();
            setTimeout(function(){ reloadSameTab(); }, 800);
        } else {
            showToast('Save failed: ' + (data.message || 'Unknown error'), 'error');
            if (btn) { btn.disabled = false; btn.textContent = 'Save'; }
        }
    })
    .catch(function() { showToast('Network error. Please try again.', 'error'); if (btn) { btn.disabled = false; btn.textContent = 'Save'; } });
}

function resetBankDetails() {
    reloadSameTab();
}

function saveSensitiveInfo() {
    var btn = document.getElementById('sensitiveSaveBtn');
    var payload = {
        tfn:              document.getElementById('sensitive_tfn').value,
        passport_no:      document.getElementById('sensitive_passport_no').value,
        passport_country: document.getElementById('sensitive_passport_country').value,
        passport_expiry:  document.getElementById('sensitive_passport_expiry').value,
        licence_no:       document.getElementById('sensitive_licence_no').value,
        licence_country:  document.getElementById('sensitive_licence_country').value,
        licence_class:    document.getElementById('sensitive_licence_class').value,
        licence_expiry:   document.getElementById('sensitive_licence_expiry').value,
        visa_no:          document.getElementById('sensitive_visa_no').value,
        visa_expiry:      document.getElementById('sensitive_visa_expiry').value,
        police_check_conducted: document.getElementById('sensitive_police_check').checked ? 1 : 0,
    };
    empApiSave(payload, btn, 'Sensitive info saved');
}

function selectPill(onId, offId) {
    document.getElementById(onId).classList.add('selected');
    document.getElementById(offId).classList.remove('selected');
}

/* Probation Yes/No — toggles the extra probation fields */
function setProbation(isYes) {
    selectPill(isYes ? 'probYes' : 'probNo', isYes ? 'probNo' : 'probYes');
    var panel = document.getElementById('probationPanel');
    if (panel) panel.style.display = isYes ? 'block' : 'none';
}

/* Reason-for-change options, shared by Role & Pay edit modals */
function reasonForChangeOptions() {
    var reasons = ['Additional responsibilities','Adjustment','Demotion','Employee setup','Job change','Length of service','Light duties','Other','Pay rise','Payroll reconciliation','Promotion','Restructure','Secondment'];
    var html = '<option value="">Select reason</option>';
    reasons.forEach(function (r) { html += '<option>' + r + '</option>'; });
    return html;
}

/* ---------- 5. Generic history modal (Role / Pay) ---------- */
function openGenericHistoryModal(target) {
    var box = document.getElementById('genericHistoryBox');
    var name = EMPLOYEE_FIRST_NAME;
    var title, columns;

    if (target === 'role') {
        title = name + '\'s role history';
        columns = ['Effective date', 'Job title', 'Changed by'];
    } else if (target === 'pay') {
        title = name + '\'s pay history';
        columns = ['Effective date', 'Salary', 'Changed by'];
    } else {
        title = name + '\'s history';
        columns = ['Effective date', 'Detail', 'Changed by'];
    }

    var theadHtml = columns.map(function (c) { return '<th>' + c + '</th>'; }).join('');

    /* No history records exist yet for any field — empty state by default.
       Once changes start being saved, push rows here and they will render in the table. */
    var rows = [];

    var bodyHtml;
    if (rows.length === 0) {
        bodyHtml =
            '<div class="emc-empty">' +
                '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12l-3-3m0 0l-3 3m3-3v6m-1.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>' +
                '<div class="emc-empty-text">Sorry, no previous changes exist for ' + name + '</div>' +
            '</div>';
    } else {
        var rowsHtml = rows.map(function (r) {
            return '<tr><td>' + r.date + '</td><td>' + r.detail + '</td><td>' + r.by + '</td></tr>';
        }).join('');
        bodyHtml = '<table class="emc-table"><thead><tr>' + theadHtml + '</tr></thead><tbody>' + rowsHtml + '</tbody></table>';
    }

    box.innerHTML =
        '<div class="emc-header"><h3>' + title + '</h3><button type="button" class="emc-close" onclick="closeGenericHistoryModal()">&times;</button></div>' +
        '<div class="emc-body">' + bodyHtml + '</div>';

    document.getElementById('genericHistoryOverlay').classList.add('open');
}
function closeGenericHistoryModal() { document.getElementById('genericHistoryOverlay').classList.remove('open'); }

/* ---------- 6. Profile picture edit / delete (UI preview only — not yet wired to backend upload) ---------- */
var avatarHasPhoto = {{ $employee->avatar_path ? 'true' : 'false' }};
var AVATAR_UPLOAD_URL = "{{ route('admin.linkers-hub.upload-avatar', $employee->id) }}";
var AVATAR_DELETE_URL = "{{ route('admin.linkers-hub.delete-avatar', $employee->id) }}";

function avatarCamClick() {
    if (avatarHasPhoto) {
        openDeleteAvatarModal();
    } else {
        document.getElementById('avatarFileInput').click();
    }
}

function handleAvatarFileSelect(event) {
    var file = event.target.files && event.target.files[0];
    if (!file) return;

    // Preview immediately
    var reader = new FileReader();
    reader.onload = function(e) {
        var display = document.getElementById('avatarDisplay');
        display.innerHTML = '<img src="' + e.target.result + '" alt="Profile picture">';
        avatarHasPhoto = true;
        setAvatarCamIcon(true);
    };
    reader.readAsDataURL(file);

    // Upload to server
    var formData = new FormData();
    formData.append('avatar', file);
    formData.append('_token', (document.querySelector('input[name="_token"]') || {}).value || '{{ csrf_token() }}');

    fetch(AVATAR_UPLOAD_URL, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            showToast('✓ Profile picture updated', 'success');
            // Update src to server URL
            var img = document.querySelector('#avatarDisplay img');
            if (img) img.src = data.url;
        } else {
            showToast('Upload failed: ' + (data.message || 'Unknown error'), 'error');
        }
    })
    .catch(function() { showToast('Network error. Please try again.', 'error'); });
}

function setAvatarCamIcon(isDelete) {
    var btn = document.getElementById('avatarCamBtn');
    var icon = document.getElementById('avatarCamIcon');
    if (isDelete) {
        btn.classList.add('is-delete');
        btn.title = 'Delete profile picture';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>';
    } else {
        btn.classList.remove('is-delete');
        btn.title = 'Edit profile picture';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>';
    }
}

function openDeleteAvatarModal() { document.getElementById('deleteAvatarOverlay').classList.add('open'); }
function closeDeleteAvatarModal() { document.getElementById('deleteAvatarOverlay').classList.remove('open'); }

function confirmDeleteAvatar() {
    var btn = document.getElementById('deleteAvatarConfirmBtn');
    if (btn) { btn.disabled = true; btn.textContent = 'Deleting…'; }

    fetch(AVATAR_DELETE_URL + '?_method=DELETE', {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN, 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({})
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            var display = document.getElementById('avatarDisplay');
            display.innerHTML = @json($employee->initials);
            document.getElementById('avatarFileInput').value = '';
            avatarHasPhoto = false;
            setAvatarCamIcon(false);
            showToast('✓ Profile picture deleted', 'success');
        } else {
            showToast('Delete failed: ' + (data.message || 'Unknown error'), 'error');
        }
        closeDeleteAvatarModal();
        if (btn) { btn.disabled = false; btn.textContent = 'Delete'; }
    })
    .catch(function() {
        showToast('Network error. Please try again.', 'error');
        closeDeleteAvatarModal();
        if (btn) { btn.disabled = false; btn.textContent = 'Delete'; }
    });
}
/* ================================================================
   REMOVE ABSENCE — custom confirm modal
   ================================================================ */
var absDeletePendingId = null;

function confirmRemoveAbsence(absenceId) {
    absDeletePendingId = absenceId;
    var modal = document.getElementById('absDeleteModal');
    if (modal) modal.style.display = 'flex';
}

function closeRemoveAbsenceModal() {
    absDeletePendingId = null;
    var modal = document.getElementById('absDeleteModal');
    if (modal) modal.style.display = 'none';
}

function doRemoveAbsence() {
    if (!absDeletePendingId) return;
    var form = document.getElementById('abs-del-form-' + absDeletePendingId);
    if (form) {
        closeRemoveAbsenceModal();
        form.submit();
    }
}

// Close on backdrop click
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('absDeleteModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeRemoveAbsenceModal();
        });
    }
});

</script>
@endsection