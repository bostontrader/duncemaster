<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\CohortsFixture;
use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\SectionsFixture;
use App\Test\Fixture\SemestersFixture;
use App\Test\Fixture\SubjectsFixture;
use Cake\ORM\TableRegistry;

class SectionsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.cohorts',
        'app.majors',
        'app.sections',
        'app.semesters',
        'app.subjects'
    ];

    public $sections;
    public $sectionsFixture;
    public $semestersFixture;
    public $subjectsFixture;

    public function setUp() {
        $this->sections = TableRegistry::get('Sections');
        $this->sectionsFixture = new SectionsFixture();
        $this->semestersFixture = new SemestersFixture();
        $this->subjectsFixture = new SubjectsFixture();
    }


    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/sections/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#SectionAddForm', 0);
        $this->assertNotNull($form);

        // 1. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order the fields are listed is hereby deemed unimportant.

        // These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 1.1 Look for the hidden POST input
        if($this->lookForHiddenInput($form)) $unknownInputCnt--;

        // 1.2 Ensure that there's a select field for cohort_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->lookForSelect($form,'SectionCohortId','cohorts')) $unknownSelectCnt--;

        // 1.3 Ensure that there's a select field for subject_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->lookForSelect($form,'SectionSubjectId','subjects')) $unknownSelectCnt--;

        // 1.4 Ensure that there's a select field for semester_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->lookForSelect($form,'SectionSemesterId','semesters')) $unknownSelectCnt--;

        // 1.5 Ensure that there's an input field for weekday, of type text, and that it is empty
        $input = $form->find('input[id=SectionWeekday]')[0];
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // 1.6 Ensure that there's an input field for start_time, of type text, and that it is empty
        $input = $form->find('input[id=SectionStartTime]')[0];
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // 1.7 Ensure that there's an input field for thours, of type text, and that it is empty
        $input = $form->find('input[id=SectionTHours]')[0];
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 2. Examine the links on this page.  There should be zero links.
        $links = $form->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testAddPOST() {

        $sectionsFixture = new SectionsFixture();

        $this->fakeLogin();
        $this->post('/sections/add', $sectionsFixture->newSectionRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/sections' );

        // Now verify what we think just got written
        $new_id = FixtureConstants::section1_id + 1;
        $query = $this->sections->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_section = $this->sections->get($new_id);
        $this->assertEquals($new_section['cohort_id'],$sectionsFixture->newSectionRecord['cohort_id']);
        $this->assertEquals($new_section['subject_id'],$sectionsFixture->newSectionRecord['subject_id']);
        $this->assertEquals($new_section['semester_id'],$sectionsFixture->newSectionRecord['semester_id']);

        $this->assertEquals($new_section['weekday'],$sectionsFixture->newSectionRecord['weekday']);
        $this->assertEquals($new_section['start_time'],$sectionsFixture->newSectionRecord['start_time']);
        $this->assertEquals($new_section['thours'],$sectionsFixture->newSectionRecord['thours']);
    }

    public function testDeletePOST() {

        $sectionsFixture = new SectionsFixture();

        $this->fakeLogin();
        $section_id = $sectionsFixture->section1Record['id'];
        $this->post('/sections/delete/' . $sectionsFixture->section1Record['id']);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/sections' );

        // Now verify that the record no longer exists
        $sections = TableRegistry::get('Sections');
        $query = $sections->find()->where(['id' => $section_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $this->fakeLogin();
        $section_id = $this->sectionsFixture->section1Record['id'];
        $this->get('/sections/edit/' . $section_id);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#SectionEditForm',0);
        $this->assertNotNull($form);

        // 1. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order the fields are listed is hereby deemed unimportant.

        // These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 1.1 Look for the hidden POST input
        if($this->lookForHiddenInput($form,'PUT')) $unknownInputCnt--;

        // 1.2 Ensure that there's a select field for cohort_id and that it is correctly set
        $option = $form->find('select#SectionCohortId option[selected]',0);
        $cohort_id = $this->sectionsFixture->section1Record['cohort_id'];
        $this->assertEquals($option->value, $cohort_id);

        // Even though cohort_id is correct, we don't display cohort_id.  Instead we display the
        // nickname from the related Cohorts table.  But nickname is a virtual field so we must
        // read the record in order to get the nickname, instead of looking it up in the fixture records.
        $cohorts = TableRegistry::get('Cohorts');
        $cohort = $cohorts->get($cohort_id,['contain' => ['Majors']]);
        $this->assertEquals($cohort->nickname, $option->plaintext);
        $unknownSelectCnt--;

        // 1.3. Ensure that there's a select field for subject_id and that it is correctly set
        $option = $form->find('select#SectionSubjectId option[selected]',0);
        $subject_id = $this->sectionsFixture->section1Record['subject_id'];
        $this->assertEquals($option->value, $subject_id);

        // Even though subject_id is correct, we don't display subject_id.  Instead we display the title
        // from the related Subjects table. Verify that title is displayed correctly.
        $subject = $this->subjectsFixture->get($subject_id);
        $this->assertEquals($subject['title'], $option->plaintext);
        $unknownSelectCnt--;

        // 1.4. Ensure that there's a select field for semester_id and that it is correctly set
        $option = $form->find('select#SectionSemesterId option[selected]',0);
        $semester_id = $this->sectionsFixture->section1Record['semester_id'];
        $this->assertEquals($option->value, $semester_id);

        // Even though semester_id is correct, we don't display semester_id.  Instead we display the
        // nickname from the related Semesters table.  But nickname is a virtual field so we must
        // read the record in order to get the nickname, instead of looking it up in the fixture records.
        $semesters = TableRegistry::get('Semesters');
        $semester = $semesters->get($semester_id);
        $this->assertEquals($semester->nickname, $option->plaintext);
        $unknownSelectCnt--;

        // 1.5 Ensure that there's a field for weekday, of type text, and that it is correctly set
        $input = $form->find('input#SectionWeekday',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $this->sectionsFixture->section1Record['weekday']);
        $unknownSelectCnt--;

        // 1.6 Ensure that there's a field for start_time, of type text, and that it is correctly set
        $input = $form->find('input#SectionStartTime',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $this->sectionsFixture->section1Record['start_time']);
        $unknownSelectCnt--;

        // 1.7 Ensure that there's a field for weekday, of type text, and that it is correctly set
        $input = $form->find('input#SectionTHours',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $this->sectionsFixture->section1Record['thours']);
        $unknownSelectCnt--;

        // 2. Examine the links on this page.  There should be zero links.
        $links = $form->find('a');
        $this->assertEquals(0,count($links));

    }

    public function testEditPOST() {

        $this->fakeLogin();
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

        $subjectsFixture = new SubjectsFixture();

        $this->fakeLogin();
        $this->get('/sections/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure these view vars are set.
        // I'd like to check that cohorts contains majors.  But...
        // doing so has proven to be too complicated and not worth the effort.
        // Just make sure cohorts contains majors.
        //$this->assertNotNull($this->viewVariable('cohorts'));
        $this->assertNotNull($this->viewVariable('sections'));
        //$this->assertNotNull($this->viewVariable('semesters'));
        //$this->assertNotNull($this->viewVariable('subjects'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // How shall we test the index?

        // 1. Ensure that there is a suitably named table to display the results.
        $sections_table = $html->find('table#sections',0);
        $this->assertNotNull($sections_table);

        // 2. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $sections_table->find('thead',0);
        $thead_ths = $thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'id');
        $this->assertEquals($thead_ths[1]->id, 'cohort');
        $this->assertEquals($thead_ths[2]->id, 'subject');
        $this->assertEquals($thead_ths[3]->id, 'semester');
        $this->assertEquals($thead_ths[4]->id, 'weekday');
        $this->assertEquals($thead_ths[5]->id, 'start_time');
        $this->assertEquals($thead_ths[6]->id, 'thours');
        $this->assertEquals($thead_ths[7]->id, 'actions');
        $this->assertEquals(count($thead_ths),8); // no other columns

        // 3. Ensure that the tbody section has the same
        //    quantity of rows as the count of section records in the fixture.
        $sectionsFixture = new SectionsFixture();
        //$studentsFixture = new StudentsFixture();
        $tbody = $sections_table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($sectionsFixture));

        // 4. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $sections = TableRegistry::get('Sections');
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($sectionsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');
            $this->assertEquals($fixtureRecord['id'], $htmlColumns[0]->plaintext);

            // cohort_nickname
            $cohorts = TableRegistry::get('Cohorts');
            $cohort = $cohorts->get($fixtureRecord['cohort_id'],['contain' => ['Majors']]);
            $this->assertEquals($cohort->nickname, $htmlColumns[1]->plaintext);

            // subject
            $subject = $subjectsFixture->get($fixtureRecord['subject_id']);
            $this->assertEquals($subject['title'], $htmlColumns[2]->plaintext);

            // semester_nickname
            $semesters = TableRegistry::get('Semesters');
            $semester = $semesters->get($fixtureRecord['semester_id']);
            $this->assertEquals($semester->nickname, $htmlColumns[3]->plaintext);

            $this->assertEquals($fixtureRecord['weekday'], $htmlColumns[4]->plaintext);
            $this->assertEquals($fixtureRecord['start_time'], $htmlColumns[5]->plaintext);
            $this->assertEquals($fixtureRecord['thours'], $htmlColumns[6]->plaintext);

            // Ignore the action links
        }
    }

    public function testViewGET() {

        $this->fakeLogin();
        $this->get('/sections/view/' . $this->sectionsFixture->section1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('section'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 1. How shall we test the view?  It doesn't have any enclosing table or structure so just
        // ignore that part.  Instead, look for individual display fields.
        $field = $html->find('td#id',0);
        $this->assertEquals($this->sectionsFixture->section1Record['id'], $field->plaintext);

        $field = $html->find('td#weekday',0);
        $this->assertEquals($this->sectionsFixture->section1Record['weekday'], $field->plaintext);

        $field = $html->find('td#start_time',0);
        $this->assertEquals($this->sectionsFixture->section1Record['start_time'], $field->plaintext);

        $field = $html->find('td#thours',0);
        $this->assertEquals($this->sectionsFixture->section1Record['thours'], $field->plaintext);

        // subject requires finding the related value in the SubjectsFixture
        $field = $html->find('td#subject',0);
        $subjectsFixture = new SubjectsFixture();
        $subject_id = $this->sectionsFixture->section1Record['subject_id'];
        $subject = $subjectsFixture->get($subject_id);
        $this->assertEquals($subject['title'], $field->plaintext);

        // cohort requires finding the nickname, which is computed by the Cohort Entity.
        $field = $html->find('td#cohort',0);
        $cohorts = TableRegistry::get('Cohorts');
        $cohort = $cohorts->get($this->sectionsFixture->section1Record['id'], ['contain' => ['Majors']]);
        $this->assertEquals($cohort->nickname, $field->plaintext);

        // 2. Examine the links on this page.  There should be zero links.
        $links = $html->find('a');
        $this->assertEquals(0,count($links));

    }

}
