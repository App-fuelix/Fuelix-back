<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Dashboard extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'total_consumption',
        'total_cost',
        'most_visited_station',
        'statistics'
    ];

    protected $casts = [
        'statistics' => 'array',
        'total_consumption' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn($model) => $model->id ??= (string) Str::uuid());
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function insights(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Insight::class);
    }

    public function calculateStatistics(): array
    {
        return [
            'total_consumption' => $this->total_consumption,
            'total_cost' => $this->total_cost,
            'most_visited_station' => $this->most_visited_station,
        ];
    }
}
