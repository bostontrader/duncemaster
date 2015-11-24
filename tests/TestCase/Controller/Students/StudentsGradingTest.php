<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\SectionsFixture;
use App\Test\Fixture\StudentsFixture;
use Cake\ORM\TableRegistry;

class StudentsGradingTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.cohorts',
        'app.majors',
        'app.roles',
        'app.roles_users',
        'app.sections',
        'app.students',
        'app.users'
    ];

    /* @var \App\Model\Table\SectionsTable */
    private $sections;

    private $sectionsFixture;
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
        $section_id=$this->sectionsFixture->section1Record['id'];
        $this->get(
            '/students/view/'.$this->studentsFixture->student1Record['id'].
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
        //
        //  In this case the actual order that the fields are listed is hereby deemed important.

        // This is the count of the table rows that are presently unaccounted for.
        $unknownRowCnt = count($this->table->find('tr'));

        // 2.1 sid
        //$field = $html->find('tr#sid td',0);
        //$this->assertEquals($fixtureRecord['sid'], $field->plaintext);
        //$unknownRowCnt--;

        // 2.2 fam_name
        //$field = $html->find('tr#fam_name td',0);
        //$this->assertEquals($fixtureRecord['fam_name'], $field->plaintext);
        //$unknownRowCnt--;

        // 2.3 giv_name
        //$field = $html->find('tr#giv_name td',0);
        //$this->assertEquals($fixtureRecord['giv_name'], $field->plaintext);
        //$unknownRowCnt--;

        // 2.4 cohort_name
        //$field = $html->find('tr#cohort_nickname td',0);
        //$student = $this->students->get($fixtureRecord['id'],['contain' => ['Cohorts.Majors']]);
        //$this->assertEquals($student->cohort->nickname, $field->plaintext);
        //$unknownRowCnt--;

        // 2.9 Have all the rows been accounted for?  Are there any extras?
        //$this->assertEquals(0, $unknownRowCnt);
    }

}
