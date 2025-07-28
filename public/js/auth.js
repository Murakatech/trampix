// public/js/auth.js

document.addEventListener('DOMContentLoaded', () => {
    const API_BASE_URL = 'http://localhost/api'; // URL base da sua API Laravel

    // Função auxiliar para exibir mensagens
    function displayMessage(elementId, message, type = 'info') {
        const messageDiv = document.getElementById(elementId);
        messageDiv.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
    }

    // --- Lógica para a Página de Registro (register.html) ---
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const password_confirmation = document.getElementById('password_confirmation').value;
            const user_type = document.getElementById('userType').value;

            displayMessage('registerMessage', 'Registrando...', 'info');

            try {
                const response = await fetch(`${API_BASE_URL}/register`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password, password_confirmation, user_type })
                });

                const result = await response.json();

                if (response.ok) { // Status 2xx (201 Created)
                    displayMessage('registerMessage', `Registro bem-sucedido! Bem-vindo, ${result.user.email}!`, 'success');
                    registerForm.reset();
                    // Armazenar o token e redirecionar para uma página logada
                    localStorage.setItem('access_token', result.access_token);
                    window.location.href = '/dashboard'; // <-- CORREÇÃO AQUI
                } else { // Status 4xx ou 5xx
                    let errorMessage = result.message || 'Erro desconhecido.';
                    if (result.errors) { // Erros de validação do Laravel (422)
                        errorMessage += '<br><ul>';
                        for (const field in result.errors) {
                            result.errors[field].forEach(error => {
                                errorMessage += `<li>${error}</li>`;
                            });
                        }
                        errorMessage += '</ul>';
                    }
                    displayMessage('registerMessage', `Erro no registro: ${errorMessage}`, 'danger');
                }
            } catch (error) {
                console.error('Erro de conexão no registro:', error);
                displayMessage('registerMessage', 'Erro de conexão com a API. Verifique se o backend está rodando.', 'danger');
            }
        });
    }

    // --- Lógica para a Página de Login (login.html) ---
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            displayMessage('loginMessage', 'Fazendo login...', 'info');

            try {
                const response = await fetch(`${API_BASE_URL}/login`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });

                const result = await await response.json(); // Duplo await aqui, pode ser um erro de digitação

                if (response.ok) { // Status 2xx (200 OK)
                    displayMessage('loginMessage', `Login bem-sucedido! Bem-vindo, ${result.user.email}!`, 'success');
                    loginForm.reset();
                    // Armazenar o token para uso em requisições futuras
                    localStorage.setItem('access_token', result.access_token);
                    window.location.href = '/dashboard'; //
                } else { // Status 4xx ou 5xx
                    let errorMessage = result.message || 'Credenciais inválidas.';
                    if (result.errors) { // Erros de validação do Laravel (422)
                        errorMessage += '<br><ul>';
                        for (const field in result.errors) {
                            result.errors[field].forEach(error => {
                                errorMessage += `<li>${error}</li>`;
                            });
                        }
                        errorMessage += '</ul>';
                    }
                    displayMessage('loginMessage', `Erro no login: ${errorMessage}`, 'danger');
                }
            } catch (error) {
                console.error('Erro de conexão no login:', error);
                displayMessage('loginMessage', 'Erro de conexão com a API. Verifique se o backend está rodando.', 'danger');
            }
        });
    }
});
