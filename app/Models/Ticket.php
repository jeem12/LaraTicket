<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'subject',
        'description',
        'status',
        'priority',
        'category',
        'user_id',
        'assigned_to',
        'department_id',
        'last_replied_at',
        'resolved_at',
    ];

    protected static function booted()
    {
        static::creating(function ($ticket) {
            $year = date('Y');
            $latest = static::whereYear('created_at', $year)->max('id') ?? 0;
            $nextSequence = $latest + 1;
            
            // Generates an enterprise-grade tracking series code (e.g., TCK-2026-000001)
            $ticket->ticket_number = 'TCK-' . $year . '-' . str_pad($nextSequence, 6, '0', STR_PAD_LEFT);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(TicketMessage::class);
    }
}