<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Eager load assignedUser to prevent N+1 issues
        $tickets = Ticket::with('assignedUser')->latest()->get();

        return view('dashboard', ['tickets' => $tickets]);
    }
}
