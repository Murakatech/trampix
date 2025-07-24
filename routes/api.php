
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FreelancerController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobVacancyController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\AuthController; // <-- Importa o AuthController


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Grupo de Rotas Protegidas por Autenticação Sanctum
Route::middleware('auth:sanctum')->group(function () {
    // Rota para obter o usuário autenticado (já existe no Laravel, mas aqui é nosso)
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rotas para todos os recursos da API (CRUD completo via apiResource)
    // Agora, estas rotas exigirão um token de autenticação válido
    Route::apiResource('users', UserController::class); // Geralmente, 'users' seria acessível apenas por admin
    Route::apiResource('freelancers', FreelancerController::class);
    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('job_vacancies', JobVacancyController::class);
    Route::apiResource('skills', SkillController::class);
    Route::apiResource('applications', ApplicationController::class);
    Route::apiResource('logs', LogController::class); // Logs podem ter regras de acesso mais específicas
});
//*LEMBRAR DE FAZER****************
// Rotas públicas (se houver alguma que não precise de autenticação)
// Ex: Route::get('/public-vacancies', [JobVacancyController::class, 'index']); // Se quiser listar vagas sem login
