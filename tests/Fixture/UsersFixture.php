<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersFixture extends TestFixture {
    public $import = ['table' => 'users'];

    // These records are injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $userAndyRecord = [
        'id'=>FixtureConstants::userAndyId,
        'username' => 'andy',
        'password' => 'passwordAndy'
    ];

    public $userSallyRecord = [
        'id'=>FixtureConstants::userSallyId,
        'username' => 'sally',
        'password' => 'passwordSally'
    ];

    public $userTommyRecord = [
        'id'=>FixtureConstants::userTommyId,
        'username' => 'tommy',
        'password' => 'passwordTommy'
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newUserRecord = ['username' => 'billy', 'password' => 'passwordBilly'];

    public function init()
    {
        $this->records = [
            $this->userAndyRecord,
            $this->userSallyRecord,
            $this->userTommyRecord
        ];
        parent::init();
    }
}
