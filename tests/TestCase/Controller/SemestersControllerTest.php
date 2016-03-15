<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\SemestersFixture;
use Cake\ORM\TableRegistry;

class SemestersControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.roles',
        'app.roles_users',
        'app.semesters',
        'app.users'
    ];

    /* @var \App\Model\Table\SemestersTable */
    private $Semesters;

    /* @var \App\Test\Fixture\SemestersFixture */
    private $semestersFixture;

    public function setUp() {
        parent::setUp();
        $this->Semesters = TableRegistry::get('Semesters');
        $this->semestersFixture = new SemestersFixture();
    }

    // Test that unauthenticated users, when submitting a request to
    // an action, will get redirected to the login url.
    //public function testUnauthenticatedActionsAndUsers() {
        //$this->tstUnauthenticatedActionsAndUsers('semesters');
    //}

    // Test that users who do not have correct roles, when submitting a request to
    // an action, will get redirected to the home page.
    //public function testUnauthorizedActionsAndUsers() {
        //$this->tstUnauthorizedActionsAndUsers('semesters');
    //}

    public function testAddGET() {

        // 1. Login, GET the url, and parse the response.
        $html=$this->loginRequestResponse(FixtureConstants::USER_ANDY_ADMIN_ID,'/semesters/add');

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#SemesterAddForm',0);
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

        // 3.3 Ensure that there's an input field for year, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#SemesterYear')) $unknownInputCnt--;

        // 3.4 Ensure that there's an input field for seq, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#SemesterSeq')) $unknownInputCnt--;

        // 3.5 Ensure that there's an input field for firstday, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#SemesterFirstday')) $unknownInputCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#SemestersAdd');
    }

    public function testAddPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->semestersFixture->newSemesterRecord;
        $fromDbRecord=$this->genericAddPostProlog(
            FixtureConstants::USER_ANDY_ADMIN_ID,
            '/semesters/add', $fixtureRecord,
            '/semesters', $this->Semesters
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['year'],$fixtureRecord['year']);
        $this->assertEquals($fromDbRecord['seq'],$fixtureRecord['seq']);
        $this->assertEquals($fromDbRecord['firstday'],$fixtureRecord['firstday']);
    }

    //public function testDeletePOST() {
        //$semester_id = $this->semestersFixture->records[0]['id'];
        //$this->deletePOST(
            //FixtureConstants::USER_ANDY_ADMIN_ID, '/semesters/delete/',
            //$semester_id, '/semesters', $this->semesters
        //);
    //}

    public function testEditGET() {

        // 1. Obtain a record to edit, login, GET the url, parse the response and send it back.
        $record2Edit=$this->semestersFixture->records[0];
        $url='/semesters/edit/' . $record2Edit['id'];
        $html=$this->loginRequestResponse(FixtureConstants::USER_ANDY_ADMIN_ID,$url);
        
        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#SemesterEditForm',0);
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
        if($this->lookForHiddenInput($form,'_method','PUT')) $unknownInputCnt--;

        // 3.3 Ensure that there's an input field for year, of type text, and that it is correctly set
        if($this->inputCheckerA($form,'input#SemesterYear',
            $record2Edit['year'])) $unknownInputCnt--;

        // 3.4 Ensure that there's an input field for seq, of type text, and that it is correctly set
        if($this->inputCheckerA($form,'input#SemesterSeq',
            $record2Edit['seq'])) $unknownInputCnt--;

        // 3.5 Ensure that there's an input field for seq, of type text, and that it is correctly set
        if($this->inputCheckerA($form,'input#SemesterFirstday',
            $record2Edit['firstday'])) $unknownInputCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#SemestersEdit');
    }

    public function testEditPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->semestersFixture->newSemesterRecord;
        $fromDbRecord=$this->genericEditPutProlog(
            FixtureConstants::USER_ANDY_ADMIN_ID,
            '/semesters/edit', $fixtureRecord,
            '/semesters', $this->Semesters
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['year'],$fixtureRecord['year']);
        $this->assertEquals($fromDbRecord['seq'],$fixtureRecord['seq']);
        $this->assertEquals($fromDbRecord['firstday'],$fixtureRecord['firstday']);
    }

    public function testIndexGET() {

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::USER_ANDY_ADMIN_ID,'/semesters/index');

        // 2. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#SemestersIndex',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 3. Look for the create new semester link
        $this->assertEquals(1, count($html->find('a#SemesterAdd')));
        $unknownATag--;

        // 4. Ensure that there is a suitably named table to display the results.
        $this->table = $html->find('table#SemestersTable',0);
        $this->assertNotNull($this->table);

        // 5. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $this->thead = $this->table->find('thead',0);
        $thead_ths = $this->thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'year');
        $this->assertEquals($thead_ths[1]->id, 'seq');
        $this->assertEquals($thead_ths[2]->id, 'firstday');
        $this->assertEquals($thead_ths[3]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,4); // no other columns

        // 6. Ensure that the tbody section has the same
        //    quantity of rows as the count of semester records in the fixture.
        $this->tbody = $this->table->find('tbody',0);
        $tbody_rows = $this->tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->semestersFixture->records));

        // 7. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->semestersFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $this->htmlRow = $values[1];
            $htmlColumns = $this->htmlRow->find('td');

            $this->assertEquals($fixtureRecord['year'],  $htmlColumns[0]->plaintext);
            $this->assertEquals($fixtureRecord['seq'],  $htmlColumns[1]->plaintext);
            $this->assertEquals($fixtureRecord['firstday'],  $htmlColumns[2]->plaintext);

            // 7.3 Now examine the action links
            $this->td = $htmlColumns[3];
            $actionLinks = $this->td->find('a');
            $this->assertEquals('SemesterView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('SemesterEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('SemesterDelete', $actionLinks[2]->name);
            $unknownATag--;

            // 7.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 8. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testViewGET() {

        // 1. Obtain a record to view, login, GET the url, parse the response and send it back.
        $record2View=$this->semestersFixture->records[0];
        $url='/semesters/view/' . $record2View['id'];
        $html=$this->loginRequestResponse(FixtureConstants::USER_ANDY_ADMIN_ID,$url);

        // 2.  Look for the table that contains the view fields.
        $this->table = $html->find('table#SemesterViewTable',0);
        $this->assertNotNull($this->table);

        // 2. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($this->table->find('tr'));

        // 2.1 year
        $field = $html->find('tr#year td',0);
        $this->assertEquals($record2View['year'], $field->plaintext);
        $unknownRowCnt--;

        // 2.2 seq
        $field = $html->find('tr#seq td',0);
        $this->assertEquals($record2View['seq'], $field->plaintext);
        $unknownRowCnt--;

        // 2.3 firstday
        $field = $html->find('tr#firstday td',0);
        $this->assertEquals($record2View['firstday'], $field->plaintext);
        $unknownRowCnt--;

        // 2.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 3. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#SemestersView',0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0,count($links));
    }

}
