<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Repository\AgencyRepository;
use App\Repository\BranchRepository;
use App\Repository\PropertyRepository;
use App\Repository\ReviewSolicitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AgencyAdminControllerTest extends WebTestCase
{
    use LoginUserTrait;

    public function testCreateAgency(): void
    {
        $client = static::createClient();

        $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_EMAIL);

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

    public function testCreateAgencyFailsWhenUserAlreadyAgencyAdmin(): void
    {
        $client = static::createClient();

        $loggedInUser = $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_EMAIL);

        $agencyRepository = static::$container->get(AgencyRepository::class);
        $agency = $agencyRepository->findOneBy(['slug' => TestFixtures::TEST_AGENCY_SLUG]);
        $agency->addAdminUser($loggedInUser);

        $client->request(
            'POST',
            '/api/verified/agency',
            [],
            [],
            [],
            '{"agencyName":"Wroxham Lettings","postcode":"NR14 8RW","externalUrl":"https://wroxhamlettings.com","googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_CONFLICT, $client->getResponse()->getStatusCode());
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

        $loggedInUser = $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_EMAIL);

        $agencyRepository = static::$container->get(AgencyRepository::class);
        $agency = $agencyRepository->findOneBy(['slug' => TestFixtures::TEST_AGENCY_SLUG]);
        $agency->addAdminUser($loggedInUser);

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

        $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_EMAIL);

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

    public function testUpdateBranch(): void
    {
        $client = static::createClient();

        $this->loginUser($client, TestFixtures::TEST_USER_AGENCY_ADMIN_EMAIL);

        $client->request(
            'PUT',
            '/api/verified/branch/'.TestFixtures::TEST_BRANCH_SLUG,
            [],
            [],
            [],
            '{"telephone":"020 2020 3030","email":"new.email@starsol.co.uk","googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $branchRepository = static::$container->get(BranchRepository::class);
        $branch = $branchRepository->findOneBySlug(TestFixtures::TEST_BRANCH_SLUG);
        $this->assertNotNull($branch);
        $this->assertEquals('020 2020 3030', $branch->getTelephone());
        $this->assertEquals('new.email@starsol.co.uk', $branch->getEmail());
    }

    public function testUpdateBranchFailsWhenNotLoggedIn(): void
    {
        $client = static::createClient();

        $client->request('PUT', '/api/verified/branch/'.TestFixtures::TEST_BRANCH_SLUG);

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testSolicitReview(): void
    {
        $client = static::createClient();

        $loggedInUser = $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_EMAIL);

        $agencyRepository = static::$container->get(AgencyRepository::class);
        $agency = $agencyRepository->findOneBy(['slug' => TestFixtures::TEST_AGENCY_SLUG]);
        $agency->addAdminUser($loggedInUser);

        $branchRepository = static::$container->get(BranchRepository::class);
        $branch = $branchRepository->findOneBy(['slug' => TestFixtures::TEST_BRANCH_SLUG]);

        $propertyRepository = static::$container->get(PropertyRepository::class);
        $property = $propertyRepository->findOneBy(['slug' => TestFixtures::TEST_PROPERTY_SLUG]);

        $entityManager = static::$container->get(EntityManagerInterface::class);
        $entityManager->flush();

        $client->request(
            'POST',
            '/api/verified/solicit-review',
            [],
            [],
            [],
            '{"branchSlug":"'.TestFixtures::TEST_BRANCH_SLUG.'","propertySlug":"'.TestFixtures::TEST_PROPERTY_SLUG.'","recipientTitle":null,"recipientFirstName":"Joanna","recipientLastName":"Jones","recipientEmail":"joanna.jones@starsol.co.uk","captchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $reviewSolicitationRepository = static::$container->get(ReviewSolicitationRepository::class);
        $reviewSolicitationRepository = $reviewSolicitationRepository->findOneBy([
            'branch' => $branch,
            'property' => $property,
            'recipientEmail' => 'joanna.jones@starsol.co.uk',
            'recipientTitle' => null,
            'recipientFirstName' => 'Joanna',
            'recipientLastName' => 'Jones',
        ]);
        $this->assertNotNull($reviewSolicitationRepository);
    }

    public function testSolicitReviewFailsWhenUserNotAgencyAdmin(): void
    {
        $client = static::createClient();

        $loggedInUser = $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_EMAIL);
        $loggedInUser->setAdminAgency(null);

        $entityManager = static::$container->get(EntityManagerInterface::class);
        $entityManager->flush();

        $client->loginUser($loggedInUser);

        $client->request(
            'POST',
            '/api/verified/solicit-review',
            [],
            [],
            [],
            '{"branchSlug":"'.TestFixtures::TEST_BRANCH_SLUG.'","propertySlug":"'.TestFixtures::TEST_PROPERTY_SLUG.'","recipientTitle":null,"recipientFirstName":"Joanna","recipientLastName":"Jones","recipientEmail":"joanna.jones@starsol.co.uk","captchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testSolicitReviewFailsWhenNotLoggedIn(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/verified/solicit-review',
            [],
            [],
            [],
            '{"branchSlug":"'.TestFixtures::TEST_BRANCH_SLUG.'","propertySlug":"'.TestFixtures::TEST_PROPERTY_SLUG.'","recipientTitle":null,"recipientFirstName":"Joanna","recipientLastName":"Jones","recipientEmail":"joanna.jones@starsol.co.uk","captchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }
}
