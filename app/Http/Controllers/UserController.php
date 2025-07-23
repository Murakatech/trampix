<?php

    namespace App\Http\Controllers;

    use App\Models\User; // Importa o modelo User
    use Illuminate\Http\Request; // Importa a classe Request do Laravel
    use Illuminate\Support\Facades\Hash; // Para hashing de senhas no Laravel
    use Illuminate\Validation\ValidationException; // Para tratar erros de validação

    class UserController extends Controller // Controladores Laravel estendem a classe base Controller
    {
        // Método para obter todos os usuários (GET /api/users)
        public function index()
        {
            // Usa o modelo User para buscar todos os usuários
            $users = User::all(); // Equivale a SELECT * FROM users

            return response()->json($users); // Retorna a resposta em JSON
        }

        // Método para criar um novo usuário (POST /api/users)
        public function store(Request $request)
        {
            // Validação dos dados de entrada usando o sistema de validação do Laravel
            try {
                $request->validate([
                    'email' => 'required|email|unique:users,email', // Email obrigatório, formato email, único na tabela users
                    'password' => 'required|min:8', // Senha obrigatória, mínimo 8 caracteres
                    'user_type' => 'required|in:Freelancer,Empresa,Administrador', // Tipo de usuário válido
                    'status' => 'in:Active,Pending Approval,Blocked', // Status opcional, valores válidos
                ]);
            } catch (ValidationException $e) {
                return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422); // Unprocessable Entity
            }

            try {
                // Cria um novo usuário usando o modelo Eloquent
                $user = User::create([
                    'email' => $request->email,
                    'password_hash' => Hash::make($request->password), // Hash da senha com o helper do Laravel
                    'user_type' => $request->user_type,
                    'status' => $request->status ?? 'Pending Approval', // Valor padrão se não fornecido
                ]);

                return response()->json([
                    'message' => 'Usuário criado com sucesso!',
                    'user_id' => $user->user_id, // Retorna o ID do usuário criado
                    'user' => $user // Retorna o objeto usuário criado
                ], 201); // Retorna status 201 Created

            } catch (\Exception $e) {
                return response()->json(['message' => 'Erro ao criar usuário: ' . $e->getMessage()], 500);
            }
        }

        // Método para obter um usuário específico (GET /api/users/{id})
        public function show(string $id) // O Laravel injeta o ID da rota
        {
            // Busca um usuário pelo user_id
            $user = User::where('user_id', $id)->first(); // Equivale a SELECT * FROM users WHERE user_id = ? LIMIT 1

            if (!$user) {
                return response()->json(['message' => 'Usuário não encontrado.'], 404);
            }

            return response()->json($user);
        }

        // Método para atualizar um usuário (PUT/PATCH /api/users/{id})
        public function update(Request $request, string $id)
        {
            try {
                $user = User::where('user_id', $id)->first();

                if (!$user) {
                    return response()->json(['message' => 'Usuário não encontrado.'], 404);
                }

                // Validação dos dados para atualização
                $request->validate([
                    'email' => 'nullable|email|unique:users,email,' . $id . ',user_id', // Email único, exceto para o próprio usuário
                    'password' => 'nullable|min:8',
                    'user_type' => 'nullable|in:Freelancer,Empresa,Administrador',
                    'status' => 'nullable|in:Active,Pending Approval,Blocked',
                ]);

                // Prepara os dados para atualização, incluindo hash da senha se ela for fornecida
                $dataToUpdate = $request->only(['email', 'user_type', 'status']);
                if ($request->has('password')) {
                    $dataToUpdate['password_hash'] = Hash::make($request->password);
                }

                $user->update($dataToUpdate); // Atualiza o usuário usando o modelo Eloquent

                return response()->json(['message' => 'Usuário atualizado com sucesso!', 'user' => $user]);

            } catch (ValidationException $e) {
                return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Erro ao atualizar usuário: ' . $e->getMessage()], 500);
            }
        }

        // Método para deletar um usuário (DELETE /api/users/{id})
        public function destroy(string $id)
        {
            try {
                $user = User::where('user_id', $id)->first();

                if (!$user) {
                    return response()->json(['message' => 'Usuário não encontrado.'], 404);
                }

                $user->delete(); // Deleta o usuário usando o modelo Eloquent

                return response()->json(null, 204); // Retorna 204 No Content para sucesso na deleção

            } catch (\Exception $e) {
                return response()->json(['message' => 'Erro ao deletar usuário: ' . $e->getMessage()], 500);
            }
        }
    }
    