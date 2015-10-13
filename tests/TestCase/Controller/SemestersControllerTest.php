<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\SemestersFixture;
use Cake\ORM\TableRegistry;

class SemestersControllerTest extends DMIntegrationTestCase {

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
        $form = $html->find('form[id=SemesterAddForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is empty
        $input = $form->find('input[id=SemesterTitle]')[0];
        $this->assertEquals($input->value, false);

        // Ensure that there's a field for sdesc, that is empty
        $input = $form->find('input[id=SemesterSDesc]')[0];
        $this->assertEquals($input->value, false);
    }

    public function testAddPOST() {

        $majorsFixture = new SemestersFixture();

        $this->fakeLogin();
        $this->post('/majors/add', $majorsFixture->newSemesterRecord);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/majors' );

        // Now verify what we think just got written
        $majors = TableRegistry::get('Semesters');
        $query = $majors->find()->where(['id' => $majorsFixture->newSemesterRecord['id']]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $major = $majors->get($majorsFixture->newSemesterRecord['id']);
        $this->assertEquals($major['title'],$majorsFixture->newSemesterRecord['title']);
    }

    public function testDeletePOST() {

        $majorsFixture = new SemestersFixture();

        $this->fakeLogin();
        $this->post('/majors/delete/' . $majorsFixture->major1Record['id']);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/majors' );

        // Now verify that the record no longer exists
        $majors = TableRegistry::get('Semesters');
        $query = $majors->find()->where(['id' => $majorsFixture->major1Record['id']]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $majorsFixture = new SemestersFixture();

        $this->fakeLogin();
        $this->get('/majors/edit/' . $majorsFixture->major1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('major'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=SemesterEditForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is correctly set
        $input = $form->find('input[id=SemesterTitle]')[0];
        $this->assertEquals($input->value, $majorsFixture->major1Record['title']);

        // Ensure that there's a field for sdesc, that is correctly set
        $input = $form->find('input[id=SemesterSDesc]')[0];
        $this->assertEquals($input->value,  $majorsFixture->major1Record['sdesc']);

    }

    public function testEditPOST() {

        $majorsFixture = new SemestersFixture();

        $this->fakeLogin();
        $this->post('/majors/edit/' . $majorsFixture->major1Record['id'], $majorsFixture->newSemesterRecord);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Now verify what we think just got written
        $majors = TableRegistry::get('Semesters');
        $query = $majors->find()->where(['id' => $majorsFixture->major1Record['id']]);
        $c = $query->count();
        $this->assertEquals(1, $c);

        // Now retrieve that 1 record and compare to what we expect
        $major = $majors->get($majorsFixture->major1Record['id']);
        $this->assertEquals($major['title'],$majorsFixture->newSemesterRecord['title']);

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
        //$majorFixture = new SemesterFixture();
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

        $majorsFixture = new SemestersFixture();

        $this->fakeLogin();
        $this->get('/majors/view/' . $majorsFixture->major1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('major'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        //$form = $html->find('form[id=SemesterEditForm]')[0];
        //$this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is correctly set
        //$input = $form->find('input[id=SemesterTitle]')[0];
        //$this->assertEquals($input->value, $majorsFixture->major1Record['title']);

        // Ensure that there's a field for sdesc, that is empty
        //$input = $form->find('input[id=SemesterSDesc]')[0];
        //$this->assertEquals($input->value, false);
    }

}
