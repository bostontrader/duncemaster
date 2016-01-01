<?php
namespace App\Test\Fixture;

use App\Controller\ItypesController;

class InteractionsFixture extends DMFixture {
    public $import = ['table' => 'interactions'];

    // The pattern of fixture record creation, for this fixture, is
    // different than that for the other fixtures.
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
    /*public $interaction1Record = [
        'id'=>FixtureConstants::interaction1_id,
        'clazz_id'=>FixtureConstants::clazz4_id,
        'student_id'=>FixtureConstants::student4_id,
        'itype_id'=>ItypesController::ATTEND
    ];*/

    // This record will be added during the POST /add test.  We don't need or want to control the id here, so omit it.
    public $newInteractionRecord = [
        'clazz_id'=>FixtureConstants::clazzTypical,
        'student_id'=>FixtureConstants::studentTypical,
        'itype_id'=>ItypesController::EJECT,
        'participate'=>null
    ];


    public function init() {
        $this->tableName='Interactions';
        parent::init(); // This is where the records are loaded.
    }

    // Given a $clazz_id, remove all elements in $this->records that don't have the same $clazz_id.
    /*public function filterByClazzId($clazz_id) {
        $newRecords=[];

        foreach ($this->records as $record)
            if ($record['clazz_id'] == $clazz_id)
                array_push($newRecords, $record);

        $this->records=$newRecords;
    }*/

}