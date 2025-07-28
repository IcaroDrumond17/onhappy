<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use SoftDeletes;
    use HasFactory;

    // Criar ou Atualizar
    protected $fillable = [
        'requestor_name',
        'destination',
        'departure_date',
        'return_date',
        'status',
        'user_id',
    ];

    protected $hidden = [
        'updated_at',
    ];

    // Converter datas
    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
    ];

    // Relação com usuário
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
