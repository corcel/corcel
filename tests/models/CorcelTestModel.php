<?php

class CorcelTestModel extends \Corcel\Post
{

    public function eloquent()
    {
        return $this->hasMany('NonCorcelTestModel', 'id', 'ID', false);
    }

    public function eloquentWithConnection()
    {
        return $this->hasMany('NonCorcelTestModel', 'id', 'ID', true);
    }
}