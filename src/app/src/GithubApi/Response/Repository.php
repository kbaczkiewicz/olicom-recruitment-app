<?php


namespace App\GithubApi\Response;


use Symfony\Component\Validator\Constraints as Assert;

class Repository
{
    /**
     * @Assert\NotNull()
     */
    private $fullName;
    private $description;
    /**
     * @Assert\NotNull()
     * @Assert\Url()
     */
    private $cloneUrl;
    /**
     * @Assert\NotNull()
     * @Assert\PositiveOrZero()
     */
    private $stars;
    /**
     * @Assert\NotNull()
     * @Assert\DateTime(format="Y-m-d\TH:i:s\Z")
     */
    private $createdAt;

    public function __construct(
        ?string $fullName,
        ?string $description,
        ?string $cloneUrl,
        ?int $stars,
        ?string $createdAt
    )
    {
        $this->fullName = $fullName;
        $this->description = $description;
        $this->cloneUrl = $cloneUrl;
        $this->stars = $stars;
        $this->createdAt = $createdAt;
    }

    public static function fromArray(array $data)
    {
        return new self(
            isset($data['full_name']) ? $data['full_name'] : null,
            isset($data['description']) ? $data['description'] : null,
            isset($data['clone_url']) ? $data['clone_url'] : null,
            isset($data['stargazers_count']) ? $data['stargazers_count'] : null,
            isset($data['created_at']) ? $data['created_at'] : null
        );
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCloneUrl(): ?string
    {
        return $this->cloneUrl;
    }

    public function getStars(): ?int
    {
        return $this->stars;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
}
