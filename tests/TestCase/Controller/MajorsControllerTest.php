<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\MajorsFixture;
use Cake\ORM\TableRegistry;

class MajorsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.majors',
        'app.roles',
        'app.roles_users',
        'app.users'
    ];

    /* @var \App\Model\Table\MajorsTable */
    private $majors;

    /* @var \App\Test\Fixture\MajorsFixture */
    private $majorsFixture;

    public function setUp() {
        parent::setUp();
        $this->majors = TableRegistry::get('Majors');
        $this->majorsFixture = new MajorsFixture();
    }

    // Test that unauthenticated users, when submitting a request to
    // an action, will get redirected to the login url.
    public function testUnauthenticatedActionsAndUsers() {
        $this->tstUnauthenticatedActionsAndUsers('majors');
    }

    // Test that users who do not have correct roles, when submitting a request to
    // an action, will get redirected to the home page.
    public function testUnauthorizedActionsAndUsers() {
        $this->tstUnauthorizedActionsAndUsers('majors');
    }

    public function testAddGET() {

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,'/majors/add');

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#MajorAddForm',0);
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
        if($this->inputCheckerA($form,'input#MajorTitle')) $unknownInputCnt--;

        // 3.4 Ensure that there's an input field for sdesc, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#MajorSDesc')) $unknownInputCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#MajorsAdd');
    }

    public function testAddPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->majorsFixture->newMajorRecord;
        $fromDbRecord=$this->genericAddPostProlog(
            FixtureConstants::userAndyAdminId,
            '/majors/add', $fixtureRecord,
            '/majors', $this->majors
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['title'],$fixtureRecord['title']);
        $this->assertEquals($fromDbRecord['sdesc'],$fixtureRecord['sdesc']);
    }

    public function testDeletePOST() {

        $major_id = $this->majorsFixture->records[0]['id'];
        $this->deletePOST(
            FixtureConstants::userAndyAdminId, '/majors/delete/',
            $major_id, '/majors', $this->majors
        );
    }

    public function testEditGET() {

        // 1. Obtain a record to edit, login, GET the url, parse the response and send it back.
        $record2Edit=$this->majorsFixture->records[0];
        $url='/majors/edit/' . $record2Edit['id'];
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,$url);

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#MajorEditForm',0);
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

        // 3.3 Ensure that there's an input field for title, of type text, and that it is correctly set
        if($this->inputCheckerA($form,'input#MajorTitle',
            $record2Edit['title'])) $unknownInputCnt--;
        
        // 3.4 Ensure that there's an input field for sdesc, of type text, and that that it is correctly set
        if($this->inputCheckerA($form,'input#MajorSDesc',
            $record2Edit['sdesc'])) $unknownInputCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#MajorsEdit');
    }

    public function testEditPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->majorsFixture->newMajorRecord;
        $fromDbRecord=$this->genericEditPutProlog(
            FixtureConstants::userAndyAdminId,
            '/majors/edit', $fixtureRecord,
            '/majors', $this->majors
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['title'],$fixtureRecord['title']);
        $this->assertEquals($fromDbRecord['sdesc'],$fixtureRecord['sdesc']);
    }

    public function testIndexGET() {

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,'/majors/index');

        // 2. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#MajorsIndex',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 3. Look for the create new major link
        $this->assertEquals(1, count($html->find('a#MajorAdd')));
        $unknownATag--;

        // 4. Ensure that there is a suitably named table to display the results.
        $this->table = $html->find('table#majors',0);
        $this->assertNotNull($this->table);

        // 5. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $this->thead = $this->table->find('thead',0);
        $thead_ths = $this->thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'title');
        $this->assertEquals($thead_ths[1]->id, 'sdesc');
        $this->assertEquals($thead_ths[2]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,3); // no other columns

        // 6. Ensure that the tbody section has the same
        //    quantity of rows as the count of major records in the fixture.
        $this->tbody = $this->table->find('tbody',0);
        $tbody_rows = $this->tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->majorsFixture->records));

        // 7. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->majorsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $this->htmlRow = $values[1];
            $htmlColumns = $this->htmlRow->find('td');

            $this->assertEquals($fixtureRecord['title'],  $htmlColumns[0]->plaintext);
            $this->assertEquals($fixtureRecord['sdesc'],  $htmlColumns[1]->plaintext);

            // 7.2 Now examine the action links
            $this->td = $htmlColumns[2];
            $actionLinks = $this->td->find('a');
            $this->assertEquals('MajorView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('MajorEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('MajorDelete', $actionLinks[2]->name);
            $unknownATag--;

            // 7.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 8. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testViewGET() {

        // 1. Obtain a record to view, login, GET the url, parse the response and send it back.
        $record2View=$this->majorsFixture->records[0];
        $url='/majors/view/' . $record2View['id'];
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,$url);

        // 2.  Look for the table that contains the view fields.
        $this->table = $html->find('table#MajorViewTable',0);
        $this->assertNotNull($this->table);

        // 3. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($this->table->find('tr'));

        // 3.1 title
        $field = $html->find('tr#title td',0);
        $this->assertEquals($record2View['title'], $field->plaintext);
        $unknownRowCnt--;

        // 3.2 sdesc
        $field = $html->find('tr#sdesc td',0);
        $this->assertEquals($record2View['sdesc'], $field->plaintext);
        $unknownRowCnt--;

        // Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 4. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#MajorsView',0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0,count($links));
    }
}
