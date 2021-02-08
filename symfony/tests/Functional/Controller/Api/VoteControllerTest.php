<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Entity\Vote\ReviewVote;
use App\Entity\Vote\Vote;
use App\Repository\ReviewRepository;
use App\Repository\VoteRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class VoteControllerTest extends WebTestCase
{
    use LoginUserTrait;

    /**
     * Test attempting to vote when not logged in returns 401.
     */
    public function testVote1(): void
    {
        $client = static::createClient();
        $entityId = $this->getAnyReviewId();

        $client->request(
            'POST',
            '/api/vote',
            [],
            [],
            [],
            '{"entityId":'.$entityId.',"entityName":"Review","positive":true,"googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * Test valid vote returns 201.
     */
    public function testVote2(): void
    {
        $client = static::createClient();
        $entityId = $this->getAnyReviewId();

        $loggedInUser = $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_EMAIL);

        $client->request(
            'POST',
            '/api/vote',
            [],
            [],
            [],
            '{"entityId":'.$entityId.',"entityName":"Review","positive":true,"googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $latestVote = $this->getLatestVote();

        $this->assertNotNull($latestVote);
        $this->assertInstanceOf(ReviewVote::class, $latestVote);
        $this->assertTrue($latestVote->isPositive());
        $this->assertEquals($loggedInUser, $latestVote->getUser());
    }

    private function getLatestVote(): ?Vote
    {
        /** @var VoteRepository $voteRepository */
        $voteRepository = static::$container->get(VoteRepository::class);

        return $voteRepository->findOneBy([], ['id' => 'DESC']);
    }

    private function getAnyReviewId(): int
    {
        /** @var ReviewRepository $reviewRepository */
        $reviewRepository = static::$container->get(ReviewRepository::class);

        return $reviewRepository->findLastPublished()->getId();
    }
}
