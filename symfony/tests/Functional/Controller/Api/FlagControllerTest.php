<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Entity\Flag;
use App\Repository\FlagRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FlagControllerTest extends WebTestCase
{
    use LoginUserTrait;

    public function testSubmitFlagNotLoggedIn(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/flag',
            [],
            [],
            [],
            '{"entityId":1,"entityName":"Review","content":"Explanation","googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $latestFlag = $this->getLatestFlag();

        $this->assertNotNull($latestFlag);
        $this->assertEquals('Review', $latestFlag->getEntityName());
        $this->assertEquals('Explanation', $latestFlag->getContent());
        $this->assertNull($latestFlag->getUser());
    }

    public function testSubmitFlagAsLoggedInUser(): void
    {
        $client = static::createClient();

        $loggedInUser = $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_EMAIL);

        $client->request(
            'POST',
            '/api/flag',
            [],
            [],
            [],
            '{"entityId":1,"entityName":"Review","content":"Explanation","googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $latestFlag = $this->getLatestFlag();

        $this->assertNotNull($latestFlag);
        $this->assertEquals('Review', $latestFlag->getEntityName());
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
        $flagRepository = static::$container->get(FlagRepository::class);

        return $flagRepository->findOneBy([], ['id' => 'DESC']);
    }
}
