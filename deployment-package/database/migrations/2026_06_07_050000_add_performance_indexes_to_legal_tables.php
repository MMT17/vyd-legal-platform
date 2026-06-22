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
        $this->addIndexIfMissing('convenios', ['estado'], 'idx_convenios_estado_perf');
        $this->addIndexIfMissing('convenios', ['fecha'], 'idx_convenios_fecha_perf');
        $this->addIndexIfMissing('convenios', ['fecha_cierre'], 'idx_convenios_fecha_cierre_perf');
        $this->addIndexIfMissing('convenios', ['abogado_responsable'], 'idx_convenios_abogado_responsable_perf');
        $this->addIndexIfMissing('convenios', ['created_at'], 'idx_convenios_created_at_perf');

        $this->addIndexIfMissing('querellas', ['estado'], 'idx_querellas_estado_perf');
        $this->addIndexIfMissing('querellas', ['fecha'], 'idx_querellas_fecha_perf');
        $this->addIndexIfMissing('querellas', ['fecha_cierre'], 'idx_querellas_fecha_cierre_perf');
        $this->addIndexIfMissing('querellas', ['abogado_responsable'], 'idx_querellas_abogado_responsable_perf');
        $this->addIndexIfMissing('querellas', ['created_at'], 'idx_querellas_created_at_perf');

        $this->addIndexIfMissing('documentos', ['created_at'], 'idx_documentos_created_at_perf');
        $this->addIndexIfMissing('procesos', ['fecha'], 'idx_procesos_fecha_perf');
        $this->addIndexIfMissing('procesos', ['created_at'], 'idx_procesos_created_at_perf');
        $this->addIndexIfMissing('contactos', ['fecha'], 'idx_contactos_fecha_perf');
        $this->addIndexIfMissing('contactos', ['created_at'], 'idx_contactos_created_at_perf');

        $this->addIndexIfMissing('activity_log', ['log_name'], 'idx_activity_log_log_name_perf');
        $this->addIndexIfMissing('activity_log', ['event'], 'idx_activity_log_event_perf');
        $this->addIndexIfMissing('activity_log', ['subject_type'], 'idx_activity_log_subject_type_perf');
        $this->addIndexIfMissing('activity_log', ['subject_id'], 'idx_activity_log_subject_id_perf');
        $this->addIndexIfMissing('activity_log', ['created_at'], 'idx_activity_log_created_at_perf');
        $this->addIndexIfMissing('activity_log', ['subject_type', 'subject_id', 'created_at'], 'idx_activity_log_subject_created_perf');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ([
            ['convenios', 'idx_convenios_estado_perf'],
            ['convenios', 'idx_convenios_fecha_perf'],
            ['convenios', 'idx_convenios_fecha_cierre_perf'],
            ['convenios', 'idx_convenios_abogado_responsable_perf'],
            ['convenios', 'idx_convenios_created_at_perf'],
            ['querellas', 'idx_querellas_estado_perf'],
            ['querellas', 'idx_querellas_fecha_perf'],
            ['querellas', 'idx_querellas_fecha_cierre_perf'],
            ['querellas', 'idx_querellas_abogado_responsable_perf'],
            ['querellas', 'idx_querellas_created_at_perf'],
            ['documentos', 'idx_documentos_created_at_perf'],
            ['procesos', 'idx_procesos_fecha_perf'],
            ['procesos', 'idx_procesos_created_at_perf'],
            ['contactos', 'idx_contactos_fecha_perf'],
            ['contactos', 'idx_contactos_created_at_perf'],
            ['activity_log', 'idx_activity_log_log_name_perf'],
            ['activity_log', 'idx_activity_log_event_perf'],
            ['activity_log', 'idx_activity_log_subject_type_perf'],
            ['activity_log', 'idx_activity_log_subject_id_perf'],
            ['activity_log', 'idx_activity_log_created_at_perf'],
            ['activity_log', 'idx_activity_log_subject_created_perf'],
        ] as [$table, $index]) {
            $this->dropIndexIfExists($table, $index);
        }
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function addIndexIfMissing(string $table, array $columns, string $index): void
    {
        if (! Schema::hasTable($table) || $this->indexExists($table, $index)) {
            return;
        }

        foreach ($columns as $column) {
            if (! Schema::hasColumn($table, $column)) {
                return;
            }
        }

        Schema::table($table, fn (Blueprint $table): mixed => $table->index($columns, $index));
    }

    private function dropIndexIfExists(string $table, string $index): void
    {
        if (! Schema::hasTable($table) || ! $this->indexExists($table, $index)) {
            return;
        }

        Schema::table($table, fn (Blueprint $table): mixed => $table->dropIndex($index));
    }

    private function indexExists(string $table, string $index): bool
    {
        $table = DB::getTablePrefix() . $table;

        return match (DB::getDriverName()) {
            'mysql', 'mariadb' => filled(DB::select('SHOW INDEX FROM ' . $this->wrapTable($table) . ' WHERE Key_name = ?', [$index])),
            'sqlite' => collect(DB::select("PRAGMA index_list('{$table}')"))->contains(fn (object $row): bool => ($row->name ?? null) === $index),
            'pgsql' => filled(DB::select('select 1 from pg_indexes where tablename = ? and indexname = ? limit 1', [$table, $index])),
            'sqlsrv' => filled(DB::select('select 1 from sys.indexes where name = ?', [$index])),
            default => false,
        };
    }

    private function wrapTable(string $table): string
    {
        return '`' . str_replace('`', '``', $table) . '`';
    }
};
