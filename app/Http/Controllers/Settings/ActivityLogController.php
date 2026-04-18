<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = Activity::with(['causer', 'subject'])
            ->when($request->search, function ($query, $search) {
                $query->where('description', 'like', "%{$search}%")
                      ->orWhere('log_name', 'like', "%{$search}%");
            })
            ->when($request->log_name, function ($query, $logName) {
                $query->where('log_name', $logName);
            })
            ->when($request->causer_id, function ($query, $causerId) {
                $query->where('causer_id', $causerId);
            })
            ->when($request->date_from, function ($query, $date) {
                $query->whereDate('created_at', '>=', $date);
            })
            ->when($request->date_to, function ($query, $date) {
                $query->whereDate('created_at', '<=', $date);
            })
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $logNames = Activity::distinct()->pluck('log_name')->filter()->sort();
        $users = \App\Models\User::orderBy('name')->get(['id', 'name']);

        return view('settings.activity-log', compact('logs', 'logNames', 'users'));
    }
}