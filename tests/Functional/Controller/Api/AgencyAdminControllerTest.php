<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Model\Agency\Flat;
use App\Model\AgencyAdmin\Home;
use App\Model\Branch\Flat as FlatBranch;
use App\Repository\AgencyRepository;
use App\Repository\BranchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class AgencyAdminControllerTest extends WebTestCase
{
    use LoginUserTrait;

    private SerializerInterface $serializer;

    public function setUp(): void
    {
        $this->serializer = new Serializer([new GetSetMethodNormalizer()], [new JsonEncoder()]);
    }

    public function testCreateAgency(): void
    {
        $client = $this->createClientAndLoginUser(TestFixtures::TEST_USER_STANDARD_1_EMAIL);

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

        $loggedInUser = $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_1_EMAIL);

        $agencyRepository = static::$container->get(AgencyRepository::class);
        $agency = $agencyRepository->findOneBy(['slug' => TestFixtures::TEST_AGENCY_1_SLUG]);
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

    public function testCreateAgencyReturnsBadRequestWhenContentMalformed(): void
    {
        $client = $this->createClientAndLoginUser(TestFixtures::TEST_USER_STANDARD_1_EMAIL);

        $client->request(
            'POST',
            '/api/verified/agency',
            [],
            [],
            [],
            '{"agencyName_MALFORMED":"Swaffham Lettings","postcode":"PE37 8RW","externalUrl":"https://swaffhamlettings.com","googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    public function testUpdateAgency(): void
    {
        $client = $this->createClientAndLoginUser(TestFixtures::TEST_USER_AGENCY_1_ADMIN_EMAIL);

        $client->request(
            'PUT',
            '/api/verified/agency/'.TestFixtures::TEST_AGENCY_1_SLUG,
            [],
            [],
            [],
            '{"externalUrl":"https://chipsticks.com","postcode":"CB1 1AA","googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $agencyRepository = static::$container->get(AgencyRepository::class);
        $agency = $agencyRepository->findOneBySlug(TestFixtures::TEST_AGENCY_1_SLUG);
        $this->assertNotNull($agency);
        $this->assertEquals('https://chipsticks.com', $agency->getExternalUrl());
        $this->assertEquals('CB1 1AA', $agency->getPostcode());
    }

    public function testUpdateAgencyFailsWhenNotLoggedIn(): void
    {
        $client = static::createClient();

        $client->request(
            'PUT',
            '/api/verified/agency/'.TestFixtures::TEST_AGENCY_1_SLUG,
            [],
            [],
            [],
            '{"externalUrl":"https://chipsticks.com","postcode":"CB1 1AA","googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testUpdateAgencyReturnsBadRequestWhenContentMalformed(): void
    {
        $client = $this->createClientAndLoginUser(TestFixtures::TEST_USER_AGENCY_1_ADMIN_EMAIL);

        $client->request(
            'PUT',
            '/api/verified/agency/'.TestFixtures::TEST_AGENCY_1_SLUG,
            [],
            [],
            [],
            'NOT_EVEN_JSON'
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    public function testCreateBranch(): void
    {
        $client = static::createClient();

        $loggedInUser = $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_1_EMAIL);

        $agencyRepository = static::$container->get(AgencyRepository::class);
        $agency = $agencyRepository->findOneBy(['slug' => TestFixtures::TEST_AGENCY_1_SLUG]);
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
        $client = $this->createClientAndLoginUser(TestFixtures::TEST_USER_STANDARD_1_EMAIL);

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

    public function testCreateBranchFailsWhenAlreadyExists(): void
    {
        $client = $this->createClientAndLoginUser(TestFixtures::TEST_USER_AGENCY_1_ADMIN_EMAIL);

        $client->request(
            'POST',
            '/api/verified/branch',
            [],
            [],
            [],
            '{"branchName":"Dereham","telephone":"0700 700 800","email":null,"googleReCaptchaToken":"SAMPLE"}'
        );

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
    }

    public function testCreateBranchReturnsBadRequestWhenContentMalformed(): void
    {
        $client = $this->createClientAndLoginUser(TestFixtures::TEST_USER_AGENCY_1_ADMIN_EMAIL);

        $client->request(
            'POST',
            '/api/verified/branch',
            [],
            [],
            [],
            '{"branchName_MALFORMED":"Watton","telephone":"0700 700 800","email":null,"googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    public function testUpdateBranch(): void
    {
        $client = $this->createClientAndLoginUser(TestFixtures::TEST_USER_AGENCY_1_ADMIN_EMAIL);

        $client->request(
            'PUT',
            '/api/verified/branch/'.TestFixtures::TEST_BRANCH_101_SLUG,
            [],
            [],
            [],
            '{"telephone":"020 2020 3030","email":"new.email@starsol.co.uk","googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $branchRepository = static::$container->get(BranchRepository::class);
        $branch = $branchRepository->findOneBySlug(TestFixtures::TEST_BRANCH_101_SLUG);
        $this->assertNotNull($branch);
        $this->assertEquals('020 2020 3030', $branch->getTelephone());
        $this->assertEquals('new.email@starsol.co.uk', $branch->getEmail());
    }

    public function testUpdateBranchFailsWhenNotLoggedIn(): void
    {
        $client = static::createClient();

        $client->request('PUT', '/api/verified/branch/'.TestFixtures::TEST_BRANCH_101_SLUG);

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testUpdateBranchReturnsBadRequestWhenContentMalformed(): void
    {
        $client = $this->createClientAndLoginUser(TestFixtures::TEST_USER_AGENCY_1_ADMIN_EMAIL);

        $client->request(
            'PUT',
            '/api/verified/branch/'.TestFixtures::TEST_BRANCH_101_SLUG,
            [],
            [],
            [],
            'NOT_EVEN_JSON'
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    public function testGetAgencyForUser(): void
    {
        $client = $this->createClientAndLoginUser(TestFixtures::TEST_USER_AGENCY_1_ADMIN_EMAIL);

        $client->request('GET', '/api/verified/agency');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        /** @var Flat $output */
        $output = $this->serializer->deserialize($client->getResponse()->getContent(), Flat::class, 'json');

        $this->assertEquals(TestFixtures::TEST_AGENCY_1_SLUG, $output->getSlug());
    }

    public function testHome(): void
    {
        $client = $this->createClientAndLoginUser(TestFixtures::TEST_USER_AGENCY_1_ADMIN_EMAIL);

        $client->request('GET', '/api/verified/dashboard');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        /** @var Home $home */
        $home = $this->serializer->deserialize($response->getContent(), Home::class, 'json');

        $this->assertEquals('Testerton Lettings', $home->getAgency()->getName());
        $this->assertCount(2, $home->getBranches());
        $this->assertCount(1, $home->getTenancyReviews());
    }

    public function testBranch(): void
    {
        $client = $this->createClientAndLoginUser(TestFixtures::TEST_USER_AGENCY_1_ADMIN_EMAIL);

        $client->request('GET', '/api/verified/branch/'.TestFixtures::TEST_BRANCH_101_SLUG);

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        /** @var FlatBranch $output */
        $output = $this->serializer->deserialize($response->getContent(), FlatBranch::class, 'json');

        $this->assertEquals(TestFixtures::TEST_BRANCH_101_SLUG, $output->getSlug());
    }

    public function testBranchNotFound(): void
    {
        $client = $this->createClientAndLoginUser(TestFixtures::TEST_USER_AGENCY_1_ADMIN_EMAIL);

        $client->request('GET', '/api/verified/branch/notExists');

        $this->assertEquals(Response::HTTP_NOT_FOUND, $client->getResponse()->getStatusCode());
    }

    private function createClientAndLoginUser(string $username): object
    {
        $client = static::createClient();
        $this->loginUser($client, $username);

        return $client;
    }
}
