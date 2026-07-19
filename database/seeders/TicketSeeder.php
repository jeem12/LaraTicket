<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        Ticket::create([
            'subject' => 'System Login Issue',
            'status' => 'Open',
            'description' => 'User is unable to log into the internal portal.',
            'user_id' => $user->id,
        ]);
    }
}
