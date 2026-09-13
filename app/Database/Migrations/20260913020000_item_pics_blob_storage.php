<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Render's free web service has no persistent disk: uploads/item_pics/ is
 * wiped on every deploy, and on every sleep/wake cycle the free instance
 * goes through on inactivity - photos vanished within hours of being
 * uploaded. Move item photos into the (persistent) MySQL database instead
 * of the filesystem, keyed by item_id. `thumb_data` is a lazily-generated
 * cache of the small table-list thumbnail, regenerated on first request
 * after being cleared by a new upload.
 */
class Migration_Item_Pics_Blob_Storage extends Migration
{
    public function up(): void
    {
        $table = $this->db->prefixTable('item_pics');

        $this->db->query("CREATE TABLE IF NOT EXISTS `$table` (
            `item_id` int(11) NOT NULL,
            `mime_type` varchar(100) NOT NULL,
            `data` longblob NOT NULL,
            `thumb_data` mediumblob DEFAULT NULL,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`item_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function down(): void
    {
    }
}
