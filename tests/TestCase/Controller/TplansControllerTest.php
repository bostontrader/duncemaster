<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\TplanElementsFixture;
use App\Test\Fixture\TplansFixture;
use Cake\ORM\TableRegistry;

class TplansControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.roles',
        'app.roles_users',
        'app.sections',
        'app.subjects',
        'app.tplans',
        'app.tplan_elements',
        'app.users'
    ];

    /* @var \App\Model\Table\TplansTable */
    private $tplans;

    /* @var \App\Test\Fixture\TplansFixture */
    private $tplansFixture;

    /* @var \App\Test\Fixture\TplanElementsFixture */
    private $tplanElementsFixture;

    public function setUp() {
        parent::setUp();
        $this->tplans = TableRegistry::get('Tplans');
        $this->tplansFixture = new TplansFixture();
        $this->tplanElementsFixture = new TplanElementsFixture();
    }

    // Test that unauthenticated users, when submitting a request to
    // an action, will get redirected to the login url.
    public function testUnauthenticatedActionsAndUsers() {
        $this->tstUnauthenticatedActionsAndUsers('tplans');
    }

    // Test that users who do not have correct roles, when submitting a request to
    // an action, will get redirected to the home page.
    public function testUnauthorizedActionsAndUsers() {
        $this->tstUnauthorizedActionsAndUsers('tplans');
    }

    public function testAddGET() {

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,'/tplans/add');

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#TplanAddForm',0);
        $this->assertNotNull($form);

        // 3. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // 3.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 3.2 Look for the hidden POST input
        if($this->lookForHiddenInput($form)) $unknownInputCnt--;

        // 3.3 Ensure that there's an input field for title, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#TplanTitle')) $unknownInputCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#TplansAdd');
    }

    public function testAddPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->tplansFixture->newTplanRecord;
        $fromDbRecord=$this->genericAddPostProlog(
            FixtureConstants::userAndyAdminId,
            '/tplans/add', $fixtureRecord,
            '/tplans', $this->tplans
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['title'],$fixtureRecord['title']);
    }

    public function testDeletePOST() {

        $tplan_id = $this->tplansFixture->records[0]['id'];
        $this->deletePOST(
            FixtureConstants::userAndyAdminId, '/tplans/delete/',
            $tplan_id, '/tplans', $this->tplans
        );
    }

    public function testEditGET() {

        // 1. Obtain a record to edit, login, GET the url, parse the response and send it back.
        $record2Edit=$this->tplansFixture->records[0];
        $url='/tplans/edit/' . $record2Edit['id'];
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,$url);

        // 2. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#TplansEdit',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 3. Look for the create new tplan_elements link
        $this->assertEquals(1, count($html->find('a#TplanElementAdd')));
        $unknownATag--;

        // 4. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#TplanEditForm',0);
        $this->assertNotNull($form);

        // 5. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 5.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 5.2 Look for the hidden POST input
        if($this->lookForHiddenInput($form,'_method','PUT')) $unknownInputCnt--;

        // 5.3 Ensure that there's an input field for title, of type text, and that it is correctly set
        if($this->inputCheckerA($form,'input#TplanTitle',
            $record2Edit['title'])) $unknownInputCnt--;

        // 5.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 6. Examine the table of TplanElements.
        $tect=new TplanElementsControllerTest();

        /* @var \simple_html_dom_node $html */
        $unknownATag-=$tect->tstTplanElementsTable($html,$this->tplanElementsFixture);

        // 7. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testEditPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->tplansFixture->newTplanRecord;
        $fromDbRecord=$this->genericEditPutProlog(
            FixtureConstants::userAndyAdminId,
            '/tplans/edit', $fixtureRecord,
            '/tplans', $this->tplans
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['title'],$fixtureRecord['title']);
    }

    public function testIndexGET() {

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,'/tplans/index');

        // 2. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#TplansIndex',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 3. Look for the create new tplan link
        $this->assertEquals(1, count($html->find('a#TplanAdd')));
        $unknownATag--;

        // 4. Ensure that there is a suitably named table to display the results.
        $this->table = $html->find('table#TplansTable',0);
        $this->assertNotNull($this->table);

        // 5. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $this->thead = $this->table->find('thead',0);
        $thead_ths = $this->thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'title');
        $this->assertEquals($thead_ths[1]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,2); // no other columns

        // 6. Ensure that the tbody section has the same
        //    quantity of rows as the count of tplan records in the fixture.
        $this->tbody = $this->table->find('tbody',0);
        $tbody_rows = $this->tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->tplansFixture->records));

        // 7. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->tplansFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $this->htmlRow = $values[1];
            $htmlColumns = $this->htmlRow->find('td');

            $this->assertEquals($fixtureRecord['title'],  $htmlColumns[0]->plaintext);

            // 7.2 Now examine the action links
            $this->td = $htmlColumns[1];
            $actionLinks = $this->td->find('a');
            $this->assertEquals('TplanView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('TplanEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('TplanDelete', $actionLinks[2]->name);
            $unknownATag--;

            // 7.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 8. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testViewGET() {

        // 1. Obtain a record to view, login, GET the url, parse the response and send it back.
        $record2View=$this->tplansFixture->records[0];
        $url='/tplans/view/' . $record2View['id'];
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,$url);

        // 2. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#TplansView',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 3.  Look for the table that contains the view fields.
        $this->table = $html->find('table#TplanViewTable',0);
        $this->assertNotNull($this->table);

        // 4. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($this->table->find('tr'));

        // 4.1 title
        $field = $html->find('tr#title td',0);
        $this->assertEquals($record2View['title'], $field->plaintext);
        $unknownRowCnt--;

        // 5. Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 6. Examine the table of repeating tplan_elements
        /* @var \simple_html_dom_node $html */
        $tect=new TplanElementsControllerTest();
        $unknownATag-=$tect->tstTplanElementsTable($html,$this->tplanElementsFixture);

        // 7. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }
}
