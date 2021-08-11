<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Entity\Vote\CommentVote;
use App\Entity\Vote\TenancyReviewVote;
use App\Entity\Vote\Vote;
use App\Repository\CommentRepository;
use App\Repository\TenancyReviewRepository;
use App\Repository\VoteRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class VoteControllerTest extends WebTestCase
{
    use LoginUserTrait;

    /**
     * Test attempting to vote when not logged in returns HTTP_UNAUTHORIZED.
     */
    public function testVote1(): void
    {
        $client = static::createClient();
        $entityId = $this->getAnyReviewId();

        $this->clientVoteRequest($client, '{"entityId":'.$entityId.',"entityName":"TenancyReview","positive":true,"googleReCaptchaToken":"SAMPLE"}');

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $client->getResponse()->getStatusCode());
    }

    /**
     * Test vote returns HTTP_BAD_REQUEST when payload is malformed.
     */
    public function testVote2(): void
    {
        $client = static::createClient();

        $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_1_EMAIL);

        $this->clientVoteRequest($client, '{MALFORMED//}');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    /**
     * Test valid review vote returns HTTP_CREATED.
     */
    public function testVote3(): void
    {
        $client = static::createClient();
        $entityId = $this->getAnyReviewId();

        $loggedInUser = $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_1_EMAIL);

        $this->clientVoteRequest($client, '{"entityId":'.$entityId.',"entityName":"TenancyReview","positive":true,"googleReCaptchaToken":"SAMPLE"}');

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $latestVote = $this->getLatestVote();

        $this->assertNotNull($latestVote);
        $this->assertInstanceOf(TenancyReviewVote::class, $latestVote);
        $this->assertTrue($latestVote->isPositive());
        $this->assertEquals($loggedInUser, $latestVote->getUser());
    }

    /**
     * Test valid comment vote returns HTTP_CREATED.
     */
    public function testVote4(): void
    {
        $client = static::createClient();
        $entityId = $this->getAnyCommentId();

        $loggedInUser = $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_1_EMAIL);

        $this->clientVoteRequest($client, '{"entityId":'.$entityId.',"entityName":"Comment","positive":false,"googleReCaptchaToken":"SAMPLE"}');

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $latestVote = $this->getLatestVote();

        $this->assertNotNull($latestVote);
        $this->assertInstanceOf(CommentVote::class, $latestVote);
        $this->assertFalse($latestVote->isPositive());
        $this->assertEquals($loggedInUser, $latestVote->getUser());
    }

    private function clientVoteRequest(KernelBrowser $client, string $content): void
    {
        $client->request(
            'POST',
            '/api/vote',
            [],
            [],
            [],
            $content
        );
    }

    private function getLatestVote(): ?Vote
    {
        /** @var VoteRepository $voteRepository */
        $voteRepository = static::$container->get(VoteRepository::class);

        return $voteRepository->findOneBy([], ['id' => 'DESC']);
    }

    private function getAnyReviewId(): int
    {
        /** @var TenancyReviewRepository $tenancyReviewRepository */
        $tenancyReviewRepository = static::$container->get(TenancyReviewRepository::class);

        return $tenancyReviewRepository->findLastPublished()->getId();
    }

    private function getAnyCommentId(): int
    {
        /** @var CommentRepository $commentRepository */
        $commentRepository = static::$container->get(CommentRepository::class);

        return $commentRepository->findLastPublished()->getId();
    }
}
