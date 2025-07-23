<?php

namespace App\Http\Controllers;

use App\Models\Freelancer;
use App\Models\User; // Necessário para criar o usuário antes do freelancer
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Para hashing de senhas
use Illuminate\Validation\ValidationException; // Para tratar erros de validação

class FreelancerController extends Controller
{
    // Método para obter todos os freelancers (GET /api/freelancers)
    public function index()
    {
        // Carrega os freelancers e seus usuários relacionados
        $freelancers = Freelancer::with('user')->get();
        return response()->json($freelancers);
    }

    // Método para criar um novo freelancer (POST /api/freelancers)
    public function store(Request $request)
    {
        try {
            // Validação dos dados de entrada
            $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'full_name' => 'required|string|max:255',
                'area_of_expertise' => 'nullable|string|max:255',
                'biography' => 'nullable|string',
                'portfolio_links' => 'nullable|string',
                'resume_link' => 'nullable|string|max:255',
                'profile_visibility' => 'in:Public,Private',
            ]);

            // 1. Criar o usuário na tabela Users
            $user = User::create([
                'email' => $request->email,
                'password_hash' => Hash::make($request->password),
                'user_type' => 'Freelancer',
                'status' => $request->status ?? 'Pending Approval', // Pode ser 'Active' se quiser que já venha ativo
            ]);

            // 2. Criar o perfil de freelancer usando o user_id recém-criado
            $freelancer = Freelancer::create([
                'user_id' => $user->user_id, // Usa o ID do usuário criado
                'full_name' => $request->full_name,
                'area_of_expertise' => $request->area_of_expertise,
                'biography' => $request->biography,
                'portfolio_links' => $request->portfolio_links,
                'resume_link' => $request->resume_link,
                'profile_visibility' => $request->profile_visibility ?? 'Private',
            ]);

            return response()->json([
                'message' => 'Freelancer criado com sucesso!',
                'freelancer' => $freelancer->load('user') // Carrega o usuário para o retorno
            ], 201); // Retorna status 201 Created

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422); // Unprocessable Entity
        } catch (\Exception $e) {
            // Se o usuário foi criado mas o freelancer falhou, você pode querer reverter o usuário
            // Ex: $user->delete();
            return response()->json(['message' => 'Erro ao criar freelancer: ' . $e->getMessage()], 500);
        }
    }

    // Método para obter um freelancer específico (GET /api/freelancers/{id})
    public function show(string $id)
    {
        $freelancer = Freelancer::with('user')->where('freelancer_id', $id)->first();

        if (!$freelancer) {
            return response()->json(['message' => 'Freelancer não encontrado.'], 404);
        }

        return response()->json($freelancer);
    }

    // Método para atualizar um freelancer (PUT/PATCH /api/freelancers/{id})
    public function update(Request $request, string $id)
    {
        try {
            $freelancer = Freelancer::where('freelancer_id', $id)->first();

            if (!$freelancer) {
                return response()->json(['message' => 'Freelancer não encontrado.'], 404);
            }

            // Validação dos dados para atualização
            $request->validate([
                'email' => 'nullable|email|unique:users,email,' . $freelancer->user_id . ',user_id', // Email único, exceto para o próprio usuário
                'password' => 'nullable|min:8',
                'full_name' => 'nullable|string|max:255',
                'area_of_expertise' => 'nullable|string|max:255',
                'biography' => 'nullable|string',
                'portfolio_links' => 'nullable|string',
                'resume_link' => 'nullable|string|max:255',
                'profile_visibility' => 'nullable|in:Public,Private',
            ]);

            // Atualiza os dados do usuário se o email ou password forem fornecidos
            if ($request->has('email') || $request->has('password')) {
                $user = $freelancer->user; // Acessa o modelo User relacionado
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
            // Atualiza os dados do freelancer
            $freelancerData = $request->except(['email', 'password']); // Exclui campos de User
            $freelancer->update($freelancerData);

            return response()->json(['message' => 'Freelancer atualizado com sucesso!', 'freelancer' => $freelancer->load('user')]);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar freelancer: ' . $e->getMessage()], 500);
        }
    }

    // Método para deletar um freelancer (DELETE /api/freelancers/{id})
    public function destroy(string $id)
    {
        try {
            $freelancer = Freelancer::where('freelancer_id', $id)->first();

            if (!$freelancer) {
                return response()->json(['message' => 'Freelancer não encontrado.'], 404);
            }

            // Deleta o usuário associado (e o freelancer será deletado em cascata pelo DB)
            $freelancer->user->delete();

            return response()->json(null, 204); // Retorna 204 No Content para sucesso na deleção

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao deletar freelancer: ' . $e->getMessage()], 500);
        }
    }
}
