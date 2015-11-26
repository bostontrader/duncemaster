<?php
namespace App\Test\Fixture;

class TplansFixture extends DMFixture {
    public $import = ['table' => 'tplans'];

    // This record is injected into the db before the tests.  We need to specify the
    // id to ensure the test records are properly related.
    public $tplan1Record = [
        'id'=>FixtureConstants::tplan1_id,
        'title'=>'easy plan'
    ];

    public $tplan2Record = [
        'id'=>FixtureConstants::tplan2_id,
        'title'=>'medium plan'
    ];

    // This record will be added during a test.  We don't need or want to control the id here, so omit it.
    public $newTplanRecord = [
        'title'=>'difficult plan'
    ];

    public function init() {
        $this->records = [
            $this->tplan1Record,
            $this->tplan2Record
        ];
        parent::init();
    }
}