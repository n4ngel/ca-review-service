<?php

namespace App\Tests\Serializer;

use App\Dto\ReviewData;
use App\Dto\ReviewList;
use App\Serializer\ReviewDeserializer;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ReviewDeserializerTest extends TestCase
{
    private SerializerInterface $serializerMock;
    private ValidatorInterface $validatorMock;
    private LoggerInterface $loggerMock;
    private ReviewDeserializer $reviewDeserializer;

    protected function setUp(): void
    {
        $this->serializerMock = $this->createMock(SerializerInterface::class);
        $this->validatorMock = $this->createMock(ValidatorInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->reviewDeserializer = new ReviewDeserializer(
            $this->serializerMock,
            $this->validatorMock,
            $this->loggerMock
        );
    }

    public function testFromJsonReturnsValidReviews(): void
    {
        $json = '{
            "review_list":[
                {
                    "reviewId":"1",
                    "status":"published",
                    "platform":"website",
                    "hotelId":"101",
                    "guestName":"John Doe",
                    "submittedAt":"2023-06-15T00:00:00+00:00",
                    "content":"Great stay!",
                    "rating":5,
                    "language":"en",
                    "responses":[]
                },
                {
                    "reviewId":"2",
                    "status":"published",
                    "platform":"website",
                    "hotelId":"101",
                    "guestName":"Jane Roe",
                    "submittedAt":"2023-06-15T00:00:00+00:00",
                    "content":"Good service.",
                    "rating":4,
                    "language":"en",
                    "responses":[]
                }
            ]
        }';

        $review1 = new ReviewData(
            reviewId: '1',
            status: 'published',
            platform: 'website',
            hotelId: '101',
            guestName: 'John Doe',
            submittedAt: new \DateTimeImmutable('2023-06-15T00:00:00+00:00'),
            content: 'Great stay!',
            rating: 5,
            language: 'en',
            responses: []
        );

        $review2 = new ReviewData(
            reviewId: '2',
            status: 'published',
            platform: 'website',
            hotelId: '101',
            guestName: 'Jane Roe',
            submittedAt: new \DateTimeImmutable('2023-06-15T00:00:00+00:00'),
            content: 'Good service.',
            rating: 4,
            language: 'en',
            responses: []
        );

        $reviewList = new ReviewList([$review1, $review2]);

        $this->serializerMock
            ->method('deserialize')
            ->with($json, ReviewList::class, 'json')
            ->willReturn($reviewList);

        $this->validatorMock
            ->method('validate')
            ->willReturn(new ConstraintViolationList());

        $result = $this->reviewDeserializer->fromJson($json);

        $this->assertCount(2, $result);
        $this->assertSame($review1, $result[0]);
        $this->assertSame($review2, $result[1]);
    }

    public function testFromJsonSkipsInvalidReviews(): void
    {
        $json = '{
            "review_list":[
                {
                    "reviewId":"1",
                    "status":"published",
                    "platform":"website",
                    "hotelId":"101",
                    "guestName":"John Doe",
                    "submittedAt":"2023-06-15T00:00:00+00:00",
                    "content":"Great stay!",
                    "rating":5,
                    "language":"en",
                    "responses":[]
                },
                {
                    "reviewId":"2",
                    "status":"published",
                    "platform":"website",
                    "hotelId":"101",
                    "guestName":"Jane Roe",
                    "submittedAt":"2023-06-15T00:00:00+00:00",
                    "content":"Good service.",
                    "rating":4,
                    "language":"en",
                    "responses":[]
                }
            ]
        }';

        $review1 = new ReviewData(
            reviewId: '1',
            status: 'published',
            platform: 'website',
            hotelId: '101',
            guestName: 'John Doe',
            submittedAt: new \DateTimeImmutable('2023-06-15T00:00:00+00:00'),
            content: 'Great stay!',
            rating: 5,
            language: 'en',
            responses: []
        );

        $review2 = new ReviewData(
            reviewId: '2',
            status: 'published',
            platform: 'website',
            hotelId: '101',
            guestName: 'Jane Roe',
            submittedAt: new \DateTimeImmutable('2023-06-15T00:00:00+00:00'),
            content: 'Good service.',
            rating: 4,
            language: 'en',
            responses: []
        );

        $reviewList = new ReviewList([$review1, $review2]);

        $violation = $this->createMock(ConstraintViolation::class);
        $violation->method('getPropertyPath')->willReturn('rating');
        $violation->method('getInvalidValue')->willReturn(null);
        $violation->method('getMessage')->willReturn('This value should not be null.');

        $this->serializerMock
            ->method('deserialize')
            ->with($json, ReviewList::class, 'json')
            ->willReturn($reviewList);

        $this->validatorMock
            ->method('validate')
            ->willReturnOnConsecutiveCalls(
                new ConstraintViolationList(),
                new ConstraintViolationList([$violation])
            );

        $this->loggerMock
            ->expects($this->once())
            ->method('warning');

        $result = $this->reviewDeserializer->fromJson($json);

        $this->assertCount(1, $result);
        $this->assertSame($review1, $result[0]);
    }

    public function testFromJsonThrowsOnInvalidJson(): void
    {
        $this->expectException(NotEncodableValueException::class);

        $invalidJson = '{invalid}';

        $this->serializerMock
            ->method('deserialize')
            ->with($invalidJson, ReviewList::class, 'json')
            ->willThrowException(new NotEncodableValueException());

        $this->reviewDeserializer->fromJson($invalidJson);
    }
}
