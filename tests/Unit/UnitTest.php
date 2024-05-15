<?php

namespace Unit;

use tests\TestCase;
use App\Core\{Router, Request, App};
use App\Models\User;

class UnitTest extends TestCase
{
    /** @test
     * @throws \Exception
     */
    public function config_is_not_empty()
    {
        $this->assertNotEmpty(App::get('config'));
    }

    /** @test
     * @throws \Exception
     */
    public function database_is_not_empty()
    {
        $this->assertNotEmpty(App::DB());
    }

    /** @test
     * @throws \Exception
     */
    public function users_store()
    {
        $testUser = App::DB()->insert('users', [
            'name' => 'TestUser'
        ]);
        $secondUser = App::DB()->insert('users', [
            'name' => 'SecondUser'
        ]);
        $this->assertNotEmpty($testUser);
        $this->assertNotEmpty($secondUser);
    }

    /** @test
     * @throws \Exception
     */
    public function users_index()
    {
        $users = App::DB()->selectAll('users');
        $this->assertNotEmpty($users);
    }

    /** @test
     * @throws \Exception
     */
    public function users_limit()
    {
        $count = 2;
        $users = App::DB()->selectAll('users', $count);
        //echo App::DB()->getSql();
        $this->assertCount($count, $users);
    }

    /** @test
     * @throws \Exception
     */
    public function user_show()
    {
        $user = App::DB()->selectAllWhere('users', [
            ['name', '=', 'TestUser'],
        ]);
        //echo App::DB()->getSql();
        $this->assertNotEmpty($user);
    }

    /** @test
     * @throws \Exception
     */
    public function user_update()
    {
        $user = App::DB()->updateWhere('users', [
            'name' => 'FirstUser'
        ], [
            ['name', '=', 'TestUser']
        ]);
        $this->assertNotEmpty($user);
        $renamedUser = App::DB()->selectAllWhere('users', [
            ['name', '=', 'FirstUser'],
        ]);
        $this->assertEquals($renamedUser[0]->name, 'FirstUser');
    }

    /** @test */
    public function users_delete()
    {
        $firstUser = App::DB()->deleteWhere('users', [
            ['name', '=', 'FirstUser'],
        ]);
        $this->assertNotEmpty($firstUser);
        $firstDeletedUser = App::DB()->selectAllWhere('users', [
            ['name', '=', 'FirstUser'],
        ]);
        $this->assertEmpty($firstDeletedUser);
        $secondUser = App::DB()->deleteWhere('users', [
            ['name', '=', 'SecondUser'],
        ]);
        $this->assertNotEmpty($secondUser);
        $secondDeletedUser = App::DB()->selectAllWhere('users', [
            ['name', '=', 'SecondUser'],
        ]);
        $this->assertEmpty($secondDeletedUser);
    }

    /** @test */
    public function user_model_add()
    {
        $user = new User();
        $user = $user->add(['name' => 'TestUser']);
        $this->assertEquals($user->first()->name, 'TestUser');
        $user = $user->where([['name', '=', 'TestUser']])->first();
        $this->assertEquals($user->name, 'TestUser');
    }

    /** @test */
    public function user_model_index()
    {
        $users = User::all();
        $this->assertNotEmpty($users);
    }

    /** @test */
    public function user_model_first_or_fail()
    {
        $user = new User();
        $user->where([['name', '=', 'TestUser']])->firstOrFail();
        $this->assertNotEmpty($user);
    }

    /** @test */
    public function user_model_find()
    {
        $user = new User();
        $user = $user->add(['name' => 'NewUserHere']);
        $newUser = $user->find($user->first()->id());
        $this->assertNotEmpty($newUser);
        $newUser->deleteWhere([[$user->first()->primary(), '=', $user->first()->id()]]);
        $user = $user->find(-1);
        $this->assertNull($user);
    }

    /** @test */
    public function user_model_find_or_fail()
    {
        $user = new User();
        $this->expectExceptionMessage("ModelNotFoundException");
        $user->findOrFail(-1);
    }

    /** @test */
    public function users_raw_query()
    {
        $unnamedUsers = App::DB()->raw('SELECT * FROM users WHERE id > ?', [0]);
        $this->assertNotEmpty(count($unnamedUsers));
        $namedUsers = App::DB()->raw('SELECT * FROM users WHERE id > :id', ['id' => 0]);
        $this->assertNotEmpty(count($namedUsers));
        $newUser = App::DB()->raw('INSERT INTO users(name) VALUES (?)', ['TestingUser']);
        $this->assertNotEmpty($newUser);
        $deleteUser = App::DB()->raw('DELETE FROM users WHERE name = :name', ['name' => 'TestingUser']);
        $this->assertNotEmpty($deleteUser);
        $deletedUser = App::DB()->raw('SELECT * FROM users WHERE name = ?', ['TestingUser']);
        $this->assertEmpty(count($deletedUser));
    }

    /** @test */
    public function user_model_update()
    {
        $user = new User();
        $user->updateWhere(['name' => 'SomeUser'], [['name', '=', 'TestUser']]);
        $user = $user->where([['name', '=', 'SomeUser']])->first();
        $this->assertEquals($user->name, 'SomeUser');
    }

    /** @test */
    public function user_model_save()
    {
        $user = new User();
        $user = $user->where([['name', '=', 'SomeUser']])->first();
        $this->assertEquals($user->name, 'SomeUser');
        $user->name = 'ThisUser';
        $user->save();
        $this->assertEquals($user->name, 'ThisUser');
    }

    /** @test */
    public function user_model_delete()
    {
        $user = new User();
        $user->deleteWhere([['name', '=', 'ThisUser']]);
        $deletedUser = $user->where([['name', '=', 'ThisUser']])->first();
        $this->assertEmpty($deletedUser);
    }
}
