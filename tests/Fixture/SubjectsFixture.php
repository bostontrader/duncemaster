<?php
namespace App\Test\Fixture;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

class SubjectsFixture extends DMFixture {
    public $import = ['table' => 'subjects'];

    // These record are injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    /*public $subject1Record = [
        'id'=>FixtureConstants::subject1_id,
        'title' => 'Lion Taming'
    ];

    public $subject2Record = [
        'id'=>FixtureConstants::subject2_id,
        'title' => 'Cat Juggling'
    ];*/

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newSubjectRecord = ['title' => 'Advanced Lion Taming'];

    public function init() {
        $this->records = [
            //$this->subject1Record,
            //$this->subject2Record
        ];

        $connection = ConnectionManager::get('fixture');
        TableRegistry::remove('Subjects');
        $subjects = TableRegistry::get('Subjects',['connection'=>$connection]);
        //$query=$clazzes->find();
        $query=new \Cake\ORM\Query($connection,$subjects);
        $query->from('Subjects')->find('all');

        foreach($query as $record) {
            $n=$record->toArray();
            //$newRecord['id']=$clazzRecord->id;
            //$newRecord['section_id']=$clazzRecord->section_id;
            //$newRecord['event_datetime']=$clazzRecord->event_datetime;
            //$newRecord['comments']=$clazzRecord->comments;
            $this->records[]=$n;
        }
        TableRegistry::remove('Subjects');
        $connection = ConnectionManager::get('test');
        TableRegistry::get('Subjects',['connection'=>$connection]);

        //TableRegistry::config('Subject', ['connection' => $connection]);
        parent::init();
    }
}