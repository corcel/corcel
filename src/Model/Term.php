<?php

namespace Corcel\Model;

use Corcel\Concerns\AdvancedCustomFields;
use Corcel\Concerns\MetaFields;
use Corcel\Model;

/**
 * Class Term.
 *
 * @package Corcel\Model
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Term extends Model
{
    use MetaFields;
    use AdvancedCustomFields;

    /**
     * @var string
     */
    protected $table = 'terms';

    /**
     * @var string
     */
    protected $primaryKey = 'term_id';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function taxonomy()
    {
        return $this->hasOne(Taxonomy::class, 'term_id');
    }

    /**
     * @param   \Illuminate\Database\Query\Builder  $query
     * @param   string|array  $taxonomies
     * @return  void
     */
    public function scopeWhereTaxonomy($query, $taxonomies)
    {
        if (!is_array($taxonomies)) {
            $taxonomies = [$taxonomies];
        }

        $query->whereHas('taxonomy', function ($query) use ($taxonomies) {
            $query->whereIn('taxonomy', $taxonomies);
        });
    }

    /**
     * Alias of scopeWhereTaxonomy method.
     *
     * @param   \Illuminate\Database\Query\Builder  $query
     * @param   array  $taxonomies
     * @return  void
     */
    public function scopeWhereTaxonomies($query, array $taxonomies = [])
    {
        $this->scopeWhereTaxonomy($query, $taxonomies);
    }
}
