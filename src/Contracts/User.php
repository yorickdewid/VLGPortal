<?php

namespace VLG\GSSAuth\Contracts;

interface User
{
    /**
     * Get the unique identifier for the user.
     *
     * @return string
     */
    public function getId();

    /**
     * Get the full name of the user.
     *
     * @return string
     */
    public function isActive();

    /**
     * Get the e-mail address of the user.
     *
     * @return string
     */
    public function isAdmin();

    /**
     * Get the avatar / image URL for the user.
     *
     * @return string
     */
    public function canRead();

    /**
     * Get the avatar / image URL for the user.
     *
     * @return string
     */
    public function canWrite();
}
