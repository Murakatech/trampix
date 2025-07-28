<?php

use Illuminate\Support\Facades\Route;


// Rota para a página inicial (Landing Page)
Route::get('/', function () {
    return file_get_contents(public_path('landing.html'));
});

// Rota para a página de registro
Route::get('/register', function () {
    return file_get_contents(public_path('register.html'));
});

// Rota para a página de login
Route::get('/login', function () {
    return file_get_contents(public_path('login.html'));
});

// Rota para a página do dashboard do usuário
Route::get('/dashboard', function () {
    return file_get_contents(public_path('dashboard.html'));
});

// Adicione aqui outras rotas web se precisar de páginas específicas
