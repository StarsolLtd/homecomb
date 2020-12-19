<?php

namespace App\Tests\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Repository\AgencyRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AgencyAdminControllerTest extends WebTestCase
{
    public function testCreateAgency(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail(TestFixtures::TEST_USER_STANDARD_EMAIL);
        $client->loginUser($testUser);

        $client->request(
            'POST',
            '/api/verified/agency',
            [],
            [],
            [],
            '{"agencyName":"Swaffham Lettings","postcode":"PE37 8RW","externalUrl":"https://swaffhamlettings.com","googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $agencyRepository = static::$container->get(AgencyRepository::class);
        $agency = $agencyRepository->findOneBy([
            'name' => 'Swaffham Lettings',
            'postcode' => 'PE37 8RW',
            'externalUrl' => 'https://swaffhamlettings.com',
        ]);
        $this->assertNotNull($agency);
        $this->assertEquals('Swaffham Lettings', $agency->getName());
        $this->assertEquals('PE37 8RW', $agency->getPostcode());
        $this->assertEquals('https://swaffhamlettings.com', $agency->getExternalUrl());
    }

    public function testCreateAgencyFailsWhenNotLoggedIn(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/verified/agency');

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }
}
