<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Model\TenancyReviewSolicitation\FormData;
use App\Repository\AgencyRepository;
use App\Repository\BranchRepository;
use App\Repository\PropertyRepository;
use App\Repository\TenancyReviewSolicitationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class SolicitReviewControllerTest extends WebTestCase
{
    use LoginUserTrait;

    private SerializerInterface $serializer;

    public function setUp(): void
    {
        $this->serializer = new Serializer([new GetSetMethodNormalizer()], [new JsonEncoder()]);
    }

    public function testSolicitReviewFormData(): void
    {
        $client = $this->createClientAndLoginUser(TestFixtures::TEST_USER_AGENCY_1_ADMIN_EMAIL);

        $client->request('GET', '/api/verified/solicit-review');

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        /** @var FormData $formData */
        $formData = $this->serializer->deserialize($response->getContent(), FormData::class, 'json');

        $this->assertEquals('Testerton Lettings', $formData->getAgency()->getName());
        $this->assertCount(2, $formData->getBranches());
    }

    public function testSolicitReview(): void
    {
        $client = static::createClient();

        $loggedInUser = $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_1_EMAIL);

        $agencyRepository = static::$container->get(AgencyRepository::class);
        $agency = $agencyRepository->findOneBy(['slug' => TestFixtures::TEST_AGENCY_1_SLUG]);
        $agency->addAdminUser($loggedInUser);

        $branchRepository = static::$container->get(BranchRepository::class);
        $branch = $branchRepository->findOneBy(['slug' => TestFixtures::TEST_BRANCH_101_SLUG]);

        $propertyRepository = static::$container->get(PropertyRepository::class);
        $property = $propertyRepository->findOneBy(['slug' => TestFixtures::TEST_PROPERTY_1_SLUG]);

        $entityManager = static::$container->get(EntityManagerInterface::class);
        $entityManager->flush();

        $client->request(
            'POST',
            '/api/verified/solicit-review',
            [],
            [],
            [],
            '{"branchSlug":"'.TestFixtures::TEST_BRANCH_101_SLUG.'","propertySlug":"'.TestFixtures::TEST_PROPERTY_1_SLUG.'","recipientTitle":null,"recipientFirstName":"Joanna","recipientLastName":"Jones","recipientEmail":"joanna.jones@starsol.co.uk","captchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $reviewSolicitationRepository = static::$container->get(TenancyReviewSolicitationRepository::class);
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

        $loggedInUser = $this->loginUser($client, TestFixtures::TEST_USER_STANDARD_1_EMAIL);
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
            '{"branchSlug":"'.TestFixtures::TEST_BRANCH_101_SLUG.'","propertySlug":"'.TestFixtures::TEST_PROPERTY_1_SLUG.'","recipientTitle":null,"recipientFirstName":"Joanna","recipientLastName":"Jones","recipientEmail":"joanna.jones@starsol.co.uk","captchaToken":"SAMPLE"}'
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
            '{"branchSlug":"'.TestFixtures::TEST_BRANCH_101_SLUG.'","propertySlug":"'.TestFixtures::TEST_PROPERTY_1_SLUG.'","recipientTitle":null,"recipientFirstName":"Joanna","recipientLastName":"Jones","recipientEmail":"joanna.jones@starsol.co.uk","captchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    public function testSolicitReviewReturnsBadRequestWhenContentMalformed(): void
    {
        $client = $this->createClientAndLoginUser(TestFixtures::TEST_USER_AGENCY_1_ADMIN_EMAIL);

        $client->request(
            'POST',
            '/api/verified/solicit-review',
            [],
            [],
            [],
            '{"branchSlug_MALFORMED":"'.TestFixtures::TEST_BRANCH_101_SLUG.'","propertySlug":"'.TestFixtures::TEST_PROPERTY_1_SLUG.'","recipientTitle":null,"recipientFirstName":"Joanna","recipientLastName":"Jones","recipientEmail":"joanna.jones@starsol.co.uk","captchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }

    private function createClientAndLoginUser(string $username): object
    {
        $client = static::createClient();
        $this->loginUser($client, $username);

        return $client;
    }
}
