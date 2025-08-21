<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard da Empresa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h1 class="text-2xl font-bold mb-4">Bem-vindo ao Dashboard da Empresa!</h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        Aqui você poderá publicar vagas, gerenciar projetos e encontrar os melhores freelancers para sua empresa na plataforma Trampix.
                    </p>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-lg">
                            <h3 class="font-semibold text-indigo-800 dark:text-indigo-200">Vagas Publicadas</h3>
                            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">0</p>
                        </div>
                        
                        <div class="bg-orange-50 dark:bg-orange-900/20 p-4 rounded-lg">
                            <h3 class="font-semibold text-orange-800 dark:text-orange-200">Propostas Recebidas</h3>
                            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">0</p>
                        </div>
                        
                        <div class="bg-teal-50 dark:bg-teal-900/20 p-4 rounded-lg">
                            <h3 class="font-semibold text-teal-800 dark:text-teal-200">Projetos Concluídos</h3>
                            <p class="text-2xl font-bold text-teal-600 dark:text-teal-400">0</p>
                        </div>
                    </div>
                    
                    <div class="mt-8">
                        <a href="#" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Publicar Nova Vaga
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>