<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\TeachersFixture;
use Cake\ORM\TableRegistry;

class TeachersControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.teachers'
    ];

    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/teachers/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('teacher'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#TeacherAddForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // Ensure that there's an input field for giv_name, of type text, and that it is empty
        $input = $form->find('input#TeacherGivName',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
    }

    public function testAddPOST() {

        $teachersFixture = new TeachersFixture();

        $this->fakeLogin();
        $this->post('/teachers/add', $teachersFixture->newTeacherRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/teachers' );

        // Now verify what we think just got written
        $teachers = TableRegistry::get('Teachers');
        $new_id = FixtureConstants::teacher1_id + 1;
        $query = $teachers->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_teacher = $teachers->get($new_id);
        $this->assertEquals($new_teacher['giv_name'],$teachersFixture->newTeacherRecord['giv_name']);
    }

    public function testDeletePOST() {

        $teachersFixture = new TeachersFixture();

        $this->fakeLogin();
        $teacher_id = $teachersFixture->teacher1Record['id'];
        $this->post('/teachers/delete/' . $teacher_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/teachers' );

        // Now verify that the record no longer exists
        $teachers = TableRegistry::get('Teachers');
        $query = $teachers->find()->where(['id' => $teacher_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $teachersFixture = new TeachersFixture();

        $this->fakeLogin();
        $teacher_id = $teachersFixture->teacher1Record['id'];
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
        $this->assertEquals($input->value, $teachersFixture->teacher1Record['giv_name']);
    }

    public function testEditPOST() {

        $teachersFixture = new TeachersFixture();

        $this->fakeLogin();
        $teacher_id = $teachersFixture->teacher1Record['id'];
        $this->put('/teachers/edit/' . $teacher_id, $teachersFixture->newTeacherRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/teachers');

        // Now verify what we think just got written
        $teachers = TableRegistry::get('Teachers');
        $query = $teachers->find()->where(['id' => $teachersFixture->teacher1Record['id']]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $teacher = $teachers->get($teachersFixture->teacher1Record['id']);
        $this->assertEquals($teacher['giv_name'],$teachersFixture->newTeacherRecord['giv_name']);

    }

    public function testIndexGET() {

        $this->fakeLogin();
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
        $teachersFixture = new TeachersFixture();
        $tbody = $teachers_table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($teachersFixture));

        // 4. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.  In order to do this we'll also need
        //    to read from the Teachers table.
        $teachers = TableRegistry::get('Teachers');
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($teachersFixture->records));
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

        $teachersFixture = new TeachersFixture();

        $this->fakeLogin();
        $this->get('/teachers/view/' . $teachersFixture->teacher1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('teacher'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // How shall we test the view?  It doesn't have any enclosing table or structure so just
        // ignore that part.  Instead, look for individual display fields.
        $field = $html->find('td#id',0);
        $this->assertEquals($teachersFixture->teacher1Record['id'], $field->plaintext);

        $field = $html->find('td#giv_name',0);
        $this->assertEquals($teachersFixture->teacher1Record['giv_name'], $field->plaintext);
    }

}
