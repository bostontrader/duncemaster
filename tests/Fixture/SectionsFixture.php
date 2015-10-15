<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class SectionsFixture extends TestFixture {
    public $import = ['table' => 'sections'];

    // Specify id because later we'll try to specifically find this record in the db
    public $section1Record = ['id'=>5,  'weekday' => 1];

    // This is a new record to be inserted by Cake's patchEntity method. We can't
    // feasibly control the id, so go with the flow.  But we _can_ predict what
    // the new ID will be, and we'll need that to read back this record.
    public $newSectionRecord = ['id'=>6,  'weekday' => 2];

    public function init()
    {
        $this->records = [
            $this->section1Record
        ];
        parent::init();
    }
}