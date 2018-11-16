<?php

namespace Corcel\Concerns;

use Corcel\Model\Taxonomy;
use Corcel\Model\Term;

/**
 * Trait TaxonomySupport
 *
 * @package Corcel\Concerns
 * @author Junior Grossi <juniorgro@gmail.com>
 */
trait TaxonomySupport
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function taxonomies()
    {
        return $this->belongsToMany(
            Taxonomy::class, 'term_relationships', 'object_id', 'term_taxonomy_id'
        );
    }

    /**
     * Add a new term and taxonomy to the post
     *
     * @param string $taxonomy
     * @param string $term
     * @return Term
     */
    public function addTerm(string $taxonomy, string $term): Term
    {
        $term = Term::query()->firstOrCreate([
            'name' => $term,
            'slug' => str_slug($term),
        ]);

        return tap($term, function (Term $term) use ($taxonomy) {
            $this->taxonomies()->firstOrCreate([
                'term_id' => $term->term_id,
                'taxonomy' => $taxonomy,
            ]);
        });
    }

    /**
     * Whether the post contains the term or not.
     *
     * @param string $taxonomy
     * @param string $term
     * @return bool
     */
    public function hasTerm($taxonomy, $term)
    {
        return isset($this->terms[$taxonomy]) &&
            isset($this->terms[$taxonomy][$term]);
    }

    /**
     * Gets all the terms arranged taxonomy => terms[].
     *
     * @return array
     */
    public function getTermsAttribute()
    {
        return $this->taxonomies
            ->groupBy(function ($taxonomy) {
                return $taxonomy->taxonomy == 'post_tag' ?
                    'tag' : $taxonomy->taxonomy;
            })->map(function ($group) {
                return $group->mapWithKeys(function ($item) {
                    return [$item->term->slug => $item->term->name];
                });
            })
            ->toArray();
    }

    /**
     * Gets the first term of the first taxonomy found.
     *
     * @return string
     */
    public function getMainCategoryAttribute(): string
    {
        $main_category = 'Uncategorized';

        if (!empty($this->terms)) {
            $first_taxonomy = array_first($this->terms);
            if (!empty($first_taxonomy)) {
                $main_category = array_first(array_values($first_taxonomy));
            }
        }

        return $main_category;
    }

    /**
     * Gets the keywords as array.
     *
     * @return array
     */
    public function getKeywordsAttribute(): array
    {
        return collect($this->terms)
            ->map(function ($taxonomy) {
                return collect($taxonomy)->values();
            })
            ->collapse()
            ->toArray();
    }

    /**
     * Gets the keywords as string.
     *
     * @return string
     */
    public function getKeywordsStrAttribute(): string
    {
        return implode(',', (array) $this->keywords);
    }

    /**
     * Get the post format, like the WP get_post_format() function.
     *
     * @return bool|string
     */
    public function getFormat()
    {
        $taxonomy = $this->taxonomies()
            ->where('taxonomy', 'post_format')
            ->first();

        if ($taxonomy && $taxonomy->term) {
            return str_replace('post-format-', '', $taxonomy->term->slug);
        }

        return false;
    }
}
