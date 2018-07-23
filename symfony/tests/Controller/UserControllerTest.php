<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;
use App\Tests\Helper\UserHelper;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends ApiTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->userHelper->createUser(UserHelper::USERNAME, UserHelper::USER_EMAIL, UserHelper::USER_PASSWORD);
    }

    public function testSignUpActionSucceeds(): void
    {
        $this->userHelper->removeUser(['username' => UserHelper::NEW_USERNAME]);
        $this->userHelper->removeUser(['email' => UserHelper::NEW_USER_EMAIL]);

        $response = $this->performRequest(
            'POST',
            'app.users.sign_up',
            [],
            [
                'username' => UserHelper::NEW_USERNAME,
                'email' => UserHelper::NEW_USER_EMAIL,
                'password' => UserHelper::NEW_USER_PASSWORD,
            ],
            false
        );
        $responseContent = json_decode($response->getContent());

        $this->assertEquals(
            [
                'status_code' => Response::HTTP_CREATED,
                'content' => [
                    'username' => UserHelper::NEW_USERNAME,
                    'email' => UserHelper::NEW_USER_EMAIL,
                    'created_at' => 'exists',
                    'updated_at' => 'exists',
                ]
            ],
            [
                'status_code' => $response->getStatusCode(),
                'content' => [
                    'username' => $responseContent->username,
                    'email' => $responseContent->email,
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
    public function testSignUpActionReturnsBadRequest(array $data): void
    {
        $this->userHelper->removeUser(['username' => UserHelper::NEW_USERNAME]);
        $this->userHelper->removeUser(['email' => UserHelper::NEW_USER_EMAIL]);

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
                    'username' => UserHelper::NEW_USERNAME,
                    'password' => UserHelper::NEW_USER_PASSWORD,
                ],
            ],
            'case 3: no username' => [
                'data' => [
                    'email' => UserHelper::NEW_USER_EMAIL,
                    'password' => UserHelper::NEW_USER_PASSWORD,
                ],
            ],
            'case 4: no password' => [
                'data' => [
                    'email' => UserHelper::NEW_USER_EMAIL,
                    'username' => UserHelper::NEW_USERNAME,
                ]
            ],
            'case 5: email duplication' => [
                'data' => [
                    'email' => UserHelper::USER_EMAIL,
                    'username' => UserHelper::NEW_USERNAME,
                    'password' => UserHelper::NEW_USER_PASSWORD,
                ]
            ],
            'case 6: username duplication' => [
                'data' => [
                    'email' => UserHelper::NEW_USER_EMAIL,
                    'username' => UserHelper::USERNAME,
                    'password' => UserHelper::NEW_USER_PASSWORD,
                ]
            ],
            'case 7: too long username' => [
                'data' => [
                    'email' => UserHelper::NEW_USER_EMAIL,
                    'username' => str_repeat('u', 26),
                    'password' => UserHelper::NEW_USER_PASSWORD,
                ]
            ],
            'case 8: too long email' => [
                'data' => [
                    'email' => str_repeat('e', 255) . '@mail.com',
                    'username' => UserHelper::NEW_USERNAME,
                    'password' => UserHelper::NEW_USER_PASSWORD,
                ]
            ],
            'case 9: too long password' => [
                'data' => [
                    'email' => UserHelper::NEW_USER_EMAIL,
                    'username' => UserHelper::NEW_USERNAME,
                    'password' => str_repeat('p', 65),
                ]
            ],
            'case 10: invalid email' => [
                'data' => [
                    'email' => str_repeat('e', 30),
                    'username' => UserHelper::NEW_USERNAME,
                    'password' => UserHelper::NEW_USER_PASSWORD,
                ]
            ],
        ];
    }
}
