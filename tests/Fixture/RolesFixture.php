<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class RolesFixture extends TestFixture {
    public $import = ['table' => 'roles'];

    // This record is injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $role1Record = [
        'id'=>FixtureConstants::role1_id,
        'title'=>'admin'
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newRoleRecord = [
        'title'=>'workerBee'
    ];

    public function init() {
        $this->records = [
            $this->role1Record
        ];
        parent::init();
    }

}