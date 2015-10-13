<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\StudentsFixture;
use Cake\ORM\TableRegistry;

class StudentsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.students'
    ];

    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/students/add');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('student'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=StudentAddForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is empty
        $input = $form->find('input[id=StudentTitle]')[0];
        $this->assertEquals($input->value, false);

        // Ensure that there's a field for sdesc, that is empty
        $input = $form->find('input[id=StudentSDesc]')[0];
        $this->assertEquals($input->value, false);
    }

    public function testAddPOST() {

        $studentsFixture = new StudentsFixture();

        $this->fakeLogin();
        $this->post('/students/add', $studentsFixture->newStudentRecord);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/students' );

        // Now verify what we think just got written
        $students = TableRegistry::get('Students');
        $query = $students->find()->where(['id' => $studentsFixture->newStudentRecord['id']]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $student = $students->get($studentsFixture->newStudentRecord['id']);
        $this->assertEquals($student['title'],$studentsFixture->newStudentRecord['title']);
    }

    public function testDeletePOST() {

        $studentsFixture = new StudentsFixture();

        $this->fakeLogin();
        $this->post('/students/delete/' . $studentsFixture->student1Record['id']);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/students' );

        // Now verify that the record no longer exists
        $students = TableRegistry::get('Students');
        $query = $students->find()->where(['id' => $studentsFixture->student1Record['id']]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $studentsFixture = new StudentsFixture();

        $this->fakeLogin();
        $this->get('/students/edit/' . $studentsFixture->student1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('student'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=StudentEditForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is correctly set
        $input = $form->find('input[id=StudentTitle]')[0];
        $this->assertEquals($input->value, $studentsFixture->student1Record['title']);

        // Ensure that there's a field for sdesc, that is correctly set
        $input = $form->find('input[id=StudentSDesc]')[0];
        $this->assertEquals($input->value,  $studentsFixture->student1Record['sdesc']);

    }

    public function testEditPOST() {

        $studentsFixture = new StudentsFixture();

        $this->fakeLogin();
        $this->post('/students/edit/' . $studentsFixture->student1Record['id'], $studentsFixture->newStudentRecord);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Now verify what we think just got written
        $students = TableRegistry::get('Students');
        $query = $students->find()->where(['id' => $studentsFixture->student1Record['id']]);
        $c = $query->count();
        $this->assertEquals(1, $c);

        // Now retrieve that 1 record and compare to what we expect
        $student = $students->get($studentsFixture->student1Record['id']);
        $this->assertEquals($student['title'],$studentsFixture->newStudentRecord['title']);

    }

    public function testIndexGET() {

        $this->fakeLogin();
        $result = $this->get('/students/index');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($result);

        // 1. Ensure that the single row of the thead section
        //    has a column for id and title, in that order
        //$rows = $html->find('table[id=students]',0)->find('thead',0)->find('tr');
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
        //    quantity of rows as the count of student records in the fixture.
        //    For each of these rows, ensure that the id and title match
        //$studentFixture = new StudentFixture();
        //$rowsInHTMLTable = $html->find('table[id=students]',0)->find('tbody',0)->find('tr');
        //$this->assertEqual(count($studentFixture->records), count($rowsInHTMLTable));
        //$iterator = new MultipleIterator;
        //$iterator->attachIterator(new ArrayIterator($studentFixture->records));
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

        $studentsFixture = new StudentsFixture();

        $this->fakeLogin();
        $this->get('/students/view/' . $studentsFixture->student1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('student'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        //$form = $html->find('form[id=StudentEditForm]')[0];
        //$this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is correctly set
        //$input = $form->find('input[id=StudentTitle]')[0];
        //$this->assertEquals($input->value, $studentsFixture->student1Record['title']);

        // Ensure that there's a field for sdesc, that is empty
        //$input = $form->find('input[id=StudentSDesc]')[0];
        //$this->assertEquals($input->value, false);
    }

}
