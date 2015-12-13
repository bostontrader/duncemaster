<?php
namespace App\Test\Fixture;

class StudentsFixture extends DMFixture {
    public $import = ['table' => 'students'];

    // This record is injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $student1Record = [
        'id'=>FixtureConstants::student1_id,
        'cohort_id'=>FixtureConstants::cohort1_id,
        'sid'=>'2014010101',
        'fam_name' => 'Smith', 'giv_name' => 'Adam',
        'user_id'=>FixtureConstants::userSallyStudentId
    ];

    public $student2Record = [
        'id'=>FixtureConstants::student2_id,
        'cohort_id'=>FixtureConstants::cohort2_id,
        'sid'=>'2015010101',
        'fam_name' => 'Rand', 'giv_name' => 'Ayn',
        'user_id'=>FixtureConstants::userTommyTeacherId
    ];

    public $student3Record = [
        'id'=>FixtureConstants::student3_id,
        'cohort_id'=>FixtureConstants::cohort1_id,
        'sid'=>'2016010101',
        'fam_name' => 'Heinlein', 'giv_name' => 'Robert',
        'user_id'=>FixtureConstants::userTommyTeacherId
    ];

    public $student4Record = [
        'id'=>FixtureConstants::student3_id,
        'cohort_id'=>FixtureConstants::cohort2_id,
        'sid'=>'2017010101',
        'fam_name' => 'Putin', 'giv_name' => 'Vladimir',
        'user_id'=>FixtureConstants::userTommyTeacherId
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newStudentRecord = [
        'cohort_id'=>FixtureConstants::cohort2_id,
        'sid'=>'2014010202',
        'fam_name' => 'Heinlein', 'giv_name' => 'Robert',
        'user_id'=>FixtureConstants::userSallyStudentId
    ];

    public function init() {
        $this->records = [
            $this->student1Record,
            $this->student2Record
        ];
        parent::init();
    }
}