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
 * column. Re-add it explicitly here; IF NOT EXISTS makes this a safe no-op on
 * any environment where the column already survived intact.
 */
class Migration_Restore_Sales_Payments_Cash_Adjustment extends Migration
{
    public function up(): void
    {
        $this->db->query('ALTER TABLE ' . $this->db->prefixTable('sales_payments') . ' ADD COLUMN IF NOT EXISTS `cash_adjustment` tinyint NOT NULL DEFAULT 0 AFTER `cash_refund`');
    }

    public function down(): void
    {
    }
}
