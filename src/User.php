<?php

namespace VLG\GSSAuth;

use ArrayAccess;

class User implements ArrayAccess, Contracts\User
{
    /**
     * The unique identifier for the user.
     *
     * @var mixed
     */
    public $id;

    /**
     * The user's nickname / username.
     *
     * @var string
     */
    public $name;

    /**
     * The user's full name.
     *
     * @var string
     */
    public $last_name;

    /**
     * The user's e-mail address.
     *
     * @var string
     */
    public $email;

    /**
     * The user's avatar image URL.
     *
     * @var string
     */
    public $phone;

    /**
     * The user's avatar image URL.
     *
     * @var string
     */
    public $mobile;

    /**
     * The user's avatar image URL.
     *
     * @var string
     */
    private $isActive = false;

    /**
     * The user's avatar image URL.
     *
     * @var string
     */
    private $isAdmin = false;

    /**
     * The user's avatar image URL.
     *
     * @var string
     */
    private $canRead = false;

    /**
     * The user's avatar image URL.
     *
     * @var string
     */
    private $canWrite = false;

    /**
     * The user's avatar image URL.
     *
     * @var string
     */
    public $function;

    /**
     * The user's avatar image URL.
     *
     * @var string
     */
    public $type;

    /**
     * The user's avatar image URL.
     *
     * @var string
     */
    public $company;

    /**
     * The user's avatar image URL.
     *
     * @var string
     */
    public $created;

    /**
     * The user's avatar image URL.
     *
     * @var string
     */
    public $updated;

    /**
     * Get the unique identifier for the user.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the avatar / image URL for the user.
     *
     * @return string
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * Get the avatar / image URL for the user.
     *
     * @return string
     */
    public function isAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * Get the avatar / image URL for the user.
     *
     * @return string
     */
    public function makeAdmin()
    {
        return $this->isAdmin = true;
    }

    /**
     * Get the avatar / image URL for the user.
     *
     * @return string
     */
    public function canRead()
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->canRead;
    }

    /**
     * Get the avatar / image URL for the user.
     *
     * @return string
     */
    public function canWrite()
    {
        if ($this->isAdmin()) {
            return true;
        }

        return $this->canWrite;
    }

    /**
     * Get the avatar / image URL for the user.
     *
     * @return string
     */
    public function formalName() {
        return $this->name . ' ' . $this->last_name;
    }

    /**
     * Map the given array onto the user's properties.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function map(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }

        return $this;
    }

    /**
     * Determine if the given raw user attribute exists.
     *
     * @param  string  $offset
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->user);
    }

    /**
     * Get the given key from the raw user.
     *
     * @param  string  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->user[$offset];
    }

    /**
     * Set the given attribute on the raw user array.
     *
     * @param  string  $offset
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->user[$offset] = $value;
    }

    /**
     * Unset the given value from the raw user array.
     *
     * @param  string  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->user[$offset]);
    }
}
