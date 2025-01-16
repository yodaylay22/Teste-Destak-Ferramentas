<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MlCategory extends Model
{
    use HasFactory;

    protected $fillable = ['id_ml', 'name'];

    protected $primaryKey = 'id_ml';

    public $incrementing = false;

    protected $keyType = 'string';

    public function products()
    {
        return $this->hasMany(Product::class, 'category', 'id_ml');
    }
}
