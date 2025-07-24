<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Freelancer extends Model
{
    use HasFactory;

    protected $table = 'freelancers';
    protected $primaryKey = 'freelancer_id'; // Chave primária da tabela freelancers

    // Laravel espera chaves primárias auto-incrementáveis por padrão.
    // Se freelancer_id não fosse auto-incrementável, você usaria:
    // public $incrementing = false;

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'user_id',
        'full_name',
        'area_of_expertise',
        'biography',
        'portfolio_links',
        'resume_link',
        'profile_visibility',
    ];

    // Definição de relacionamentos
    // Um Freelancer pertence a um User (relacionamento 1:1)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Um Freelancer tem muitas Skills (relacionamento N:N via FreelancerSkill)
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'freelancer_skills', 'freelancer_id', 'skill_id');
    }
}
