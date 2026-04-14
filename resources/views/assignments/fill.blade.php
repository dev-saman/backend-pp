<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $funnel->name }} — {{ $assignment->patient->first_name }} {{ $assignment->patient->last_name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f0f4f8; min-height: 100vh; }

        /* ── Header ── */
        .fill-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }
        .fill-header .logo { font-size: 18px; font-weight: 700; color: #1e293b; }
        .fill-header .logo span { color: #3b82f6; }
        .fill-header .patient-info { font-size: 13px; color: #64748b; }
        .fill-header .patient-info strong { color: #1e293b; }

        /* ── Progress bar ── */
        .progress-bar-wrap {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
        }
        .progress-steps {
            display: flex;
            align-items: center;
            gap: 0;
            max-width: 900px;
            margin: 0 auto;
            overflow-x: auto;
            padding-bottom: 4px;
        }
        .step-dot {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            min-width: 80px;
            cursor: pointer;
            position: relative;
        }
        .step-dot:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 16px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: #e2e8f0;
            z-index: 0;
        }
        .step-dot.completed:not(:last-child)::after { background: #22c55e; }
        .step-dot.active:not(:last-child)::after { background: #3b82f6; }
        .step-num {
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 600;
            background: #e2e8f0; color: #64748b;
            position: relative; z-index: 1;
            transition: all .2s;
        }
        .step-dot.completed .step-num { background: #22c55e; color: #fff; }
        .step-dot.active .step-num { background: #3b82f6; color: #fff; box-shadow: 0 0 0 4px rgba(59,130,246,.2); }
        .step-label {
            font-size: 11px; color: #94a3b8; margin-top: 4px;
            text-align: center; max-width: 80px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .step-dot.active .step-label { color: #3b82f6; font-weight: 600; }
        .step-dot.completed .step-label { color: #22c55e; }

        /* ── Overall progress ── */
        .overall-progress {
            max-width: 900px; margin: 0 auto 8px;
            display: flex; align-items: center; gap: 12px;
        }
        .overall-bar {
            flex: 1; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;
        }
        .overall-bar-fill {
            height: 100%; background: linear-gradient(90deg, #3b82f6, #22c55e);
            border-radius: 3px; transition: width .5s ease;
        }
        .overall-pct { font-size: 12px; font-weight: 600; color: #64748b; white-space: nowrap; }

        /* ── Main content ── */
        .fill-main { max-width: 760px; margin: 32px auto; padding: 0 16px 80px; }

        /* ── Form card ── */
        .form-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 8px rgba(0,0,0,.08);
            overflow: hidden;
            display: none;
        }
        .form-card.active { display: block; }
        .form-card-header {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: #fff;
            padding: 24px 28px;
        }
        .form-card-header .step-badge {
            font-size: 11px; font-weight: 600; opacity: .8; text-transform: uppercase; letter-spacing: .5px;
            margin-bottom: 6px;
        }
        .form-card-header h2 { font-size: 20px; font-weight: 700; }
        .form-card-header p { font-size: 13px; opacity: .8; margin-top: 4px; }
        .form-card-body { padding: 28px; }

        /* ── Draft resume banner ── */
        .draft-banner {
            background: #fffbeb; border: 1px solid #fbbf24; border-radius: 8px;
            padding: 12px 16px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
            font-size: 13px; color: #92400e;
        }
        .draft-banner .icon { font-size: 18px; }
        .draft-banner strong { font-weight: 600; }

        /* ── Form fields ── */
        .field-group { margin-bottom: 20px; }
        .field-label {
            display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;
        }
        .field-label .required { color: #ef4444; margin-left: 2px; }
        .field-help { font-size: 12px; color: #9ca3af; margin-top: 4px; }
        .field-input {
            width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px;
            font-size: 14px; color: #1e293b; transition: border-color .2s;
            background: #fff;
        }
        .field-input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.1); }
        textarea.field-input { resize: vertical; min-height: 100px; }
        select.field-input { cursor: pointer; }

        .field-row { display: grid; gap: 16px; }
        .field-row.cols-2 { grid-template-columns: 1fr 1fr; }
        .field-row.cols-3 { grid-template-columns: 1fr 1fr 1fr; }

        /* Radio / Checkbox */
        .choice-group { display: flex; flex-direction: column; gap: 8px; }
        .choice-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px;
            cursor: pointer; transition: all .15s;
        }
        .choice-item:hover { border-color: #93c5fd; background: #eff6ff; }
        .choice-item input { accent-color: #3b82f6; width: 16px; height: 16px; }
        .choice-item.selected { border-color: #3b82f6; background: #eff6ff; }

        /* Signature */
        .signature-wrap {
            border: 1.5px solid #e2e8f0; border-radius: 8px; overflow: hidden;
            background: #fafafa;
        }
        .signature-canvas { display: block; width: 100%; height: 160px; cursor: crosshair; touch-action: none; }
        .signature-actions {
            display: flex; gap: 8px; padding: 8px 12px;
            border-top: 1px solid #e2e8f0; background: #fff;
        }
        .sig-btn {
            padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 500;
            cursor: pointer; border: 1px solid #e2e8f0; background: #fff; color: #64748b;
        }
        .sig-btn:hover { background: #f8fafc; }

        /* File upload */
        .file-drop {
            border: 2px dashed #cbd5e1; border-radius: 8px; padding: 24px;
            text-align: center; cursor: pointer; transition: all .2s;
            background: #f8fafc;
        }
        .file-drop:hover, .file-drop.dragover { border-color: #3b82f6; background: #eff6ff; }
        .file-drop .file-icon { font-size: 28px; margin-bottom: 8px; }
        .file-drop p { font-size: 13px; color: #64748b; }
        .file-drop strong { color: #3b82f6; }

        /* Rating */
        .rating-stars { display: flex; gap: 6px; }
        .rating-stars .star {
            font-size: 28px; cursor: pointer; color: #d1d5db; transition: color .15s;
        }
        .rating-stars .star.active, .rating-stars .star:hover { color: #f59e0b; }

        /* Scale */
        .scale-wrap { display: flex; gap: 6px; flex-wrap: wrap; }
        .scale-btn {
            width: 40px; height: 40px; border-radius: 8px; border: 1.5px solid #e2e8f0;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 600; cursor: pointer; transition: all .15s;
            background: #fff; color: #64748b;
        }
        .scale-btn:hover { border-color: #3b82f6; color: #3b82f6; }
        .scale-btn.active { background: #3b82f6; border-color: #3b82f6; color: #fff; }

        /* Toggle */
        .toggle-wrap { display: flex; align-items: center; gap: 12px; }
        .toggle-switch {
            position: relative; width: 44px; height: 24px; cursor: pointer;
        }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute; inset: 0; background: #cbd5e1; border-radius: 12px; transition: .2s;
        }
        .toggle-slider::before {
            content: ''; position: absolute; width: 18px; height: 18px; border-radius: 50%;
            background: #fff; left: 3px; top: 3px; transition: .2s;
        }
        .toggle-switch input:checked + .toggle-slider { background: #3b82f6; }
        .toggle-switch input:checked + .toggle-slider::before { transform: translateX(20px); }

        /* Section header */
        .section-header { margin: 24px 0 16px; }
        .section-header h3 { font-size: 16px; font-weight: 700; color: #1e293b; }
        .section-header p { font-size: 13px; color: #64748b; margin-top: 2px; }
        .section-divider { border: none; border-top: 1px solid #e2e8f0; margin: 20px 0; }

        /* ── Auto-save indicator ── */
        .autosave-indicator {
            position: fixed; bottom: 80px; right: 20px;
            background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;
            padding: 8px 14px; font-size: 12px; color: #64748b;
            box-shadow: 0 2px 8px rgba(0,0,0,.1);
            display: flex; align-items: center; gap: 6px;
            opacity: 0; transition: opacity .3s; z-index: 200;
        }
        .autosave-indicator.show { opacity: 1; }
        .autosave-indicator .dot { width: 8px; height: 8px; border-radius: 50%; background: #22c55e; }
        .autosave-indicator.saving .dot { background: #f59e0b; animation: pulse 1s infinite; }
        @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: .4; } }

        /* ── Navigation ── */
        .form-nav {
            display: flex; align-items: center; justify-content: space-between;
            padding: 20px 28px; border-top: 1px solid #f1f5f9;
            background: #f8fafc;
        }
        .nav-btn {
            padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: 600;
            cursor: pointer; border: none; transition: all .2s;
        }
        .nav-btn.prev { background: #fff; border: 1.5px solid #e2e8f0; color: #64748b; }
        .nav-btn.prev:hover { background: #f8fafc; }
        .nav-btn.next { background: #3b82f6; color: #fff; }
        .nav-btn.next:hover { background: #2563eb; }
        .nav-btn.submit { background: #22c55e; color: #fff; }
        .nav-btn.submit:hover { background: #16a34a; }
        .nav-btn:disabled { opacity: .5; cursor: not-allowed; }
        .nav-info { font-size: 13px; color: #94a3b8; }

        /* ── Completed step overlay ── */
        .completed-badge {
            display: flex; align-items: center; gap: 8px;
            background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px;
            padding: 12px 16px; margin-bottom: 16px;
            font-size: 13px; color: #166534; font-weight: 500;
        }
        .completed-badge .check { font-size: 18px; }

        /* ── Success screen ── */
        .success-screen {
            display: none; text-align: center; padding: 60px 28px;
        }
        .success-screen.show { display: block; }
        .success-icon { font-size: 64px; margin-bottom: 16px; }
        .success-screen h2 { font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
        .success-screen p { font-size: 15px; color: #64748b; max-width: 400px; margin: 0 auto; }

        /* ── Validation ── */
        .field-error { font-size: 12px; color: #ef4444; margin-top: 4px; display: none; }
        .field-input.error { border-color: #ef4444; }

        @media (max-width: 600px) {
            .fill-main { margin: 16px auto; }
            .form-card-body { padding: 20px 16px; }
            .field-row.cols-2, .field-row.cols-3 { grid-template-columns: 1fr; }
            .fill-header { flex-direction: column; gap: 4px; align-items: flex-start; }
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="fill-header">
    <div class="logo">Advantage<span>HCS</span></div>
    <div class="patient-info">
        Hello, <strong>{{ $assignment->patient->first_name }} {{ $assignment->patient->last_name }}</strong>
        &nbsp;·&nbsp; {{ $funnel->name }}
    </div>
</div>

<!-- Progress -->
<div class="progress-bar-wrap">
    <div class="overall-progress">
        <div class="overall-bar">
            <div class="overall-bar-fill" id="overallBarFill" style="width: {{ $assignment->progress_percent }}%"></div>
        </div>
        <div class="overall-pct" id="overallPct">{{ $assignment->progress_percent }}% Complete</div>
    </div>
    <div class="progress-steps" id="progressSteps">
        @foreach($steps as $i => $step)
        <div class="step-dot {{ $step['status'] === 'completed' ? 'completed' : ($i == $currentStep ? 'active' : '') }}"
             id="stepDot{{ $i }}" onclick="goToStep({{ $i }})">
            <div class="step-num">
                @if($step['status'] === 'completed') ✓ @else {{ $i + 1 }} @endif
            </div>
            <div class="step-label">{{ Str::limit($step['form']->name, 12) }}</div>
        </div>
        @endforeach
    </div>
</div>

<!-- Main -->
<div class="fill-main">
    @if($assignment->status === 'completed')
    <div class="form-card active">
        <div class="success-screen show">
            <div class="success-icon">🎉</div>
            <h2>All Done!</h2>
            <p>You have already completed all forms in this funnel. Thank you, {{ $assignment->patient->first_name }}!</p>
        </div>
    </div>
    @else

    @foreach($steps as $i => $step)
    <div class="form-card {{ $i == $currentStep ? 'active' : '' }}" id="formCard{{ $i }}">

        @if($step['status'] === 'completed')
        <div class="form-card-header" style="background: linear-gradient(135deg, #22c55e, #16a34a);">
            <div class="step-badge">Step {{ $i + 1 }} of {{ $steps->count() }}</div>
            <h2>{{ $step['form']->name }}</h2>
            @if($step['form']->description)
            <p>{{ $step['form']->description }}</p>
            @endif
        </div>
        <div class="form-card-body">
            <div class="completed-badge">
                <span class="check">✅</span>
                This form was submitted {{ $step['last_saved'] ?? 'earlier' }}. Your data has been saved.
            </div>
            <p style="font-size:13px;color:#64748b;">You can review your answers below or move to the next step.</p>
        </div>
        @else
        <div class="form-card-header">
            <div class="step-badge">Step {{ $i + 1 }} of {{ $steps->count() }}</div>
            <h2>{{ $step['form']->name }}</h2>
            @if($step['form']->description)
            <p>{{ $step['form']->description }}</p>
            @endif
        </div>
        <div class="form-card-body">
            @if(!empty($step['saved_data']) && $step['status'] === 'draft')
            <div class="draft-banner">
                <span class="icon">💾</span>
                <div>
                    <strong>Draft restored</strong> — We saved your progress from {{ $step['last_saved'] ?? 'your last visit' }}.
                    Continue where you left off.
                </div>
            </div>
            @endif

            <form id="form{{ $i }}" data-form-id="{{ $step['form_id'] }}" data-step="{{ $i }}">
                @php
                    $fields = $step['form']->fields ?? [];
                    $savedData = $step['saved_data'] ?? [];
                @endphp

                @foreach($fields as $row)
                    @if(isset($row['columns']))
                        <div class="field-row cols-{{ count($row['columns']) }}">
                        @foreach($row['columns'] as $col)
                            @foreach($col['fields'] ?? [] as $field)
                                @include('assignments._field', ['field' => $field, 'savedData' => $savedData])
                            @endforeach
                        @endforeach
                        </div>
                    @else
                        @include('assignments._field', ['field' => $row, 'savedData' => $savedData])
                    @endif
                @endforeach

                @if(empty($fields))
                <p style="text-align:center;color:#94a3b8;padding:32px 0;">
                    This form has no fields yet. Click Next to continue.
                </p>
                @endif
            </form>
        </div>
        @endif

        <div class="form-nav">
            <div>
                @if($i > 0)
                <button class="nav-btn prev" onclick="goToStep({{ $i - 1 }})">← Previous</button>
                @endif
            </div>
            <div class="nav-info">Step {{ $i + 1 }} of {{ $steps->count() }}</div>
            <div>
                @if($step['status'] !== 'completed')
                    @if($i < $steps->count() - 1)
                    <button class="nav-btn next" onclick="submitStep({{ $i }}, false)">Save & Next →</button>
                    @else
                    <button class="nav-btn submit" onclick="submitStep({{ $i }}, true)">Submit All ✓</button>
                    @endif
                @else
                    @if($i < $steps->count() - 1)
                    <button class="nav-btn next" onclick="goToStep({{ $i + 1 }})">Next →</button>
                    @else
                    <button class="nav-btn submit" onclick="showSuccess()">Finish ✓</button>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @endforeach

    <!-- Success Screen -->
    <div class="form-card" id="successCard">
        <div class="success-screen show">
            <div class="success-icon">🎉</div>
            <h2>All Forms Submitted!</h2>
            <p>Thank you, {{ $assignment->patient->first_name }}! All your forms have been received. Our team will review them shortly.</p>
            <p style="margin-top:16px;font-size:13px;color:#94a3b8;">You may close this window.</p>
        </div>
    </div>

    @endif
</div>

<!-- Auto-save indicator -->
<div class="autosave-indicator" id="autosaveIndicator">
    <div class="dot"></div>
    <span id="autosaveText">Saved</span>
</div>

<script>
const TOKEN = '{{ $assignment->token }}';
const CSRF  = document.querySelector('meta[name="csrf-token"]').content;
let currentStep = {{ $currentStep }};
let autoSaveTimers = {};
let lastSavedData = {};

// ── Navigation ──────────────────────────────────────────────────────────────
function goToStep(index) {
    const cards = document.querySelectorAll('.form-card');
    cards.forEach(c => c.classList.remove('active'));
    document.getElementById('formCard' + index)?.classList.add('active');

    const dots = document.querySelectorAll('.step-dot');
    dots.forEach((d, i) => {
        d.classList.remove('active');
        if (i === index) d.classList.add('active');
    });

    currentStep = index;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ── Collect form data ────────────────────────────────────────────────────────
function collectFormData(stepIndex) {
    const form = document.getElementById('form' + stepIndex);
    if (!form) return {};

    const data = {};
    // Text inputs, selects, textareas
    form.querySelectorAll('[name]').forEach(el => {
        if (el.type === 'checkbox') {
            if (!data[el.name]) data[el.name] = [];
            if (el.checked) data[el.name].push(el.value);
        } else if (el.type === 'radio') {
            if (el.checked) data[el.name] = el.value;
        } else {
            data[el.name] = el.value;
        }
    });

    // Signature canvases
    form.querySelectorAll('canvas[data-field-id]').forEach(canvas => {
        data[canvas.dataset.fieldId] = canvas.toDataURL();
    });

    // Rating stars
    form.querySelectorAll('.rating-stars[data-field-id]').forEach(el => {
        data[el.dataset.fieldId] = el.dataset.value || '';
    });

    // Scale buttons
    form.querySelectorAll('.scale-wrap[data-field-id]').forEach(el => {
        data[el.dataset.fieldId] = el.dataset.value || '';
    });

    // Toggles
    form.querySelectorAll('.toggle-switch input').forEach(el => {
        data[el.name] = el.checked ? 'yes' : 'no';
    });

    return data;
}

// ── Auto-save ────────────────────────────────────────────────────────────────
function showAutosave(saving) {
    const el = document.getElementById('autosaveIndicator');
    const dot = el.querySelector('.dot');
    const txt = document.getElementById('autosaveText');

    if (saving) {
        el.classList.add('saving', 'show');
        txt.textContent = 'Saving…';
    } else {
        el.classList.remove('saving');
        el.classList.add('show');
        txt.textContent = 'Saved at ' + new Date().toLocaleTimeString([], {hour:'2-digit',minute:'2-digit'});
        setTimeout(() => el.classList.remove('show'), 3000);
    }
}

function autoSave(stepIndex) {
    const form = document.getElementById('form' + stepIndex);
    if (!form) return;

    const formId = form.dataset.formId;
    const data   = collectFormData(stepIndex);

    // Don't save if nothing changed
    const dataStr = JSON.stringify(data);
    if (dataStr === lastSavedData[stepIndex]) return;
    lastSavedData[stepIndex] = dataStr;

    showAutosave(true);

    fetch('/fill/' + TOKEN + '/save', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
        },
        body: JSON.stringify({ form_id: parseInt(formId), data }),
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') showAutosave(false);
    })
    .catch(() => showAutosave(false));
}

// Auto-save every 30 seconds for the current step
setInterval(() => autoSave(currentStep), 30000);

// Auto-save on input blur
document.addEventListener('blur', function(e) {
    if (e.target.closest('form[id^="form"]')) {
        const form = e.target.closest('form');
        const stepIndex = parseInt(form.dataset.step);
        clearTimeout(autoSaveTimers[stepIndex]);
        autoSaveTimers[stepIndex] = setTimeout(() => autoSave(stepIndex), 1000);
    }
}, true);

// ── Submit Step ──────────────────────────────────────────────────────────────
function submitStep(stepIndex, isLast) {
    const form   = document.getElementById('form' + stepIndex);
    if (!form) return;

    const formId = form.dataset.formId;
    const data   = collectFormData(stepIndex);

    // Basic required validation
    let valid = true;
    form.querySelectorAll('[required]').forEach(el => {
        el.classList.remove('error');
        const errEl = el.parentElement.querySelector('.field-error');
        if (errEl) errEl.style.display = 'none';

        if (!el.value.trim()) {
            el.classList.add('error');
            if (errEl) errEl.style.display = 'block';
            valid = false;
        }
    });

    if (!valid) {
        const firstError = form.querySelector('.error');
        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
    }

    const btn = document.querySelector('#formCard' + stepIndex + ' .nav-btn.next, #formCard' + stepIndex + ' .nav-btn.submit');
    if (btn) { btn.disabled = true; btn.textContent = 'Saving…'; }

    fetch('/fill/' + TOKEN + '/submit-step', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF,
        },
        body: JSON.stringify({ form_id: parseInt(formId), data }),
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            // Mark step dot as completed
            const dot = document.getElementById('stepDot' + stepIndex);
            if (dot) {
                dot.classList.add('completed');
                dot.classList.remove('active');
                dot.querySelector('.step-num').textContent = '✓';
            }

            // Update overall progress
            document.getElementById('overallBarFill').style.width = res.progress_percent + '%';
            document.getElementById('overallPct').textContent = res.progress_percent + '% Complete';

            if (isLast || res.is_last_step) {
                showSuccess();
            } else {
                goToStep(stepIndex + 1);
            }
        }
    })
    .catch(() => {
        if (btn) { btn.disabled = false; btn.textContent = isLast ? 'Submit All ✓' : 'Save & Next →'; }
        alert('Error saving. Please try again.');
    });
}

function showSuccess() {
    const cards = document.querySelectorAll('.form-card');
    cards.forEach(c => c.classList.remove('active'));
    document.getElementById('successCard')?.classList.add('active');
    window.scrollTo({ top: 0, behavior: 'smooth' });

    // Mark all dots completed
    document.querySelectorAll('.step-dot').forEach(d => {
        d.classList.add('completed');
        d.classList.remove('active');
        d.querySelector('.step-num').textContent = '✓';
    });
    document.getElementById('overallBarFill').style.width = '100%';
    document.getElementById('overallPct').textContent = '100% Complete';
}

// ── Signature Pad ────────────────────────────────────────────────────────────
function initSignaturePad(canvas) {
    const ctx = canvas.getContext('2d');
    let drawing = false;

    canvas.addEventListener('mousedown', e => { drawing = true; ctx.beginPath(); ctx.moveTo(...getPos(e, canvas)); });
    canvas.addEventListener('mousemove', e => { if (!drawing) return; ctx.lineTo(...getPos(e, canvas)); ctx.strokeStyle = '#1e293b'; ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.stroke(); });
    canvas.addEventListener('mouseup', () => drawing = false);
    canvas.addEventListener('mouseleave', () => drawing = false);

    canvas.addEventListener('touchstart', e => { e.preventDefault(); drawing = true; ctx.beginPath(); ctx.moveTo(...getPos(e.touches[0], canvas)); });
    canvas.addEventListener('touchmove', e => { e.preventDefault(); if (!drawing) return; ctx.lineTo(...getPos(e.touches[0], canvas)); ctx.strokeStyle = '#1e293b'; ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.stroke(); });
    canvas.addEventListener('touchend', () => drawing = false);
}

function getPos(e, canvas) {
    const r = canvas.getBoundingClientRect();
    return [e.clientX - r.left, e.clientY - r.top];
}

function clearSignature(canvasId) {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext('2d');
    ctx.clearRect(0, 0, canvas.width, canvas.height);
}

// ── Rating Stars ─────────────────────────────────────────────────────────────
function setRating(wrap, value) {
    wrap.dataset.value = value;
    wrap.querySelectorAll('.star').forEach((s, i) => {
        s.classList.toggle('active', i < value);
    });
}

// ── Scale ────────────────────────────────────────────────────────────────────
function setScale(wrap, value) {
    wrap.dataset.value = value;
    wrap.querySelectorAll('.scale-btn').forEach(b => {
        b.classList.toggle('active', b.dataset.val == value);
    });
}

// ── Init ─────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.signature-canvas').forEach(initSignaturePad);
});
</script>
</body>
</html>
