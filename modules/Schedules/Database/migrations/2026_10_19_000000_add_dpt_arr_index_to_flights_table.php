<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const INDEX = 'flights_dpt_arr_index';

    public function up(): void
    {
        if ($this->indexExists()) {
            return;
        }

        Schema::table('flights', function (Blueprint $table) {
            $table->index(['dpt_airport_id', 'arr_airport_id'], self::INDEX);
        });
    }

    public function down(): void
    {
        if (!$this->indexExists()) {
            return;
        }

        Schema::table('flights', function (Blueprint $table) {
            $table->dropIndex(self::INDEX);
        });
    }

    private function indexExists(): bool
    {
        // MySQL / MariaDB
        return count(DB::select('SHOW INDEX FROM `flights` WHERE Key_name = ?', [self::INDEX])) > 0;
    }
};
