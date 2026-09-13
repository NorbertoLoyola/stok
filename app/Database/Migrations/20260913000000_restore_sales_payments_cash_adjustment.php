<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * The repair migration (20260911000000) re-runs 3.3.0_paymenttracking.sql to
 * recover from earlier silently-skipped migrations. That script does
 * `RENAME TABLE ospos_sales_payments TO ..._backup` followed by a fresh
 * `CREATE TABLE ospos_sales_payments`, which resets the table to its 3.3.0-era
 * shape — wiping out `cash_adjustment`, a column added years later (2020) by
 * 20201108100000_cashrounding.php. That migration is already marked complete
 * in the migrations table, so CodeIgniter will never re-run it to restore the
 * column. Re-add it explicitly here, guarded by an INFORMATION_SCHEMA check
 * (rather than `ADD COLUMN IF NOT EXISTS`, which Aiven's MySQL 8.4.8 rejects
 * with a syntax error despite that clause existing since MySQL 8.0.29) so
 * this stays a safe no-op on any environment where the column already
 * survived intact.
 */
class Migration_Restore_Sales_Payments_Cash_Adjustment extends Migration
{
    public function up(): void
    {
        $table = $this->db->prefixTable('sales_payments');

        $exists = $this->db->query(
            'SELECT 1 FROM information_schema.columns
             WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?',
            [$table, 'cash_adjustment']
        )->getNumRows() > 0;

        if (! $exists) {
            $this->db->query("ALTER TABLE $table ADD COLUMN `cash_adjustment` tinyint NOT NULL DEFAULT 0 AFTER `cash_refund`");
        }
    }

    public function down(): void
    {
    }
}
