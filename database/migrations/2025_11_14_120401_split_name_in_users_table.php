<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add the new columns first
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
        });

        // Copy data from `name` to `first_name` and `last_name`
        DB::table('users')->get()->each(function ($user) {
            $names = explode(' ', $user->name, 2);
            DB::table('users')->where('id', $user->id)->update([
                'first_name' => $names[0],
                'last_name' => $names[1] ?? null
            ]);
        });

        // Drop the old `name` column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the old `name` column
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->dropColumn(['first_name', 'last_name']);
        });

        // Optionally, merge first_name and last_name back into name
        DB::table('users')->get()->each(function ($user) {
            $fullName = trim($user->first_name . ' ' . $user->last_name);
            DB::table('users')->where('id', $user->id)->update(['name' => $fullName]);
        });
    }
};
