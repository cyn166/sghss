<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Log;

class LogController extends Controller
{
    public function index(Request $request)
    {

        $query = Log::with('user');

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }
        if ($request->has('table')) {
            $query->where('table_affected', $request->table);
        }

        $logs = $query->latest()->paginate(20);

        $logs->getCollection()->transform(function ($log) {
            $log->description = json_decode($log->description, true);
            if ($log->user) {
                $log->user->makeHidden(['cpf']);
                $log->user->makeHidden(['last_login_at']);
            }
            return $log;
        });

        return response()->json($logs, 200);
    }
}
