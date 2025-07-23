
<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController; // Importa o UserController
use App\Http\Controllers\FreelancerController; // Importa o FreelancerController
use App\Http\Controllers\CompanyController; // Importa o CompanyController
use App\Http\Controllers\JobVacancyController; // Importa o JobVacancyController
use App\Http\Controllers\SkillController; // Importa o SkillController
use App\Http\Controllers\ApplicationController; // Importa o ApplicationController
use App\Http\Controllers\LogController; // Importa o LogController

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rota de teste padrão do Laravel (opcional, pode remover se quiser)
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas para todos os recursos da API (CRUD completo via apiResource)
// O Laravel mapeia automaticamente para os métodos index, store, show, update, destroy
Route::apiResource('users', UserController::class);
Route::apiResource('freelancers', FreelancerController::class);
Route::apiResource('companies', CompanyController::class);
Route::apiResource('job_vacancies', JobVacancyController::class);
Route::apiResource('skills', SkillController::class);
Route::apiResource('applications', ApplicationController::class);
Route::apiResource('logs', LogController::class);
