<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\CalendarEvent;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{
    public function index()
    {
        return view('admin.leaves.index', ['requests' => LeaveRequest::with('user')->latest()->get()]);
    }

    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        abort_if($leaveRequest->status !== 'pending', 422, 'Pengajuan ini sudah ditinjau.');
        $data = $request->validate(['status' => ['required', 'in:approved,rejected'], 'admin_note' => ['nullable', 'string', 'max:500']]);
        $leaveRequest->update([...$data, 'reviewed_by' => $request->user()->id, 'reviewed_at' => now()]);
        CalendarEvent::where('user_id', $leaveRequest->user_id)->where('description', 'Pengajuan #'.$leaveRequest->id)->delete();
        if ($data['status'] === 'approved') {
            for ($date = $leaveRequest->start_date->copy(); $date->lte($leaveRequest->end_date); $date->addDay()) {
                CalendarEvent::create(['user_id' => $leaveRequest->user_id, 'title' => ucfirst($leaveRequest->type).' '.$leaveRequest->user->name, 'event_date' => $date, 'type' => 'leave', 'description' => 'Pengajuan #'.$leaveRequest->id]);
            }
        }
        AuditLog::record('leave_'.$data['status'], $leaveRequest, $data['status'] === 'approved' ? 'Admin menyetujui pengajuan izin/cuti.' : 'Admin menolak pengajuan izin/cuti.', ['status' => 'pending'], ['status' => $data['status'], 'admin_note' => $data['admin_note'] ?? null]);

        return back()->with('success', 'Status pengajuan diperbarui.');
    }
}
