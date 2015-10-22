<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class StudentsFixture extends TestFixture {
    public $import = ['table' => 'students'];

    // This record is injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $student1Record = [
        'id'=>FixtureConstants::student1_id,
        'cohort_id'=>FixtureConstants::cohort1_id,
        'sid'=>'2014010101',
        'fam_name' => 'Smith', 'giv_name' => 'John'
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newStudentRecord = [
        'cohort_id'=>FixtureConstants::cohort2_id,
        'sid'=>'2014010202',
        'fam_name' => 'Jones', 'giv_name' => 'Billy'
    ];

    public function init() {
        $this->records = [
            $this->student1Record
        ];
        parent::init();
    }
}