<?php

namespace Corcel;

class TermMeta extends PostMeta
{
    protected $table = 'termmeta';
    protected $fillable = ['meta_key', 'meta_value', 'term_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function term()
    {
        return $this->belongsTo(Term::class);
    }
}
