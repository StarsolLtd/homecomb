<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use App\Model\User\RegisterInput;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class RegistrationControllerTest extends WebTestCase
{
    private SerializerInterface $serializer;

    public function setUp(): void
    {
        $this->serializer = new Serializer([new GetSetMethodNormalizer()], [new JsonEncoder()]);
    }

    public function registerSuccessDataProvider(): array
    {
        return [
            ['{"email": "test.1@starsol.co.uk","firstName":"Jack","lastName":"Harper","plainPassword":"Vision_2021","googleReCaptchaToken":"SAMPLE"}'],
            ['{"UNUSED_ADDITIONAL_FIELD": "anything","email": "test.1@starsol.co.uk","firstName":"Jack","lastName":"Harper","plainPassword":"Vision_2021","googleReCaptchaToken":"SAMPLE"}'],
            ['{"email": "j@h.io","firstName":"Jelena","lastName":"Harpova","plainPassword":"&L_5YMe<+NC<,n]FhwR=_Q4","googleReCaptchaToken":"SAMPLE"}'],
            ['{"email": "beth.evans.long.email@Llanfairpwll-gwyngyllgogerychwyrndrob.cymru","firstName":"Beth","lastName":"Evans","plainPassword":"Dragon_123","googleReCaptchaToken":"SAMPLE"}'],
        ];
    }

    /**
     * @dataProvider registerSuccessDataProvider
     */
    public function testRegister(string $content): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            [],
            $content
        );

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        /** @var RegisterInput $input */
        $input = $this->serializer->deserialize($content, RegisterInput::class, 'json');

        /** @var UserRepository $userRepository */
        $userRepository = static::$container->get(UserRepository::class);
        $user = $userRepository->loadUserByUsername($input->getEmail());
        $this->assertNotNull($user);
    }

    public function testRegisterReturnsConflictWhenUserAlreadyExists(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            [],
            '{"email": "'.TestFixtures::TEST_USER_STANDARD_1_EMAIL.'","firstName":"Jelena","lastName":"Harpova","plainPassword":"&L_5YMe<+NC<,n]FhwR=_Q4","googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_CONFLICT, $client->getResponse()->getStatusCode());
    }

    public function testRegisterReturnsBadRequestWhenContentMalformed(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/register',
            [],
            [],
            [],
            '{"email_MALFORMED": "test.1@starsol.co.uk","firstName":"Jack","lastName":"Harper","plainPassword":"Vision_2021","googleReCaptchaToken":"SAMPLE"}'
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $client->getResponse()->getStatusCode());
    }
}
