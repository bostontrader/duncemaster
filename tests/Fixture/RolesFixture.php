<?php
namespace App\Test\Fixture;

class RolesFixture extends DMFixture {
    public $import = ['table' => 'roles'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newRoleRecord = [
        'title'=>'workerBee'
    ];

    public function init() {
        $this->tableName='Roles';
        parent::init(); // This is where the records are loaded.
    }

}