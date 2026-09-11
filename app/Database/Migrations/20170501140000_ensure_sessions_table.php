<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * initial_schema.sql's CREATE TABLE ospos_sessions statement can fail
 * silently: Migration_Initial_Schema::up() discards executeScript()'s
 * return value, so a missing table doesn't surface until a later
 * migration (upgrade_to_3_1_1's CONVERT TO CHARACTER SET) hits it with
 * a hard-to-diagnose "doesn't exist" error. Recreate it here if needed
 * before that migration runs.
 */
class Migration_Ensure_Sessions_Table extends Migration
{
    public function up(): void
    {
        $this->db->query('
            CREATE TABLE IF NOT EXISTS `ospos_sessions` (
                `id` varchar(40) NOT NULL,
                `ip_address` varchar(45) NOT NULL,
                `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
                `data` blob NOT NULL,
                KEY `ci_sessions_timestamp` (`timestamp`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');
    }

    public function down(): void {}
}
