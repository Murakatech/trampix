<?php

namespace App\Http\Controllers;

use App\Models\JobVacancy;
use App\Models\Company; // Necessário para validação de company_id
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class JobVacancyController extends Controller
{
    // Método para obter todas as vagas ativas (GET /api/job_vacancies)
    public function index()
    {
        $vacancies = JobVacancy::with('company.user')->where('status', 'Active')->get();
        return response()->json($vacancies);
    }

    // Método para criar uma nova vaga (POST /api/job_vacancies)
    public function store(Request $request)
    {
        try {
            $request->validate([
                'company_id' => 'required|exists:companies,company_id',
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'requirements' => 'nullable|string',
                'category' => 'nullable|string|max:100',
                'contract_type' => 'required|in:PJ,CLT,Autônomo,Híbrido',
                'location_type' => 'required|in:Remoto,Presencial,Híbrido',
                'salary_range' => 'nullable|string|max:100',
                'status' => 'in:Active,Expired,Filled',
                'expires_at' => 'nullable|date',
            ]);

            $vacancy = JobVacancy::create($request->all());

            return response()->json([
                'message' => 'Vaga criada com sucesso!',
                'vacancy' => $vacancy
            ], 201);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao criar vaga: ' . $e->getMessage()], 500);
        }
    }

    // Método para obter uma vaga específica (GET /api/job_vacancies/{id})
    public function show(string $id)
    {
        $vacancy = JobVacancy::with('company.user')->where('vacancy_id', $id)->first();

        if (!$vacancy) {
            return response()->json(['message' => 'Vaga não encontrada.'], 404);
        }

        return response()->json($vacancy);
    }

    // Método para atualizar uma vaga (PUT/PATCH /api/job_vacancies/{id})
    public function update(Request $request, string $id)
    {
        try {
            $vacancy = JobVacancy::where('vacancy_id', $id)->first();

            if (!$vacancy) {
                return response()->json(['message' => 'Vaga não encontrada.'], 404);
            }

            $request->validate([
                'company_id' => 'nullable|exists:companies,company_id',
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'requirements' => 'nullable|string',
                'category' => 'nullable|string|max:100',
                'contract_type' => 'nullable|in:PJ,CLT,Autônomo,Híbrido',
                'location_type' => 'nullable|in:Remoto,Presencial,Híbrido',
                'salary_range' => 'nullable|string|max:100',
                'status' => 'nullable|in:Active,Expired,Filled',
                'expires_at' => 'nullable|date',
            ]);

            $vacancy->update($request->all());

            return response()->json(['message' => 'Vaga atualizada com sucesso!', 'vacancy' => $vacancy]);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar vaga: ' . $e->getMessage()], 500);
        }
    }

    // Método para deletar uma vaga (DELETE /api/job_vacancies/{id})
    public function destroy(string $id)
    {
        try {
            $vacancy = JobVacancy::where('vacancy_id', $id)->first();

            if (!$vacancy) {
                return response()->json(['message' => 'Vaga não encontrada.'], 404);
            }

            $vacancy->delete();

            return response()->json(null, 204);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao deletar vaga: ' . $e->getMessage()], 500);
        }
    }
}
