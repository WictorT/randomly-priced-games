<?php

namespace App\Tests\Helper;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserHelper
{
    const USER_EMAIL = 'user@mail.com';
    const USERNAME = 'user';
    const USER_PASSWORD = 'userpass';

    const NEW_USER_EMAIL = 'new_user@mail.com';
    const NEW_USERNAME = 'new_user';
    const NEW_USER_PASSWORD = 'new_userpass';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $username
     * @param string $email
     * @param string $password
     *
     * @return User
     */
    public function createUser(
        $username = self::NEW_USERNAME,
        $email = self::NEW_USER_EMAIL,
        $password = self::NEW_USER_PASSWORD
    ): User {
        $this->removeUser(['username' => $username]);
        $this->removeUser(['email' => $email]);

        $user = (new User)
            ->setUsername($username)
            ->setEmail($email)
            ->setPassword($password);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param array $findParams
     * @return void
     */
    public function removeUser(array $findParams)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy($findParams);
        $user && $this->entityManager->remove($user);

        $this->entityManager->flush();
    }

    /**
     * @return User
     */
    public function guaranteeAdminUserExists(): User
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy([
            'username' => 'admin',
            'email' => 'admin@mail.com',
        ]);
        if ($user) {
            return $user;
        }

        // remove users with email=admin@mail.com or username=admin to avoid conflict
        $user = $userRepository->findOneBy(['username' => 'admin']);
        $user && $this->entityManager->remove($user);
        $user = $userRepository->findOneBy(['email' => 'admin@mail.com']);
        $user && $this->entityManager->remove($user);
        $this->entityManager->flush();

        $user = (new User)
            ->setUsername('admin')
            ->setEmail('admin@mail.com')
            ->setPassword('$2a$08$jHZj/wJfcVKlIwr5AvR78euJxYK7Ku5kURNhNx.7.CSIJ3Pq6LEPC');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
