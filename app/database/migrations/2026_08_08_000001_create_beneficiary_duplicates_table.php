<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiary_duplicates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('age')->nullable();
            $table->string('id_number')->nullable();
            $table->string('email')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('student_status')->nullable();
            $table->string('pwd_status')->nullable();
            $table->string('eligibility_status')->nullable();
            $table->boolean('is_eligible')->default(true);
            $table->string('tags')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiary_duplicates');
    }
};
