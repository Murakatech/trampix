<?php

    namespace App\Console\Commands;

    use Illuminate\Console\Command;

    class InspirePortugueseQuote extends Command
    {
        /**
         * The name and signature of the console command.
         *
         * @var string
         */
        protected $signature = 'inspire:pt'; // Nome do seu novo comando Artisan

        /**
         * The console command description.
         *
         * @var string
         */
        protected $description = 'Exibe uma citação inspiradora em português.';

        /**
         * Execute the console command.
         */
        public function handle()
        {
            $quotes = [
                "A persistência realiza o impossível. - Provérbio Chinês",
                "O único lugar onde o sucesso vem antes do trabalho é no dicionário. - Vidal Sassoon",
                "Seja a mudança que você deseja ver no mundo. - Mahatma Gandhi",
                "A vida é 10% o que acontece com você e 90% como você reage a isso. - Charles R. Swindoll",
                "O sucesso é ir de fracasso em fracasso sem perder o entusiasmo. - Winston Churchill",
                "Grandes coisas são feitas por uma série de pequenas coisas reunidas. - Vincent Van Gogh",
                "Acredite que você pode e você estará no meio do caminho. - Theodore Roosevelt",
            ];

            $this->comment($quotes[array_rand($quotes)]); // Seleciona uma citação aleatória e exibe
        }
    }
    