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
            Schema::create('freelancers', function (Blueprint $table) {
                $table->increments('freelancer_id');
                $table->unsignedInteger('user_id')->unique(); // Chave estrangeira para Users.user_id, ÚNICO
                $table->string('full_name');
                $table->string('area_of_expertise')->nullable(); // Pode ser nulo
                $table->text('biography')->nullable();
                $table->text('portfolio_links')->nullable();
                $table->string('resume_link')->nullable();
                $table->enum('profile_visibility', ['Public', 'Private'])->default('Private');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            });
            // Adicione as chaves estrangeiras APÓS a criação da tabela
            Schema::table('freelancers', function (Blueprint $table) {
                $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('freelancers');
        }
    };
    