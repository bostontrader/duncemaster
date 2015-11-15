<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class RolesFixture extends TestFixture {
    public $import = ['table' => 'roles'];

    // These records are injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $roleAdminRecord = [
        'id'=>FixtureConstants::roleAdminId,
        'title'=>'admin'
    ];

    public $roleAdvisorRecord = [
        'id'=>FixtureConstants::roleAdvisorId,
        'title'=>'advisor'
    ];

    public $roleStudentRecord = [
        'id'=>FixtureConstants::roleStudentId,
        'title'=>'student'
    ];

    public $roleTeacherRecord = [
        'id'=>FixtureConstants::roleTeacherId,
        'title'=>'teacher'
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newRoleRecord = [
        'title'=>'workerBee'
    ];

    public function init() {
        $this->records = [
            $this->roleAdminRecord,
            $this->roleAdvisorRecord,
            $this->roleTeacherRecord,
            $this->roleStudentRecord
        ];
        parent::init();
    }

}