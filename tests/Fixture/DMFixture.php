<?php
namespace App\Test\Fixture;

use Cake\Datasource\ConnectionManager;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\Fixture\TestFixture;

class DMFixture extends TestFixture {

    protected $tableName;
    protected $order;

    // Given an id, return the first fixture record found with that id, or null if not found.
    public function get($id) {
        foreach ($this->records as $record)
            if ($record['id'] == $id) return $record;
        return null;
    }

    // We obtain the records to use for this fixture by reading them from the 'fixture' db
    public function init() {

        parent::init();

        // Not all fixtures want this.
        if(is_null($this->tableName)) return;

        // We need to do this to ensure that the Subjects table really does use this connection.
        TableRegistry::remove($this->tableName);
        $table = TableRegistry::get($this->tableName,['connection'=>ConnectionManager::get('fixture')]);

        $query=new Query(ConnectionManager::get('fixture'),$table);
        $query->find('all');
        if(!is_null($this->order)) $query->order($this->order);

        /* @var \Cake\ORM\Entity $record */
        foreach($query as $record) {
            $this->records[]=$record->toArray();
        }

        // Do this again to ensure that the table uses the 'test' connection.
        TableRegistry::remove($this->tableName);
        TableRegistry::get($this->tableName,['connection'=>ConnectionManager::get('test')]);

    }
}