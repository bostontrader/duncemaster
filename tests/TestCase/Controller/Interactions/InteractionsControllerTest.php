<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\ClazzesFixture;
use App\Test\Fixture\ItypesFixture;
use App\Test\Fixture\InteractionsFixture;
use App\Test\Fixture\StudentsFixture;
use Cake\ORM\TableRegistry;

class InteractionsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.clazzes',
        'app.cohorts',
        'app.interactions',
        'app.itypes',
        'app.roles',
        'app.roles_users',
        'app.sections',
        'app.students',
        'app.users'
    ];

    /* @var \App\Model\Table\ClazzesTable */
    private $clazzes;

    /* @var \App\Model\Table\InteractionsTable */
    private $interactions;

    /* @var \App\Model\Table\StudentsTable */
    private $students;

    /* @var \App\Test\Fixture\ClazzesFixture */
    private $clazzesFixture;

    /* @var \App\Test\Fixture\ItypesFixture */
    private $itypesFixture;

    /* @var \App\Test\Fixture\InteractionsFixture */
    private $interactionsFixture;

    /* @var \App\Test\Fixture\StudentsFixture */
    private $studentsFixture;

    public function setUp() {
        parent::setUp();
        $this->clazzes = TableRegistry::get('Clazzes');
        $this->interactions = TableRegistry::get('Interactions');
        $this->students = TableRegistry::get('Students');
        $this->clazzesFixture = new ClazzesFixture();
        $this->itypesFixture = new ItypesFixture();
        $this->interactionsFixture = new InteractionsFixture();
        $this->studentsFixture = new StudentsFixture();
    }

    // Test that unauthenticated users, when submitting a request to
    // an action, will get redirected to the login url.
    public function testUnauthenticatedActionsAndUsers() {
        $this->tstUnauthenticatedActionsAndUsers('interactions');
    }

    // Test that users who do not have correct roles, when submitting a request to
    // an action, will get redirected to the home page.
    public function testUnauthorizedActionsAndUsers() {
        $this->tstUnauthorizedActionsAndUsers('interactions');
    }

    public function testAddGET() {

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,'/interactions/add');

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#InteractionAddForm',0);
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

        // 3.3 Ensure that there's a select field for clazz_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($form, 'InteractionClazzId','clazzes')) $unknownSelectCnt--;

        // 3.4 Ensure that there's a select field for student_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($form, 'InteractionStudentId','students')) $unknownSelectCnt--;

        // 3.5 Ensure that there's a select field for itype_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($form, 'InteractionItypeId','itypes')) $unknownSelectCnt--;

        // 3.6 Ensure that there's an input field for participate, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#InteractionParticipate')) $unknownInputCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#InteractionsAdd');
    }

    public function testAddPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->interactionsFixture->newInteractionRecord;
        $fromDbRecord=$this->genericAddPostProlog(
            FixtureConstants::userAndyAdminId,
            '/interactions/add', $fixtureRecord,
            '/interactions', $this->interactions
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['student_id'],$fixtureRecord['student_id']);
        $this->assertEquals($fromDbRecord['itype_id'],$fixtureRecord['itype_id']);
        $this->assertEquals($fromDbRecord['participate'],$fixtureRecord['participate']);
    }

    public function testDeletePOST() {

        $interaction_id = $this->interactionsFixture->records[0]['id'];
        $this->deletePOST(
            FixtureConstants::userAndyAdminId, '/interactions/delete/',
            $interaction_id, '/interactions', $this->interactions
        );
    }

    public function testEditGET() {

        // 1. Obtain a record to edit, login, GET the url, parse the response and send it back.
        $record2Edit=$this->interactionsFixture->records[0];
        $url='/interactions/edit/' . $record2Edit['id'];
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,$url);

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#InteractionEditForm',0);
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
        if ($this->lookForHiddenInput($form, '_method', 'PUT')) $unknownInputCnt--;

        // 3.3 Ensure that there's a select field for clazz_id and that it is correctly set
        // clazz_id / $clazz['nickname'], from table
        $clazz_id = $record2Edit['clazz_id'];
        $clazz = $this->clazzes->get($clazz_id);
        if($this->inputCheckerB($form,'select#InteractionClazzId option[selected]',$clazz_id,$clazz['nickname']))
            $unknownSelectCnt--;

        // 3.4 Ensure that there's a select field for student_id and that it is correctly set
        // student_id / $student['fullname'], from table
        $student_id = $record2Edit['student_id'];
        $student = $this->students->get($student_id);
        if($this->inputCheckerB($form,'select#InteractionStudentId option[selected]',$student_id,$student['fullname']))
            $unknownSelectCnt--;

        // 3.5 Ensure that there's a select field for itype_id and that it is correctly set
        // $itype_id / $itype['title'], from fixture
        $itype_id=$record2Edit['itype_id'];
        $itype = $this->itypesFixture->get($itype_id);
        if($this->inputCheckerB($form,'select#InteractionItypeId option[selected]',$itype_id,$itype['title']))
            $unknownSelectCnt--;

        // 3.6 Ensure that there's an input field for participate, of type text, and that it is correctly set
        if($this->inputCheckerA($form,'input#InteractionParticipate',
            $record2Edit['participate'])) $unknownInputCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#InteractionsEdit');
    }

    public function testEditPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->interactionsFixture->newInteractionRecord;
        $fromDbRecord=$this->genericEditPutProlog(
            FixtureConstants::userAndyAdminId,
            '/interactions/edit', $fixtureRecord,
            '/interactions', $this->interactions
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['clazz_id'], $fixtureRecord['clazz_id']);
        $this->assertEquals($fromDbRecord['student_id'], $fixtureRecord['student_id']);
        $this->assertEquals($fromDbRecord['itype_id'], $fixtureRecord['itype_id']);
        $this->assertEquals($fromDbRecord['participate'], $fixtureRecord['participate']);
    }

    public function testIndexGET() {

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,'/interactions/index');

        // 2. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#InteractionsIndex', 0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 3. Look for the create new interaction link
        $this->assertEquals(1, count($html->find('a#InteractionAdd')));
        $unknownATag--;

        // 4. Ensure that there is a suitably named table to display the results.
        $this->table = $html->find('table#InteractionsTable', 0);
        $this->assertNotNull($this->table);

        // 5. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $this->thead = $this->table->find('thead', 0);
        $this->thead_ths = $this->thead->find('tr th');

        $this->assertEquals($this->thead_ths[0]->id, 'clazz');
        $this->assertEquals($this->thead_ths[1]->id, 'student');
        $this->assertEquals($this->thead_ths[2]->id, 'itype');
        $this->assertEquals($this->thead_ths[3]->id, 'participate');
        $this->assertEquals($this->thead_ths[4]->id, 'actions');
        $column_count = count($this->thead_ths);
        $this->assertEquals($column_count, 5); // no other columns

        // 6. Ensure that the tbody section has the same
        //    quantity of rows as the count of interaction records in the fixture.
        $this->tbody = $this->table->find('tbody', 0);
        $this->tbody_rows = $this->tbody->find('tr');
        $this->assertEquals(count($this->tbody_rows), count($this->interactionsFixture->records));

        // 7. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->interactionsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($this->tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $this->htmlRow = $values[1];
            $htmlColumns = $this->htmlRow->find('td');

            // 7.0 clazz_nickname. read from Table because we need to compute
            // the 'nickname' virtual field.
            $clazz = $this->clazzes->get($fixtureRecord['clazz_id']);
            $this->assertEquals($clazz->nickname, $htmlColumns[0]->plaintext);

            // 7.1 student_fullname. read from Table because we need to compute
            // the 'fullname' virtual field.
            $student = $this->students->get($fixtureRecord['student_id']);
            $this->assertEquals($student->fullname, $htmlColumns[1]->plaintext);

            // 7.2 itype_id requires finding the related value in the ItypesFixture
            $itype_id = $fixtureRecord['itype_id'];
            $itype = $this->itypesFixture->get($itype_id);
            $this->assertEquals($itype['title'], $htmlColumns[2]->plaintext);

            // 7.3 participate
            $this->assertEquals($fixtureRecord['participate'],  $htmlColumns[3]->plaintext);

            // 7.4 Now examine the action links
            $this->td = $htmlColumns[4];
            $actionLinks = $this->td->find('a');
            $this->assertEquals('InteractionView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('InteractionEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('InteractionDelete', $actionLinks[2]->name);
            $unknownATag--;

            // 7.9 No other columns
            $this->assertEquals(count($htmlColumns), $column_count);
        }

        // 8. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testViewGET() {

        // 1. Obtain a record to view, login, GET the url, parse the response and send it back.
        $record2View=$this->interactionsFixture->records[0];
        $url='/interactions/view/' . $record2View['id'];
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,$url);

        // 2.  Look for the table that contains the view fields.
        $this->table = $html->find('table#InteractionViewTable', 0);
        $this->assertNotNull($this->table);

        // 3. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($this->table->find('tr'));

        // 3.1 clazz requires finding the nickname, which is computed by the Clazz Entity.
        $field = $html->find('tr#clazz td', 0);
        $clazz = $this->clazzes->get($record2View['clazz_id']);
        $this->assertEquals($clazz->nickname, $field->plaintext);
        $unknownRowCnt--;

        // 3.2 student requires finding the nickname, which is computed by the Semester Entity.
        $field = $html->find('tr#student td', 0);
        $student = $this->students->get($record2View['student_id']);
        $this->assertEquals($student->fullname, $field->plaintext);
        $unknownRowCnt--;

        // 3.3 itype_id requires finding the related value in the ItypesFixture
        $field = $html->find('tr#itype td', 0);
        $itype_id = $record2View['itype_id'];
        $itype = $this->itypesFixture->get($itype_id);
        $this->assertEquals($itype['title'], $field->plaintext);
        $unknownRowCnt--;

        // 3.4 participate
        $field = $html->find('tr#participate td',0);
        $this->assertEquals($record2View['participate'], $field->plaintext);
        $unknownRowCnt--;

        // 3.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 4. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#InteractionsView', 0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0, count($links));
    }
}
