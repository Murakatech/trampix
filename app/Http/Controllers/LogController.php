<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LogController extends Controller
{
    public function index()
    {
        $logs = Log::with('user')->get(); // Carrega o usuário relacionado
        return response()->json($logs);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'nullable|exists:users,user_id',
                'action' => 'required|string|max:255',
                'entity_type' => 'nullable|string|max:255',
                'entity_id' => 'nullable|integer',
                'details' => 'nullable|string',
            ]);

            $log = Log::create($request->all());
            return response()->json([
                'message' => 'Log registrado com sucesso!',
                'log' => $log
            ], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao registrar log: ' . $e->getMessage()], 500);
        }
    }

    public function show(string $id)
    {
        $log = Log::with('user')->where('log_id', $id)->first();

        if (!$log) {
            return response()->json(['message' => 'Log não encontrado.'], 404);
        }

        return response()->json($log);
    }

    public function update(Request $request, string $id)
    {
        // Logs geralmente não são atualizados, mas aqui está a implementação básica
        try {
            $log = Log::where('log_id', $id)->first();

            if (!$log) {
                return response()->json(['message' => 'Log não encontrado.'], 404);
            }

            $request->validate([
                'user_id' => 'nullable|exists:users,user_id',
                'action' => 'nullable|string|max:255',
                'entity_type' => 'nullable|string|max:255',
                'entity_id' => 'nullable|integer',
                'details' => 'nullable|string',
            ]);

            $log->update($request->all());

            return response()->json(['message' => 'Log atualizado com sucesso!', 'log' => $log]);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Erro de validação', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao atualizar log: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(string $id)
    {
        // Logs geralmente não são deletados, mas aqui está a implementação básica
        try {
            $log = Log::where('log_id', $id)->first();

            if (!$log) {
                return response()->json(['message' => 'Log não encontrado.'], 404);
            }

            $log->delete();

            return response()->json(null, 204);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao deletar log: ' . $e->getMessage()], 500);
        }
    }
}
