<?php
namespace App\Test\Fixture;

class ItypesFixture extends DMFixture {
    public $import = ['table' => 'itypes'];

    // These records are injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    /*public $itypeAttendRecord = [
        'id'=>FixtureConstants::itypeAttend_id,
        'title' => 'Attend'
    ];
    public $itypeEjectRecord = [
        'id'=>FixtureConstants::itypeEject_id,
        'title' => 'Eject'
    ];
    public $itypeLeaveRecord = [
        'id'=>FixtureConstants::itypeLeave_id,
        'title' => 'Leave'
    ];
    public $itypeParticipateRecord = [
        'id'=>FixtureConstants::itypeParticipate_id,
        'title' => 'Participate'
    ];
    public $itypeExcusedAbsenceRecord = [
        'id'=>FixtureConstants::itypeExcusedAbsence_id,
        'title' => 'Participate'
    ];*/
    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newItypeRecord = ['title' => 'Massacre classmates'];

    public function init() {
        $this->tableName='Itypes';
        parent::init(); // This is where the records are loaded.
    }
}