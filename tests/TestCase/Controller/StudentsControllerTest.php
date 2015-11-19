<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\StudentsFixture;
use Cake\ORM\TableRegistry;

class StudentsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.cohorts',
        'app.majors',
        'app.roles',
        'app.roles_users',
        'app.sections',
        'app.students',
        'app.users'
    ];

    private $cohorts;
    private $students;
    private $studentsFixture;

    public function setUp() {
        parent::setUp();
        $this->cohorts = TableRegistry::get('Cohorts');
        $this->students = TableRegistry::get('Students');
        $this->studentsFixture = new StudentsFixture();
    }

    // Test that unauthenticated users, when submitting a request to
    // an action, will get redirected to the login url.
    public function testUnauthenticatedActionsAndUsers() {
        $this->tstUnauthenticatedActionsAndUsers('students');
    }

    // Test that users who do not have correct roles, when submitting a request to
    // an action, will get redirected to the home page.
    public function testUnauthorizedActionsAndUsers() {
        $this->tstUnauthorizedActionsAndUsers('students');
    }

    public function testAddGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/students/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#StudentAddForm',0);
        $this->assertNotNull($form);

        // 4. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 4.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 4.2 Look for the hidden POST input
        if($this->lookForHiddenInput($form)) $unknownInputCnt--;

        // 4.3 Ensure that there's an input field for fam_name, of type text, and that it is empty
        $input = $form->find('input#StudentFamName',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // 4.4 Ensure that there's an input field for giv_name, of type text, and that it is empty
        $input = $form->find('input#StudentGivName',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // 4.5 Ensure that there's an input field for sid, of type text, and that it is empty
        $input = $form->find('input#StudentSid',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // 4.6 Ensure that there's a select field for cohort_id and that is has no selection
        $option = $form->find('select#StudentCohortId option[selected]',0);
        $this->assertNull($option);
        $unknownSelectCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#StudentsAdd',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testAddPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->post('/students/add', $this->studentsFixture->newStudentRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/students' );

        // Now verify what we think just got written
        $new_id = count($this->studentsFixture->records) + 1;
        $query = $this->students->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_student = $this->students->get($new_id);
        $this->assertEquals($new_student['fam_name'],$this->studentsFixture->newStudentRecord['fam_name']);
        $this->assertEquals($new_student['giv_name'],$this->studentsFixture->newStudentRecord['giv_name']);
        $this->assertEquals($new_student['sid'],$this->studentsFixture->newStudentRecord['sid']);
        $this->assertEquals($new_student['cohort_id'],$this->studentsFixture->newStudentRecord['cohort_id']);
    }

    public function testDeletePOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $student_id = $this->studentsFixture->student1Record['id'];
        $this->post('/students/delete/' . $student_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/students' );

        // Now verify that the record no longer exists
        $query = $this->students->find()->where(['id' => $student_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/students/edit/' . $this->studentsFixture->student1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#StudentEditForm',0);
        $this->assertNotNull($form);

        // 4. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 4.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 4.2 Look for the hidden POST input
        if($this->lookForHiddenInput($form,'_method','PUT')) $unknownInputCnt--;

        // 4.3 Ensure that there's an input field for giv_name, of type text, and that it is correctly set
        $input = $form->find('input#StudentGivName',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $this->studentsFixture->student1Record['giv_name']);
        $unknownInputCnt--;

        // 4.4 Ensure that there's an input field for fam_name, of type text, and that it is correctly set
        $input = $form->find('input#StudentFamName',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value,  $this->studentsFixture->student1Record['fam_name']);
        $unknownInputCnt--;

        // 4.5 Ensure that there's an input field for sid, of type text, and that it is correctly set
        $input = $form->find('input#StudentSid',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value,  $this->studentsFixture->student1Record['sid']);
        $unknownInputCnt--;

        // 4.6 Ensure that there's a select field for cohort_id and that it is correctly set
        $option = $form->find('select#StudentCohortId option[selected]',0);
        $cohort_id = $this->studentsFixture->student1Record['cohort_id'];
        $this->assertEquals($option->value, $cohort_id);
        $unknownSelectCnt--;

        // Even though cohort_id is correct, we don't display cohort_id.  Instead we display the
        // nickname from the related Cohorts table.  But nickname is a virtual field so we must
        // read the record in order to get the nickname, instead of looking it up in the fixture records.
        $cohort = $this->cohorts->get($cohort_id,['contain' => ['Majors']]);
        $this->assertEquals($cohort->nickname, $option->plaintext);

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#StudentsEdit',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testEditPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $student_id = $this->studentsFixture->student1Record['id'];
        $this->put('/students/edit/' . $student_id, $this->studentsFixture->newStudentRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/students');

        // Now verify what we think just got written
        $query = $this->students->find()->where(['id' => $student_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $student = $this->students->get($student_id);
        $this->assertEquals($student['giv_name'],$this->studentsFixture->newStudentRecord['giv_name']);
        $this->assertEquals($student['fam_name'],$this->studentsFixture->newStudentRecord['fam_name']);
        $this->assertEquals($student['sid'],$this->studentsFixture->newStudentRecord['sid']);
        $this->assertEquals($student['cohort_id'],$this->studentsFixture->newStudentRecord['cohort_id']);
    }

    public function testIndexGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/students/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $content = $html->find('div#StudentsIndex',0);
        $this->assertNotNull($content);
        $unknownATag = count($content->find('a'));

        // 4. Look for the create new student link
        $this->assertEquals(1, count($html->find('a#StudentAdd')));
        $unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        $students_table = $html->find('table#StudentsTable',0);
        $this->assertNotNull($students_table);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $students_table->find('thead',0);
        $thead_ths = $thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'sid');
        $this->assertEquals($thead_ths[1]->id, 'fullname');
        $this->assertEquals($thead_ths[2]->id, 'cohort_nickname');
        $this->assertEquals($thead_ths[3]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,4); // no other columns

        // 7. Ensure that the tbody section has the same
        //    quantity of rows as the count of students records in the fixture.
        $tbody = $students_table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->studentsFixture->records));

        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->studentsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');

            // 8.0 sid
            $this->assertEquals($fixtureRecord['sid'],  $htmlColumns[0]->plaintext);

            // 8.1 fullname is computed by the Student entity.
            $student = $this->students->get($fixtureRecord['id'],['contain' => ['Cohorts.Majors']]);
            $this->assertEquals($student->fullname, $htmlColumns[1]->plaintext);

            // 8.2 cohort_nickname is computed by the Cohort entity.
            $this->assertEquals($student->cohort->nickname, $htmlColumns[2]->plaintext);

            // 8.3 Now examine the action links
            $actionLinks = $htmlColumns[3]->find('a');
            $this->assertEquals('StudentView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('StudentEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('StudentDelete', $actionLinks[2]->name);
            $unknownATag--;

            // 8.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 9. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    // The view method will display the basic information about the student.  It will also
    // display information about his grades. This is easier said than done because we must
    // determine which section to display the grades for.
    //
    // In order to determine which section, we use a select input. The information from the select input
    // is submitted to the view method via GET (because this is idempotent, nothing changes.)
    //
    // We therefore have the following testing scenarios.
    //
    // 1. A view with no request parameters: The section select is set to "nothing selected" and no grade
    // information is displayed.  We default to "nothing selected" because we cannot think
    // of a better default choice.
    //
    // 2. A view with request parameters: The grades are displayed for that particular section.

    public function testViewGET() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $student_id = $this->studentsFixture->student1Record['id'];
        $this->get('/students/view/' . $student_id);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 1.  Look for the table that contains the view fields.
        $table = $html->find('table#StudentViewTable',0);
        $this->assertNotNull($table);

        // 2. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($table->find('tr'));

        // 2.1 sid
        $field = $html->find('tr#sid td',0);
        $this->assertEquals($this->studentsFixture->student1Record['sid'], $field->plaintext);
        $unknownRowCnt--;

        // 2.2 fam_name
        $field = $html->find('tr#fam_name td',0);
        $this->assertEquals($this->studentsFixture->student1Record['fam_name'], $field->plaintext);
        $unknownRowCnt--;

        // 2.3 giv_name
        $field = $html->find('tr#giv_name td',0);
        $this->assertEquals($this->studentsFixture->student1Record['giv_name'], $field->plaintext);
        $unknownRowCnt--;

        // 2.4 cohort_name
        $field = $html->find('tr#cohort_nickname td',0);
        $student = $this->students->get($student_id,['contain' => ['Cohorts.Majors']]);
        $this->assertEquals($student->cohort->nickname, $field->plaintext);
        $unknownRowCnt--;

        // Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 3. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#StudentsView',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

}
