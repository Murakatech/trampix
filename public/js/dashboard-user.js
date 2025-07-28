// public/js/dashboard-user.js

document.addEventListener('DOMContentLoaded', () => {
    const API_BASE_URL = 'http://localhost/api'; // URL base da sua API Laravel

    const userEmailDisplay = document.getElementById('userEmailDisplay');
    const userNameDisplay = document.getElementById('userNameDisplay');
    const userTypeDisplay = document.getElementById('userTypeDisplay');
    const userInfoEmail = document.getElementById('userInfoEmail');
    const userInfoId = document.getElementById('userInfoId');
    const userInfoStatus = document.getElementById('userInfoStatus');
    const userInfoCreatedAt = document.getElementById('userInfoCreatedAt');
    const logoutBtn = document.getElementById('logoutBtn');
    const dashboardMessage = document.getElementById('dashboardMessage');

    const freelancerLink = document.getElementById('freelancerLink');
    const companyLink = document.getElementById('companyLink');
    const adminLink = document.getElementById('adminLink');

    // Função auxiliar para exibir mensagens
    function displayMessage(elementId, message, type = 'info') {
        const messageDiv = document.getElementById(elementId);
        messageDiv.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
    }

    // Função para verificar autenticação e carregar dados do usuário
    async function fetchUserData() {
        const token = localStorage.getItem('access_token');

        if (!token) {
            // Se não houver token, redireciona para a página de login
            window.location.href = 'login.html';
            return;
        }

        try {
            const response = await fetch(`${API_BASE_URL}/user`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json' // Solicita resposta JSON
                }
            });

            const user = await response.json();

            if (response.ok) {
                // Exibir informações do usuário
                userEmailDisplay.textContent = user.email;
                userNameDisplay.textContent = user.email; // Ou user.full_name se carregar perfil
                userTypeDisplay.textContent = `Você está logado como: ${user.user_type}`;
                userInfoEmail.textContent = user.email;
                userInfoId.textContent = user.user_id;
                userInfoStatus.textContent = user.status;
                userInfoCreatedAt.textContent = new Date(user.created_at).toLocaleDateString('pt-BR');

                // Exibir links específicos por tipo de usuário
                if (user.user_type === 'Freelancer') {
                    freelancerLink.style.display = 'block';
                } else if (user.user_type === 'Empresa') {
                    companyLink.style.display = 'block';
                } else if (user.user_type === 'Administrador') {
                    adminLink.style.display = 'block';
                }

            } else if (response.status === 401) {
                // Token inválido ou expirado
                displayMessage('dashboardMessage', 'Sessão expirada ou inválida. Faça login novamente.', 'warning');
                localStorage.removeItem('access_token'); // Limpa o token inválido
                window.location.href = 'login.html';
            } else {
                displayMessage('dashboardMessage', `Erro ao carregar dados do usuário: ${user.message || 'Erro desconhecido'}`, 'danger');
            }
        } catch (error) {
            console.error('Erro de conexão ao carregar dados do usuário:', error);
            displayMessage('dashboardMessage', 'Erro de conexão com a API. Verifique se o backend está rodando.', 'danger');
            localStorage.removeItem('access_token'); // Limpa o token em caso de erro de conexão
            window.location.href = 'login.html';
        }
    }

    // --- Lógica de Logout ---
    logoutBtn.addEventListener('click', async () => {
        const token = localStorage.getItem('access_token');
        if (!token) {
            window.location.href = 'login.html'; // Já deslogado
            return;
        }

        try {
            const response = await fetch(`${API_BASE_URL}/logout`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            // Mesmo que a API retorne um erro, queremos limpar o token local
            localStorage.removeItem('access_token');
            if (response.ok) {
                displayMessage('dashboardMessage', 'Logout realizado com sucesso!', 'success');
            } else {
                const result = await response.json();
                displayMessage('dashboardMessage', `Erro ao fazer logout: ${result.message || 'Erro desconhecido'}`, 'warning');
            }
            window.location.href = 'login.html'; // Redireciona para login
        } catch (error) {
            console.error('Erro de conexão no logout:', error);
            localStorage.removeItem('access_token');
            window.location.href = 'login.html';
        }
    });

    // Carrega os dados do usuário ao carregar a página do dashboard
    fetchUserData();
});
