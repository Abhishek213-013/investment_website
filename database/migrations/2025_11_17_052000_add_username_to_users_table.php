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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->after('name')->nullable();
            }
            
            if (!Schema::hasColumn('users', 'dob')) {
                $table->date('dob')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('users', 'education_qualification')) {
                $table->string('education_qualification')->nullable()->after('dob');
            }
            
            if (!Schema::hasColumn('users', 'institute')) {
                $table->string('institute')->nullable()->after('education_qualification');
            }
            
            if (!Schema::hasColumn('users', 'user_type')) {
                $table->enum('user_type', ['investor', 'admin', 'super_admin'])->default('investor')->after('institute');
            }
            
            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('user_type');
            }
            
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'dob', 'education_qualification', 'institute', 'user_type', 'is_active', 'last_login_at']);
        });
    }
};