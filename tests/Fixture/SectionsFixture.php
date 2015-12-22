<?php
namespace App\Test\Fixture;

class SectionsFixture extends DMFixture {
    public $import = ['table' => 'sections'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newSectionRecord = [
        'cohort_id'=>FixtureConstants::cohort2_id,
        'semester_id'=>FixtureConstants::semester2_id,
        'subject_id'=>FixtureConstants::subject2_id,
        'tplan_id'=>FixtureConstants::tplan2_id,
        'weekday' => 'tue',
        'start_time' => '09:30',
        'thours' => '3'
    ];

    public function init() {
        $this->tableName='Sections';
        $this->joinTableName='Semesters';

        // Ensure that the ordering produced here matches the ordering in SectionsController->index.
        // We don't really need to sort on Sections.id, but we need some method of ensuring that the sort produced
        // here matches that from the controller. Reliance upon the default ordering is not reliable.
        $this->order=['Semesters.year','Sections.id'];

        parent::init(); // This is where the records are loaded.
    }
}