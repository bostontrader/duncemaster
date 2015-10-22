<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\CohortsFixture;
use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\StudentsFixture;
use Cake\ORM\TableRegistry;

class StudentsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.cohorts',
        'app.majors',
        'app.students'
    ];

    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/students/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure these view vars are set.
        // I'd like to check that cohorts contains majors.  But...
        // doing so has proven to be too complicated and not worth the effort.
        // Just make sure cohorts contains majors.
        $this->assertNotNull($this->viewVariable('cohorts'));
        $this->assertNotNull($this->viewVariable('student'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#StudentAddForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // Ensure that there's an input field for fam_name, of type text, and that it is empty
        $input = $form->find('input#StudentFamName',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);

        // Ensure that there's an input field for giv_name, of type text, and that it is empty
        $input = $form->find('input#StudentGivName',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);

        // Ensure that there's an input field for sid, of type text, and that it is empty
        $input = $form->find('input#StudentSid',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);

        // Ensure that there's a select field for cohort_id and that is has no selection
        $option = $form->find('select#StudentCohortId option[selected]',0);
        $this->assertNull($option);
    }

    public function testAddPOST() {

        $studentsFixture = new StudentsFixture();

        $this->fakeLogin();
        $this->post('/students/add', $studentsFixture->newStudentRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/students' );

        // Now verify what we think just got written
        $students = TableRegistry::get('Students');
        $new_id = FixtureConstants::student1_id + 1;
        $query = $students->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_student = $students->get($new_id);
        $this->assertEquals($new_student['fam_name'],$studentsFixture->newStudentRecord['fam_name']);
        $this->assertEquals($new_student['giv_name'],$studentsFixture->newStudentRecord['giv_name']);
        $this->assertEquals($new_student['sid'],$studentsFixture->newStudentRecord['sid']);
        $this->assertEquals($new_student['cohort_id'],$studentsFixture->newStudentRecord['cohort_id']);
    }

    public function testDeletePOST() {

        $studentsFixture = new StudentsFixture();

        $this->fakeLogin();
        $student_id = $studentsFixture->student1Record['id'];
        $this->post('/students/delete/' . $student_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/students' );

        // Now verify that the record no longer exists
        $students = TableRegistry::get('Students');
        $query = $students->find()->where(['id' => $student_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $cohortsFixture = new CohortsFixture();
        $studentsFixture = new StudentsFixture();

        $this->fakeLogin();
        $student_id = $studentsFixture->student1Record['id'];
        $this->get('/students/edit/' . $student_id);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure these view vars are set.
        // I'd like to check that cohorts contains majors.  But...
        // doing so has proven to be too complicated and not worth the effort.
        // Just make sure cohorts contains majors.
        $this->assertNotNull($this->viewVariable('cohorts'));
        $this->assertNotNull($this->viewVariable('student'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#StudentEditForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // Ensure that there's an input field for giv_name, of type text, and that it is correctly set
        $input = $form->find('input#StudentGivName',0);
        $this->assertEquals($input->value, $studentsFixture->student1Record['giv_name']);

        // Ensure that there's an input field for fam_name, of type text, and that it is correctly set
        $input = $form->find('input#StudentFamName',0);
        $this->assertEquals($input->value,  $studentsFixture->student1Record['fam_name']);

        // Ensure that there's an input field for sid, of type text, and that it is correctly set
        $input = $form->find('input#StudentSid',0);
        $this->assertEquals($input->value,  $studentsFixture->student1Record['sid']);

        // Ensure that there's a select field for cohort_id and that it is correctly set
        $option = $form->find('select#StudentCohortId option[selected]',0);
        $cohort_id = $studentsFixture->student1Record['cohort_id'];
        $this->assertEquals($option->value, $cohort_id);

        // Even though cohort_id is correct, we don't display cohort_id.  Instead we display the
        // nickname from the related Cohorts table.  But nickname is a virtual field so we must
        // read the record in order to get the nickname, instead of looking it up in the fixture records.
        $cohorts = TableRegistry::get('Cohorts');
        $cohort = $cohorts->get($cohort_id,['contain' => ['Majors']]);
        $this->assertEquals($cohort->nickname, $option->plaintext);
    }

    public function testEditPOST() {

        $studentsFixture = new StudentsFixture();

        $this->fakeLogin();
        $student_id = $studentsFixture->student1Record['id'];
        $this->put('/students/edit/' . $student_id, $studentsFixture->newStudentRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/students');

        // Now verify what we think just got written
        $students = TableRegistry::get('Students');
        $query = $students->find()->where(['id' => $studentsFixture->student1Record['id']]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $student = $students->get($studentsFixture->student1Record['id']);
        $this->assertEquals($student['giv_name'],$studentsFixture->newStudentRecord['giv_name']);
        $this->assertEquals($student['fam_name'],$studentsFixture->newStudentRecord['fam_name']);
        $this->assertEquals($student['sid'],$studentsFixture->newStudentRecord['sid']);
        $this->assertEquals($student['cohort_id'],$studentsFixture->newStudentRecord['cohort_id']);
    }

    public function testIndexGET() {

        $this->fakeLogin();
        $this->get('/students/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('students'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // How shall we test the index?

        // 1. Ensure that there is a suitably named table to display the results.
        $students_table = $html->find('table#students',0);
        $this->assertNotNull($students_table);

        // 2. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $students_table->find('thead',0);
        $thead_ths = $thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'id');
        $this->assertEquals($thead_ths[1]->id, 'sid');
        $this->assertEquals($thead_ths[2]->id, 'fullname');
        $this->assertEquals($thead_ths[3]->id, 'cohort_nickname');
        $this->assertEquals($thead_ths[4]->id, 'actions');
        $this->assertEquals(count($thead_ths),5); // no other columns

        // 3. Ensure that the tbody section has the same
        //    quantity of rows as the count of cohort records in the fixture.
        $cohortsFixture = new CohortsFixture();
        $studentsFixture = new StudentsFixture();
        $tbody = $students_table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($cohortsFixture));

        // 4. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.  In order to do this we'll also need
        //    to read from the Cohorts table.
        $students = TableRegistry::get('Students');
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($studentsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');
            $this->assertEquals($fixtureRecord['id'], $htmlColumns[0]->plaintext);
            $this->assertEquals($fixtureRecord['sid'],  $htmlColumns[1]->plaintext);

            // fullname is computed by the Student Entity.
            $student = $students->get($fixtureRecord['id'],['contain' => ['Cohorts.Majors']]);
            $this->assertEquals($student->fullname, $htmlColumns[2]->plaintext);

            $this->assertEquals($student->cohort->nickname, $htmlColumns[3]->plaintext);

            // Ignore the action links

        }

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
        //$input = $form->find('input[id=StudentGivName]')[0];
        //$this->assertEquals($input->value, $studentsFixture->student1Record['title']);

        // Ensure that there's a field for fam_name, that is empty
        //$input = $form->find('input[id=StudentFamName]')[0];
        //$this->assertEquals($input->value, false);
    }

}
