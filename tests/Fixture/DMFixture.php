<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class DMFixture extends TestFixture {

    // Given an id, return the first fixture record found with that id, or null if not found.
    public function get($id) {
        foreach ($this->records as $record)
            if ($record['id'] == $id) return $record;
        return null;
    }

}