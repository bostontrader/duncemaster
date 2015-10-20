<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class TeachersFixture extends TestFixture {
    public $import = ['table' => 'teachers'];

    // This record is injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $teacher1Record = [
        'id'=>FixtureConstants::teacher1_id,
        'giv_name' => 'Jack'
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newTeacherRecord = ['giv_name' => 'Sally'];

    public function init() {
        $this->records = [
            $this->teacher1Record
        ];
        parent::init();
    }
}