<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $query = AuditLog::with('user')->latest('created_at');
        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }
        $logs = $query->paginate(30)->withQueryString();
        $actions = AuditLog::distinct()->orderBy('action')->pluck('action');

        return view('admin.audit-logs.index', compact('logs', 'actions'));
    }
}
