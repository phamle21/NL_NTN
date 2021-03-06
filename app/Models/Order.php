<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'order';

    protected $fillable = [
        'user_id',
        'promotion_id',
        'total',
        'address',
        'note',
        'status',
        'payment_method',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_detail');
    }

    public function promotion()
    {
        return $this->hasOne(Promotion::class, 'id', 'promotion_id');
    }
}
