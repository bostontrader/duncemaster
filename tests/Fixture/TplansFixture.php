<?php
namespace App\Test\Fixture;

class TplansFixture extends DMFixture {
    public $import = ['table' => 'tplans'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newTplanRecord = [
        'title'=>'difficult plan'
    ];

    public function init() {
        $this->tableName='Tplans';
        parent::init(); // This is where the records are loaded.
    }
}