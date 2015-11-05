<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\SemestersFixture;
use Cake\ORM\TableRegistry;

class SemestersControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.semesters'
    ];

    public function setUp() {
        $this->semesters = TableRegistry::get('Semesters');
        $this->semestersFixture = new SemestersFixture();
    }

    public function testAddGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin();
        $this->get('/semesters/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#SemesterAddForm',0);
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

        // 4.3 Ensure that there's an input field for year, of type text, and that it is empty
        $input = $form->find('input#SemesterYear',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // 4.4 Ensure that there's an input field for seq, of type text, and that it is empty
        $input = $form->find('input#SemesterSeq',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // 4.5 Ensure that there's an input field for firstday, of type text, and that it is empty
        $input = $form->find('input#SemesterFirstday',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#ClazzesAdd',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testAddPOST() {

        $this->fakeLogin();
        $this->post('/semesters/add', $this->semestersFixture->newSemesterRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/semesters' );

        // Now verify what we think just got written
        $new_id = FixtureConstants::semester1_id + 1;
        $query = $this->semesters->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_semester = $this->semesters->get($new_id);
        $this->assertEquals($new_semester['year'],$this->semestersFixture->newSemesterRecord['year']);
        $this->assertEquals($new_semester['seq'],$this->semestersFixture->newSemesterRecord['seq']);
        $this->assertEquals($new_semester['firstday'],$this->semestersFixture->newSemesterRecord['firstday']);
    }

    public function testDeletePOST() {

        $this->fakeLogin();
        $semester_id = $this->semestersFixture->semester1Record['id'];
        $this->post('/semesters/delete/' . $semester_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/semesters' );

        // Now verify that the record no longer exists
        $query = $this->semesters->find()->where(['id' => $semester_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin();
        $this->get('/semesters/edit/' . $this->semestersFixture->semester1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#SemesterEditForm',0);
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
        if($this->lookForHiddenInput($form,'PUT')) $unknownInputCnt--;

        // 4.3 Ensure that there's an input field for year, of type text, and that it is correctly set
        $input = $form->find('input#SemesterYear',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $this->semestersFixture->semester1Record['year']);
        $unknownInputCnt--;

        // 4.4 Ensure that there's an input field for seq, of type text, and that it is correctly set
        $input = $form->find('input#SemesterSeq',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value,  $this->semestersFixture->semester1Record['seq']);
        $unknownInputCnt--;

        // 4.5 Ensure that there's an input field for seq, of type text, and that it is correctly set
        $input = $form->find('input#SemesterFirstday',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value,  $this->semestersFixture->semester1Record['firstday']);
        $unknownInputCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#SemestersEdit',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testEditPOST() {

        $this->fakeLogin();
        $semester_id = $this->semestersFixture->semester1Record['id'];
        $this->put('/semesters/edit/' . $semester_id, $this->semestersFixture->newSemesterRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/semesters');

        // Now verify what we think just got written
        $query = $this->semesters->find()->where(['id' => $semester_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $semester = $this->semesters->get($semester_id);
        $this->assertEquals($semester['year'],$this->semestersFixture->newSemesterRecord['year']);
        $this->assertEquals($semester['seq'],$this->semestersFixture->newSemesterRecord['seq']);
        $this->assertEquals($semester['firstday'],$this->semestersFixture->newSemesterRecord['firstday']);
    }

    public function testIndexGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin();
        $this->get('/semesters/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());
        
        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $content = $html->find('div#SemestersIndex',0);
        $this->assertNotNull($content);
        $unknownATag = count($content->find('a'));

        // 4. Look for the create new semester link
        $this->assertEquals(1, count($html->find('a#SemesterAdd')));
        $unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        $semesters_table = $html->find('table#SemestersTable',0);
        $this->assertNotNull($semesters_table);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $semesters_table->find('thead',0);
        $thead_ths = $thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'year');
        $this->assertEquals($thead_ths[1]->id, 'seq');
        $this->assertEquals($thead_ths[2]->id, 'firstday');
        $this->assertEquals($thead_ths[3]->id, 'actions');
        $this->assertEquals(count($thead_ths),4); // no other columns

        // 7. Ensure that the tbody section has the same
        //    quantity of rows as the count of semester records in the fixture.
        $tbody = $semesters_table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->semestersFixture));

        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->semestersFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');
            $this->assertEquals($fixtureRecord['year'],  $htmlColumns[0]->plaintext);
            $this->assertEquals($fixtureRecord['seq'],  $htmlColumns[1]->plaintext);
            $this->assertEquals($fixtureRecord['firstday'],  $htmlColumns[2]->plaintext);

            // Now examine the action links
            $actionLinks = $htmlRow->find('a');
            $this->assertEquals('SemesterView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('SemesterEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('SemesterDelete', $actionLinks[2]->name);
            $unknownATag--;
        }
        // 9. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testViewGET() {

        $this->fakeLogin();
        $this->get('/semesters/view/' . $this->semestersFixture->semester1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 1.  Look for the table that contains the view fields.
        $table = $html->find('table#SemesterViewTable',0);
        $this->assertNotNull($table);

        // 2. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($table->find('tr'));

        $field = $html->find('tr#year td',0);
        $this->assertEquals($this->semestersFixture->semester1Record['year'], $field->plaintext);
        $unknownRowCnt--;

        $field = $html->find('tr#seq td',0);
        $this->assertEquals($this->semestersFixture->semester1Record['seq'], $field->plaintext);
        $unknownRowCnt--;

        $field = $html->find('tr#firstday td',0);
        $this->assertEquals($this->semestersFixture->semester1Record['firstday'], $field->plaintext);
        $unknownRowCnt--;

        // Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 3. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#SemestersView',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

}
