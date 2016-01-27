<?php

namespace VLG\GSSAuth\Contracts;

interface Token
{
    /**
     * Get the unique identifier for the user.
     *
     * @return string
     */
    public function isValid();
}
