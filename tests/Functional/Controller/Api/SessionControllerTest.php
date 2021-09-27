<?php

namespace App\Tests\Functional\Controller\Api;

use App\Model\Session\FlashMessagesView;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class SessionControllerTest extends WebTestCase
{
    private SerializerInterface $serializer;

    public function setUp(): void
    {
        $this->serializer = new Serializer([new GetSetMethodNormalizer()], [new JsonEncoder()]);
    }

    public function testGetAndClearFlashBagWhenEmpty(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/session/flash');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        /** @var FlashMessagesView $flashMessagesView */
        $flashMessagesView = $this->serializer->deserialize($response->getContent(), FlashMessagesView::class, 'json');

        $this->assertCount(0, $flashMessagesView->getMessages());

        dump($flashMessagesView);
    }

    public function testGetAndClearFlashBag(): void
    {
        $client = static::createClient();

        $session = new Session(new MockFileSessionStorage());

        $session->getFlashBag()->add('notice', 'Your alligator is not paid enough.');
        $session->getFlashBag()->add('notice', 'Your alligator keeper quit her job.');
        $session->getFlashBag()->add('warning', 'Your alligators are hungry.');
        $session->getFlashBag()->add('error', 'You have been eaten by your alligators.');

        $client->getContainer()->set('session', $session);

        $client->request('GET', '/api/session/flash');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $flashMessagesView = $this->serializer->deserialize($response->getContent(), FlashMessagesView::class, 'json');

        $this->assertCount(4, $flashMessagesView->getMessages());
    }
}
