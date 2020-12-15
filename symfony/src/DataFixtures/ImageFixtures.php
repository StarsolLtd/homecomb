<?php

namespace App\DataFixtures;

use App\Entity\Image;
use App\Entity\Locale;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $cambridgeImage = (new Image())
         ->setDescription('Kings College Chapel West in Cambridge');

        $assetPath = $this->getImageFixturesPath().'cambridge.jpg';
        $assetCopyPath = $assetPath.'.copy';

        copy($assetPath, $assetCopyPath);
        $file = new UploadedFile($assetCopyPath, 'cambridge.jpg');
        $cambridgeImage->setImageFile($file);

        $manager->persist($cambridgeImage);

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

    public function getImageFixturesPath(): string
    {
        return '/var/www/symfony/assets/images/';
    }
}
