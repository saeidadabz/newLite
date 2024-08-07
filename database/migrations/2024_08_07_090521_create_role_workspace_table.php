<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('role_workspace', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles');
            $table->foreignId('workspace_id')->constrained('workspaces');

            $table->primary(['workspace_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_workspace');
    }
};
