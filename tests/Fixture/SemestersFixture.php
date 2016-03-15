<?php
namespace App\Test\Fixture;
use Cake\TestSuite\Fixture\TestFixture;
class SemestersFixture extends DMFixture {
    public $import = ['table' => 'semesters'];
    // This record is injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    //public $semester1Record = [
        //'id'=>FixtureConstants::semester_2016_1,
        //'year' => 2016, 'seq' => 1,
        //'firstday'=>'2016-03-01'
    //];
    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newSemesterRecord = [
        'year' => 2016, 'seq' => 2,
        'firstday'=>'2016-09-08'
    ];

    public function init() {
        $this->records = [
            [
                'id'=>FixtureConstants::SEMESTER_2016_1_ID,
                'year' => 2016, 'seq' => 1,
                'firstday'=>'2016-03-01'
            ]
        ];
        //parent::init();
    }
}
