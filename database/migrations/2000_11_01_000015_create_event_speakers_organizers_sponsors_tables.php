<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $jsonType = (string) config('events.database.json_column_type', 'jsonb');

        Schema::create(config('events.database.tables.speakers', 'event_speakers'), function (Blueprint $table) use ($jsonType): void {
            $table->uuid('id')->primary();
            $table->nullableUuidMorphs('owner');
            $table->nullableMorphs('assignable');
            $table->nullableMorphs('person');
            $table->string('display_name')->nullable();
            $table->text('biography')->nullable();
            $table->string('role_key')->nullable();
            $table->unsignedInteger('order_column')->nullable();
            $table->string('visibility', 32)->default('public');
            $table->{$jsonType}('metadata')->nullable();
            $table->timestampsTz();

            $table->index(['assignable_type', 'assignable_id', 'order_column']);
        });

        Schema::create(config('events.database.tables.organizers', 'event_organizers'), function (Blueprint $table) use ($jsonType): void {
            $table->uuid('id')->primary();
            $table->nullableUuidMorphs('owner');
            $table->nullableMorphs('assignable');
            $table->nullableMorphs('reference');
            $table->string('display_name')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('website_url')->nullable();
            $table->unsignedInteger('order_column')->nullable();
            $table->{$jsonType}('metadata')->nullable();
            $table->timestampsTz();

            $table->index(['assignable_type', 'assignable_id', 'order_column']);
        });

        Schema::create(config('events.database.tables.sponsors', 'event_sponsors'), function (Blueprint $table) use ($jsonType): void {
            $table->uuid('id')->primary();
            $table->nullableUuidMorphs('owner');
            $table->nullableMorphs('assignable');
            $table->nullableMorphs('reference');
            $table->string('tier')->nullable();
            $table->string('display_name')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('website_url')->nullable();
            $table->unsignedInteger('order_column')->nullable();
            $table->{$jsonType}('metadata')->nullable();
            $table->timestampsTz();

            $table->index(['assignable_type', 'assignable_id', 'order_column']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('events.database.tables.speakers', 'event_speakers'));
        Schema::dropIfExists(config('events.database.tables.organizers', 'event_organizers'));
        Schema::dropIfExists(config('events.database.tables.sponsors', 'event_sponsors'));
    }
};
