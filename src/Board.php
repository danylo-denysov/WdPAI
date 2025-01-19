<?php

namespace App;

class Board
{
    private ?int $id;
    private string $title;
    private int $ownerId;
    private ?string $createdAt;

    public function __construct(
        ?int $id,
        string $title,
        int $ownerId,
        ?string $createdAt = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->ownerId = $ownerId;
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
}
