<?php

namespace Tempo;

abstract class TokenInfo 
{
    public static function getSecretKey()
    {
        return $_ENV['SECRET_KEY'];
    }

    public static function getAlgorithm()
    {
        return $_ENV['ALGORITHM'];
    }

    public static function getExpireTime()
    {
        return $_ENV['EXPIRE_TIME'];
    }
}