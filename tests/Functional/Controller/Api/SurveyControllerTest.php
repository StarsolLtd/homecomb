<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Repository\Survey\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class SurveyControllerTest extends WebTestCase
{
    public function testView(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/s/'.TestFixtures::TEST_SURVEY_SLUG);

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals(TestFixtures::TEST_SURVEY_SLUG, $content['slug']);
    }

    /**
     * Test answering returns HTTP_CREATED for valid request with content.
     */
    public function testAnswer1(): void
    {
        $client = static::createClient();
        $questionId = $this->getAnyQuestionId();

        $this->clientAnswerRequest($client, '{"questionId":'.$questionId.',"content":"I like the taste"}');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['success']);
    }

    /**
     * Test answering returns HTTP_CREATED for valid request with rating.
     */
    public function testAnswer2(): void
    {
        $client = static::createClient();
        $questionId = $this->getAnyQuestionId();

        $this->clientAnswerRequest($client, '{"questionId":'.$questionId.',"rating":4}');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertTrue($content['success']);
    }

    /**
     * Test answer returns HTTP_NOT_FOUND when question does not exist.
     */
    public function testAnswer3(): void
    {
        $client = static::createClient();

        $this->clientAnswerRequest($client, '{"questionId":99999999999,"content":"I like the taste"}');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    /**
     * Test answer returns HTTP_BAD_REQUEST when payload is malformed.
     */
    public function testAnswer4(): void
    {
        $client = static::createClient();

        $this->clientAnswerRequest($client, '{MALFORMED//}');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    private function clientAnswerRequest(KernelBrowser $client, string $content): void
    {
        $client->request(
            'POST',
            '/api/s/answer',
            [],
            [],
            [],
            $content
        );
    }

    private function getAnyQuestionId(): int
    {
        /** @var QuestionRepository $questionRepository */
        $questionRepository = static::$container->get(QuestionRepository::class);

        return $questionRepository->findLastPublished()->getId();
    }
}
