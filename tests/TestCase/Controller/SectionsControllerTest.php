<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\ClazzesFixture;
use App\Test\Fixture\SectionsFixture;
use App\Test\Fixture\SemestersFixture;
use App\Test\Fixture\SubjectsFixture;
use App\Test\Fixture\TplansFixture;
use Cake\ORM\TableRegistry;

class SectionsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.clazzes',
        'app.cohorts',
        'app.majors',
        'app.roles',
        'app.roles_users',
        'app.sections',
        'app.semesters',
        'app.subjects',
        'app.tplans',
        'app.users'
    ];

    /* @var \App\Model\Table\ClazzesTable */
    private $clazzes;

    /* @var \App\Model\Table\CohortsTable */
    private $cohorts;

    /* @var \App\Model\Table\SectionsTable */
    private $sections;

    /* @var \App\Model\Table\SemestersTable */
    private $semesters;

    /* @var \App\Test\Fixture\ClazzesFixture */
    private $clazzesFixture;

    /* @var \App\Test\Fixture\SectionsFixture */
    private $sectionsFixture;

    /* @var \App\Test\Fixture\SemestersFixture */
    private $semestersFixture;

    /* @var \App\Test\Fixture\SubjectsFixture */
    private $subjectsFixture;

    /* @var \App\Test\Fixture\TplansFixture */
    private $tplansFixture;

    public function setUp() {
        parent::setUp();
        $this->clazzes = TableRegistry::get('Clazzes');
        $this->cohorts = TableRegistry::get('Cohorts');
        $this->sections = TableRegistry::get('Sections');
        $this->semesters = TableRegistry::get('Semesters');
        $this->clazzesFixture = new ClazzesFixture();
        $this->sectionsFixture = new SectionsFixture();
        $this->semestersFixture = new SemestersFixture();
        $this->subjectsFixture = new SubjectsFixture();
        $this->tplansFixture = new TplansFixture();
    }

    // Test that unauthenticated users, when submitting a request to
    // an action, will get redirected to the login url.
    public function testUnauthenticatedActionsAndUsers() {
        $this->tstUnauthenticatedActionsAndUsers('sections');
    }

    // Test that users who do not have correct roles, when submitting a request to
    // an action, will get redirected to the home page.
    public function testUnauthorizedActionsAndUsers() {
        $this->tstUnauthorizedActionsAndUsers('sections');
    }

    public function testAddGET() {

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,'/sections/add');

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#SectionAddForm',0);
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

        // 3.3 Ensure that there's a select field for cohort_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($form, 'SectionCohortId', 'cohorts')) $unknownSelectCnt--;

        // 3.4 Ensure that there's a select field for subject_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($form, 'SectionSubjectId', 'subjects')) $unknownSelectCnt--;

        // 3.5 Ensure that there's a select field for semester_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($form, 'SectionSemesterId', 'semesters')) $unknownSelectCnt--;

        // 3.6 Ensure that there's a select field for tplan_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($form, 'SectionTplanId', 'tplans')) $unknownSelectCnt--;

        // 3.7 Ensure that there's an input field for weekday, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#SectionWeekday')) $unknownInputCnt--;

        // 3.8 Ensure that there's an input field for start_time, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#SectionStartTime')) $unknownInputCnt--;

        // 3.9 Ensure that there's an input field for thours, of type text, and that it is empty
        if($this->inputCheckerA($form,'input#SectionTHours')) $unknownInputCnt--;

        // 4. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#SectionsAdd');
    }

    public function testAddPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->sectionsFixture->newSectionRecord;
        $fromDbRecord=$this->genericAddPostProlog(
            FixtureConstants::userAndyAdminId,
            '/sections/add', $fixtureRecord,
            '/sections', $this->sections
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['cohort_id'],$fixtureRecord['cohort_id']);
        $this->assertEquals($fromDbRecord['subject_id'],$fixtureRecord['subject_id']);
        $this->assertEquals($fromDbRecord['semester_id'],$fixtureRecord['semester_id']);
        $this->assertEquals($fromDbRecord['tplan_id'],$fixtureRecord['tplan_id']);
        $this->assertEquals($fromDbRecord['weekday'],$fixtureRecord['weekday']);
        $this->assertEquals($fromDbRecord['start_time'],$fixtureRecord['start_time']);
        $this->assertEquals($fromDbRecord['thours'],$fixtureRecord['thours']);
    }

    public function testDeletePOST() {

        $section_id = $this->sectionsFixture->records[0]['id'];
        $this->deletePOST(
            FixtureConstants::userAndyAdminId, '/sections/delete/',
            $section_id, '/sections', $this->sections
        );
    }

    public function testEditGET() {

        // 1. Obtain a record to edit, login, GET the url, parse the response and send it back.
        $record2Edit=$this->sectionsFixture->records[0];
        $url='/sections/edit/' . $record2Edit['id'];
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,$url);

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#SectionEditForm',0);
        $this->assertNotNull($form);

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#SectionsEdit',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 4. Look for the create new clazz link
        $this->assertEquals('/clazzes/add?section_id='.$record2Edit['id'],$html->find('a#ClazzAdd')[0]->href);
        $unknownATag--;

        // 5. Ensure that the correct form exists
        $form = $html->find('form#SectionEditForm',0);
        $this->assertNotNull($form);

        // 6. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // 6.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 6.2 Look for the hidden POST input
        if($this->lookForHiddenInput($form,'_method','PUT')) $unknownInputCnt--;

        // 6.3 Ensure that there's a select field for cohort_id and that it is correctly set
        // $cohort_id / $cohort['nickname'], from table
        $cohort_id = $record2Edit['cohort_id'];
        $cohort = $this->cohorts->get($cohort_id,['contain' => ['Majors']]);
        if($this->inputCheckerB($form,'select#SectionCohortId option[selected]',$cohort_id,$cohort['nickname']))
            $unknownSelectCnt--;

        // 6.4. Ensure that there's a select field for subject_id and that it is correctly set
        // $subject_id / $subject['title'], from fixture
        $subject_id=$record2Edit['subject_id'];
        $subject = $this->subjectsFixture->get($subject_id);
        if($this->inputCheckerB($form,'select#SectionSubjectId option[selected]',$subject_id,$subject['title']))
            $unknownSelectCnt--;

        // 6.5. Ensure that there's a select field for semester_id and that it is correctly set
        // semester_id / $semester['nickname'], from table
        $semester_id = $record2Edit['semester_id'];
        $semester = $this->semesters->get($semester_id);
        if($this->inputCheckerB($form,'select#SectionSemesterId option[selected]',$semester_id,$semester['nickname']))
            $unknownSelectCnt--;
        
        // 6.6. Ensure that there's a select field for tplan_id and that it is correctly set
        // $tplan_id / $tplan['title'], from fixture
        $tplan_id=$record2Edit['tplan_id'];
        $tplan = $this->tplansFixture->get($tplan_id);
        if($this->inputCheckerB($form,'select#SectionTplanId option[selected]',$tplan_id,$tplan['title']))
            $unknownSelectCnt--;

        // 6.7 Ensure that there's a field for weekday, of type text, and that it is correctly set
        if($this->inputCheckerA($form,'input#SectionWeekday',
            $record2Edit['weekday'])) $unknownInputCnt--;

        // 6.8 Ensure that there's a field for start_time, of type text, and that it is correctly set
        if($this->inputCheckerA($form,'input#SectionStartTime',
            $record2Edit['start_time'])) $unknownInputCnt--;

        // 6.9 Ensure that there's a field for weekday, of type text, and that it is correctly set
        if($this->inputCheckerA($form,'input#SectionTHours',
            $record2Edit['thours'])) $unknownInputCnt--;

        // 6.10 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 7. Examine the table of TplanElements.
        $cct=new ClazzesControllerTest();
        /* @var \simple_html_dom_node $html */
        $unknownATag-=$cct->tstClazzesTable($html,$this->clazzes,$this->clazzesFixture,$this->sections,$record2Edit['id']);

        // 8. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testEditPOST() {

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        $fixtureRecord=$this->sectionsFixture->newSectionRecord;
        $fromDbRecord=$this->genericEditPutProlog(
            FixtureConstants::userAndyAdminId,
            '/sections/edit', $fixtureRecord,
            '/sections', $this->sections
        );

        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['cohort_id'],$fixtureRecord['cohort_id']);
        $this->assertEquals($fromDbRecord['semester_id'],$fixtureRecord['semester_id']);
        $this->assertEquals($fromDbRecord['subject_id'],$fixtureRecord['subject_id']);
        $this->assertEquals($fromDbRecord['weekday'],$fixtureRecord['weekday']);
        $this->assertEquals($fromDbRecord['start_time'],$fixtureRecord['start_time']);
        $this->assertEquals($fromDbRecord['thours'],$fixtureRecord['thours']);
    }

    public function testIndexGET() {

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,'/sections/index');

        // 2. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#SectionsIndex',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 3. Look for the create new section link
        $this->assertEquals(1, count($html->find('a#SectionAdd')));
        $unknownATag--;

        // 4. Ensure that there is a suitably named table to display the results.
        $this->table = $html->find('table#SectionsTable',0);
        $this->assertNotNull($this->table);

        // 5. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $this->thead = $this->table->find('thead',0);
        $thead_ths = $this->thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'cohort');
        $this->assertEquals($thead_ths[1]->id, 'subject');
        $this->assertEquals($thead_ths[2]->id, 'semester');
        $this->assertEquals($thead_ths[3]->id, 'tplan');
        $this->assertEquals($thead_ths[4]->id, 'weekday');
        $this->assertEquals($thead_ths[5]->id, 'start_time');
        $this->assertEquals($thead_ths[6]->id, 'thours');
        $this->assertEquals($thead_ths[7]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,8); // no other columns

        // 6. Ensure that the tbody section has the same
        //    quantity of rows as the count of section records in the fixture.
        $this->tbody = $this->table->find('tbody',0);
        $this->tbody_rows = $this->tbody->find('tr');
        $this->assertEquals(count($this->tbody_rows), count($this->sectionsFixture->records));

        // 7. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->sectionsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($this->tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $this->htmlRow = $values[1];
            $htmlColumns = $this->htmlRow->find('td');

            // 7.0 cohort_nickname. read from Table because we need to compute
            // the 'nickname' virtual field.
            $cohort = $this->cohorts->get($fixtureRecord['cohort_id'],['contain' => ['Majors']]);
            $this->assertEquals($cohort->nickname, $htmlColumns[0]->plaintext);

            // 7.1 subject. all info is available via fixture.
            $subject = $this->subjectsFixture->get($fixtureRecord['subject_id']);
            $this->assertEquals($subject['title'], $htmlColumns[1]->plaintext);

            // 7.2 semester_nickname. read from Table because we need to compute
            // the 'nickname' virtual field.
            $semester = $this->semesters->get($fixtureRecord['semester_id']);
            $this->assertEquals($semester->nickname, $htmlColumns[2]->plaintext);

            // 7.3 tplan. all info is available via fixture.
            $tplan = $this->tplansFixture->get($fixtureRecord['tplan_id']);
            $this->assertEquals($tplan['title'], $htmlColumns[3]->plaintext);

            // 7.4 weekday
            $this->assertEquals($fixtureRecord['weekday'], $htmlColumns[4]->plaintext);

            // 7.5 start_time
            $this->assertEquals($fixtureRecord['start_time'], $htmlColumns[5]->plaintext);

            // 7.6 thours
            $this->assertEquals($fixtureRecord['thours'], $htmlColumns[6]->plaintext);

            // 7.7 Now examine the action links
            $this->td = $htmlColumns[7];
            $actionLinks = $this->td->find('a');
            $this->assertEquals('SectionClazzes', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('SectionView', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('SectionEdit', $actionLinks[2]->name);
            $unknownATag--;
            $this->assertEquals('SectionDelete', $actionLinks[3]->name);
            $unknownATag--;

            // 7.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 8. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testViewGET() {

        // 1. Obtain a record to view, login, GET the url, parse the response and send it back.
        $record2View=$this->sectionsFixture->records[0];
        $url='/sections/view/' . $record2View['id'];
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,$url);

        // 2. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#SectionsView',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 3.  Look for the table that contains the view fields.
        $this->table = $html->find('table#SectionViewTable',0);
        $this->assertNotNull($this->table);

        // 4. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($this->table->find('tr'));

        // 4.1 cohort requires finding the nickname, which is computed by the Cohort Entity.
        $field = $html->find('tr#cohort td',0);
        $cohort = $this->cohorts->get($record2View['cohort_id'], ['contain' => ['Majors']]);
        $this->assertEquals($cohort->nickname, $field->plaintext);
        $unknownRowCnt--;

        // 4.2 subject requires finding the related value in the SubjectsFixture
        $field = $html->find('tr#subject td',0);
        $subject_id = $record2View['subject_id'];
        $subject = $this->subjectsFixture->get($subject_id);
        $this->assertEquals($subject['title'], $field->plaintext);
        $unknownRowCnt--;

        // 4.3 semester requires finding the nickname, which is computed by the Semester Entity.
        $field = $html->find('tr#semester td',0);
        $semester_id = $record2View['semester_id'];
        $semester = $this->semesters->get($semester_id);
        $this->assertEquals($semester->nickname, $field->plaintext);
        $unknownRowCnt--;

        // 4.4 tplan requires finding the related value in the TplansFixture
        $field = $html->find('tr#tplan td',0);
        $tplan_id = $record2View['tplan_id'];
        $tplan = $this->tplansFixture->get($tplan_id);
        $this->assertEquals($tplan['title'], $field->plaintext);
        $unknownRowCnt--;

        // 4.5 weekday
        $field = $html->find('tr#weekday td',0);
        $this->assertEquals($record2View['weekday'], $field->plaintext);
        $unknownRowCnt--;

        // 4.6 start_time
        $field = $html->find('tr#start_time td',0);
        $this->assertEquals($record2View['start_time'], $field->plaintext);
        $unknownRowCnt--;

        // 4.7 thours
        $field = $html->find('tr#thours td',0);
        $this->assertEquals($record2View['thours'], $field->plaintext);
        $unknownRowCnt--;

        // 4.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 5. Examine the table of repeating clazzes
        /* @var \simple_html_dom_node $html */
        $cct=new ClazzesControllerTest();
        $unknownATag-=$cct->tstClazzesTable($html,$this->clazzes,$this->clazzesFixture,$this->sections,$record2View['id']);

        // 6. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }
}
