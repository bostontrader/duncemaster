<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\SectionsFixture;
use App\Test\Fixture\StudentsFixture;
use Cake\ORM\TableRegistry;

class StudentsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.clazzes',
        'app.cohorts',
        'app.interactions',
        'app.majors',
        'app.roles',
        'app.roles_users',
        'app.sections',
        'app.students',
        'app.users'
    ];

    /* @var \App\Model\Table\CohortsTable */
    private $cohorts;

    /* @var \App\Model\Table\SectionsTable */
    private $sections;

    /* @var \App\Model\Table\StudentsTable */
    private $students;

    /* @var \App\Test\Fixture\SectionsFixture */
    private $sectionsFixture;

    /* @var \App\Test\Fixture\StudentsFixture */
    private $studentsFixture;

    public function setUp() {
        parent::setUp();
        $this->cohorts = TableRegistry::get('Cohorts');
        $this->sections = TableRegistry::get('Sections');
        $this->students = TableRegistry::get('Students');
        $this->sectionsFixture = new SectionsFixture();
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

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,'/students/add');

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#StudentAddForm',0);
        $this->assertNotNull($form);

        // 3. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 3.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 3.2 Look for the hidden POST input
        if($this->lookForHiddenInput($form)) $unknownInputCnt--;

        if($this->inputCheckerA($form,'input#StudentFamName')) $unknownInputCnt--;
        if($this->inputCheckerA($form,'input#StudentGivName')) $unknownInputCnt--;
        if($this->inputCheckerA($form,'input#StudentPhoneticName')) $unknownInputCnt--;
        if($this->inputCheckerA($form,'input#StudentSid')) $unknownInputCnt--;

        // 3.6 Ensure that there's a select field for cohort_id and that is has no selection
        if($this->selectCheckerA($form, 'StudentCohortId','cohorts')) $unknownSelectCnt--;

        // 3.7 Ensure that there's a select field for user_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($form, 'StudentUserId','users')) $unknownSelectCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#StudentsAdd');
    }

    public function testAddPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->studentsFixture->newStudentRecord;
        $fromDbRecord=$this->genericAddPostProlog(
            FixtureConstants::userAndyAdminId,
            '/students/add', $fixtureRecord,
            '/students', $this->students
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['fam_name'],$fixtureRecord['fam_name']);
        $this->assertEquals($fromDbRecord['giv_name'],$fixtureRecord['giv_name']);
        $this->assertEquals($fromDbRecord['phonetic_name'],$fixtureRecord['phonetic_name']);
        $this->assertEquals($fromDbRecord['sid'],$fixtureRecord['sid']);
        $this->assertEquals($fromDbRecord['cohort_id'],$fixtureRecord['cohort_id']);
        $this->assertEquals($fromDbRecord['user_id'],$fixtureRecord['user_id']);
    }

    public function testDeletePOST() {

        $student_id = $this->studentsFixture->records[0]['id'];
        $this->deletePOST(
            FixtureConstants::userAndyAdminId, '/students/delete/',
            $student_id, '/students', $this->students
        );
    }

    public function testEditGET() {
        $this->tstEditGET(FixtureConstants::studentTypical);
        $this->tstEditGET(FixtureConstants::studentCohortIdNull);
        $this->tstEditGET(FixtureConstants::studentUserIdNull);
    }

    private function tstEditGET($student_id) {

        // 1. Obtain a record to edit, login, GET the url, parse the response and send it back.
        $record2Edit=$this->studentsFixture->get($student_id);
        $url='/students/edit/' . $record2Edit['id'];
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,$url);

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#StudentEditForm',0);
        $this->assertNotNull($form);

        // 3. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 3.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 3.2 Look for the hidden POST input
        if($this->lookForHiddenInput($form,'_method','PUT')) $unknownInputCnt--;

        // 3.3 giv_name
        if($this->inputCheckerA($form,'input#StudentGivName',
            $record2Edit['giv_name'])) $unknownInputCnt--;

        // 3.4 fam_name
        if($this->inputCheckerA($form,'input#StudentFamName',
            $record2Edit['fam_name'])) $unknownInputCnt--;

        // 3.5 phonetic_name
        if($this->inputCheckerA($form,'input#StudentPhoneticName',
            $record2Edit['phonetic_name'])) $unknownInputCnt--;

        // 3.6 sid
        if($this->inputCheckerA($form,'input#StudentSid',
            $record2Edit['sid'])) $unknownInputCnt--;

        // 3.7 cohort_id / $cohort['nickname']
        $cohort_id = $record2Edit['cohort_id'];
        if(is_null($cohort_id)) {
            if($this->selectCheckerA($form, 'StudentCohortId','cohorts')) $unknownSelectCnt--;
        } else {
            $cohort = $this->cohorts->get($cohort_id,['contain' => ['Majors']]);
            if($this->inputCheckerB($form,'select#StudentCohortId option[selected]',$cohort_id,$cohort['nickname'])) $unknownSelectCnt--;
        }

        // 3.8. user_id / $user_fixture_record['username']
        $user_id = $record2Edit['user_id'];
        if(is_null($user_id)) {
            if($this->selectCheckerA($form, 'StudentUserId','users')) $unknownSelectCnt--;
        } else {
            $user = $this->usersFixture->get($user_id);
            if($this->inputCheckerB($form,'select#StudentUserId option[selected]',$user_id,$user['username'])) $unknownSelectCnt--;
        }


        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#StudentsEdit');
    }

    public function testEditPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->studentsFixture->newStudentRecord;
        $fromDbRecord=$this->genericEditPutProlog(
            FixtureConstants::userAndyAdminId,
            '/students/edit', $fixtureRecord,
            '/students', $this->students
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['giv_name'],$fixtureRecord['giv_name']);
        $this->assertEquals($fromDbRecord['fam_name'],$fixtureRecord['fam_name']);
        $this->assertEquals($fromDbRecord['phonetic_name'],$fixtureRecord['phonetic_name']);
        $this->assertEquals($fromDbRecord['sid'],$fixtureRecord['sid']);
        $this->assertEquals($fromDbRecord['cohort_id'],$fixtureRecord['cohort_id']);
        $this->assertEquals($fromDbRecord['user_id'],$fixtureRecord['user_id']);
    }

    public function testIndexGET() {

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,'/students/index');

        // 2. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#StudentsIndex',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 3. Look for the create new student link
        $this->assertEquals(1, count($html->find('a#StudentAdd')));
        $unknownATag--;

        // 4. Ensure that there is a suitably named table to display the results.
        $this->table = $html->find('table#StudentsTable',0);
        $this->assertNotNull($this->table);

        // 5. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $this->thead = $this->table->find('thead',0);
        $thead_ths = $this->thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'sid');
        $this->assertEquals($thead_ths[1]->id, 'fullname');
        $this->assertEquals($thead_ths[2]->id, 'phonetic_name');
        $this->assertEquals($thead_ths[3]->id, 'cohort_nickname');
        $this->assertEquals($thead_ths[4]->id, 'username');
        $this->assertEquals($thead_ths[5]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,6); // no other columns

        // 6. Ensure that the tbody section has the same
        //    quantity of rows as the count of students records in the fixture.
        $this->tbody = $this->table->find('tbody',0);
        $tbody_rows = $this->tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->studentsFixture->records));

        // 7. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->studentsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $this->htmlRow = $values[1];
            $htmlColumns = $this->htmlRow->find('td');

            // 7.0 sid
            $this->assertEquals($fixtureRecord['sid'],  $htmlColumns[0]->plaintext);

            // 7.1 fullname is computed by the Student entity.
            $student = $this->students->get($fixtureRecord['id'],['contain' => ['Cohorts.Majors']]);
            $this->assertEquals($student->fullname, $htmlColumns[1]->plaintext);

            // 7.2 phonetic_name
            $this->assertEquals($fixtureRecord['phonetic_name'],  $htmlColumns[2]->plaintext);

            // 7.3 cohort_nickname is computed by the Cohort entity.
            $expected_value = is_null($student->cohort) ? '' : $student->cohort->nickname;
            $this->assertEquals( $expected_value, $htmlColumns[3]->plaintext);

            // 7.4 username requires finding the related value in the UsersFixture
            $user_id = $fixtureRecord['user_id'];
            if (is_null($user_id)) {
                $expectedValue='';
            } else {
                $user = $this->usersFixture->get($user_id);
                $expectedValue=$user['username'];
            }
            $this->assertEquals($expectedValue, $htmlColumns[4]->plaintext);

            // 7.5 Now examine the action links
            $this->td = $htmlColumns[5];
            $actionLinks = $this->td->find('a');
            $this->assertEquals('StudentView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('StudentEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('StudentDelete', $actionLinks[2]->name);
            $unknownATag--;

            // 7.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 8. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    // The view method will display the basic information about the student.  It will also
    // optionally display information about his grades. This is easier said than done because we must
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
    // 2. The view request references a Student that does not have a User, and has no request parameters.
    // In both cases verify:
    //   A. The body of the Student info
    //   B. That the Section select is "nothing selected".
    //   C. That the grading info section is not present.
    //
    // 3. The view request has a request parameter specifying a section_id. Whether or not
    // the Student has a User is unimportant. In this case verify the existence and correctness
    // of the grading info section.
    //

    // Scenario 1.
    public function testViewGETWithUser() {
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $fixtureRecord=$this->studentsFixture->records[0];
        $this->get('/students/view/' . $fixtureRecord['id']);
        $this->tstViewGet($fixtureRecord);
    }

    // Scenario 2.
    public function testViewGETWithOutUser() {
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $fixtureRecord=$this->studentsFixture->records[0];
        $this->get('/students/view/' . $fixtureRecord['id']);
        $this->tstViewGet($fixtureRecord);
    }


    // Scenario 3. Don't care which student. Send a section_id as a request
    // param. Examine grading info.
    public function testViewGETWithRequestParameters() {
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $section_id=$this->sectionsFixture->records[0]['id'];
        $this->get(
            '/students/view/'.$this->studentsFixture->records[0]['id'].
            '?section_id='.$section_id
        );
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

        // 2.3 section_id / $section['nickname']
        $section = $this->sections->get($section_id);
        if($this->inputCheckerB($this->form,'select#StudentViewSectionId option[selected]',$section_id,$section['nickname'])) $unknownSelectCnt--;

        // 2.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 3.  Look for the table that contains the grading info.
        $this->table = $html->find('table#StudentGradingTable',0);
        $this->assertNotNull($this->table);
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

        // 2.4 phonetic_name
        $field = $html->find('tr#phonetic_name td',0);
        $this->assertEquals($fixtureRecord['phonetic_name'], $field->plaintext);
        $unknownRowCnt--;

        // 2.5 cohort_name
        $field = $html->find('tr#cohort_nickname td',0);
        $student = $this->students->get($fixtureRecord['id'],['contain' => ['Cohorts.Majors']]);
        $this->assertEquals($student->cohort->nickname, $field->plaintext);
        $unknownRowCnt--;

        // 2.6 user_id requires finding the related value in the UsersFixture
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
        if($this->selectCheckerA($this->form,'StudentViewSectionId','sections_list')) $unknownSelectCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5.  Look for the table that contains the grading info.
        // It should _not_ be visible.
        $this->table = $html->find('table#StudentGradingTable',0);
        $this->assertNull($this->table);

        // 6. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#StudentsView',0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0,count($links));
    }

}
