<?php

namespace GopanKombiyil\Larango\Eloquent;

use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;

abstract class Pivot extends Model
{
    use AsPivot;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
