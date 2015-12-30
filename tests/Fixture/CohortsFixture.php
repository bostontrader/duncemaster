<?php
namespace App\Test\Fixture;

class CohortsFixture extends DMFixture {
    public $import = ['table' => 'cohorts'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newCohortRecord = [
        'major_id'=>FixtureConstants::majorTypical,
        'start_year' => 2016, 'seq' => 2
    ];

    public function init() {
        $this->tableName='Cohorts';
        parent::init(); // This is where the records are loaded.
    }

}