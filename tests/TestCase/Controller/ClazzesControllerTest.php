<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\ClazzesFixture;
use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\SectionsFixture;
use Cake\ORM\TableRegistry;

class ClazzesControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.clazzes',
        'app.sections'
    ];

    public $clazzes;
    public $clazzesFixture;

    public function setUp() {
        $this->clazzes = TableRegistry::get('Clazzes');
        $this->clazzesFixture = new ClazzesFixture();
        $this->sectionsFixture = new SectionsFixture();
    }

    public function testAddGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin();
        $this->get('/clazzes/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#ClazzAddForm',0);
        $this->assertNotNull($form);

        // 4. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // 4.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 4.2 Look for the hidden POST input
        if($this->lookForHiddenInput($form)) $unknownInputCnt--;

        // 4.3 Ensure that there's a select field for section_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->lookForSelect($form,'ClazzSectionId','sections')) $unknownSelectCnt--;
        
        // 4.4 Ensure that there's an input field for event_datetime, of type text, and that it is empty
        $input = $form->find('input#ClazzDatetime',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // 4.5 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#clazzesAdd',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testAddPOST() {

        $this->fakeLogin();
        $this->post('/clazzes/add', $this->clazzesFixture->newClazzRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/clazzes' );

        // Now verify what we think just got written
        $new_id = FixtureConstants::clazz1_id + 1;
        $query = $this->clazzes->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_clazz = $this->clazzes->get($new_id);
        $this->assertEquals($new_clazz['section_id'],$this->clazzesFixture->newClazzRecord['section_id']);
        $this->assertEquals($new_clazz['event_datetime'],$this->clazzesFixture->newClazzRecord['event_datetime']);
    }

    public function testDeletePOST() {

        $this->fakeLogin();
        $clazz_id = $this->clazzesFixture->clazz1Record['id'];
        $this->post('/clazzes/delete/' . $clazz_id);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/clazzes' );

        // Now verify that the record no longer exists
        $query = $this->clazzes->find()->where(['id' => $clazz_id]);
        $this->assertEquals(0, $query->count());
    }

    /*public function testEditGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin();
        $this->get('/clazzes/edit/' . $clazzesFixture->clazz1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#ClazzEditForm',0);
        $this->assertNotNull($form);

        // 4. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // 4.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 4.2 Look for the hidden POST input
        if($this->lookForHiddenInput($form,'PUT')) $unknownInputCnt--;

        // Omit the id field
        // Ensure that there's a field for section_id, that is correctly set
        $input = $form->find('select#ClazzSectionId',0);
        $s1 = $input->value;
        $s2 = $clazzesFixture->clazz1Record['section_id'];
        //$this->assertEquals($input->value, $clazzesFixture->clazz1Record['section_id']);

        // Ensure that there's a field for datetime, that is correctly set
        //$input = $form->find('input[id=ClazzDatetime]')[0];
        //$this->assertEquals($input->value,  $clazzesFixture->clazz1Record['datetime']);

    }

    public function testEditPOST() {

        $this->fakeLogin();
        $this->post('/clazzes/edit/' . $clazzesFixture->clazz1Record['id'], $clazzesFixture->newClazzRecord);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Now verify what we think just got written
        $clazzes = TableRegistry::get('Clazzs');
        $query = $clazzes->find()->where(['id' => $clazzesFixture->clazz1Record['id']]);
        $c = $query->count();
        $this->assertEquals(1, $c);

        // Now retrieve that 1 record and compare to what we expect
        $clazz = $clazzes->get($clazzesFixture->clazz1Record['id']);
        $this->assertEquals($clazz['section_id'],$clazzesFixture->newClazzRecord['section_id']);
        $this->assertEquals($clazz['datetime'],$clazzesFixture->newClazzRecord['datetime']);

    }*/

    public function testIndexGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin();
        $result = $this->get('/clazzes/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($result);

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $unknownATag = count($html->find('div#sectionsIndex a'));

        // 4. Look for the create new section link
        $this->assertEquals(1, count($html->find('a#sectionAdd')));
        $unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        $sections_table = $html->find('table#sectionsTable',0);
        $this->assertNotNull($sections_table);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $sections_table->find('thead',0);
        $thead_ths = $thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'cohort');
        $this->assertEquals($thead_ths[1]->id, 'subject');
        $this->assertEquals($thead_ths[2]->id, 'semester');
        $this->assertEquals($thead_ths[3]->id, 'weekday');
        $this->assertEquals($thead_ths[4]->id, 'start_time');
        $this->assertEquals($thead_ths[5]->id, 'thours');
        $this->assertEquals($thead_ths[6]->id, 'actions');
        $this->assertEquals(count($thead_ths),7); // no other columns

        // 7. Ensure that the tbody section has the same
        //    quantity of rows as the count of section records in the fixture.
        $tbody = $sections_table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->sectionsFixture));

        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->sectionsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');

            // 8.0 cohort_nickname
            $cohorts = TableRegistry::get('Cohorts');
            $cohort = $cohorts->get($fixtureRecord['cohort_id'],['contain' => ['Majors']]);
            $this->assertEquals($cohort->nickname, $htmlColumns[0]->plaintext);

            // 8.1 subject
            $subject = $this->subjectsFixture->get($fixtureRecord['subject_id']);
            $this->assertEquals($subject['title'], $htmlColumns[1]->plaintext);

            // 8.2 semester_nickname
            $semesters = TableRegistry::get('Semesters');
            $semester = $semesters->get($fixtureRecord['semester_id']);
            $this->assertEquals($semester->nickname, $htmlColumns[2]->plaintext);

            $this->assertEquals($fixtureRecord['weekday'], $htmlColumns[3]->plaintext);
            $this->assertEquals($fixtureRecord['start_time'], $htmlColumns[4]->plaintext);
            $this->assertEquals($fixtureRecord['thours'], $htmlColumns[5]->plaintext);

            // 8.6 Now examine the action links
            $actionLinks = $htmlRow->find('a');
            $this->assertEquals('sectionView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('sectionEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('sectionDelete', $actionLinks[2]->name);
            $unknownATag--;
        }

        // 9. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    /*public function testViewGET() {

        $this->fakeLogin();
        $this->get('/clazzes/view/' . $clazzesFixture->clazz1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('clazz'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        //$form = $html->find('form[id=ClazzEditForm]')[0];
        //$this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is correctly set
        //$input = $form->find('input[id=ClazzTitle]')[0];
        //$this->assertEquals($input->value, $clazzesFixture->clazz1Record['title']);

        // Ensure that there's a field for sdesc, that is empty
        //$input = $form->find('input[id=ClazzSDesc]')[0];
        //$this->assertEquals($input->value, false);
    }*/

}
