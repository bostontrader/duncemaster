<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class InteractionsFixture extends TestFixture {
    public $import = ['table' => 'interactions'];

    // This record is injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $interaction1Record = [
        'id'=>FixtureConstants::interaction1_id,
        'clazz_id'=>FixtureConstants::clazz1_id,
        'student_id'=>FixtureConstants::student1_id
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newInteractionRecord = [
        'clazz_id'=>FixtureConstants::clazz2_id,
        'student_id'=>FixtureConstants::student2_id
    ];

    public function init() {
        $this->records = [
            $this->interaction1Record
        ];
        parent::init();
    }
}