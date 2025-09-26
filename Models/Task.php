<?php

namespace Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Task extends Model
{
    protected $table = 'tasks';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_completed',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento: a task pertence a um usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
