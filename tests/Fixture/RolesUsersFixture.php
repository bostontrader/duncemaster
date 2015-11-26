<?php
namespace App\Test\Fixture;

class RolesUsersFixture extends DMFixture {
    public $import = ['table' => 'roles_users'];

    //        admin advisor teacher student
    // andy     x
    // arnold        x
    // tommy                 x
    // sally                            x

    public function init() {
        $this->records = [
            ['id'=>1,'role_id'=>FixtureConstants::roleAdminId,'user_id'=>FixtureConstants::userAndyAdminId],
            ['id'=>2,'role_id'=>FixtureConstants::roleAdvisorId,'user_id'=>FixtureConstants::userArnoldAdvisorId],
            ['id'=>3,'role_id'=>FixtureConstants::roleStudentId,'user_id'=>FixtureConstants::userSallyStudentId],
            ['id'=>4,'role_id'=>FixtureConstants::roleTeacherId,'user_id'=>FixtureConstants::userTommyTeacherId]
        ];
        parent::init();
    }

}