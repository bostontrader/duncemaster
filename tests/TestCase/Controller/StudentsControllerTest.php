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

    /* @var \App\Model\Table\CohortsTable */
    private $cohorts;

    /* @var \App\Model\Table\TeachersTable */
    private $students;

    private $studentsFixture;

    // If I put this in the super-class phpstorm won't understand their types
    /* @var \simple_html_dom_node */
    private $content,$field,$form,$htmlRow,$input,$table,$tbody,$td,$thead;

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
        $this->form = $html->find('form#StudentAddForm',0);
        $this->assertNotNull($this->form);

        // 4. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 4.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($this->form->find('select'));
        $unknownInputCnt = count($this->form->find('input'));

        // 4.2 Look for the hidden POST input
        if($this->lookForHiddenInput($this->form)) $unknownInputCnt--;

        // 4.3 Ensure that there's an input field for fam_name, of type text, and that it is empty
        $this->input = $this->form->find('input#StudentFamName',0);
        $this->assertEquals($this->input->type, "text");
        $this->assertEquals($this->input->value, false);
        $unknownInputCnt--;

        // 4.4 Ensure that there's an input field for giv_name, of type text, and that it is empty
        $this->input = $this->form->find('input#StudentGivName',0);
        $this->assertEquals($this->input->type, "text");
        $this->assertEquals($this->input->value, false);
        $unknownInputCnt--;

        // 4.5 Ensure that there's an input field for sid, of type text, and that it is empty
        $this->input = $this->form->find('input#StudentSid',0);
        $this->assertEquals($this->input->type, "text");
        $this->assertEquals($this->input->value, false);
        $unknownInputCnt--;

        // 4.6 Ensure that there's a select field for cohort_id and that is has no selection
        if($this->lookForSelect($this->form,'StudentCohortId','cohorts')) $unknownSelectCnt--;
        //$option = $this->form->find('select#StudentCohortId option[selected]',0);
        //$this->assertNull($option);
        //$unknownSelectCnt--;

        // 4.7 Ensure that there's a select field for user_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->lookForSelect($this->form,'StudentUserId','users')) $unknownSelectCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#StudentsAdd',0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
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
        $this->assertEquals($new_student['user_id'],$this->studentsFixture->newStudentRecord['user_id']);
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
        $this->form = $html->find('form#StudentEditForm',0);
        $this->assertNotNull($this->form);

        // 4. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 4.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($this->form->find('select'));
        $unknownInputCnt = count($this->form->find('input'));

        // 4.2 Look for the hidden POST input
        if($this->lookForHiddenInput($this->form,'_method','PUT')) $unknownInputCnt--;

        // 4.3 Ensure that there's an input field for giv_name, of type text, and that it is correctly set
        $this->input = $this->form->find('input#StudentGivName',0);
        $this->assertEquals($this->input->type, "text");
        $this->assertEquals($this->input->value, $this->studentsFixture->student1Record['giv_name']);
        $unknownInputCnt--;

        // 4.4 Ensure that there's an input field for fam_name, of type text, and that it is correctly set
        $this->input = $this->form->find('input#StudentFamName',0);
        $this->assertEquals($this->input->type, "text");
        $this->assertEquals($this->input->value,  $this->studentsFixture->student1Record['fam_name']);
        $unknownInputCnt--;

        // 4.5 Ensure that there's an input field for sid, of type text, and that it is correctly set
        $this->input = $this->form->find('input#StudentSid',0);
        $this->assertEquals($this->input->type, "text");
        $this->assertEquals($this->input->value,  $this->studentsFixture->student1Record['sid']);
        $unknownInputCnt--;

        // 4.6 Ensure that there's a select field for cohort_id and that it is correctly set
        $option = $this->form->find('select#StudentCohortId option[selected]',0);
        $cohort_id = $this->studentsFixture->student1Record['cohort_id'];
        $this->assertEquals($option->value, $cohort_id);
        $unknownSelectCnt--;

        // Even though cohort_id is correct, we don't display cohort_id.  Instead we display the
        // nickname from the related Cohorts table.  But nickname is a virtual field so we must
        // read the record in order to get the nickname, instead of looking it up in the fixture records.
        $cohort = $this->cohorts->get($cohort_id,['contain' => ['Majors']]);
        $this->assertEquals($cohort->nickname, $option->plaintext);

        // 4.7. Ensure that there's a select field for user_id and that it is correctly set
        $option = $this->form->find('select#StudentUserId option[selected]',0);
        $user_id = $this->studentsFixture->student1Record['user_id'];
        $this->assertEquals($option->value, $user_id);

        // Even though user_id is correct, we don't display user_id.  Instead we display the username
        // from the related Users table. Verify that username is displayed correctly.
        $user = $this->usersFixture->get($user_id);
        $this->assertEquals($user['username'], $option->plaintext);
        $unknownSelectCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#StudentsEdit',0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
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
        $this->assertEquals($student['user_id'],$this->studentsFixture->newStudentRecord['user_id']);
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
        $this->content = $html->find('div#StudentsIndex',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 4. Look for the create new student link
        $this->assertEquals(1, count($html->find('a#StudentAdd')));
        $unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        $this->table = $html->find('table#StudentsTable',0);
        $this->assertNotNull($this->table);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $this->thead = $this->table->find('thead',0);
        $thead_ths = $this->thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'sid');
        $this->assertEquals($thead_ths[1]->id, 'fullname');
        $this->assertEquals($thead_ths[2]->id, 'cohort_nickname');
        $this->assertEquals($thead_ths[3]->id, 'username');
        $this->assertEquals($thead_ths[4]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,5); // no other columns

        // 7. Ensure that the tbody section has the same
        //    quantity of rows as the count of students records in the fixture.
        $this->tbody = $this->table->find('tbody',0);
        $tbody_rows = $this->tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->studentsFixture->records));

        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->studentsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $this->htmlRow = $values[1];
            $htmlColumns = $this->htmlRow->find('td');

            // 8.0 sid
            $this->assertEquals($fixtureRecord['sid'],  $htmlColumns[0]->plaintext);

            // 8.1 fullname is computed by the Student entity.
            $student = $this->students->get($fixtureRecord['id'],['contain' => ['Cohorts.Majors']]);
            $this->assertEquals($student->fullname, $htmlColumns[1]->plaintext);

            // 8.2 cohort_nickname is computed by the Cohort entity.
            $this->assertEquals($student->cohort->nickname, $htmlColumns[2]->plaintext);

            // 8.3 username requires finding the related value in the UsersFixture
            $user_id = $fixtureRecord['user_id'];
            if (is_null($user_id)) {
                $expectedValue='';
            } else {
                $user = $this->usersFixture->get($user_id);
                $expectedValue=$user['username'];
            }
            $this->assertEquals($expectedValue, $htmlColumns[3]->plaintext);

            // 8.4 Now examine the action links
            $this->td = $htmlColumns[4];
            $actionLinks = $this->td->find('a');
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
    // In addition, a student may optionally have an associated User. This should also be
    // tested.
    //
    // We therefore have the following testing scenarios.
    //
    // 1. The view request references a Student that has a User, but has no request parameters.
    // Validate the body of the Student info and that the Section select is "nothing selected".
    // Hence no other grading info.
    //
    // 2. The view request references a Student that does not have a User, and has no request parameters.
    // Validate the body of the Student info and that the Section select is "nothing selected".
    // Hence no other grading info.
    //
    // 3. The view request has request parameters. Whether the Student has a User is unimportant.
    // Only validate the grading info.
    //

    // Scenario 1.
    public function testViewGETWithUser() {
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $fixtureRecord=$this->studentsFixture->student1Record;
        $a=['controller'=>'students','action'=>'view','id'=>$fixtureRecord['id'],'catfood'=>'yum'];
        $this->get($a);
        //$this->get('/students/view/' . $fixtureRecord['id'],['catfood'=>'yum']);
        $this->tstViewGet($fixtureRecord);
    }

    // Scenario 2.
    public function testViewGETWithOutUser() {
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $fixtureRecord=$this->studentsFixture->student2Record;
        $this->get('/students/view/' . $fixtureRecord['id']);
        $this->tstViewGet($fixtureRecord);
    }

    // Scenario 3. Don't care which student. Send a section_id as a request
    // param. Examine grading info.
    public function testViewGETWithRequestParameters() {
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $fixtureRecord=$this->studentsFixture->student1Record;
        $this->get('/students/view/' . $fixtureRecord['id'],['section_id'=>2]);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 1.  Look for the form that contains the Section selector.
        $this->form = $html->find('form#StudentViewGradeForm',0);
        $this->assertNotNull($this->form);

        // 2. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 2.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($this->form->find('select'));
        $unknownInputCnt = count($this->form->find('input'));

        // 2.2 Look for the hidden POST input
        if($this->lookForHiddenInput($this->form,'_method','PUT')) $unknownInputCnt--;

        // 4.3 Ensure that there's a select field for major_id, that it has no selection,
        // and that it has the correct quantity of available choices.
        //if($this->lookForSelect($this->form,'StudentViewSectionId','sections_list')) $unknownSelectCnt--;

        // Even though cohort_id is correct, we don't display cohort_id.  Instead we display the
        // nickname from the related Cohorts table.  But nickname is a virtual field so we must
        // read the record in order to get the nickname, instead of looking it up in the fixture records.
        //$cohort = $this->cohorts->get($cohort_id,['contain' => ['Majors']]);
        //$this->assertEquals($cohort->nickname, $option->plaintext);

        // 4.7. Ensure that there's a select field for user_id and that it is correctly set
        //$option = $this->form->find('select#StudentUserId option[selected]',0);
        //$user_id = $this->studentsFixture->student1Record['user_id'];
        //$this->assertEquals($option->value, $user_id);

        // Even though user_id is correct, we don't display user_id.  Instead we display the username
        // from the related Users table. Verify that username is displayed correctly.
        //$user = $this->usersFixture->get($user_id);
        //$this->assertEquals($user['username'], $option->plaintext);
        //$unknownSelectCnt--;

        // 2.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);
    }

    public function tstViewGET($fixtureRecord) {

        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 1.  Look for the table that contains the view fields.
        $this->table = $html->find('table#StudentViewTable',0);
        $this->assertNotNull($this->table);

        // 2. Now inspect the fields in the table.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($this->table->find('tr'));

        // 2.1 sid
        $field = $html->find('tr#sid td',0);
        $this->assertEquals($fixtureRecord['sid'], $field->plaintext);
        $unknownRowCnt--;

        // 2.2 fam_name
        $field = $html->find('tr#fam_name td',0);
        $this->assertEquals($fixtureRecord['fam_name'], $field->plaintext);
        $unknownRowCnt--;

        // 2.3 giv_name
        $field = $html->find('tr#giv_name td',0);
        $this->assertEquals($fixtureRecord['giv_name'], $field->plaintext);
        $unknownRowCnt--;

        // 2.4 cohort_name
        $field = $html->find('tr#cohort_nickname td',0);
        $student = $this->students->get($fixtureRecord['id'],['contain' => ['Cohorts.Majors']]);
        $this->assertEquals($student->cohort->nickname, $field->plaintext);
        $unknownRowCnt--;

        // 2.5 user_id requires finding the related value in the UsersFixture
        $this->field = $html->find('tr#username td',0);
        $user_id = $fixtureRecord['user_id'];
        $user = $this->usersFixture->get($user_id);

        $this->assertEquals($user['username'], $this->field->plaintext);
        $unknownRowCnt--;

        // 2.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 3.  Look for the form that contains the Section selector.
        $this->form = $html->find('form#StudentViewGradeForm',0);
        $this->assertNotNull($this->form);

        // 4. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 4.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($this->form->find('select'));
        $unknownInputCnt = count($this->form->find('input'));

        // 4.2 Look for the hidden POST input
        if($this->lookForHiddenInput($this->form,'_method','PUT')) $unknownInputCnt--;

        // 4.3 Ensure that there's a select field for section_id, that it has no selection,
        // and that it has the correct quantity of available choices.
        if($this->lookForSelect($this->form,'StudentViewSectionId','sections_list')) $unknownSelectCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#StudentsView',0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0,count($links));
    }

}
