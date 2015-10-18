<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\MajorsFixture;
use Cake\ORM\TableRegistry;

class MajorsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.majors'
    ];

    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/majors/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('major'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#MajorAddForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // Ensure that there's an input field for title, of type text, and that it is empty
        $input = $form->find('input#MajorTitle',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);

        // Ensure that there's an input field for sdesc, of type text, and that it is empty
        $input = $form->find('input#MajorSDesc',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
    }

    public function testAddPOST() {

        $majorsFixture = new MajorsFixture();

        $this->fakeLogin();
        $this->post('/majors/add', $majorsFixture->newMajorRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/majors' );

        // Now verify what we think just got written
        $majors = TableRegistry::get('Majors');
        $new_id = FixtureConstants::major1_id + 1;
        $query = $majors->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_major = $majors->get($new_id);
        $this->assertEquals($new_major['title'],$majorsFixture->newMajorRecord['title']);
        $this->assertEquals($new_major['sdesc'],$majorsFixture->newMajorRecord['sdesc']);
    }

    public function testDeletePOST() {

        $majorsFixture = new MajorsFixture();

        $this->fakeLogin();
        $major_id = $majorsFixture->major1Record['id'];
        $this->post('/majors/delete/' . $major_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/majors' );

        // Now verify that the record no longer exists
        $majors = TableRegistry::get('Majors');
        $query = $majors->find()->where(['id' => $major_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $majorsFixture = new MajorsFixture();

        $this->fakeLogin();
        $major_id = $majorsFixture->major1Record['id'];
        $this->get('/majors/edit/' . $major_id);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('major'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#MajorEditForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // Ensure that there's an input field for title, of type text, and that it is correctly set
        $input = $form->find('input#MajorTitle',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $majorsFixture->major1Record['title']);

        // Ensure that there's an input field for sdesc, of type text, and that that it is correctly set
        $input = $form->find('input#MajorSDesc',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value,  $majorsFixture->major1Record['sdesc']);
    }

    public function testEditPOST() {

        $majorsFixture = new MajorsFixture();

        $this->fakeLogin();
        $major_id = $majorsFixture->major1Record['id'];
        $this->post('/majors/edit/' . $major_id, $majorsFixture->newMajorRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertNoRedirect();
        $this->assertRedirect('/majors');

        // Now verify what we think just got written
        $majors = TableRegistry::get('Majors');
        $query = $majors->find()->where(['id' => $major_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $major = $majors->get($major_id);
        $this->assertEquals($major['title'],$majorsFixture->newMajorRecord['title']);
        $this->assertEquals($major['sdesc'],$majorsFixture->newMajorRecord['sdesc']);
    }

    public function testIndexGET() {

        $this->fakeLogin();
        $result = $this->get('/majors/index');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($result);

        // 1. Ensure that the single row of the thead section
        //    has a column for id and title, in that order
        //$rows = $html->find('table[id=majors]',0)->find('thead',0)->find('tr');
        //$row_cnt = count($rows);
        //$this->assertEqual($row_cnt, 1);

        // 2. Ensure that the thead section has a heading
        //    for id, title, is_active, and is_admin.
        //$columns = $rows[0]->find('td');
        //$this->assertEqual($columns[0]->plaintext, 'id');
        //$this->assertEqual($columns[1]->plaintext, 'title');
        //$this->assertEqual($columns[2]->plaintext, 'is_active');
        //$this->assertEqual($columns[3]->plaintext, 'is_admin');

        // 3. Ensure that the tbody section has the same
        //    quantity of rows as the count of major records in the fixture.
        //    For each of these rows, ensure that the id and title match
        //$majorFixture = new MajorFixture();
        //$rowsInHTMLTable = $html->find('table[id=majors]',0)->find('tbody',0)->find('tr');
        //$this->assertEqual(count($majorFixture->records), count($rowsInHTMLTable));
        //$iterator = new MultipleIterator;
        //$iterator->attachIterator(new ArrayIterator($majorFixture->records));
        //$iterator->attachIterator(new ArrayIterator($rowsInHTMLTable));

        //foreach ($iterator as $values) {
        //$fixtureRecord = $values[0];
        //$htmlRow = $values[1];
        //$htmlColumns = $htmlRow->find('td');
        //$this->assertEqual($fixtureRecord['id'],        $htmlColumns[0]->plaintext);
        //$this->assertEqual($fixtureRecord['title'],  $htmlColumns[1]->plaintext);
        //$this->assertEqual($fixtureRecord['is_active'], $htmlColumns[2]->plaintext);
        //$this->assertEqual($fixtureRecord['is_admin'],  $htmlColumns[3]->plaintext);
        //}
    }

    public function testViewGET() {

        $majorsFixture = new MajorsFixture();

        $this->fakeLogin();
        $this->get('/majors/view/' . $majorsFixture->major1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('major'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        //$form = $html->find('form[id=MajorEditForm]')[0];
        //$this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is correctly set
        //$input = $form->find('input[id=MajorTitle]')[0];
        //$this->assertEquals($input->value, $majorsFixture->major1Record['title']);

        // Ensure that there's a field for sdesc, that is empty
        //$input = $form->find('input[id=MajorSDesc]')[0];
        //$this->assertEquals($input->value, false);
    }

}
