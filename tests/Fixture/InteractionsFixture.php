<?php
namespace App\Test\Fixture;
use App\Controller\ItypesController;

class InteractionsFixture extends DMFixture {
    public $import = ['table' => 'interactions'];

    // This record is injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $interaction1Record = [
        'id'=>FixtureConstants::interaction1_id,
        'clazz_id'=>FixtureConstants::clazz1_id,
        'student_id'=>FixtureConstants::student1_id,
        'itype_id'=>ItypesController::ATTEND
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newInteractionRecord = [
        'clazz_id'=>FixtureConstants::clazz2_id,
        'student_id'=>FixtureConstants::student2_id,
        'itype_id'=>ItypesController::EJECT
    ];

    public function init() {
        $this->records = [
            $this->interaction1Record
        ];

        // Add additional records for the grading tests.
        // We are tempted to omit the id because we don't directly need it.
        // However, there is a bug whereby the ordinary auto-increment sequence
        // will have a skipped value. This fubars our test.
        $nextId=FixtureConstants::interaction1_id+1;
        $this->records=array_merge(
            [$this->interaction1Record],
            [
                [
                    'id'=>$nextId++,
                    'clazz_id'=>FixtureConstants::clazz1_id,
                    'student_id'=>FixtureConstants::student1_id,
                    'itype_id'=>ItypesController::EJECT
                ],
                [
                    'id'=>$nextId++,
                    'clazz_id'=>FixtureConstants::clazz1_id,
                    'student_id'=>FixtureConstants::student1_id,
                    'itype_id'=>ItypesController::LEAVE
                ],
                [
                    'id'=>$nextId++,
                    'clazz_id'=>FixtureConstants::clazz1_id,
                    'student_id'=>FixtureConstants::student1_id,
                    'itype_id'=>ItypesController::PARTICIPATE
                ],/*
                [
                    'id'=>$nextId++,
                    'clazz_id'=>FixtureConstants::clazz1_id,
                    'student_id'=>FixtureConstants::student1_id,
                    'itype_id'=>ItypesController::EXCUSED
                ],*/
                [
                    'id'=>$nextId++,
                    'clazz_id'=>FixtureConstants::clazz1_id,
                    'student_id'=>FixtureConstants::student2_id,
                    'itype_id'=>ItypesController::ATTEND
                ],
                [
                    'id'=>$nextId++,
                    'clazz_id'=>FixtureConstants::clazz1_id,
                    'student_id'=>FixtureConstants::student2_id,
                    'itype_id'=>ItypesController::EJECT
                ]/*,
                [
                    'id'=>$nextId++,
                    'clazz_id'=>FixtureConstants::clazz1_id,
                    'studet_id'=>FixtureConstants::student2_id,
                    'itype_id'=>ItypesController::LEAVE
                ],
                [
                    'id'=>$nextId++,
                    'clazz_id'=>FixtureConstants::clazz1_id,
                    'student_id'=>FixtureConstants::student2_id,
                    'itype_id'=>ItypesController::PARTICIPATE
                ],
                [
                    'id'=>$nextId++,
                    'clazz_id'=>FixtureConstants::clazz1_id,
                    'student_id'=>FixtureConstants::student2_id,
                    'itype_id'=>ItypesController::EXCUSED
                ]*/
            ]
        );
        parent::init();
    }
}