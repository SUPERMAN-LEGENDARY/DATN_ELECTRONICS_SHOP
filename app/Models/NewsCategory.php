<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class NewsCategory extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $fillable = ['name', 'slug', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function news(): HasMany
    {
        return $this->hasMany(News::class, 'news_category_id');
    }

    public static function generateSlug(string $name, ?int $exceptId = null): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;
        while (
            static::withTrashed()->where('slug', $slug)
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->exists()
        ) {
            $slug = $original . '-' . $count++;
        }
        return $slug;
    }
}
