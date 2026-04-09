<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function show(): View
    {
        $path = public_path('res/date_debut_maintenance.txt');
        $depuis = is_readable($path) ? trim((string) file_get_contents($path)) : '';

        return view('maintenance', ['depuis' => $depuis]);
    }
}
