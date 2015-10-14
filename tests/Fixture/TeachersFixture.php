<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class TeachersFixture extends TestFixture {
    public $import = ['table' => 'teachers'];

    // Specify id because later we'll try to specifically find this record in the db
    public $teacher1Record = ['id'=>5,  'giv_name' => 'Jack'];

    // This is a new record to be inserted by Cake's patchEntity method. We can't
    // feasibly control the id, so go with the flow.  But we _can_ predict what
    // the new ID will be, and we'll need that to read back this record.
    public $newTeacherRecord = ['id'=>6,  'giv_name' => 'Sally'];

    public function init()
    {
        $this->records = [
            $this->teacher1Record
        ];
        parent::init();
    }
}