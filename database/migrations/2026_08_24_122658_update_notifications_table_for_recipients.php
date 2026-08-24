<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('title')->after('type');
            $table->text('message')->after('title');
            $table->string('action_url')->nullable()->after('message');
            $table->string('action_label')->nullable()->after('action_url');
            $table->string('icon')->nullable()->after('action_label');
            $table->json('metadata')->nullable()->after('icon');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['notifiable_type', 'notifiable_id', 'data', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('notifiable_type')->after('type');
            $table->uuid('notifiable_id')->after('notifiable_type');
            $table->json('data')->after('notifiable_id');
            $table->timestamp('read_at')->nullable()->after('data');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn(['title', 'message', 'action_url', 'action_label', 'icon', 'metadata']);
        });
    }
};
