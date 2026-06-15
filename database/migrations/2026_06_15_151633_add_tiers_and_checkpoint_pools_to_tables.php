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
        Schema::table('events', function (Blueprint $table) {
            $table->json('point_tiers')->nullable()->after('total_event_point');
        });

        Schema::table('checkpoints', function (Blueprint $table) {
            $table->bigInteger('point_pool')->default(0)->after('point');
            $table->bigInteger('remaining_point_pool')->default(0)->after('point_pool');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('point_tiers');
        });

        Schema::table('checkpoints', function (Blueprint $table) {
            $table->dropColumn(['point_pool', 'remaining_point_pool']);
        });
    }
};
