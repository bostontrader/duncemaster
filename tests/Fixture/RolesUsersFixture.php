<?php
namespace App\Test\Fixture;

class RolesUsersFixture extends DMFixture {
    public $import = ['table' => 'roles_users'];

    public function init() {
        $this->tableName='RolesUsers';
        parent::init(); // This is where the records are loaded.
    }
}
