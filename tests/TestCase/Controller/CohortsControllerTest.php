<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\CohortsFixture;
use Cake\ORM\TableRegistry;

class CohortsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.cohorts'
    ];

    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/cohorts/add');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('cohort'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=CohortAddForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for start_year, that is empty
        $input = $form->find('input[id=CohortStartYear]')[0];
        $this->assertEquals($input->value, false);

        // Ensure that there's a field for seq, that is empty
        $input = $form->find('input[id=CohortSeq]')[0];
        $this->assertEquals($input->value, false);
    }

    public function testAddPOST() {

        $cohortsFixture = new CohortsFixture();

        $this->fakeLogin();
        $this->post('/cohorts/add', $cohortsFixture->newCohortRecord);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/cohorts' );

        // Now verify what we think just got written
        $cohorts = TableRegistry::get('Cohorts');
        $query = $cohorts->find()->where(['id' => $cohortsFixture->newCohortRecord['id']]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $cohort = $cohorts->get($cohortsFixture->newCohortRecord['id']);
        $this->assertEquals($cohort['start_year'],$cohortsFixture->newCohortRecord['start_year']);
        $this->assertEquals($cohort['seq'],$cohortsFixture->newCohortRecord['seq']);
    }

    public function testDeletePOST() {

        $cohortsFixture = new CohortsFixture();

        $this->fakeLogin();
        $this->post('/cohorts/delete/' . $cohortsFixture->cohort1Record['id']);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/cohorts' );

        // Now verify that the record no longer exists
        $cohorts = TableRegistry::get('Cohorts');
        $query = $cohorts->find()->where(['id' => $cohortsFixture->cohort1Record['id']]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $cohortsFixture = new CohortsFixture();

        $this->fakeLogin();
        $this->get('/cohorts/edit/' . $cohortsFixture->cohort1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('cohort'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=CohortEditForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for start_year, that is correctly set
        $input = $form->find('input[id=CohortStartYear]')[0];
        $this->assertEquals($input->value, $cohortsFixture->cohort1Record['start_year']);

        // Ensure that there's a field for sdesc, that is correctly set
        $input = $form->find('input[id=CohortSeq]')[0];
        $this->assertEquals($input->value,  $cohortsFixture->cohort1Record['seq']);

    }

    public function testEditPOST() {

        $cohortsFixture = new CohortsFixture();

        $this->fakeLogin();
        $this->post('/cohorts/edit/' . $cohortsFixture->cohort1Record['id'], $cohortsFixture->newCohortRecord);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Now verify what we think just got written
        $cohorts = TableRegistry::get('Cohorts');
        $query = $cohorts->find()->where(['id' => $cohortsFixture->cohort1Record['id']]);
        $c = $query->count();
        $this->assertEquals(1, $c);

        // Now retrieve that 1 record and compare to what we expect
        $cohort = $cohorts->get($cohortsFixture->cohort1Record['id']);
        $this->assertEquals($cohort['start_year'],$cohortsFixture->newCohortRecord['start_year']);
        $this->assertEquals($cohort['seq'],$cohortsFixture->newCohortRecord['seq']);

    }

    public function testIndexGET() {

        $this->fakeLogin();
        $result = $this->get('/cohorts/index');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($result);

        // 1. Ensure that the single row of the thead section
        //    has a column for id and title, in that order
        //$rows = $html->find('table[id=cohorts]',0)->find('thead',0)->find('tr');
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
        //    quantity of rows as the count of cohort records in the fixture.
        //    For each of these rows, ensure that the id and title match
        //$cohortFixture = new CohortFixture();
        //$rowsInHTMLTable = $html->find('table[id=cohorts]',0)->find('tbody',0)->find('tr');
        //$this->assertEqual(count($cohortFixture->records), count($rowsInHTMLTable));
        //$iterator = new MultipleIterator;
        //$iterator->attachIterator(new ArrayIterator($cohortFixture->records));
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

        $cohortsFixture = new CohortsFixture();

        $this->fakeLogin();
        $this->get('/cohorts/view/' . $cohortsFixture->cohort1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('cohort'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        //$form = $html->find('form[id=CohortEditForm]')[0];
        //$this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is correctly set
        //$input = $form->find('input[id=CohortTitle]')[0];
        //$this->assertEquals($input->value, $cohortsFixture->cohort1Record['title']);

        // Ensure that there's a field for sdesc, that is empty
        //$input = $form->find('input[id=CohortSDesc]')[0];
        //$this->assertEquals($input->value, false);
    }

}
