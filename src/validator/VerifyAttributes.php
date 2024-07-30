<?php

namespace Tempo\Attributes;

#[\Attribute]
class Required
{
    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}


#[\Attribute]
class Email
{
    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}

#[\Attribute]
class MinLength
{
    public int $length;
    public string $message;

    public function __construct(int $length, string $message)
    {
        $this->length = $length;
        $this->message = $message;
    }
}

#[\Attribute]
class OnlyNumbers {
    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}

#[\Attribute]
class onlyLetters {
    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}


#[\Attribute]
class MaxLength
{
    public int $length;
    public string $message;

    public function __construct(int $length, string $message)
    {
        $this->length = $length;
        $this->message = $message;
    }
}

#[\Attribute]
class MinValue
{
    public int $value;
    public string $message;

    public function __construct(int $value, string $message)
    {
        $this->value = $value;
        $this->message = $message;
    }
}

#[\Attribute]
class MaxValue
{
    public int $value;
    public string $message;

    public function __construct(int $value, string $message)
    {
        $this->value = $value;
        $this->message = $message;
    }
}

#[\Attribute]
class Date
{
    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}

#[\Attribute]
class DateTime
{
    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}

#[\Attribute]
class Time
{
    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}

#[\Attribute]
class Url
{
    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}

#[\Attribute]
class Boolean
{
    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}

#[\Attribute]
class minWords
{
    public int $words;
    public string $message;

    public function __construct(int $words, string $message)
    {
        $this->words = $words;
        $this->message = $message;
    }

}

#[\Attribute]
class maxWords
{
    public int $words;
    public string $message;

    public function __construct(int $words, string $message)
    {
        $this->words = $words;
        $this->message = $message;
    }
}

#[\Attribute]
class regex
{
    public string $regex;
    public string $message;

    public function __construct(string $regex, string $message)
    {
        $this->regex = $regex;
        $this->message = $message;
    }
}