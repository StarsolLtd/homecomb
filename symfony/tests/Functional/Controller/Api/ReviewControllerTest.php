<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Entity\Review;
use App\Model\SubmitReviewInput;
use App\Repository\PropertyRepository;
use App\Repository\ReviewRepository;
use App\Repository\ReviewSolicitationRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ReviewControllerTest extends WebTestCase
{
    use LoginUserTrait;

    private SerializerInterface $serializer;

    public function setUp(): void
    {
        $this->serializer = new Serializer([new GetSetMethodNormalizer()], [new JsonEncoder()]);
    }

    public function submitReviewWithLoggedInUserSuccessContentDataProvider(): array
    {
        return [
            ['{"propertySlug":"'.TestFixtures::TEST_PROPERTY_SLUG.'","code":null,"reviewerName":"Jack Harper","reviewerEmail":"test.reviewer.1@starsol.co.uk","agencyName":"New Agency Company","agencyBranch":"Duxford","reviewTitle":"I lived here and it was adequate","reviewContent":"I lived here, the carpet was lovely.\n\nBut the front door was orange and I would have preferred purple.","overallStars":3,"agencyStars":4,"landlordStars":null,"propertyStars":3,"googleReCaptchaToken":"SAMPLE"}'],
            ['{"propertySlug":"'.TestFixtures::TEST_PROPERTY_SLUG.'","code":"'.TestFixtures::TEST_REVIEW_SOLICITATION_CODE.'","reviewerName":"Jack H.","reviewerEmail":"test.reviewer.2@starsol.co.uk","agencyName":"","agencyBranch":"","reviewTitle":"I lived here and it was adequate","reviewContent":"I lived here, the carpet was lovely.\n\nBut the front door was orange and I would have preferred purple.","overallStars":null,"agencyStars":null,"landlordStars":null,"propertyStars":null,"googleReCaptchaToken":"SAMPLE"}'],
            ['{"propertySlug":"'.TestFixtures::TEST_PROPERTY_SLUG.'","reviewerName":"Jack P.","reviewerEmail":"test.reviewer.3@starsol.co.uk","agencyName":null,"agencyBranch":null,"reviewTitle":"I lived here and it was adequate","reviewContent":"I lived here, the carpet was lovely.\n\nBut the front door was orange and I would have preferred purple.","overallStars":null,"agencyStars":1,"landlordStars":4,"propertyStars":null,"googleReCaptchaToken":"SAMPLE"}'],
            ['{"propertySlug":"'.TestFixtures::TEST_PROPERTY_SLUG.'","reviewerName":"JP","reviewerEmail":"test.reviewer.4@starsol.co.uk","reviewTitle":"I lived here and it was adequate","reviewContent":"I lived here, the carpet was lovely.\n\nBut the front door was orange and I would have preferred purple.","googleReCaptchaToken":"SAMPLE"}'],
            ['{"propertySlug":"'.TestFixtures::TEST_PROPERTY_SLUG.'","reviewerName":"JH","reviewerEmail":"jack@starsol.co.uk","agencyName":"Fresh Agency No Branch","agencyBranch":"","reviewTitle":"Test","reviewContent":"Testing","overallStars":null,"landlordStars":3,"agencyStars":null,"propertyStars":null,"captchaToken":"SAMPLE"}'],
            ['{"unusedAdditionalField": "Gouda","propertySlug":"'.TestFixtures::TEST_PROPERTY_SLUG.'","reviewerName":"Jack Harper","reviewerEmail":"test.reviewer.1@starsol.co.uk","agencyName":"New Agency Company","agencyBranch":"Duxford","reviewTitle":"I lived here and it was adequate","reviewContent":"I lived here, the carpet was lovely.\n\nBut the front door was orange and I would have preferred purple.","overallStars":3,"agencyStars":4,"landlordStars":null,"propertyStars":3,"googleReCaptchaToken":"SAMPLE"}'],
        ];
    }

    /**
     * @dataProvider submitReviewWithLoggedInUserSuccessContentDataProvider
     */
    public function testSubmitReviewWithLoggedInUserSuccess(string $content): void
    {
        $client = static::createClient();

        $loggedInUser = $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_EMAIL);

        $client->request(
            'POST',
            '/api/submit-review',
            [],
            [],
            [],
            $content
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        /** @var SubmitReviewInput $input */
        $input = $this->serializer->deserialize($content, SubmitReviewInput::class, 'json');

        /** @var PropertyRepository $propertyRepository */
        $propertyRepository = static::$container->get(PropertyRepository::class);
        $property = $propertyRepository->findOneBySlugOrNull($input->getPropertySlug());

        /** @var ReviewRepository $reviewRepository */
        $reviewRepository = static::$container->get(ReviewRepository::class);
        $review = $reviewRepository->findLastByPropertyAndAuthorOrNull($property, $input->getReviewerName());

        $this->assertNotNull($review);
        $this->assertEquals($input->getReviewerName(), $review->getAuthor());
        $this->assertEquals($input->getPropertySlug(), $review->getProperty()->getSlug());
        if ($input->getAgencyName() && $input->getAgencyBranch()) {
            $this->assertEquals($input->getAgencyName(), $review->getAgency()->getName());
        } else {
            $this->assertNull($review->getAgency());
        }
        if ($input->getAgencyBranch()) {
            $this->assertEquals($input->getAgencyBranch(), $review->getBranch()->getName());
        } else {
            $this->assertNull($review->getBranch());
        }
        $this->assertEquals($loggedInUser, $review->getUser());

        $this->assertReviewMatchesReviewSolicitationWhenCodeNotNull($input->getCode(), $review);
    }

    public function testSubmitReviewReturnsBadRequestWhenContentMalformed(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/submit-review',
            [],
            [],
            [],
            '{"propertySlug_MALFORMED":"XXX","code":null,"reviewerName":"Jack Harper","reviewerEmail":"test.reviewer.1@starsol.co.uk","agencyName":"New Agency Company","agencyBranch":"Duxford","reviewTitle":"I lived here and it was adequate","reviewContent":"I lived here, the carpet was lovely.\n\nBut the front door was orange and I would have preferred purple.","overallStars":3,"agencyStars":4,"landlordStars":null,"propertyStars":3,"googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    private function assertReviewMatchesReviewSolicitationWhenCodeNotNull(?string $code, Review $review): void
    {
        if (null === $code) {
            return;
        }
        /** @var ReviewSolicitationRepository $rsRepository */
        $rsRepository = static::$container->get(ReviewSolicitationRepository::class);
        $rs = $rsRepository->findOneByCodeOrNull($code);
        $this->assertEquals($review, $rs->getReview());
    }
}
