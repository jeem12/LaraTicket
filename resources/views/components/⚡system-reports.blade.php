<?php

use Livewire\Component;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

new class extends Component
{
    public function with(): array
    {
        return [
            // Aggregate metrics for cards
            'totalUsers' => User::count(),
            'totalDepartments' => Department::count(),
            
            // Calculate users per department with safety guard for PostgreSQL CAST
            'departmentDistribution' => Department::query()
                ->leftJoin('users', function ($join) {
                    $join->on('departments.id', '=', DB::raw('CAST(users.department AS INTEGER)'))
                         ->whereRaw("users.department ~ '^[0-9]+$'");
                })
                ->select('departments.name', DB::raw('count(users.id) as user_count'))
                ->groupBy('departments.id', 'departments.name')
                ->get(),
                
            // Recent activity
            'recentUsers' => User::latest()->limit(5)->get(),
        ];
    }
};
?>

<div>
    <flux:heading size="xl" class="mb-6">System Reports</flux:heading>

    {{-- Metrics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <flux:card>
            <flux:heading>Total Users</flux:heading>
            <flux:subheading size="xl">{{ $totalUsers }}</flux:subheading>
        </flux:card>
        
        <flux:card>
            <flux:heading>Total Departments</flux:heading>
            <flux:subheading size="xl">{{ $totalDepartments }}</flux:subheading>
        </flux:card>
    </div>

    {{-- Data Summary Table --}}
    <flux:card class="p-6">
        <flux:heading class="mb-4">Department Staffing</flux:heading>
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Department</flux:table.column>
                <flux:table.column>User Count</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @foreach($departmentDistribution as $dept)
                    <flux:table.row>
                        <flux:table.cell>{{ $dept->name }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="neutral">{{ $dept->user_count }}</flux:badge>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    </flux:card>
</div>