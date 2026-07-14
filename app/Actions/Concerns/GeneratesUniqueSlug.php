<?php

namespace App\Actions\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait GeneratesUniqueSlug
{
    protected function uniqueSlug(string $modelClass, ?string $slug, string $name, ?int $ignoreId = null): string
    {
        $slug = $slug ?: Str::slug($name);

        if ($slug === '') {
            $slug = 'item';
        }

        $original = $slug;
        $count = 1;

        while ($this->slugExists($modelClass, $slug, $ignoreId)) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }

    protected function slugExists(string $modelClass, string $slug, ?int $ignoreId): bool
    {
        $usesSoftDeletes = in_array(
            \Illuminate\Database\Eloquent\SoftDeletes::class,
            array_keys(class_uses($modelClass)),
            true
        );

        $query = $usesSoftDeletes
            ? $modelClass::withTrashed()->where('slug', $slug)
            : $modelClass::where('slug', $slug);

        if ($ignoreId) {
            $query->where('id', '<>', $ignoreId);
        }

        return $query->exists();
    }
}
