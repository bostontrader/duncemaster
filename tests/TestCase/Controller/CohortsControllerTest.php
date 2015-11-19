<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\CohortsFixture;
use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\MajorsFixture;
use Cake\ORM\TableRegistry;

class CohortsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.cohorts',
        'app.majors',
        'app.roles',
        'app.roles_users',
        'app.users'
    ];

    private $cohorts;
    private $cohortsFixture;
    private $majors;
    private $majorsFixture;

    public function setUp() {
        parent::setUp();
        $this->cohorts = TableRegistry::get('Cohorts');
        $this->majors = TableRegistry::get('Majors');
        $this->cohortsFixture = new CohortsFixture();
        $this->majorsFixture = new MajorsFixture();
    }

    // Test that unauthenticated users, when submitting a request to
    // an action, will get redirected to the login url.
    public function testUnauthenticatedActionsAndUsers() {
        $this->tstUnauthenticatedActionsAndUsers('cohorts');
    }

    // Test that users who do not have correct roles, when submitting a request to
    // an action, will get redirected to the home page.
    public function testUnauthorizedActionsAndUsers() {
        $this->tstUnauthorizedActionsAndUsers('cohorts');
    }

    public function testAddGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/cohorts/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#CohortAddForm',0);
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

        // 4.3 Ensure that there's an input field for start_year, of type text, and that it is empty
        $input = $form->find('input#CohortStartYear',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // 4.4 Ensure that there's an input field for seq, of type text, and that it is empty
        $input = $form->find('input#CohortSeq',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // 4.5 Ensure that there's a select field for major_id, that it has no selection,
        // and that it has the correct quantity of available choices.
        if($this->lookForSelect($form,'CohortMajorId','majors')) $unknownSelectCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#CohortsAdd',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testAddPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->post('/cohorts/add', $this->cohortsFixture->newCohortRecord);
        $this->assertResponseSuccess(); // 2xx,3xx
        $this->assertRedirect( '/cohorts' );

        // Now verify what we think just got written
        $new_id = count($this->cohortsFixture->records) + 1;
        $query = $this->cohorts->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_cohort = $this->cohorts->get($new_id);
        $this->assertEquals($new_cohort['start_year'],$this->cohortsFixture->newCohortRecord['start_year']);
        $this->assertEquals($new_cohort['seq'],$this->cohortsFixture->newCohortRecord['seq']);
        $this->assertEquals($new_cohort['major_id'],$this->cohortsFixture->newCohortRecord['major_id']);
    }

    public function testDeletePOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $cohort_id = $this->cohortsFixture->cohort1Record['id'];
        $this->post('/cohorts/delete/' . $cohort_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/cohorts' );

        // Now verify that the record no longer exists
        $query = $this->cohorts->find()->where(['id' => $cohort_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/cohorts/edit/' . $this->cohortsFixture->cohort1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#CohortEditForm',0);
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
        if($this->lookForHiddenInput($form,'_method','PUT')) $unknownInputCnt--;

        // 4.3 Ensure that there's an input field for start_year, of type text, and that it is correctly set
        $input = $form->find('input#CohortStartYear',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $this->cohortsFixture->cohort1Record['start_year']);
        $unknownInputCnt--;

        // 4.4 Ensure that there's an input field for seq, of type text, and that it is correctly set
        $input = $form->find('input#CohortSeq',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value,  $this->cohortsFixture->cohort1Record['seq']);
        $unknownInputCnt--;

        // 4.5 Ensure that there's a select field for major_id and that it is correctly set
        $option = $form->find('select#CohortMajorId option[selected]',0);
        $major_id = $this->cohortsFixture->cohort1Record['major_id'];
        $this->assertEquals($option->value, $major_id);

        // Even though major_id is correct, we don't display major_id.  Instead we display the title
        // from the related Majors table. Verify that title is displayed correctly.
        $major = $this->majorsFixture->get($major_id);
        $this->assertEquals($major['title'], $option->plaintext);
        $unknownSelectCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#CohortsEdit',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testEditPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $cohort_id = $this->cohortsFixture->cohort1Record['id'];
        $this->put('/cohorts/edit/' . $cohort_id, $this->cohortsFixture->newCohortRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/cohorts');

        // Now verify what we think just got written
        $query = $this->cohorts->find()->where(['id' => $cohort_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $cohort = $this->cohorts->get($cohort_id);
        $this->assertEquals($cohort['start_year'],$this->cohortsFixture->newCohortRecord['start_year']);
        $this->assertEquals($cohort['seq'],$this->cohortsFixture->newCohortRecord['seq']);
        $this->assertEquals($cohort['major_id'],$this->cohortsFixture->newCohortRecord['major_id']);
    }

    public function testIndexGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/cohorts/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $content = $html->find('div#CohortsIndex',0);
        $this->assertNotNull($content);
        $unknownATag = count($content->find('a'));

        // 4. Look for the create new cohort link
        $this->assertEquals(1, count($html->find('a#CohortAdd')));
        $unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        $cohorts_table = $html->find('table#CohortsTable',0);
        $this->assertNotNull($cohorts_table);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $cohorts_table->find('thead',0);
        $thead_ths = $thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'start_year');
        $this->assertEquals($thead_ths[1]->id, 'major');
        $this->assertEquals($thead_ths[2]->id, 'seq');
        $this->assertEquals($thead_ths[3]->id, 'nickname');
        $this->assertEquals($thead_ths[4]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,5); // no other columns

        // 7. Ensure that the tbody section has the same
        //    quantity of rows as the count of cohort records in the fixture.
        $tbody = $cohorts_table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->cohortsFixture->records));

        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter. 
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->cohortsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');

            // 8.0 start_year
            $this->assertEquals($fixtureRecord['start_year'],  $htmlColumns[0]->plaintext);

            // 8.1 major_id requires finding the related value in the MajorsFixture
            $major_id = $fixtureRecord['major_id'];
            $major = $this->majorsFixture->get($major_id);
            $this->assertEquals($major['sdesc'], $htmlColumns[1]->plaintext);

            // 8.2 seq
            $this->assertEquals($fixtureRecord['seq'],  $htmlColumns[2]->plaintext);

            // 8.3 nickname is computed by the Cohort Entity.
            $cohort = $this->cohorts->get($fixtureRecord['id'], ['contain' => ['Majors']]);
            $this->assertEquals($cohort->nickname, $htmlColumns[3]->plaintext);

            // 8.4 Now examine the action links
            $actionLinks = $htmlColumns[4]->find('a');
            $this->assertEquals('CohortView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('CohortEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('CohortDelete', $actionLinks[2]->name);
            $unknownATag--;

            // 8.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 9. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testViewGET() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/cohorts/view/' . $this->cohortsFixture->cohort1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 1.  Look for the table that contains the view fields.
        $table = $html->find('table#CohortViewTable',0);
        $this->assertNotNull($table);

        // 2. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($table->find('tr'));

        // 2.1 start_year
        $field = $html->find('tr#start_year td',0);
        $this->assertEquals($this->cohortsFixture->cohort1Record['start_year'], $field->plaintext);
        $unknownRowCnt--;

        // 2.2 major_id requires finding the related value in the MajorsFixture
        $field = $html->find('tr#major_title td',0);
        $major_id = $this->cohortsFixture->cohort1Record['major_id'];
        $major = $this->majorsFixture->get($major_id);
        $this->assertEquals($major['title'], $field->plaintext);
        $unknownRowCnt--;

        // 2.3 seq
        $field = $html->find('tr#seq td',0);
        $this->assertEquals($this->cohortsFixture->cohort1Record['seq'], $field->plaintext);
        $unknownRowCnt--;

        // 2.4 nickname is computed by the Cohort Entity.
        $field = $html->find('tr#nickname td',0);
        $cohort = $this->cohorts->get($this->cohortsFixture->cohort1Record['id'], ['contain' => ['Majors']]);
        $this->assertEquals($cohort->nickname, $field->plaintext);
        $unknownRowCnt--;

        // Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 3. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#CohortsView',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

}
