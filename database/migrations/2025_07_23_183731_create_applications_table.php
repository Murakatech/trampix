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
            Schema::create('applications', function (Blueprint $table) {
                $table->increments('application_id');
                $table->unsignedInteger('freelancer_id');
                $table->unsignedInteger('vacancy_id');
                $table->enum('application_status', ['Pending', 'Reviewed', 'Interview', 'Hired', 'Rejected'])->default('Pending');
                $table->timestamp('applied_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

                $table->unique(['freelancer_id', 'vacancy_id']); // Chave única composta
            });
            // Adicione as chaves estrangeiras APÓS a criação da tabela
            Schema::table('applications', function (Blueprint $table) {
                $table->foreign('freelancer_id')->references('freelancer_id')->on('freelancers')->onDelete('cascade');
                $table->foreign('vacancy_id')->references('vacancy_id')->on('job_vacancies')->onDelete('cascade');
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('applications');
        }
    };
    