<?php

if (!function_exists('corcel')) {
    /**
     * Returns a model instance based on parameter
     *
     * @param string $model
     * @return \Corcel\Model|\Illuminate\Database\Eloquent\Builder
     */
    function corcel(string $model)
    {
        $name = str_singular(studly_case($model));
        $class = sprintf('Corcel\\Model\\%s', $name);

        return new $class();
    }
}
