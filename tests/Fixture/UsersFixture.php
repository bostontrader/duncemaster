<?php
namespace App\Test\Fixture;

class UsersFixture extends DMFixture {
    public $import = ['table' => 'users'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newUserRecord = ['username' => 'billy', 'password' => 'passwordBilly'];

    public function init() {
        $this->tableName='Users';
        parent::init(); // This is where the records are loaded.
    }
}
