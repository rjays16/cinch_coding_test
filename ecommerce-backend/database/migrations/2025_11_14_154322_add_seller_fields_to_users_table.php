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
            $table->dropColumn('name');
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');       
            $table->enum('role', ['buyer', 'seller'])->default('buyer')->after('last_name');         
            $table->string('store_name')->nullable()->unique()->after('role');
            $table->string('phone')->nullable()->after('email');
            $table->boolean('terms_accepted')->default(false)->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->dropColumn([
                'first_name',
                'last_name',
                'role',
                'store_name',
                'phone',
                'terms_accepted',
            ]);
        });
    }
};