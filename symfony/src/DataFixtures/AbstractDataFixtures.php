<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractDataFixtures extends Fixture implements ContainerAwareInterface, FixtureInterface
{
    protected ContainerInterface $container;

    public function load(ObjectManager $manager): void
    {
        /** @var KernelInterface $kernel */
        $kernel = $this->container->get('kernel');

        if (in_array($kernel->getEnvironment(), $this->getEnvironments())) {
            $this->doLoad($manager);
        }
    }

    public function setContainer(?ContainerInterface $container = null): void
    {
        if (null === $container) {
            throw new RuntimeException('Container must not be null.');
        }

        $this->container = $container;
    }

    /**
     * Performs the actual fixtures loading.
     *
     * @see \Doctrine\Common\DataFixtures\FixtureInterface::load()
     *
     * @param ObjectManager $manager the object manager
     */
    abstract protected function doLoad(ObjectManager $manager): void;

    abstract protected function getEnvironments(): array;
}
