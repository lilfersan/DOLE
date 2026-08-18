<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            if (!Schema::hasColumn('beneficiaries', 'id_number')) {
                $table->string('id_number')->nullable()->after('age');
            }
            if (!Schema::hasColumn('beneficiaries', 'email')) {
                $table->string('email')->nullable()->after('id_number');
            }
            if (!Schema::hasColumn('beneficiaries', 'birthdate')) {
                $table->date('birthdate')->nullable()->after('email');
            }
            if (!Schema::hasColumn('beneficiaries', 'eligibility_status')) {
                $table->string('eligibility_status')->nullable()->after('pwd_status');
            }
            if (!Schema::hasColumn('beneficiaries', 'is_eligible')) {
                $table->boolean('is_eligible')->default(true)->after('eligibility_status');
            }
            if (!Schema::hasColumn('beneficiaries', 'tags')) {
                $table->string('tags')->nullable()->after('is_eligible');
            }
        });
    }

    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropColumn(['id_number', 'email', 'birthdate', 'eligibility_status', 'is_eligible', 'tags']);
        });
    }
};
