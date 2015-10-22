<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\SubjectsFixture;
use Cake\ORM\TableRegistry;

class SubjectsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.subjects'
    ];

    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/subjects/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('subject'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#SubjectAddForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // Ensure that there's an input field for title, of type text, and that it is empty
        $input = $form->find('input#SubjectTitle',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
    }

    public function testAddPOST() {

        $subjectsFixture = new SubjectsFixture();

        $this->fakeLogin();
        $this->post('/subjects/add', $subjectsFixture->newSubjectRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/subjects' );

        // Now verify what we think just got written
        $subjects = TableRegistry::get('Subjects');
        $new_id = FixtureConstants::subject1_id + 1;
        $query = $subjects->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_subject = $subjects->get($new_id);
        $this->assertEquals($new_subject['title'],$subjectsFixture->newSubjectRecord['title']);
    }

    public function testDeletePOST() {

        $subjectsFixture = new SubjectsFixture();

        $this->fakeLogin();
        $subject_id = $subjectsFixture->subject1Record['id'];
        $this->post('/subjects/delete/' . $subject_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/subjects' );

        // Now verify that the record no longer exists
        $subjects = TableRegistry::get('Subjects');
        $query = $subjects->find()->where(['id' => $subject_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $subjectsFixture = new SubjectsFixture();

        $this->fakeLogin();
        $subject_id = $subjectsFixture->subject1Record['id'];
        $this->get('/subjects/edit/' . $subject_id);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('subject'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#SubjectEditForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // Ensure that there's an input field for title, of type text, that is correctly set
        $input = $form->find('input#SubjectTitle',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $subjectsFixture->subject1Record['title']);
    }

    public function testEditPOST() {

        $subjectsFixture = new SubjectsFixture();

        $this->fakeLogin();
        $subject_id = $subjectsFixture->subject1Record['id'];
        $this->put('/subjects/edit/' . $subject_id, $subjectsFixture->newSubjectRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/subjects');

        // Now verify what we think just got written
        $subjects = TableRegistry::get('Subjects');
        $query = $subjects->find()->where(['id' => $subjectsFixture->subject1Record['id']]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $subject = $subjects->get($subject_id);
        $this->assertEquals($subject['title'],$subjectsFixture->newSubjectRecord['title']);

    }

    public function testIndexGET() {

        $this->fakeLogin();
        $result = $this->get('/subjects/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('subjects'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // How shall we test the index?

        // 1. Ensure that there is a suitably named table to display the results.
        $subjects_table = $html->find('table#subjects',0);
        $this->assertNotNull($subjects_table);

        // 2. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $subjects_table->find('thead',0);
        $thead_ths = $thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'id');
        $this->assertEquals($thead_ths[1]->id, 'title');
        $this->assertEquals($thead_ths[2]->id, 'actions');
        $this->assertEquals(count($thead_ths),3); // no other columns

        // 3. Ensure that the tbody section has the same
        //    quantity of rows as the count of subject records in the fixture.
        $subjectsFixture = new SubjectsFixture();
        $tbody = $subjects_table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($subjectsFixture));

        // 4. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.  In order to do this we'll also need
        //    to read from the Majors table.
        $subjects = TableRegistry::get('Subjects');
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($subjectsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');
            $this->assertEquals($fixtureRecord['id'], $htmlColumns[0]->plaintext);
            $this->assertEquals($fixtureRecord['title'],  $htmlColumns[1]->plaintext);

            // Ignore the action links
        }
    }

    public function testViewGET() {

        $subjectsFixture = new SubjectsFixture();

        $this->fakeLogin();
        $this->get('/subjects/view/' . $subjectsFixture->subject1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('subject'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // How shall we test the view?  It doesn't have any enclosing table or structure so just
        // ignore that part.  Instead, look for individual display fields.
        $field = $html->find('td#id',0);
        $this->assertEquals($subjectsFixture->subject1Record['id'], $field->plaintext);

        $field = $html->find('td#title',0);
        $this->assertEquals($subjectsFixture->subject1Record['title'], $field->plaintext);

    }

}
