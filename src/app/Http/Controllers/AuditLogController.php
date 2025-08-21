<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = AuditLog::with('user');
        
        // Apply filters if provided
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->has('activity_type') && $request->activity_type) {
            $query->where('activity_type', $request->activity_type);
        }
        
        if ($request->has('entity') && $request->entity) {
            $query->where('entity', $request->entity);
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        $auditLogs = $query->latest()->paginate(20);
        
        // Get unique values for filter dropdowns
        $users = \App\Models\User::all();
        $activityTypes = AuditLog::select('activity_type')->distinct()->get();
        $entities = AuditLog::select('entity')->distinct()->get();
        
        return view('audit_logs.index', compact('auditLogs', 'users', 'activityTypes', 'entities'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AuditLog  $auditLog
     * @return \Illuminate\Http\Response
     */
    public function show(AuditLog $auditLog)
    {
        $auditLog->load('user');
        return view('audit_logs.show', compact('auditLog'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AuditLog  $auditLog
     * @return \Illuminate\Http\Response
     */
    public function destroy(AuditLog $auditLog)
    {
        $auditLog->delete();

        return redirect()->route('audit-logs.index')
            ->with('success', 'Audit log entry deleted successfully.');
    }

    /**
     * Remove all audit logs.
     *
     * @return \Illuminate\Http\Response
     */
    public function clear()
    {
        AuditLog::truncate();

        return redirect()->route('audit-logs.index')
            ->with('success', 'All audit logs cleared successfully.');
    }
}
