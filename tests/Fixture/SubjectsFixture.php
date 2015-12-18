<?php
namespace App\Test\Fixture;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

class SubjectsFixture extends DMFixture {
    public $import = ['table' => 'subjects'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newSubjectRecord = ['title' => 'Advanced Lion Taming'];

    // We obtain the records to use for this fixture by reading them from the 'fixture' db
    public function init() {

        // We need to do this to ensure that the Subjects table really does use this connection.
        TableRegistry::remove('Subjects');
        $subjects = TableRegistry::get('Subjects',['connection'=>ConnectionManager::get('fixture')]);

        $query=new Query(ConnectionManager::get('fixture'),$subjects);
        $query->from('Subjects')->find('all');

        /* @var \Cake\ORM\Entity $record */
        foreach($query as $record) {
            $this->records[]=$record->toArray();
        }

        // Do this again to ensure that the Subjects table uses the 'test' connection.
        TableRegistry::remove('Subjects');
        TableRegistry::get('Subjects',['connection'=>ConnectionManager::get('test')]);

        parent::init();
    }
}