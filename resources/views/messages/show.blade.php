@extends('layouts.app')

@section('title', $message->subject . ' - AdvantageHCS Admin')
@section('page-title', 'Messages')
@section('page-subtitle', 'Secure messaging center')

@section('header-actions')
    <a href="{{ route('messages.create') }}" class="btn btn-primary">
        <i class="fas fa-pen"></i> New Message
    </a>
@endsection

@section('content')
<div style="display:grid; grid-template-columns:340px 1fr; gap:0; background:white; border-radius:12px; border:1px solid #e5e7eb; overflow:hidden; min-height:600px;">

    <!-- Message List -->
    <div style="border-right:1px solid #e5e7eb; overflow-y:auto;">
        <div style="padding:16px; border-bottom:1px solid #e5e7eb;">
            <div class="search-input-wrap" style="margin:0;">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search messages..." style="border:1px solid #e5e7eb; border-radius:8px; padding:9px 9px 9px 36px; width:100%; font-size:13px; outline:none;">
            </div>
        </div>

        @foreach($allMessages as $msg)
        <a href="{{ route('messages.show', $msg) }}" style="display:block; padding:16px; border-bottom:1px solid #f3f4f6; text-decoration:none; background:{{ $msg->id === $message->id ? '#fef2f2' : 'white' }}; transition:background 0.15s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='{{ $msg->id === $message->id ? '#fef2f2' : 'white' }}'">
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
                <div style="width:36px; height:36px; background:#e5e7eb; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; color:#374151; flex-shrink:0;">
                    {{ strtoupper(substr($msg->sender_name ?? 'P', 0, 2)) }}
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-weight:500; font-size:14px; color:#1a1a2e; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ $msg->sender_name ?? 'Unknown' }}
                    </div>
                    <div style="font-size:11px; color:#9ca3af;">{{ $msg->created_at->diffForHumans() }}</div>
                </div>
            </div>
            <div style="font-weight:500; font-size:13px; color:#374151; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $msg->subject }}</div>
            <div style="font-size:12px; color:#9ca3af; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ Str::limit($msg->body, 60) }}</div>
        </a>
        @endforeach
    </div>

    <!-- Message Thread -->
    <div style="display:flex; flex-direction:column;">
        <!-- Thread Header -->
        <div style="padding:20px 24px; border-bottom:1px solid #e5e7eb;">
            <div style="font-size:18px; font-weight:700; color:#1a1a2e; margin-bottom:4px;">{{ $message->subject }}</div>
            <div style="display:flex; align-items:center; gap:12px; font-size:13px; color:#6b7280;">
                <span>From: <strong style="color:#374151;">{{ $message->sender_name }}</strong></span>
                <span>{{ $message->created_at->format('M d, Y h:i A') }}</span>
                @if($message->category)
                    <span style="background:#eff6ff; color:#3b82f6; padding:2px 8px; border-radius:4px;">{{ ucfirst($message->category) }}</span>
                @endif
                @if($message->status)
                    <span style="background:#fef9c3; color:#854d0e; padding:2px 8px; border-radius:4px;">{{ ucfirst($message->status) }}</span>
                @endif
            </div>
        </div>

        <!-- Messages -->
        <div style="flex:1; padding:24px; overflow-y:auto; display:flex; flex-direction:column; gap:16px;">
            <!-- Original Message -->
            <div style="display:flex; gap:12px;">
                <div style="width:36px; height:36px; background:#e5e7eb; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; color:#374151; flex-shrink:0;">
                    {{ strtoupper(substr($message->sender_name ?? 'P', 0, 2)) }}
                </div>
                <div style="flex:1;">
                    <div style="font-weight:600; font-size:13px; color:#374151; margin-bottom:4px;">{{ $message->sender_name }}</div>
                    <div style="background:#f3f4f6; padding:14px 16px; border-radius:10px; font-size:14px; color:#374151; line-height:1.6;">
                        {{ $message->body }}
                    </div>
                    <div style="font-size:11px; color:#9ca3af; margin-top:6px;">{{ $message->created_at->format('M d, Y h:i A') }}</div>
                </div>
            </div>

            <!-- Replies -->
            @foreach($message->replies as $reply)
            <div style="display:flex; gap:12px; {{ $reply->sender_type === 'admin' ? 'flex-direction:row-reverse;' : '' }}">
                <div style="width:36px; height:36px; background:{{ $reply->sender_type === 'admin' ? '#C8102E' : '#e5e7eb' }}; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; color:{{ $reply->sender_type === 'admin' ? 'white' : '#374151' }}; flex-shrink:0;">
                    {{ strtoupper(substr($reply->sender_name ?? 'A', 0, 2)) }}
                </div>
                <div style="flex:1; {{ $reply->sender_type === 'admin' ? 'text-align:right;' : '' }}">
                    <div style="font-weight:600; font-size:13px; color:#374151; margin-bottom:4px;">{{ $reply->sender_name }}</div>
                    <div style="background:{{ $reply->sender_type === 'admin' ? '#C8102E' : '#f3f4f6' }}; color:{{ $reply->sender_type === 'admin' ? 'white' : '#374151' }}; padding:14px 16px; border-radius:10px; font-size:14px; line-height:1.6; display:inline-block; text-align:left; max-width:80%;">
                        {{ $reply->body }}
                    </div>
                    <div style="font-size:11px; color:#9ca3af; margin-top:6px;">{{ $reply->created_at->format('M d, Y h:i A') }}</div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Reply Box -->
        <div style="padding:16px 24px; border-top:1px solid #e5e7eb;">
            <form method="POST" action="{{ route('messages.reply', $message) }}">
                @csrf
                <div style="display:flex; gap:12px; align-items:flex-end;">
                    <textarea name="body" placeholder="Type your reply..." style="flex:1; border:1px solid #e5e7eb; border-radius:8px; padding:12px; font-size:14px; font-family:inherit; resize:none; outline:none; min-height:80px;" required></textarea>
                    <button type="submit" class="btn btn-primary" style="align-self:flex-end;">
                        <i class="fas fa-paper-plane"></i> Send
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
