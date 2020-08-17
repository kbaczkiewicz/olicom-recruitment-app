<?php


namespace App\Value;


class Repository implements \JsonSerializable
{
    private $fullName;
    private $description;
    private $cloneUrl;
    private $stars;
    private $createdAt;

    public function __construct(
        string $fullName,
        Url $cloneUrl,
        int $stars,
        \DateTime $createdAt,
        ?string $description = null
    )
    {
        $this->fullName = $fullName;
        $this->description = $description;
        $this->cloneUrl = $cloneUrl;
        $this->stars = $stars;
        $this->createdAt = $createdAt;
    }

    public function getFullName(): string
    {
        return $this->fullName;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCloneUrl(): Url
    {
        return $this->cloneUrl;
    }

    public function getStars(): int
    {
        return $this->stars;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
    
    public function jsonSerialize(): array
    {
        return [
            'fullName' => $this->getFullName(),
            'description' => $this->getDescription(),
            'cloneUrl' => $this->getCloneUrl()->get(),
            'stars' => $this->getStars(),
            'createdAt' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
