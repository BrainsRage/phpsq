<?php

namespace phpsq;

use phpsq\exceptions\PhpSQException;

class StringHelper
{
    /**
     * Generates specified number of random bytes.
     * Note that output may not be ASCII.
     *
     * @see generateRandomString() if you need a string.
     *
     * @param integer $length the number of bytes to generate
     *
     * @return string the generated random bytes
     * @throws PhpSQException if mcrypt extension is not installed.
     */
    public static function generateRandomKey($length = 32)
    {
        if (!extension_loaded('mcrypt')) {
            throw new PhpSQException('The mcrypt PHP extension is not installed.');
        }
        $bytes = mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
        if ($bytes === false) {
            throw new PhpSQException('Unable to generate random bytes.');
        }
        return $bytes;
    }
}