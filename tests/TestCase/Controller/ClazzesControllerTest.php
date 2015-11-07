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

    public function setUp() {
        $this->clazzes = TableRegistry::get('Clazzes');
        $this->sections = TableRegistry::get('Sections');
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
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

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
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/clazzes' );

        // Now verify that the record no longer exists
        $query = $this->clazzes->find()->where(['id' => $clazz_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin();
        $this->get('/clazzes/edit/' . $this->clazzesFixture->clazz1Record['id']);
        $this->assertResponseOk(); // 2xx
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
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 4.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 4.2 Look for the hidden POST input
        if($this->lookForHiddenInput($form,'PUT')) $unknownInputCnt--;

        // 4.3. Ensure that there's a select field for semester_id and that it is correctly set
        $option = $form->find('select#ClazzSectionId option[selected]',0);
        $section_id = $this->clazzesFixture->clazz1Record['section_id'];
        $this->assertEquals($option->value, $section_id);

        // Even though section_id is correct, we don't display section_id.  Instead we display the
        // nickname from the related Sections table.  But nickname is a virtual field so we must
        // read the record in order to get the nickname, instead of looking it up in the fixture records.
        $section = $this->sections->get($section_id);
        $this->assertEquals($section->nickname, $option->plaintext);
        $unknownSelectCnt--;

        // 4.4 Ensure that there's an input field for event_datetime, of type text, and that it is correctly set
        $input = $form->find('input#ClazzDatetime',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $this->clazzesFixture->clazz1Record['event_datetime']);
        $unknownInputCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#ClazzesEdit',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testEditPOST() {

        $this->fakeLogin();
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
        $this->fakeLogin();
        $this->get('/clazzes/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $content = $html->find('div#ClazzesIndex',0);
        $this->assertNotNull($content);
        $unknownATag = count($content->find('a'));

        // 4. Look for the create new clazz link
        $this->assertEquals(1, count($html->find('a#ClazzAdd')));
        $unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        $clazzes_table = $html->find('table#ClazzesTable',0);
        $this->assertNotNull($clazzes_table);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $clazzes_table->find('thead',0);
        $thead_ths = $thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'section');
        $this->assertEquals($thead_ths[1]->id, 'week');
        $this->assertEquals($thead_ths[2]->id, 'event_datetime');
        $this->assertEquals($thead_ths[3]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,4); // no other columns

        // 7. Ensure that the tbody section has the same
        //    quantity of rows as the count of clazz records in the fixture.
        $tbody = $clazzes_table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->clazzesFixture));

        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->clazzesFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');

            // 8.0 section_nickname (virtual field of Section)
            $section = $this->sections->get($fixtureRecord['section_id']);
            $this->assertEquals($section->nickname, $htmlColumns[0]->plaintext);

            // 8.1 week (virtual field of Clazz)
            $clazz = $this->clazzes->get($fixtureRecord['id']);
            $this->assertEquals($clazz->week, $htmlColumns[1]->plaintext);

            // 8.2 event_datetime
            $this->assertEquals($fixtureRecord['event_datetime'], $htmlColumns[2]->plaintext);

            // 8.3 Now examine the action links
            $actionLinks = $htmlColumns[3]->find('a');
            $this->assertEquals('ClazzView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('ClazzEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('ClazzDelete', $actionLinks[2]->name);
            $unknownATag--;

            // 8.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 9. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testViewGET() {

        $this->fakeLogin();
        $this->get('/clazzes/view/' . $this->clazzesFixture->clazz1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 1.  Look for the table that contains the view fields.
        $table = $html->find('table#ClazzViewTable',0);
        $this->assertNotNull($table);

        // 2. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($table->find('tr'));

        // 2.1 section requires finding the nickname, which is computed by the Section Entity.
        $field = $html->find('tr#section td',0);
        $section = $this->sections->get($this->sectionsFixture->section1Record['id']);
        $this->assertEquals($section->nickname, $field->plaintext);
        $unknownRowCnt--;

        // 2.2 event_datetime
        $field = $html->find('tr#event_datetime td',0);
        $this->assertEquals($this->clazzesFixture->clazz1Record['event_datetime'], $field->plaintext);
        $unknownRowCnt--;

        // Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 3. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#ClazzesView',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

}
