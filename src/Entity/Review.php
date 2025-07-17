<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $reviewId = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column(length: 50)]
    private ?string $platform = null;

    #[ORM\Column(length: 50)]
    private ?string $hotelId = null;

    #[ORM\Column(length: 100)]
    private ?string $guestName = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $submittedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $rating = null;

    #[ORM\Column(length: 10)]
    private ?string $language = null;

    /**
     * @var Collection<int, ReviewResponse>
     */
    #[ORM\OneToMany(
        targetEntity: ReviewResponse::class,
        mappedBy: 'review',
        cascade: ['persist'],
        orphanRemoval: true)
    ]
    private Collection $responses;

    public function __construct()
    {
        $this->responses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReviewId(): ?string
    {
        return $this->reviewId;
    }

    public function setReviewId(string $reviewId): static
    {
        $this->reviewId = $reviewId;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function setPlatform(string $platform): static
    {
        $this->platform = $platform;

        return $this;
    }

    public function getHotelId(): ?string
    {
        return $this->hotelId;
    }

    public function setHotelId(string $hotelId): static
    {
        $this->hotelId = $hotelId;

        return $this;
    }

    public function getGuestName(): ?string
    {
        return $this->guestName;
    }

    public function setGuestName(string $guestName): static
    {
        $this->guestName = $guestName;

        return $this;
    }

    public function getSubmittedAt(): ?\DateTimeImmutable
    {
        return $this->submittedAt;
    }

    public function setSubmittedAt(\DateTimeImmutable $submittedAt): static
    {
        $this->submittedAt = $submittedAt;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(string $language): static
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return Collection<int, ReviewResponse>
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    public function addResponse(ReviewResponse $response): static
    {
        if (!$this->responses->contains($response)) {
            $this->responses->add($response);
            $response->setReview($this);
        }

        return $this;
    }

    public function removeResponse(ReviewResponse $response): static
    {
        if ($this->responses->removeElement($response)) {
            // set the owning side to null (unless already changed)
            if ($response->getReview() === $this) {
                $response->setReview(null);
            }
        }

        return $this;
    }

    public function clearResponses(): static
    {
        $this->responses->clear();

        return $this;
    }
}
