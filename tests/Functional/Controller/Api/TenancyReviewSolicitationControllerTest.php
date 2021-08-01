<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Model\TenancyReviewSolicitation\View;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class TenancyReviewSolicitationControllerTest extends WebTestCase
{
    private SerializerInterface $serializer;

    public function setUp(): void
    {
        $this->serializer = new Serializer([new GetSetMethodNormalizer()], [new JsonEncoder()]);
    }

    public function testView(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/rs/'.TestFixtures::TEST_REVIEW_SOLICITATION_CODE);

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        /** @var View $view */
        $view = $this->serializer->deserialize($response->getContent(), View::class, 'json');

        $this->assertEquals(TestFixtures::TEST_REVIEW_SOLICITATION_CODE, $view->getCode());
        $this->assertEquals('Anna', $view->getReviewerFirstName());
        $this->assertEquals('Testinova', $view->getReviewerLastName());
        $this->assertEquals('anna.testinova@starsol.co.uk', $view->getReviewerEmail());
    }
}
