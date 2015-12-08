<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\ClazzesFixture;
use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\SectionsFixture;
use Cake\ORM\TableRegistry;

class ClazzesControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.clazzes',
        'app.roles',
        'app.roles_users',
        'app.sections',
        'app.users'
    ];

    /* @var \App\Model\Table\ClazzesTable */
    private $clazzes;
    private $clazzesFixture;

    /* @var \App\Model\Table\SectionsTable */
    private $sections;
    private $sectionsFixture;

    public function setUp() {
        parent::setUp();
        $this->clazzes = TableRegistry::get('Clazzes');
        $this->sections = TableRegistry::get('Sections');
        $this->clazzesFixture = new ClazzesFixture();
        $this->sectionsFixture = new SectionsFixture();
    }

    // Test that unauthenticated users, when submitting a request to
    // an action, will get redirected to the login url.
    public function testUnauthenticatedActionsAndUsers() {
        $this->tstUnauthenticatedActionsAndUsers('clazzes');
    }

    // Test that users who do not have correct roles, when submitting a request to
    // an action, will get redirected to the home page.
    public function testUnauthorizedActionsAndUsers() {
        $this->tstUnauthorizedActionsAndUsers('clazzes');
    }

    // GET /add, no section_id parameter
    public function testAddGet() {
        $this->tstAddGet(null);
    }

    // GET /add, with section_id parameter
    public function testAddGetSectionId() {
        $this->tstAddGet($this->sectionsFixture->section1Record['id']);
    }

    private function tstAddGET($section_id=null) {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);

        if(is_null($section_id))
            $this->get('/clazzes/add');
        else
            $this->get('/clazzes/add?section_id=1');

        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $this->form = $html->find('form#ClazzAddForm',0);
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

        // 4.3 test the ClazzSectionId select.
        if(is_null($section_id)) {
            // 4.3.1 Ensure that there's a select field for section_id, that it has no selection,
            // and that it has the correct quantity of available choices.
            if($this->selectCheckerA($this->form, 'ClazzSectionId', 'sections')) $unknownSelectCnt--;
        } else {
            if($this->tstSectionIdSelect($this->form, $section_id, $this->sections)) $unknownSelectCnt--;
        }

        // 4.4 Ensure that there's an input field for event_datetime, of type text, and that it is empty
        if($this->inputCheckerA($this->form,'input#ClazzDatetime')) $unknownInputCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#ClazzesAdd',0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testAddPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->post('/clazzes/add', $this->clazzesFixture->newClazzRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/clazzes' );

        // Now verify what we think just got written
        $new_id = count($this->clazzesFixture->records) + 1;
        $query = $this->clazzes->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_clazz = $this->clazzes->get($new_id);
        $this->assertEquals($new_clazz['section_id'],$this->clazzesFixture->newClazzRecord['section_id']);
        $this->assertEquals($new_clazz['event_datetime'],$this->clazzesFixture->newClazzRecord['event_datetime']);
    }

    public function testDeletePOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $clazz_id = $this->clazzesFixture->clazz1Record['id'];
        $this->post('/clazzes/delete/' . $clazz_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/clazzes' );

        // Now verify that the record no longer exists
        $query = $this->clazzes->find()->where(['id' => $clazz_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/clazzes/edit/' . $this->clazzesFixture->clazz1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $this->form = $html->find('form#ClazzEditForm',0);
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
        if($this->lookForHiddenInput($this->form,'_method','PUT')) $unknownInputCnt--;

        // 4.3. Ensure that there's a select field for section_id and that it is correctly set
        $section_id = $this->clazzesFixture->clazz1Record['section_id'];
        if($this->tstSectionIdSelect($this->form, $section_id, $this->sections)) $unknownSelectCnt--;

        // 4.4 Ensure that there's an input field for event_datetime, of type text, and that it is correctly set
        if($this->inputCheckerA($this->form,'input#ClazzDatetime',
            $this->clazzesFixture->clazz1Record['event_datetime'])) $unknownInputCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#ClazzesEdit',0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0,count($links));
    }

    /**
     * A. The input has a given id, is of some given type, and has a specified value.
     * @param \simple_html_dom_node $html_node the form that contains the select.
     * @param int $section_id The expected selected section_id.
     * @param \App\Model\Table\SectionsTable $sections.
     * @return boolean Return true if a matching input is found, else assertion errors.
     */
    private function tstSectionIdSelect($html_node, $section_id, $sections) {

        // 1. Ensure that there's a select field for section_id and that it is correctly set
        $option = $html_node->find('select#ClazzSectionId option[selected]',0);
        $this->assertEquals($option->value, $section_id);

        // 2. Even though section_id is correct, we don't display section_id.  Instead we display the
        // nickname from the related Sections table.  But nickname is a virtual field so we must
        // read the record in order to get the nickname, instead of looking it up in the fixture records.
        $section = $sections->get($section_id);
        $this->assertEquals($section->nickname, $option->plaintext);
        return true;
    }

    public function testEditPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $clazz_id = $this->clazzesFixture->clazz1Record['id'];
        $this->put('/clazzes/edit/' . $clazz_id, $this->clazzesFixture->newClazzRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/clazzes');

        // Now verify what we think just got written
        $query = $this->clazzes->find()->where(['id' => $clazz_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $clazz = $this->clazzes->get($clazz_id);
        $this->assertEquals($clazz['section_id'],$this->clazzesFixture->newClazzRecord['section_id']);
        $this->assertEquals($clazz['event_datetime'],$this->clazzesFixture->newClazzRecord['event_datetime']);
    }

    public function testIndexGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/clazzes/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#ClazzesIndex',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 4. Look for the create new clazz link
        $this->assertEquals(1, count($html->find('a#ClazzAdd')));
        $unknownATag--;

        // 5. Examine the table of Clazzes.
        /* @var \simple_html_dom_node $html */
        $unknownATag-=$this->tstClazzesTable($html,$this->clazzes,$this->clazzesFixture,$this->sections);

        // 6. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    /**
     * At least three views create a table of Clazzes.
     * (Sections.edit,  Sections.view, and Clazzes.index)
     * This table must be tested. Factor that testing into this method.
     * @param \simple_html_dom_node $html parsed dom that contains the ClazzesTable
     * @param \App\Model\Table\ClazzesTable $clazzes
     * @param \App\Test\Fixture\ClazzesFixture $clazzesFixture
     * @param \App\Model\Table\SectionsTable $sections
     * @param int $sectionId If $sectionId=null, the test will expect to see all the records from
     * the fixture. Else the test will only expect to see fixture records with the given $sectionId.
     * @return int $aTagsFoundCnt The number of aTagsFound.
     */
    public function tstClazzesTable($html, $clazzes, $clazzesFixture, $sections, $sectionId=null) {

        // 1. Ensure that there is a suitably named table to display the results.
        $this->table = $html->find('table#ClazzesTable',0);
        $this->assertNotNull($this->table);

        // 2. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $this->thead = $this->table->find('thead',0);
        $thead_ths = $this->thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'section');
        $this->assertEquals($thead_ths[1]->id, 'week');
        $this->assertEquals($thead_ths[2]->id, 'event_datetime');
        $this->assertEquals($thead_ths[3]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,4); // no other columns

        // 3. Ensure that the tbody section has the same
        //    quantity of rows as the count of expected clazz records in the fixture, filtered by $sectionId
        $this->tbody = $this->table->find('tbody',0);
        $tbody_rows = $this->tbody->find('tr');
        if(!is_null($sectionId))
            $clazzesFixture->filter($sectionId);
        $this->assertEquals(count($tbody_rows), count($clazzesFixture->records));

        // 4. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($clazzesFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        $aTagsFoundCnt=0;
        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $this->htmlRow = $values[1];
            $htmlColumns = $this->htmlRow->find('td');

            // 8.0 section_nickname (virtual field of Section)
            $section = $sections->get($fixtureRecord['section_id']);
            $this->assertEquals($section->nickname, $htmlColumns[0]->plaintext);

            // 8.1 week (virtual field of Clazz)
            $clazz = $clazzes->get($fixtureRecord['id']);
            $this->assertEquals($clazz->week, $htmlColumns[1]->plaintext);

            // 8.2 event_datetime
            $this->assertEquals($fixtureRecord['event_datetime'], $htmlColumns[2]->plaintext);

            // 8.3 Now examine the action links
            $this->td = $htmlColumns[3];
            $actionLinks = $this->td->find('a');
            $this->assertEquals('ClazzView', $actionLinks[0]->name);
            $aTagsFoundCnt++;
            $this->assertEquals('ClazzEdit', $actionLinks[1]->name);
            $aTagsFoundCnt++;
            $this->assertEquals('ClazzDelete', $actionLinks[2]->name);
            $aTagsFoundCnt++;

            // 8.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }
        return $aTagsFoundCnt;
    }

    public function testViewGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $fixtureRecord=$this->clazzesFixture->clazz1Record;
        $this->get('/clazzes/view/' . $fixtureRecord['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3.  Look for the table that contains the view fields.
        $this->table = $html->find('table#ClazzViewTable',0);
        $this->assertNotNull($this->table);

        // 4. Now inspect the fields on the table.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($this->table->find('tr'));

        // 2.1 section requires finding the nickname, which is computed by the Section Entity.
        $field = $html->find('tr#section td',0);
        $section_id = $fixtureRecord['id'];
        $section = $this->sections->get($section_id);
        $this->assertEquals($section->nickname, $field->plaintext);
        $unknownRowCnt--;

        // 2.2 event_datetime
        $field = $html->find('tr#event_datetime td',0);
        $this->assertEquals($fixtureRecord['event_datetime'], $field->plaintext);
        $unknownRowCnt--;

        // 2.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 3. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#ClazzesView',0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0,count($links));
    }
}
