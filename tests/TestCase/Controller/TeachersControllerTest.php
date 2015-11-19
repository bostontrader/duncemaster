<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\TeachersFixture;
use Cake\ORM\TableRegistry;

class TeachersControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.roles',
        'app.roles_users',
        'app.teachers',
        'app.users'
    ];

    private $teachers;
    private $teachersFixture;

    public function setUp() {
        parent::setUp();
        $this->teachers = TableRegistry::get('Teachers');
        $this->teachersFixture = new TeachersFixture();
    }

    // Test that unauthenticated users, when submitting a request to
    // an action, will get redirected to the login url.
    public function testUnauthenticatedActionsAndUsers() {
        $this->tstUnauthenticatedActionsAndUsers('teachers');
    }

    // Test that users who do not have correct roles, when submitting a request to
    // an action, will get redirected to the home page.
    public function testUnauthorizedActionsAndUsers() {
        $this->tstUnauthorizedActionsAndUsers('teachers');
    }

    public function testAddGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/teachers/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#TeacherAddForm',0);
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

        // 4.3 Ensure that there's an input field for giv_name, of type text, and that it is empty
        $input = $form->find('input#TeacherGivName',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#TeachersAdd',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testAddPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->post('/teachers/add', $this->teachersFixture->newTeacherRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/teachers' );

        // Now verify what we think just got written
        $new_id = count($this->teachersFixture->records) + 1;
        $query = $this->teachers->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_teacher = $this->teachers->get($new_id);
        $this->assertEquals($new_teacher['giv_name'],$this->teachersFixture->newTeacherRecord['giv_name']);
    }

    public function testDeletePOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $teacher_id = $this->teachersFixture->teacher1Record['id'];
        $this->post('/teachers/delete/' . $teacher_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/teachers' );

        // Now verify that the record no longer exists
        $query = $this->teachers->find()->where(['id' => $teacher_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/teachers/edit/' . $this->teachersFixture->teacher1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#TeacherEditForm',0);
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
        if($this->lookForHiddenInput($form,'_method','PUT')) $unknownInputCnt--;

        // 4.3 Ensure that there's an input field for giv_name, of type text, and that it is correctly set
        $input = $form->find('input#TeacherGivName',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $this->teachersFixture->teacher1Record['giv_name']);
        $unknownInputCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#TeachersEdit',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testEditPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $teacher_id = $this->teachersFixture->teacher1Record['id'];
        $this->put('/teachers/edit/' . $teacher_id, $this->teachersFixture->newTeacherRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/teachers');

        // Now verify what we think just got written
        $query = $this->teachers->find()->where(['id' => $this->teachersFixture->teacher1Record['id']]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $teacher = $this->teachers->get($this->teachersFixture->teacher1Record['id']);
        $this->assertEquals($teacher['giv_name'],$this->teachersFixture->newTeacherRecord['giv_name']);

    }

    public function testIndexGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/teachers/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $content = $html->find('div#TeachersIndex',0);
        $this->assertNotNull($content);
        $unknownATag = count($content->find('a'));

        // 4. Look for the create new subject link
        $this->assertEquals(1, count($html->find('a#TeacherAdd')));
        $unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        $teachers_table = $html->find('table#TeachersTable',0);
        $this->assertNotNull($teachers_table);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $teachers_table->find('thead',0);
        $thead_ths = $thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'giv_name');
        $this->assertEquals($thead_ths[1]->id, 'actions');
        $column_count = count($thead_ths);
        $this->assertEquals($column_count,2); // no other columns

        // 7. Ensure that the tbody section has the same
        //    quantity of rows as the count of subject records in the fixture.
        $tbody = $teachers_table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->teachersFixture->records));
        
        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->teachersFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');

            // 8.0 giv_name
            $this->assertEquals($fixtureRecord['giv_name'],  $htmlColumns[0]->plaintext);

            // 8.1 Now examine the action links
            $actionLinks = $htmlColumns[1]->find('a');
            $this->assertEquals('TeacherView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('TeacherEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('TeacherDelete', $actionLinks[2]->name);
            $unknownATag--;

            // 8.9 No other columns
            $this->assertEquals(count($htmlColumns),$column_count);
        }

        // 9. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    public function testViewGET() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/teachers/view/' . $this->teachersFixture->teacher1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 1.  Look for the table that contains the view fields.
        $table = $html->find('table#TeacherViewTable',0);
        $this->assertNotNull($table);

        // 2. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.
        //
        //  The actual order that the fields are listed is hereby deemed unimportant.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($table->find('tr'));

        // 2.1 giv_name
        $field = $table->find('tr#giv_name td',0);
        $this->assertEquals($this->teachersFixture->teacher1Record['giv_name'], $field->plaintext);
        $unknownRowCnt--;

        // 2.9 Have all the rows been accounted for?  Are there any extras?
        $this->assertEquals(0, $unknownRowCnt);

        // 3. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#TeachersView',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

}
