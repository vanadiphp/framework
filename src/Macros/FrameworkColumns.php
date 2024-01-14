<?php

namespace Vanadi\Framework\Macros;

use Illuminate\Database\Schema\Blueprint;
use Vanadi\Framework\Contracts\State;

class FrameworkColumns
{
    public static function auditColumns(Blueprint $table): void
    {
        $table->foreignId('owner_id')->nullable()->constrained('users')->restrictOnDelete();
        $table->foreignId('modified_by')->nullable()->constrained('users')->nullOnDelete();

        $table->timestamp('submitted_at')->nullable();
        $table->timestamp('cancelled_at')->nullable();
        $table->timestamp('recalled_at')->nullable();
    }

    public static function statusColumns(Blueprint $table): void
    {
        $table->tinyInteger('state')->default(State::DRAFT);
        $table->boolean('is_active')->default(true);
    }

    public static function reversalColumns(Blueprint $table): void
    {
        $table->boolean('is_reversed')->default(false);
        $table->foreignId('reversed_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('reversed_at')->nullable();
    }

    public static function dropReversalColumns(Blueprint $table): void
    {
        $table->dropColumn('is_reversed');
        $table->dropColumn('reversed_by');
        $table->dropColumn('reversed_at');
    }

    public static function dropAuditColumns(Blueprint $table): void
    {
        $table->dropColumn('owner_id');
        $table->dropColumn('modified_by');

        $table->dropColumn('submitted_at');
        $table->dropColumn('cancelled_at');
        $table->dropColumn('recalled_at');
    }

    public static function dropStatusColumns(Blueprint $table): void
    {
        $table->dropColumn('doc_status');
        $table->dropColumn('is_active');
    }

    public static function teamColumn(Blueprint $table): void
    {
        $table->foreignId('team_id')->constrained()->restrictOnDelete();
    }

    public static function teamCodeColumn(Blueprint $table, bool $createCodeColumn = false, bool $createTeamColumn = false): void
    {

        if ($createTeamColumn) {
            $table->teamColumn();
        }
        if ($createCodeColumn) {
            $table->codeColumn();
        }
        $table->unique([
            'code',
            'team_id',
        ]);
    }

    public static function dropTeamColumn(Blueprint $table): void
    {
        $table->dropColumn('team_id');
    }
}
