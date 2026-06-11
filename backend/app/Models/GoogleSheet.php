<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleSheet extends Model
{
    protected $fillable = [
        'user_id',
        'spreadsheet_id',
        'form_url',
        'name_column',
        'email_column',
        'phone_column',
        'address_column',
        'last_synced_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
