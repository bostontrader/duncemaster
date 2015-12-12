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

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/sections/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $this->form = $html->find('form#SectionAddForm', 0);
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

        // 4.3 Ensure that there's a select field for cohort_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($this->form, 'SectionCohortId', 'cohorts')) $unknownSelectCnt--;

        // 4.4 Ensure that there's a select field for subject_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($this->form, 'SectionSubjectId', 'subjects')) $unknownSelectCnt--;

        // 4.5 Ensure that there's a select field for semester_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($this->form, 'SectionSemesterId', 'semesters')) $unknownSelectCnt--;

        // 4.6 Ensure that there's a select field for tplan_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->selectCheckerA($this->form, 'SectionTplanId', 'tplans')) $unknownSelectCnt--;

        // 4.7 Ensure that there's an input field for weekday, of type text, and that it is empty
        if($this->inputCheckerA($this->form,'input#SectionWeekday')) $unknownInputCnt--;

        // 4.8 Ensure that there's an input field for start_time, of type text, and that it is empty
        if($this->inputCheckerA($this->form,'input#SectionStartTime')) $unknownInputCnt--;

        // 4.9 Ensure that there's an input field for thours, of type text, and that it is empty
        if($this->inputCheckerA($this->form,'input#SectionTHours')) $unknownInputCnt--;

        // 4.10 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $this->content = $html->find('div#SectionsAdd',0);
        $this->assertNotNull($this->content);
        $links = $this->content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testAddPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->post('/sections/add', $this->sectionsFixture->newSectionRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/sections' );

        // Now verify what we think just got written
        $new_id = count($this->sectionsFixture->records) + 1;
        $query = $this->sections->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_section = $this->sections->get($new_id);
        $this->assertEquals($new_section['cohort_id'],$this->sectionsFixture->newSectionRecord['cohort_id']);
        $this->assertEquals($new_section['subject_id'],$this->sectionsFixture->newSectionRecord['subject_id']);
        $this->assertEquals($new_section['semester_id'],$this->sectionsFixture->newSectionRecord['semester_id']);
        $this->assertEquals($new_section['tplan_id'],$this->sectionsFixture->newSectionRecord['tplan_id']);
        $this->assertEquals($new_section['weekday'],$this->sectionsFixture->newSectionRecord['weekday']);
        $this->assertEquals($new_section['start_time'],$this->sectionsFixture->newSectionRecord['start_time']);
        $this->assertEquals($new_section['thours'],$this->sectionsFixture->newSectionRecord['thours']);
    }

    public function testDeletePOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $section_id = $this->sectionsFixture->section1Record['id'];
        $this->post('/sections/delete/' . $section_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/sections' );

        // Now verify that the record no longer exists
        $query = $this->sections->find()->where(['id' => $section_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $section_id = $this->sectionsFixture->section1Record['id'];
        $this->get('/sections/edit/' . $section_id);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#SectionsEdit',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 4. Look for the create new clazz link
        $this->assertEquals('/clazzes/add?section_id='.$section_id,$html->find('a#ClazzAdd')[0]->href);
        $unknownATag--;

        // 5. Ensure that the correct form exists
        $this->form = $html->find('form#SectionEditForm',0);
        $this->assertNotNull($this->form);

        // 6. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // 6.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($this->form->find('select'));
        $unknownInputCnt = count($this->form->find('input'));

        // 6.2 Look for the hidden POST input
        if($this->lookForHiddenInput($this->form,'_method','PUT')) $unknownInputCnt--;

        // 6.3 Ensure that there's a select field for cohort_id and that it is correctly set
        $option = $this->form->find('select#SectionCohortId option[selected]',0);
        $cohort_id = $this->sectionsFixture->section1Record['cohort_id'];
        $this->assertEquals($option->value, $cohort_id);

        // Even though cohort_id is correct, we don't display cohort_id.  Instead we display the
        // nickname from the related Cohorts table.  But nickname is a virtual field so we must
        // read the record in order to get the nickname, instead of looking it up in the fixture records.
        $cohort = $this->cohorts->get($cohort_id,['contain' => ['Majors']]);
        $this->assertEquals($cohort->nickname, $option->plaintext);
        $unknownSelectCnt--;

        // 6.4. Ensure that there's a select field for subject_id and that it is correctly set
        $option = $this->form->find('select#SectionSubjectId option[selected]',0);
        $subject_id = $this->sectionsFixture->section1Record['subject_id'];
        $this->assertEquals($option->value, $subject_id);

        // Even though subject_id is correct, we don't display subject_id.  Instead we display the title
        // from the related Subjects table. Verify that title is displayed correctly.
        $subject = $this->subjectsFixture->get($subject_id);
        $this->assertEquals($subject['title'], $option->plaintext);
        $unknownSelectCnt--;

        // 6.5. Ensure that there's a select field for semester_id and that it is correctly set
        $option = $this->form->find('select#SectionSemesterId option[selected]',0);
        $semester_id = $this->sectionsFixture->section1Record['semester_id'];
        $this->assertEquals($option->value, $semester_id);

        // Even though semester_id is correct, we don't display semester_id.  Instead we display the
        // nickname from the related Semesters table.  But nickname is a virtual field so we must
        // read the record in order to get the nickname, instead of looking it up in the fixture records.
        $semester = $this->semesters->get($semester_id);
        $this->assertEquals($semester->nickname, $option->plaintext);
        $unknownSelectCnt--;

        // 6.6. Ensure that there's a select field for tplan_id and that it is correctly set
        $option = $this->form->find('select#SectionTplanId option[selected]',0);
        $tplan_id = $this->sectionsFixture->section1Record['tplan_id'];
        $this->assertEquals($option->value, $tplan_id);

        // Even though tplan_id is correct, we don't display tplan_id.  Instead we display the title
        // from the related Tplans table. Verify that title is displayed correctly.
        $tplan = $this->tplansFixture->get($tplan_id);
        $this->assertEquals($tplan['title'], $option->plaintext);
        $unknownSelectCnt--;

        // 6.7 Ensure that there's a field for weekday, of type text, and that it is correctly set
        $input = $this->form->find('input#SectionWeekday',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $this->sectionsFixture->section1Record['weekday']);
        $unknownInputCnt--;

        // 6.8 Ensure that there's a field for start_time, of type text, and that it is correctly set
        $input = $this->form->find('input#SectionStartTime',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $this->sectionsFixture->section1Record['start_time']);
        $unknownInputCnt--;

        // 6.9 Ensure that there's a field for weekday, of type text, and that it is correctly set
        $input = $this->form->find('input#SectionTHours',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $this->sectionsFixture->section1Record['thours']);
        $unknownInputCnt--;

        // 6.10 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 7. Examine the table of TplanElements.
        $cct=new ClazzesControllerTest();
        /* @var \simple_html_dom_node $html */
        $unknownATag-=$cct->tstClazzesTable($html,$this->clazzes,$this->clazzesFixture,$this->sections,$section_id);

        // 8. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testEditPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $section_id = $this->sectionsFixture->section1Record['id'];
        $this->put('/sections/edit/' . $section_id, $this->sectionsFixture->newSectionRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/sections');

        // Now verify what we think just got written
        $query = $this->sections->find()->where(['id' => $section_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $section = $this->sections->get($section_id);
        $this->assertEquals($section['cohort_id'],$this->sectionsFixture->newSectionRecord['cohort_id']);
        $this->assertEquals($section['semester_id'],$this->sectionsFixture->newSectionRecord['semester_id']);
        $this->assertEquals($section['subject_id'],$this->sectionsFixture->newSectionRecord['subject_id']);
        $this->assertEquals($section['weekday'],$this->sectionsFixture->newSectionRecord['weekday']);
        $this->assertEquals($section['start_time'],$this->sectionsFixture->newSectionRecord['start_time']);
        $this->assertEquals($section['thours'],$this->sectionsFixture->newSectionRecord['thours']);

    }

    public function testIndexGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/sections/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#SectionsIndex',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 4. Look for the create new section link
        $this->assertEquals(1, count($html->find('a#SectionAdd')));
        $unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        $this->table = $html->find('table#SectionsTable',0);
        $this->assertNotNull($this->table);

        // 6. Ensure that said table's thead element contains the correct
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

        // 7. Ensure that the tbody section has the same
        //    quantity of rows as the count of section records in the fixture.
        $this->tbody = $this->table->find('tbody',0);
        $this->tbody_rows = $this->tbody->find('tr');
        $this->assertEquals(count($this->tbody_rows), count($this->sectionsFixture->records));

        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->sectionsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($this->tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $this->htmlRow = $values[1];
            $htmlColumns = $this->htmlRow->find('td');

            // 8.0 cohort_nickname. read from Table because we need to compute
            // the 'nickname' virtual field.
            $cohort = $this->cohorts->get($fixtureRecord['cohort_id'],['contain' => ['Majors']]);
            $this->assertEquals($cohort->nickname, $htmlColumns[0]->plaintext);

            // 8.1 subject. all info is available via fixture.
            $subject = $this->subjectsFixture->get($fixtureRecord['subject_id']);
            $this->assertEquals($subject['title'], $htmlColumns[1]->plaintext);

            // 8.2 semester_nickname. read from Table because we need to compute
            // the 'nickname' virtual field.
            $semester = $this->semesters->get($fixtureRecord['semester_id']);
            $this->assertEquals($semester->nickname, $htmlColumns[2]->plaintext);

            // 8.3 tplan. all info is available via fixture.
            $tplan = $this->tplansFixture->get($fixtureRecord['tplan_id']);
            $this->assertEquals($tplan['title'], $htmlColumns[3]->plaintext);

            // 8.4 weekday
            $this->assertEquals($fixtureRecord['weekday'], $htmlColumns[4]->plaintext);

            // 8.5 start_time
            $this->assertEquals($fixtureRecord['start_time'], $htmlColumns[5]->plaintext);

            // 8.6 thours
            $this->assertEquals($fixtureRecord['thours'], $htmlColumns[6]->plaintext);

            // 8.7 Now examine the action links
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

            // 8.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 9. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testViewGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $fixtureRecord=$this->sectionsFixture->section1Record;
        $section_id=$fixtureRecord['id'];
        $this->get('/sections/view/' . $section_id);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#SectionsView',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 4.  Look for the table that contains the view fields.
        $this->table = $html->find('table#SectionViewTable',0);
        $this->assertNotNull($this->table);

        // 5. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($this->table->find('tr'));

        // 5.1 cohort requires finding the nickname, which is computed by the Cohort Entity.
        $field = $html->find('tr#cohort td',0);
        $cohort = $this->cohorts->get($fixtureRecord['id'], ['contain' => ['Majors']]);
        $this->assertEquals($cohort->nickname, $field->plaintext);
        $unknownRowCnt--;

        // 5.2 subject requires finding the related value in the SubjectsFixture
        $field = $html->find('tr#subject td',0);
        $subject_id = $fixtureRecord['subject_id'];
        $subject = $this->subjectsFixture->get($subject_id);
        $this->assertEquals($subject['title'], $field->plaintext);
        $unknownRowCnt--;

        // 5.3 semester requires finding the nickname, which is computed by the Semester Entity.
        $field = $html->find('tr#semester td',0);
        $semester = $this->semesters->get($fixtureRecord['id']);
        $this->assertEquals($semester->nickname, $field->plaintext);
        $unknownRowCnt--;

        // 5.4 tplan requires finding the related value in the TplansFixture
        $field = $html->find('tr#tplan td',0);
        $tplan_id = $fixtureRecord['tplan_id'];
        $tplan = $this->tplansFixture->get($tplan_id);
        $this->assertEquals($tplan['title'], $field->plaintext);
        $unknownRowCnt--;

        // 5.5 weekday
        $field = $html->find('tr#weekday td',0);
        $this->assertEquals($fixtureRecord['weekday'], $field->plaintext);
        $unknownRowCnt--;

        // 5.6 start_time
        $field = $html->find('tr#start_time td',0);
        $this->assertEquals($fixtureRecord['start_time'], $field->plaintext);
        $unknownRowCnt--;

        // 5.7 thours
        $field = $html->find('tr#thours td',0);
        $this->assertEquals($fixtureRecord['thours'], $field->plaintext);
        $unknownRowCnt--;

        // 5.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 6. Examine the table of repeating clazzes
        /* @var \simple_html_dom_node $html */
        $cct=new ClazzesControllerTest();
        $unknownATag-=$cct->tstClazzesTable($html,$this->clazzes,$this->clazzesFixture,$this->sections,$section_id);

        // 7. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

}
