<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class ReviewControllerTest extends WebTestCase
{
    use LoginUserTrait;

    public function testSubmitLocaleReview1(): void
    {
        $client = static::createClient();
        $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_1_EMAIL);

        $client->request(
            'POST',
            '/api/review/locale',
            [],
            [],
            [],
            '{"localeSlug":"'.TestFixtures::TEST_LOCALE_SLUG.'","code":"test","reviewerName":"Fiona","reviewerEmail":"fiona@starsol.co.uk","reviewTitle":"Test","reviewContent":"Test","overallStars":3,"captchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }

    /**
     * Test returns HTTP_NOT_FOUND when locale does not exist.
     */
    public function testSubmitLocaleReview2(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/review/locale',
            [],
            [],
            [],
            '{"localeSlug":"DOES-NOT-EXIST","reviewerName":"Fiona","reviewerEmail":"fiona@starsol.co.uk","reviewTitle":"Test","reviewContent":"Test","overallStars":3,"googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    /**
     * Test returns HTTP_BAD_REQUEST when malformed.
     */
    public function testSubmitLocaleReview3(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/review/locale',
            [],
            [],
            [],
            '{"localeSlug_MALFORMED":"'.TestFixtures::TEST_LOCALE_SLUG.'","reviewerName":"Fiona","reviewerEmail":"fiona@starsol.co.uk","googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }
}
