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
            $table->renameColumn('total_event_point', 'total_point_pool');
            $table->dropColumn(['point_tiers', 'point_pool', 'remaining_point_pool', 'fastest_participant_limit', 'bonus_percentage']);
        });

        Schema::table('checkpoints', function (Blueprint $table) {
            $table->boolean('is_custom_point')->default(false)->after('point');
            $table->dropColumn(['point_pool', 'remaining_point_pool']);
        });

        Schema::create('checkpoint_bonus_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checkpoint_id')->constrained()->cascadeOnDelete();
            $table->integer('rank_start');
            $table->integer('rank_end')->nullable();
            $table->decimal('bonus_percentage', 5, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkpoint_bonus_tiers');

        Schema::table('checkpoints', function (Blueprint $table) {
            $table->bigInteger('point_pool')->default(0)->after('point');
            $table->bigInteger('remaining_point_pool')->default(0)->after('point_pool');
            $table->dropColumn('is_custom_point');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->renameColumn('total_point_pool', 'total_event_point');
            $table->json('point_tiers')->nullable()->after('total_event_point');
            $table->bigInteger('point_pool')->default(0)->after('max_points');
            $table->bigInteger('remaining_point_pool')->default(0)->after('point_pool');
            $table->integer('fastest_participant_limit')->default(0)->after('point_distribution_mode');
            $table->decimal('bonus_percentage', 5, 2)->default(0.00)->after('fastest_participant_limit');
        });
    }
};
