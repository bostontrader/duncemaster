<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\InteractionsFixture;
use Cake\ORM\TableRegistry;

class InteractionsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.interactions'
    ];

    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/interactions/add');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('interaction'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=InteractionAddForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is empty
        $input = $form->find('input[id=InteractionTitle]')[0];
        $this->assertEquals($input->value, false);

        // Ensure that there's a field for sdesc, that is empty
        $input = $form->find('input[id=InteractionSDesc]')[0];
        $this->assertEquals($input->value, false);
    }

    public function testAddPOST() {

        $interactionsFixture = new InteractionsFixture();

        $this->fakeLogin();
        $this->post('/interactions/add', $interactionsFixture->newInteractionRecord);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/interactions' );

        // Now verify what we think just got written
        $interactions = TableRegistry::get('Interactions');
        $query = $interactions->find()->where(['id' => $interactionsFixture->newInteractionRecord['id']]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $interaction = $interactions->get($interactionsFixture->newInteractionRecord['id']);
        $this->assertEquals($interaction['title'],$interactionsFixture->newInteractionRecord['title']);
    }

    public function testDeletePOST() {

        $interactionsFixture = new InteractionsFixture();

        $this->fakeLogin();
        $this->post('/interactions/delete/' . $interactionsFixture->interaction1Record['id']);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/interactions' );

        // Now verify that the record no longer exists
        $interactions = TableRegistry::get('Interactions');
        $query = $interactions->find()->where(['id' => $interactionsFixture->interaction1Record['id']]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $interactionsFixture = new InteractionsFixture();

        $this->fakeLogin();
        $this->get('/interactions/edit/' . $interactionsFixture->interaction1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('interaction'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=InteractionEditForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is correctly set
        $input = $form->find('input[id=InteractionTitle]')[0];
        $this->assertEquals($input->value, $interactionsFixture->interaction1Record['title']);

        // Ensure that there's a field for sdesc, that is correctly set
        $input = $form->find('input[id=InteractionSDesc]')[0];
        $this->assertEquals($input->value,  $interactionsFixture->interaction1Record['sdesc']);

    }

    public function testEditPOST() {

        $interactionsFixture = new InteractionsFixture();

        $this->fakeLogin();
        $this->post('/interactions/edit/' . $interactionsFixture->interaction1Record['id'], $interactionsFixture->newInteractionRecord);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Now verify what we think just got written
        $interactions = TableRegistry::get('Interactions');
        $query = $interactions->find()->where(['id' => $interactionsFixture->interaction1Record['id']]);
        $c = $query->count();
        $this->assertEquals(1, $c);

        // Now retrieve that 1 record and compare to what we expect
        $interaction = $interactions->get($interactionsFixture->interaction1Record['id']);
        $this->assertEquals($interaction['title'],$interactionsFixture->newInteractionRecord['title']);

    }

    public function testIndexGET() {

        $this->fakeLogin();
        $result = $this->get('/interactions/index');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($result);

        // 1. Ensure that the single row of the thead section
        //    has a column for id and title, in that order
        //$rows = $html->find('table[id=interactions]',0)->find('thead',0)->find('tr');
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
        //    quantity of rows as the count of interaction records in the fixture.
        //    For each of these rows, ensure that the id and title match
        //$interactionFixture = new InteractionFixture();
        //$rowsInHTMLTable = $html->find('table[id=interactions]',0)->find('tbody',0)->find('tr');
        //$this->assertEqual(count($interactionFixture->records), count($rowsInHTMLTable));
        //$iterator = new MultipleIterator;
        //$iterator->attachIterator(new ArrayIterator($interactionFixture->records));
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

        $interactionsFixture = new InteractionsFixture();

        $this->fakeLogin();
        $this->get('/interactions/view/' . $interactionsFixture->interaction1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('interaction'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        //$form = $html->find('form[id=InteractionEditForm]')[0];
        //$this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is correctly set
        //$input = $form->find('input[id=InteractionTitle]')[0];
        //$this->assertEquals($input->value, $interactionsFixture->interaction1Record['title']);

        // Ensure that there's a field for sdesc, that is empty
        //$input = $form->find('input[id=InteractionSDesc]')[0];
        //$this->assertEquals($input->value, false);
    }

}
