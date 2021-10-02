<?php

namespace App\Service\User;

use App\Entity\User;
use App\Exception\ConflictException;
use App\Exception\UserException;
use App\Factory\UserFactory;
use App\Model\User\RegisterInput;
use App\Repository\UserRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Provider\GoogleUser;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class RegistrationService
{
    public function __construct(
        private UserFactory $userFactory,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private EmailService $emailService
    ) {
    }

    public function register(RegisterInput $input): User
    {
        $username = $input->getEmail();
        $this->registerCheckExisting($username);

        $user = $this->userFactory->createEntityFromRegisterInput($input);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->sendVerificationEmail($user);

        return $user;
    }

    public function registerFromGoogleUser(GoogleUser $googleUser): User
    {
        $username = $googleUser->getEmail();
        if (null === $username) {
            throw new UserException('GoogleUser email must not be null.');
        }
        $this->registerCheckExisting($username);

        $user = (new User())
            ->setEmail($username)
            ->setGoogleId($googleUser->getId())
            ->setFirstName($googleUser->getFirstName())
            ->setLastName($googleUser->getLastName())
            ->setIsVerified(true)
        ;

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function sendVerificationEmail(User $user): bool
    {
        if (!$this->isSendingVerificationEmailAppropriate($user)) {
            return false;
        }

        $userEmail = $user->getEmail();
        $userFullName = $user->getFirstName().' '.$user->getLastName();

        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            'app_verify_email',
            (string) $user->getId(),
            $userEmail
        );

        $this->emailService->process(
            $userEmail,
            $userFullName,
            'Welcome to HomeComb! Please verify your email address',
            'email-verification',
            [
                'signedUrl' => $signatureComponents->getSignedUrl(),
                'expiresAt' => $signatureComponents->getExpiresAt(),
            ],
            null,
            $user,
            $user
        );

        return true;
    }

    private function isSendingVerificationEmailAppropriate(User $user): bool
    {
        return !$user->isVerified();
    }

    private function registerCheckExisting(string $username): void
    {
        $existing = $this->userRepository->loadUserByUsername($username);
        if (null !== $existing) {
            throw new ConflictException(sprintf('User already exists with username %s', $username));
        }
    }
}
