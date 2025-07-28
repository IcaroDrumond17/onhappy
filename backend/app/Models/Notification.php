<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'notification_message',
        'viewed',
    ];

    // Relacionamentos opcionais
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
