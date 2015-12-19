<?php
namespace App\Test\Fixture;

class MajorsFixture extends DMFixture {
    public $import = ['table' => 'majors'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newMajorRecord = ['title' => 'Advanced Lion Taming', 'sdesc' => 'AT'];

    public function init() {
        $this->tableName='Majors';
        parent::init(); // This is where the records are loaded.
    }
}