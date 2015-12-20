<?php
namespace App\Test\Fixture;

class SectionsFixture extends DMFixture {
    public $import = ['table' => 'sections'];

    // These records are injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    /*public $section1Record = [
        'id'=>FixtureConstants::section11_id,
        'cohort_id'=>FixtureConstants::cohort1_id,
        'semester_id'=>FixtureConstants::semester1_id,
        'subject_id'=>FixtureConstants::subject1_id,
        'tplan_id'=>FixtureConstants::tplan1_id,
        'weekday' => 'mon',
        'start_time' => '08:30',
        'thours' => '2'
    ];

    public $section2Record = [
        'id'=>FixtureConstants::section2_id,
        'cohort_id'=>FixtureConstants::cohort2_id,
        'semester_id'=>FixtureConstants::semester2_id,
        'subject_id'=>FixtureConstants::subject2_id,
        'tplan_id'=>FixtureConstants::tplan2_id,
        'weekday' => 'tue',
        'start_time' => '08:30',
        'thours' => '2'
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newSectionRecord = [
        'cohort_id'=>FixtureConstants::cohort2_id,
        'semester_id'=>FixtureConstants::semester2_id,
        'subject_id'=>FixtureConstants::subject2_id,
        'tplan_id'=>FixtureConstants::tplan2_id,
        'weekday' => 'tue',
        'start_time' => '09:30',
        'thours' => '3'
    ];*/

    public function init() {
        $this->tableName='Sections';
        parent::init(); // This is where the records are loaded.
    }
}