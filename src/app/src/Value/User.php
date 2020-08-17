<?php


namespace App\Value;


class User implements \JsonSerializable
{
    private $name;
    private $url;
    private $email;
    private $createdAt;

    public function __construct(string $name, Url $url, \DateTime $createdAt, ?Email $email = null)
    {
        $this->name = $name;
        $this->url = $url;
        $this->email = $email;
        $this->createdAt = $createdAt;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): Url
    {
        return $this->url;
    }

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->getName(),
            'email' => $this->getEmail() ? $this->getEmail()->get() : null,
            'url' => $this->getUrl()->get(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s')
        ];        
    }
}
