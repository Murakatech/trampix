<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard do Freelancer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-4">Bem-vindo ao Dashboard do Freelancer!</h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        Aqui você poderá gerenciar seus projetos, visualizar propostas e acompanhar seu progresso como freelancer na plataforma Trampix.
                    </p>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <h3 class="font-semibold text-blue-800 dark:text-blue-200">Projetos Ativos</h3>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">0</p>
                        </div>
                        
                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <h3 class="font-semibold text-green-800 dark:text-green-200">Propostas Enviadas</h3>
                            <p class="text-2xl font-bold text-green-600 dark:text-green-400">0</p>
                        </div>
                        
                        <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg">
                            <h3 class="font-semibold text-purple-800 dark:text-purple-200">Avaliação</h3>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">⭐ N/A</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>