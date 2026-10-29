<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        return view('leaves.index', ['requests' => $request->user()->leaveRequests()->latest()->get()]);
    }

    public function create()
    {
        return view('leaves.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:izin,sakit,cuti'], 'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'], 'reason' => ['required', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:3072'],
        ]);
        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('leave-attachments', 'public');
        }
        $request->user()->leaveRequests()->create($data);

        return redirect()->route('leaves.index')->with('success', 'Pengajuan berhasil dikirim.');
    }
}
