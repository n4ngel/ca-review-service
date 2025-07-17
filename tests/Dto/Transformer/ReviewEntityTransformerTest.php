<?php

namespace App\Tests\Dto\Transformer;

use App\Dto\ResponseData;
use App\Dto\ReviewData;
use App\Dto\Transformer\ReviewEntityTransformer;
use App\Entity\Review;
use App\Entity\ReviewResponse;
use PHPUnit\Framework\TestCase;

class ReviewEntityTransformerTest extends TestCase
{
    private ReviewEntityTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new ReviewEntityTransformer();
    }

    public function testTransformWithNewReviewData(): void
    {
        $dto = new ReviewData(
            reviewId: 'review-123',
            status: 'published',
            platform: 'TripAdvisor',
            hotelId: 'hotel-001',
            guestName: 'John Doe',
            submittedAt: new \DateTimeImmutable('2025-07-01'),
            content: 'Great stay!',
            rating: 5,
            language: 'en',
            responses: [
                new ResponseData(
                    responseId: 'response-100',
                    responder: 'Manager',
                    repliedAt: new \DateTimeImmutable('2025-07-02'),
                    content: 'Thank you for your feedback!'
                )
            ]
        );

        $review = new Review();
        $transformed = $this->transformer->transform($dto, $review);

        $this->assertSame($dto->reviewId, $transformed->getReviewId());
        $this->assertSame($dto->status, $transformed->getStatus());
        $this->assertSame($dto->platform, $transformed->getPlatform());
        $this->assertSame($dto->hotelId, $transformed->getHotelId());
        $this->assertSame($dto->guestName, $transformed->getGuestName());
        $this->assertEquals($dto->submittedAt, $transformed->getSubmittedAt());
        $this->assertSame($dto->content, $transformed->getContent());
        $this->assertSame($dto->rating, $transformed->getRating());
        $this->assertSame($dto->language, $transformed->getLanguage());

        $responses = $transformed->getResponses();
        $this->assertCount(1, $responses);

        /** @var ReviewResponse $response */
        $response = $responses->first();
        $this->assertSame('response-100', $response->getResponseId());
        $this->assertSame('Manager', $response->getResponder());
        $this->assertEquals(new \DateTimeImmutable('2025-07-02'), $response->getRepliedAt());
        $this->assertSame('Thank you for your feedback!', $response->getContent());
    }

    public function testTransformWithUpdatedResponses(): void
    {
        $existingResponse = (new ReviewResponse())
            ->setResponseId('response-100')
            ->setResponder('Manager')
            ->setRepliedAt(new \DateTimeImmutable('2025-07-02'))
            ->setContent('Thank you for your feedback!');

        $review = (new Review())
            ->setReviewId('review-123')
            ->addResponse($existingResponse);

        $dto = new ReviewData(
            reviewId: 'review-123',
            status: 'approved',
            platform: 'Booking.com',
            hotelId: 'hotel-001',
            guestName: 'Jane Doe',
            submittedAt: new \DateTimeImmutable('2025-07-05'),
            content: 'Excellent service!',
            rating: 4,
            language: 'en',
            responses: [
                new ResponseData(
                    responseId: 'response-100',
                    responder: 'Admin',
                    repliedAt: new \DateTimeImmutable('2025-07-06'),
                    content: 'We appreciate your feedback!'
                )
            ]
        );

        $transformed = $this->transformer->transform($dto, $review);

        $this->assertSame($dto->status, $transformed->getStatus());
        $this->assertSame($dto->platform, $transformed->getPlatform());
        $this->assertSame($dto->guestName, $transformed->getGuestName());
        $this->assertEquals($dto->submittedAt, $transformed->getSubmittedAt());
        $this->assertSame($dto->content, $transformed->getContent());
        $this->assertSame($dto->rating, $transformed->getRating());
        $this->assertSame($dto->language, $transformed->getLanguage());

        $responses = $transformed->getResponses();
        $this->assertCount(1, $responses);

        /** @var ReviewResponse $response */
        $response = $responses->first();
        $this->assertSame('response-100', $response->getResponseId());
        $this->assertSame('Admin', $response->getResponder());
        $this->assertEquals(new \DateTimeImmutable('2025-07-06'), $response->getRepliedAt());
        $this->assertSame('We appreciate your feedback!', $response->getContent());
    }

    public function testTransformWithRemovedResponses(): void
    {
        $existingResponse1 = (new ReviewResponse())
            ->setResponseId('response-100')
            ->setResponder('Manager')
            ->setRepliedAt(new \DateTimeImmutable('2025-07-02'))
            ->setContent('Thank you for your feedback!');

        $existingResponse2 = (new ReviewResponse())
            ->setResponseId('response-101')
            ->setResponder('Staff')
            ->setRepliedAt(new \DateTimeImmutable('2025-07-03'))
            ->setContent('We are glad you enjoyed your stay.');

        $review = (new Review())
            ->setReviewId('review-123')
            ->addResponse($existingResponse1)
            ->addResponse($existingResponse2);

        $dto = new ReviewData(
            reviewId: 'review-123',
            status: 'approved',
            platform: 'Expedia',
            hotelId: 'hotel-002',
            guestName: 'Mike Smith',
            submittedAt: new \DateTimeImmutable('2025-07-10'),
            content: 'Amazing experience!',
            rating: 5,
            language: 'en',
            responses: [
                new ResponseData(
                    responseId: 'response-101',
                    responder: 'Staff',
                    repliedAt: new \DateTimeImmutable('2025-07-12'),
                    content: 'Thank you for your kind words!'
                )
            ]
        );

        $transformed = $this->transformer->transform($dto, $review);

        $responses = $transformed->getResponses();
        $this->assertCount(1, $responses);

        /** @var ReviewResponse $response */
        $response = $responses->first();
        $this->assertSame('response-101', $response->getResponseId());
        $this->assertSame('Staff', $response->getResponder());
        $this->assertEquals(new \DateTimeImmutable('2025-07-12'), $response->getRepliedAt());
        $this->assertSame('Thank you for your kind words!', $response->getContent());
    }
}
