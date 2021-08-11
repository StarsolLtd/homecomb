<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Model\User\Flat;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class UserControllerTest extends WebTestCase
{
    use LoginUserTrait;

    private SerializerInterface $serializer;

    public function setUp(): void
    {
        $this->serializer = new Serializer([new GetSetMethodNormalizer()], [new JsonEncoder()]);
    }

    public function testGetUserFlatModelWhenUserLoggedIn(): void
    {
        $client = static::createClient();

        $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_1_EMAIL);

        $client->request('GET', '/api/user');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        /** @var Flat $userModel */
        $userModel = $this->serializer->deserialize($response->getContent(), Flat::class, 'json');

        $this->assertEquals(TestFixtures::TEST_USER_STANDARD_1_EMAIL, $userModel->getUsername());
        $this->assertEquals('Mr', $userModel->getTitle());
        $this->assertEquals('Terry', $userModel->getFirstName());
        $this->assertEquals('Sterling', $userModel->getLastName());
    }

    public function testGetUserFlatModelWhenUserNotLoggedIn(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/user');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('null', $response->getContent());
    }
}
