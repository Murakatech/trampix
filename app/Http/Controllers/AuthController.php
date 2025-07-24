<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // Para hash de senha
use Illuminate\Validation\ValidationException; // Para tratar erros de validação
use Illuminate\Auth\AuthenticationException; // Para erros de autenticação

class AuthController extends Controller
{
    // Método para registrar um novo usuário (POST /api/register)
    public function register(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed', // 'confirmed' exige password_confirmation
                'user_type' => 'required|in:Freelancer,Empresa,Administrador',
                'status' => 'in:Active,Pending Approval,Blocked',
            ]);

            $user = User::create([
                'email' => $request->email,
                'password_hash' => Hash::make($request->password),
                'user_type' => $request->user_type,
                'status' => $request->status ?? 'Pending Approval',
            ]);

            // Gera um token de acesso pessoal para o usuário recém-registrado
            // 'auth_token' é o nome do token, pode ser qualquer string
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Usuário registrado com sucesso!',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201); // 201 Created

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao registrar usuário: ' . $e->getMessage()], 500);
        }
    }

    // Método para login de usuário (POST /api/login)
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            // Verifica se o usuário existe e se a senha está correta
            if (!$user || !Hash::check($request->password, $user->password_hash)) {
                throw new AuthenticationException('Credenciais inválidas.');
            }

            // Revoga tokens antigos para este usuário (opcional, para segurança)
            $user->tokens()->delete();

            // Gera um novo token de acesso pessoal
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login realizado com sucesso!',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422);
        } catch (AuthenticationException $e) {
            return response()->json(['message' => $e->getMessage()], 401); // 401 Unauthorized
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao realizar login: ' . $e->getMessage()], 500);
        }
    }

    // Método para logout de usuário (POST /api/logout)
    public function logout(Request $request)
    {
        // Deleta o token atual usado para a requisição
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso!']);
    }

    // Método para obter o usuário autenticado (GET /api/user - protegido)
    public function user(Request $request)
    {
        // Retorna o usuário autenticado
        return response()->json($request->user());
    }
}
