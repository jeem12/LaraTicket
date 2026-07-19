<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // Important for UUIDs
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{


    protected $fillable = ['subject', 'description', 'status', 'user_id'];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}