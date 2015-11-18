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

    // Test that users who do not have correct roles, when submitting a request to
    // an action, will get redirected to the login url.
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

        // Ensure that there's an input field for giv_name, of type text, and that it is empty
        $input = $form->find('input#TeacherGivName',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
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
        $teachers = TableRegistry::get('Teachers');
        $query = $teachers->find()->where(['id' => $teacher_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $teacher_id = $this->teachersFixture->teacher1Record['id'];
        $this->get('/teachers/edit/' . $teacher_id);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('teacher'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#TeacherEditForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // Ensure that there's an input field for giv_name, of type text, and that it is correctly set
        $input = $form->find('input#TeacherGivName',0);
        $this->assertEquals($input->value, $this->teachersFixture->teacher1Record['giv_name']);
    }

    public function testEditPOST() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $teacher_id = $this->teachersFixture->teacher1Record['id'];
        $this->put('/teachers/edit/' . $teacher_id, $this->teachersFixture->newTeacherRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/teachers');

        // Now verify what we think just got written
        $teachers = TableRegistry::get('Teachers');
        $query = $teachers->find()->where(['id' => $this->teachersFixture->teacher1Record['id']]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $teacher = $teachers->get($this->teachersFixture->teacher1Record['id']);
        $this->assertEquals($teacher['giv_name'],$this->teachersFixture->newTeacherRecord['giv_name']);

    }

    public function testIndexGET() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/teachers/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('teachers'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // How shall we test the index?

        // 1. Ensure that there is a suitably named table to display the results.
        $teachers_table = $html->find('table#teachers',0);
        $this->assertNotNull($teachers_table);

        // 2. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $teachers_table->find('thead',0);
        $thead_ths = $thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'id');
        $this->assertEquals($thead_ths[1]->id, 'giv_name');
        $this->assertEquals($thead_ths[2]->id, 'actions');
        $this->assertEquals(count($thead_ths),3); // no other columns

        // 3. Ensure that the tbody section has the same
        //    quantity of rows as the count of teacher records in the fixture.
        $tbody = $teachers_table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->teachersFixture));

        // 4. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.  In order to do this we'll also need
        //    to read from the Teachers table.
        $teachers = TableRegistry::get('Teachers');
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->teachersFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');
            $this->assertEquals($fixtureRecord['id'], $htmlColumns[0]->plaintext);
            $this->assertEquals($fixtureRecord['giv_name'],  $htmlColumns[1]->plaintext);

            // Ignore the action links
        }
    }

    public function testViewGET() {

        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $this->get('/teachers/view/' . $this->teachersFixture->teacher1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('teacher'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // How shall we test the view?  It doesn't have any enclosing table or structure so just
        // ignore that part.  Instead, look for individual display fields.
        $field = $html->find('tr#id td',0);
        $this->assertEquals($this->teachersFixture->teacher1Record['id'], $field->plaintext);

        $field = $html->find('tr#giv_name td',0);
        $this->assertEquals($this->teachersFixture->teacher1Record['giv_name'], $field->plaintext);
    }

}
