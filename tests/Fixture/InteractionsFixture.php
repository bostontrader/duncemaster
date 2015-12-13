<?php
namespace App\Test\Fixture;
use App\Controller\ItypesController;

class InteractionsFixture extends DMFixture {
    public $import = ['table' => 'interactions'];

    private $nextId;

    // The pattern of fixture record creation, for this fixture, is
    // different that for the other fixtures.
    //
    // Here, we retain the public
    // members $interaction1Record and $newInteractionRecord, as well as
    // FixtureConstants::interaction1_id, for CRUD testing.
    //
    // In addition, during init, we add numerous other records, so that we have sufficient
    // variation to test the various edges and corners of generating attendance and scoring
    // information. The quantity of required records is so large that we don't want to
    // define specific static ids in FixtureConstants.

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

        // Add additional records for the attendance and scoring tests.
        // We are tempted to omit the id because we don't directly need it.
        // However, there is a bug whereby the ordinary auto-increment sequence
        // will have a skipped value. This fubars our test.
        
        // WARNING: Make sure clazz->section->cohort = student->cohort!
        $this->nextId=FixtureConstants::interaction1_id+1;
        $this->records=array_merge(
            [$this->interaction1Record],
            [
                [
                    'id'=>$this->nextId++,
                    'clazz_id'=>FixtureConstants::clazz1_id,
                    'student_id'=>FixtureConstants::student1_id,
                    'itype_id'=>ItypesController::ATTEND
                ],
                [
                    'id'=>$this->nextId++,
                    'clazz_id'=>FixtureConstants::clazz1_id,
                    'student_id'=>FixtureConstants::student3_id,
                    'itype_id'=>ItypesController::ATTEND
                ],
                [
                    'id'=>$this->nextId++,
                    'clazz_id'=>FixtureConstants::clazz2_id,
                    'student_id'=>FixtureConstants::student2_id,
                    'itype_id'=>ItypesController::ATTEND
                ],
                [
                    'id'=>$this->nextId++,
                    'clazz_id'=>FixtureConstants::clazz2_id,
                    'student_id'=>FixtureConstants::student4_id,
                    'itype_id'=>ItypesController::ATTEND
                ],
                [
                    'id'=>$this->nextId++,
                    'clazz_id'=>FixtureConstants::clazz1_id,
                    'student_id'=>FixtureConstants::student1_id,
                    'itype_id'=>ItypesController::EJECT
                ],
                [
                    'id'=>$this->nextId++,
                    'clazz_id'=>FixtureConstants::clazz2_id,
                    'student_id'=>FixtureConstants::student2_id,
                    'itype_id'=>ItypesController::LEAVE
                ],
                [
                    'id'=>$this->nextId++,
                    'clazz_id'=>FixtureConstants::clazz2_id,
                    'student_id'=>FixtureConstants::student4_id,
                    'itype_id'=>ItypesController::PARTICIPATE
                ],/*
                [
                    'id'=>$this->nextId++,
                    'clazz_id'=>FixtureConstants::clazz1_id,
                    'student_id'=>FixtureConstants::student1_id,
                    'itype_id'=>ItypesController::EXCUSED
                ],*/
                [
                    'id'=>$this->nextId++,
                    'clazz_id'=>FixtureConstants::clazz2_id,
                    'student_id'=>FixtureConstants::student2_id,
                    'itype_id'=>ItypesController::ATTEND
                ],
                [
                    'id'=>$this->nextId++,
                    'clazz_id'=>FixtureConstants::clazz2_id,
                    'student_id'=>FixtureConstants::student2_id,
                    'itype_id'=>ItypesController::EJECT
                ]/*,
                [
                    'id'=>$this->nextId++,
                    'clazz_id'=>FixtureConstants::clazz1_id,
                    'student_id'=>FixtureConstants::student2_id,
                    'itype_id'=>ItypesController::LEAVE
                ],
                [
                    'id'=>$this->nextId++,
                    'clazz_id'=>FixtureConstants::clazz1_id,
                    'student_id'=>FixtureConstants::student2_id,
                    'itype_id'=>ItypesController::PARTICIPATE
                ],
                [
                    'id'=>$this->nextId++,
                    'clazz_id'=>FixtureConstants::clazz1_id,
                    'student_id'=>FixtureConstants::student2_id,
                    'itype_id'=>ItypesController::EXCUSED
                ]*/
            ]
        );
        parent::init();
    }

    // Given a $clazz_id, remove all elements in $this->records that don't have the same $clazz_id.
    public function filterByClazzId($clazz_id) {
        $newRecords=[];

        foreach ($this->records as $record)
            if ($record['clazz_id'] == $clazz_id)
                array_push($newRecords, $record);

        $this->records=$newRecords;
    }

}