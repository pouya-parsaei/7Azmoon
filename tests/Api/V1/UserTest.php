<?php

namespace Tests\Api\V1;

use Tests\TestCase;

class UserTest extends TestCase
{
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
                'role'
            ]
        ]);
    }

    public function test_must_throw_an_exception_when_parameters_does_not_pass()
    {
        $response = $this->call('POST', 'api/v1/users',[]);
        $this->assertEquals(422, $response->status());

    }
}
