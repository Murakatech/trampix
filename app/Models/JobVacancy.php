<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobVacancy extends Model
{
    use HasFactory;

    protected $table = 'job_vacancies';
    protected $primaryKey = 'vacancy_id';

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'requirements',
        'category',
        'contract_type',
        'location_type',
        'salary_range',
        'status',
        'posted_at',
        'expires_at',
    ];

    // Uma JobVacancy pertence a uma Company (relacionamento N:1)
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }

    // Uma JobVacancy tem muitas Applications (relacionamento 1:N)
    public function applications()
    {
        return $this->hasMany(Application::class, 'vacancy_id', 'vacancy_id');
    }
}
