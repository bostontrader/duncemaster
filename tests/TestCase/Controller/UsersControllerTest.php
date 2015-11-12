<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\UsersFixture;
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;

class UsersControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.roles',
        'app.roles_users',
        'app.users'
    ];

    private $users;
    private $usersFixture;

    public function setUp() {
        $this->users = TableRegistry::get('Users');
        $this->usersFixture = new UsersFixture();
    }

    public function testAddGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin();
        $this->get('/users/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#UserAddForm',0);
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

        // 4.3 Ensure that there's an input field for username, of type text, and that it is empty
        $input = $form->find('input#UserUsername',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // 4.4 Ensure that there's an input field for password, of type text, and that it is empty
        $input = $form->find('input#UserPassword',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // 4.5 Ensure that there's a select field for roles.ids, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->lookForSelect($form,'UserRoles','sections')) $unknownSelectCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#UsersAdd',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testAddPOST() {

        $this->fakeLogin();
        $this->post('/users/add', $this->usersFixture->newUserRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/users' );

        // Now verify what we think just got written
        $new_id = count($this->usersFixture->records) + 1;
        $query = $this->users->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_user = $this->users->get($new_id);
        $this->assertEquals($new_user['username'],$this->usersFixture->newUserRecord['username']);

        // The password is hashed and needs to be checked using the hashed-password checking mechanism
        $dph = new DefaultPasswordHasher();
        $this->assertTrue($dph->check($this->usersFixture->newUserRecord['password'], $new_user['password']));
    }

    public function testDeletePOST() {

        $this->fakeLogin();
        $user_id = $this->usersFixture->userAndyRecord['id'];
        $this->post('/users/delete/' . $user_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/users' );

        // Now verify that the record no longer exists
        $query = $this->users->find()->where(['id' => $user_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin();
        $this->get('/users/edit/' . $this->usersFixture->userAndyRecord['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#UserEditForm',0);
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
        if($this->lookForHiddenInput($form,'PUT')) $unknownInputCnt--;

        // 4.3 Ensure that there's an input field for username, of type text, and that it is correctly set
        $input = $form->find('input#UserUsername',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $this->usersFixture->userAndyRecord['username']);
        $unknownInputCnt--;

        // 4.4 Ensure that there's an input field for password, of type text, and that it is correctly
        // set. Note: In this special case, the password is not hashed, because the record was injected
        // by the FixtureManager, which apparently doesn't bother with such nicities.
        // The password is hashed and needs to be checked using the hashed-password checking mechanism.
        $input = $form->find('input#UserPassword',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $this->usersFixture->userAndyRecord['password']);
        $unknownInputCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#UsersEdit',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testEditPOST() {

        $this->fakeLogin();
        $user_id = $this->usersFixture->userAndyRecord['id'];
        $this->put('/users/edit/' . $user_id, $this->usersFixture->newUserRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/users');

        // Now verify what we think just got written
        $query = $this->users->find()->where(['id' => $user_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $user = $this->users->get($user_id);

        // Username
        $this->assertEquals($user['username'],$this->usersFixture->newUserRecord['username']);

        // The password is hashed and needs to be checked using the hashed-password checking mechanism.
        $dph = new DefaultPasswordHasher();
        $this->assertTrue($dph->check($this->usersFixture->newUserRecord['password'], $user['password']));
    }

    public function testIndexGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin();
        $this->get('/users/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $content = $html->find('div#UsersIndex',0);
        $this->assertNotNull($content);
        $unknownATag = count($content->find('a'));

        // 4. Look for the create new user link
        $this->assertEquals(1, count($html->find('a#UserAdd')));
        $unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        $users_table = $html->find('table#UsersTable',0);
        $this->assertNotNull($users_table);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $users_table->find('thead',0);
        $thead_ths = $thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'username');
        $this->assertEquals($thead_ths[1]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,2); // no other columns

        // 7. Ensure that the tbody section has the same
        //    quantity of rows as the count of users records in the fixture.
        $tbody = $users_table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->usersFixture->records));

        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->usersFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');

            // 8.0 username
            $this->assertEquals($fixtureRecord['username'],  $htmlColumns[0]->plaintext);

            // We don't need to display a password.  What's the point of displaying a long hashed, password?

            // 8.1 Now examine the action links
            $actionLinks = $htmlColumns[1]->find('a');
            $this->assertEquals('UserView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('UserEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('UserDelete', $actionLinks[2]->name);
            $unknownATag--;

            // 8.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 9. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testViewGET() {

        $this->fakeLogin();
        $user_id = $this->usersFixture->userAndyRecord['id'];
        $this->get('/users/view/' . $user_id);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 1.  Look for the table that contains the view fields.
        $table = $html->find('table#UserViewTable',0);
        $this->assertNotNull($table);

        // 2. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($table->find('tr'));

        // 2.1 username
        $field = $html->find('tr#username td',0);
        $this->assertEquals($this->usersFixture->userAndyRecord['username'], $field->plaintext);
        $unknownRowCnt--;

        // We don't need to display a password.  What's the point of displaying a long hashed, password?

        // Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 3. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#UsersView',0);
        $this->assertNotNull($content);

        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }
}
