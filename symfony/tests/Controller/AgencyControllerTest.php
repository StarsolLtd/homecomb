<?php

namespace App\Tests\Controller;

use App\DataFixtures\TestFixtures;
use App\Repository\AgencyRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AgencyControllerTest extends WebTestCase
{
    public function testViewBySlug()
    {
        $client = static::createClient();

        $client->request('GET', '/agency/f66a03fd63bbee');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function testCreateAgency()
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail(TestFixtures::TEST_USER_STANDARD_EMAIL);

        $client->loginUser($testUser);

        $client->request(
            'POST',
            '/api/verified/create-agency',
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

    public function testCreateAgencyFailsWhenNotLoggedIn()
    {
        $client = static::createClient();

        $client->request('POST', '/api/verified/create-agency');

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }
}
