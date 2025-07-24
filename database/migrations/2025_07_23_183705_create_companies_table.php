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
            Schema::create('companies', function (Blueprint $table) {
                $table->increments('company_id');
                $table->unsignedInteger('user_id')->unique(); // Chave estrangeira para Users.user_id, ÚNICO
                $table->string('company_name');
                $table->string('trade_name')->nullable();
                $table->string('sector')->nullable();
                $table->text('description')->nullable();
                $table->string('cnpj', 18)->unique();
                $table->string('logo_url')->nullable();
                $table->string('location')->nullable();
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            });
            // Adicione as chaves estrangeiras APÓS a criação da tabela
            Schema::table('companies', function (Blueprint $table) {
                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('companies');
        }
    };
    