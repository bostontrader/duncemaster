<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class UsersFixture extends TestFixture {
    public $import = ['table' => 'users'];

    // These records are injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $userAndyAdminRecord = [
        'id'=>FixtureConstants::userAndyAdminId,
        'username' => 'AndyAdmin',
        'password' => 'passwordAndyAdmin'
    ];

    public $userArnoldAdvisorRecord = [
        'id'=>FixtureConstants::userArnoldAdvisorId,
        'username' => 'ArnoldAdvisor',
        'password' => 'passwordArnoldAdvisor'
    ];

    public $userSallyStudentRecord = [
        'id'=>FixtureConstants::userSallyStudentId,
        'username' => 'SallyStudent',
        'password' => 'passwordSallyStudent'
    ];

    public $userTommyTeacherRecord = [
        'id'=>FixtureConstants::userTommyTeacherId,
        'username' => 'TommyTeacher',
        'password' => 'passwordTommyTeacher'
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newUserRecord = ['username' => 'billy', 'password' => 'passwordBilly'];

    public function init()
    {
        $this->records = [
            $this->userAndyAdminRecord,
            $this->userArnoldAdvisorRecord,
            $this->userSallyStudentRecord,
            $this->userTommyTeacherRecord
        ];
        parent::init();
    }

    // Given an id, return the first fixture record found with that id, or null if not found.
    public function get($id) {
        foreach ($this->records as $record)
            if ($record['id'] == $id) return $record;
        return null;
    }

}
