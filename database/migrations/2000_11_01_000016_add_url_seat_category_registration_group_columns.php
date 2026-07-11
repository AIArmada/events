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
        $registrationsTable = (string) config('events.database.tables.registrations', 'event_registrations');
        $jsonType = (string) config('events.database.json_column_type', 'jsonb');

        // Phase 7: Drop organizer columns from events
        Schema::table($eventsTable, function (Blueprint $table) use ($eventsTable): void {
            if (Schema::hasColumn($eventsTable, 'organizer_type')) {
                $table->dropColumn(['organizer_type', 'organizer_id']);
            }
        });

        // Phase 11: URL columns
        Schema::table($eventsTable, function (Blueprint $table) use ($eventsTable): void {
            if (! Schema::hasColumn($eventsTable, 'website_url')) {
                $table->string('website_url')->nullable()->after('description');
            }
        });

        Schema::table($occurrencesTable, function (Blueprint $table) use ($occurrencesTable): void {
            if (! Schema::hasColumn($occurrencesTable, 'website_url')) {
                $table->string('website_url')->nullable()->after('name');
            }

            if (! Schema::hasColumn($occurrencesTable, 'livestream_url')) {
                $table->string('livestream_url')->nullable()->after('website_url');
            }

            if (! Schema::hasColumn($occurrencesTable, 'recording_url')) {
                $table->string('recording_url')->nullable()->after('livestream_url');
            }
        });

        // Phase 13: EventSeatCategory
        Schema::create(config('events.database.tables.seat_categories', 'event_seat_categories'), function (Blueprint $table) use ($jsonType): void {
            $table->uuid('id')->primary();
            $table->nullableUuidMorphs('owner');
            $table->nullableMorphs('assignable');
            $table->string('name');
            $table->unsignedInteger('capacity')->nullable();
            $table->uuid('product_id')->nullable();
            $table->uuid('variant_id')->nullable();
            $table->unsignedInteger('order_column')->nullable();
            $table->{$jsonType}('metadata')->nullable();
            $table->timestampsTz();

            $table->index(['assignable_type', 'assignable_id', 'order_column']);
        });

        // Phase 14: Add registration_group_id to registrations
        if (! Schema::hasColumn($registrationsTable, 'registration_group_id')) {
            Schema::table($registrationsTable, function (Blueprint $table) use ($registrationsTable): void {
                $table->uuid('registration_group_id')->nullable()->index()->after('occurrence_id');
            });
        }

        if (! Schema::hasColumn($registrationsTable, 'seat_category_id')) {
            Schema::table($registrationsTable, function (Blueprint $table) use ($registrationsTable): void {
                $table->uuid('seat_category_id')->nullable()->index()->after('registration_group_id');
            });
        }
    }

    public function down(): void
    {
        $eventsTable = (string) config('events.database.tables.events', 'events');
        $occurrencesTable = (string) config('events.database.tables.occurrences', 'event_occurrences');
        $registrationsTable = (string) config('events.database.tables.registrations', 'event_registrations');

        Schema::table($eventsTable, function (Blueprint $table) use ($eventsTable): void {
            if (Schema::hasColumn($eventsTable, 'organizer_type')) {
                $table->string('organizer_type')->nullable();
                $table->uuid('organizer_id')->nullable();
            }
            if (Schema::hasColumn($eventsTable, 'website_url')) {
                $table->dropColumn('website_url');
            }
        });

        Schema::table($occurrencesTable, function (Blueprint $table) use ($occurrencesTable): void {
            foreach (['website_url', 'livestream_url', 'recording_url'] as $col) {
                if (Schema::hasColumn($occurrencesTable, $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::dropIfExists(config('events.database.tables.seat_categories', 'event_seat_categories'));

        Schema::table(config('events.database.tables.registrations', 'event_registrations'), function (Blueprint $table): void {
            if (Schema::hasColumn($table->getTable(), 'registration_group_id')) {
                $table->dropColumn(['registration_group_id', 'seat_category_id']);
            }
        });
    }
};
