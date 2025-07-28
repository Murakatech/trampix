<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth; // Importa o facade Auth

class CompanyController extends Controller
{
    // Construtor para aplicar middleware de autenticação e autorização
    public function __construct()
    {
        $this->middleware('auth:sanctum');
        $this->middleware('can:update,company')->only(['update', 'destroy']);
        $this->middleware('can:create,App\Models\Company')->only('store');
    }

    public function index()
    {
        $companies = Company::with('user')->get();
        return response()->json($companies);
    }

    // Método para criar uma nova empresa (POST /api/companies)
    // O user_id virá do usuário autenticado
    public function store(Request $request)
    {
        $user = Auth::user(); // Pega o usuário autenticado
        if ($user->company) {
            return response()->json(['message' => 'Você já possui um perfil de empresa.'], 409);
        }
        if ($user->user_type !== 'Empresa') {
            return response()->json(['message' => 'Seu tipo de usuário não permite criar um perfil de empresa.'], 403);
        }

        try {
            $request->validate([
                'company_name' => 'required|string|max:255',
                'cnpj' => 'required|string|max:18|unique:companies,cnpj',
                'trade_name' => 'nullable|string|max:255',
                'sector' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'logo_url' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
            ]);

            $company = Company::create([
                'user_id' => $user->user_id, // Usa o ID do usuário autenticado
                'company_name' => $request->company_name,
                'trade_name' => $request->trade_name,
                'sector' => $request->sector,
                'description' => $request->description,
                'cnpj' => $request->cnpj,
                'logo_url' => $request->logo_url,
                'location' => $request->location,
            ]);

            return response()->json([
                'message' => 'Perfil de empresa criado com sucesso!',
                'company' => $company->load('user')
            ], 201);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao criar perfil de empresa: ' . $e->getMessage()], 500);
        }
    }

    // Método para obter uma empresa específica (GET /api/companies/{id})
    public function show(string $id)
    {
        $company = Company::with('user')->where('company_id', $id)->first();

        if (!$company) {
            return response()->json(['message' => 'Empresa não encontrada.'], 404);
        }

        return response()->json($company);
    }

    // Método para atualizar uma empresa (PUT/PATCH /api/companies/{id})
    public function update(Request $request, string $id)
    {
        try {
            $company = Company::where('company_id', $id)->first();

            if (!$company) {
                return response()->json(['message' => 'Empresa não encontrada.'], 404);
            }

            // Autorização: O usuário logado deve ser o proprietário do perfil ou um administrador
            // if (Auth::id() !== $company->user_id && Auth::user()->user_type !== 'Administrador') {
            //     return response()->json(['message' => 'Você não tem permissão para atualizar este perfil.'], 403);
            // }

            $request->validate([
                'company_name' => 'nullable|string|max:255',
                'cnpj' => 'nullable|string|max:18|unique:companies,cnpj,' . $id . ',company_id',
                'trade_name' => 'nullable|string|max:255',
                'sector' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'logo_url' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
            ]);

            $companyData = $request->all();
            // Se o status for atualizado, apenas um admin pode fazer isso
            if (isset($companyData['status']) && Auth::user()->user_type !== 'Administrador') {
                unset($companyData['status']); // Remove o campo se não for admin
            }

            $company->update($companyData);

            return response()->json(['message' => 'Empresa atualizada com sucesso!', 'company' => $company->load('user')]);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar empresa: ' . $e->getMessage()], 500);
        }
    }

    // Método para deletar uma empresa (DELETE /api/companies/{id})
    public function destroy(string $id)
    {
        try {
            $company = Company::where('company_id', $id)->first();

            if (!$company) {
                return response()->json(['message' => 'Empresa não encontrada.'], 404);
            }

            // Autorização: O usuário logado deve ser o proprietário do perfil ou um administrador
            // if (Auth::id() !== $company->user_id && Auth::user()->user_type !== 'Administrador') {
            //     return response()->json(['message' => 'Você não tem permissão para deletar este perfil.'], 403);
            // }

            $company->user->delete(); // Deleta o usuário associado (e a empresa em cascata)

            return response()->json(null, 204);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao deletar empresa: ' . $e->getMessage()], 500);
        }
    }
}
