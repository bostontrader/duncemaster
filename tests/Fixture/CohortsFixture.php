<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class CohortsFixture extends TestFixture {
    public $import = ['table' => 'cohorts'];

    // This record is injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $cohort1Record = [
        'id'=>FixtureConstants::cohort1_id,
        'major_id'=>FixtureConstants::major1_id,
        'start_year' => 2015, 'seq' => 1
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newCohortRecord = [
        'major_id'=>FixtureConstants::major2_id,
        'start_year' => 2016, 'seq' => 2
    ];

    public function init() {
        $this->records = [
            $this->cohort1Record
        ];
        parent::init();
    }
}