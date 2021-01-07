<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Entity\Flag\Flag;
use App\Entity\Flag\ReviewFlag;
use App\Repository\FlagRepository;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FlagControllerTest extends WebTestCase
{
    use LoginUserTrait;

    public function testSubmitFlagNotLoggedIn(): void
    {
        $client = static::createClient();
        $entityId = $this->getAnyReviewId();

        $client->request(
            'POST',
            '/api/flag',
            [],
            [],
            [],
            '{"entityId":'.$entityId.',"entityName":"Review","content":"Explanation","googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $latestFlag = $this->getLatestFlag();

        $this->assertNotNull($latestFlag);
        $this->assertInstanceOf(ReviewFlag::class, $latestFlag);
        $this->assertEquals('Explanation', $latestFlag->getContent());
        $this->assertNull($latestFlag->getUser());
    }

    public function testSubmitFlagAsLoggedInUser(): void
    {
        $client = static::createClient();
        $entityId = $this->getAnyReviewId();

        $loggedInUser = $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_EMAIL);

        $client->request(
            'POST',
            '/api/flag',
            [],
            [],
            [],
            '{"entityId":'.$entityId.',"entityName":"Review","content":"Explanation","googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $latestFlag = $this->getLatestFlag();

        $this->assertNotNull($latestFlag);
        $this->assertInstanceOf(ReviewFlag::class, $latestFlag);
        $this->assertEquals('Explanation', $latestFlag->getContent());
        $this->assertEquals($loggedInUser, $latestFlag->getUser());
    }

    public function testSubmitFlagReturnsBadRequestWhenContentMalformed(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/flag',
            [],
            [],
            [],
            '{"entityId_MALFORMED":1,"entityName":"Review","content":"Explanation","googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    private function getLatestFlag(): ?Flag
    {
        /** @var FlagRepository $flagRepository */
        $flagRepository = static::$container->get(FlagRepository::class);

        return $flagRepository->findOneBy([], ['id' => 'DESC']);
    }

    private function getAnyReviewId(): int
    {
        /** @var ReviewRepository $reviewRepository */
        $reviewRepository = static::$container->get(ReviewRepository::class);

        return $reviewRepository->findLastPublished()->getId();
    }
}
