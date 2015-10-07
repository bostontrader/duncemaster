<?php
namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

require_once 'config\bootstrap.php';

/**
 * App\Controller\UsersController Test Case
 *
 * In this test I only want to test that:
 *
 * 1. A controller method exists...
 *
 * 2. Said method returns ResponseOK.
 *
 * 3. A bare minimum of html structure required to reasonbly verify correct operation
 *    and to facilitate TDD.  For example, the add method should return a form with certain fields.
 *
 * 4. Verify that the db has changed as expected, if applicable.
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

    public function testAddGet() {
        $this->fakeLogin();
        $this->get('/users/add');
        $html = 
        $this->assertResponseOk();
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




    //public function testAddPOST() {
        //$data = array(
            //'User' => array(
                //'username' => 'hendrix',
                //'is_active' => 1,
                //'is_admin' => 1
            //)
        //);
        //$result = $this->testAction('/users/add', array('data' => $data, 'return' => 'view', 'method' => 'POST'));
        //$newRecordId = $this->controller->User->id;
        //$newRecord = $this->controller->User->findById($newRecordId);
        //$this->assertEqual($data['User']['username'],  $newRecord['User']['username']);
        //$this->assertEqual($data['User']['is_active'], $newRecord['User']['is_active']);
        //$this->assertEqual($data['User']['is_admin'],  $newRecord['User']['is_admin']);
    //}

    //public function testDelete() {
        //$result = $this->testAction('/users/delete/1', array('method' => 'DELETE'));
        //$this->assertEqual($result, true);
        //$deletedRecord = $this->controller->User->findById(1);
        //$this->assertEqual(count($deletedRecord), 0);
    //}
    // Test for an http verb that the delete method should ignore.
    //public function testDeleteBadVerb() {
        //$result = $this->testAction('/users/delete', array('return' => 'view', 'method' => 'GET'));
        //$this->assertEqual($result, false);
    //}
    // Test for an http verb that the edit method should ignore.
    //public function testEditBadVerb() {
        //$result = $this->testAction('/users/edit', array('return' => 'view', 'method' => 'DELETE'));
        //$this->assertEqual($result, false);
    //}
    //public function testEditGET() {
        //$result = $this->testAction('/users/edit/1', array('return' => 'view', 'method' => 'GET'));
        //$html = str_get_html($result);
        //$userFixture = new UserFixture();
        //$fixtureRecord = $userFixture->records[0];
        //$form = $html->find('form[id=UserEditForm]')[0];
        // Omit the id field
        // Ensure that there's a field, labled Username, that contains the correct value
        //$label = $form->find('label[for=UserUsername]')[0];
        //$input = $form->find('input[id=UserUsername]')[0];
        //$this->assertEqual($label->plaintext, "Username");
        //$this->assertEqual($input->value, $fixtureRecord['username']);
        // Ensure that there's a field, labled 'Is active', that is set to the correct value
        //$label = $form->find('label[for=UserIsActive]')[0];
        //$input = $form->find('input[id=UserIsActive]')[0];
        //$this->assertEqual($label->plaintext, "Is Active");
        //$this->assertEqual($input->checked, ($fixtureRecord['is_active']?"checked":false) );
        // Ensure that there's a field, labled 'Is admin', that is set to the correct value
        //$label = $form->find('label[for=UserIsAdmin]')[0];
        //$input = $form->find('input[id=UserIsAdmin]')[0];
        //$this->assertEqual($label->plaintext, "Is Admin");
        //$this->assertEqual($input->checked, ($fixtureRecord['is_admin']?"checked":false));
    //}
    //public function testEditPUT() {
        //$data = array(
            //'User' => array(
                //'username' => 'hendrix',
                //'is_active' => 1,
                //'is_admin' => 1
            //)
        //);
        //$result = $this->testAction('/users/edit/1', array('data' => $data, 'return' => 'view', 'method' => 'PUT'));
        //$changedRecord = $this->controller->User->findById(1);
        //$this->assertEqual($data['User']['username'],  $changedRecord['User']['username']);
        //$this->assertEqual($data['User']['is_active'], $changedRecord['User']['is_active']);
        //$this->assertEqual($data['User']['is_admin'],  $changedRecord['User']['is_admin']);
    //}
    // Test for an http verb that the index method should ignore.
    //public function testIndexBadVerb() {
        //$result = $this->testAction('/users/add', array('return' => 'view', 'method' => 'DELETE'));
        //$this->assertEqual($result, false);
    //}

    public function testIndexGET() {
        //$result = $this->testAction('/users/index', array('return' => 'view', 'method' => 'GET'));
        // Set session data
        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 1,
                    'username' => 'testing',
                    // other keys.
                ]
            ]
        ]);
        $result = $this->get('/users/index');
        $this->assertResponseOk();
        //$html = str_get_html($result);
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

    // Test for an http verb that the view method should ignore.
    //public function testViewBadVerb()
        //$result = $this->testAction('/users/add', array('return' => 'view', 'method' => 'DELETE'));
        //$this->assertEqual($result, false);
    //}



    //public function testViewGET() {
        // This test will look for a user with an id=1.  The ids
        // are assigned using an autoincrement field that starts with 1.
        // The array of user fixture records use zero-based indexing.
        // Therefore the id number will be 1 higher than the index for
        // the corresponding record in the array of user fixture records.
        //$result = $this->testAction('/users/view/1', array('return' => 'view', 'method' => 'GET'));
        //$userFixture = new UserFixture();
        //$fixtureRecord = $userFixture->records[0];
        //$html = str_get_html($result);
        //$p = $html->find('p[id=id]');
        //$this->assertEqual($fixtureRecord['id'], $p[0]->plaintext);
        //$p = $html->find('p[id=username]');
        //$this->assertEqual($fixtureRecord['username'], $p[0]->plaintext);
        //$p = $html->find('p[id=is_active]');
        //$this->assertEqual($fixtureRecord['is_active'], $p[0]->plaintext);
        //$p = $html->find('p[id=is_admin]');
        //$this->assertEqual($fixtureRecord['is_admin'], $p[0]->plaintext);}
}
