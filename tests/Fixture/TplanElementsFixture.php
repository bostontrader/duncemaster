<?php
namespace App\Test\Fixture;

class TplanElementsFixture extends DMFixture {
    public $import = ['table' => 'tplan_elements'];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newTplanElementRecord = [
        'tplan_id'=>FixtureConstants::tplanTypical,
        'col1'=>'Animal Farm',
        'col2'=>'learn to make animal sounds'
    ];

    public function init() {
        $this->tableName='TplanElements';
        parent::init(); // This is where the records are loaded.
    }
}