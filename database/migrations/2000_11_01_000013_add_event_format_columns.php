<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $eventsTable = (string) config('events.database.tables.events', 'events');
        $occurrencesTable = (string) config('events.database.tables.occurrences', 'event_occurrences');

        Schema::table($eventsTable, function (Blueprint $table) use ($eventsTable): void {
            if (! Schema::hasColumn($eventsTable, 'format')) {
                $table->string('format', 32)->default('physical')->index()->after('structure');
            }
        });

        Schema::table($occurrencesTable, function (Blueprint $table) use ($occurrencesTable): void {
            if (! Schema::hasColumn($occurrencesTable, 'visibility')) {
                $table->string('visibility', 32)->default('public')->index()->after('status');
            }

            if (! Schema::hasColumn($occurrencesTable, 'format')) {
                $table->string('format', 32)->nullable()->index()->after('participation_mode');
            }

            if (! Schema::hasColumn($occurrencesTable, 'postponed_at')) {
                $table->timestampTz('postponed_at')->nullable()->after('scheduled_at');
            }

            if (! Schema::hasColumn($occurrencesTable, 'delayed_at')) {
                $table->timestampTz('delayed_at')->nullable()->after('postponed_at');
            }

            if (! Schema::hasColumn($occurrencesTable, 'visible_at')) {
                $table->timestampTz('visible_at')->nullable()->after('delayed_at');
            }
        });
    }

    public function down(): void
    {
        $eventsTable = (string) config('events.database.tables.events', 'events');
        $occurrencesTable = (string) config('events.database.tables.occurrences', 'event_occurrences');

        Schema::table($eventsTable, function (Blueprint $table) use ($eventsTable): void {
            if (Schema::hasColumn($eventsTable, 'format')) {
                $table->dropColumn('format');
            }
        });

        Schema::table($occurrencesTable, function (Blueprint $table) use ($occurrencesTable): void {
            if (Schema::hasColumn($occurrencesTable, 'visibility')) {
                $table->dropColumn('visibility');
            }

            if (Schema::hasColumn($occurrencesTable, 'format')) {
                $table->dropColumn('format');
            }

            if (Schema::hasColumn($occurrencesTable, 'postponed_at')) {
                $table->dropColumn('postponed_at');
            }

            if (Schema::hasColumn($occurrencesTable, 'delayed_at')) {
                $table->dropColumn('delayed_at');
            }

            if (Schema::hasColumn($occurrencesTable, 'visible_at')) {
                $table->dropColumn('visible_at');
            }
        });
    }
};
