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
            Schema::create('users', function (Blueprint $table) {
                $table->increments('user_id'); // Chave Primária, INT, AUTO_INCREMENT
                $table->string('email')->unique(); // VARCHAR(255), ÚNICO, NOT NULL
                $table->string('password_hash'); // VARCHAR(255), NOT NULL
                $table->enum('user_type', ['Freelancer', 'Empresa', 'Administrador']); // ENUM, NOT NULL
                $table->enum('status', ['Active', 'Pending Approval', 'Blocked'])->default('Pending Approval'); // ENUM, DEFAULT
                $table->timestamp('created_at')->useCurrent(); // DATETIME, DEFAULT CURRENT_TIMESTAMP
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // DATETIME, ON UPDATE CURRENT_TIMESTAMP
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('users');
        }
    };
    