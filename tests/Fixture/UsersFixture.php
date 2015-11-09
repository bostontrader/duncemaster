<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersFixture extends TestFixture {
    public $import = ['table' => 'users'];

    // This record is injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $user1Record = [
        'id'=>FixtureConstants::user1_id,
        'username' => 'admin'
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newUserRecord = ['username' => 'billy'];

    public function init()
    {
        $this->records = [
            $this->user1Record
        ];
        parent::init();
    }
}
