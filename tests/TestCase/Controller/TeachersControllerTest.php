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

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/teachers/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $this->form = $html->find('form#TeacherAddForm',0);
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
        if($this->inputCheckerA($this->form,'input#TeacherFamName')) $unknownInputCnt--;

        // 4.4 Ensure that there's an input field for giv_name, of type text, and that it is empty
        if($this->inputCheckerA($this->form,'input#TeacherGivName')) $unknownInputCnt--;

        // 4.5 Ensure that there's a select field for user_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($this->form, 'TeacherUserId', 'users')) $unknownSelectCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#TeachersAdd',0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testAddPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->post('/teachers/add', $this->teachersFixture->newTeacherRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/teachers' );

        // Now verify what we think just got written
        $new_id = count($this->teachersFixture->records) + 1;
        $query = $this->teachers->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_teacher = $this->teachers->get($new_id);
        $this->assertEquals($new_teacher['fam_name'],$this->teachersFixture->newTeacherRecord['fam_name']);
        $this->assertEquals($new_teacher['giv_name'],$this->teachersFixture->newTeacherRecord['giv_name']);
        $this->assertEquals($new_teacher['user_id'],$this->teachersFixture->newTeacherRecord['user_id']);
    }

    public function testDeletePOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $teacher_id = $this->teachersFixture->teacher1Record['id'];
        $this->post('/teachers/delete/' . $teacher_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/teachers' );

        // Now verify that the record no longer exists
        $query = $this->teachers->find()->where(['id' => $teacher_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/teachers/edit/' . $this->teachersFixture->teacher1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $this->form = $html->find('form#TeacherEditForm',0);
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

        // 4.3 Ensure that there's an input field for fam_name, of type text, and that it is correctly set
        if($this->inputCheckerA($this->form,'input#TeacherFamName',
            $this->teachersFixture->teacher1Record['fam_name'])) $unknownInputCnt--;

        // 4.4 Ensure that there's an input field for giv_name, of type text, and that it is correctly set
        if($this->inputCheckerA($this->form,'input#TeacherGivName',
            $this->teachersFixture->teacher1Record['giv_name'])) $unknownInputCnt--;

        // 4.5. Ensure that there's a select field for user_id and that it is correctly set
        $option = $this->form->find('select#TeacherUserId option[selected]',0);
        $user_id = $this->teachersFixture->teacher1Record['user_id'];
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
        $this->content = $html->find('div#TeachersEdit',0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testEditPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $teacher_id = $this->teachersFixture->teacher1Record['id'];
        $this->put('/teachers/edit/' . $teacher_id, $this->teachersFixture->newTeacherRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/teachers');

        // Now verify what we think just got written
        $query = $this->teachers->find()->where(['id' => $this->teachersFixture->teacher1Record['id']]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $teacher = $this->teachers->get($this->teachersFixture->teacher1Record['id']);
        $this->assertEquals($teacher['fam_name'],$this->teachersFixture->newTeacherRecord['fam_name']);
        $this->assertEquals($teacher['giv_name'],$this->teachersFixture->newTeacherRecord['giv_name']);
        $this->assertEquals($teacher['user_id'],$this->teachersFixture->newTeacherRecord['user_id']);
    }

    public function testIndexGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/teachers/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#TeachersIndex',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 4. Look for the create new subject link
        $this->assertEquals(1, count($html->find('a#TeacherAdd')));
        $unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        $this->table = $html->find('table#TeachersTable',0);
        $this->assertNotNull($this->table);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $this->thead = $this->table->find('thead',0);
        $thead_ths = $this->thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'fam_name');
        $this->assertEquals($thead_ths[1]->id, 'giv_name');
        $this->assertEquals($thead_ths[2]->id, 'username');
        $this->assertEquals($thead_ths[3]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,4); // no other columns

        // 7. Ensure that the tbody section has the same
        //    quantity of rows as the count of subject records in the fixture.
        $this->tbody = $this->table->find('tbody',0);
        $tbody_rows = $this->tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->teachersFixture->records));
        
        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->teachersFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $this->htmlRow = $values[1];
            $htmlColumns = $this->htmlRow->find('td');

            // 8.0 fam_name
            $this->assertEquals($fixtureRecord['fam_name'], $htmlColumns[0]->plaintext);

            // 8.1 giv_name
            $this->assertEquals($fixtureRecord['giv_name'], $htmlColumns[1]->plaintext);

            // 8.2 username requires finding the related value in the UsersFixture
            $user_id = $fixtureRecord['user_id'];
            if (is_null($user_id)) {
                $expectedValue='';
            } else {
                $user = $this->usersFixture->get($user_id);
                $expectedValue=$user['username'];
            }
            $this->assertEquals($expectedValue, $htmlColumns[2]->plaintext);

            // 8.3 Now examine the action links
            $this->td = $htmlColumns[3];
            $actionLinks = $this->td->find('a');
            $this->assertEquals('TeacherView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('TeacherEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('TeacherDelete', $actionLinks[2]->name);
            $unknownATag--;

            // 8.9 No other columns
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
        $fixtureRecord=$this->teachersFixture->teacher1Record;
        $this->get('/teachers/view/' . $fixtureRecord['id']);
        $this->tstViewGet($fixtureRecord);
    }

    public function testViewGETWithOutUser() {
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $fixtureRecord=$this->teachersFixture->teacher2Record;
        $this->get('/teachers/view/' . $fixtureRecord['id']);
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
