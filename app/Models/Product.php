<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $fillable = [
        'user_id',
        'ml_id',
        'name',
        'description',
        'price',
        'stock_quantity',
        'category',
        'image',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mlCategory()
    {
        return $this->belongsTo(MlCategory::class, 'category', 'id_ml');
    }

    protected static function boot(){
        parent::boot();
        static::creating(function ($model) {
            $model->user_id = auth()->user()->id;
        });
    }

    protected static function booted(): void
    {
        static::addGlobalScope('user', function (Builder $query) {
            if (auth()->hasUser()) {
                $query->where('user_id', auth()->user()->id);
            }
        });
    }
}
