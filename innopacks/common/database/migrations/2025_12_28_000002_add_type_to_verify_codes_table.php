<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('verify_codes', function (Blueprint $table) {
            if (! Schema::hasColumn('verify_codes', 'type')) {
                $table->string('type', 20)->default('register')->after('code')->comment('Verification Type: register, login, reset');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('verify_codes', function (Blueprint $table) {
            if (Schema::hasColumn('verify_codes', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
