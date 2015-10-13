<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class RolsFixture extends TestFixture {
    public $import = ['table' => 'roles'];

    // Specify id because later we'll try to specifically find this record in the db
    public $role1Record = ['id'=>5,  'title' => 'Lion Taming', 'sdesc' => 'LT'];

    // This is a new record to be inserted by Cake's patchEntity method. We can't
    // feasibly control the id, so go with the flow.  But we _can_ predict what
    // the new ID will be, and we'll need that to read back this record.
    public $newRolRecord = ['id'=>6,  'title' => 'Advanced Lion Taming', 'sdesc' => 'AT'];

    public function init()
    {
        $this->records = [
            $this->role1Record
        ];
        parent::init();
    }
}