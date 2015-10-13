<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\ClazzsFixture;
use Cake\ORM\TableRegistry;

class ClazzsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.clazzs'
    ];

    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/clazzs/add');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('clazz'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=ClazzAddForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is empty
        $input = $form->find('input[id=ClazzTitle]')[0];
        $this->assertEquals($input->value, false);

        // Ensure that there's a field for sdesc, that is empty
        $input = $form->find('input[id=ClazzSDesc]')[0];
        $this->assertEquals($input->value, false);
    }

    public function testAddPOST() {

        $clazzsFixture = new ClazzsFixture();

        $this->fakeLogin();
        $this->post('/clazzs/add', $clazzsFixture->newClazzRecord);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/clazzs' );

        // Now verify what we think just got written
        $clazzs = TableRegistry::get('Clazzs');
        $query = $clazzs->find()->where(['id' => $clazzsFixture->newClazzRecord['id']]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $clazz = $clazzs->get($clazzsFixture->newClazzRecord['id']);
        $this->assertEquals($clazz['title'],$clazzsFixture->newClazzRecord['title']);
    }

    public function testDeletePOST() {

        $clazzsFixture = new ClazzsFixture();

        $this->fakeLogin();
        $this->post('/clazzs/delete/' . $clazzsFixture->clazz1Record['id']);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/clazzs' );

        // Now verify that the record no longer exists
        $clazzs = TableRegistry::get('Clazzs');
        $query = $clazzs->find()->where(['id' => $clazzsFixture->clazz1Record['id']]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $clazzsFixture = new ClazzsFixture();

        $this->fakeLogin();
        $this->get('/clazzs/edit/' . $clazzsFixture->clazz1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('clazz'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=ClazzEditForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is correctly set
        $input = $form->find('input[id=ClazzTitle]')[0];
        $this->assertEquals($input->value, $clazzsFixture->clazz1Record['title']);

        // Ensure that there's a field for sdesc, that is correctly set
        $input = $form->find('input[id=ClazzSDesc]')[0];
        $this->assertEquals($input->value,  $clazzsFixture->clazz1Record['sdesc']);

    }

    public function testEditPOST() {

        $clazzsFixture = new ClazzsFixture();

        $this->fakeLogin();
        $this->post('/clazzs/edit/' . $clazzsFixture->clazz1Record['id'], $clazzsFixture->newClazzRecord);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Now verify what we think just got written
        $clazzs = TableRegistry::get('Clazzs');
        $query = $clazzs->find()->where(['id' => $clazzsFixture->clazz1Record['id']]);
        $c = $query->count();
        $this->assertEquals(1, $c);

        // Now retrieve that 1 record and compare to what we expect
        $clazz = $clazzs->get($clazzsFixture->clazz1Record['id']);
        $this->assertEquals($clazz['title'],$clazzsFixture->newClazzRecord['title']);

    }

    public function testIndexGET() {

        $this->fakeLogin();
        $result = $this->get('/clazzs/index');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($result);

        // 1. Ensure that the single row of the thead section
        //    has a column for id and title, in that order
        //$rows = $html->find('table[id=clazzs]',0)->find('thead',0)->find('tr');
        //$row_cnt = count($rows);
        //$this->assertEqual($row_cnt, 1);

        // 2. Ensure that the thead section has a heading
        //    for id, title, is_active, and is_admin.
        //$columns = $rows[0]->find('td');
        //$this->assertEqual($columns[0]->plaintext, 'id');
        //$this->assertEqual($columns[1]->plaintext, 'title');
        //$this->assertEqual($columns[2]->plaintext, 'is_active');
        //$this->assertEqual($columns[3]->plaintext, 'is_admin');

        // 3. Ensure that the tbody section has the same
        //    quantity of rows as the count of clazz records in the fixture.
        //    For each of these rows, ensure that the id and title match
        //$clazzFixture = new ClazzFixture();
        //$rowsInHTMLTable = $html->find('table[id=clazzs]',0)->find('tbody',0)->find('tr');
        //$this->assertEqual(count($clazzFixture->records), count($rowsInHTMLTable));
        //$iterator = new MultipleIterator;
        //$iterator->attachIterator(new ArrayIterator($clazzFixture->records));
        //$iterator->attachIterator(new ArrayIterator($rowsInHTMLTable));

        //foreach ($iterator as $values) {
        //$fixtureRecord = $values[0];
        //$htmlRow = $values[1];
        //$htmlColumns = $htmlRow->find('td');
        //$this->assertEqual($fixtureRecord['id'],        $htmlColumns[0]->plaintext);
        //$this->assertEqual($fixtureRecord['title'],  $htmlColumns[1]->plaintext);
        //$this->assertEqual($fixtureRecord['is_active'], $htmlColumns[2]->plaintext);
        //$this->assertEqual($fixtureRecord['is_admin'],  $htmlColumns[3]->plaintext);
        //}
    }

    public function testViewGET() {

        $clazzsFixture = new ClazzsFixture();

        $this->fakeLogin();
        $this->get('/clazzs/view/' . $clazzsFixture->clazz1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('clazz'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        //$form = $html->find('form[id=ClazzEditForm]')[0];
        //$this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is correctly set
        //$input = $form->find('input[id=ClazzTitle]')[0];
        //$this->assertEquals($input->value, $clazzsFixture->clazz1Record['title']);

        // Ensure that there's a field for sdesc, that is empty
        //$input = $form->find('input[id=ClazzSDesc]')[0];
        //$this->assertEquals($input->value, false);
    }

}
