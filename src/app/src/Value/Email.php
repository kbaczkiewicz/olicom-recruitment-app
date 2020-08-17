<?php


namespace App\Value;


class Email
{
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function get(): string
    {
        return $this->email;
    }
}
