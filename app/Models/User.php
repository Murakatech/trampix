<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;
    use Laravel\Sanctum\HasApiTokens;

    class User extends Authenticatable
    {
        use HasFactory, Notifiable, HasApiTokens;

        protected $table = 'users';
        protected $primaryKey = 'user_id';

        protected $fillable = [
            'email',
            'password_hash',
            'user_type',
            'status',
        ];

        protected $hidden = [
            'password_hash',
        ];

        protected $casts = [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];

        public function getAuthPassword()
        {
            return $this->password_hash;
        }

        public function freelancer()
        {
            return $this->hasOne(Freelancer::class, 'user_id', 'user_id');
        }

        public function company()
        {
            return $this->hasOne(Company::class, 'user_id', 'user_id');
        }
    }
    