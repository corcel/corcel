<?php

/**
 * User model.
 *
 * @author Ashwin Sureshkumar<ashwin.sureshkumar@gmail.com>
 * @author Mickael Burguet <www.rundef.com>
 */

namespace Corcel;

use Corcel\Traits\HasMetaFields;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;

class User extends Model implements Authenticatable, CanResetPassword
{
    const CREATED_AT = 'user_registered';
    const UPDATED_AT = null;

    use HasMetaFields;

    /**
     * @var string
     */
    protected $table = 'users';

    /**
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * @var array
     */
    protected $hidden = ['user_pass'];

    /**
     * @var array
     */
    protected $dates = ['user_registered'];

    /**
     * @var array
     */
    protected $with = ['meta'];

    /**
     * @param mixed $value
     */
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'post_author');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    /**
     * @return UserBuilder
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
     * @return string
     */
    public function __get($key)
    {
        if ($value = parent::__get($key)) {
            return $value;
        }

        if (!isset($this->$key)) {
            if (isset($this->meta->$key)) {
                return $this->meta->$key;
            }
        }
    }

    /**
     * @param array $options
     * @return bool
     * @todo Refactor this to HasMetaFields trait of something like this
     */
    public function save(array $options = [])
    {
        if (isset($this->attributes[$this->primaryKey])) {
            $this->meta->save($this->attributes[$this->primaryKey]);
        }

        return parent::save($options);
    }

    /**
     * Get login attribute.
     *
     * @return string
     * @todo Think in how to avoid this extra methods. Maybe an instance variable as array
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

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->primaryKey;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->attributes[$this->primaryKey];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->user_pass;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return $this->meta->{$this->getRememberTokenName()};
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     */
    public function setRememberToken($value)
    {
        $this->meta->{$this->getRememberTokenName()} = $value;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->user_email;
    }

    /**
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        // TODO: Implement sendPasswordResetNotification() method.
    }
}
