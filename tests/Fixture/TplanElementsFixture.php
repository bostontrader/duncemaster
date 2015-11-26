<?php
namespace App\Test\Fixture;

class TplanElementsFixture extends DMFixture {
    public $import = ['table' => 'tplan_elements'];

    // This record is injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $tplan_element1Record = [
        'id'=>FixtureConstants::tplan_element1_id,
        'col1'=>'Curious George',
        'col2'=>'read the book'
    ];

    public $tplan_element2Record = [
        'id'=>FixtureConstants::tplan_element2_id,
        'col1'=>'The Cat in The Hat',
        'col2'=>'discuss the book'
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newTplanElementRecord = [
        'col1'=>'Animal Farm',
        'col2'=>'learn to make animal sounds'
    ];

    public function init() {
        $this->records = [
            $this->tplan_element1Record,
            $this->tplan_element2Record
        ];
        parent::init();
    }
}