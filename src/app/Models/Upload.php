<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Upload extends Model
{
    protected $fillable = [
        "user_id",
        'filename',
        'filepath',
        'status',
        'error_message',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
