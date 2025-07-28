// public/js/company-management.js

document.addEventListener('DOMContentLoaded', () => {
    const API_BASE_URL = 'http://localhost/api'; // URL base da sua API Laravel

    const companyForm = document.getElementById('companyForm');
    const formTitle = document.getElementById('formTitle');
    const submitBtn = document.getElementById('submitBtn');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    const formMessage = document.getElementById('formMessage');
    const companiesTableBody = document.getElementById('companiesTableBody');
    const refreshCompaniesBtn = document.getElementById('refreshCompaniesBtn');
    const listMessage = document.getElementById('listMessage');

    // Navbar elements
    const userEmailDisplayNav = document.getElementById('userEmailDisplayNav');
    const logoutBtnNav = document.getElementById('logoutBtnNav');

    let editingCompanyId = null; // Armazena o ID da empresa que está sendo editada

    // --- Funções Auxiliares ---
    function displayMessage(elementId, message, type = 'info') {
        const messageDiv = document.getElementById(elementId);
        messageDiv.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
    }

    // Função para obter o token de autenticação
    function getAuthHeaders() {
        const token = localStorage.getItem('access_token');
        if (token) {
            return {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            };
        }
        return {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        };
    }

    // Função para verificar autenticação e carregar dados do usuário na navbar
    async function loadUserForNavbar() {
        const token = localStorage.getItem('access_token');
        if (!token) {
            window.location.href = 'login.html'; // Redireciona se não houver token
            return;
        }
        try {
            const response = await fetch(`${API_BASE_URL}/user`, {
                method: 'GET',
                headers: getAuthHeaders()
            });
            const user = await response.json();
            if (response.ok) {
                userEmailDisplayNav.textContent = user.email;
            } else {
                displayMessage('listMessage', 'Sessão expirada. Faça login novamente.', 'warning');
                localStorage.removeItem('access_token');
                window.location.href = 'login.html';
            }
        } catch (error) {
            console.error('Erro ao carregar dados do usuário para navbar:', error);
            displayMessage('listMessage', 'Erro de conexão com a API.', 'danger');
            localStorage.removeItem('access_token');
            window.location.href = 'login.html';
        }
    }

    // --- Funções CRUD para Empresas ---

    // Função para buscar e exibir todas as empresas
    async function fetchCompanies() {
        listMessage.innerHTML = '<div class="alert alert-info">Carregando empresas...</div>';
        try {
            const response = await fetch(`${API_BASE_URL}/companies`, {
                method: 'GET',
                headers: getAuthHeaders()
            });
            const companies = await response.json();

            companiesTableBody.innerHTML = ''; // Limpa a tabela

            if (response.ok) {
                if (companies.length > 0) {
                    companies.forEach(company => {
                        const row = companiesTableBody.insertRow();
                        row.innerHTML = `
                            <td>${company.company_id}</td>
                            <td>${company.user ? company.user.email : 'N/A'}</td>
                            <td>${company.company_name}</td>
                            <td>${company.cnpj}</td>
                            <td>${company.sector || 'N/A'}</td>
                            <td>${company.location || 'N/A'}</td>
                            <td>
                                <button class="btn btn-sm btn-warning me-2" data-id="${company.company_id}" data-user-id="${company.user_id}" data-action="edit">Editar</button>
                                <button class="btn btn-sm btn-danger" data-id="${company.company_id}" data-action="delete">Excluir</button>
                            </td>
                        `;
                    });
                    listMessage.innerHTML = '';
                } else {
                    listMessage.innerHTML = '<div class="alert alert-warning">Nenhuma empresa encontrada.</div>';
                }
            } else {
                listMessage.innerHTML = `<div class="alert alert-danger">Erro ao carregar empresas: ${companies.message || 'Erro desconhecido'}</div>`;
            }
        } catch (error) {
            console.error('Erro ao buscar empresas:', error);
            listMessage.innerHTML = '<div class="alert alert-danger">Erro de conexão com a API. Verifique se o backend está rodando.</div>';
        }
    }

    // Lógica para o formulário de Criar/Editar Empresa
    companyForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        formMessage.innerHTML = ''; // Limpa mensagens anteriores

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const companyName = document.getElementById('companyName').value;
        const tradeName = document.getElementById('tradeName').value;
        const cnpj = document.getElementById('cnpj').value;
        const sector = document.getElementById('sector').value;
        const description = document.getElementById('description').value;
        const logoUrl = document.getElementById('logoUrl').value;
        const location = document.getElementById('location').value;

        const headers = getAuthHeaders();

        if (editingCompanyId) {
            // --- Lógica de Atualização (PUT) ---
            const userId = document.getElementById('userId').value; // Pega o user_id da empresa que está sendo editada

            const updateData = {
                company_name: companyName,
                trade_name: tradeName,
                sector: sector,
                description: description,
                cnpj: cnpj,
                logo_url: logoUrl,
                location: location
            };

            // Se o email ou password forem alterados, inclua-os
            if (email) updateData.email = email;
            if (password) updateData.password = password;

            try {
                const response = await fetch(`${API_BASE_URL}/companies/${editingCompanyId}`, {
                    method: 'PUT',
                    headers: headers,
                    body: JSON.stringify(updateData)
                });

                const result = await response.json();

                if (response.ok) {
                    displayMessage('formMessage', result.message || 'Empresa atualizada com sucesso!', 'success');
                    resetForm();
                    fetchCompanies(); // Atualiza a lista
                } else {
                    let errorMessage = result.message || 'Erro desconhecido.';
                    if (result.errors) {
                        errorMessage += '<br><ul>';
                        for (const field in result.errors) {
                            result.errors[field].forEach(error => {
                                errorMessage += `<li>${error}</li>`;
                            });
                        }
                        errorMessage += '</ul>';
                    }
                    displayMessage('formMessage', `Erro ao atualizar empresa: ${errorMessage}`, 'danger');
                }
            } catch (error) {
                console.error('Erro ao atualizar empresa:', error);
                displayMessage('formMessage', 'Erro de conexão ao atualizar empresa.', 'danger');
            }

        } else {
            // --- Lógica de Criação (POST) ---
            // Primeiro, registra o usuário na tabela Users
            const registerResponse = await fetch(`${API_BASE_URL}/register`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password, password_confirmation: password, user_type: 'Empresa' })
            });
            const registerResult = await registerResponse.json();

            if (!registerResponse.ok) {
                let errorMessage = registerResult.message || 'Erro desconhecido no registro.';
                if (registerResult.errors) {
                    errorMessage += '<br><ul>';
                    for (const field in registerResult.errors) {
                        registerResult.errors[field].forEach(error => {
                            errorMessage += `<li>${error}</li>`;
                        });
                    }
                    errorMessage += '</ul>';
                }
                displayMessage('formMessage', `Erro ao registrar usuário da empresa: ${errorMessage}`, 'danger');
                return;
            }

            const newUserId = registerResult.user.user_id;
            displayMessage('formMessage', `Usuário registrado com ID: ${newUserId}. Criando perfil da empresa...`, 'info');

            // Agora, cria o perfil da empresa usando o user_id
            const createCompanyData = {
                user_id: newUserId,
                company_name: companyName,
                trade_name: tradeName,
                cnpj: cnpj,
                sector: sector,
                description: description,
                logo_url: logoUrl,
                location: location
            };

            try {
                const response = await fetch(`${API_BASE_URL}/companies`, {
                    method: 'POST',
                    headers: headers,
                    body: JSON.stringify(createCompanyData)
                });

                const result = await response.json();

                if (response.ok) {
                    displayMessage('formMessage', result.message || 'Empresa criada com sucesso!', 'success');
                    companyForm.reset();
                    fetchCompanies(); // Atualiza a lista
                } else {
                    let errorMessage = result.message || 'Erro desconhecido.';
                    if (result.errors) {
                        errorMessage += '<br><ul>';
                        for (const field in result.errors) {
                            result.errors[field].forEach(error => {
                                errorMessage += `<li>${error}</li>`;
                            });
                        }
                        errorMessage += '</ul>';
                    }
                    displayMessage('formMessage', `Erro ao criar empresa: ${errorMessage}`, 'danger');
                }
            } catch (error) {
                console.error('Erro ao criar empresa:', error);
                displayMessage('formMessage', 'Erro de conexão ao criar empresa.', 'danger');
            }
        }
    });

    // Lógica para preencher o formulário ao clicar em "Editar"
    companiesTableBody.addEventListener('click', async (e) => {
        if (e.target.dataset.action === 'edit') {
            const companyId = e.target.dataset.id;
            const userId = e.target.dataset.userId; // user_id da empresa

            // Buscar dados da empresa para preencher o formulário
            try {
                const response = await fetch(`${API_BASE_URL}/companies/${companyId}`, {
                    method: 'GET',
                    headers: getAuthHeaders()
                });
                const company = await response.json();

                if (response.ok) {
                    document.getElementById('companyId').value = company.company_id;
                    document.getElementById('userId').value = company.user_id; // Preenche o user_id
                    document.getElementById('email').value = company.user.email; // Email do usuário relacionado
                    document.getElementById('password').value = ''; // Senha não pode ser lida, então limpa
                    document.getElementById('companyName').value = company.company_name;
                    document.getElementById('tradeName').value = company.trade_name || '';
                    document.getElementById('cnpj').value = company.cnpj;
                    document.getElementById('sector').value = company.sector || '';
                    document.getElementById('description').value = company.description || '';
                    document.getElementById('logoUrl').value = company.logo_url || '';
                    document.getElementById('location').value = company.location || '';

                    formTitle.textContent = `Editar Empresa (ID: ${company.company_id})`;
                    submitBtn.textContent = 'Atualizar Empresa';
                    cancelEditBtn.style.display = 'block';
                    editingCompanyId = company.company_id;

                    // Desabilita campos de email/password em edição se não for para mudar o usuário
                    // Ou se for para mudar, trate como atualização de usuário separada
                    document.getElementById('email').required = false;
                    document.getElementById('password').required = false;
                    document.getElementById('email').placeholder = 'Deixe em branco para não alterar o email';
                    document.getElementById('password').placeholder = 'Deixe em branco para não alterar a senha';


                } else {
                    displayMessage('listMessage', `Erro ao carregar empresa para edição: ${company.message || 'Erro desconhecido'}`, 'danger');
                }
            } catch (error) {
                console.error('Erro ao buscar empresa para edição:', error);
                displayMessage('listMessage', 'Erro de conexão ao carregar empresa para edição.', 'danger');
            }
        } else if (e.target.dataset.action === 'delete') {
            // Lógica de Deleção
            const companyId = e.target.dataset.id;
            if (confirm(`Tem certeza que deseja deletar a empresa com ID ${companyId}?`)) {
                try {
                    const response = await fetch(`${API_BASE_URL}/companies/${companyId}`, {
                        method: 'DELETE',
                        headers: getAuthHeaders()
                    });

                    if (response.ok) { // Status 204 No Content
                        displayMessage('listMessage', `Empresa ID ${companyId} deletada com sucesso!`, 'success');
                        fetchCompanies(); // Atualiza a lista
                    } else {
                        const result = await response.json();
                        displayMessage('listMessage', `Erro ao deletar empresa: ${result.message || 'Erro desconhecido'}`, 'danger');
                    }
                } catch (error) {
                    console.error('Erro ao deletar empresa:', error);
                    displayMessage('listMessage', 'Erro de conexão ao deletar empresa.', 'danger');
                }
            }
        }
    });

    // Lógica para cancelar edição
    cancelEditBtn.addEventListener('click', () => {
        resetForm();
    });

    function resetForm() {
        companyForm.reset();
        formTitle.textContent = 'Criar Nova Empresa';
        submitBtn.textContent = 'Criar Empresa';
        cancelEditBtn.style.display = 'none';
        editingCompanyId = null;
        formMessage.innerHTML = '';
        document.getElementById('email').required = true;
        document.getElementById('password').required = true;
        document.getElementById('email').placeholder = '';
        document.getElementById('password').placeholder = '';
        document.getElementById('userId').value = ''; // Limpa o user_id oculto
    }

    // --- Inicialização ---
    loadUserForNavbar(); // Carrega dados do usuário logado na navbar
    fetchCompanies(); // Carrega a lista de empresas ao iniciar a página

    // Lógica de Logout
    logoutBtnNav.addEventListener('click', async () => {
        const token = localStorage.getItem('access_token');
        if (!token) {
            window.location.href = 'login.html';
            return;
        }
        try {
            await fetch(`${API_BASE_URL}/logout`, {
                method: 'POST',
                headers: getAuthHeaders()
            });
        } catch (error) {
            console.error('Erro de conexão no logout:', error);
        } finally {
            localStorage.removeItem('access_token');
            window.location.href = 'login.html';
        }
    });
});
