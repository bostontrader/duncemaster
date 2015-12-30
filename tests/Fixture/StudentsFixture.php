<?php
namespace App\Test\Fixture;

class StudentsFixture extends DMFixture {
    public $import = ['table' => 'students'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newStudentRecord = [
        'cohort_id'=>FixtureConstants::cohortTypical,
        'sid'=>'2014010202',
        'fam_name' => 'Heinlein', 'giv_name' => 'Robert', 'phonetic_name' => 'Phonetic',
        'user_id'=>FixtureConstants::userSallyStudentId
    ];

    public function init() {
        $this->tableName='Students';
        parent::init(); // This is where the records are loaded.
    }
}