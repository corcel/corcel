<?php

namespace Corcel\Model;

use Corcel\Model;
use Corcel\Traits\HasAcfFields;
use Corcel\Traits\HasMetaFields;

/**
 * Class Term.
 *
 * @package Corcel\Model
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Term extends Model
{
    use HasMetaFields;
    use HasAcfFields;

    protected $table = 'terms';
    protected $primaryKey = 'term_id';
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function taxonomy()
    {
        return $this->hasOne(Taxonomy::class, 'term_id');
    }
}
