<?php

namespace Tests\Api\V1;

use App\Repositories\Contracts\UserRepositoryInterface;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserTest extends TestCase
{

    use DatabaseMigrations;

    public function test_should_create_a_new_user()
    {
        $response = $this->call('POST', 'api/v1/users', [
            'full_name' => 'Pouya Parsaei',
            'email' => 'pya.prs@gmail.com',
            'mobile' => '09121112222',
            'password' => '123456',
        ]);

        $this->assertEquals(201, $response->status());

        $this->seeJsonStructure([
            'success',
            'message',
            'data' => [
                'full_name',
                'email',
                'mobile',
                'password',
            ]
        ]);
    }

    public function test_must_throw_an_exception_when_parameters_does_not_pass()
    {
        $response = $this->call('POST', 'api/v1/users', []);
        $this->assertEquals(422, $response->status());

    }

    public function test_should_update_user_info()
    {
        $user = $this->createUsers()[0];
        $response = $this->call('PUT', 'api/v1/users', [
            'id' => (string)$user->getId(),
            'full_name' => $user->getFullName(),
            'email' => $user->getEmail(),
            'mobile' => $user->getMobile(),
        ]);

        $this->assertEquals(200, $response->status());

        $this->seeJsonStructure([
            'success',
            'message',
            'data' => [
                'full_name',
                'email',
                'mobile',
            ]
        ]);
    }

    public function test_must_throw_an_exception_when_parameters_does_not_pass_to_update_user_info()
    {
        $response = $this->call('PUT', 'api/v1/users', []);
        $this->assertEquals(422, $response->status());

    }

    public function test_should_update_user_password()
    {
        $user = $this->createUsers()[0];
        $response = $this->call('PUT', 'api/v1/users/change-password', [
            'id' => (string)$user->getId(),
            'password' => '1234567890',
            'password_repeat' => '1234567890',
        ]);

        $this->assertEquals(200, $response->status());

        $this->seeJsonStructure([
            'success',
            'message',
            'data' => [
                'full_name',
                'email',
                'mobile'
            ]
        ]);
    }

    public function test_must_throw_an_exception_when_parameters_does_not_pass_to_update_user_password()
    {
        $response = $this->call('PUT', 'api/v1/users/change-password', []);
        $this->assertEquals(422, $response->status());

    }

    public function test_should_get_users()
    {
        $this->createUsers(30);
        $pageSize = 5;

        $response = $this->call('GET', 'api/v1/users', [
            'page' => 1,
            'pagesize' => $pageSize
        ]);

        $data = json_decode($response->getContent(), true)['data']['data'];
        $this->assertCount($pageSize, $data);
        $this->assertEquals(200, $response->status());
        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }

    public function test_should_get_filtered_users()
    {
        $pageSize = 5;
        $userEmail = 'pya.prs@gmail.com';
        $response = $this->call('GET', 'api/v1/users', [
            'search' => $userEmail,
            'page' => 2,
            'pagesize' => $pageSize
        ]);

        $data = json_decode($response->getContent(), true)['data']['data'];

        foreach ($data as $user) {
            $this->assertEquals($userEmail, $user['email']);
        }

        $this->assertEquals(200, $response->status());
        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }

    public function test_should_delete_user()
    {
        $user = $this->createUsers()[0];
        $response = $this->call('DELETE', 'api/v1/users', [
            'id' => (string)$user->getId()
        ]);

        $this->assertEquals(200, $response->status());
        $this->seeJsonStructure([
            'success',
            'message',
            'data'
        ]);
    }

    public function createUsers($count = 1): array
    {
        $userRepository = $this->app->make(UserRepositoryInterface::class);
        $userData = [
            'full_name' => 'test testi',
            'email' => 'test@test.com',
            'mobile' => '09112223344',
        ];

        $users = [];
        foreach (range(0, $count) as $item) {
            $users[] = $userRepository->store($userData);
        }
        return $users;
    }
}
