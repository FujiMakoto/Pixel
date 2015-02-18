<?php namespace Pixel\Repositories;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
//use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Pagination\Paginator as Paginator;

abstract class Repository implements \ArrayAccess, Arrayable, Jsonable, \JsonSerializable {

    /**
     * The repositories attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * The repositories hidden attributes
     *
     * @var array
     */
    protected $hiddenAttributes = [];

    /**
     * The attributes excluded from the repositories JSON form
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be mutated to dates
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'expires_at'];

    /**
     * Create a new image repository instance
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        $this->fill($attributes);
    }

    /**
     * Fill the repository with an array of attributes
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value)
            $this->setAttribute($key, $value);

        return $this;
    }

    /**
     * Get an attribute from the repository
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        $inAttributes = array_key_exists($key, $this->attributes);

        if ($inAttributes) {
            $value = $this->attributes[$key];

            // If the attribute is listed as a date, we will convert it to a DateTime
            // instance on retrieval
            if ( in_array($key, $this->dates) ) {
                if ($value) return $this->asDateTime($value);
            }

            return $value;
        }
    }

    /**
     * Return all attribute types in a single array
     *
     * @return array
     */
    public function getAllAttributes()
    {
        return array_merge($this->attributes, $this->hiddenAttributes);
    }

    /**
     * Set a given attribute on the repository
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function setAttribute($key, $value)
    {
        if ( in_array($key, $this->hidden) )
            $this->hiddenAttributes[$key] = $value;
        else
            $this->attributes[$key] = $value;
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param  mixed  $value
     * @return \Carbon\Carbon
     */
    protected function asDateTime($value)
    {
        if ( is_numeric($value) )
        {
            return Carbon::createFromTimestamp($value);
        }
        elseif (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value))
        {
            return Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        }
        elseif ( ! $value instanceof \DateTime)
        {
            return Carbon::createFromTimestamp( strtotime($value) );
        }

        return Carbon::instance($value);
    }

    /**
     * Convert the repository instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the repository instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return (array)$this->attributes;
    }

    /**
     * Determine if an attribute exists on the repository
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Unset an attribute on the repository
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
    }

    /**
     * Dynamically retrieve attributes on the repository
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the repository
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * Get the value for a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * Set the value for a given offset.
     *
     * @param  mixed  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * Unset the value for a given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->$offset);
    }

    /**
     * Convert the repository to its string representation
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }


}