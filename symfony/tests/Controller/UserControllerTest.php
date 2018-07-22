<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends ApiTestCase
{
    const USER_EMAIL = 'user@mail.com';
    const USERNAME = 'user';
    const USER_PASSWORD = 'userpass';

    const NEW_USER_EMAIL = 'new_user@mail.com';
    const NEW_USERNAME = 'new_user';
    const NEW_USER_PASSWORD = 'new_userpass';

    protected function setUp()
    {
        parent::setUp();

        $this->createUser(self::USERNAME, self::USER_EMAIL, self::USER_PASSWORD);
    }

    public function testSignUpActionSucceeds()
    {
        $this->removeUser(['username' => self::NEW_USERNAME]);
        $this->removeUser(['email' => self::NEW_USER_EMAIL]);

        $response = $this->performRequest(
            'POST',
            'app.users.sign_up',
            [],
            [
                'username' => self::NEW_USERNAME,
                'email' => self::NEW_USER_EMAIL,
                'password' => self::NEW_USER_PASSWORD,
            ],
            false
        );
        $responseContent = json_decode($response->getContent());

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_CREATED,
                'content' => [
                    'username' => self::NEW_USERNAME,
                    'email' => self::NEW_USER_EMAIL,
                    'cart_items' => [],
                    'password' => 'exists',
                    'created_at' => 'exists',
                    'updated_at' => 'exists',
                ]
            ],
            [
                'status_code' => $response->getStatusCode(),
                'content' => [
                    'username' => $responseContent->username,
                    'email' => $responseContent->email,
                    'cart_items' => $responseContent->cart_items,
                    'password' => $responseContent->password ? 'exists' : 'is missing',
                    'created_at' => $responseContent->created_at ? 'exists' : 'is missing',
                    'updated_at' => $responseContent->updated_at ? 'exists' : 'is missing',
                ]
            ]
        );
    }

    /**
     * @dataProvider dataTestUpdateActionReturnsBadRequest
     * @param array $data
     */
    public function testSignUpActionReturnsBadRequest(array $data)
    {
        $this->removeUser(['username' => self::NEW_USERNAME]);
        $this->removeUser(['email' => self::NEW_USER_EMAIL]);

        $response = $this->performRequest(
            'POST',
            'app.users.sign_up',
            [],
            $data,
            false
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @return array
     */
    public function dataTestUpdateActionReturnsBadRequest() : array
    {
        return [
            'case 1: no parameters' => [
                'data' => [],
            ],
            'case 2: no email' => [
                'data' => [
                    'username' => self::NEW_USERNAME,
                    'password' => self::NEW_USER_PASSWORD,
                ],
            ],
            'case 3: no username' => [
                'data' => [
                    'email' => self::NEW_USER_EMAIL,
                    'password' => self::NEW_USER_PASSWORD,
                ],
            ],
            'case 4: no password' => [
                'data' => [
                    'email' => self::NEW_USER_EMAIL,
                    'username' => self::NEW_USERNAME,
                ]
            ],
            'case 5: email duplication' => [
                'data' => [
                    'email' => self::USER_EMAIL,
                    'username' => self::NEW_USERNAME,
                    'password' => self::NEW_USER_PASSWORD,
                ]
            ],
            'case 6: username duplication' => [
                'data' => [
                    'email' => self::NEW_USER_EMAIL,
                    'username' => self::USERNAME,
                    'password' => self::NEW_USER_PASSWORD,
                ]
            ],
            'case 7: too long username' => [
                'data' => [
                    'email' => self::NEW_USER_EMAIL,
                    'username' => str_repeat('u', 26),
                    'password' => self::NEW_USER_PASSWORD,
                ]
            ],
            'case 8: too long email' => [
                'data' => [
                    'email' => str_repeat('e', 255) . '@mail.com',
                    'username' => self::NEW_USERNAME,
                    'password' => self::NEW_USER_PASSWORD,
                ]
            ],
            'case 9: too long password' => [
                'data' => [
                    'email' => self::NEW_USER_EMAIL,
                    'username' => self::NEW_USERNAME,
                    'password' => str_repeat('p', 65),
                ]
            ],
            'case 10: invalid email' => [
                'data' => [
                    'email' => str_repeat('e', 30),
                    'username' => self::NEW_USERNAME,
                    'password' => self::NEW_USER_PASSWORD,
                ]
            ],
        ];
    }

    /**
     * @param string $username
     * @param string $email
     * @param string $password
     *
     * @return User
     */
    private function createUser(
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
    private function removeUser(array $findParams)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy($findParams);
        $user && $this->entityManager->remove($user);

        $this->entityManager->flush();
    }
}
