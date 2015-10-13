<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class SemestersFixture extends TestFixture {
    public $import = ['table' => 'semesters'];

    // Specify id because later we'll try to specifically find this record in the db
    public $semester1Record = ['id'=>5,  'year' => 2015, 'seq' => 1];

    // This is a new record to be inserted by Cake's patchEntity method. We can't
    // feasibly control the id, so go with the flow.  But we _can_ predict what
    // the new ID will be, and we'll need that to read back this record.
    public $newSemesterRecord = ['id'=>6,  'year' => 2016, 'seq' => 2];

    public function init()
    {
        $this->records = [
            $this->semester1Record
        ];
        parent::init();
    }
}