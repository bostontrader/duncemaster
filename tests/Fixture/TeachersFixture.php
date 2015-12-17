<?php
namespace App\Test\Fixture;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

class TeachersFixture extends DMFixture {
    public $import = ['table' => 'teachers'];

    // This record is injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $teacher1Record = [
        'id'=>FixtureConstants::teacher1_id,
        'fam_name' => 'Goff',
        'giv_name' => 'Jack',
        'user_id'=>FixtureConstants::userTommyTeacherId
    ];

    public $teacher2Record = [
        'id'=>FixtureConstants::teacher2_id,
        'fam_name' => 'Heinlein',
        'giv_name' => 'Robert',
        'user_id' => null // no user_id, need to test what happens
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newTeacherRecord = [
        'fam_name'=>'Smith',
        'giv_name' => 'Sally',
        'user_id'=>FixtureConstants::userSallyStudentId
    ];

    public function init() {
        $this->records = [
            //$this->teacher1Record,
            //$this->teacher2Record
        ];
        $connection = ConnectionManager::get('fixture');
        $teachers = TableRegistry::get('Teachers');
        $query=$teachers->find()->connection($connection);

        foreach($query as $record) {
            $n=$record->toArray();
            //$newRecord['id']=$clazzRecord->id;
            //$newRecord['section_id']=$clazzRecord->section_id;
            //$newRecord['event_datetime']=$clazzRecord->event_datetime;
            //$newRecord['comments']=$clazzRecord->comments;
            $this->records[]=$n;
        }
        parent::init();
    }
}