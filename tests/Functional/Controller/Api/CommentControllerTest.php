<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Repository\TenancyReviewRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CommentControllerTest extends WebTestCase
{
    use LoginUserTrait;

    public function testSubmitComment(): void
    {
        $client = static::createClient();
        $this->loginUser($client, TestFixtures::TEST_USER_AGENCY_1_ADMIN_EMAIL);

        $entityId = $this->getAnyReviewId();

        $this->makeValidSubmitCommentRequest($client, $entityId);

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider submitCommentReturnsForbiddenWhenUserDoesNotHavePermissionUserEmailDataProvider
     */
    public function testSubmitCommentReturnsForbiddenWhenUserDoesNotHavePermission(string $userEmail): void
    {
        $client = static::createClient();
        $this->loginUser($client, $userEmail);

        $entityId = $this->getAnyReviewId();

        $this->makeValidSubmitCommentRequest($client, $entityId);

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testSubmitCommentReturnsBadRequestWhenRelatedEntityUnsupported(): void
    {
        $client = static::createClient();
        $this->loginUser($client, TestFixtures::TEST_USER_AGENCY_1_ADMIN_EMAIL);

        $entityId = $this->getAnyReviewId();

        $client->request(
            'POST',
            '/api/comment',
            [],
            [],
            [],
            '{"entityId":'.$entityId.',"entityName":"Pancake","content":"Explanation","googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    public function testSubmitFlagReturnsBadRequestWhenContentMalformed(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/comment',
            [],
            [],
            [],
            '{"entityId_MALFORMED":1,"entityName":"Review","content":"Explanation","googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    public function submitCommentReturnsForbiddenWhenUserDoesNotHavePermissionUserEmailDataProvider(): array
    {
        return [
            [TestFixtures::TEST_USER_STANDARD_1_EMAIL],
            [TestFixtures::TEST_USER_AGENCY_2_ADMIN_EMAIL],
        ];
    }

    private function getAnyReviewId(): int
    {
        /** @var TenancyReviewRepository $reviewRepository */
        $reviewRepository = static::$container->get(TenancyReviewRepository::class);

        return $reviewRepository->findLastPublished()->getId();
    }

    private function makeValidSubmitCommentRequest(KernelBrowser $client, int $entityId): void
    {
        $client->request(
            'POST',
            '/api/comment',
            [],
            [],
            [],
            '{"entityId":'.$entityId.',"entityName":"Review","content":"Explanation","googleReCaptchaToken":"SAMPLE"}'
        );
    }
}
