<?php
namespace App\Test\Fixture;

class ClazzesFixture extends DMFixture {
    public $import = ['table' => 'clazzes'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newClazzRecord = [
        'section_id' => FixtureConstants::section2_id,
        'event_datetime' => '2015-10-16 00:00:00',
        'comments' => 'comment new'
    ];

    public function init() {
        $this->tableName='Clazzes';
        parent::init(); // This is where the records are loaded.
    }

    // Given a $sectionId, remove all elements in $this->records that don't have the same $sectionId.
    public function filterBySectionId($sectionId) {
        $newRecords=[];
        foreach ($this->records as $record)
            if($record['section_id']==$sectionId) array_push($newRecords,$record);
        $this->records=$newRecords;
    }

}
