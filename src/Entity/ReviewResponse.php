<?php

namespace App\Entity;

use App\Repository\ReviewResponseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewResponseRepository::class)]
class ReviewResponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $responseId = null;

    #[ORM\Column(length: 100)]
    private ?string $responder = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $repliedAt = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'responses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Review $review = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResponseId(): ?string
    {
        return $this->responseId;
    }

    public function setResponseId(string $responseId): static
    {
        $this->responseId = $responseId;

        return $this;
    }

    public function getResponder(): ?string
    {
        return $this->responder;
    }

    public function setResponder(string $responder): static
    {
        $this->responder = $responder;

        return $this;
    }

    public function getRepliedAt(): ?\DateTimeImmutable
    {
        return $this->repliedAt;
    }

    public function setRepliedAt(\DateTimeImmutable $repliedAt): static
    {
        $this->repliedAt = $repliedAt;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): static
    {
        $this->review = $review;

        return $this;
    }
}
