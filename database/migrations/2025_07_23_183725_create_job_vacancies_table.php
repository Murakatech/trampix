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
            Schema::create('job_vacancies', function (Blueprint $table) {
                $table->increments('vacancy_id');
                $table->unsignedInteger('company_id'); // Chave estrangeira para Companies.company_id
                $table->string('title');
                $table->text('description');
                $table->text('requirements')->nullable();
                $table->string('category')->nullable();
                $table->enum('contract_type', ['PJ', 'CLT', 'Autônomo', 'Híbrido']);
                $table->enum('location_type', ['Remoto', 'Presencial', 'Híbrido']);
                $table->string('salary_range')->nullable();
                $table->enum('status', ['Active', 'Expired', 'Filled'])->default('Active');
                $table->timestamp('posted_at')->useCurrent();
                $table->dateTime('expires_at')->nullable(); // DATETIME
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            });
            // Adicione as chaves estrangeiras APÓS a criação da tabela
            Schema::table('job_vacancies', function (Blueprint $table) {
                $table->foreign('company_id')->references('company_id')->on('companies')->onDelete('cascade');
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('job_vacancies');
        }
    };
    