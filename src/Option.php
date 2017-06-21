<?php

namespace Corcel;

use Exception;

/**
 * Option class.
 *
 * @author José CI <josec89@gmail.com>
 */
class Option extends Model
{
    const CREATED_AT = null;
    const UPDATED_AT = null;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'option';
    /**
     * The primary key of the model.
     *
     * @var string
     */
    protected $primaryKey = 'option_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'option_name',
        'option_value',
        'autoload',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['value'];

    /**
     * Gets the value.
     * Tries to unserialize the object and returns the value if that doesn't work.
     *
     * @return value
     */
    public function getValueAttribute()
    {
        try {
            $value = unserialize($this->option_value);
            // if we get false, but the original value is not false then something has gone wrong.
            // return the option_value as is instead of unserializing
            // added this to handle cases where unserialize doesn't throw an error that is catchable
            return $value === false && $this->option_value !== false ? $this->option_value : $value;
        } catch (Exception $ex) {
            return $this->option_value;
        }
    }

    /**
     * Gets option field by its name.
     *
     * @param string $name
     *
     * @return string|array
     */
    public static function get($name)
    {
        if ($option = self::where('option_name', $name)->first()) {
            return $option->value;
        }

        return;
    }

    /**
     * Gets all the options.
     *
     * @return array
     * @deprecated
     */
    public static function getAll()
    {
        return static::asArray();
    }

    /**
     * @return array
     */
    public static function asArray()
    {
        return static::all()
            ->pluck('value', 'option_name')
            ->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        if ($this instanceof Option) {
            return [
                $this->option_name => $this->value,
            ];
        }

        return parent::toArray();
    }
}
