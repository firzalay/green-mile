<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->bigInteger('total_event_point')->default(0)->after('max_points');
            $table->string('point_distribution_mode')->default('automatic')->after('total_event_point');
            $table->integer('fastest_participant_limit')->default(0)->after('point_distribution_mode');
            $table->decimal('bonus_percentage', 5, 2)->default(0.00)->after('fastest_participant_limit');
        });

        // Copy max_points to total_event_point for existing events
        DB::table('events')->update([
            'total_event_point' => DB::raw('max_points'),
        ]);

        Schema::table('checkpoints', function (Blueprint $table) {
            $table->bigInteger('point')->default(0)->after('points');
        });

        // Copy points to point for existing checkpoints
        DB::table('checkpoints')->update([
            'point' => DB::raw('points'),
        ]);

        Schema::table('checkpoint_scans', function (Blueprint $table) {
            $table->bigInteger('base_point')->default(0)->after('points_awarded');
            $table->bigInteger('bonus_point')->default(0)->after('base_point');
            $table->bigInteger('total_point')->default(0)->after('bonus_point');
        });

        // Copy points_awarded to the new columns for existing scans
        DB::table('checkpoint_scans')->update([
            'base_point' => DB::raw('points_awarded'),
            'total_point' => DB::raw('points_awarded'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkpoint_scans', function (Blueprint $table) {
            $table->dropColumn(['base_point', 'bonus_point', 'total_point']);
        });

        Schema::table('checkpoints', function (Blueprint $table) {
            $table->dropColumn('point');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'total_event_point',
                'point_distribution_mode',
                'fastest_participant_limit',
                'bonus_percentage',
            ]);
        });
    }
};
