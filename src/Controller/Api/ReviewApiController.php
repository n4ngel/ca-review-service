<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Dto\ReviewListQuery;
use App\Dto\ReviewList;
use App\Repository\ReviewRepository;
use App\Dto\Transformer\ReviewDtoTransformerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/reviews', name: 'api_reviews_list', methods: ['GET'])]
final class ReviewApiController extends AbstractController
{
    public function __construct(
        private readonly ReviewRepository               $reviews,
        private readonly ReviewDtoTransformerInterface  $transformer,
        private readonly ValidatorInterface             $validator,
        private readonly SerializerInterface            $serializer,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $reviewListQuery = new ReviewListQuery();
        $reviewListQuery->submittedAfter  = $request->query->get('submitted_after');
        $reviewListQuery->submittedBefore = $request->query->get('submitted_before');
        $reviewListQuery->hotelId         = $request->query->get('hotel_id');

        $violations = $this->validator->validate($reviewListQuery);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $v) {
                $errors[] = sprintf('%s: %s', $v->getPropertyPath(), $v->getMessage());
            }
            return $this->json(
                ['error' => ['code'=>'INVALID_PARAMS','message'=>'Invalid query parameters'],'data'=>$errors],
                400
            );
        }

        $after  = $reviewListQuery->submittedAfter  ? new \DateTimeImmutable($reviewListQuery->submittedAfter)  : null;
        $before = $reviewListQuery->submittedBefore ? new \DateTimeImmutable($reviewListQuery->submittedBefore) : null;

        $entities = $this->reviews->findPublishedInDateRange($after, $before, $reviewListQuery->hotelId);

        $transformedReviews = array_map([$this->transformer, 'transform'], $entities);

        $reviewList = new ReviewList($transformedReviews);
        $json    = $this->serializer->serialize($reviewList, 'json');

        return new JsonResponse($json, 200, [], true);
    }
}
