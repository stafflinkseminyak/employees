@extends('admin.layout')

@php
    $labels = [
        'annual'   => 'annual leave',
        'personal' => "personal / carer's leave",
        'lateness' => 'lateness',
        'other'    => 'other absence',
    ];
    $typeLabel = $labels[$type] ?? 'absence';
@endphp

@section('title', 'Add ' . $typeLabel . ' for ' . $employee->first_name)
@section('page-title', 'Add absence')

@section('content')
<style>
    .al-wrap { max-width: 1100px; }
    .al-back {
        display: inline-flex; align-items: center; gap: 6px;
        color: #2e7d5e; text-decoration: none; font-size: 14px; font-weight: 600; margin-bottom: 16px;
    }
    .al-back svg { width: 18px; height: 18px; }
    .al-title {
        font-size: 1.6rem; font-weight: 700; color: #1a1a2e;
        border-bottom: 1px solid #e5e7eb; padding-bottom: 16px; margin-bottom: 28px;
    }

    /* Horizontal rows (annual / lateness / other) */
    .al-row { display: grid; grid-template-columns: 160px 1fr; gap: 24px; align-items: start; margin-bottom: 22px; max-width: 760px; }
    .al-row > label { font-size: 0.95rem; color: #333; padding-top: 11px; }

    /* Stacked fields (personal) */
    .al-field { margin-bottom: 20px; }
    .al-field > label { display: block; font-size: 0.95rem; color: #333; font-weight: 500; margin-bottom: 8px; }
    .al-field-desc { font-size: 0.82rem; color: #888; margin-top: 4px; }

    .al-input, .al-select, .al-textarea {
        width: 100%; padding: 11px 14px; border: 1px solid #cbd5e1; border-radius: 8px;
        font-size: 0.95rem; color: #1a1a2e; background: #fff; outline: none; box-sizing: border-box;
    }
    .al-select {
        appearance: none; -webkit-appearance: none; -moz-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23667085' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat; background-position: right 14px center; background-size: 18px;
        padding-right: 42px;
    }
    .al-input:focus, .al-select:focus, .al-textarea:focus {
        border-color: #2e7d5e; box-shadow: 0 0 0 3px rgba(46,125,94,0.10);
    }
    .al-textarea { min-height: 110px; resize: vertical; }
    .al-lateby { display: flex; gap: 12px; max-width: 420px; }
    .al-lateby .unit { position: relative; flex: 1; }
    .al-lateby .unit span { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #999; font-size: 0.9rem; }

    /* Toggle switch */
    .al-toggle-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 20px; }
    .al-switch { position: relative; display: inline-block; width: 44px; height: 24px; flex-shrink: 0; }
    .al-switch input { opacity: 0; width: 0; height: 0; }
    .al-slider { position: absolute; cursor: pointer; inset: 0; background: #cbd5e1; border-radius: 999px; transition: .2s; }
    .al-slider:before { content: ""; position: absolute; height: 18px; width: 18px; left: 3px; top: 3px; background: #fff; border-radius: 50%; transition: .2s; }
    .al-switch input:checked + .al-slider { background: #2e7d5e; }
    .al-switch input:checked + .al-slider:before { transform: translateX(20px); }

    /* File attach */
    .al-attach-row { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 8px; }
    .al-attach-btn {
        display: inline-block; border: 1.5px solid #2874a6; color: #2874a6; background: #fff;
        border-radius: 8px; padding: 8px 16px; font-size: 0.88rem; font-weight: 600; cursor: pointer;
        font-family: inherit;
    }
    .al-attach-btn:hover { background: #f0f7fc; }
    .al-attach-name { font-size: 0.85rem; color: #888; }

    /* Personal two-column */
    .al-split { display: grid; grid-template-columns: 1.4fr 1fr; gap: 40px; align-items: start; }
    .al-summary { background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 10px; padding: 20px 22px; margin-bottom: 24px; }
    .al-summary-title { color: #2874a6; font-weight: 700; font-size: 1.1rem; margin-bottom: 12px; }
    .al-summary-row { display: flex; justify-content: space-between; padding: 7px 0; font-size: 0.9rem; }
    .al-summary-row .k { color: #333; }
    .al-summary-row .v { color: #888; }
    .al-section-line { display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e5e7eb; padding-bottom: 10px; margin-bottom: 12px; }
    .al-section-line h4 { font-size: 1rem; font-weight: 600; color: #1a1a2e; margin: 0; }
    .al-counter { font-size: 0.8rem; color: #999; margin-top: 4px; }

    /* Footer */
    .al-actions { display: flex; gap: 12px; margin-top: 24px; }
    .al-btn-primary {
        padding: 11px 26px; border: none; border-radius: 8px; font-size: 0.95rem; font-weight: 600;
        background: #2e7d5e; color: #fff; cursor: pointer; transition: background 0.2s;
    }
    .al-btn-primary:hover { background: #1b4332; }
    .al-btn-primary:disabled { background: #cbd5e1; cursor: not-allowed; }
    .al-btn-back {
        padding: 11px 26px; border: 1.5px solid #e74c5e; color: #e74c5e; background: #fff;
        border-radius: 8px; font-size: 0.95rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center;
    }
    .al-btn-back:hover { background: #fef2f2; }
    .al-note { margin-top: 14px; font-size: 0.85rem; color: #b45309; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 10px 14px; max-width: 760px; }

    /* Company paid panel */
    .al-paid-panel { background: #f0f6f9; border-radius: 10px; padding: 18px 20px; margin: -8px 0 20px; }
    .al-paid-row-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
    .al-paid-row-bottom { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
    .al-paid-label { font-size: 0.95rem; font-weight: 600; color: #1a1a2e; }
    .al-segmented { display: inline-flex; background: #dbe9f0; border-radius: 999px; padding: 3px; }
    .al-seg-btn {
        border: none; background: transparent; padding: 7px 18px; font-size: 0.85rem; font-weight: 700;
        color: #2874a6; border-radius: 999px; cursor: pointer; transition: all .15s;
    }
    .al-seg-btn.active { background: #fff; color: #1b4332; box-shadow: 0 1px 3px rgba(0,0,0,0.12); }
    .al-duration-unit { position: relative; max-width: 160px; flex: 1; }
    .al-duration-unit span { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #999; font-size: 0.9rem; }
    .al-duration-unit input { padding-right: 50px; }

    /* Attach File Modal */
    .al-modal-overlay {
        display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55);
        z-index: 1000; align-items: center; justify-content: center;
    }
    .al-modal-overlay.open { display: flex; }
    .al-modal-box {
        background: #fff; border-radius: 14px; width: 100%; max-width: 520px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.25); overflow: hidden;
    }
    .al-modal-header {
        background: #1b4332; color: #fff; padding: 18px 24px;
        display: flex; align-items: center; justify-content: space-between;
        border-bottom: 3px solid #D4A017;
    }
    .al-modal-header h3 { margin: 0; font-size: 1.1rem; font-weight: 700; }
    .al-modal-close {
        background: none; border: none; color: #fff; font-size: 1.4rem; line-height: 1;
        cursor: pointer; opacity: 0.85; padding: 0 2px;
    }
    .al-modal-close:hover { opacity: 1; }
    .al-modal-body { padding: 24px; }
    .al-modal-body p { margin: 0 0 16px; font-size: 0.9rem; color: #444; }
    .al-modal-body p strong { color: #1b4332; }
    .al-dropzone {
        border: 2px dashed #cbd5e1; border-radius: 10px; padding: 36px 20px;
        text-align: center; transition: all .15s; background: #fafbfd;
    }
    .al-dropzone.dragover { border-color: #2e7d5e; background: #eef6f2; }
    .al-dropzone svg { display: block; width: 40px; height: 40px; color: #2e7d5e; margin: 0 auto 10px; }
    .al-dropzone-text { font-size: 0.95rem; color: #444; margin-bottom: 16px; }
    .al-dropzone-filename { font-size: 0.92rem; color: #1b4332; font-weight: 600; margin-bottom: 16px; }
    .al-browse-btn {
        border: 1.5px solid #D4A017; color: #a3790f; background: #fff;
        border-radius: 8px; padding: 9px 20px; font-size: 0.88rem; font-weight: 700; cursor: pointer;
    }
    .al-browse-btn:hover { background: #fdf6e7; }
    .al-modal-footer {
        display: flex; justify-content: flex-end; gap: 12px; padding: 16px 24px;
        border-top: 1px solid #eef1f4;
    }
    .al-modal-btn-cancel {
        border: 1.5px solid #D4A017; color: #a3790f; background: #fff;
        border-radius: 8px; padding: 9px 20px; font-size: 0.88rem; font-weight: 700; cursor: pointer;
    }
    .al-modal-btn-cancel:hover { background: #fdf6e7; }
    .al-modal-btn-attach {
        border: none; background: #2e7d5e; color: #fff;
        border-radius: 8px; padding: 9px 22px; font-size: 0.88rem; font-weight: 700; cursor: pointer;
    }
    .al-modal-btn-attach:hover { background: #1b4332; }
    .al-modal-btn-attach:disabled { background: #cbd5e1; cursor: not-allowed; }
</style>

<div class="al-wrap">
    <a class="al-back" href="{{ route('admin.linkers-hub.employee-profile', $employee->id) }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
        Back to profile
    </a>

    <h1 class="al-title">Add {{ $typeLabel }} for {{ $employee->first_name }}</h1>

    {{-- Absence type selector (shared) - changing it switches the form --}}
    <div class="al-row">
        <label>Absence type</label>
        <div style="max-width:420px;">
            <select class="al-select" onchange="if(this.value){window.location.href=this.value;}">
                <option value="{{ route('admin.linkers-hub.add-absence', [$employee->id, 'annual']) }}" @selected($type==='annual')>Annual leave</option>
                <option value="{{ route('admin.linkers-hub.add-absence', [$employee->id, 'personal']) }}" @selected($type==='personal')>Personal / carer's leave</option>
                <option value="{{ route('admin.linkers-hub.add-absence', [$employee->id, 'lateness']) }}" @selected($type==='lateness')>Lateness</option>
                <option value="{{ route('admin.linkers-hub.add-absence', [$employee->id, 'other']) }}" @selected($type==='other')>Other</option>
            </select>
        </div>
    </div>

    @if($type === 'annual' || $type === 'other')
        {{-- ===== ANNUAL / OTHER: Start, End, Notes ===== --}}
        <div class="al-row">
            <label>Start</label>
            <div style="max-width:420px;"><input type="date" class="al-input"></div>
        </div>
        <div class="al-row">
            <label>End</label>
            <div style="max-width:420px;"><input type="date" class="al-input"></div>
        </div>
        <div class="al-row">
            <label>Notes</label>
            <div style="max-width:600px;"><textarea class="al-textarea" placeholder="Notes regarding the absence"></textarea></div>
        </div>

    @elseif($type === 'lateness')
        {{-- ===== LATENESS: Date, Late by, Notes ===== --}}
        <div class="al-row">
            <label>Date</label>
            <div style="max-width:420px;"><input type="date" class="al-input"></div>
        </div>
        <div class="al-row">
            <label>Late by</label>
            <div class="al-lateby">
                <div class="unit"><input type="number" min="0" value="0" class="al-input"><span>hrs</span></div>
                <div class="unit"><input type="number" min="0" value="0" class="al-input"><span>mins</span></div>
            </div>
        </div>
        <div class="al-row">
            <label>Notes</label>
            <div style="max-width:600px;"><textarea class="al-textarea" placeholder="Notes regarding the absence"></textarea></div>
        </div>

    @elseif($type === 'personal')
        {{-- ===== PERSONAL / CARER'S: full form ===== --}}
        <div class="al-split">
            {{-- LEFT --}}
            <div>
                <div class="al-field">
                    <label>Personal / carer's leave reason</label>
                    <select class="al-select" id="pcReason" onchange="updateSummary()">
                        <option value="Not provided">Not provided</option>
                        <option value="Illness (general)">Illness (general)</option>
                        <option value="Infectious disease / virus">Infectious disease / virus</option>
                        <option value="Mental health / stress">Mental health / stress</option>
                        <option value="Injury (non work-related)">Injury (non work-related)</option>
                        <option value="Medical appointment / procedure">Medical appointment / procedure</option>
                        <option value="Pregnancy-related">Pregnancy-related</option>
                        <option value="Surgery / recovery">Surgery / recovery</option>
                        <option value="Other medical reason">Other medical reason</option>
                        <option value="Unknown / prefer not to disclose">Unknown / prefer not to disclose</option>
                    </select>
                </div>

                <div class="al-toggle-row">
                    <div>
                        <label style="font-weight:500;color:#333;">Ongoing absence</label>
                        <div class="al-field-desc">Enable this option if the return date is unknown.</div>
                    </div>
                    <label class="al-switch"><input type="checkbox" id="pcOngoing" onchange="toggleOngoing(); updateSummary();"><span class="al-slider"></span></label>
                </div>

                <div class="al-field">
                    <label>Start</label>
                    <input type="date" class="al-input" id="pcStart" onchange="updateSummary()">
                </div>
                <div class="al-field">
                    <label id="pcEndLabel">End</label>
                    <input type="date" class="al-input" id="pcEnd" onchange="updateSummary()">
                </div>

                <div class="al-toggle-row">
                    <div>
                        <label style="font-weight:500;color:#333;">Company paid</label>
                        <div class="al-field-desc">Enable this option to define how the employee will be paid.</div>
                    </div>
                    <label class="al-switch"><input type="checkbox" id="pcPaid" onchange="togglePaidPanel(); updateSummary();"><span class="al-slider"></span></label>
                </div>

                <div class="al-paid-panel" id="paidPanel" style="display:none;">
                    <div class="al-paid-row-top">
                        <span class="al-paid-label">Recorded in</span>
                        <div class="al-segmented">
                            <button type="button" class="al-seg-btn active" id="segDays" onclick="setRecordMode('days')">Days</button>
                            <button type="button" class="al-seg-btn" id="segHrsMins" onclick="setRecordMode('hrsmins')">Hrs &amp; Mins</button>
                        </div>
                    </div>
                    <div class="al-paid-row-bottom">
                        <span class="al-paid-label">Duration paid</span>
                        <div id="durationDaysWrap" class="al-duration-unit">
                            <input type="number" min="0" value="0" class="al-input" id="durationDays" onchange="updateSummary()"><span>days</span>
                        </div>
                        <div id="durationHrsMinsWrap" class="al-lateby" style="display:none; max-width:240px;">
                            <div class="unit"><input type="number" min="0" value="0" class="al-input" id="durationHrs" onchange="updateSummary()"><span>hrs</span></div>
                            <div class="unit"><input type="number" min="0" value="0" class="al-input" id="durationMins" onchange="updateSummary()"><span>mins</span></div>
                        </div>
                    </div>
                </div>

                <div class="al-toggle-row">
                    <div>
                        <label style="font-weight:500;color:#333;">Evidenced personal / carer's leave</label>
                        <div class="al-field-desc">Enable this option to define if personal / carer's leave is evidenced.</div>
                    </div>
                    <label class="al-switch"><input type="checkbox" id="pcEvidenced" onchange="updateSummary()"><span class="al-slider"></span></label>
                </div>

                <div class="al-attach-row">
                    <span style="font-size:0.95rem;color:#333;font-weight:500;">Upload medical certificate</span>
                    <button type="button" class="al-attach-btn" onclick="openAttachModal('medCert')">Attach medical certificate</button>
                </div>
                <div class="al-attach-name" id="medCertName">No file attached</div>
            </div>

            {{-- RIGHT --}}
            <div>
                <div class="al-summary">
                    <div class="al-summary-title">Summary</div>
                    <div class="al-summary-row"><span class="k">Reason</span><span class="v" id="sumReason">Not provided</span></div>
                    <div class="al-summary-row"><span class="k">Start</span><span class="v" id="sumStart">Not set</span></div>
                    <div class="al-summary-row" id="sumEndRow"><span class="k" id="sumEndLabel">End</span><span class="v" id="sumEnd">Not set</span></div>
                    <div class="al-summary-row" id="sumDurationRow"><span class="k">Duration</span><span class="v" id="sumDuration">Not set</span></div>
                    <div class="al-summary-row"><span class="k">Paid by company</span><span class="v" id="sumPaid">No</span></div>
                    <div class="al-summary-row"><span class="k">Evidenced</span><span class="v" id="sumEvidenced">No</span></div>
                    <div class="al-summary-row"><span class="k">Medical certificate added</span><span class="v" id="sumMedCert">No</span></div>
                </div>

                <div class="al-section-line">
                    <h4>Supporting documents</h4>
                    <button type="button" class="al-attach-btn" onclick="openAttachModal('support')">Attach a file</button>
                </div>
                <div class="al-attach-name" id="supportName" style="margin-bottom:20px;">No files attached</div>

                <div class="al-field">
                    <label>Notes</label>
                    <textarea class="al-textarea" id="pcNotes" maxlength="1000" placeholder="Add a note here" oninput="document.getElementById('noteCount').textContent=this.value.length+'/1000';"></textarea>
                    <div class="al-counter" id="noteCount">0/1000</div>
                </div>
            </div>
        </div>
    @endif

    {{-- Footer --}}
    <div class="al-actions">
        <button type="button" class="al-btn-primary" id="addAbsenceBtn" onclick="submitAbsence()">Add absence</button>
        <a class="al-btn-back" href="{{ route('admin.linkers-hub.employee-profile', $employee->id) }}">Back to profile</a>
    </div>

    {{-- Attach File Modal (shared for medical certificate & supporting documents) --}}
    <div class="al-modal-overlay" id="attachModalOverlay">
        <div class="al-modal-box">
            <div class="al-modal-header">
                <h3 id="attachModalTitle">Attach a medical certificate</h3>
                <button type="button" class="al-modal-close" onclick="closeAttachModal()">&times;</button>
            </div>
            <div class="al-modal-body">
                <p>Choose the file you wish to upload, there is a limit of <strong>30MB</strong>.</p>
                <div class="al-dropzone" id="attachDropzone"
                     ondragover="event.preventDefault(); this.classList.add('dragover');"
                     ondragleave="this.classList.remove('dragover');"
                     ondrop="handleAttachDrop(event)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3 3m3-3l3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z"/></svg>
                    <div class="al-dropzone-text" id="attachDropzoneText">Drag and drop file here to select</div>
                    <div class="al-dropzone-filename" id="attachFileName" style="display:none;"></div>
                    <button type="button" class="al-browse-btn" onclick="document.getElementById('attachFileInput').click()">Browse files...</button>
                    <input type="file" id="attachFileInput" style="display:none;" onchange="handleAttachFileChosen(this.files)">
                </div>
            </div>
            <div class="al-modal-footer">
                <button type="button" class="al-modal-btn-cancel" onclick="closeAttachModal()">Cancel</button>
                <button type="button" class="al-modal-btn-attach" id="attachConfirmBtn" disabled onclick="confirmAttach()">Attach</button>
            </div>
        </div>
    </div>
</div>

<script>
function showFileName(targetId, fileName) {
    var el = document.getElementById(targetId);
    if (el) el.textContent = fileName || 'No file attached';
    if (targetId === 'medCertName') {
        var s = document.getElementById('sumMedCert');
        if (s) s.textContent = fileName ? 'Yes' : 'No';
    }
}

/* ===== Attach File Modal (medical certificate / supporting documents) ===== */
var attachTarget = null; // 'medCert' or 'support'
var attachPendingFile = null;

var attachModalConfig = {
    medCert: { title: 'Attach a medical certificate', displayId: 'medCertName' },
    support: { title: 'Attach a file', displayId: 'supportName' }
};

function openAttachModal(target) {
    attachTarget = target;
    attachPendingFile = null;
    var cfg = attachModalConfig[target];

    document.getElementById('attachModalTitle').textContent = cfg.title;
    document.getElementById('attachFileInput').value = '';
    document.getElementById('attachFileName').style.display = 'none';
    document.getElementById('attachFileName').textContent = '';
    document.getElementById('attachDropzoneText').style.display = 'block';
    document.getElementById('attachConfirmBtn').disabled = true;
    document.getElementById('attachDropzone').classList.remove('dragover');
    document.getElementById('attachModalOverlay').classList.add('open');
}

function closeAttachModal() {
    document.getElementById('attachModalOverlay').classList.remove('open');
    attachTarget = null;
    attachPendingFile = null;
}

function handleAttachFileChosen(files) {
    if (!files || !files.length) return;
    setAttachPendingFile(files[0]);
}

function handleAttachDrop(event) {
    event.preventDefault();
    document.getElementById('attachDropzone').classList.remove('dragover');
    var files = event.dataTransfer.files;
    if (files && files.length) setAttachPendingFile(files[0]);
}

function setAttachPendingFile(file) {
    attachPendingFile = file;
    document.getElementById('attachDropzoneText').style.display = 'none';
    var nameEl = document.getElementById('attachFileName');
    nameEl.textContent = file.name;
    nameEl.style.display = 'block';
    document.getElementById('attachConfirmBtn').disabled = false;
}

function confirmAttach() {
    if (!attachPendingFile || !attachTarget) return;
    var cfg = attachModalConfig[attachTarget];
    showFileName(cfg.displayId, attachPendingFile.name);
    closeAttachModal();
}

@if($type === 'personal')
var recordMode = 'days';

function toggleOngoing() {
    var ongoing = document.getElementById('pcOngoing').checked;
    var endInput = document.getElementById('pcEnd');
    var endLabel = document.getElementById('pcEndLabel');
    var sumEndLabel = document.getElementById('sumEndLabel');
    var sumDurationRow = document.getElementById('sumDurationRow');

    if (ongoing) {
        endLabel.textContent = 'Estimated end (optional)';
        sumEndLabel.textContent = 'Estimated end';
        endInput.removeAttribute('required');
        sumDurationRow.style.display = 'none';
    } else {
        endLabel.textContent = 'End';
        sumEndLabel.textContent = 'End';
        endInput.setAttribute('required', 'required');
        sumDurationRow.style.display = 'flex';
    }
}

function togglePaidPanel() {
    var paid = document.getElementById('pcPaid').checked;
    document.getElementById('paidPanel').style.display = paid ? 'block' : 'none';
}

function setRecordMode(mode) {
    recordMode = mode;
    var segDays = document.getElementById('segDays');
    var segHrsMins = document.getElementById('segHrsMins');
    var daysWrap = document.getElementById('durationDaysWrap');
    var hrsMinsWrap = document.getElementById('durationHrsMinsWrap');

    if (mode === 'days') {
        segDays.classList.add('active');
        segHrsMins.classList.remove('active');
        daysWrap.style.display = 'block';
        hrsMinsWrap.style.display = 'none';
    } else {
        segDays.classList.remove('active');
        segHrsMins.classList.add('active');
        daysWrap.style.display = 'none';
        hrsMinsWrap.style.display = 'flex';
    }
    updateSummary();
}

function updateSummary() {
    var reason = document.getElementById('pcReason');
    var start = document.getElementById('pcStart');
    var end = document.getElementById('pcEnd');
    var ongoing = document.getElementById('pcOngoing');
    var paid = document.getElementById('pcPaid');
    var evidenced = document.getElementById('pcEvidenced');

    document.getElementById('sumReason').textContent = reason ? reason.value : 'Not provided';
    document.getElementById('sumStart').textContent = (start && start.value) ? start.value : 'Not set';
    document.getElementById('sumEnd').textContent = (end && end.value) ? end.value : 'Not set';
    document.getElementById('sumPaid').textContent = (paid && paid.checked) ? 'Yes' : 'No';
    document.getElementById('sumEvidenced').textContent = (evidenced && evidenced.checked) ? 'Yes' : 'No';

    // Duration (whole days, inclusive) - only relevant when not ongoing
    if (!ongoing.checked && start && end && start.value && end.value) {
        var d1 = new Date(start.value), d2 = new Date(end.value);
        var diff = Math.round((d2 - d1) / 86400000) + 1;
        document.getElementById('sumDuration').textContent = (diff > 0) ? (diff + ' day' + (diff > 1 ? 's' : '')) : 'Not set';
    } else {
        document.getElementById('sumDuration').textContent = 'Not set';
    }
}
@endif


/* ── Absence submit ─────────────────────────────────── */
var ABSENCE_STORE_URL = "{{ route('admin.linkers-hub.store-absence', $employee->id) }}";
var ABSENCE_CSRF = document.querySelector('meta[name="csrf-token"]')
    ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    : '{{ csrf_token() }}';
var ABSENCE_TYPE = "{{ $type }}";
var ABSENCE_PROFILE_URL = "{{ route('admin.linkers-hub.employee-profile', $employee->id) }}";

function submitAbsence() {
    var btn = document.getElementById('addAbsenceBtn');
    var payload = { type: ABSENCE_TYPE };

    if (ABSENCE_TYPE === 'annual' || ABSENCE_TYPE === 'other') {
        var inputs = document.querySelectorAll('.al-input[type=date]');
        payload.start_date = inputs[0] ? inputs[0].value : null;
        payload.end_date   = inputs[1] ? inputs[1].value : null;
        var ta = document.querySelector('.al-textarea');
        payload.notes = ta ? ta.value : null;
        if (!payload.start_date) { alert('Please select a start date.'); return; }

    } else if (ABSENCE_TYPE === 'lateness') {
        var dateEl = document.querySelector('.al-input[type=date]');
        payload.start_date = dateEl ? dateEl.value : null;
        var nums = document.querySelectorAll('.al-input[type=number]');
        payload.late_hours   = nums[0] ? parseInt(nums[0].value) || 0 : 0;
        payload.late_minutes = nums[1] ? parseInt(nums[1].value) || 0 : 0;
        var ta = document.querySelector('.al-textarea');
        payload.notes = ta ? ta.value : null;
        if (!payload.start_date) { alert('Please select a date.'); return; }

    } else if (ABSENCE_TYPE === 'personal') {
        payload.start_date = document.getElementById('pcStart') ? document.getElementById('pcStart').value : null;
        payload.end_date   = document.getElementById('pcEnd')   ? document.getElementById('pcEnd').value   : null;
        payload.reason     = document.getElementById('pcReason') ? document.getElementById('pcReason').value : null;
        payload.ongoing    = document.getElementById('pcOngoing') ? document.getElementById('pcOngoing').checked : false;
        payload.company_paid = document.getElementById('pcPaid') ? document.getElementById('pcPaid').checked : false;
        payload.evidenced  = document.getElementById('pcEvidenced') ? document.getElementById('pcEvidenced').checked : false;
        payload.notes      = document.getElementById('pcNotes') ? document.getElementById('pcNotes').value : null;
        if (!payload.start_date) { alert('Please select a start date.'); return; }
    }

    btn.disabled    = true;
    btn.textContent = 'Saving…';

    fetch(ABSENCE_STORE_URL, {
        method: 'POST',
        headers: {
            'Content-Type':     'application/json',
            'Accept':           'application/json',
            'X-CSRF-TOKEN':     ABSENCE_CSRF,
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(payload),
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            window.location.href = ABSENCE_PROFILE_URL + '#absence';
        } else {
            alert('Error: ' + (data.message || 'Could not save absence.'));
            btn.disabled = false;
            btn.textContent = 'Add absence';
        }
    })
    .catch(function() {
        alert('Network error. Please try again.');
        btn.disabled = false;
        btn.textContent = 'Add absence';
    });
}
</script>
@endsection
