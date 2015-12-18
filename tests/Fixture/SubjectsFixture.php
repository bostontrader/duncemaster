<?php
namespace App\Test\Fixture;

class SubjectsFixture extends DMFixture {
    public $import = ['table' => 'subjects'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newSubjectRecord = ['title' => 'Advanced Lion Taming'];

    public function init() {
        $this->tableName='Subjects';
        parent::init(); // This is where the records are loaded.
    }
}