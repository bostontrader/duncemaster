<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;

class UsersControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.roles',
        'app.roles_users',
        'app.students',
        'app.teachers',
        'app.users'
    ];

    private $users;

    public function setUp() {
        parent::setUp();
        $this->users = TableRegistry::get('Users');
    }

    public function testLoginGET() {

        // 1. Don't login, but GET the url, and parse the response.
        $html=$this->loginRequestResponse(null,'/users/login');

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#UserLoginForm',0);
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

        // 3.3 Ensure that there's an input field for username, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#UserUsername')) $unknownInputCnt--;

        // 3.4 Ensure that there's an input field for password, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#UserPassword')) $unknownInputCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#UsersLogin');
    }

    // This is a completely different pattern so we can't use the conventional test.
    // Instead of observing changes to the db, we need to observe the changes
    // to the Auth component.
    public function testLoginPOST() {

        // 1. Login with credentials expected to not pass.
        $this->tstLoginPOST('baduser','badpassword', false);

        // 2. Login as an admin. This is expected to pass, but there should be no associated teacher or student.
        $this->tstLoginPOST(FixtureConstants::userAndyAdminUsername,
            FixtureConstants::userAndyAdminPw, true);

        // 3. Login as an advisor. This is expected to pass, but there should be no associated teacher or student.
        $this->tstLoginPOST(FixtureConstants::userArnoldAdvisorUsername,
            FixtureConstants::userArnoldAdvisorPw, true, false, false);

        // 4.1 Login as a user with the role of teacher, but who is not associated with a
        // teacher record. This is expected to pass. The Auth->user should have a teachers_id key,
        // set to null, but no students_id key.
        $this->tstLoginPOST(FixtureConstants::userTerryTeacherUsername,
            FixtureConstants::userTerryTeacherPw, true, true, null, false);

        // 4.2 Login as a user with the role of teacher and who is associated with a
        // teacher record. This is expected to pass. The Auth->user should have a teachers_id key,
        // set to the teachers_id, but no students_id key.
        $this->tstLoginPOST(FixtureConstants::userTommyTeacherUsername,
            FixtureConstants::userTommyTeacherPw, true, true, FixtureConstants::teacherTypical, false);

        // 5.1 Login as a user with the role of student, but who is not associated with a
        // student record. This is expected to pass. The Auth->user should have a students_id key,
        // set to null, but no teachers_id key.
        $this->tstLoginPOST(FixtureConstants::userSuzyStudentUsername,
            FixtureConstants::userSuzyStudentPw, true, false, null, true, null);

        // 5.2 Login as a user with the role of student and who is associated with a
        // student record. This is expected to pass. The Auth->user should have a students_id key,
        // set to the students_id, but no teachers_id key.
        $this->tstLoginPOST(FixtureConstants::userSallyStudentUsername,
            FixtureConstants::userSallyStudentPw, true, false, null, true, FixtureConstants::studentTypical);

        // 6. Login as a user with the roles of both student and teacher, both of which
        // are associated with student or teacher records.  This is expected to pass. The Auth->user
        // should have a students_id key set to the students_id and a teachers_id key set to the
        // teachers_id.
        $this->tstLoginPOST(FixtureConstants::userTammyTeacherAndStudentUsername,
            FixtureConstants::userTammyTeacherAndStudentPw, true,
            true, FixtureConstants::teacherAndStudent,
            true, FixtureConstants::studentAndTeacher);
    }

    private function tstLoginPOST($username, $password, $expectPass, 
        $expectTeacher=false, $expectedTeachersId=null, $expectStudent=false, $expectedStudentsId=null) {

        // 1. Attempt to login, observe redirect
        $credentials=['username'=>$username,'password'=>$password];
        $this->post('/users/login', $credentials);

        // 2. Verify login results
        $authUser = $this->_controller->Auth->user();

        if($expectPass) {
            $this->assertResponseSuccess(); // 2xx, 3xx
            $this->assertRedirect('/');

            $this->assertTrue(array_key_exists('teachers_id', $authUser)==$expectTeacher);
            if($expectTeacher)
                $this->assertTrue($authUser['teachers_id']==$expectedTeachersId);

            $this->assertTrue(array_key_exists('students_id', $authUser)==$expectStudent);
            if($expectStudent)
                $this->assertTrue($authUser['students_id']==$expectedStudentsId);

        } else {
            $this->assertResponseOk(); // 2xx
            $this->assertNoRedirect();
            $this->assertNull($authUser);
        }
    }

    // Test that unauthenticated users, when submitting a request to
    // an action, will get redirected to the login url.
    public function testUnauthenticatedActionsAndUsers() {
        $this->tstUnauthenticatedActionsAndUsers('users');
    }

    // Test that users who do not have correct roles, when submitting a request to
    // an action, will get redirected to the home page.
    public function testUnauthorizedActionsAndUsers() {
        $this->tstUnauthorizedActionsAndUsers('users');
    }

    public function testAddGET() {

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,'/users/add');

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#UserAddForm',0);
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

        // 3.3 Ensure that there's an input field for username, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#UserUsername')) $unknownInputCnt--;

        // 3.4 Ensure that there's an input field for password, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#UserPassword')) $unknownInputCnt--;

        // 3.5 Ensure that there's a select field for roles.ids, that it has no selection,
        if($this->selectCheckerA($form, 'UserRoles', 'roles')) $unknownSelectCnt--;

        // 3.6 Because UserRoles is a multi-select, there should also be an associated
        // hidden input. Note: supply blank value argument because we're looking
        // for a blank value. Don't want the default value.
        if($this->lookForHiddenInput($form, 'roles[_ids]', '')) $unknownInputCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#UsersAdd');
    }

    public function testAddPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->usersFixture->newUserRecord;
        $fromDbRecord=$this->genericAddPostProlog(
            FixtureConstants::userAndyAdminId,
            '/users/add', $fixtureRecord,
            '/users', $this->users
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['username'],$fixtureRecord['username']);

        // 3. The password is hashed and needs to be checked using the hashed-password checking mechanism
        $dph = new DefaultPasswordHasher();
        $this->assertTrue($dph->check($fixtureRecord['password'], $fromDbRecord['password']));
    }

    public function testDeletePOST() {

        $user_id = $this->usersFixture->records[0]['id'];
        $this->deletePOST(
            FixtureConstants::userAndyAdminId, '/users/delete/',
            $user_id, '/users', $this->users
        );
    }

    public function testEditGET() {

        // 1. Obtain a record to edit, login, GET the url, parse the response and send it back.
        $record2Edit=$this->usersFixture->records[0];
        $url='/users/edit/' . $record2Edit['id'];
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,$url);

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#UserEditForm',0);
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

        // 3.3 Ensure that there's an input field for username, of type text, and that it is correctly set
        if($this->inputCheckerA($form,'input#UserUsername',
            $record2Edit['username'])) $unknownInputCnt--;

        // 3.4 Ensure that there's an input field for password, of type text, and that it is correctly
        // set. Note: In this special case, the password is not hashed, because the record was injected
        // by the FixtureManager, which apparently doesn't bother with such niceties.
        // The password is hashed and needs to be checked using the hashed-password checking mechanism.
        if($this->inputCheckerA($form,'input#UserPassword',
            $record2Edit['password'])) $unknownInputCnt--;

        // 3.5 check for properly set roles. For now, assume it's ok
        $unknownSelectCnt--;
        
        // 3.6 Because UserRoles is a multi-select, there should also be an associated
        // hidden input. Note: supply blank value argument because we're looking
        // for a blank value. Don't want the default value.
        if($this->lookForHiddenInput($form, 'roles[_ids]', '')) $unknownInputCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#UsersEdit');
    }

    public function testEditPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->usersFixture->newUserRecord;
        $fromDbRecord=$this->genericEditPutProlog(
            FixtureConstants::userAndyAdminId,
            '/users/edit', $fixtureRecord,
            '/users', $this->users
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['username'],$fixtureRecord['username']);

        // 3. The password is hashed and needs to be checked using the hashed-password checking mechanism.
        $dph = new DefaultPasswordHasher();
        $this->assertTrue($dph->check($fixtureRecord['password'], $fromDbRecord['password']));
    }

    public function testIndexGET() {

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,'/users/index');

        // 2. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#UsersIndex',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 3. Look for the create new user link
        $this->assertEquals(1, count($html->find('a#UserAdd')));
        $unknownATag--;

        // 4. Ensure that there is a suitably named table to display the results.
        $this->table = $html->find('table#UsersTable',0);
        $this->assertNotNull($this->table);

        // 5. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $this->thead = $this->table->find('thead',0);
        $thead_ths = $this->thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'username');
        $this->assertEquals($thead_ths[1]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,2); // no other columns

        // 6. Ensure that the tbody section has the same
        //    quantity of rows as the count of users records in the fixture.
        $this->tbody = $this->table->find('tbody',0);
        $tbody_rows = $this->tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->usersFixture->records));

        // 7. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->usersFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $this->htmlRow = $values[1];
            $htmlColumns = $this->htmlRow->find('td');

            // 7.0 username
            $this->assertEquals($fixtureRecord['username'],  $htmlColumns[0]->plaintext);

            // We don't need to display a password.  What's the point of displaying a long hashed, password?

            // 7.1 Now examine the action links
            $this->td = $htmlColumns[1];
            $actionLinks = $this->td->find('a');
            $this->assertEquals('UserView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('UserEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('UserDelete', $actionLinks[2]->name);
            $unknownATag--;

            // 7.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 8. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }



    public function testViewGET() {

        // 1. Obtain a record to view, login, GET the url, parse the response and send it back.
        $record2View=$this->usersFixture->records[0];
        $url='/users/view/' . $record2View['id'];
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,$url);
        
        // 2.  Look for the table that contains the view fields.
        $this->table = $html->find('table#UserViewTable',0);
        $this->assertNotNull($this->table);

        // 3. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($this->table->find('tr'));

        // 3.1 username
        $field = $html->find('tr#username td',0);
        $this->assertEquals($record2View['username'], $field->plaintext);
        $unknownRowCnt--;

        // We don't need to display a password.  What's the point of displaying a long hashed, password?

        // Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 3. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#UsersView',0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0,count($links));
    }
}
