<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Entity\TenancyReview;
use App\Model\TenancyReview\SubmitInput;
use App\Repository\PropertyRepository;
use App\Repository\TenancyReviewRepository;
use App\Repository\TenancyReviewSolicitationRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class TenancyReviewControllerTest extends WebTestCase
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
            ['{"propertySlug":"'.TestFixtures::TEST_PROPERTY_1_SLUG.'","code":null,"reviewerName":"Jack Harper","reviewerEmail":"test.reviewer.1@starsol.co.uk","agencyName":"New Agency Company","agencyBranch":"Duxford","reviewTitle":"I lived here and it was adequate","reviewContent":"I lived here, the carpet was lovely.\n\nBut the front door was orange and I would have preferred purple.","overallStars":3,"agencyStars":4,"landlordStars":null,"propertyStars":3,"googleReCaptchaToken":"SAMPLE"}'],
            ['{"propertySlug":"'.TestFixtures::TEST_PROPERTY_1_SLUG.'","code":"'.TestFixtures::TEST_REVIEW_SOLICITATION_CODE.'","reviewerName":"Jack H.","reviewerEmail":"test.reviewer.2@starsol.co.uk","agencyName":"","agencyBranch":"","reviewTitle":"I lived here and it was adequate","reviewContent":"I lived here, the carpet was lovely.\n\nBut the front door was orange and I would have preferred purple.","overallStars":null,"agencyStars":null,"landlordStars":null,"propertyStars":null,"googleReCaptchaToken":"SAMPLE"}'],
            ['{"propertySlug":"'.TestFixtures::TEST_PROPERTY_1_SLUG.'","reviewerName":"Jack P.","reviewerEmail":"test.reviewer.3@starsol.co.uk","agencyName":null,"agencyBranch":null,"reviewTitle":"I lived here and it was adequate","reviewContent":"I lived here, the carpet was lovely.\n\nBut the front door was orange and I would have preferred purple.","overallStars":null,"agencyStars":1,"landlordStars":4,"propertyStars":null,"googleReCaptchaToken":"SAMPLE"}'],
            ['{"propertySlug":"'.TestFixtures::TEST_PROPERTY_1_SLUG.'","reviewerName":"JP","reviewerEmail":"test.reviewer.4@starsol.co.uk","reviewTitle":"I lived here and it was adequate","reviewContent":"I lived here, the carpet was lovely.\n\nBut the front door was orange and I would have preferred purple.","googleReCaptchaToken":"SAMPLE"}'],
            ['{"propertySlug":"'.TestFixtures::TEST_PROPERTY_1_SLUG.'","reviewerName":"JH","reviewerEmail":"jack@starsol.co.uk","agencyName":"Fresh Agency No Branch","agencyBranch":"","reviewTitle":"Test","reviewContent":"Testing","overallStars":null,"landlordStars":3,"agencyStars":null,"propertyStars":null,"captchaToken":"SAMPLE"}'],
            ['{"unusedAdditionalField": "Gouda","propertySlug":"'.TestFixtures::TEST_PROPERTY_1_SLUG.'","reviewerName":"Jack Harper","reviewerEmail":"test.reviewer.1@starsol.co.uk","agencyName":"New Agency Company","agencyBranch":"Duxford","reviewTitle":"I lived here and it was adequate","reviewContent":"I lived here, the carpet was lovely.\n\nBut the front door was orange and I would have preferred purple.","overallStars":3,"agencyStars":4,"landlordStars":null,"propertyStars":3,"googleReCaptchaToken":"SAMPLE"}'],
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

        /** @var SubmitInput $input */
        $input = $this->serializer->deserialize($content, SubmitInput::class, 'json');

        /** @var PropertyRepository $propertyRepository */
        $propertyRepository = static::$container->get(PropertyRepository::class);
        $property = $propertyRepository->findOneBySlugOrNull($input->getPropertySlug());

        /** @var TenancyReviewRepository $tenancyReviewRepository */
        $tenancyReviewRepository = static::$container->get(TenancyReviewRepository::class);
        $tenancyReview = $tenancyReviewRepository->findLastByPropertyAndAuthorOrNull($property, $input->getReviewerName());

        $this->assertNotNull($tenancyReview);
        $this->assertEquals($input->getReviewerName(), $tenancyReview->getAuthor());
        $this->assertEquals($input->getPropertySlug(), $tenancyReview->getProperty()->getSlug());
        if ($input->getAgencyName() && $input->getAgencyBranch()) {
            $this->assertEquals($input->getAgencyName(), $tenancyReview->getAgency()->getName());
        } else {
            $this->assertNull($tenancyReview->getAgency());
        }
        if ($input->getAgencyBranch()) {
            $this->assertEquals($input->getAgencyBranch(), $tenancyReview->getBranch()->getName());
        } else {
            $this->assertNull($tenancyReview->getBranch());
        }
        $this->assertEquals($loggedInUser, $tenancyReview->getUser());

        $this->assertReviewMatchesReviewSolicitationWhenCodeNotNull($input->getCode(), $tenancyReview);
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

    /**
     * Test the endpoint to get a view returns a HTTP_OK.
     */
    public function testGetViewById1(): void
    {
        $client = static::createClient();

        $tenancyReviewId = $this->getAnyPublishedReviewId();

        $client->request('GET', '/api/review/'.$tenancyReviewId);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /**
     * Test the endpoint to get a view returns a HTTP_NOT_FOUND when ID does not exist.
     */
    public function testGetViewById2(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/review/9999999999');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    /**
     * Test the endpoint to get the latest reviews returns a HTTP_OK.
     */
    public function testLatest1(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/review/latest');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    private function assertReviewMatchesReviewSolicitationWhenCodeNotNull(?string $code, TenancyReview $review): void
    {
        if (null === $code) {
            return;
        }
        /** @var TenancyReviewSolicitationRepository $rsRepository */
        $rsRepository = static::$container->get(TenancyReviewSolicitationRepository::class);
        $rs = $rsRepository->findOneByCodeOrNull($code);
        $this->assertEquals($review, $rs->getTenancyReview());
    }

    private function getAnyPublishedReviewId(): int
    {
        /** @var TenancyReviewRepository $reviewRepository */
        $reviewRepository = static::$container->get(TenancyReviewRepository::class);

        return $reviewRepository->findLastPublished()->getId();
    }
}
