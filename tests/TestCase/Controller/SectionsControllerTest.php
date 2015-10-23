<?php
namespace App\Test\TestCase\Controller;

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

    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/sections/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure these view vars are set.
        // I'd like to check that cohorts contains majors.  But...
        // doing so has proven to be too complicated and not worth the effort.
        // Just make sure cohorts contains majors.
        $this->assertNotNull($this->viewVariable('cohorts'));
        $this->assertNotNull($this->viewVariable('section'));
        $this->assertNotNull($this->viewVariable('semesters'));
        $this->assertNotNull($this->viewVariable('subjects'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#SectionAddForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // Ensure that there's a select field for cohort_id and that is has no selection
        $option = $form->find('select#SectionCohortId option[selected]',0);
        $this->assertNull($option);

        // Ensure that there's a select field for subject_id and that is has no selection
        $option = $form->find('select#SectionSubjectId option[selected]',0);
        $this->assertNull($option);

        // Ensure that there's a select field for semester_id and that is has no selection
        $option = $form->find('select#SectionSemesterId option[selected]',0);
        $this->assertNull($option);

        // Ensure that there's an input field for weekday, of type text, and that it is empty
        $input = $form->find('input[id=SectionWeekday]')[0];
        $this->assertEquals($input->value, false);

        // Ensure that there's an input field for start_time, of type text, and that it is empty
        $input = $form->find('input[id=SectionStartTime]')[0];
        $this->assertEquals($input->value, false);

        // Ensure that there's an input field for thours, of type text, and that it is empty
        $input = $form->find('input[id=SectionTHours]')[0];
        $this->assertEquals($input->value, false);    }

    public function testAddPOST() {

        $sectionsFixture = new SectionsFixture();

        $this->fakeLogin();
        $this->post('/sections/add', $sectionsFixture->newSectionRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/sections' );

        // Now verify what we think just got written
        $sections = TableRegistry::get('Sections');
        $new_id = FixtureConstants::section1_id + 1;
        $query = $sections->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_section = $sections->get($new_id);
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

        $sectionsFixture = new SectionsFixture();
        $semestersFixture = new SemestersFixture();
        $subjectsFixture = new SubjectsFixture();

        $this->fakeLogin();
        $section_id = $sectionsFixture->section1Record['id'];
        $this->get('/sections/edit/' . $section_id);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure these view vars are set.
        // I'd like to check that cohorts contains majors.  But...
        // doing so has proven to be too complicated and not worth the effort.
        // Just make sure cohorts contains majors.
        $this->assertNotNull($this->viewVariable('cohorts'));
        $this->assertNotNull($this->viewVariable('section'));
        $this->assertNotNull($this->viewVariable('semesters'));
        $this->assertNotNull($this->viewVariable('subjects'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#SectionEditForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // 1. Ensure that there's a field for weekday, of type text, and that it is correctly set
        $input = $form->find('input#SectionWeekday',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $sectionsFixture->section1Record['weekday']);

        // 2. Ensure that there's a field for start_time, of type text, and that it is correctly set
        $input = $form->find('input#SectionStartTime',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $sectionsFixture->section1Record['start_time']);

        // 3. Ensure that there's a field for weekday, of type text, and that it is correctly set
        $input = $form->find('input#SectionTHours',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $sectionsFixture->section1Record['thours']);

        // 4. Ensure that there's a select field for cohort_id and that it is correctly set
        $option = $form->find('select#SectionCohortId option[selected]',0);
        $cohort_id = $sectionsFixture->section1Record['cohort_id'];
        $this->assertEquals($option->value, $cohort_id);

        // Even though cohort_id is correct, we don't display cohort_id.  Instead we display the
        // nickname from the related Cohorts table.  But nickname is a virtual field so we must
        // read the record in order to get the nickname, instead of looking it up in the fixture records.
        $cohorts = TableRegistry::get('Cohorts');
        $cohort = $cohorts->get($cohort_id,['contain' => ['Majors']]);
        $this->assertEquals($cohort->nickname, $option->plaintext);

        // 5. Ensure that there's a select field for subject_id and that it is correctly set
        $option = $form->find('select#SectionSubjectId option[selected]',0);
        $subject_id = $sectionsFixture->section1Record['subject_id'];
        $this->assertEquals($option->value, $subject_id);

        // Even though subject_id is correct, we don't display subject_id.  Instead we display the title
        // from the related Subjects table. Verify that title is displayed correctly.
        $subject = $subjectsFixture->get($subject_id);
        $this->assertEquals($subject['title'], $option->plaintext);

        // 6. Ensure that there's a select field for semester_id and that it is correctly set
        $option = $form->find('select#SectionSemesterId option[selected]',0);
        $semester_id = $sectionsFixture->section1Record['semester_id'];
        $this->assertEquals($option->value, $semester_id);

        // Even though semester_id is correct, we don't display semester_id.  Instead we display the
        // nickname from the related Semesters table.  But nickname is a virtual field so we must
        // read the record in order to get the nickname, instead of looking it up in the fixture records.
        $semesters = TableRegistry::get('Semesters');
        $semester = $semesters->get($semester_id);
        $this->assertEquals($semester->nickname, $option->plaintext);
    }

    public function testEditPOST() {

        $sectionsFixture = new SectionsFixture();

        $this->fakeLogin();
        $section_id = $sectionsFixture->section1Record['id'];
        $this->put('/sections/edit/' . $section_id, $sectionsFixture->newSectionRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/sections');

        // Now verify what we think just got written
        $sections = TableRegistry::get('Sections');
        $query = $sections->find()->where(['id' => $section_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $section = $sections->get($section_id);
        $this->assertEquals($section['cohort_id'],$sectionsFixture->newSectionRecord['cohort_id']);
        $this->assertEquals($section['semester_id'],$sectionsFixture->newSectionRecord['semester_id']);
        $this->assertEquals($section['subject_id'],$sectionsFixture->newSectionRecord['subject_id']);
        $this->assertEquals($section['weekday'],$sectionsFixture->newSectionRecord['weekday']);
        $this->assertEquals($section['start_time'],$sectionsFixture->newSectionRecord['start_time']);
        $this->assertEquals($section['thours'],$sectionsFixture->newSectionRecord['thours']);

    }

    public function testIndexGET() {

        $this->fakeLogin();
        $result = $this->get('/sections/index');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($result);

        // 1. Ensure that the single row of the thead section
        //    has a column for id and title, in that order
        //$rows = $html->find('table[id=sections]',0)->find('thead',0)->find('tr');
        //$row_cnt = count($rows);
        //$this->assertEqual($row_cnt, 1);

        // 2. Ensure that the thead section has a heading
        //    for id, title, is_active, and is_admin.
        //$columns = $rows[0]->find('td');
        //$this->assertEqual($columns[0]->plaintext, 'id');
        //$this->assertEqual($columns[1]->plaintext, 'title');
        //$this->assertEqual($columns[2]->plaintext, 'is_active');
        //$this->assertEqual($columns[3]->plaintext, 'is_admin');

        // 3. Ensure that the tbody section has the same
        //    quantity of rows as the count of section records in the fixture.
        //    For each of these rows, ensure that the id and title match
        //$sectionFixture = new SectionsFixture();
        //$rowsInHTMLTable = $html->find('table[id=sections]',0)->find('tbody',0)->find('tr');
        //$this->assertEqual(count($sectionFixture->records), count($rowsInHTMLTable));
        //$iterator = new MultipleIterator;
        //$iterator->attachIterator(new ArrayIterator($sectionFixture->records));
        //$iterator->attachIterator(new ArrayIterator($rowsInHTMLTable));

        //foreach ($iterator as $values) {
        //$fixtureRecord = $values[0];
        //$htmlRow = $values[1];
        //$htmlColumns = $htmlRow->find('td');
        //$this->assertEqual($fixtureRecord['id'],        $htmlColumns[0]->plaintext);
        //$this->assertEqual($fixtureRecord['title'],  $htmlColumns[1]->plaintext);
        //$this->assertEqual($fixtureRecord['is_active'], $htmlColumns[2]->plaintext);
        //$this->assertEqual($fixtureRecord['is_admin'],  $htmlColumns[3]->plaintext);
        //}
    }

    public function testViewGET() {

        $sectionsFixture = new SectionsFixture();

        $this->fakeLogin();
        $this->get('/sections/view/' . $sectionsFixture->section1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('section'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        //$form = $html->find('form[id=SectionsEditForm]')[0];
        //$this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is correctly set
        //$input = $form->find('input[id=SectionsTitle]')[0];
        //$this->assertEquals($input->value, $sectionsFixture->section1Record['title']);

        // Ensure that there's a field for sdesc, that is empty
        //$input = $form->find('input[id=SectionsSDesc]')[0];
        //$this->assertEquals($input->value, false);
    }

}
