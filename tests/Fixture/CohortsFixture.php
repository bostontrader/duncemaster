<?php
namespace App\Test\Fixture;

class CohortsFixture extends DMFixture {
    public $import = ['table' => 'cohorts'];

    // These records are injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $cohort1Record = [
        'id'=>FixtureConstants::cohort1_id,
        'major_id'=>FixtureConstants::major1_id,
        'start_year' => 2015, 'seq' => 1
    ];

    public $cohort2Record = [
        'id'=>FixtureConstants::cohort2_id,
        'major_id'=>FixtureConstants::major2_id,
        'start_year' => 2015, 'seq' => 2
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newCohortRecord = [
        'major_id'=>FixtureConstants::major2_id,
        'start_year' => 2016, 'seq' => 2
    ];

    public function init() {
        $this->records = [
            $this->cohort1Record,
            $this->cohort2Record,
        ];
        parent::init();
    }

}