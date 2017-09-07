<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{
    private const USER_ROLE_NAME = 'ROLE_USER';
    private const MANAGER_ROLE_NAME = 'ROLE_MANAGER';
    private const ADMIN_ROLE_NAME = 'ROLE_ADMIN';

    private $doctrine;
    private $encoder;

    public function __construct(Registry $doctrine, UserPasswordEncoderInterface $encoder)
    {
        $this->doctrine = $doctrine;
        $this->encoder = $encoder;
    }

    public function addNewUser(User $newUser)
    {
        if (!$this->isUserAlreadyExists($newUser)) {
            $this->setUserInfo($newUser);
            $this->storeUserEntity($newUser);
        }
    }

    private function isUserAlreadyExists(User $user)
    {
        $repository = $this->doctrine->getRepository(User::class);
        return $repository->findOneBy(['email' => $user->getEmail()]) !== null;
    }

    private function setUserInfo(User $user)
    {
        $this->encodeUserPassword($user);
        $user->setRole(self::USER_ROLE_NAME);
        $user->setIsActive(true);
    }

    private function encodeUserPassword(User $user)
    {
        $encodedPassword = $this->encoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($encodedPassword);
    }

    private function storeUserEntity(User $user)
    {
        $manager = $this->doctrine->getManager();
        $manager->persist($user);
        $manager->flush();
    }
}