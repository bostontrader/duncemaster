<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\ClazzesFixture;
use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\SectionsFixture;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

/**
 * Class ClazzesControllerTest
 * @package App\Test\TestCase\Controller
 */
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

    /* @var \App\Test\Fixture\ClazzesFixture */
    private $clazzesFixture;

    /* @var \App\Model\Table\SectionsTable */
    private $sections;

    /* @var \App\Test\Fixture\SectionsFixture */
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
    //public function testUnauthenticatedActionsAndUsers() {
        //$this->tstUnauthenticatedActionsAndUsers('clazzes');
    //}

    // Test that users who do not have correct roles, when submitting a request to
    // an action, will get redirected to the home page.
    //public function testUnauthorizedActionsAndUsers() {
        //$this->tstUnauthorizedActionsAndUsers('clazzes');
    //}

    /**
     * GET /clazzes/add?section_id=n
     * Returns the new clazz form.
     *
     * The section_id parameter is mandatory. Because...
     * A clazz needs an associated section. The entry form can easily enough present a select-list
     * of section choices. But if the request _does not_ specify a section then which sections should
     * appear in the select list? Including all of them will involve a long and cumbersome list
     * because it's filled with sections from semesters past. But pruning that list is a remarkably
     * slippery and needlessly tedious issue best left as an exercise for the reader.
     *
     * As a practical matter, this is a non-issue. The creation of a new clazz should be in the
     * context of a section (and its teacher and semester) that has already been determined.
     * We'll still use a select-list, but now we can populate it with only those sections from
     * the same semester, with the same teacher, as the section specified by the request.
     *
     * 1. The new class form can only be seen by an admin or the teacher of the specified section.
     *    We don't want to leak _any_ information to users who are not properly authorized. This
     *    form will contain a list of sections, so redirect to /clazzes/index if not properly authorized.
     * 2. A class must have an associated section.
     * 3. The form will present a select list of candidate sections and by default no option will be selected.
     *    If (the section_id param matches an available choice) then
     *      the value of that param will be used by the form to set the initial selection in the select list.
     * 4. The avail choices for the select list are:
     *    all sections from the same semester, with the same teacher, as the section specified by the request.
     */

    public function testAddGet() {

        // 1. Build a list of all sections that have the same semester and teacher
        // as the given typical section
        $query = "SELECT DISTINCT b.id
            FROM sections AS a, sections AS b
            WHERE a.semester_id=b.semester_id
            and a.teacher_id=b.teacher_id
            and a.id=".FixtureConstants::sectionTypical;

        /* @var \Cake\Database\Connection $connection */
        $connection = ConnectionManager::get('default');
        $allSectionsForThisTeacherAndSemester = $connection->execute($query)->fetchAll('assoc');

        // 2.1 admin   GET /clazzes/add
        //     The section_id param is missing and mandatory.
        //     Redirect to /clazzes/index
        $this->tstAddGet(FixtureConstants::userAndyAdminId);

        // 2.2 teacher1 GET /clazzes/add?section_id=n
        //     Note: Make sure userTommyTeacher is the teacher for sectionTypical.
        $this->tstAddGet(FixtureConstants::userTommyTeacherId, FixtureConstants::sectionTypical, $allSectionsForThisTeacherAndSemester);

        // 2.3 teacher2 GET /clazzes/add?section_id=n
        //     Note: Make sure userTommyTeacher is NOT the teacher for sectionTypical.
        $this->tstAddGet(FixtureConstants::userTommyTeacherId, FixtureConstants::sectionTypical, $allSectionsForThisTeacherAndSemester);

        // 2.4 admin   GET /clazzes/add?section_id=n
        //     An admin can do this but the select list is still populated with the same sections as for
        //     a teacher.
        $this->tstAddGet(FixtureConstants::userAndyAdminId, FixtureConstants::sectionTypical, $allSectionsForThisTeacherAndSemester);
    }


    private function tstAddGET($user_id, $section_id=null, $allSectionsForThisTeacherAndSemester=[]) {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin($user_id);

        if(is_null($section_id)) {
            $this->get('/clazzes/add');
            $this->assertResponseSuccess(); // 2xx, 3xx
            $this->assertRedirect('/clazzes');
        } else {
            $this->get('/clazzes/add?section_id='.$section_id);
            $this->assertResponseOk(); // 2xx
            $this->assertNoRedirect();
        }

        // 2. Parse the html from the response
        /* @var \simple_html_dom_node $html */
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#ClazzAddForm',0);
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

        // 4.3 test the ClazzSectionId select.
        if(is_null($section_id)) {
            // 4.3.1 Ensure that there's a select field for section_id, that it has no selection,
            // and that it has the correct quantity of available choices.
            if($this->selectCheckerA($form, 'ClazzSectionId', 'sections')) $unknownSelectCnt--;
        } else {
            if($this->tstSectionIdSelect($form, $section_id, $this->sections)) $unknownSelectCnt--;
        }

        // 4.4 Ensure that there's an input field for event_datetime, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#ClazzDatetime')) $unknownInputCnt--;

        // 4.5 Ensure that there's an input field for comments, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#ClazzComments')) $unknownInputCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#ClazzesAdd');
    }

    public function testAddPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->clazzesFixture->newClazzRecord;
        $fromDbRecord=$this->genericAddPostProlog(
            FixtureConstants::userAndyAdminId,
            '/clazzes/add', $fixtureRecord,
            '/clazzes', $this->clazzes
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['section_id'],$fixtureRecord['section_id']);
        $this->assertEquals($fromDbRecord['event_datetime'],$fixtureRecord['event_datetime']);
        $this->assertEquals($fromDbRecord['comments'],$fixtureRecord['comments']);
    }

    public function testDeletePOST() {

        $clazz_id = $this->clazzesFixture->records[0]['id'];
        $this->deletePOST(
            FixtureConstants::userAndyAdminId, '/clazzes/delete/',
            $clazz_id, '/clazzes', $this->clazzes
        );
    }

    public function testEditGET() {

        // 1. Obtain a record to edit, login, GET the url, parse the response and send it back.
        $record2Edit=$this->clazzesFixture->records[0];
        $url='/clazzes/edit/' . $record2Edit['id'];
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,$url);

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#ClazzEditForm',0);
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

        // 3.3. Ensure that there's a select field for section_id and that it is correctly set
        $section_id = $record2Edit['section_id'];
        if($this->tstSectionIdSelect($form, $section_id, $this->sections)) $unknownSelectCnt--;

        // 3.4 Ensure that there's an input field for event_datetime, of type text, and that it is correctly set
        if($this->inputCheckerA($form,'input#ClazzDatetime',
            $record2Edit['event_datetime'])) $unknownInputCnt--;

        // 3.5 Ensure that there's an input field for comments, of type text, and that it is correctly set
        if($this->inputCheckerA($form,'input#ClazzComments',
            $record2Edit['comments'])) $unknownInputCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#ClazzesEdit');
    }

    /**
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

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->clazzesFixture->newClazzRecord;
        $fromDbRecord=$this->genericEditPutProlog(
            FixtureConstants::userAndyAdminId,
            '/clazzes/edit', $fixtureRecord,
            '/clazzes', $this->clazzes
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['section_id'],$fixtureRecord['section_id']);
        $this->assertEquals($fromDbRecord['event_datetime'],$fixtureRecord['event_datetime']);
    }

    // GET /index, no section_id parameter
    public function testIndexGet() {
        $this->tstIndexGet(null);
    }

    // GET /index, with section_id parameter
    public function testIndexGetSectionId() {
        $this->tstIndexGet($this->sectionsFixture->records[0]['id']);
    }
    
    private function tstIndexGET($section_id=null) {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);

        if(is_null($section_id))
            $this->get('/clazzes/index');
        else
            $this->get('/clazzes/index?section_id='.$section_id);

        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#ClazzesIndex',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 4. Test the new clazz link

        // 4.1 Does it exist?
        $link=$html->find('a#ClazzAdd')[0];
        $this->assertNotNull($link);
        $unknownATag--;

        // 4.2 Does it point to the correct destination?
        if(is_null($section_id))
            $expectedHref='/clazzes/add';
        else
            $expectedHref='/clazzes/add?section_id='.$section_id;

        $this->assertEquals($expectedHref,$link->href);

        // 5. Examine the table of Clazzes.
        /* @var \simple_html_dom_node $html */
        if(is_null($section_id))
            $unknownATag-=$this->tstClazzesTable($html,$this->clazzes,$this->clazzesFixture,$this->sections);
        else
            $unknownATag-=$this->tstClazzesTable($html,$this->clazzes,$this->clazzesFixture,$this->sections,$section_id);

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

        $this->assertEquals($thead_ths[0]->id, 'event_datetime');
        $this->assertEquals($thead_ths[1]->id, 'comments');
        $this->assertEquals($thead_ths[2]->id, 'attend');
        $this->assertEquals($thead_ths[3]->id, 'participate');
        $this->assertEquals($thead_ths[4]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,5); // no other columns

        // 3. Ensure that the tbody section has the correct quantity of rows.
        // This should be done using a very similar query as used by the controller.
        $this->tbody = $this->table->find('tbody',0);
        $this->tbody_rows = $this->tbody->find('tr');
        //if(!is_null($sectionId))
            //$clazzesFixture->filterBySectionId($sectionId);
        //$this->assertEquals(count($tbody_rows), count($clazzesFixture->records));


        $connection = ConnectionManager::get('default');

        // This query should be essentially the same as the query in InteractionsController.attend
        $query = "select students.sort, students.sid, students.id as student_id, students.giv_name, students.fam_name, students.phonetic_name, cohorts.id, sections.id, clazzes.id
            from students
            left join cohorts on students.cohort_id = cohorts.id
            left join sections on sections.cohort_id = cohorts.id
            left join clazzes on clazzes.section_id = sections.id
            left join interactions on interactions.clazz_id=clazzes.id and interactions.student_id=students.id and interactions.itype_id=".ItypesController::ATTEND." where clazzes.id=".$clazz_id.
            " order by sort";
        $studentsResults = $connection->execute($query)->fetchAll('assoc');
        $s1=count($this->tbody_rows);
        $s2=count($studentsResults);
        $this->assertEquals($s1,$s2);

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

            // 8.0 event_datetime
            $this->assertEquals($fixtureRecord['event_datetime'], $htmlColumns[0]->plaintext);

            // 8.1 comments
            $this->assertEquals($fixtureRecord['comments'], $htmlColumns[1]->plaintext);


            // 8.4 Now examine the action links
            $this->td = $htmlColumns[4];
            $actionLinks = $this->td->find('a');
            $this->assertEquals('ClazzAttend', $actionLinks[0]->name);
            $aTagsFoundCnt++;
            $this->assertEquals('ClazzParticipate', $actionLinks[1]->name);
            $aTagsFoundCnt++;
            $this->assertEquals('ClazzView', $actionLinks[2]->name);
            $aTagsFoundCnt++;
            $this->assertEquals('ClazzEdit', $actionLinks[3]->name);
            $aTagsFoundCnt++;
            $this->assertEquals('ClazzDelete', $actionLinks[4]->name);
            $aTagsFoundCnt++;

            // 8.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }
        return $aTagsFoundCnt;
    }

    public function testViewGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $fixtureRecord=$this->clazzesFixture->records[0];
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

        // 2.3 comments
        $field = $html->find('tr#comments td',0);
        $this->assertEquals($fixtureRecord['comments'], $field->plaintext);
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
