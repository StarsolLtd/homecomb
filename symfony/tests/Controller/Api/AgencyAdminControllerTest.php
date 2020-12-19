<?php

namespace App\Tests\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Repository\AgencyRepository;
use App\Repository\BranchRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    public function testCreateBranch(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail(TestFixtures::TEST_USER_STANDARD_EMAIL);
        $client->loginUser($testUser);

        $agencyRepository = static::$container->get(AgencyRepository::class);
        $agency = $agencyRepository->findOneBy(['slug' => TestFixtures::TEST_AGENCY_SLUG]);
        $agency->addAdminUser($testUser);

        $entityManager = static::$container->get(EntityManagerInterface::class);
        $entityManager->flush();

        $client->request(
            'POST',
            '/api/verified/branch',
            [],
            [],
            [],
            '{"branchName":"Watton","telephone":"0700 700 800","email":null,"googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $branchRepository = static::$container->get(BranchRepository::class);
        $branch = $branchRepository->findOneBy([
            'name' => 'Watton',
            'agency' => $agency,
        ]);
        $this->assertNotNull($branch);
        $this->assertEquals('Watton', $branch->getName());
        $this->assertEquals('0700 700 800', $branch->getTelephone());
        $this->assertNull($branch->getEmail());
        $this->assertEquals($agency, $branch->getAgency());
    }

    public function testCreateBranchFailsWhenUserIsNotAnAgencyAdmin(): void
    {
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail(TestFixtures::TEST_USER_STANDARD_EMAIL);
        $client->loginUser($testUser);

        $client->request(
            'POST',
            '/api/verified/branch',
            [],
            [],
            [],
            '{"branchName":"Watton","telephone":"0700 700 800","email":null,"googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $client->getResponse()->getStatusCode());
    }


    public function testCreateBranchFailsWhenNotLoggedIn(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/verified/branch');

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }
}
