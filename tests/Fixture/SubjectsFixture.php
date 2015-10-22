<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class SubjectsFixture extends TestFixture {
    public $import = ['table' => 'subjects'];

    // This record is injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $subject1Record = [
        'id'=>FixtureConstants::subject1_id,
        'title' => 'Lion Taming'
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newSubjectRecord = ['title' => 'Advanced Lion Taming'];

    public function init() {
        $this->records = [
            $this->subject1Record
        ];
        parent::init();
    }
}