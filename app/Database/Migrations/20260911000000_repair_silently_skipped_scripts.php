<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Almost every SQL-script migration in this app calls executeScript()
 * without checking its return value, so a script that failed midway (e.g.
 * because Aiven's managed MySQL enforces sql_require_primary_key, which
 * several legacy CREATE TABLE statements here violate) still got marked as
 * a completed migration — CodeIgniter never retries those. executeScript()
 * has since been fixed to throw on real failures and to tolerate the
 * specific MySQL error codes that mean "this change is already applied",
 * so re-running every previously-discarded-return-value script here is
 * safe: anything already correctly applied is a fast no-op, and anything
 * silently missed the first time actually gets created now.
 *
 * Deliberately excluded: initial_schema.sql and 3.0.2_to_3.1.1.sql (their
 * migrations already check executeScript()'s return value and throw, so a
 * silent partial failure isn't possible for them) and 3.4.0_CI4Conversion.sql
 * (same — checked and thrown already).
 */
class Migration_Repair_Silently_Skipped_Scripts extends Migration
{
    public function up(): void
    {
        helper('migration');

        $scripts = [
            '3.1.1_to_3.2.0.sql',
            '3.2.0_to_3.2.1.sql',
            '3.2.1_to_3.3.0.sql',
            '3.3.0_attributes.sql',
            '3.3.0_indiagst.sql',
            '3.3.0_indiagst1.sql',
            '3.3.0_indiagst2.sql',
            '3.3.0_decimal_attribute_type.sql',
            '3.3.0_add_iso_4217.sql',
            '3.3.0_paymenttracking.sql',
            '3.3.0_refundtracking.sql',
            '3.3.0_dbfix.sql',
            '3.3.0_fix_attribute_datetime.sql',
            '3.3.2_paymentdatefix.sql',
            '3.3.2_saleschangeprice.sql',
            '3.3.2_modify_attr_links_constraint.sql',
            '3.3.3_add_kits_item_number.sql',
            '3.3.4_modify_session_datatype.sql',
            '3.4.0_database_optimizations.sql',
            '3.4.0_attribute_links_unique_constraint.sql',
            '3.4.1_migrate_sessions_table.sql',
            '3.4.1_attribute_links_unique_constraint.sql',
            '3.4.2_missing_config_keys.sql',
            '3.4.2_nullable_tax_category_id.sql',
        ];

        foreach ($scripts as $script) {
            // Matches the transaction flag each script's original migration
            // wrapper used.
            $withTransaction = $script === '3.4.2_missing_config_keys.sql';
            executeScript(APPPATH . "Database/Migrations/sqlscripts/$script", $withTransaction);
        }
    }

    public function down(): void {}
}
