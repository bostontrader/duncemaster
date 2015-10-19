<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class SemestersFixture extends TestFixture {
    public $import = ['table' => 'semesters'];

    // This record is injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $semester1Record = [
        'id'=>FixtureConstants::semester1_id,
        'year' => 2015, 'seq' => 1
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newSemesterRecord = ['year' => 2016, 'seq' => 2];

    public function init()
    {
        $this->records = [
            $this->semester1Record
        ];
        parent::init();
    }
}