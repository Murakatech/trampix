<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';
    protected $primaryKey = 'company_id'; // Chave primária da tabela companies

    protected $fillable = [
        'user_id',
        'company_name',
        'trade_name',
        'sector',
        'description',
        'cnpj',
        'logo_url',
        'location',
    ];

    // Um Company pertence a um User (relacionamento 1:1)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Uma Company tem muitas JobVacancies (relacionamento 1:N)
    public function jobVacancies()
    {
        return $this->hasMany(JobVacancy::class, 'company_id', 'company_id');
    }
}
