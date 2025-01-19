<?php

namespace App;

class TaskGroup
{
    private ?int $id;
    private int $boardId;
    private string $title;
    private int $position;
    private ?string $createdAt;

    public function __construct(
        ?int $id,
        int $boardId,
        string $title,
        int $position = 0,
        ?string $createdAt = null
    ) {
        $this->id = $id;
        $this->boardId = $boardId;
        $this->title = $title;
        $this->position = $position;
        $this->createdAt = $createdAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBoardId(): int
    {
        return $this->boardId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
}
