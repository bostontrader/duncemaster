<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\SemestersFixture;
use Cake\ORM\TableRegistry;

class SemestersControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.semesters'
    ];

    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/semesters/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('semester'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#SemesterAddForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // Ensure that there's an input field for year, of type text, and that it is empty
        $input = $form->find('input#SemesterYear',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);

        // Ensure that there's an input field for seq, of type text, and that it is empty
        $input = $form->find('input#SemesterSeq',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
    }

    public function testAddPOST() {

        $semestersFixture = new SemestersFixture();

        $this->fakeLogin();
        $this->post('/semesters/add', $semestersFixture->newSemesterRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/semesters' );

        // Now verify what we think just got written
        $semesters = TableRegistry::get('Semesters');
        $new_id = FixtureConstants::semester1_id + 1;
        $query = $semesters->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_semester = $semesters->get($new_id);
        $this->assertEquals($new_semester['year'],$semestersFixture->newSemesterRecord['year']);
        $this->assertEquals($new_semester['seq'],$semestersFixture->newSemesterRecord['seq']);
    }

    public function testDeletePOST() {

        $semestersFixture = new SemestersFixture();

        $this->fakeLogin();
        $semester_id = $semestersFixture->semester1Record['id'];
        $this->post('/semesters/delete/' . $semester_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/semesters' );

        // Now verify that the record no longer exists
        $semesters = TableRegistry::get('Semesters');
        $query = $semesters->find()->where(['id' => $semester_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $semestersFixture = new SemestersFixture();

        $this->fakeLogin();
        $semester_id = $semestersFixture->semester1Record['id'];
        $this->get('/semesters/edit/' . $semester_id);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('semester'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#SemesterEditForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // Ensure that there's an input field for year, of type text, and that it is correctly set
        $input = $form->find('input#SemesterYear',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $semestersFixture->semester1Record['year']);

        // Ensure that there's an input field for seq, of type text, and that it is correctly set
        $input = $form->find('input#SemesterSeq',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value,  $semestersFixture->semester1Record['seq']);
    }

    public function testEditPOST() {

        $semestersFixture = new SemestersFixture();

        $this->fakeLogin();
        $semester_id = $semestersFixture->semester1Record['id'];
        $this->put('/semesters/edit/' . $semester_id, $semestersFixture->newSemesterRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/semesters');

        // Now verify what we think just got written
        $semesters = TableRegistry::get('Semesters');
        $query = $semesters->find()->where(['id' => $semester_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $semester = $semesters->get($semestersFixture->semester1Record['id']);
        $this->assertEquals($semester['year'],$semestersFixture->newSemesterRecord['year']);
        $this->assertEquals($semester['seq'],$semestersFixture->newSemesterRecord['seq']);
    }

    public function testIndexGET() {

        $this->fakeLogin();
        $this->get('/semesters/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('semesters'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // How shall we test the index?

        // 1. Ensure that there is a suitably named table to display the results.
        $semesters_table = $html->find('table#semesters',0);
        $this->assertNotNull($semesters_table);

        // 2. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $semesters_table->find('thead',0);
        $thead_ths = $thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'id');
        $this->assertEquals($thead_ths[1]->id, 'year');
        $this->assertEquals($thead_ths[2]->id, 'seq');
        $this->assertEquals($thead_ths[3]->id, 'actions');
        $this->assertEquals(count($thead_ths),4); // no other columns

        // 3. Ensure that the tbody section has the same
        //    quantity of rows as the count of semester records in the fixture.
        $semestersFixture = new SemestersFixture();
        $tbody = $semesters_table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($semestersFixture));

        // 4. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.  In order to do this we'll also need
        //    to read from the Semesters table.
        $semesters = TableRegistry::get('Semesters');
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($semestersFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');
            $this->assertEquals($fixtureRecord['id'], $htmlColumns[0]->plaintext);
            $this->assertEquals($fixtureRecord['year'],  $htmlColumns[1]->plaintext);
            $this->assertEquals($fixtureRecord['seq'],  $htmlColumns[2]->plaintext);

            // Ignore the action links
        }
    }

    public function testViewGET() {

        $semestersFixture = new SemestersFixture();

        $this->fakeLogin();
        $this->get('/semesters/view/' . $semestersFixture->semester1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('semester'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // How shall we test the view?  It doesn't have any enclosing table or structure so just
        // ignore that part.  Instead, look for individual display fields.
        $field = $html->find('td#id',0);
        $this->assertEquals($semestersFixture->semester1Record['id'], $field->plaintext);

        $field = $html->find('td#year',0);
        $this->assertEquals($semestersFixture->semester1Record['year'], $field->plaintext);

        $field = $html->find('td#seq',0);
        $this->assertEquals($semestersFixture->semester1Record['seq'], $field->plaintext);
    }

}
