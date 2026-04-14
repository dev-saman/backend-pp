@extends('layouts.app')

@section('title', $funnel->name . ' - AdvantageHCS Admin')
@section('page-title', $funnel->name)
@section('page-subtitle', 'Funnel Builder & Settings')

@section('header-actions')
    <a href="{{ route('funnels.edit', $funnel) }}" class="btn btn-secondary">
        <i class="fas fa-cog"></i> Settings
    </a>
    @if($funnel->slug)
        <button onclick="copyFunnelLink()" class="btn btn-secondary">
            <i class="fas fa-link"></i> Copy Link
        </button>
    @endif
    <a href="{{ route('funnels.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
@endsection

@section('content')

<!-- Meta Bar -->
<div style="display:flex; gap:12px; align-items:center; margin-bottom:20px; flex-wrap:wrap;">
    <span class="badge {{ $funnel->status === 'active' ? 'badge-success' : ($funnel->status === 'draft' ? 'badge-warning' : 'badge-secondary') }}" style="font-size:13px; padding:6px 12px;">
        {{ ucfirst($funnel->status) }}
    </span>
    <span style="font-size:13px; color:#6b7280;"><i class="fas fa-layer-group" style="margin-right:4px;"></i>{{ count($funnel->form_ids ?? []) }} form(s)</span>
    <span style="font-size:13px; color:#6b7280;"><i class="fas fa-check-circle" style="margin-right:4px;"></i>{{ $funnel->completion_count }} completions</span>
    @if($funnel->slug)
        <code style="font-size:12px; background:#f3f4f6; padding:4px 10px; border-radius:4px; color:#374151;">
            {{ url('/f/' . $funnel->slug) }}
        </code>
    @endif
</div>

<!-- Black Funnel Canvas -->
<div style="
    background: #000000;
    border-radius: 12px;
    min-height: 640px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 48px;
    position: relative;
    overflow: hidden;
">
    <!-- Subtle dot grid -->
    <div style="
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle, #2a2a2a 1px, transparent 1px);
        background-size: 28px 28px;
    "></div>

    <!-- Flow lines (decorative) -->
    <div style="position:absolute; top:0; left:50%; transform:translateX(-50%); width:2px; height:100%; background:linear-gradient(to bottom, transparent, rgba(200,16,46,0.2), transparent);"></div>

    <div style="position:relative; text-align:center; max-width:560px; width:100%;">

        <div style="
            width: 72px;
            height: 72px;
            background: rgba(200,16,46,0.15);
            border: 2px solid rgba(200,16,46,0.4);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
        ">
            <i class="fas fa-filter" style="font-size:28px; color:#C8102E;"></i>
        </div>

        <h2 style="color:#ffffff; font-size:22px; font-weight:700; margin-bottom:12px;">
            Funnel Builder
        </h2>
        <p style="color:#9ca3af; font-size:15px; line-height:1.6; margin-bottom:36px;">
            The visual funnel builder will be implemented here. You'll be able to drag and arrange forms, set step conditions, configure completion actions, and preview the patient experience.
        </p>

        <!-- Funnel Steps Preview -->
        @php $formIds = $funnel->form_ids ?? []; @endphp
        @if(!empty($formIds))
            <div style="display:flex; flex-direction:column; gap:0; align-items:center; width:100%; max-width:400px; margin:0 auto 32px;">
                @foreach($formIds as $index => $formId)
                    @php $form = \App\Models\Form::find($formId); @endphp
                    @if($form)
                    <div style="background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12); border-radius:10px; padding:14px 20px; width:100%; display:flex; align-items:center; gap:12px;">
                        <div style="width:28px; height:28px; background:rgba(200,16,46,0.2); border:1px solid rgba(200,16,46,0.5); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:#C8102E; flex-shrink:0;">
                            {{ $index + 1 }}
                        </div>
                        <div style="text-align:left;">
                            <div style="color:#ffffff; font-weight:500; font-size:14px;">{{ $form->name }}</div>
                            <div style="color:#6b7280; font-size:12px;">{{ ucfirst($form->category ?? 'General') }}</div>
                        </div>
                        <span class="badge {{ $form->status === 'active' ? 'badge-success' : 'badge-warning' }}" style="margin-left:auto; font-size:11px;">
                            {{ ucfirst($form->status) }}
                        </span>
                    </div>
                    @if(!$loop->last)
                    <div style="width:2px; height:20px; background:rgba(200,16,46,0.3);"></div>
                    @endif
                    @endif
                @endforeach
            </div>
        @endif

        <div style="margin-top:8px; padding:16px; background:rgba(200,16,46,0.1); border:1px solid rgba(200,16,46,0.3); border-radius:8px;">
            <p style="color:#fca5a5; font-size:13px; margin:0;">
                <i class="fas fa-info-circle" style="margin-right:6px;"></i>
                Coming Soon — Visual funnel builder with step ordering, conditional branching, and shareable patient links.
            </p>
        </div>
    </div>
</div>

<script>
function copyFunnelLink() {
    const url = '{{ url("/f/" . $funnel->slug) }}';
    navigator.clipboard.writeText(url).then(() => {
        alert('Funnel link copied: ' + url);
    });
}
</script>
@endsection
