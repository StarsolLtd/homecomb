<?php

namespace App\Tests\Unit\Service\Agency;

use App\Entity\Agency;
use App\Entity\User;
use App\Exception\ForbiddenException;
use App\Exception\NotFoundException;
use App\Model\Agency\UpdateInputInterface;
use App\Service\Agency\UpdateService;
use App\Service\User\UserService;
use App\Tests\Unit\EntityManagerTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class UpdateServiceTest extends TestCase
{
    use ProphecyTrait;
    use EntityManagerTrait;

    private UpdateService $updateService;

    private ObjectProphecy $userService;

    public function setUp(): void
    {
        $this->userService = $this->prophesize(UserService::class);
        $this->entityManager = $this->prophesize(EntityManagerInterface::class);

        $this->updateService = new UpdateService(
            $this->userService->reveal(),
            $this->entityManager->reveal(),
        );
    }

    public function testUpdateAgency(): void
    {
        $slug = 'testagencyslug';

        $input = $this->prophesize(UpdateInputInterface::class);
        $input->getExternalUrl()->shouldBeCalledOnce()->willReturn('https://updated.com/here');
        $input->getPostcode()->shouldBeCalledOnce()->willReturn('NR21 4SF');

        $user = new User();
        $agency = (new Agency())->setSlug($slug)->addAdminUser($user);

        $this->userService->getEntityFromInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $this->entityManager->flush()->shouldBeCalledOnce();

        $output = $this->updateService->updateAgency($slug, $input->reveal(), $user);

        $this->assertEquals('https://updated.com/here', $agency->getExternalUrl());
        $this->assertEquals('NR21 4SF', $agency->getPostcode());
        $this->assertTrue($output->isSuccess());
    }

    public function testUpdateAgencyThrowsExceptionWhenUserNotAgencyAdmin(): void
    {
        $slug = 'testagencyslug';

        $input = $this->prophesize(UpdateInputInterface::class);

        $user = new User();

        $this->userService->getEntityFromInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $this->expectException(NotFoundException::class);
        $this->assertEntityManagerUnused();

        $this->updateService->updateAgency($slug, $input->reveal(), $user);
    }

    public function testUpdateAgencyThrowsExceptionWhenUserAdminOfDifferentAgency(): void
    {
        $slug = 'testagencyslug';

        $input = $this->prophesize(UpdateInputInterface::class);

        $user = new User();
        $agency = (new Agency())->setSlug($slug);
        $differentAgency = (new Agency())->setSlug('different')->addAdminUser($user);

        $this->userService->getEntityFromInterface($user)->shouldBeCalledOnce()->willReturn($user);

        $this->expectException(ForbiddenException::class);
        $this->assertEntityManagerUnused();

        $this->updateService->updateAgency($slug, $input->reveal(), $user);
    }
}
