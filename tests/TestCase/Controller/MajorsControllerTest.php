<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\MajorsFixture;
use Cake\ORM\TableRegistry;

class MajorsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.majors'
    ];

    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/majors/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('major'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#MajorAddForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // Ensure that there's an input field for title, of type text, and that it is empty
        $input = $form->find('input#MajorTitle',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);

        // Ensure that there's an input field for sdesc, of type text, and that it is empty
        $input = $form->find('input#MajorSDesc',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
    }

    public function testAddPOST() {

        $majorsFixture = new MajorsFixture();

        $this->fakeLogin();
        $this->post('/majors/add', $majorsFixture->newMajorRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/majors' );

        // Now verify what we think just got written
        $majors = TableRegistry::get('Majors');
        $new_id = FixtureConstants::major1_id + 1;
        $query = $majors->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_major = $majors->get($new_id);
        $this->assertEquals($new_major['title'],$majorsFixture->newMajorRecord['title']);
        $this->assertEquals($new_major['sdesc'],$majorsFixture->newMajorRecord['sdesc']);
    }

    public function testDeletePOST() {

        $majorsFixture = new MajorsFixture();

        $this->fakeLogin();
        $major_id = $majorsFixture->major1Record['id'];
        $this->post('/majors/delete/' . $major_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/majors' );

        // Now verify that the record no longer exists
        $majors = TableRegistry::get('Majors');
        $query = $majors->find()->where(['id' => $major_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $majorsFixture = new MajorsFixture();

        $this->fakeLogin();
        $major_id = $majorsFixture->major1Record['id'];
        $this->get('/majors/edit/' . $major_id);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('major'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#MajorEditForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // Ensure that there's an input field for title, of type text, and that it is correctly set
        $input = $form->find('input#MajorTitle',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $majorsFixture->major1Record['title']);

        // Ensure that there's an input field for sdesc, of type text, and that that it is correctly set
        $input = $form->find('input#MajorSDesc',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value,  $majorsFixture->major1Record['sdesc']);
    }

    public function testEditPOST() {

        $majorsFixture = new MajorsFixture();

        $this->fakeLogin();
        $major_id = $majorsFixture->major1Record['id'];
        $this->put('/majors/edit/' . $major_id, $majorsFixture->newMajorRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/majors');

        // Now verify what we think just got written
        $majors = TableRegistry::get('Majors');
        $query = $majors->find()->where(['id' => $major_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $major = $majors->get($major_id);
        $this->assertEquals($major['title'],$majorsFixture->newMajorRecord['title']);
        $this->assertEquals($major['sdesc'],$majorsFixture->newMajorRecord['sdesc']);
    }

    public function testIndexGET() {

        $this->fakeLogin();
        $this->get('/majors/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('majors'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // How shall we test the index?

        // 1. Ensure that there is a suitably named table to display the results.
        $majors_table = $html->find('table#majors',0);
        $this->assertNotNull($majors_table);

        // 2. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $majors_table->find('thead',0);
        $thead_ths = $thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'id');
        $this->assertEquals($thead_ths[1]->id, 'title');
        $this->assertEquals($thead_ths[2]->id, 'sdesc');
        $this->assertEquals($thead_ths[3]->id, 'actions');
        $this->assertEquals(count($thead_ths),4); // no other columns

        // 3. Ensure that the tbody section has the same
        //    quantity of rows as the count of major records in the fixture.
        $majorsFixture = new MajorsFixture();
        $tbody = $majors_table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($majorsFixture));

        // 4. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.  In order to do this we'll also need
        //    to read from the Majors table.
        $majors = TableRegistry::get('Majors');
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($majorsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');
            $this->assertEquals($fixtureRecord['id'], $htmlColumns[0]->plaintext);
            $this->assertEquals($fixtureRecord['title'],  $htmlColumns[1]->plaintext);
            $this->assertEquals($fixtureRecord['sdesc'],  $htmlColumns[2]->plaintext);
            
            // Ignore the action links
        }
    }

    public function testViewGET() {

        $majorsFixture = new MajorsFixture();

        $this->fakeLogin();
        $this->get('/majors/view/' . $majorsFixture->major1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('major'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // How shall we tet the view?  It doesn't have any enclosing table or structure so just
        // ignore that part.  Instead, look for individual display fields.
        $field = $html->find('td#id',0);
        $this->assertEquals($majorsFixture->major1Record['id'], $field->plaintext);

        $field = $html->find('td#title',0);
        $this->assertEquals($majorsFixture->major1Record['title'], $field->plaintext);

        $field = $html->find('td#sdesc',0);
        $this->assertEquals($majorsFixture->major1Record['sdesc'], $field->plaintext);
    }

}
