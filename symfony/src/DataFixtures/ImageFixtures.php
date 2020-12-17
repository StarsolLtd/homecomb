<?php

namespace App\DataFixtures;

use App\Entity\Image;
use App\Entity\Locale;
use function copy;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $cambridgeImage = (new Image())
            ->setDescription('Kings College Chapel West in Cambridge')
            ->setImage('cambridge.jpg');
        $manager->persist($cambridgeImage);

        $this->copyImageToPublic('cambridge.jpg');

        /** @var Locale $cambridgeLocale */
        $cambridgeLocale = $this->getReference('locale-cambridge');
        $cambridgeLocale->addImage($cambridgeImage);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            LocaleFixtures::class,
        ];
    }

    private function getImageFixturesPath(): string
    {
        return '/var/www/symfony/assets/images/';
    }

    private function getPublicImagesPath(): string
    {
        return '/var/www/symfony/public/images/images/';
    }

    private function copyImageToPublic(string $filename): void
    {
        copy($this->getImageFixturesPath().$filename, $this->getPublicImagesPath().$filename);
    }
}
