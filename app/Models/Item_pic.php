<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Item_pic class
 *
 * Stores item photos as BLOBs in the database instead of on disk - the
 * free hosting this app runs on has no persistent filesystem, so anything
 * written to uploads/item_pics/ is lost on the next deploy or sleep/wake
 * cycle.
 */
class Item_pic extends Model
{
    protected $table = 'item_pics';
    protected $primaryKey = 'item_id';
    protected $useAutoIncrement = false;
    protected $useSoftDeletes = false;
    protected $allowedFields = ['item_id', 'mime_type', 'data', 'thumb_data'];

    /**
     * Inserts or replaces an item's photo. Clears any cached thumbnail so
     * it gets regenerated from the new image on next request.
     */
    public function save_picture(int $itemId, string $mimeType, string $data): bool
    {
        $builder = $this->db->table('item_pics');
        $builder->where('item_id', $itemId);
        $exists = $builder->countAllResults() > 0;

        $builder = $this->db->table('item_pics');

        if ($exists) {
            $builder->where('item_id', $itemId);

            return $builder->update(['mime_type' => $mimeType, 'data' => $data, 'thumb_data' => null]);
        }

        return $builder->insert(['item_id' => $itemId, 'mime_type' => $mimeType, 'data' => $data]);
    }

    /**
     * Caches a lazily-generated thumbnail so it isn't rebuilt on every
     * table-list request.
     */
    public function save_thumb(int $itemId, string $thumbData): bool
    {
        $builder = $this->db->table('item_pics');
        $builder->where('item_id', $itemId);

        return $builder->update(['thumb_data' => $thumbData]);
    }

    /**
     * @return array{item_id:int,mime_type:string,data:string,thumb_data:?string}|null
     */
    public function get_picture(int $itemId): ?array
    {
        $builder = $this->db->table('item_pics');
        $builder->where('item_id', $itemId);
        $row = $builder->get()->getRowArray();

        return $row ?: null;
    }

    public function delete_picture(int $itemId): bool
    {
        $builder = $this->db->table('item_pics');
        $builder->where('item_id', $itemId);

        return $builder->delete();
    }
}
