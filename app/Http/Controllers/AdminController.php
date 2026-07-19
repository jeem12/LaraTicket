<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
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

    public function indexReports()
    {
        // Logic for displaying reports list
        return view('admin.reports.index');
    }

    public function showSettings()
    {
        // Logic for displaying settings
        return view('admin.settings');
    }


}
