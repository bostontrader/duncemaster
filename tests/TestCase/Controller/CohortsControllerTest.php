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

    /* @var \App\Model\Table\CohortsTable */
    private $cohorts;

    /* @var \App\Test\Fixture\CohortsFixture */
    private $cohortsFixture;

    /* @var \App\Model\Table\MajorsTable */
    private $majors;

    /* @var \App\Test\Fixture\MajorsFixture */
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

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,'/cohorts/add');

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#CohortAddForm',0);
        $this->assertNotNull($form);

        // 3. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 3.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 3.2 Look for the hidden POST input
        if($this->lookForHiddenInput($form)) $unknownInputCnt--;

        // 3.3 Ensure that there's an input field for start_year, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#CohortStartYear')) $unknownInputCnt--;

        // 3.4 Ensure that there's an input field for seq, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#CohortSeq')) $unknownInputCnt--;

        // 3.5 Ensure that there's a select field for major_id, that it has no selection,
        // and that it has the correct quantity of available choices.
        if($this->selectCheckerA($form, 'CohortMajorId', 'majors')) $unknownSelectCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#CohortsAdd');
    }

    public function testAddPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->cohortsFixture->newCohortRecord;
        $fromDbRecord=$this->genericAddPostProlog(
            FixtureConstants::userAndyAdminId,
            '/cohorts/add', $fixtureRecord,
            '/cohorts', $this->cohorts
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['start_year'],$fixtureRecord['start_year']);
        $this->assertEquals($fromDbRecord['seq'],$fixtureRecord['seq']);
        $this->assertEquals($fromDbRecord['major_id'],$fixtureRecord['major_id']);
    }

    public function testDeletePOST() {

        $cohort_id = $this->cohortsFixture->records[0]['id'];
        $this->deletePOST(
            FixtureConstants::userAndyAdminId, '/cohorts/delete/',
            $cohort_id, '/cohorts', $this->cohorts
        );
    }

    public function testEditGET() {

        // 1. Obtain a record to edit, login, GET the url, parse the response and send it back.
        $record2Edit=$this->cohortsFixture->records[0];
        $url='/cohorts/edit/' . $record2Edit['id'];
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,$url);

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#CohortEditForm',0);
        $this->assertNotNull($form);

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
        if($this->inputCheckerA($form,'input#CohortStartYear',
            $record2Edit['start_year'])) $unknownInputCnt--;

        // 4.4 Ensure that there's an input field for seq, of type text, and that it is correctly set
        if($this->inputCheckerA($form,'input#CohortSeq',
            $record2Edit['seq'])) $unknownInputCnt--;

        // 4.5 Ensure that there's a select field for major_id and that it is correctly set
        // $major_id / $major['title'], from fixture
        $major_id=$record2Edit['major_id'];
        $major = $this->majorsFixture->get($major_id);
        if($this->inputCheckerB($form,'select#CohortMajorId option[selected]',$major_id,$major['title']))
            $unknownSelectCnt--;


        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#CohortsEdit');
    }

    public function testEditPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->cohortsFixture->newCohortRecord;
        $fromDbRecord=$this->genericEditPutProlog(
            FixtureConstants::userAndyAdminId,
            '/cohorts/edit', $fixtureRecord,
            '/cohorts', $this->cohorts
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['start_year'],$fixtureRecord['start_year']);
        $this->assertEquals($fromDbRecord['seq'],$fixtureRecord['seq']);
        $this->assertEquals($fromDbRecord['major_id'],$fixtureRecord['major_id']);
    }

    public function testIndexGET() {

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,'/cohorts/index');

        // 2. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#CohortsIndex',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 3. Look for the create new cohort link
        $this->assertEquals(1, count($html->find('a#CohortAdd')));
        $unknownATag--;

        // 4. Ensure that there is a suitably named table to display the results.
        $this->table = $html->find('table#CohortsTable',0);
        $this->assertNotNull($this->table);

        // 5. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $this->thead = $this->table->find('thead',0);
        $thead_ths = $this->thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'start_year');
        $this->assertEquals($thead_ths[1]->id, 'major');
        $this->assertEquals($thead_ths[2]->id, 'seq');
        $this->assertEquals($thead_ths[3]->id, 'nickname');
        $this->assertEquals($thead_ths[4]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,5); // no other columns

        // 6. Ensure that the tbody section has the same
        //    quantity of rows as the count of cohort records in the fixture.
        $this->tbody = $this->table->find('tbody',0);
        $tbody_rows = $this->tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->cohortsFixture->records));

        // 7. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter. 
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->cohortsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $this->htmlRow = $values[1];
            $htmlColumns = $this->htmlRow->find('td');

            // 7.0 start_year
            $this->assertEquals($fixtureRecord['start_year'],  $htmlColumns[0]->plaintext);

            // 7.1 major_id requires finding the related value in the MajorsFixture
            $major_id = $fixtureRecord['major_id'];
            $major = $this->majorsFixture->get($major_id);
            $this->assertEquals($major['sdesc'], $htmlColumns[1]->plaintext);

            // 7.2 seq
            $this->assertEquals($fixtureRecord['seq'],  $htmlColumns[2]->plaintext);

            // 7.3 nickname is computed by the Cohort Entity.
            $cohort = $this->cohorts->get($fixtureRecord['id'], ['contain' => ['Majors']]);
            $this->assertEquals($cohort->nickname, $htmlColumns[3]->plaintext);

            // 8.4 Now examine the action links
            $this->td = $htmlColumns[4];
            $actionLinks = $this->td->find('a');
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

        // 1. Obtain a record to view, login, GET the url, parse the response and send it back.
        $record2View=$this->cohortsFixture->records[0];
        $url='/cohorts/view/' . $record2View['id'];
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,$url);

        // 2.  Look for the table that contains the view fields.
        $this->table = $html->find('table#CohortViewTable',0);
        $this->assertNotNull($this->table);

        // 3. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($this->table->find('tr'));

        // 3.1 start_year
        $field = $html->find('tr#start_year td',0);
        $this->assertEquals($record2View['start_year'], $field->plaintext);
        $unknownRowCnt--;

        // 3.2 major_id requires finding the related value in the MajorsFixture
        $field = $html->find('tr#major_title td',0);
        $major_id = $record2View['major_id'];
        $major = $this->majorsFixture->get($major_id);
        $this->assertEquals($major['title'], $field->plaintext);
        $unknownRowCnt--;

        // 3.3 seq
        $field = $html->find('tr#seq td',0);
        $this->assertEquals($record2View['seq'], $field->plaintext);
        $unknownRowCnt--;

        // 3.4 nickname is computed by the Cohort Entity.
        $field = $html->find('tr#nickname td',0);
        $cohort = $this->cohorts->get($record2View['id'], ['contain' => ['Majors']]);
        $this->assertEquals($cohort->nickname, $field->plaintext);
        $unknownRowCnt--;

        // 3.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 4. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#CohortsView',0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0,count($links));
    }
}
