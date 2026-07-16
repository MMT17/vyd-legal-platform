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
        $this->addChilquintaColumns('convenios');

        Schema::table('querellas', function (Blueprint $table) {
            if (! Schema::hasColumn('querellas', 'caso')) {
                $table->string('caso')->nullable()->index()->after('numero');
            }

            if (! Schema::hasColumn('querellas', 'ruc')) {
                $table->string('ruc')->nullable()->after('caso');
            }

            if (! Schema::hasColumn('querellas', 'ruc_dv')) {
                $table->string('ruc_dv', 20)->nullable()->after('ruc');
            }

            if (! Schema::hasColumn('querellas', 'rit')) {
                $table->string('rit')->nullable()->after('ruc_dv');
            }

            if (! Schema::hasColumn('querellas', 'juzgado')) {
                $table->string('juzgado')->nullable()->after('rit');
            }

            if (! Schema::hasColumn('querellas', 'fecha_presentacion')) {
                $table->dateTime('fecha_presentacion')->nullable()->after('juzgado');
            }
        });

        $this->addChilquintaColumns('querellas');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropChilquintaColumns('convenios');

        Schema::table('querellas', function (Blueprint $table) {
            foreach ([
                'caso',
                'ruc',
                'ruc_dv',
                'rit',
                'juzgado',
                'fecha_presentacion',
            ] as $column) {
                if (Schema::hasColumn('querellas', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        $this->dropChilquintaColumns('querellas');
    }

    private function addChilquintaColumns(string $tableName): void
    {
        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            foreach ([
                'energia_ventana',
                'energia_fv',
                'energia_total',
                'monto_ventana',
                'monto_fv',
                'monto_total',
            ] as $column) {
                if (! Schema::hasColumn($tableName, $column)) {
                    $table->decimal($column, 18, 2)->nullable();
                }
            }

            foreach ([
                'meses_ventana',
                'meses_fv',
                'meses_total',
            ] as $column) {
                if (! Schema::hasColumn($tableName, $column)) {
                    $table->integer($column)->nullable();
                }
            }

            if (! Schema::hasColumn($tableName, 'tipo_cnr')) {
                $table->string('tipo_cnr')->nullable();
            }

            if (! Schema::hasColumn($tableName, 'tipo_irregularidad')) {
                $table->text('tipo_irregularidad')->nullable();
            }

            foreach ([
                'nombre',
                'direccion',
                'comuna',
            ] as $column) {
                if (! Schema::hasColumn($tableName, $column)) {
                    $table->string($column)->nullable();
                }
            }

            if (! Schema::hasColumn($tableName, 'telefono')) {
                $table->text('telefono')->nullable();
            }
        });
    }

    private function dropChilquintaColumns(string $tableName): void
    {
        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            foreach ([
                'energia_ventana',
                'energia_fv',
                'energia_total',
                'monto_ventana',
                'monto_fv',
                'monto_total',
                'meses_ventana',
                'meses_fv',
                'meses_total',
                'tipo_cnr',
                'tipo_irregularidad',
                'nombre',
                'direccion',
                'comuna',
                'telefono',
            ] as $column) {
                if (Schema::hasColumn($tableName, $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
