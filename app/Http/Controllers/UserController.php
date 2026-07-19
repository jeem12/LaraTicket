<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {

        // $tickets = Ticket::with('assignedUser')->latest()->get();
        // dd(Auth::id());
        $userTickets = Ticket::where('user_id', Auth::id())->get();
        // Logic for displaying user dashboard
        return view('user.user-dashboard', compact('userTickets'));
    }

    public function showTicket($id)
    {
        $ticket = Ticket::with('assignedUser')->findOrFail($id);

        // Logic for displaying ticket details
        return view('user.ticket-details', compact('ticket'));
    }

    public function createTicket()
    {
        // Logic for displaying ticket creation form
        return view('user.create-ticket');
    }

    public function storeTicket(Request $request)
    {
        // Validate only the fields that come from the user
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Manually add the status
        Ticket::create([
            'user_id'     => Auth::id(),
            'subject'     => $validated['subject'],
            'description' => $validated['description'],
            'status'      => 'open', // Set the default here
        ]);

        return redirect()->route('user.dashboard')->with('success', 'Ticket created!');
    }
}
