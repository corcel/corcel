<?php

/**
 * User model.
 *
 * @author Ashwin Sureshkumar<ashwin.sureshkumar@gmail.com>
 * @author Mickael Burguet <www.rundef.com>
 */
namespace Corcel;

class User extends Model
{
    const CREATED_AT = 'user_registered';
    const UPDATED_AT = 'updated_at';

    protected $table = 'users';
    protected $primaryKey = 'ID';
    protected $hidden = ['user_pass'];
    protected $dates = ['user_registered'];
    protected $with = array('meta');

    // Disable updated_at
    public function setUpdatedAtAttribute($value)
    {
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'login',
        'email',
        'slug',
        'url',
        'nickname',
        'first_name',
        'last_name',
        'created_at',
    ];

    /**
     * Meta data relationship.
     *
     * @return Corcel\UserMetaCollection
     */
    public function meta()
    {
        return $this->hasMany('Corcel\UserMeta', 'user_id');
    }

    public function fields()
    {
        return $this->meta();
    }

    /**
     * Posts relationship.
     *
     * @return Corcel\PostMetaCollection
     */
    public function posts()
    {
        return $this->hasMany('Corcel\Post', 'post_author');
    }

    /**
     * Comments relationship.
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function comments()
    {
        return $this->hasMany('Corcel\Comment', 'user_id');
    }

    /**
     * Overriding newQuery() to the custom UserBuilder with some interesting methods.
     *
     * @param bool $excludeDeleted
     *
     * @return Corcel\UserBuilder
     */
    public function newQuery()
    {
        $builder = new UserBuilder($this->newBaseQueryBuilder());
        $builder->setModel($this)->with($this->with);
        $builder->orderBy('user_registered', 'desc');

        return $builder;
    }

    /**
     * Magic method to return the meta data like the user original fields.
     *
     * @param string $key
     *
     * @return string
     */
    public function __get($key)
    {
        if (!isset($this->$key)) {
            if (isset($this->meta()->get()->$key)) {
                return $this->meta()->get()->$key;
            }
        }

        return parent::__get($key);
    }

    public function save(array $options = array())
    {
        if (isset($this->attributes[$this->primaryKey])) {
            $this->meta->save($this->attributes[$this->primaryKey]);
        }

        return parent::save($options);
    }


    /**
     * Accessors.
     */

    /**
     * Get login attribute.
     *
     * @return string
     */
    public function getLoginAttribute()
    {
        return $this->user_login;
    }
    /**
     * Get email attribute.
     *
     * @return string
     */
    public function getEmailAttribute()
    {
        return $this->user_email;
    }
    /**
     * Get slug attribute.
     *
     * @return string
     */
    public function getSlugAttribute()
    {
        return $this->user_nicename;
    }
    /**
     * Get url attribute.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        return $this->user_url;
    }
    /**
     * Get nickname attribute.
     *
     * @return string
     */
    public function getNicknameAttribute()
    {
        return $this->meta->nickname;
    }
    /**
     * Get first name attribute.
     *
     * @return string
     */
    public function getFirstNameAttribute()
    {
        return $this->meta->first_name;
    }
    /**
     * Get last name attribute.
     *
     * @return string
     */
    public function getLastNameAttribute()
    {
        return $this->meta->last_name;
    }

    /**
     * Get created at attribute.
     *
     * @return date
     */
    public function getCreatedAtAttribute()
    {
        return $this->user_registered;
    }
}
