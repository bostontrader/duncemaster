<?php
namespace App\Test\Fixture;

class SemestersFixture extends DMFixture {
    public $import = ['table' => 'semesters'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newSemesterRecord = [
        'year' => 2016, 'seq' => 2,
        'firstday'=>'2015-09-08'
    ];

    public function init() {
        $this->tableName='Semesters';
        $this->order=['year','seq'];
        parent::init(); // This is where the records are loaded.
    }


}