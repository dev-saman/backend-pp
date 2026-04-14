@php
    $fieldId   = $field['id'] ?? uniqid('f_');
    $label     = $field['label'] ?? '';
    $type      = $field['type'] ?? 'text';
    $required  = !empty($field['required']);
    $placeholder = $field['placeholder'] ?? '';
    $helpText  = $field['helpText'] ?? '';
    $options   = $field['options'] ?? [];
    $savedVal  = $savedData[$fieldId] ?? '';
@endphp

@if($type === 'header')
<div class="section-header">
    <h3>{{ $label }}</h3>
    @if($helpText)<p>{{ $helpText }}</p>@endif
</div>

@elseif($type === 'paragraph')
<p style="font-size:14px;color:#64748b;margin-bottom:16px;">{{ $label }}</p>

@elseif($type === 'divider')
<hr class="section-divider">

@elseif($type === 'signature')
<div class="field-group">
    <label class="field-label">{{ $label }}@if($required)<span class="required">*</span>@endif</label>
    <div class="signature-wrap">
        <canvas class="signature-canvas" id="sig_{{ $fieldId }}" data-field-id="{{ $fieldId }}"
                width="700" height="160"></canvas>
        <div class="signature-actions">
            <button type="button" class="sig-btn" onclick="clearSignature('sig_{{ $fieldId }}')">✕ Clear</button>
            <span style="font-size:11px;color:#94a3b8;margin-left:auto;">Sign above</span>
        </div>
    </div>
    @if($helpText)<div class="field-help">{{ $helpText }}</div>@endif
</div>

@elseif($type === 'radio')
<div class="field-group">
    <label class="field-label">{{ $label }}@if($required)<span class="required">*</span>@endif</label>
    <div class="choice-group">
        @foreach($options as $opt)
        <label class="choice-item {{ $savedVal === $opt ? 'selected' : '' }}">
            <input type="radio" name="{{ $fieldId }}" value="{{ $opt }}"
                   {{ $savedVal === $opt ? 'checked' : '' }}
                   {{ $required ? 'required' : '' }}
                   onchange="this.closest('.choice-group').querySelectorAll('.choice-item').forEach(el=>el.classList.remove('selected')); this.closest('.choice-item').classList.add('selected')">
            {{ $opt }}
        </label>
        @endforeach
    </div>
    @if($helpText)<div class="field-help">{{ $helpText }}</div>@endif
</div>

@elseif($type === 'checkbox')
<div class="field-group">
    <label class="field-label">{{ $label }}@if($required)<span class="required">*</span>@endif</label>
    <div class="choice-group">
        @foreach($options as $opt)
        @php $checked = is_array($savedVal) ? in_array($opt, $savedVal) : false; @endphp
        <label class="choice-item {{ $checked ? 'selected' : '' }}">
            <input type="checkbox" name="{{ $fieldId }}[]" value="{{ $opt }}"
                   {{ $checked ? 'checked' : '' }}
                   onchange="this.closest('.choice-item').classList.toggle('selected', this.checked)">
            {{ $opt }}
        </label>
        @endforeach
    </div>
    @if($helpText)<div class="field-help">{{ $helpText }}</div>@endif
</div>

@elseif($type === 'dropdown' || $type === 'select')
<div class="field-group">
    <label class="field-label">{{ $label }}@if($required)<span class="required">*</span>@endif</label>
    <select name="{{ $fieldId }}" class="field-input" {{ $required ? 'required' : '' }}>
        <option value="">{{ $placeholder ?: 'Select an option' }}</option>
        @foreach($options as $opt)
        <option value="{{ $opt }}" {{ $savedVal === $opt ? 'selected' : '' }}>{{ $opt }}</option>
        @endforeach
    </select>
    @if($helpText)<div class="field-help">{{ $helpText }}</div>@endif
    <div class="field-error">This field is required.</div>
</div>

@elseif($type === 'textarea')
<div class="field-group">
    <label class="field-label">{{ $label }}@if($required)<span class="required">*</span>@endif</label>
    <textarea name="{{ $fieldId }}" class="field-input" rows="4"
              placeholder="{{ $placeholder }}"
              {{ $required ? 'required' : '' }}>{{ $savedVal }}</textarea>
    @if($helpText)<div class="field-help">{{ $helpText }}</div>@endif
    <div class="field-error">This field is required.</div>
</div>

@elseif($type === 'rating')
<div class="field-group">
    <label class="field-label">{{ $label }}@if($required)<span class="required">*</span>@endif</label>
    <div class="rating-stars" data-field-id="{{ $fieldId }}" data-value="{{ $savedVal }}">
        @for($s = 1; $s <= 5; $s++)
        <span class="star {{ $savedVal >= $s ? 'active' : '' }}"
              onclick="setRating(this.parentElement, {{ $s }})">★</span>
        @endfor
    </div>
    @if($helpText)<div class="field-help">{{ $helpText }}</div>@endif
</div>

@elseif($type === 'scale')
<div class="field-group">
    <label class="field-label">{{ $label }}@if($required)<span class="required">*</span>@endif</label>
    <div class="scale-wrap" data-field-id="{{ $fieldId }}" data-value="{{ $savedVal }}">
        @for($s = 1; $s <= 10; $s++)
        <button type="button" class="scale-btn {{ $savedVal == $s ? 'active' : '' }}"
                data-val="{{ $s }}" onclick="setScale(this.parentElement, {{ $s }})">{{ $s }}</button>
        @endfor
    </div>
    @if($helpText)<div class="field-help">{{ $helpText }}</div>@endif
</div>

@elseif($type === 'toggle')
<div class="field-group">
    <div class="toggle-wrap">
        <label class="toggle-switch">
            <input type="checkbox" name="{{ $fieldId }}" {{ $savedVal === 'yes' ? 'checked' : '' }}>
            <span class="toggle-slider"></span>
        </label>
        <span class="field-label" style="margin-bottom:0;">{{ $label }}</span>
    </div>
    @if($helpText)<div class="field-help">{{ $helpText }}</div>@endif
</div>

@elseif($type === 'file')
<div class="field-group">
    <label class="field-label">{{ $label }}@if($required)<span class="required">*</span>@endif</label>
    <div class="file-drop" onclick="document.getElementById('file_{{ $fieldId }}').click()"
         ondragover="event.preventDefault();this.classList.add('dragover')"
         ondragleave="this.classList.remove('dragover')"
         ondrop="event.preventDefault();this.classList.remove('dragover')">
        <div class="file-icon">📎</div>
        <p>Drag & drop a file here or <strong>click to browse</strong></p>
        <input type="file" id="file_{{ $fieldId }}" name="{{ $fieldId }}" style="display:none"
               onchange="document.querySelector('[for=file_{{ $fieldId }}]')">
    </div>
    @if($helpText)<div class="field-help">{{ $helpText }}</div>@endif
</div>

@elseif($type === 'address')
<div class="field-group">
    <label class="field-label">{{ $label }}@if($required)<span class="required">*</span>@endif</label>
    <div style="display:flex;flex-direction:column;gap:8px;">
        <input type="text" name="{{ $fieldId }}_street" class="field-input" placeholder="Street Address"
               value="{{ $savedData[$fieldId . '_street'] ?? '' }}" {{ $required ? 'required' : '' }}>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;">
            <input type="text" name="{{ $fieldId }}_city" class="field-input" placeholder="City"
                   value="{{ $savedData[$fieldId . '_city'] ?? '' }}">
            <input type="text" name="{{ $fieldId }}_state" class="field-input" placeholder="State"
                   value="{{ $savedData[$fieldId . '_state'] ?? '' }}">
            <input type="text" name="{{ $fieldId }}_zip" class="field-input" placeholder="ZIP"
                   value="{{ $savedData[$fieldId . '_zip'] ?? '' }}">
        </div>
    </div>
    @if($helpText)<div class="field-help">{{ $helpText }}</div>@endif
</div>

@elseif($type === 'name' || $type === 'fullname')
<div class="field-group">
    <label class="field-label">{{ $label }}@if($required)<span class="required">*</span>@endif</label>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
        <input type="text" name="{{ $fieldId }}_first" class="field-input" placeholder="First Name"
               value="{{ $savedData[$fieldId . '_first'] ?? '' }}" {{ $required ? 'required' : '' }}>
        <input type="text" name="{{ $fieldId }}_last" class="field-input" placeholder="Last Name"
               value="{{ $savedData[$fieldId . '_last'] ?? '' }}" {{ $required ? 'required' : '' }}>
    </div>
    @if($helpText)<div class="field-help">{{ $helpText }}</div>@endif
</div>

@else
{{-- Default: text, email, phone, number, date, time, password --}}
<div class="field-group">
    <label class="field-label">{{ $label }}@if($required)<span class="required">*</span>@endif</label>
    <input type="{{ in_array($type, ['email','number','date','time','password','tel']) ? $type : 'text' }}"
           name="{{ $fieldId }}"
           class="field-input"
           placeholder="{{ $placeholder }}"
           value="{{ $savedVal }}"
           {{ $required ? 'required' : '' }}>
    @if($helpText)<div class="field-help">{{ $helpText }}</div>@endif
    <div class="field-error">This field is required.</div>
</div>
@endif
