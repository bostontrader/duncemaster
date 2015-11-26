<?php
namespace App\Test\Fixture;

class MajorsFixture extends DMFixture {
    public $import = ['table' => 'majors'];

    // These records are injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $major1Record = [
        'id'=>FixtureConstants::major1_id,
        'title' => 'Lion Taming', 'sdesc' => 'LT'
    ];

    public $major2Record = [
        'id'=>FixtureConstants::major2_id,
        'title' => 'Cat Juggling', 'sdesc' => 'CJ'
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newMajorRecord = ['title' => 'Advanced Lion Taming', 'sdesc' => 'AT'];

    public function init() {
        $this->records = [
            $this->major1Record,
            $this->major2Record,
        ];
        parent::init();
    }

}