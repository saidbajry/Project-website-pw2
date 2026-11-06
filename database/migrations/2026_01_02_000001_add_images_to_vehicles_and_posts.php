<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', fn (Blueprint $t) => $t->string('image')->nullable()->after('image_url'));
        Schema::table('posts', fn (Blueprint $t) => $t->string('image')->nullable()->after('body'));
    }

    public function down(): void
    {
        Schema::table('vehicles', fn (Blueprint $t) => $t->dropColumn('image'));
        Schema::table('posts', fn (Blueprint $t) => $t->dropColumn('image'));
    }
};
