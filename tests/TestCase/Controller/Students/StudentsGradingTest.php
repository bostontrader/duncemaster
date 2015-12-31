<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\SectionsFixture;
use App\Test\Fixture\StudentsFixture;
use Cake\ORM\TableRegistry;

class StudentsGradingTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.clazzes',
        'app.cohorts',
        'app.interactions',
        'app.majors',
        'app.roles',
        'app.roles_users',
        'app.sections',
        'app.students',
        'app.users'
    ];

    /* @var \App\Model\Table\SectionsTable */
    private $sections;

    /* @var \App\Test\Fixture\SectionsFixture */
    private $sectionsFixture;

    /* @var \App\Test\Fixture\StudentsFixture */
    private $studentsFixture;

    public function setUp() {
        parent::setUp();
        $this->sections = TableRegistry::get('Sections');
        $this->sectionsFixture = new SectionsFixture();
        $this->studentsFixture = new StudentsFixture();
    }

    // Don't care which student. Send a section_id as a request
    // param. Examine grading info.
    public function testViewGETWithRequestParameters() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);
        $section_id=FixtureConstants::sectionToGrade;
        $student_id=FixtureConstants::studentTypical;
        $this->get(
            '/students/view/'.$student_id.
            '?section_id='.$section_id
        );
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // In this test we are only interested in examining the Grading info.
        // The other things have been tested elsewhere.

        // 3.  Look for the table that contains the grading info.
        $this->table = $html->find('table#StudentGradingTable',0);
        $this->assertNotNull($this->table);

        // 4. Now inspect the fields in the table.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values.

        $rows=$this->table->find('tr');

        // 4.0 clazzCnt
        /* @var \simple_html_dom_node $row */
        $row=$rows[0];
        $field = $row->find('td',0);
        $this->assertEquals(FixtureConstants::clazzCnt, $field->plaintext);

        // 4.1 attendCnt
        $row=$rows[1];
        $field = $row->find('td',0);
        $this->assertEquals(FixtureConstants::attendCnt, $field->plaintext);

        // 4.2 excusedAbsenceCnt
        $row=$rows[1];
        $field = $row->find('td',0);
        $this->assertEquals(FixtureConstants::excusedAbsenceCnt, $field->plaintext);

        // 4.3 ejectedFromClassCnt
        $row=$rows[1];
        $field = $row->find('td',0);
        $this->assertEquals(FixtureConstants::ejectedFromClassCnt, $field->plaintext);

        // 4.4 leftClassCnt
        $row=$rows[1];
        $field = $row->find('td',0);
        $this->assertEquals(FixtureConstants::leftClassCnt, $field->plaintext);
    }
}
