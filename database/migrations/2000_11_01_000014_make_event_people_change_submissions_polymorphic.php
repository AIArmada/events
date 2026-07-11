<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $peopleTable = (string) config('events.database.tables.people', 'event_speakers');
        $changeNoticesTable = (string) config('events.database.tables.change_notices', 'event_change_notices');
        $submissionsTable = (string) config('events.database.tables.submissions', 'event_submissions');

        foreach ([$peopleTable, $changeNoticesTable, $submissionsTable] as $table) {
            Schema::table($table, function (Blueprint $table) use ($table): void {
                if (! Schema::hasColumn($table, 'assignable_type')) {
                    $table->string('assignable_type')->nullable()->after('owner_id');
                }

                if (! Schema::hasColumn($table, 'assignable_id')) {
                    $table->uuid('assignable_id')->nullable()->after('assignable_type');
                }

                if (Schema::hasColumn($table, 'event_id')) {
                    $dbTable = $table;
                    DB::statement("UPDATE \"{$dbTable}\" SET assignable_type = ?, assignable_id = event_id WHERE event_id IS NOT NULL", [\AIArmada\Events\Models\Event::class]);
                }
            });
        }
    }

    public function down(): void
    {
        $peopleTable = (string) config('events.database.tables.people', 'event_speakers');
        $changeNoticesTable = (string) config('events.database.tables.change_notices', 'event_change_notices');
        $submissionsTable = (string) config('events.database.tables.submissions', 'event_submissions');

        foreach ([$peopleTable, $changeNoticesTable, $submissionsTable] as $table) {
            Schema::table($table, function (Blueprint $table) use ($table): void {
                if (Schema::hasColumn($table, 'assignable_type')) {
                    $table->dropColumn(['assignable_type', 'assignable_id']);
                }
            });
        }
    }
};
