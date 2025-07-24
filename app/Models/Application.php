<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $table = 'applications';
    protected $primaryKey = 'application_id';

    protected $fillable = [
        'freelancer_id',
        'vacancy_id',
        'application_status',
        'applied_at',
    ];

    // Uma Application pertence a um Freelancer (relacionamento N:1)
    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class, 'freelancer_id', 'freelancer_id');
    }

    // Uma Application pertence a uma JobVacancy (relacionamento N:1)
    public function jobVacancy()
    {
        return $this->belongsTo(JobVacancy::class, 'vacancy_id', 'vacancy_id');
    }
}
