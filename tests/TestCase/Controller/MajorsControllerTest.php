<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\MajorsFixture;
//use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

require_once 'simple_html_dom.php';

class MajorsControllerTest extends IntegrationTestCase {
//class MajorsControllerTest extends DMIntegrationTestCase {

    // Hack the session to make it look as if we're properly logged in.
    protected function fakeLogin() {
        // Set session data
        $this->session(
            [
                'Auth' => [
                    'User' => [
                        'id' => 1,
                        'username' => 'testing',
                    ]
                ]
            ]
        );
    }

    public $fixtures = [
        'app.majors'
    ];

    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/majors/add');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('major'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=MajorAddForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for majorname, that is empty
        $input = $form->find('input[id=MajorMajorname]')[0];
        $this->assertEquals($input->value, false);

        // Ensure that there's a field for password, that is empty
        $input = $form->find('input[id=MajorPassword]')[0];
        $this->assertEquals($input->value, false);
    }

    public function testAddPOST() {

        $majorsFixture = new MajorsFixture();

        $this->fakeLogin();
        $this->post('/majors/add', $majorsFixture->newMajorRecord);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/majors' );

        // Now verify what we think just got written
        $majors = TableRegistry::get('Majors');
        $query = $majors->find()->where(['id' => $majorsFixture->newMajorRecord['id']]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $major = $majors->get($majorsFixture->newMajorRecord['id']);
        $this->assertEquals($major['majorname'],$majorsFixture->newMajorRecord['majorname']);
    }

    public function testDeletePOST() {

        $majorsFixture = new MajorsFixture();

        $this->fakeLogin();
        $this->post('/majors/delete/' . $majorsFixture->major1Record['id']);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/majors' );

        // Now verify that the record no longer exists
        $majors = TableRegistry::get('Majors');
        $query = $majors->find()->where(['id' => $majorsFixture->major1Record['id']]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $majorsFixture = new MajorsFixture();

        $this->fakeLogin();
        $this->get('/majors/edit/' . $majorsFixture->major1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('major'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=MajorEditForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for majorname, that is correctly set
        $input = $form->find('input[id=MajorMajorname]')[0];
        $this->assertEquals($input->value, $majorsFixture->major1Record['majorname']);

        // Ensure that there's a field for password, that is empty
        $input = $form->find('input[id=MajorPassword]')[0];
        $this->assertEquals($input->value, false);

    }

    public function testEditPOST() {

        $majorsFixture = new MajorsFixture();

        $this->fakeLogin();
        $this->post('/majors/edit/' . $majorsFixture->major1Record['id'], $majorsFixture->newMajorRecord);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Now verify what we think just got written
        $majors = TableRegistry::get('Majors');
        $query = $majors->find()->where(['id' => $majorsFixture->major1Record['id']]);
        $c = $query->count();
        $this->assertEquals(1, $c);

        // Now retrieve that 1 record and compare to what we expect
        $major = $majors->get($majorsFixture->major1Record['id']);
        $this->assertEquals($major['majorname'],$majorsFixture->newMajorRecord['majorname']);

    }

    public function testIndexGET() {

        $this->fakeLogin();
        $result = $this->get('/majors/index');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($result);

        // 1. Ensure that the single row of the thead section
        //    has a column for id and majorname, in that order
        //$rows = $html->find('table[id=majors]',0)->find('thead',0)->find('tr');
        //$row_cnt = count($rows);
        //$this->assertEqual($row_cnt, 1);

        // 2. Ensure that the thead section has a heading
        //    for id, majorname, is_active, and is_admin.
        //$columns = $rows[0]->find('td');
        //$this->assertEqual($columns[0]->plaintext, 'id');
        //$this->assertEqual($columns[1]->plaintext, 'majorname');
        //$this->assertEqual($columns[2]->plaintext, 'is_active');
        //$this->assertEqual($columns[3]->plaintext, 'is_admin');

        // 3. Ensure that the tbody section has the same
        //    quantity of rows as the count of major records in the fixture.
        //    For each of these rows, ensure that the id and majorname match
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
        //$this->assertEqual($fixtureRecord['majorname'],  $htmlColumns[1]->plaintext);
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
        // Ensure that there's a field for majorname, that is correctly set
        //$input = $form->find('input[id=MajorMajorname]')[0];
        //$this->assertEquals($input->value, $majorsFixture->major1Record['majorname']);

        // Ensure that there's a field for password, that is empty
        //$input = $form->find('input[id=MajorPassword]')[0];
        //$this->assertEquals($input->value, false);
    }

}
