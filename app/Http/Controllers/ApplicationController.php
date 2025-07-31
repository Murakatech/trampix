<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Freelancer; 
use App\Models\JobVacancy; 
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApplicationController extends Controller
{
    public function index()
    {

        $applications = Application::with(['freelancer.user', 'jobVacancy.company.user'])->get();
        return response()->json($applications);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'freelancer_id' => 'required|exists:freelancers,freelancer_id',
                'vacancy_id' => 'required|exists:job_vacancies,vacancy_id',
                'application_status' => 'in:Pending,Reviewed,Interview,Hired,Rejected',
            ]);


            $existingApplication = Application::where('freelancer_id', $request->freelancer_id)
                                                ->where('vacancy_id', $request->vacancy_id)
                                                ->first();
            if ($existingApplication) {
                return response()->json(['message' => 'Candidatura já existe para este freelancer e vaga.'], 409); // Conflict
            }

            $application = Application::create($request->all());
            return response()->json([
                'message' => 'Candidatura criada com sucesso!',
                'application' => $application
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao criar candidatura: ' . $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        $application = Application::with(['freelancer.user', 'jobVacancy.company.user'])->where('application_id', $id)->first();

        if (!$application) {
            return response()->json(['message' => 'Candidatura não encontrada.'], 404);
        }

        return response()->json($application);
    }

    public function update(Request $request, string $id)
    {
        try {
            $application = Application::where('application_id', $id)->first();

            if (!$application) {
                return response()->json(['message' => 'Candidatura não encontrada.'], 404);
            }

            $request->validate([
                'freelancer_id' => 'nullable|exists:freelancers,freelancer_id',
                'vacancy_id' => 'nullable|exists:job_vacancies,vacancy_id',
                'application_status' => 'nullable|in:Pending,Reviewed,Interview,Hired,Rejected',
            ]);


            if ($request->has('freelancer_id') || $request->has('vacancy_id')) {
                $checkFreelancerId = $request->freelancer_id ?? $application->freelancer_id;
                $checkVacancyId = $request->vacancy_id ?? $application->vacancy_id;

                $existingApplication = Application::where('freelancer_id', $checkFreelancerId)
                                                ->where('vacancy_id', $checkVacancyId)
                                                ->where('application_id', '!=', $id) 
                                                ->first();
                if ($existingApplication) {
                    return response()->json(['message' => 'Nova combinação de freelancer e vaga já existe em outra candidatura.'], 409);
                }
            }


            $application->update($request->all());

            return response()->json(['message' => 'Candidatura atualizada com sucesso!', 'application' => $application]);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar candidatura: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $application = Application::where('application_id', $id)->first();

            if (!$application) {
                return response()->json(['message' => 'Candidatura não encontrada.'], 404);
            }

            $application->delete();

            return response()->json(null, 204);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao deletar candidatura: ' . $e->getMessage()], 500);
        }
    }
}
