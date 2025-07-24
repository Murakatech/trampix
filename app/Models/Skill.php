<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $table = 'skills';
    protected $primaryKey = 'skill_id';

    protected $fillable = [
        'skill_name',
    ];

    // Uma Skill pertence a muitos Freelancers (relacionamento N:N via FreelancerSkill)
    public function freelancers()
    {
        return $this->belongsToMany(Freelancer::class, 'freelancer_skills', 'skill_id', 'freelancer_id');
    }
}
