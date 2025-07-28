<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException; // Importar esta classe
use Illuminate\Auth\AuthenticationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $rules = [
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'user_type' => 'required|in:Freelancer,Empresa,Administrador',
                'status' => 'in:Active,Pending Approval,Blocked',
            ];

            $messages = [
                'email.unique' => 'Este endereço de e-mail já está cadastrado. Por favor, use outro.',
                'email.required' => 'O campo e-mail é obrigatório.',
                'email.email' => 'O campo e-mail deve ser um endereço de e-mail válido.',
                'password.required' => 'O campo senha é obrigatório.',
                'password.min' => 'A senha deve ter no mínimo :min caracteres.',
                'password.confirmed' => 'A confirmação de senha não corresponde.',
                'user_type.required' => 'O tipo de usuário é obrigatório.',
                'user_type.in' => 'O tipo de usuário selecionado é inválido.',
            ];

            // O Laravel lança ValidationException automaticamente se a validação falhar.
            // Para APIs, ele já converte para 422 JSON por padrão.
            $request->validate($rules, $messages); // <-- A validação ocorre aqui

            // Se a validação passar, o código continua aqui
            $user = User::create([
                'email' => $request->email,
                'password_hash' => Hash::make($request->password),
                'user_type' => $request->user_type,
                'status' => $request->status ?? 'Pending Approval',
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Usuário registrado com sucesso!',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);

        } catch (ValidationException $e) { // <-- CAPTURA A EXCEÇÃO DE VALIDAÇÃO
            // O Laravel já retorna 422 por padrão para ValidationException em APIs.
            // Apenas retornamos a resposta para garantir que o fluxo pare aqui.
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422); // <-- GARANTA O STATUS 422 AQUI
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro interno do servidor: ' . $e->getMessage()], 500);
        }
    }

    // ... (método login e outros) ...

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ], [
                'email.required' => 'O campo e-mail é obrigatório para o login.',
                'email.email' => 'O campo e-mail deve ser um endereço de e-mail válido.',
                'password.required' => 'O campo senha é obrigatório para o login.',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password_hash)) {
                throw new AuthenticationException('Email ou senha inválidos.');
            }

            $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Login realizado com sucesso!',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422); // <-- GARANTA O STATUS 422 AQUI
        } catch (AuthenticationException $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao realizar login: ' . $e->getMessage()], 500);
        }
    }
}
