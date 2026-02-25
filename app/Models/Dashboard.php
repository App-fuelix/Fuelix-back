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

    public function refreshDashboard(): void
    {
        $user = $this->user;
        
        // Recalculer les statistiques
        $this->total_consumption = $user->transactions()->sum('quantity_liters') ?? 0;
        $this->total_cost = $user->transactions()
            ->selectRaw('SUM(quantity_liters * price_per_liter) as total')
            ->value('total') ?? 0;
        
        // Station la plus visitée
        $this->most_visited_station = $user->transactions()
            ->selectRaw('station_name, COUNT(*) as count')
            ->groupBy('station_name')
            ->orderByDesc('count')
            ->value('station_name');
        
        $this->save();
    }
}
