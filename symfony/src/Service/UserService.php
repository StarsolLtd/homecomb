<?php

namespace App\Service;

use App\Entity\User;
use App\Exception\ConflictException;
use App\Exception\UserException;
use App\Factory\FlatModelFactory;
use App\Factory\UserFactory;
use App\Model\User\Flat;
use App\Model\User\RegisterInput;
use App\Repository\BranchRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService
{
    private UserFactory $userFactory;
    private BranchRepository $branchRepository;
    private UserRepository $userRepository;
    private FlatModelFactory $flatModelFactory;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UserFactory $userFactory,
        BranchRepository $branchRepository,
        UserRepository $userRepository,
        FlatModelFactory $flatModelFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->userFactory = $userFactory;
        $this->branchRepository = $branchRepository;
        $this->userRepository = $userRepository;
        $this->flatModelFactory = $flatModelFactory;
        $this->entityManager = $entityManager;
    }

    public function getUserEntityOrNullFromUserInterface(?UserInterface $user): ?User
    {
        if (null === $user) {
            return null;
        }
        if ($user instanceof User) {
            return $user;
        }

        return $this->userRepository->loadUserByUsername($user->getUsername()) ?? null;
    }

    public function getEntityFromInterface(?UserInterface $user): User
    {
        $userEntity = $this->getUserEntityOrNullFromUserInterface($user);
        if (null === $userEntity) {
            throw new UserException('User entity not found.');
        }

        return $userEntity;
    }

    public function isUserBranchAdmin(string $branchSlug, ?UserInterface $user): bool
    {
        $user = $this->getEntityFromInterface($user);
        $agency = $user->getAdminAgency();
        if (null === $agency) {
            return false;
        }

        $branch = $this->branchRepository->findOnePublishedBySlug($branchSlug);
        $branchAgency = $branch->getAgency();
        if (null == $branchAgency) {
            return false;
        }
        if ($branchAgency->getId() !== $agency->getId()) {
            return false;
        }

        return true;
    }

    public function getFlatModelFromUserInterface(?UserInterface $user): ?Flat
    {
        $user = $this->getUserEntityOrNullFromUserInterface($user);

        if (null === $user) {
            return null;
        }

        return $this->flatModelFactory->getUserFlatModel($user);
    }

    public function register(RegisterInput $input): User
    {
        $username = $input->getEmail();
        $existing = $this->userRepository->loadUserByUsername($username);
        if (null !== $existing) {
            throw new ConflictException(sprintf('User already exists with username %s', $username));
        }

        $user = $this->userFactory->createEntityFromRegisterInput($input);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
