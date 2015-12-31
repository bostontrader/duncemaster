<?php
namespace App\Test\Fixture;

class TeachersFixture extends DMFixture {
    public $import = ['table' => 'teachers'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newTeacherRecord = [
        'fam_name'=>'Smith',
        'giv_name' => 'Sally',
        'user_id'=>FixtureConstants::userSallyStudentId
    ];

    public function init() {
        $this->tableName='Teachers';
        parent::init(); // This is where the records are loaded.
    }
}