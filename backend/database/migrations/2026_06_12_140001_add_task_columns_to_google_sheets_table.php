<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('google_sheets', function (Blueprint $table) {
            $table->string('form_name')->nullable()->after('form_url');
            $table->string('file_url_column')->nullable()->after('address_column');
            $table->string('status_column')->nullable()->after('file_url_column');
            $table->string('due_date_column')->nullable()->after('status_column');
        });
    }

    public function down(): void
    {
        Schema::table('google_sheets', function (Blueprint $table) {
            $table->dropColumn(['form_name', 'file_url_column', 'status_column', 'due_date_column']);
        });
    }
};
