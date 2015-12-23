<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\TplanElementsFixture;
use App\Test\Fixture\TplansFixture;
use Cake\ORM\TableRegistry;

class TplanElementsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.roles',
        'app.roles_users',
        'app.tplans',
        'app.tplan_elements',
        'app.users'
    ];

    /* @var \App\Model\Table\TplanElementsTable */
    private $tplan_elements;

    /* @var \App\Test\Fixture\TplanElementsFixture */
    private $tplan_elementsFixture;

    /* @var \App\Test\Fixture\TplansFixture */
    private $tplansFixture;

    public function setUp() {
        parent::setUp();
        $this->tplan_elements = TableRegistry::get('TplanElements');
        $this->tplan_elementsFixture = new TplanElementsFixture();
        $this->tplansFixture = new TplansFixture();
    }

    // Test that unauthenticated users, when submitting a request to
    // an action, will get redirected to the login url.
    public function testUnauthenticatedActionsAndUsers() {
        $this->tstUnauthenticatedActionsAndUsers('tplan_elements');
    }

    // Test that users who do not have correct roles, when submitting a request to
    // an action, will get redirected to the home page.
    public function testUnauthorizedActionsAndUsers() {
        $this->tstUnauthorizedActionsAndUsers('tplan_elements');
    }

    public function testAddGET() {

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,'/tplan_elements/add');

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#TplanElementAddForm',0);
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

        // 3.3 Ensure that there's a select field for tplan_id, that it has no selection,
        // and that it has the correct quantity of available choices.
        if($this->selectCheckerA($form, 'TplanElementTplanId', 'tplans')) $unknownSelectCnt--;

        // 3.4 Ensure that there's an input field for col1, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#TplanElementCol1')) $unknownInputCnt--;

        // 3.5 Ensure that there's an input field for col2, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#TplanElementCol2')) $unknownInputCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#TplanElementsAdd');
    }

    public function testAddPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->tplan_elementsFixture->newTplanElementRecord;
        $fromDbRecord=$this->genericAddPostProlog(
            FixtureConstants::userAndyAdminId,
            '/tplan-elements/add', $fixtureRecord,
            '/tplan-elements', $this->tplan_elements
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['tplan_id'],$fixtureRecord['tplan_id']);
        $this->assertEquals($fromDbRecord['col1'],$fixtureRecord['col1']);
        $this->assertEquals($fromDbRecord['col2'],$fixtureRecord['col2']);
    }

    public function testDeletePOST() {

        $tplan_element_id = $this->tplan_elementsFixture->records[0]['id'];
        $this->deletePOST(
            FixtureConstants::userAndyAdminId, '/tplan-elements/delete/',
            $tplan_element_id, '/tplan-elements', $this->tplan_elements
        );
    }

    public function testEditGET() {

        // 1. Obtain a record to edit, login, GET the url, parse the response and send it back.
        $record2Edit=$this->tplan_elementsFixture->records[0];
        $url='/tplan_elements/edit/' . $record2Edit['id'];
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,$url);

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#TplanElementEditForm',0);
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

        // 3.3 Ensure that there's a select field for tplan_id and that it is correctly set
        // $tplan_id / $tplan['title'], from fixture
        $tplan_id=$record2Edit['tplan_id'];
        $tplan = $this->tplansFixture->get($tplan_id);
        if($this->inputCheckerB($form,'select#TplanElementTplanId option[selected]',$tplan_id,$tplan['title']))
            $unknownSelectCnt--;
        
        // 3.4 col1
        if($this->inputCheckerA($form,'input#TplanElementCol1',
            $record2Edit['col1'])) $unknownInputCnt--;

        // 3.5 col2
        if($this->inputCheckerA($form,'input#TplanElementCol2',
            $record2Edit['col2'])) $unknownInputCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#TplanElementsEdit');
    }

    public function testEditPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->tplan_elementsFixture->newTplanElementRecord;
        $fromDbRecord=$this->genericEditPutProlog(
            FixtureConstants::userAndyAdminId,
            '/tplan-elements/edit', $fixtureRecord,
            '/tplan-elements', $this->tplan_elements
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['tplan_id'],$fixtureRecord['tplan_id']);
        $this->assertEquals($fromDbRecord['col1'],$fixtureRecord['col1']);
        $this->assertEquals($fromDbRecord['col2'],$fixtureRecord['col2']);
    }

    public function testIndexGET() {

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,'/tplan_elements/index');

        // 2. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#TplanElementsIndex',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 3. Look for the create new tplan_element link
        $this->assertEquals(1, count($html->find('a#TplanElementAdd')));
        $unknownATag--;

        // 4. Examine the table of TplanElements.
        /* @var \simple_html_dom_node $html */
        $unknownATag-=$this->tstTplanElementsTable($html,$this->tplan_elementsFixture);

        // 5. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    /**
     * At least three views create a table of TplanElements.
     * (Tplans.edit,  Tplans.view, and TplanElements.index)
     * This table must be tested. Factor that testing into this method.
     * @param \simple_html_dom_node $html parsed dom that contains the TplanElementsTable
     * @param \App\Test\Fixture\TplanElementsFixture $tplan_elementsFixture
     * @return int $aTagsFoundCnt The number of aTagsFound.
     */
    public function tstTplanElementsTable($html, $tplan_elementsFixture) {

        // 1. Ensure that there is a suitably named table to display the results.
        $this->table = $html->find('table#TplanElementsTable',0);
        $this->assertNotNull($this->table);

        // 2. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $this->thead = $this->table->find('thead',0);
        $thead_ths = $this->thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'col1');
        $this->assertEquals($thead_ths[1]->id, 'col2');
        $this->assertEquals($thead_ths[2]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,3); // no other columns

        // 3. Ensure that the tbody section has the same
        //    quantity of rows as the count of tplan_elements records in the fixture.
        $this->tbody = $this->table->find('tbody',0);
        $tbody_rows = $this->tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($tplan_elementsFixture->records));

        // 4. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($tplan_elementsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        $aTagsFoundCnt=0;
        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $this->htmlRow = $values[1];
            $htmlColumns = $this->htmlRow->find('td');

            // 4.0 col1
            $this->assertEquals($fixtureRecord['col1'],  $htmlColumns[0]->plaintext);

            // 4.1 col2
            $this->assertEquals($fixtureRecord['col2'],  $htmlColumns[1]->plaintext);

            // 4.2 Now examine the action links
            $this->td = $htmlColumns[2];
            $actionLinks = $this->td->find('a');
            $this->assertEquals('TplanElementView', $actionLinks[0]->name);
            $aTagsFoundCnt++;
            $this->assertEquals('TplanElementEdit', $actionLinks[1]->name);
            $aTagsFoundCnt++;
            $this->assertEquals('TplanElementDelete', $actionLinks[2]->name);
            $aTagsFoundCnt++;

            // 4.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }
        return $aTagsFoundCnt;
    }

    public function testViewGET() {

        // 1. Obtain a record to view, login, GET the url, parse the response and send it back.
        $record2View=$this->tplan_elementsFixture->records[0];
        $url='/tplan-elements/view/' . $record2View['id'];
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,$url);

        // 2.  Look for the table that contains the view fields.
        $this->table = $html->find('table#TplanElementViewTable',0);
        $this->assertNotNull($this->table);

        // 3. Now inspect the fields in the table.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($this->table->find('tr'));

        // 3.1 tplan_id requires finding the related value in the TplansFixture
        $field = $html->find('tr#tplan_title td',0);
        $tplan_id = $record2View['tplan_id'];
        $tplan = $this->tplansFixture->get($tplan_id);
        $this->assertEquals($tplan['title'], $field->plaintext);
        $unknownRowCnt--;

        // 3.2 col1
        $field = $html->find('tr#col1 td',0);
        $this->assertEquals($record2View['col1'], $field->plaintext);
        $unknownRowCnt--;

        // 3.3 col2
        $field = $html->find('tr#col2 td',0);
        $this->assertEquals($record2View['col2'], $field->plaintext);
        $unknownRowCnt--;

        // 3.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 4. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#TplanElementsView',0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0,count($links));
    }
}
