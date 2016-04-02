<?php

class NonCorcelTestModel extends \Illuminate\Database\Eloquent\Model
{
    protected $connection = 'no_prefix';
    protected $table = 'eloquent';

    public function corcel()
    {
        return $this->belongsTo('CorcelTestModel', 'corcel_id', 'ID');
    }
}