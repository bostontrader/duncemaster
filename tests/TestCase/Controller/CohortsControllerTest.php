<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\CohortsFixture;
use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\MajorsFixture;
use Cake\ORM\TableRegistry;

class CohortsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.cohorts',
        'app.majors'
    ];

    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/cohorts/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure these view vars are set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('cohort'));
        $this->assertNotNull($this->viewVariable('majors'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#CohortAddForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // Ensure that there's an input field for start_year, of type text, and that it is empty
        $input = $form->find('input#CohortStartYear',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);

        // Ensure that there's an input field for seq, of type text, and that it is empty
        $input = $form->find('input#CohortSeq',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);

        // Ensure that there's a select field for major_id and that is has no selection
        $option = $form->find('select#CohortMajorId option[selected]',0);
        $this->assertNull($option);
    }

    public function testAddPOST() {

        $cohortsFixture = new CohortsFixture();

        $this->fakeLogin();
        $this->post('/cohorts/add', $cohortsFixture->newCohortRecord);
        $this->assertResponseSuccess(); // 2xx,3xx
        $this->assertRedirect( '/cohorts' );

        // Now verify what we think just got written
        $cohorts = TableRegistry::get('Cohorts');
        $new_id = FixtureConstants::cohort1_id + 1;
        $query = $cohorts->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_cohort = $cohorts->get($new_id);
        $this->assertEquals($new_cohort['start_year'],$cohortsFixture->newCohortRecord['start_year']);
        $this->assertEquals($new_cohort['seq'],$cohortsFixture->newCohortRecord['seq']);
        $this->assertEquals($new_cohort['major_id'],$cohortsFixture->newCohortRecord['major_id']);
    }

    public function testDeletePOST() {

        $cohortsFixture = new CohortsFixture();

        $this->fakeLogin();
        $cohort_id = $cohortsFixture->cohort1Record['id'];
        $this->post('/cohorts/delete/' . $cohort_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/cohorts' );

        // Now verify that the record no longer exists
        $cohorts = TableRegistry::get('Cohorts');
        $query = $cohorts->find()->where(['id' => $cohort_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $cohortsFixture = new CohortsFixture();
        $majorsFixture = new MajorsFixture();

        $this->fakeLogin();
        $cohort_id = $cohortsFixture->cohort1Record['id'];
        $this->get('/cohorts/edit/' . $cohort_id);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure these view vars are set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('cohort'));
        $this->assertNotNull($this->viewVariable('majors'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#CohortEditForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // Ensure that there's an input field for start_year, of type text, and that it is correctly set
        $input = $form->find('input#CohortStartYear',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $cohortsFixture->cohort1Record['start_year']);

        // Ensure that there's an input field for seq, of type text, and that it is correctly set
        $input = $form->find('input#CohortSeq',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value,  $cohortsFixture->cohort1Record['seq']);

        // Ensure that there's a select field for major_id and that it is correctly set
        $option = $form->find('select#CohortMajorId option[selected]',0);
        $major_id = $cohortsFixture->cohort1Record['major_id'];
        $this->assertEquals($option->value, $major_id);

        // Even though major_id is correct, we don't display major_id.  Instead we display the title
        // from the related Majors table. Verify that title is displayed correctly.
        $major = $majorsFixture->get($major_id);
        $this->assertEquals($major['title'], $option->plaintext);
    }

    public function testEditPOST() {

        $cohortsFixture = new CohortsFixture();

        $this->fakeLogin();
        $cohort_id = $cohortsFixture->cohort1Record['id'];
        $this->put('/cohorts/edit/' . $cohort_id, $cohortsFixture->newCohortRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/cohorts');

        // Now verify what we think just got written
        $cohorts = TableRegistry::get('Cohorts');
        $query = $cohorts->find()->where(['id' => $cohort_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $cohort = $cohorts->get($cohort_id);
        $this->assertEquals($cohort['start_year'],$cohortsFixture->newCohortRecord['start_year']);
        $this->assertEquals($cohort['seq'],$cohortsFixture->newCohortRecord['seq']);
        $this->assertEquals($cohort['major_id'],$cohortsFixture->newCohortRecord['major_id']);
    }

    public function testIndexGET() {

        $this->fakeLogin();
        $this->get('/cohorts/index');
        $this->assertResponseOk(); //2xx
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('cohorts'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // How shall we test the index?

        // 1. Ensure that there is a suitably named table to display the results.
        $cohorts_table = $html->find('table#cohorts',0);
        $this->assertNotNull($cohorts_table);

        // 2. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $cohorts_table->find('thead',0);
        $thead_ths = $thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'id');
        $this->assertEquals($thead_ths[1]->id, 'start_year');
        $this->assertEquals($thead_ths[2]->id, 'major');
        $this->assertEquals($thead_ths[3]->id, 'seq');
        $this->assertEquals($thead_ths[4]->id, 'nickname');
        $this->assertEquals($thead_ths[5]->id, 'actions');
        $this->assertEquals(count($thead_ths),6); // no other columns

        // 3. Ensure that the tbody section has the same
        //    quantity of rows as the count of cohort records in the fixture.
        $cohortsFixture = new CohortsFixture();
        $majorsFixture = new MajorsFixture();
        $tbody = $cohorts_table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($cohortsFixture));

        // 4. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.  In order to do this we'll also need
        //    to read from the Cohorts table.
        $cohorts = TableRegistry::get('Cohorts');
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($cohortsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');
            $this->assertEquals($fixtureRecord['id'], $htmlColumns[0]->plaintext);
            $this->assertEquals($fixtureRecord['start_year'],  $htmlColumns[1]->plaintext);

            // major_id requires finding the related value in the MajorsFixture
            $major_id = $fixtureRecord['major_id'];
            $major = $majorsFixture->get($major_id);
            $this->assertEquals($major['sdesc'], $htmlColumns[2]->plaintext);

            $this->assertEquals($fixtureRecord['seq'],  $htmlColumns[3]->plaintext);

            // nickname is computed by the Cohort Entity.
            $cohort = $cohorts->get($fixtureRecord['id'], ['contain' => ['Majors']]);
            $this->assertEquals($cohort->nickname, $htmlColumns[4]->plaintext);
            // Ignore the action links
        }

    }

    public function testViewGET() {

        $cohortsFixture = new CohortsFixture();

        $this->fakeLogin();
        $this->get('/cohorts/view/' . $cohortsFixture->cohort1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('cohort'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // How shall we test the view?  It doesn't have any enclosing table or structure so just
        // ignore that part.  Instead, look for individual display fields.
        $field = $html->find('td#id',0);
        $this->assertEquals($cohortsFixture->cohort1Record['id'], $field->plaintext);

        $field = $html->find('td#start_year',0);
        $this->assertEquals($cohortsFixture->cohort1Record['start_year'], $field->plaintext);

        // major_id requires finding the related value in the MajorsFixture
        $field = $html->find('td#major_title',0);
        $majorsFixture = new MajorsFixture();
        $major_id = $cohortsFixture->cohort1Record['major_id'];
        $major = $majorsFixture->get($major_id);
        $this->assertEquals($major['title'], $field->plaintext);

        $field = $html->find('td#seq',0);
        $this->assertEquals($cohortsFixture->cohort1Record['seq'], $field->plaintext);

        // nickname is computed by the Cohort Entity.
        $field = $html->find('td#nickname',0);
        $cohorts = TableRegistry::get('Cohorts');
        $cohort = $cohorts->get($cohortsFixture->cohort1Record['id'], ['contain' => ['Majors']]);
        $this->assertEquals($cohort->nickname, $field->plaintext);
    }

}
