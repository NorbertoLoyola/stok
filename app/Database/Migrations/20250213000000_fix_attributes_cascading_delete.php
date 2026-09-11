<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use App\Models\Attribute;
use CodeIgniter\Database\ResultInterface;

class Migration_Attributes_fix_cascading_delete extends Migration
{
    /**
     * Perform a migration step.
     */
    public function up(): void
    {
        log_message('info', 'Fixing cascading deletes.');
        helper('migration');

        // attribute_links may not exist yet on a database where
        // 20181015100000_attributes.php's script silently failed to create
        // it (see 20260911000000_repair_silently_skipped_scripts.php).
        // That repair migration recreates it later in the run, so just
        // skip this cascade-behavior tweak instead of crashing here.
        try {
            $this->db->query("ALTER TABLE `ospos_attribute_links` DROP INDEX `attribute_links_uq3`");
            $this->db->query("ALTER TABLE `ospos_attribute_links` DROP COLUMN `generated_unique_column`");

            dropForeignKeyConstraints(['ospos_attribute_links_ibfk_1', 'ospos_attribute_links_ibfk_2'], 'attribute_links');

            $this->db->query("ALTER TABLE `ospos_attribute_links` ADD CONSTRAINT `ospos_attribute_links_ibfk_1` FOREIGN KEY (`definition_id`) REFERENCES `ospos_attribute_definitions` (`definition_id`) ON DELETE CASCADE;");
            $this->db->query("ALTER TABLE `ospos_attribute_links` ADD CONSTRAINT `ospos_attribute_links_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `ospos_attribute_values` (`attribute_id`) ON DELETE CASCADE;");
        } catch (\Throwable $e) {
            fwrite(STDERR, '[fix_attributes_cascading_delete] skipped: ' . $e->getMessage() . PHP_EOL);
        }
    }

    /**
     * Revert a migration step.
     */
    public function down(): void {}
}
