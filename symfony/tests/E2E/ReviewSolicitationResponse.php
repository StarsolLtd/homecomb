<?php

namespace App\Tests\E2E;

use Symfony\Component\Panther\PantherTestCase;

class ReviewSolicitationResponse extends PantherTestCase
{
    public function testLoadHomePageAndSearchForProperty(): void
    {
        $client = static::createPantherClient();
        $crawler = $client->request('GET', '/review-your-tenancy/73d2d50d17e8c1bbb05b8fddb3918033f2daf589');

        $this->assertPageTitleContains('HomeComb');

        $client->waitFor('#review-tenancy-form-submit');

        $form = $crawler->selectButton('review-tenancy-form-submit')->form();

        $form['reviewerEmail'] = 'luciana@starsol.co.uk';
        $form['reviewerName'] = 'Luciana';
        $form['reviewTitle'] = 'This was a nice place to live';
        $form['reviewContent'] = 'There were some sunflowers in the garden';

        $crawler = $client->submit($form);

//        $client->waitFor('.alert');
    }
}