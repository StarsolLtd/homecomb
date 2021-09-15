<?php

namespace App\Tests\Unit\Security;

use App\Entity\Agency;
use App\Entity\Flag\Flag;
use App\Entity\TenancyReview;
use App\Entity\User;
use App\Security\TenancyReviewVoter;
use App\Service\UserService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * @covers \App\Security\TenancyReviewVoter
 */
class TenancyReviewVoterTest extends TestCase
{
    use ProphecyTrait;

    private TenancyReviewVoter $tenancyReviewVoter;

    private ObjectProphecy $userService;

    public function setUp(): void
    {
        $this->userService = $this->prophesize(UserService::class);

        $this->tenancyReviewVoter = new TenancyReviewVoter(
            $this->userService->reveal()
        );
    }

    /**
     * @covers \App\Security\TenancyReviewVoter::vote
     * Test ACCESS_GRANTED for agency admin user of review agency
     */
    public function testVote1(): void
    {
        $user = $this->prophesize(User::class);
        $agency = $this->prophesize(Agency::class);
        $tenancyReview = $this->prophesize(TenancyReview::class);

        $tenancyReview->isPublished()->shouldBeCalledOnce()->willReturn(true);
        $tenancyReview->getAgency()->shouldBeCalledOnce()->willReturn($agency);
        $this->userService->getUserEntityOrNullFromUserInterface($user)->shouldBeCalledOnce()->willReturn($user);
        $user->getAdminAgency()->shouldBeCalledOnce()->willReturn($agency);
        $agency->getId()->shouldBeCalledTimes(2)->willReturn(5678);

        $token = $this->getTokenForUser($user->reveal());

        $result = $this->tenancyReviewVoter->vote($token, $tenancyReview->reveal(), [TenancyReviewVoter::COMMENT]);

        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $result);
    }

    /**
     * @covers \App\Security\TenancyReviewVoter::vote
     * Test ACCESS_DENIED when user is admin of different agency
     */
    public function testVote2(): void
    {
        $user = $this->prophesize(User::class);
        $agency = $this->prophesize(Agency::class);
        $differentAgency = $this->prophesize(Agency::class);
        $tenancyReview = $this->prophesize(TenancyReview::class);

        $tenancyReview->isPublished()->shouldBeCalledOnce()->willReturn(true);
        $tenancyReview->getAgency()->shouldBeCalledOnce()->willReturn($agency);
        $this->userService->getUserEntityOrNullFromUserInterface($user)->shouldBeCalledOnce()->willReturn($user);
        $user->getAdminAgency()->shouldBeCalledOnce()->willReturn($differentAgency);
        $agency->getId()->shouldBeCalledOnce()->willReturn(5678);
        $differentAgency->getId()->shouldBeCalledOnce()->willReturn(4321);

        $token = $this->getTokenForUser($user->reveal());

        $result = $this->tenancyReviewVoter->vote($token, $tenancyReview->reveal(), [TenancyReviewVoter::COMMENT]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    /**
     * @covers \App\Security\TenancyReviewVoter::vote
     * Test ACCESS_DENIED when user is not agency admin
     */
    public function testVote3(): void
    {
        $user = $this->prophesize(User::class);
        $agency = $this->prophesize(Agency::class);
        $tenancyReview = $this->prophesize(TenancyReview::class);

        $tenancyReview->isPublished()->shouldBeCalledOnce()->willReturn(true);
        $tenancyReview->getAgency()->shouldBeCalledOnce()->willReturn($agency);
        $this->userService->getUserEntityOrNullFromUserInterface($user)->shouldBeCalledOnce()->willReturn($user);
        $user->getAdminAgency()->shouldBeCalledOnce()->willReturn(null);

        $token = $this->getTokenForUser($user->reveal());

        $result = $this->tenancyReviewVoter->vote($token, $tenancyReview->reveal(), [TenancyReviewVoter::COMMENT]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    /**
     * @covers \App\Security\TenancyReviewVoter::vote
     * Test ACCESS_DENIED when user entity could not be retrieved
     */
    public function testVote4(): void
    {
        $user = $this->prophesize(User::class);
        $agency = $this->prophesize(Agency::class);
        $tenancyReview = $this->prophesize(TenancyReview::class);

        $tenancyReview->isPublished()->shouldBeCalledOnce()->willReturn(true);
        $tenancyReview->getAgency()->shouldBeCalledOnce()->willReturn($agency);
        $this->userService->getUserEntityOrNullFromUserInterface($user)->shouldBeCalledOnce()->willReturn(null);

        $token = $this->getTokenForUser($user->reveal());

        $result = $this->tenancyReviewVoter->vote($token, $tenancyReview->reveal(), [TenancyReviewVoter::COMMENT]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    /**
     * @covers \App\Security\TenancyReviewVoter::vote
     * Test ACCESS_DENIED when review has no agency
     */
    public function testVote5(): void
    {
        $user = $this->prophesize(User::class);
        $tenancyReview = $this->prophesize(TenancyReview::class);

        $tenancyReview->isPublished()->shouldBeCalledOnce()->willReturn(true);
        $tenancyReview->getAgency()->shouldBeCalledOnce()->willReturn(null);

        $token = $this->getTokenForUser($user->reveal());

        $result = $this->tenancyReviewVoter->vote($token, $tenancyReview->reveal(), [TenancyReviewVoter::COMMENT]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    /**
     * @covers \App\Security\TenancyReviewVoter::vote
     * Test ACCESS_DENIED on unpublished review
     */
    public function testVote6(): void
    {
        $user = $this->prophesize(User::class);
        $tenancyReview = $this->prophesize(TenancyReview::class);

        $tenancyReview->isPublished()->shouldBeCalledOnce()->willReturn(false);

        $token = $this->getTokenForUser($user->reveal());

        $result = $this->tenancyReviewVoter->vote($token, $tenancyReview->reveal(), [TenancyReviewVoter::COMMENT]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    /**
     * @covers \App\Security\TenancyReviewVoter::vote
     * Test ACCESS_ABSTAIN when attribute not supported
     */
    public function testVote7(): void
    {
        $user = $this->prophesize(User::class);
        $tenancyReview = $this->prophesize(TenancyReview::class);

        $token = $this->getTokenForUser($user->reveal());

        $result = $this->tenancyReviewVoter->vote($token, $tenancyReview->reveal(), ['besmirch']);

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    /**
     * @covers \App\Security\TenancyReviewVoter::vote
     * Test ACCESS_ABSTAIN when subject not Review
     */
    public function testVote8(): void
    {
        $user = $this->prophesize(User::class);
        $flag = $this->prophesize(Flag::class);

        $token = $this->getTokenForUser($user->reveal());

        $result = $this->tenancyReviewVoter->vote($token, $flag->reveal(), [TenancyReviewVoter::COMMENT]);

        $this->assertEquals(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    /**
     * @covers \App\Security\TenancyReviewVoter::vote
     * Test ACCESS_DENIED when can not get user from token
     */
    public function testVote9(): void
    {
        $tenancyReview = $this->prophesize(TenancyReview::class);

        $token = $this->prophesize(UsernamePasswordToken::class);
        $token->getUser()->shouldBeCalledOnce()->willReturn(null);

        $result = $this->tenancyReviewVoter->vote($token->reveal(), $tenancyReview->reveal(), [TenancyReviewVoter::COMMENT]);

        $this->assertEquals(VoterInterface::ACCESS_DENIED, $result);
    }

    private function getTokenForUser(User $user): UsernamePasswordToken
    {
        return new UsernamePasswordToken(
            $user, 'credentials', 'memory'
        );
    }
}
