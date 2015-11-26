<?php
namespace App\Test\Fixture;

class SemestersFixture extends DMFixture {
    public $import = ['table' => 'semesters'];

    // These records are injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $semester1Record = [
        'id'=>FixtureConstants::semester1_id,
        'year' => 2015, 'seq' => 1,
        'firstday'=>'2015-09-07'
    ];

    public $semester2Record = [
        'id'=>FixtureConstants::semester2_id,
        'year' => 2015, 'seq' => 2,
        'firstday'=>'2016-09-07'
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newSemesterRecord = [
        'year' => 2016, 'seq' => 2,
        'firstday'=>'2015-09-08'
    ];

    public function init() {
        $this->records = [
            $this->semester1Record,
            $this->semester2Record,
        ];
        parent::init();
    }
}