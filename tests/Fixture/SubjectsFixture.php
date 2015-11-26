<?php
namespace App\Test\Fixture;

class SubjectsFixture extends DMFixture {
    public $import = ['table' => 'subjects'];

    // These record are injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $subject1Record = [
        'id'=>FixtureConstants::subject1_id,
        'title' => 'Lion Taming'
    ];

    public $subject2Record = [
        'id'=>FixtureConstants::subject2_id,
        'title' => 'Cat Juggling'
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newSubjectRecord = ['title' => 'Advanced Lion Taming'];

    public function init() {
        $this->records = [
            $this->subject1Record,
            $this->subject2Record
        ];
        parent::init();
    }
}