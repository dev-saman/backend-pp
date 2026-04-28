<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $messages = Message::with('patient')
            ->whereNull('parent_id')
            ->latest()
            ->paginate(30);
        return view('messages.index', compact('messages'));
    }

    public function create()
    {
        $patients = Patient::where('status', 'active')->orderBy('first_name')->get();
        return view('messages.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'subject'    => 'required|string|max:255',
            'body'       => 'required|string',
            'category'   => 'nullable|string|max:50',
        ]);
        $patient = Patient::findOrFail($validated['patient_id']);
        Message::create([
            'patient_id'  => $patient->id,
            'sender_name' => Auth::user()->name,
            'sender_type' => 'admin',
            'subject'     => $validated['subject'],
            'body'        => $validated['body'],
            'category'    => $validated['category'] ?? null,
            'status'      => 'sent',
            'is_read'     => true,
        ]);
        return redirect()->route('messages.index')->with('success', 'Message sent to ' . $patient->full_name . '.');
    }

    public function show(Message $message)
    {
        if (!$message->is_read && $message->sender_type === 'patient') {
            $message->update(['is_read' => true]);
        }
        $allMessages = Message::with('patient')->whereNull('parent_id')->latest()->take(30)->get();
        $message->load('replies');
        return view('messages.show', compact('message', 'allMessages'));
    }

    public function reply(Request $request, Message $message)
    {
        $request->validate(['body' => 'required|string']);
        Message::create([
            'patient_id'  => $message->patient_id,
            'parent_id'   => $message->id,
            'sender_name' => Auth::user()->name,
            'sender_type' => 'admin',
            'subject'     => 'Re: ' . $message->subject,
            'body'        => $request->body,
            'status'      => 'sent',
            'is_read'     => true,
        ]);
        return redirect()->route('messages.show', $message)->with('success', 'Reply sent.');
    }
}
