<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Department;

class AdminController extends Controller
{
    public function indexDashboard()
    {

        $tickets = Ticket::where('user_id', Auth::id())->get();
        // Logic for displaying admin dashboard
        return view('admin.dashboard',compact('tickets'));
    }

    public function userManagement()
    {
        $users = User::paginate(15);
        // Logic for displaying users list
        return view('admin.usermanagement',compact('users'));
    }



    public function showSettings()
    {
        // Logic for displaying settings
        return view('admin.settings');
    }

    public function createUser()
    {
        // Logic for displaying user creation form
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        // Logic for storing new user
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function indexDepartments()
    {

        // Logic for displaying departments list
        return view('admin.departmentlist');
    }

//REPORTS
    public function indexReports()
    {
        // Logic for displaying reports list
        return view('admin.systemreports');
    }

    // TICKETS

    public function openTickets()
    {
        $tickets = Ticket::where('status', 'open')->get();
        return view('admin.tickets', compact('tickets'));
    }

    public function pendingTickets()
    {
        $tickets = Ticket::where('status', 'pending')->get();
        return view('admin.tickets', compact('tickets'));
    }

    public function closedTickets()
    {
        $tickets = Ticket::where('status', 'closed')->get();
        return view('admin.tickets', compact('tickets'));
    }




}
