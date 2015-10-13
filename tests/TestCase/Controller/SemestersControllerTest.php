<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\SemestersFixture;
use Cake\ORM\TableRegistry;

class SemestersControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.semesters'
    ];

    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/semesters/add');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('semester'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=SemesterAddForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for year, that is empty
        $input = $form->find('input[id=SemesterYear]')[0];
        $this->assertEquals($input->value, false);

        // Ensure that there's a field for seq, that is empty
        $input = $form->find('input[id=SemesterSeq]')[0];
        $this->assertEquals($input->value, false);
    }

    public function testAddPOST() {

        $semestersFixture = new SemestersFixture();

        $this->fakeLogin();
        $this->post('/semesters/add', $semestersFixture->newSemesterRecord);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/semesters' );

        // Now verify what we think just got written
        $semesters = TableRegistry::get('Semesters');
        $query = $semesters->find()->where(['id' => $semestersFixture->newSemesterRecord['id']]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $semester = $semesters->get($semestersFixture->newSemesterRecord['id']);
        $this->assertEquals($semester['year'],$semestersFixture->newSemesterRecord['year']);
        $this->assertEquals($semester['seq'],$semestersFixture->newSemesterRecord['seq']);
    }

    public function testDeletePOST() {

        $semestersFixture = new SemestersFixture();

        $this->fakeLogin();
        $this->post('/semesters/delete/' . $semestersFixture->semester1Record['id']);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/semesters' );

        // Now verify that the record no longer exists
        $semesters = TableRegistry::get('Semesters');
        $query = $semesters->find()->where(['id' => $semestersFixture->semester1Record['id']]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $semestersFixture = new SemestersFixture();

        $this->fakeLogin();
        $this->get('/semesters/edit/' . $semestersFixture->semester1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('semester'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=SemesterEditForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for year, that is correctly set
        $input = $form->find('input[id=SemesterYear]')[0];
        $this->assertEquals($input->value, $semestersFixture->semester1Record['year']);

        // Ensure that there's a field for seq, that is correctly set
        $input = $form->find('input[id=SemesterSeq]')[0];
        $this->assertEquals($input->value,  $semestersFixture->semester1Record['seq']);

    }

    public function testEditPOST() {

        $semestersFixture = new SemestersFixture();

        $this->fakeLogin();
        $this->post('/semesters/edit/' . $semestersFixture->semester1Record['id'], $semestersFixture->newSemesterRecord);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Now verify what we think just got written
        $semesters = TableRegistry::get('Semesters');
        $query = $semesters->find()->where(['id' => $semestersFixture->semester1Record['id']]);
        $c = $query->count();
        $this->assertEquals(1, $c);

        // Now retrieve that 1 record and compare to what we expect
        $semester = $semesters->get($semestersFixture->semester1Record['id']);
        $this->assertEquals($semester['year'],$semestersFixture->newSemesterRecord['year']);

    }

    public function testIndexGET() {

        $this->fakeLogin();
        $result = $this->get('/semesters/index');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($result);

        // 1. Ensure that the single row of the thead section
        //    has a column for id and year, in that order
        //$rows = $html->find('table[id=semesters]',0)->find('thead',0)->find('tr');
        //$row_cnt = count($rows);
        //$this->assertEqual($row_cnt, 1);

        // 2. Ensure that the thead section has a heading
        //    for id, year, is_active, and is_admin.
        //$columns = $rows[0]->find('td');
        //$this->assertEqual($columns[0]->plaintext, 'id');
        //$this->assertEqual($columns[1]->plaintext, 'year');
        //$this->assertEqual($columns[2]->plaintext, 'is_active');
        //$this->assertEqual($columns[3]->plaintext, 'is_admin');

        // 3. Ensure that the tbody section has the same
        //    quantity of rows as the count of semester records in the fixture.
        //    For each of these rows, ensure that the id and year match
        //$semesterFixture = new SemesterFixture();
        //$rowsInHTMLTable = $html->find('table[id=semesters]',0)->find('tbody',0)->find('tr');
        //$this->assertEqual(count($semesterFixture->records), count($rowsInHTMLTable));
        //$iterator = new MultipleIterator;
        //$iterator->attachIterator(new ArrayIterator($semesterFixture->records));
        //$iterator->attachIterator(new ArrayIterator($rowsInHTMLTable));

        //foreach ($iterator as $values) {
        //$fixtureRecord = $values[0];
        //$htmlRow = $values[1];
        //$htmlColumns = $htmlRow->find('td');
        //$this->assertEqual($fixtureRecord['id'],        $htmlColumns[0]->plaintext);
        //$this->assertEqual($fixtureRecord['year'],  $htmlColumns[1]->plaintext);
        //$this->assertEqual($fixtureRecord['is_active'], $htmlColumns[2]->plaintext);
        //$this->assertEqual($fixtureRecord['is_admin'],  $htmlColumns[3]->plaintext);
        //}
    }

    public function testViewGET() {

        $semestersFixture = new SemestersFixture();

        $this->fakeLogin();
        $this->get('/semesters/view/' . $semestersFixture->semester1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('semester'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        //$form = $html->find('form[id=SemesterEditForm]')[0];
        //$this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for year, that is correctly set
        //$input = $form->find('input[id=SemesterYear]')[0];
        //$this->assertEquals($input->value, $semestersFixture->semester1Record['year']);

        // Ensure that there's a field for seq, that is empty
        //$input = $form->find('input[id=SemesterSeq]')[0];
        //$this->assertEquals($input->value, false);
    }

}
