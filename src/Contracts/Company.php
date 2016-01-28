<?php

namespace VLG\GSSAuth\Contracts;

interface Company
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
}
