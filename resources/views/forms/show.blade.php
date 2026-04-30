@extends('layouts.app')

@section('title', $form->name . ' — Preview')
@section('page-title', 'Form Preview')
@section('page-subtitle', 'Preview of: ' . $form->name)

@section('header-actions')
    <a href="{{ route('forms.edit', $form) }}" class="btn btn-secondary">
        <i class="fas fa-edit"></i> Edit Settings
    </a>
    <a href="{{ route('forms.builder', $form) }}" class="btn btn-secondary">
        <i class="fas fa-tools"></i> Open Builder
    </a>
    <a href="{{ route('forms.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Forms
    </a>
@endsection

@section('content')
<style>
/* ── Preview wrapper ────────────────────────────────────────── */
.preview-outer {
    max-width: 720px;
    margin: 0 auto;
    padding-bottom: 60px;
}

/* ── Preview badge bar ──────────────────────────────────────── */
.preview-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.preview-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}
.preview-badge.active  { background: #dcfce7; color: #16a34a; }
.preview-badge.draft   { background: #fef9c3; color: #a16207; }
.preview-badge.inactive { background: #f3f4f6; color: #6b7280; }
.preview-meta-item { font-size: 13px; color: #6b7280; }

/* ── Form card ──────────────────────────────────────────────── */
.pv-card {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    overflow: hidden;
}
.pv-card-header {
    background: #8B1A1A;
    padding: 28px 36px;
    color: #fff;
}
.pv-card-title {
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 4px;
}
.pv-card-desc {
    font-size: 14px;
    opacity: 0.85;
    line-height: 1.5;
}
.pv-card-body {
    padding: 32px 36px;
}

/* ── Fields ─────────────────────────────────────────────────── */
.pv-field { margin-bottom: 22px; }
.pv-label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 7px;
}
.pv-required { color: #dc2626; margin-left: 3px; }
.pv-help { font-size: 11px; color: #9ca3af; margin-top: 5px; }
.pv-input {
    width: 100%;
    padding: 10px 14px;
    background: #f9fafb;
    border: 1.5px solid #e5e7eb;
    border-radius: 9px;
    color: #374151;
    font-size: 14px;
    font-family: inherit;
    pointer-events: none;
}
.pv-textarea {
    width: 100%;
    padding: 10px 14px;
    height: 100px;
    resize: none;
    background: #f9fafb;
    border: 1.5px solid #e5e7eb;
    border-radius: 9px;
    color: #374151;
    font-size: 14px;
    font-family: inherit;
    pointer-events: none;
}
.pv-select {
    width: 100%;
    padding: 10px 14px;
    background: #f9fafb;
    border: 1.5px solid #e5e7eb;
    border-radius: 9px;
    color: #374151;
    font-size: 14px;
    font-family: inherit;
    pointer-events: none;
}

/* Choice */
.pv-choice-group { display: flex; flex-direction: column; gap: 8px; }
.pv-choice-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    border-radius: 9px;
    border: 1.5px solid #e5e7eb;
    background: #f9fafb;
    font-size: 14px;
    color: #374151;
    pointer-events: none;
}
.pv-choice-item input { accent-color: #8B1A1A; width: 15px; height: 15px; }

/* Toggle */
.pv-toggle-row { display: flex; align-items: center; justify-content: space-between; }
.pv-toggle-track {
    width: 44px; height: 24px; border-radius: 24px;
    background: #e5e7eb; position: relative; flex-shrink: 0;
}
.pv-toggle-knob {
    position: absolute; top: 3px; left: 3px;
    width: 18px; height: 18px; border-radius: 50%;
    background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,.2);
}

/* Rating */
.pv-rating { display: flex; gap: 6px; }
.pv-star { font-size: 28px; color: #e5e7eb; line-height: 1; }

/* Scale */
.pv-scale { display: flex; gap: 6px; flex-wrap: wrap; }
.pv-scale-num {
    width: 40px; height: 40px; border-radius: 8px;
    border: 1.5px solid #e5e7eb; background: #f9fafb;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 600; color: #9ca3af;
}
.pv-scale-labels { display: flex; justify-content: space-between; font-size: 11px; color: #9ca3af; margin-top: 5px; }

/* Signature */
.pv-sig-box {
    border: 1.5px solid #e5e7eb;
    border-radius: 9px;
    height: 110px;
    background: #f9fafb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 13px;
}

/* File upload */
.pv-file-zone {
    border: 2px dashed #e5e7eb;
    border-radius: 9px;
    padding: 28px 20px;
    text-align: center;
    background: #f9fafb;
    color: #9ca3af;
    font-size: 13px;
}
.pv-file-zone i { font-size: 28px; display: block; margin-bottom: 8px; }

/* Section header */
.pv-section-header { margin-bottom: 4px; }
.pv-section-header h3 {
    font-size: 18px; font-weight: 700; color: #111827;
    padding-bottom: 10px; border-bottom: 2px solid #e5e7eb;
}

/* Paragraph */
.pv-para { font-size: 14px; color: #6b7280; line-height: 1.7; }

/* Divider */
.pv-divider { border: none; border-top: 1px solid #e5e7eb; margin: 4px 0; }

/* Multi-col row */
.pv-row { display: flex; gap: 18px; }
.pv-row .pv-field { flex: 1; min-width: 0; }

/* Name / Address */
.pv-name-row, .pv-addr-row { display: flex; gap: 10px; }
.pv-name-row .pv-input, .pv-addr-row .pv-input { flex: 1; }

/* Submit button preview */
.pv-submit-btn {
    width: 100%; padding: 13px 24px; border-radius: 10px;
    border: none; background: #8B1A1A; color: #fff;
    font-size: 15px; font-weight: 700; cursor: not-allowed;
    opacity: 0.85; font-family: inherit;
}

/* Preview notice */
.preview-notice {
    background: #fffbeb;
    border: 1px solid #fde68a;
    border-radius: 10px;
    padding: 12px 18px;
    font-size: 13px;
    color: #92400e;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

@media (max-width: 600px) {
    .pv-card-header, .pv-card-body { padding: 22px 18px; }
    .pv-row { flex-direction: column; gap: 0; }
    .pv-name-row, .pv-addr-row { flex-direction: column; }
}
</style>

<div class="preview-outer">

    {{-- Meta bar --}}
    <div class="preview-meta">
        <span class="preview-badge {{ $form->is_active ? 'active' : 'inactive' }}">
            <i class="fas fa-circle" style="font-size:8px;"></i>
            {{ $form->is_active ? 'Active' : 'Inactive' }}
        </span>
        <span class="preview-meta-item"><i class="fas fa-inbox" style="margin-right:4px;"></i>{{ $form->submission_count }} submissions</span>
        <span class="preview-meta-item"><i class="fas fa-clock" style="margin-right:4px;"></i>Created {{ $form->created_at->format('M d, Y') }}</span>
    </div>

    {{-- Preview notice --}}
    <div class="preview-notice">
        <i class="fas fa-eye"></i>
        <span>This is a <strong>read-only preview</strong> of the form as patients will see it. Fields are not interactive.</span>
    </div>

    {{-- Form card --}}
    <div class="pv-card">
        <div class="pv-card-header">
            <div class="pv-card-title">{{ $form->name }}</div>
            @if($form->description)
                <div class="pv-card-desc">{{ $form->description }}</div>
            @endif
        </div>
        <div class="pv-card-body" id="pvFormBody">
            {{-- Fields rendered by JS below --}}
        </div>
    </div>

</div>

<script>
const schema = @json($form->fields ?? ['rows' => []]);
const rows = schema.rows || [];

function esc(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g,'&amp;')
        .replace(/</g,'&lt;')
        .replace(/>/g,'&gt;')
        .replace(/"/g,'&quot;');
}

function renderPreview() {
    const container = document.getElementById('pvFormBody');
    if (!rows.length) {
        container.innerHTML = '<div style="text-align:center;padding:48px;color:#9ca3af;"><i class="fas fa-wpforms" style="font-size:36px;display:block;margin-bottom:12px;"></i>No fields added yet. Open the builder to add fields.</div>';
        return;
    }
    rows.forEach(row => {
        if (row.cols && row.cols.length > 1) {
            const rowEl = document.createElement('div');
            rowEl.className = 'pv-row';
            row.cols.forEach(col => (col.fields || []).forEach(field => rowEl.appendChild(renderField(field))));
            container.appendChild(rowEl);
        } else {
            (row.cols || []).forEach(col => (col.fields || []).forEach(field => container.appendChild(renderField(field))));
        }
    });
    // Append submit button at bottom
    const submitWrap = document.createElement('div');
    submitWrap.style.marginTop = '16px';
    submitWrap.innerHTML = `<button class="pv-submit-btn" disabled>Submit Form</button>`;
    container.appendChild(submitWrap);
}

function renderField(field) {
    const wrap = document.createElement('div');
    wrap.className = 'pv-field';
    const req = field.required ? `<span class="pv-required">*</span>` : '';

    switch (field.type) {
        case 'header':
            wrap.innerHTML = `<div class="pv-section-header"><h3>${esc(field.content || 'Section')}</h3></div>`;
            break;
        case 'paragraph':
            wrap.innerHTML = `<div class="pv-para">${esc(field.content || '')}</div>`;
            break;
        case 'divider':
            wrap.innerHTML = `<hr class="pv-divider">`;
            break;
        case 'image':
            wrap.innerHTML = `<div style="text-align:center;padding:12px 0;"><div style="border:2px dashed #e5e7eb;border-radius:9px;padding:20px;color:#9ca3af;font-size:13px;"><i class="fas fa-image" style="font-size:24px;display:block;margin-bottom:6px;"></i>Image placeholder</div></div>`;
            break;
        case 'submit':
            wrap.innerHTML = '';
            break;
        case 'text': case 'email': case 'phone': case 'number': case 'date': case 'time': case 'password':
            wrap.innerHTML = `<label class="pv-label">${esc(field.label)}${req}</label>
                <input class="pv-input" type="${field.type === 'phone' ? 'tel' : field.type}" placeholder="${esc(field.placeholder || '')}" readonly tabindex="-1">
                ${field.helpText ? `<div class="pv-help">${esc(field.helpText)}</div>` : ''}`;
            break;
        case 'textarea':
            wrap.innerHTML = `<label class="pv-label">${esc(field.label)}${req}</label>
                <textarea class="pv-textarea" placeholder="${esc(field.placeholder || '')}" readonly tabindex="-1"></textarea>
                ${field.helpText ? `<div class="pv-help">${esc(field.helpText)}</div>` : ''}`;
            break;
        case 'dropdown':
            wrap.innerHTML = `<label class="pv-label">${esc(field.label)}${req}</label>
                <select class="pv-select" disabled tabindex="-1">
                    <option>${esc(field.placeholder || 'Select an option...')}</option>
                    ${(field.options || []).map(o => `<option>${esc(o)}</option>`).join('')}
                </select>
                ${field.helpText ? `<div class="pv-help">${esc(field.helpText)}</div>` : ''}`;
            break;
        case 'radio':
            wrap.innerHTML = `<label class="pv-label">${esc(field.label)}${req}</label>
                <div class="pv-choice-group">
                    ${(field.options || []).map(o => `<div class="pv-choice-item"><input type="radio" disabled><span>${esc(o)}</span></div>`).join('')}
                </div>`;
            break;
        case 'checkbox':
            wrap.innerHTML = `<label class="pv-label">${esc(field.label)}${req}</label>
                <div class="pv-choice-group">
                    ${(field.options || []).map(o => `<div class="pv-choice-item"><input type="checkbox" disabled><span>${esc(o)}</span></div>`).join('')}
                </div>
                ${field.helpText ? `<div class="pv-help">${esc(field.helpText)}</div>` : ''}`;
            break;
        case 'toggle':
            wrap.innerHTML = `<div class="pv-toggle-row">
                <label class="pv-label" style="margin:0;">${esc(field.label)}${req}</label>
                <div class="pv-toggle-track"><div class="pv-toggle-knob"></div></div>
            </div>`;
            break;
        case 'rating':
            wrap.innerHTML = `<label class="pv-label">${esc(field.label)}${req}</label>
                <div class="pv-rating">${[1,2,3,4,5].map(() => `<span class="pv-star">★</span>`).join('')}</div>`;
            break;
        case 'scale':
            wrap.innerHTML = `<label class="pv-label">${esc(field.label)}${req}</label>
                <div class="pv-scale">${[1,2,3,4,5,6,7,8,9,10].map(n => `<div class="pv-scale-num">${n}</div>`).join('')}</div>
                <div class="pv-scale-labels"><span>Not at all</span><span>Extremely</span></div>`;
            break;
        case 'signature':
            wrap.innerHTML = `<label class="pv-label">${esc(field.label)}${req}</label>
                <div class="pv-sig-box"><i class="fas fa-signature" style="margin-right:8px;"></i>Signature area</div>
                <div style="font-size:11px;color:#9ca3af;margin-top:5px;text-align:center;">Draw your signature above</div>`;
            break;
        case 'file':
            wrap.innerHTML = `<label class="pv-label">${esc(field.label)}${req}</label>
                <div class="pv-file-zone">
                    <i class="fas fa-paperclip"></i>
                    Click to upload or drag & drop<br>
                    <span style="font-size:11px;">PDF, JPG, PNG, DOCX up to 10MB</span>
                </div>`;
            break;
        case 'address':
            wrap.innerHTML = `<label class="pv-label">${esc(field.label)}${req}</label>
                <div style="display:flex;flex-direction:column;gap:8px;">
                    <input class="pv-input" placeholder="Street Address" readonly tabindex="-1">
                    <div class="pv-addr-row">
                        <input class="pv-input" placeholder="City" readonly tabindex="-1">
                        <input class="pv-input" style="max-width:90px;" placeholder="State" readonly tabindex="-1">
                        <input class="pv-input" style="max-width:100px;" placeholder="ZIP Code" readonly tabindex="-1">
                    </div>
                </div>`;
            break;
        case 'name':
            wrap.innerHTML = `<label class="pv-label">${esc(field.label)}${req}</label>
                <div class="pv-name-row">
                    <input class="pv-input" placeholder="First Name" readonly tabindex="-1">
                    <input class="pv-input" placeholder="Last Name" readonly tabindex="-1">
                </div>`;
            break;
        default:
            wrap.innerHTML = `<label class="pv-label">${esc(field.label || field.type)}${req}</label>
                <input class="pv-input" type="text" placeholder="${esc(field.placeholder || '')}" readonly tabindex="-1">`;
    }
    return wrap;
}

renderPreview();
</script>
@endsection
