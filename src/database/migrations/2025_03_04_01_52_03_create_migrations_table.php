<?php

namespace Monolog\App\Database\Migrations;

use Monolog\App\Helpers\Database\Migration;

return new class extends Migration {
    /**
     * Runs the migration to create the migrations table.
     */
    public function up() {
        $this->createTable('migrations', [
            'id' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
            'migration_name' => 'VARCHAR(255) NOT NULL',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        ]);
    }

    /**
     * Rolls back the migration by dropping the migrations table.
     */
    public function down() {
        $this->dropTable('migrations');
    }
};