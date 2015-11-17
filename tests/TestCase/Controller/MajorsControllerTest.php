<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\MajorsFixture;
use Cake\ORM\TableRegistry;

class MajorsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.majors',
        'app.roles',
        'app.roles_users',
        'app.users'
    ];

    private $majors;
    private $majorsFixture;

    public function setUp() {
        parent::setUp();
        $this->majors = TableRegistry::get('Majors');
        $this->majorsFixture = new MajorsFixture();
    }

    // Test that users who do not have correct roles, when submitting a request to
    // an action, will get redirected to the login url.
    public function testUnauthorizedActionsAndUsers() {
        $this->tstUnauthorizedActionsAndUsers('majors');
    }

    public function testAddGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/majors/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#MajorAddForm',0);
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

        // 4.3 Ensure that there's an input field for title, of type text, and that it is empty
        $input = $form->find('input#MajorTitle',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // 4.4 Ensure that there's an input field for sdesc, of type text, and that it is empty
        $input = $form->find('input#MajorSDesc',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#MajorsAdd',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testAddPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->post('/majors/add', $this->majorsFixture->newMajorRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/majors' );

        // Now verify what we think just got written
        $new_id = FixtureConstants::major1_id + 1;
        $query = $this->majors->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_major = $this->majors->get($new_id);
        $this->assertEquals($new_major['title'],$this->majorsFixture->newMajorRecord['title']);
        $this->assertEquals($new_major['sdesc'],$this->majorsFixture->newMajorRecord['sdesc']);
    }

    public function testDeletePOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $major_id = $this->majorsFixture->major1Record['id'];
        $this->post('/majors/delete/' . $major_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/majors' );

        // Now verify that the record no longer exists
        $query = $this->majors->find()->where(['id' => $major_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/majors/edit/' . $this->majorsFixture->major1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#MajorEditForm',0);
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
        if($this->lookForHiddenInput($form,'_method','PUT')) $unknownInputCnt--;

        // 4.3 Ensure that there's an input field for title, of type text, and that it is correctly set
        $input = $form->find('input#MajorTitle',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $this->majorsFixture->major1Record['title']);
        $unknownInputCnt--;

        // 4.4 Ensure that there's an input field for sdesc, of type text, and that that it is correctly set
        $input = $form->find('input#MajorSDesc',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value,  $this->majorsFixture->major1Record['sdesc']);
        $unknownInputCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#MajorsEdit',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));    }

    public function testEditPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $major_id = $this->majorsFixture->major1Record['id'];
        $this->put('/majors/edit/' . $major_id, $this->majorsFixture->newMajorRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/majors');

        // Now verify what we think just got written
        $query = $this->majors->find()->where(['id' => $major_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $major = $this->majors->get($major_id);
        $this->assertEquals($major['title'],$this->majorsFixture->newMajorRecord['title']);
        $this->assertEquals($major['sdesc'],$this->majorsFixture->newMajorRecord['sdesc']);
    }

    public function testIndexGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/majors/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $content = $html->find('div#MajorsIndex',0);
        $this->assertNotNull($content);
        $unknownATag = count($content->find('a'));

        // 4. Look for the create new major link
        $this->assertEquals(1, count($html->find('a#MajorAdd')));
        $unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        $majors_table = $html->find('table#majors',0);
        $this->assertNotNull($majors_table);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $majors_table->find('thead',0);
        $thead_ths = $thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'title');
        $this->assertEquals($thead_ths[1]->id, 'sdesc');
        $this->assertEquals($thead_ths[2]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,3); // no other columns

        // 7. Ensure that the tbody section has the same
        //    quantity of rows as the count of major records in the fixture.
        $tbody = $majors_table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->majorsFixture));

        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->majorsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');

            $this->assertEquals($fixtureRecord['title'],  $htmlColumns[0]->plaintext);
            $this->assertEquals($fixtureRecord['sdesc'],  $htmlColumns[1]->plaintext);

            // 8.2 Now examine the action links
            $actionLinks = $htmlRow->find('a');
            $this->assertEquals('MajorView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('MajorEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('MajorDelete', $actionLinks[2]->name);
            $unknownATag--;

            // 8.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 9. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testViewGET() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/majors/view/' . $this->majorsFixture->major1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 1.  Look for the table that contains the view fields.
        $table = $html->find('table#MajorViewTable',0);
        $this->assertNotNull($table);

        // 2. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($table->find('tr'));

        // 2.1 title
        $field = $html->find('tr#title td',0);
        $this->assertEquals($this->majorsFixture->major1Record['title'], $field->plaintext);
        $unknownRowCnt--;

        // 2.2 sdesc
        $field = $html->find('tr#sdesc td',0);
        $this->assertEquals($this->majorsFixture->major1Record['sdesc'], $field->plaintext);
        $unknownRowCnt--;

        // Have all the rows been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 3. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#MajorsView',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

}
