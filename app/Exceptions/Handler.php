<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException; // <-- Importar esta classe
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Adiciona um callback para renderizar exceções
        $this->renderable(function (Throwable $e, Request $request) {
            // Tratamento para AuthenticationException (retorna 401 JSON para API)
            if ($e instanceof AuthenticationException && $request->expectsJson()) {
                return response()->json(['message' => 'Não autenticado.'], 401);
            }

            // Tratamento para ValidationException (retorna 422 JSON para API)
            // O Laravel já faz isso por padrão, mas ter explícito pode ajudar a depurar.
            // A verificação mais forte 'isJson() || wantsJson() || is('api/*')' garante que a requisição é de API.
            if ($e instanceof ValidationException) {
                if ($request->isJson() || $request->wantsJson() || $request->is('api/*')) {
                    return response()->json([
                        'message' => 'Erro de validação',
                        'errors' => $e->errors()
                    ], 422);
                }
            }
            // Se não for uma exceção que queremos tratar como JSON, o Laravel lida com ela por padrão.
        });
    }
}
