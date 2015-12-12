<?php
namespace App\Test\TestCase\Controller;

use Cake\Datasource\ConnectionManager;
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
    
    private $clazzesFixture;
    private $itypesFixture;
    private $interactionsFixture;
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

    // Make sure clazz->section->cohort = student->cohort!
    public function testFixtureIntegrity() {

        $connection = ConnectionManager::get('test');
        $query="SELECT clazz_id, student_id, clazzes.section_id, sections.cohort_id as Csec, students.cohort_id as CStu from interactions
            left join students on interactions.student_id=students.id
            left join clazzes on interactions.clazz_id=clazzes.id
            left join sections on clazzes.section_id=sections.id
            where sections.cohort_id != students.cohort_id";
        $results = $connection->execute($query)->fetchAll('assoc');
        $n=count($results);
        $this->assertEquals(0,$n);
    }

    public function testAddGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/interactions/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $this->form = $html->find('form#InteractionAddForm',0);
        $this->assertNotNull($this->form);

        // 4. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // 4.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($this->form->find('select'));
        $unknownInputCnt = count($this->form->find('input'));

        // 4.2 Look for the hidden POST input
        if($this->lookForHiddenInput($this->form)) $unknownInputCnt--;

        // 4.3 Ensure that there's a select field for clazz_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($this->form, 'InteractionClazzId','clazzes')) $unknownSelectCnt--;

        // 4.4 Ensure that there's a select field for student_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($this->form, 'InteractionStudentId','students')) $unknownSelectCnt--;

        // 4.4 Ensure that there's a select field for itype_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($this->form, 'InteractionItypeId','itypes')) $unknownSelectCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#InteractionsAdd',0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testAddPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->post('/interactions/add', $this->interactionsFixture->newInteractionRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/interactions' );

        // Now verify what we think just got written
        $new_id = count($this->interactionsFixture->records) + 1;
        $query = $this->interactions->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_interaction = $this->interactions->get($new_id);
        $this->assertEquals($new_interaction['clazz_id'],$this->interactionsFixture->newInteractionRecord['clazz_id']);
        $this->assertEquals($new_interaction['student_id'],$this->interactionsFixture->newInteractionRecord['student_id']);
        $this->assertEquals($new_interaction['itype_id'],$this->interactionsFixture->newInteractionRecord['itype_id']);
    }

    // GET /attend, no clazz_id parameter
    //public function testAttendGet() {
        //$this->tstAttendGet(null);
    //}

    // GET /attend, with clazz_id parameter, for a class with no attendance
    public function testAttendGetClazzId() {
        $this->tstAttendGet($this->interactionsFixture, FixtureConstants::clazz1_id);
    }

    // GET /attend, with clazz_id parameter, for a class with existing attendance
    //public function testAttendGetClazzId() {
        //$this->tstAttendGet($this->clazzesFixture->clazz1Record['id']);
    //}

    private function tstAttendGET($interactionsFixture, $clazz_id=null) {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);

        if(is_null($clazz_id))
            $this->get('/interactions/attend');
        else
            $this->get('/interactions/attend?clazz_id='.$clazz_id);

        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#InteractionsAttend',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 4. Ensure that there is a suitably named form element
        $this->form = $this->content->find('form#InteractionAttendForm', 0);
        $this->assertNotNull($this->form);

        // 5. Ensure that there is a suitably named table to display the results.
        $this->table = $this->form->find('table#InteractionsTable', 0);
        $this->assertNotNull($this->table);

        // 6. Ensure that said table's thead element co`ntains the correct
        //    headings, in the correct order, and nothing else.
        $this->thead = $this->table->find('thead', 0);
        $this->thead_ths = $this->thead->find('tr th');

        $this->assertEquals($this->thead_ths[0]->id, 'fam_name');
        $this->assertEquals($this->thead_ths[1]->id, 'giv_name');
        $this->assertEquals($this->thead_ths[2]->id, 'attend');
        $column_count = count($this->thead_ths);
        $this->assertEquals($column_count, 3); // no other columns

        // 7. Ensure that the tbody section has the same
        //    quantity of rows as the count of interaction records in the fixture.
        $this->tbody = $this->table->find('tbody', 0);
        $this->tbody_rows = $this->tbody->find('tr');
        if(!is_null($clazz_id))
            $interactionsFixture->filterByClazzId($clazz_id);
        $s1=count($this->tbody_rows);
        $s2=count($this->interactionsFixture->records);
        $this->assertEquals(count($this->tbody_rows), count($this->interactionsFixture->records));

        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        //$iterator = new \MultipleIterator();
        //$iterator->attachIterator(new \ArrayIterator($this->interactionsFixture->records));
        //$iterator->attachIterator(new \ArrayIterator($this->tbody_rows));

        //foreach ($iterator as $values) {
            //$fixtureRecord = $values[0];
            //$this->htmlRow = $values[1];
            //$htmlColumns = $this->htmlRow->find('td');

            // 8.0 clazz_nickname. read from Table because we need to compute
            // the 'nickname' virtual field.
            //$clazz = $this->clazzes->get($fixtureRecord['clazz_id']);
            //$this->assertEquals($clazz->nickname, $htmlColumns[0]->plaintext);

            // 8.1 student_fullname. read from Table because we need to compute
            // the 'fullname' virtual field.
            //$student = $this->students->get($fixtureRecord['student_id']);
            //$this->assertEquals($student->fullname, $htmlColumns[1]->plaintext);

            // 8.2 itype_id requires finding the related value in the ItypesFixture
            //$itype_id = $fixtureRecord['itype_id'];
            //$itype = $this->itypesFixture->get($itype_id);
            //$this->assertEquals($itype['title'], $htmlColumns[2]->plaintext);

            // 8.2 Now examine the action links
            //$this->td = $htmlColumns[3];
            //$actionLinks = $this->td->find('a');
            //$this->assertEquals('InteractionView', $actionLinks[0]->name);
            //$unknownATag--;
            //$this->assertEquals('InteractionEdit', $actionLinks[1]->name);
            //$unknownATag--;
            //$this->assertEquals('InteractionDelete', $actionLinks[2]->name);
            //$unknownATag--;

            // 8.9 No other columns
            //$this->assertEquals(count($htmlColumns), $column_count);
        //}

        // 9. Ensure that all the <A> tags have been accounted for
        //$this->assertEquals(0, $unknownATag);

    }

    // GET /attend, no clazz_id parameter
    public function testAttendPOST() {
        //$this->tstAttendGet(null);
    }

    public function testDeletePOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $interaction_id = $this->interactionsFixture->interaction1Record['id'];
        $this->post('/interactions/delete/' . $interaction_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/interactions' );

        // Now verify that the record no longer exists
        $query = $this->interactions->find()->where(['id' => $interaction_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/interactions/edit/' . $this->interactionsFixture->interaction1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $this->form = $html->find('form#InteractionEditForm', 0);
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
        if ($this->lookForHiddenInput($this->form, '_method', 'PUT')) $unknownInputCnt--;

        // 4.3. Ensure that there's a select field for clazz_id and that it is correctly set
        $option = $this->form->find('select#InteractionClazzId option[selected]', 0);
        $clazz_id = $this->interactionsFixture->interaction1Record['clazz_id'];
        $this->assertEquals($option->value, $clazz_id);

        // Even though clazz_id is correct, we don't display clazz_id.  Instead we display the
        // nickname from the related Clazzes table.  But nickname is a virtual field so we must
        // read the record in order to get the nickname, instead of looking it up in the fixture records.
        $clazz = $this->clazzes->get($clazz_id);
        $this->assertEquals($clazz->nickname, $option->plaintext);
        $unknownSelectCnt--;

        // 4.4 Ensure that there's a select field for student_id and that it is correctly set
        $option = $this->form->find('select#InteractionStudentId option[selected]', 0);
        $student_id = $this->interactionsFixture->interaction1Record['student_id'];
        $this->assertEquals($option->value, $student_id);

        // Even though student_id is correct, we don't display student_id.  Instead we display the
        // fullname from the related Students table.  But fullname is a virtual field so we must
        // read the record in order to get the fullname, instead of looking it up in the fixture records.
        $student = $this->students->get($student_id);
        $this->assertEquals($student->fullname, $option->plaintext);
        $unknownSelectCnt--;

        // 4.5 Ensure that there's a select field for itype_id and that it is correctly set
        $option = $this->form->find('select#InteractionItypeId option[selected]', 0);
        $itype_id = $this->interactionsFixture->interaction1Record['itype_id'];
        $this->assertEquals($option->value, $itype_id);

        // Even though itype_id is correct, we don't display itype_id.  Instead we display the title
        // from the related Itypes table. Verify that title is displayed correctly.
        $itype = $this->itypesFixture->get($itype_id);
        $this->assertEquals($itype['title'], $option->plaintext);
        $unknownSelectCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#InteractionsEdit', 0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0, count($links));
    }

    public function testEditPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $interaction_id = $this->interactionsFixture->interaction1Record['id'];
        $this->post('/interactions/edit/' . $interaction_id, $this->interactionsFixture->newInteractionRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/interactions');

        // Now verify what we think just got written
        $query = $this->interactions->find()->where(['id' => $interaction_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $interaction = $this->interactions->get($interaction_id);
        $this->assertEquals($interaction['clazz_id'], $this->interactionsFixture->newInteractionRecord['clazz_id']);
        $this->assertEquals($interaction['student_id'], $this->interactionsFixture->newInteractionRecord['student_id']);
        $this->assertEquals($interaction['itype_id'], $this->interactionsFixture->newInteractionRecord['itype_id']);

    }

    public function testIndexGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/interactions/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#InteractionsIndex', 0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 4. Look for the create new interaction link
        $this->assertEquals(1, count($html->find('a#InteractionAdd')));
        $unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        $this->table = $html->find('table#InteractionsTable', 0);
        $this->assertNotNull($this->table);

        // 6. Ensure that said table's thead element co`ntains the correct
        //    headings, in the correct order, and nothing else.
        $this->thead = $this->table->find('thead', 0);
        $this->thead_ths = $this->thead->find('tr th');

        $this->assertEquals($this->thead_ths[0]->id, 'clazz');
        $this->assertEquals($this->thead_ths[1]->id, 'student');
        $this->assertEquals($this->thead_ths[2]->id, 'itype');
        $this->assertEquals($this->thead_ths[3]->id, 'actions');
        $column_count = count($this->thead_ths);
        $this->assertEquals($column_count, 4); // no other columns

        // 7. Ensure that the tbody section has the same
        //    quantity of rows as the count of interaction records in the fixture.
        $this->tbody = $this->table->find('tbody', 0);
        $this->tbody_rows = $this->tbody->find('tr');
        $this->assertEquals(count($this->tbody_rows), count($this->interactionsFixture->records));

        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->interactionsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($this->tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $this->htmlRow = $values[1];
            $htmlColumns = $this->htmlRow->find('td');

            // 8.0 clazz_nickname. read from Table because we need to compute
            // the 'nickname' virtual field.
            $clazz = $this->clazzes->get($fixtureRecord['clazz_id']);
            $this->assertEquals($clazz->nickname, $htmlColumns[0]->plaintext);

            // 8.1 student_fullname. read from Table because we need to compute
            // the 'fullname' virtual field.
            $student = $this->students->get($fixtureRecord['student_id']);
            $this->assertEquals($student->fullname, $htmlColumns[1]->plaintext);

            // 8.2 itype_id requires finding the related value in the ItypesFixture
            $itype_id = $fixtureRecord['itype_id'];
            $itype = $this->itypesFixture->get($itype_id);
            $this->assertEquals($itype['title'], $htmlColumns[2]->plaintext);

            // 8.2 Now examine the action links
            $this->td = $htmlColumns[3];
            $actionLinks = $this->td->find('a');
            $this->assertEquals('InteractionView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('InteractionEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('InteractionDelete', $actionLinks[2]->name);
            $unknownATag--;

            // 8.9 No other columns
            $this->assertEquals(count($htmlColumns), $column_count);
        }

        // 9. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testViewGET() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/interactions/view/' . $this->interactionsFixture->interaction1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 1.  Look for the table that contains the view fields.
        $this->table = $html->find('table#InteractionViewTable', 0);
        $this->assertNotNull($this->table);

        // 2. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($this->table->find('tr'));

        // 2.1 clazz requires finding the nickname, which is computed by the Clazz Entity.
        $field = $html->find('tr#clazz td', 0);
        $clazz = $this->clazzes->get($this->interactionsFixture->interaction1Record['clazz_id']);
        $this->assertEquals($clazz->nickname, $field->plaintext);
        $unknownRowCnt--;

        // 2.2 student requires finding the nickname, which is computed by the Semester Entity.
        $field = $html->find('tr#student td', 0);
        $student = $this->students->get($this->interactionsFixture->interaction1Record['student_id']);
        $this->assertEquals($student->fullname, $field->plaintext);
        $unknownRowCnt--;

        // 2.3 itype_id requires finding the related value in the ItypesFixture
        $field = $html->find('tr#itype td', 0);
        $itype_id = $this->interactionsFixture->interaction1Record['itype_id'];
        $itype = $this->itypesFixture->get($itype_id);
        $this->assertEquals($itype['title'], $field->plaintext);
        $unknownRowCnt--;

        // Have all the rows been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 3. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#InteractionsView', 0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0, count($links));
    }
}
