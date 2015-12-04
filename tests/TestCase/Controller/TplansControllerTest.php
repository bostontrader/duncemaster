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

    private $tplansFixture;
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

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/tplans/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $this->form = $html->find('form#TplanAddForm',0);
        $this->assertNotNull($this->form);

        // 4. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 4.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($this->form->find('select'));
        $unknownInputCnt = count($this->form->find('input'));

        // 4.2 Look for the hidden POST input
        if($this->lookForHiddenInput($this->form)) $unknownInputCnt--;

        // 4.3 Ensure that there's an input field for title, of type text, and that it is empty
        if($this->inputCheckerA($this->form,'input#TplanTitle')) $unknownInputCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#TplansAdd',0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testAddPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->post('/tplans/add', $this->tplansFixture->newTplanRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/tplans' );

        // Now verify what we think just got written
        $new_id = count($this->tplansFixture->records) + 1;
        $query = $this->tplans->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_tplan = $this->tplans->get($new_id);
        $this->assertEquals($new_tplan['title'],$this->tplansFixture->newTplanRecord['title']);
    }

    public function testDeletePOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $tplan_id = $this->tplansFixture->tplan1Record['id'];
        $this->post('/tplans/delete/' . $tplan_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/tplans' );

        // Now verify that the record no longer exists
        $query = $this->tplans->find()->where(['id' => $tplan_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/tplans/edit/' . $this->tplansFixture->tplan1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#TplansEdit',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 4. Look for the create new tplan_elements link
        $this->assertEquals(1, count($html->find('a#TplanElementAdd')));
        $unknownATag--;

        // 5. Ensure that the correct form exists
        $this->form = $html->find('form#TplanEditForm',0);
        $this->assertNotNull($this->form);

        // 6. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 6.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($this->form->find('select'));
        $unknownInputCnt = count($this->form->find('input'));

        // 6.2 Look for the hidden POST input
        if($this->lookForHiddenInput($this->form,'_method','PUT')) $unknownInputCnt--;

        // 6.3 Ensure that there's an input field for title, of type text, and that it is correctly set
        if($this->inputCheckerA($this->form,'input#TplanTitle',
            $this->tplansFixture->tplan1Record['title'])) $unknownInputCnt--;

        // 6.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 7. Examine the table of TplanElements.
        $tect=new TplanElementsControllerTest();
        /* @var \simple_html_dom_node $html */
        $unknownATag-=$tect->tstTplanElementsTable($html,$this->tplanElementsFixture);

        // 8. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testEditPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $tplan_id = $this->tplansFixture->tplan1Record['id'];
        $this->put('/tplans/edit/' . $tplan_id, $this->tplansFixture->newTplanRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/tplans');

        // Now verify what we think just got written
        $query = $this->tplans->find()->where(['id' => $tplan_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $tplan = $this->tplans->get($tplan_id);
        $this->assertEquals($tplan['title'],$this->tplansFixture->newTplanRecord['title']);
    }

    public function testIndexGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/tplans/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#TplansIndex',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 4. Look for the create new tplan link
        $this->assertEquals(1, count($html->find('a#TplanAdd')));
        $unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        $this->table = $html->find('table#TplansTable',0);
        $this->assertNotNull($this->table);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $this->thead = $this->table->find('thead',0);
        $thead_ths = $this->thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'title');
        $this->assertEquals($thead_ths[1]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,2); // no other columns

        // 7. Ensure that the tbody section has the same
        //    quantity of rows as the count of tplans records in the fixture.
        $this->tbody = $this->table->find('tbody',0);
        $tbody_rows = $this->tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->tplansFixture->records));

        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->tplansFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $this->htmlRow = $values[1];
            $htmlColumns = $this->htmlRow->find('td');

            // 8.0 title
            $this->assertEquals($fixtureRecord['title'],  $htmlColumns[0]->plaintext);

            // 8.1 Now examine the action links
            $this->td = $htmlColumns[1];
            $actionLinks = $this->td->find('a');
            $this->assertEquals('TplanView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('TplanEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('TplanDelete', $actionLinks[2]->name);
            $unknownATag--;

            // 8.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 9. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testViewGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $fixtureRecord=$this->tplansFixture->tplan1Record;
        $this->get('/tplans/view/' . $fixtureRecord['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#TplansView',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 4.  Look for the table that contains the view fields.
        $this->table = $html->find('table#TplanViewTable',0);
        $this->assertNotNull($this->table);

        // 5. Now inspect the fields in the table.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($this->table->find('tr'));

        // 5.1 section.subject.title requires finding the related value in the SubjectsFixture,
        // via the SectionsFixture
        $field = $html->find('tr#section_subject_title td',0);
        $section_id = $this->tplansFixture->tplan1Record['section_id'];
        $section = $this->sectionsFixture->get($section_id);
        $subject_id = $section['section_id'];
        $subject = $this->subjectsFixture->get($subject_id);
        $this->assertEquals($subject['title'], $field->plaintext);
        $unknownRowCnt--;

        // 5.1 title
        $field = $html->find('tr#title td',0);
        $this->assertEquals($fixtureRecord['title'], $field->plaintext);
        $unknownRowCnt--;

        // 5.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 6. Examine the table of repeating tplan_elements
        /* @var \simple_html_dom_node $html */
        $tect=new TplanElementsControllerTest();
        $unknownATag-=$tect->tstTplanElementsTable($html,$this->tplanElementsFixture);

        // 7. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }
}
