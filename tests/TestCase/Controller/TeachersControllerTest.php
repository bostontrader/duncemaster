<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\TeachersFixture;
use Cake\ORM\TableRegistry;

class TeachersControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.teachers'
    ];

    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/teachers/add');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('teacher'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=TeacherAddForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is empty
        $input = $form->find('input[id=TeacherTitle]')[0];
        $this->assertEquals($input->value, false);

        // Ensure that there's a field for sdesc, that is empty
        $input = $form->find('input[id=TeacherSDesc]')[0];
        $this->assertEquals($input->value, false);
    }

    public function testAddPOST() {

        $teachersFixture = new TeachersFixture();

        $this->fakeLogin();
        $this->post('/teachers/add', $teachersFixture->newTeacherRecord);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/teachers' );

        // Now verify what we think just got written
        $teachers = TableRegistry::get('Teachers');
        $query = $teachers->find()->where(['id' => $teachersFixture->newTeacherRecord['id']]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $teacher = $teachers->get($teachersFixture->newTeacherRecord['id']);
        $this->assertEquals($teacher['title'],$teachersFixture->newTeacherRecord['title']);
    }

    public function testDeletePOST() {

        $teachersFixture = new TeachersFixture();

        $this->fakeLogin();
        $this->post('/teachers/delete/' . $teachersFixture->teacher1Record['id']);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/teachers' );

        // Now verify that the record no longer exists
        $teachers = TableRegistry::get('Teachers');
        $query = $teachers->find()->where(['id' => $teachersFixture->teacher1Record['id']]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $teachersFixture = new TeachersFixture();

        $this->fakeLogin();
        $this->get('/teachers/edit/' . $teachersFixture->teacher1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('teacher'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=TeacherEditForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is correctly set
        $input = $form->find('input[id=TeacherTitle]')[0];
        $this->assertEquals($input->value, $teachersFixture->teacher1Record['title']);

        // Ensure that there's a field for sdesc, that is correctly set
        $input = $form->find('input[id=TeacherSDesc]')[0];
        $this->assertEquals($input->value,  $teachersFixture->teacher1Record['sdesc']);

    }

    public function testEditPOST() {

        $teachersFixture = new TeachersFixture();

        $this->fakeLogin();
        $this->post('/teachers/edit/' . $teachersFixture->teacher1Record['id'], $teachersFixture->newTeacherRecord);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Now verify what we think just got written
        $teachers = TableRegistry::get('Teachers');
        $query = $teachers->find()->where(['id' => $teachersFixture->teacher1Record['id']]);
        $c = $query->count();
        $this->assertEquals(1, $c);

        // Now retrieve that 1 record and compare to what we expect
        $teacher = $teachers->get($teachersFixture->teacher1Record['id']);
        $this->assertEquals($teacher['title'],$teachersFixture->newTeacherRecord['title']);

    }

    public function testIndexGET() {

        $this->fakeLogin();
        $result = $this->get('/teachers/index');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($result);

        // 1. Ensure that the single row of the thead section
        //    has a column for id and title, in that order
        //$rows = $html->find('table[id=teachers]',0)->find('thead',0)->find('tr');
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
        //    quantity of rows as the count of teacher records in the fixture.
        //    For each of these rows, ensure that the id and title match
        //$teacherFixture = new TeacherFixture();
        //$rowsInHTMLTable = $html->find('table[id=teachers]',0)->find('tbody',0)->find('tr');
        //$this->assertEqual(count($teacherFixture->records), count($rowsInHTMLTable));
        //$iterator = new MultipleIterator;
        //$iterator->attachIterator(new ArrayIterator($teacherFixture->records));
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

        $teachersFixture = new TeachersFixture();

        $this->fakeLogin();
        $this->get('/teachers/view/' . $teachersFixture->teacher1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('teacher'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        //$form = $html->find('form[id=TeacherEditForm]')[0];
        //$this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is correctly set
        //$input = $form->find('input[id=TeacherTitle]')[0];
        //$this->assertEquals($input->value, $teachersFixture->teacher1Record['title']);

        // Ensure that there's a field for sdesc, that is empty
        //$input = $form->find('input[id=TeacherSDesc]')[0];
        //$this->assertEquals($input->value, false);
    }

}
