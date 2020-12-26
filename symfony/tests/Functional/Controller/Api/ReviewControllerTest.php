<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Repository\PropertyRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ReviewControllerTest extends WebTestCase
{
    use LoginUserTrait;

    public function testSubmitReview(): void
    {
        $client = static::createClient();

        $loggedInUser = $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_EMAIL);

        $propertyRepository = static::$container->get(PropertyRepository::class);
        $property = $propertyRepository->findOneBy(['slug' => TestFixtures::TEST_PROPERTY_SLUG]);

        $client->request(
            'POST',
            '/api/submit-review',
            [],
            [],
            [],
            '{"propertySlug":"'.TestFixtures::TEST_PROPERTY_SLUG.'","reviewerName":"Jack Harper","reviewerEmail":"test.reviewer@starsol.co.uk","agencyName":"New Agency Company","agencyBranch":"Duxford","reviewTitle":"I lived here and it was adequate","reviewContent":"I lived here, the carpet was lovely.\n\nBut the front door was orange and I would have preferred purple.","overallStars":3,"agencyStars":4,"landlordStars":null,"propertyStars":3,"googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $reviewRepository = static::$container->get(ReviewRepository::class);
        $review = $reviewRepository->findOneBy([
            'author' => 'Jack Harper',
            'property' => $property,
        ]);
        $this->assertNotNull($review);
        $this->assertEquals('Jack Harper', $review->getAuthor());
        $this->assertEquals(TestFixtures::TEST_PROPERTY_SLUG, $review->getProperty()->getSlug());
        $this->assertEquals('New Agency Company', $review->getAgency()->getName());
        $this->assertEquals('Duxford', $review->getBranch()->getName());
        $this->assertEquals($loggedInUser, $review->getUser());
    }
}
