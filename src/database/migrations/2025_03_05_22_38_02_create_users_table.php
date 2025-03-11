<?php

namespace Monolog\App\Database\Migrations;

use Monolog\App\Helpers\Database\Migration;

return new class extends Migration {
    /**
     * Runs the migration to create the users table.
     */
    public function up() {
        $this->createTable('users', [
            'id' => 'INT(11) AUTO_INCREMENT PRIMARY KEY',
            'name' => 'VARCHAR(255) NOT NULL',
            'email' => 'VARCHAR(255) UNIQUE NOT NULL',
            'password' => 'VARCHAR(255) NOT NULL',
            'verify_at' => 'TIMESTAMP NULL DEFAULT NULL',
            'token_session' => 'VARCHAR(255) NULL DEFAULT NULL',
            'created_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
        ]);
           
    }

    /**
     * Rolls back the migration by dropping the users table.
     */
    public function down() {
        $this->dropTable('users');
    }
};
