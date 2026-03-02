<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoreConfigAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = config('bl.config.database.connection') ?: config('database.default');
        $configTable = config('bl.config.db.table', 'core_config');
        $tableAdmin = config('bl.config.db.core_config_table', 'core_config_admin');

        Schema::connection($connection)->create($tableAdmin, function (Blueprint $table) use ($configTable) {
            $table->foreignId('config_id')
                ->unique()
                ->constrained($configTable)
                ->restrictOnUpdate()
                ->cascadeOnDelete();

            $table->string('backend_type')->default('text');
            $table->string('source')->nullable();
            $table->json('methods')->nullable();
            $table->string('rules')->nullable();
            $table->boolean('resettable')->nullable();
            $table->string('depends_of')->nullable();
            $table->string('depends_val')->nullable();
            $table->string('label')->nullable();
            $table->string('description')->nullable();
            $table->unsignedSmallInteger('order')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = config('bl.config.database.connection') ?: config('database.default');
        $table = config('bl.config.db.core_config_table', 'core_config_admin');

        Schema::connection($connection)->dropIfExists($table);
    }
}
