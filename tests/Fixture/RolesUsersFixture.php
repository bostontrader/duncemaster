<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class RolesUsersFixture extends TestFixture {
    public $import = ['table' => 'roles_users'];

    //        admin advisor teacher student
    // andy     x
    // sally          x
    // tommy                 x        x

    // These records are injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    //public $roleAdminRecord = [
    //'id'=>FixtureConstants::roleAdminId,
    //'title'=>'admin'
    //];



    public function init() {
        $this->records = [
            ['id'=>1,'role_id'=>FixtureConstants::roleAdminId,'user_id'=>'',],
        //$this->roleAdminRecord,
        //$this->roleTeacherRecord,
        //$this->roleStudentRecord
        ];
        //parent::init();
    }

}