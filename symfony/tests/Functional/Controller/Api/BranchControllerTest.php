<?php

namespace App\Tests\Functional\Controller\Api;

use App\DataFixtures\TestFixtures;
use function json_decode;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class BranchControllerTest extends WebTestCase
{
    use ProphecyTrait;

    public function testView(): void
    {
        $client = static::createClient();

        $client->request('GET', '/api/branch/'.TestFixtures::TEST_BRANCH_SLUG);

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $content = json_decode($response->getContent(), true);
        $this->assertEquals('Dereham', $content['branch']['name']);
        $this->assertEquals(TestFixtures::TEST_BRANCH_SLUG, $content['branch']['slug']);
        $this->assertEquals('Testerton Lettings', $content['agency']['name']);
        $this->assertEquals(TestFixtures::TEST_AGENCY_SLUG, $content['agency']['slug']);
    }
}
