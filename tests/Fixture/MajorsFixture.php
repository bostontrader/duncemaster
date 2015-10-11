<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class MajorsFixture extends TestFixture {
    public $import = ['table' => 'majors'];

    // Specify id because later we'll try to specifically find this record in the db
    public $major1Record = ['id'=>5,  'title' => 'Lion Taming', 'sdesc' => 'LT'];

    // This is a new record to be inserted by Cake's patchEntity method. We can't
    // feasibly control the id, so go with the flow.  But we _can_ predict what
    // the new ID will be, and we'll need that to read back this record.
    public $newMajorRecord = ['id'=>6,  'title' => 'Advanced Lion Taming', 'sdesc' => 'AT'];

    public function init()
    {
        $this->records = [
            $this->major1Record
        ];
        parent::init();
    }
}