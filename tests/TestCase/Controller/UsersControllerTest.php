<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\UsersFixture;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

require_once 'simple_html_dom.php';

/**
 * App\Controller\UsersController Test Case
 *
 * In this test I only want to test that:
 *
 * 1. A controller method exists...
 *
 * 2. Said method returns ResponseOK.
 *
 * 3. Said method does or does not redirect.  If it redirects, then where to?
 *
 * 4. A bare minimum of html structure required to reasonably verify correct operation
 *    and to facilitate TDD.  For example, the add method should return a form with certain fields.
 *
 * 5. Verify that the db has changed as expected, if applicable.
 *
 * I do not want to test:
 *
 * 1. Whether or not Auth prevents/allows access to a method.
 *
 * 2. How the method responds to badly formed requests, such as trying to submit a DELETE to the add method.
 *
 * 3. Any html structure, formatting, css, scripts, tags, krakens, or whatever, beyond the bare minimum
 *    listed above.
 *
 * These items should be tested elsewhere.
 *
 */
class UsersControllerTest extends IntegrationTestCase {

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.users'
    ];

    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/users/add');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('user'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=UserAddForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for username, that is empty
        $input = $form->find('input[id=UserUsername]')[0];
        $this->assertEquals($input->value, false);

        // Ensure that there's a field for password, that is empty
        $input = $form->find('input[id=UserPassword]')[0];
        $this->assertEquals($input->value, false);
    }

    public function testAddPOST() {

        $usersFixture = new UsersFixture();

        $this->fakeLogin();
        $this->post('/users/add', $usersFixture->newUserRecord);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/users' );

        // Now verify what we think just got written
        $users = TableRegistry::get('Users');
        $query = $users->find()->where(['id' => $usersFixture->newUserRecord['id']]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $user = $users->get($usersFixture->newUserRecord['id']);
        $this->assertEquals($user['username'],$usersFixture->newUserRecord['username']);
    }

    public function testDeletePOST() {

        $usersFixture = new UsersFixture();

        $this->fakeLogin();
        $this->post('/users/delete/' . $usersFixture->adminRecord['id']);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/users' );

        // Now verify that the record no longer exists
        $users = TableRegistry::get('Users');
        $query = $users->find()->where(['id' => $usersFixture->adminRecord['id']]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $usersFixture = new UsersFixture();

        $this->fakeLogin();
        $this->get('/users/edit/' . $usersFixture->adminRecord['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('user'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=UserEditForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for username, that is correctly set
        $input = $form->find('input[id=UserUsername]')[0];
        $this->assertEquals($input->value, $usersFixture->adminRecord['username']);

        // Ensure that there's a field for password, that is empty
        $input = $form->find('input[id=UserPassword]')[0];
        $this->assertEquals($input->value, false);

    }

    public function testEditPOST() {

        $usersFixture = new UsersFixture();

        $this->fakeLogin();
        $this->post('/users/edit/' . $usersFixture->adminRecord['id'], $usersFixture->newUserRecord);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Now verify what we think just got written
        $users = TableRegistry::get('Users');
        $query = $users->find()->where(['id' => $usersFixture->adminRecord['id']]);
        $c = $query->count();
        $this->assertEquals(1, $c);

        // Now retrieve that 1 record and compare to what we expect
        $user = $users->get($usersFixture->adminRecord['id']);
        $this->assertEquals($user['username'],$usersFixture->newUserRecord['username']);

    }

    public function testIndexGET() {

        $this->fakeLogin();
        $result = $this->get('/users/index');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($result);

        // 1. Ensure that the single row of the thead section
        //    has a column for id and username, in that order
        //$rows = $html->find('table[id=users]',0)->find('thead',0)->find('tr');
        //$row_cnt = count($rows);
        //$this->assertEqual($row_cnt, 1);

        // 2. Ensure that the thead section has a heading
        //    for id, username, is_active, and is_admin.
        //$columns = $rows[0]->find('td');
        //$this->assertEqual($columns[0]->plaintext, 'id');
        //$this->assertEqual($columns[1]->plaintext, 'username');
        //$this->assertEqual($columns[2]->plaintext, 'is_active');
        //$this->assertEqual($columns[3]->plaintext, 'is_admin');

        // 3. Ensure that the tbody section has the same
        //    quantity of rows as the count of user records in the fixture.
        //    For each of these rows, ensure that the id and username match
        //$userFixture = new UserFixture();
        //$rowsInHTMLTable = $html->find('table[id=users]',0)->find('tbody',0)->find('tr');
        //$this->assertEqual(count($userFixture->records), count($rowsInHTMLTable));
        //$iterator = new MultipleIterator;
        //$iterator->attachIterator(new ArrayIterator($userFixture->records));
        //$iterator->attachIterator(new ArrayIterator($rowsInHTMLTable));

        //foreach ($iterator as $values) {
        //$fixtureRecord = $values[0];
        //$htmlRow = $values[1];
        //$htmlColumns = $htmlRow->find('td');
        //$this->assertEqual($fixtureRecord['id'],        $htmlColumns[0]->plaintext);
        //$this->assertEqual($fixtureRecord['username'],  $htmlColumns[1]->plaintext);
        //$this->assertEqual($fixtureRecord['is_active'], $htmlColumns[2]->plaintext);
        //$this->assertEqual($fixtureRecord['is_admin'],  $htmlColumns[3]->plaintext);
        //}
    }

    public function testViewGET() {

        $usersFixture = new UsersFixture();

        $this->fakeLogin();
        $this->get('/users/view/' . $usersFixture->adminRecord['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('user'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        //$form = $html->find('form[id=UserEditForm]')[0];
        //$this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for username, that is correctly set
        //$input = $form->find('input[id=UserUsername]')[0];
        //$this->assertEquals($input->value, $usersFixture->adminRecord['username']);

        // Ensure that there's a field for password, that is empty
        //$input = $form->find('input[id=UserPassword]')[0];
        //$this->assertEquals($input->value, false);
    }

    // Hack the session to make it look as if we're properly logged in.
    private function fakeLogin() {
        // Set session data
        $this->session(
            [
                'Auth' => [
                    'User' => [
                        'id' => 1,
                        'username' => 'testing',
                    ]
                ]
            ]
        );
    }

}
