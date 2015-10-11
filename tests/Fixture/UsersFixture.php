<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 *
 */
class UsersFixture extends TestFixture {
    public $import = ['table' => 'users'];

    // Specify id because later we'll try to specifically find this record in the db
    public $adminRecord = ['id'=>5,  'username' => 'admin'];

    // This is a new record to be inserted by Cake's patchEntity method. We can't
    // feasibly control the id, so go with the flow.  But we _can_ predict what
    // the new ID will be, and we'll need that to read back this record.
    public $newUserRecord = ['id'=>6,  'username' => 'billy'];

    public function init()
    {
        $this->records = [
            $this->adminRecord
        ];
        parent::init();
    }
}
