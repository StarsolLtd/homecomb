<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Model\SubmitReviewInput;
use App\Repository\PropertyRepository;
use App\Repository\ReviewRepository;
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
            ['{"propertySlug":"'.TestFixtures::TEST_PROPERTY_SLUG.'","reviewerName":"Jack Harper","reviewerEmail":"test.reviewer.1@starsol.co.uk","agencyName":"New Agency Company","agencyBranch":"Duxford","reviewTitle":"I lived here and it was adequate","reviewContent":"I lived here, the carpet was lovely.\n\nBut the front door was orange and I would have preferred purple.","overallStars":3,"agencyStars":4,"landlordStars":null,"propertyStars":3,"googleReCaptchaToken":"SAMPLE"}'],
            ['{"propertySlug":"'.TestFixtures::TEST_PROPERTY_SLUG.'","reviewerName":"Jack H.","reviewerEmail":"test.reviewer.2@starsol.co.uk","agencyName":"","agencyBranch":"","reviewTitle":"I lived here and it was adequate","reviewContent":"I lived here, the carpet was lovely.\n\nBut the front door was orange and I would have preferred purple.","overallStars":null,"agencyStars":null,"landlordStars":null,"propertyStars":null,"googleReCaptchaToken":"SAMPLE"}'],
            ['{"propertySlug":"'.TestFixtures::TEST_PROPERTY_SLUG.'","reviewerName":"Jack P.","reviewerEmail":"test.reviewer.3@starsol.co.uk","agencyName":null,"agencyBranch":null,"reviewTitle":"I lived here and it was adequate","reviewContent":"I lived here, the carpet was lovely.\n\nBut the front door was orange and I would have preferred purple.","overallStars":null,"agencyStars":1,"landlordStars":4,"propertyStars":null,"googleReCaptchaToken":"SAMPLE"}'],
            ['{"propertySlug":"'.TestFixtures::TEST_PROPERTY_SLUG.'","reviewerName":"JP","reviewerEmail":"test.reviewer.4@starsol.co.uk","reviewTitle":"I lived here and it was adequate","reviewContent":"I lived here, the carpet was lovely.\n\nBut the front door was orange and I would have preferred purple.","googleReCaptchaToken":"SAMPLE"}'],
            ['{"propertySlug":"'.TestFixtures::TEST_PROPERTY_SLUG.'","reviewerName":"JH","reviewerEmail":"jack@starsol.co.uk","agencyName":"Fresh Agency No Branch","agencyBranch":"","reviewTitle":"Test","reviewContent":"Testing","overallStars":null,"landlordStars":3,"agencyStars":null,"propertyStars":null,"captchaToken":"SAMPLE"}'],
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

        $propertyRepository = static::$container->get(PropertyRepository::class);
        $property = $propertyRepository->findOneBy(['slug' => $input->getPropertySlug()]);

        $reviewRepository = static::$container->get(ReviewRepository::class);
        $review = $reviewRepository->findOneBy(
            [
                'author' => $input->getReviewerName(),
                'property' => $property,
            ],
            [
                'id' => 'DESC',
            ]
        );
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
    }
}
