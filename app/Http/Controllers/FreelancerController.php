<?php

namespace App\Http\Controllers;

use App\Models\Freelancer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth; // Importa o facade Auth para pegar o usuário logado

class FreelancerController extends Controller
{
    // Construtor para aplicar middleware de autenticação e autorização
    public function __construct()
    {
        // Protege todas as rotas deste controlador com autenticação Sanctum
        $this->middleware('auth:sanctum');
        // Ações que exigem que o usuário seja o proprietário do perfil OU um administrador
        $this->middleware('can:update,freelancer')->only(['update', 'destroy']);
        // Ação de store só pode ser feita por quem não tem perfil ainda
        $this->middleware('can:create,App\Models\Freelancer')->only('store');
    }

    public function index()
    {
        $freelancers = Freelancer::with('user')->get();
        return response()->json($freelancers);
    }

    // Método para criar um novo freelancer (POST /api/freelancers)
    // O user_id virá do usuário autenticado
    public function store(Request $request)
    {
        // Verifica se o usuário autenticado já tem um perfil de freelancer
        $user = Auth::user(); // Pega o usuário autenticado
        if ($user->freelancer) {
            return response()->json(['message' => 'Você já possui um perfil de freelancer.'], 409); // Conflict
        }
        if ($user->user_type !== 'Freelancer') {
            return response()->json(['message' => 'Seu tipo de usuário não permite criar um perfil de freelancer.'], 403); // Forbidden
        }

        try {
            $request->validate([
                'full_name' => 'required|string|max:255',
                'area_of_expertise' => 'nullable|string|max:255',
                'biography' => 'nullable|string',
                'portfolio_links' => 'nullable|string',
                'resume_link' => 'nullable|string|max:255',
                'profile_visibility' => 'in:Public,Private',
            ]);

            $freelancer = Freelancer::create([
                'user_id' => $user->user_id, // Usa o ID do usuário autenticado
                'full_name' => $request->full_name,
                'area_of_expertise' => $request->area_of_expertise,
                'biography' => $request->biography,
                'portfolio_links' => $request->portfolio_links,
                'resume_link' => $request->resume_link,
                'profile_visibility' => $request->profile_visibility ?? 'Private',
            ]);

            return response()->json([
                'message' => 'Perfil de freelancer criado com sucesso!',
                'freelancer' => $freelancer->load('user')
            ], 201);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao criar perfil de freelancer: ' . $e->getMessage()], 500);
        }
    }

    // Método para obter um freelancer específico (GET /api/freelancers/{id})
    // Adicionado Policy para autorização (veremos depois)
    public function show(string $id)
    {
        $freelancer = Freelancer::with('user')->where('freelancer_id', $id)->first();

        if (!$freelancer) {
            return response()->json(['message' => 'Freelancer não encontrado.'], 404);
        }

        // Exemplo de autorização: apenas o próprio freelancer ou admin pode ver detalhes privados
        // if (Auth::id() !== $freelancer->user_id && Auth::user()->user_type !== 'Administrador' && $freelancer->profile_visibility === 'Private') {
        //     return response()->json(['message' => 'Acesso negado. Perfil privado.'], 403);
        // }

        return response()->json($freelancer);
    }

    // Método para atualizar um freelancer (PUT/PATCH /api/freelancers/{id})
    // O user_id autenticado será usado para verificar permissão
    public function update(Request $request, string $id)
    {
        try {
            $freelancer = Freelancer::where('freelancer_id', $id)->first();

            if (!$freelancer) {
                return response()->json(['message' => 'Freelancer não encontrado.'], 404);
            }

            // Autorização: O usuário logado deve ser o proprietário do perfil ou um administrador
            // Isso será feito por uma Policy (veremos depois), mas a lógica básica é:
            // if (Auth::id() !== $freelancer->user_id && Auth::user()->user_type !== 'Administrador') {
            //     return response()->json(['message' => 'Você não tem permissão para atualizar este perfil.'], 403);
            // }

            $request->validate([
                'full_name' => 'nullable|string|max:255',
                'area_of_expertise' => 'nullable|string|max:255',
                'biography' => 'nullable|string',
                'portfolio_links' => 'nullable|string',
                'resume_link' => 'nullable|string|max:255',
                'profile_visibility' => 'nullable|in:Public,Private',
                // 'status' => 'nullable|in:Active,Pending Approval,Blocked', // Apenas admin pode mudar status
            ]);

            $freelancerData = $request->all();
            // Se o status for atualizado, apenas um admin pode fazer isso
            if (isset($freelancerData['status']) && Auth::user()->user_type !== 'Administrador') {
                unset($freelancerData['status']); // Remove o campo se não for admin
            }

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

            // Autorização: O usuário logado deve ser o proprietário do perfil ou um administrador
            // if (Auth::id() !== $freelancer->user_id && Auth::user()->user_type !== 'Administrador') {
            //     return response()->json(['message' => 'Você não tem permissão para deletar este perfil.'], 403);
            // }

            $freelancer->user->delete(); // Deleta o usuário associado (e o freelancer em cascata)

            return response()->json(null, 204);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao deletar freelancer: ' . $e->getMessage()], 500);
        }
    }
}
