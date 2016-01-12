<?php
namespace App\Test\Fixture;

class SectionsFixture extends DMFixture {
    public $import = ['table' => 'sections'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newSectionRecord = [
        'cohort_id'=>FixtureConstants::cohortTypical,
        'semester_id'=>FixtureConstants::semesterTypical,
        'teacher_id'=>FixtureConstants::teacherTypical,
        'seq'=>1,
        'subject_id'=>FixtureConstants::subjectTypical,
        'tplan_id'=>FixtureConstants::tplanTypical,
        'weekday' => 'tue',
        'start_time' => '09:30',
        'thours' => '3'
    ];

    public function init() {
        $this->tableName='Sections';
        $this->joinTableName='Semesters';

        // Ensure that the ordering produced here matches the ordering in SectionsController->index.
        $this->order=['Semesters.year','Sections.id'];

        parent::init(); // This is where the records are loaded.
    }
}