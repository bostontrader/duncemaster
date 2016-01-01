<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\TeachersFixture;
use Cake\ORM\TableRegistry;

class TeachersControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.roles',
        'app.roles_users',
        'app.teachers',
        'app.users'
    ];

    /* @var \App\Model\Table\TeachersTable */
    private $teachers;

    /* @var \App\Test\Fixture\TeachersFixture */
    private $teachersFixture;

    public function setUp() {
        parent::setUp();
        $this->teachers = TableRegistry::get('Teachers');
        $this->teachersFixture = new TeachersFixture();
    }

    // Test that unauthenticated users, when submitting a request to
    // an action, will get redirected to the login url.
    public function testUnauthenticatedActionsAndUsers() {
        $this->tstUnauthenticatedActionsAndUsers('teachers');
    }

    // Test that users who do not have correct roles, when submitting a request to
    // an action, will get redirected to the home page.
    public function testUnauthorizedActionsAndUsers() {
        $this->tstUnauthorizedActionsAndUsers('teachers');
    }

    public function testAddGET() {

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,'/teachers/add');

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#TeacherAddForm',0);
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

        // 3.3 Ensure that there's an input field for fam_name, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#TeacherFamName')) $unknownInputCnt--;

        // 3.4 Ensure that there's an input field for giv_name, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#TeacherGivName')) $unknownInputCnt--;

        // 3.5 Ensure that there's a select field for user_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($form, 'TeacherUserId', 'users')) $unknownSelectCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#TeachersAdd');
    }

    public function testAddPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->teachersFixture->newTeacherRecord;
        $fromDbRecord=$this->genericAddPostProlog(
            FixtureConstants::userAndyAdminId,
            '/teachers/add', $fixtureRecord,
            '/teachers', $this->teachers
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['fam_name'],$fixtureRecord['fam_name']);
        $this->assertEquals($fromDbRecord['giv_name'],$fixtureRecord['giv_name']);
        $this->assertEquals($fromDbRecord['user_id'],$fixtureRecord['user_id']);
    }

    public function testDeletePOST() {

        $teacher_id = $this->teachersFixture->records[0]['id'];
        $this->deletePOST(
            FixtureConstants::userAndyAdminId, '/teachers/delete/',
            $teacher_id, '/teachers', $this->teachers
        );
    }

    public function testEditGET() {
        $this->tstEditGET(FixtureConstants::teacherTypical);
        $this->tstEditGET(FixtureConstants::teacherUserIdNull);
    }

    private function tstEditGET($teacher_id) {

        // 1. Obtain a record to edit, login, GET the url, parse the response and send it back.
        $record2Edit=$this->teachersFixture->get($teacher_id);
        $url='/teachers/edit/' . $teacher_id;
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,$url);

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#TeacherEditForm',0);
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

        // 3.3 Ensure that there's an input field for fam_name, of type text, and that it is correctly set
        if($this->inputCheckerA($form,'input#TeacherFamName',
            $record2Edit['fam_name'])) $unknownInputCnt--;

        // 3.4 Ensure that there's an input field for giv_name, of type text, and that it is correctly set
        if($this->inputCheckerA($form,'input#TeacherGivName',
            $record2Edit['giv_name'])) $unknownInputCnt--;

        // 3.5. user_id / $user_fixture_record['username']
        $user_id = $record2Edit['user_id'];
        if(is_null($user_id)) {
            if($this->selectCheckerA($form, 'TeacherUserId','users')) $unknownSelectCnt--;
        } else {
            $user = $this->usersFixture->get($user_id);
            if($this->inputCheckerB($form,'select#TeacherUserId option[selected]',$user_id,$user['username'])) $unknownSelectCnt--;
        }
        
        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#TeachersEdit');
    }

    public function testEditPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->teachersFixture->newTeacherRecord;
        $fromDbRecord=$this->genericEditPutProlog(
            FixtureConstants::userAndyAdminId,
            '/teachers/edit', $fixtureRecord,
            '/teachers', $this->teachers
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['fam_name'],$fixtureRecord['fam_name']);
        $this->assertEquals($fromDbRecord['giv_name'],$fixtureRecord['giv_name']);
        $this->assertEquals($fromDbRecord['user_id'],$fixtureRecord['user_id']);
    }

    public function testIndexGET() {

        // 1. Simulate login, submit request, examine response.
        /*$this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/teachers/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());*/

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,'/teachers/index');

        // 2. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#TeachersIndex',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 3. Look for the create new subject link
        $this->assertEquals(1, count($html->find('a#TeacherAdd')));
        $unknownATag--;

        // 4. Ensure that there is a suitably named table to display the results.
        $this->table = $html->find('table#TeachersTable',0);
        $this->assertNotNull($this->table);

        // 5. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $this->thead = $this->table->find('thead',0);
        $thead_ths = $this->thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'fam_name');
        $this->assertEquals($thead_ths[1]->id, 'giv_name');
        $this->assertEquals($thead_ths[2]->id, 'username');
        $this->assertEquals($thead_ths[3]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,4); // no other columns

        // 6. Ensure that the tbody section has the same
        //    quantity of rows as the count of subject records in the fixture.
        $this->tbody = $this->table->find('tbody',0);
        $tbody_rows = $this->tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->teachersFixture->records));
        
        // 7. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->teachersFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $this->htmlRow = $values[1];
            $htmlColumns = $this->htmlRow->find('td');

            // 7.0 fam_name
            $this->assertEquals($fixtureRecord['fam_name'], $htmlColumns[0]->plaintext);

            // 7.1 giv_name
            $this->assertEquals($fixtureRecord['giv_name'], $htmlColumns[1]->plaintext);

            // 7.2 username requires finding the related value in the UsersFixture
            $user_id = $fixtureRecord['user_id'];
            if (is_null($user_id)) {
                $expectedValue='';
            } else {
                $user = $this->usersFixture->get($user_id);
                $expectedValue=$user['username'];
            }
            $this->assertEquals($expectedValue, $htmlColumns[2]->plaintext);

            // 7.3 Now examine the action links
            $this->td = $htmlColumns[3];
            $actionLinks = $this->td->find('a');
            $this->assertEquals('TeacherView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('TeacherEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('TeacherDelete', $actionLinks[2]->name);
            $unknownATag--;

            // 7.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 9. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    // We need two tests for View:
    // View a teacher with an associated user
    // View a teacher without an associated user
    public function testViewGETWithUser() {
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $teacher_id = FixtureConstants::teacherTypical;
        $fixtureRecord=$this->teachersFixture->get($teacher_id);
        $this->get('/teachers/view/' . $teacher_id);
        $this->tstViewGet($fixtureRecord);
    }

    public function testViewGETWithOutUser() {
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $teacher_id = FixtureConstants::teacherTypical;
        $fixtureRecord=$this->teachersFixture->get($teacher_id);
        $this->get('/teachers/view/' . $teacher_id);
        $this->tstViewGet($fixtureRecord);
    }

    private function tstViewGET($fixtureRecord) {

        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 1.  Look for the table that contains the view fields.
        $this->table = $html->find('table#TeacherViewTable',0);
        $this->assertNotNull($this->table);

        // 2. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($this->table->find('tr'));

        // 2.1 fam_name
        $this->field = $this->table->find('tr#fam_name td',0);
        $this->assertEquals($fixtureRecord['fam_name'], $this->field->plaintext);
        $unknownRowCnt--;

        // 2.2 giv_name
        $this->field = $this->table->find('tr#giv_name td',0);
        $this->assertEquals($fixtureRecord['giv_name'], $this->field->plaintext);
        $unknownRowCnt--;

        // 2.3 user_id requires finding the related value in the UsersFixture
        $this->field = $html->find('tr#username td',0);
        $user_id = $fixtureRecord['user_id'];
        $user = $this->usersFixture->get($user_id);

        $this->assertEquals($user['username'], $this->field->plaintext);
        $unknownRowCnt--;

        // 2.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 3. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#TeachersView',0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0,count($links));
    }

}
