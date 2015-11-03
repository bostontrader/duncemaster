<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class ClazzesFixture extends TestFixture {
    public $import = ['table' => 'clazzes'];

    // This record is injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $clazz1Record = [
        'id'=>FixtureConstants::clazz1_id,
        'section_id' => FixtureConstants::section1_id,
        'week' => '5',
        'event_datetime' => '2015-10-15 00:00:00'
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.

    public $newClazzRecord = [
        'section_id' => FixtureConstants::section2_id,
        'week' => '6',
        'event_datetime' => '2015-10-16 00:00:00'
    ];

    public function init() {
        $this->records = [
            $this->clazz1Record
        ];
        parent::init();
    }

    // Given an id, return the first fixture record found with that id, or null if not found.
    public function get($id) {
        foreach ($this->records as $record)
            if ($record['id'] == $id) return $record;
        return null;
    }
}