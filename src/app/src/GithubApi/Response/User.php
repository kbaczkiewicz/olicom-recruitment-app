<?php


namespace App\GithubApi\Response;

use Symfony\Component\Validator\Constraints as Assert;

class User
{
    /**
     * @Assert\NotBlank()
     */
    private $name;
    /**
     * @Assert\NotBlank()
     * @Assert\Url()
     */
    private $url;
    /**
     * @Assert\Email()
     */
    private $email;
    /**
     * @Assert\NotBlank()
     * @Assert\DateTime(format="Y-m-d\TH:i:s\Z")
     */
    private $createdAt;

    public function __construct(?string $name, ?string $url, ?string $email, ?string $createdAt)
    {
        $this->name = $name;
        $this->url = $url;
        $this->email = $email;
        $this->createdAt = $createdAt;
    }

    public static function fromArray(array $data)
    {
        return new self(
            isset($data['name']) ? $data['name'] : null,
            isset($data['html_url']) ? $data['html_url'] : null,
            isset($data['email']) ? $data['email'] : null,
            isset($data['created_at']) ? $data['created_at'] : null,
        );
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
}
