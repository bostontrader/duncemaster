<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class StudentsFixture extends TestFixture {
    public $import = ['table' => 'students'];

    // Specify id because later we'll try to specifically find this record in the db
    public $student1Record = ['id'=>5,  'fam_name' => 'Smith', 'giv_name' => 'John'];

    // This is a new record to be inserted by Cake's patchEntity method. We can't
    // feasibly control the id, so go with the flow.  But we _can_ predict what
    // the new ID will be, and we'll need that to read back this record.
    public $newStudentRecord = ['id'=>6,  'fam_name' => 'Jones', 'giv_name' => 'Sally'];

    public function init()
    {
        $this->records = [
            $this->student1Record
        ];
        parent::init();
    }
}