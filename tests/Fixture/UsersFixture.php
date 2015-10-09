<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 *
 */
class UsersFixture extends TestFixture {
    public $import = ['table' => 'users'];

    public $records = [
        [
            'username' => 'adminx'
        ]
    ];
}
