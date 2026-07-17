<?php

namespace App\Traits;

use App\Enum\CacheKeyEnum;

trait ModelCache
{
    /**
     * Get cached record(s) by id/column, avoiding repeated DB calls.
     *
     * @param  mixed  $id
     * @param  string|array  $col
     * @param  mixed  $select
     * @param  mixed  $ttl
     * @param  bool  $withRelations
     * @param  array  $relations
     * @param  bool|string  $aggregateRelations
     * @return $this|static|\\Illuminate\\Database\\Eloquent\\Collection|\\Illuminate\\Support\\Collection|null
     */
    public static function getCached(
        string|int|array|null $id,
        string|array $col = 'id',
        $select = ['*'],
        $ttl = 3600,
        bool $withRelations = false,
        array $relations = [],
        bool|string $aggregateRelations = false
    ) {
        $cacheId = is_array($id) ? implode('_', $id) : ($id ?? 'all');
        $colKey = is_array($col) ? implode('_', $col) : $col;
        $clonedObj = new static;
        $table = $clonedObj->table ?? $clonedObj->getTable();

        $cacheKey = CacheKeyEnum::modelCacheKey($table, (string) $colKey, (string) $cacheId, $withRelations, $aggregateRelations);

        return cache()->remember($cacheKey, $ttl, function () use ($id, $col, $select, $withRelations, $relations, $aggregateRelations) {
            $query = self::select($select);

            if (! is_null($id)) {
                if (is_array($id)) {
                    $query->whereIn($col, $id);
                } else {
                    $query->where($col, $id);
                }
            }

            if ($withRelations) {
                if ($aggregateRelations) {
                    $query->{'with'.ucfirst((string) $aggregateRelations)}($relations);
                } else {
                    $query->with($relations);
                }
            }

            return is_array($id) || is_null($id) ? $query->get() : $query->first();
        });
    }
}
