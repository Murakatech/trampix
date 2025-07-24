<?php

    namespace App\Exceptions;

    use Illuminate\Auth\AuthenticationException; // Importa a exceção
    use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
    use Illuminate\Http\Request; // Importa Request
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
                // Se a exceção for de autenticação E a requisição for para a API
                if ($e instanceof AuthenticationException && $request->expectsJson()) {
                    return response()->json(['message' => 'Não autenticado.'], 401);
                }
            });
        }
    }
    