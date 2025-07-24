<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class FreelancerSkill extends Pivot // Extender Pivot para tabelas de junção
{
    use HasFactory;

    protected $table = 'freelancer_skills';

    // Se a tabela de junção não tiver um PK auto-incrementável 'id', defina isso:
    public $incrementing = false;

    // Defina as chaves primárias compostas
    protected $primaryKey = ['freelancer_id', 'skill_id'];

    protected $fillable = [
        'freelancer_id',
        'skill_id',
    ];

    // Relacionamentos inversos para acessar os modelos principais
    public function freelancer()
    {
        return $this->belongsTo(Freelancer::class, 'freelancer_id', 'freelancer_id');
    }

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id', 'skill_id');
    }
}
