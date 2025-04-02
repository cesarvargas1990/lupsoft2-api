<?php

namespace Tests\Unit;

use App\Http\Controllers\UserController;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Testing\TestCase;
use Mockery;

class UserControllerTest extends TestCase
{
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    // public function test_profile_returns_authenticated_user()
    // {
    //     $mockAuth = Mockery::mock('alias:Illuminate\\Support\\Facades\\Auth');
    //     $mockAuth->shouldReceive('user')->once()->andReturn(['id' => 1, 'name' => 'Test User']);

    //     $controller = new UserController();
    //     $response = $controller->profile($mockAuth);

    //     $this->assertEquals(200, $response->getStatusCode());
    //     $this->assertEquals(['user' => ['id' => 1, 'name' => 'Test User']], json_decode($response->getContent(), true));
    // }

    public function test_all_users_returns_user_list()
    {
        $mockUser = Mockery::mock(User::class);
        $mockUser->shouldReceive('all')->once()->andReturn([
            ['id' => 1, 'name' => 'User1'],
            ['id' => 2, 'name' => 'User2']
        ]);

        $controller = new UserController();
        $response = $controller->allUsers($mockUser);

        $this->assertEquals(200, $response->getStatusCode());
        $users = json_decode($response->getContent(), true)['users'];
        $this->assertCount(2, $users);
    }

    public function test_single_user_returns_user()
    {
        $mockUser = Mockery::mock(User::class);
        $mockUser->shouldReceive('findOrFail')->with(1)->once()->andReturn(['id' => 1, 'name' => 'Test']);

        $controller = new UserController();
        $response = $controller->singleUser(1, $mockUser);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(['user' => ['id' => 1, 'name' => 'Test']], json_decode($response->getContent(), true));
    }

    public function test_single_user_handles_not_found_exception()
    {
        $mockUser = Mockery::mock(User::class);
        $mockUser->shouldReceive('findOrFail')->with(999)->once()->andThrow(new \Exception('Not Found'));

        $controller = new UserController();
        $response = $controller->singleUser(999, $mockUser);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(['message' => 'user not found!'], json_decode($response->getContent(), true));
    }

    // public function test_get_users_returns_transformed_data()
    // {
    //     $mockUser = Mockery::mock(User::class);
    //     $mockUser->shouldReceive('select')->with(['id as value', 'name as label'])->andReturnSelf();
    //     $mockUser->shouldReceive('where')->with('id_user', 1)->andReturnSelf();
    //     $mockUser->shouldReceive('get')->andReturn(collect([
    //         ['value' => 1, 'label' => 'Test User']
    //     ]));

    //     $controller = new UserController();
    //     $response = $controller->getUsers(1, $mockUser);

    //     $this->assertEquals(200, $response->getStatusCode());
    //     $data = json_decode($response->getContent(), true);
    //     $this->assertEquals('Test User', $data[0]['label']);
    // }

    public function test_get_users_returns_404_if_empty()
    {
        $mockUser = Mockery::mock(User::class);
        $mockUser->shouldReceive('select')->andReturnSelf();
        $mockUser->shouldReceive('where')->andReturnSelf();
        $mockUser->shouldReceive('get')->andReturn(collect());

        $controller = new UserController();
        $response = $controller->getUsers(1, $mockUser);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals(['message' => 'User not found!'], json_decode($response->getContent(), true));
    }

    public function test_get_users_handles_exception()
    {
        $mockUser = Mockery::mock(User::class);
        $mockUser->shouldReceive('select')->andThrow(new \Exception('DB Error'));

        $controller = new UserController();
        $response = $controller->getUsers(1, $mockUser);

        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals(['message' => 'Error retrieving user!'], json_decode($response->getContent(), true));
    }
}
