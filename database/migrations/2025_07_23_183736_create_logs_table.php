    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::create('logs', function (Blueprint $table) {
                $table->increments('log_id');
                $table->unsignedInteger('user_id')->nullable(); // Pode ser nulo se o usuário for deletado
                $table->string('action');
                $table->string('entity_type')->nullable();
                $table->unsignedInteger('entity_id')->nullable();
                $table->timestamp('timestamp')->useCurrent();
                $table->text('details')->nullable();
            });
            // Adicione a chave estrangeira APÓS a criação da tabela
            Schema::table('logs', function (Blueprint $table) {
                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('logs');
        }
    };
    