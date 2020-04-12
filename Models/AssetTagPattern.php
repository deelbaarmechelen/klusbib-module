<?php

namespace Modules\Klusbib\Models;

use Illuminate\Database\Eloquent\Model;

class AssetTagPattern extends Model
{
    protected $fillable = [
        'pattern',
        'next_auto_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kb_asset_tag_patterns';

    /**
     * Query builder scope for pattern
     *
     * @param  \Illuminate\Database\Query\Builder $query Query builder instance
     *
     * @return \Illuminate\Database\Query\Builder          Modified query builder
     */

    public function scopePattern($query, $pattern)
    {
        return $query->where('pattern', '=', $pattern);
    }

}
