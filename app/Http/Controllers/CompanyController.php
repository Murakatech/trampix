<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CompanyController extends Controller
{
    // Método para obter todas as empresas (GET /api/companies)
    public function index()
    {
        $companies = Company::with('user')->get();
        return response()->json($companies);
    }

    // Método para criar uma nova empresa (POST /api/companies)
    public function store(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'company_name' => 'required|string|max:255',
                'cnpj' => 'required|string|max:18|unique:companies,cnpj',
                'trade_name' => 'nullable|string|max:255',
                'sector' => 'nullable|string|max:100',
                'description' => 'nullable|string',
                'logo_url' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
            ]);

            // 1. Criar o usuário na tabela Users
            $user = User::create([
                'email' => $request->email,
                'password_hash' => Hash::make($request->password),
                'user_type' => 'Empresa',
                'status' => $request->status ?? 'Pending Approval',
            ]);

            // 2. Criar o perfil da empresa usando o user_id recém-criado
            $company = Company::create([
                'user_id' => $user->user_id,
                'company_name' => $request->company_name,
                'trade_name' => $request->trade_name,
                'sector' => $request->sector,
                'description' => $request->description,
                'cnpj' => $request->cnpj,
                'logo_url' => $request->logo_url,
                'location' => $request->location,
            ]);

            return response()->json([
                'message' => 'Empresa criada com sucesso!',
                'company' => $company->load('user')
            ], 201);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao criar empresa: ' . $e->getMessage()], 500);
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

            $request->validate([
                'email' => 'nullable|email|unique:users,email,' . $company->user_id . ',user_id',
                'password' => 'nullable|min:8',
                'company_name' => 'nullable|string|max:255',
                'cnpj' => 'nullable|string|max:18|unique:companies,cnpj,' . $id . ',company_id',
                // Adicione validação para outros campos
            ]);

            // Atualiza os dados do usuário se o email ou password forem fornecidos
            if ($request->has('email') || $request->has('password')) {
                $user = $company->user;
                if ($user) {
                    $userData = [];
                    if ($request->has('email')) {
                        $userData['email'] = $request->email;
                    }
                    if ($request->has('password')) {
                        $userData['password_hash'] = Hash::make($request->password);
                    }
                    $user->update($userData);
                }
            }
            // Atualiza os dados da empresa
            $companyData = $request->except(['email', 'password']);
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

            // Deleta o usuário associado (e a empresa será deletada em cascata pelo DB)
            $company->user->delete();

            return response()->json(null, 204);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao deletar empresa: ' . $e->getMessage()], 500);
        }
    }
}
