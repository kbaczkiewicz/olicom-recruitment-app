<?php


namespace App\Value;


class Url
{
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function get(): string
    {
        return $this->url;
    }
}
