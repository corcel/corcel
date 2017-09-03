<?php

namespace Corcel\Model\Meta;

use Corcel\Model\Term;

/**
 * Class TermMeta
 *
 * @package Corcel\Model\Meta
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class TermMeta extends Meta
{
    /**
     * @var string
     */
    protected $table = 'termmeta';

    /**
     * @var array
     */
    protected $fillable = ['meta_key', 'meta_value', 'term_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo(Term::class);
    }
}
