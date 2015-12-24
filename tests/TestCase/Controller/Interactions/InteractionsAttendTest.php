<?php
namespace App\Test\TestCase\Controller;

use Cake\Datasource\ConnectionManager;
use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\ClazzesFixture;
use App\Test\Fixture\ItypesFixture;
use App\Test\Fixture\InteractionsFixture;
use App\Test\Fixture\StudentsFixture;
use Cake\ORM\TableRegistry;

class InteractionsAttendTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.clazzes',
        'app.cohorts',
        'app.interactions',
        'app.itypes',
        'app.roles',
        'app.roles_users',
        'app.sections',
        'app.students',
        'app.users'
    ];

    /* @var \App\Model\Table\ClazzesTable */
    private $clazzes;

    /* @var \App\Model\Table\InteractionsTable */
    private $interactions;

    /* @var \App\Model\Table\StudentsTable */
    private $students;

    /* @var \App\Test\Fixture\ClazzesFixture */
    private $clazzesFixture;

    /* @var \App\Test\Fixture\ItypesFixture */
    private $itypesFixture;

    /* @var \App\Test\Fixture\InteractionsFixture */
    private $interactionsFixture;

    /* @var \App\Test\Fixture\StudentsFixture */
    private $studentsFixture;

    public function setUp() {
        parent::setUp();
        $this->clazzes = TableRegistry::get('Clazzes');
        $this->interactions = TableRegistry::get('Interactions');
        $this->students = TableRegistry::get('Students');
        $this->clazzesFixture = new ClazzesFixture();
        $this->itypesFixture = new ItypesFixture();
        $this->interactionsFixture = new InteractionsFixture();
        $this->studentsFixture = new StudentsFixture();
    }

    // Test that unauthenticated users, when submitting a request to
    // an action, will get redirected to the login url.
    public function testUnauthenticatedActionsAndUsers() {
        $this->tstUnauthenticatedActionsAndUsers('interactions');
    }

    // Test that users who do not have correct roles, when submitting a request to
    // an action, will get redirected to the home page.
    public function testUnauthorizedActionsAndUsers() {
        $this->tstUnauthorizedActionsAndUsers('interactions');
    }

    // Make sure clazz->section->cohort = student->cohort!
    public function testFixtureIntegrity() {
        /* @var \Cake\Database\Connection $connection */
        $connection = ConnectionManager::get('test');

        $query="SELECT clazz_id, student_id, clazzes.section_id, sections.cohort_id as Csec, students.cohort_id as CStu from interactions
            left join students on interactions.student_id=students.id
            left join clazzes on interactions.clazz_id=clazzes.id
            left join sections on clazzes.section_id=sections.id
            where sections.cohort_id != students.cohort_id";
        $results = $connection->execute($query)->fetchAll('assoc');
        $n=count($results);
        $this->assertEquals(0,$n);
    }


    // GET /attend, no clazz_id parameter
    //public function testAttendGet() {
        //$this->tstAttendGet(null);
    //}

    // GET /attend, with clazz_id parameter, for a class with no attendance
    //public function testAttendGetClazzId() {
        //$this->tstAttendGet(FixtureConstants::clazz2_id);
    //}

    // GET /attend, with clazz_id parameter, for a class with existing attendance
    //public function testAttendGetClazzId() {
        //$this->tstAttendGet($this->clazzesFixture->clazz1Record['id']);
    //}

    /*private function tstAttendGET($clazz_id=null) {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin(FixtureConstants::userAndyAdminId);

        if(is_null($clazz_id))
            $this->get('/interactions/attend');
        else
            $this->get('/interactions/attend?clazz_id='.$clazz_id);

        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#InteractionsAttend',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 4. Ensure that there is a suitably named form element
        $form = $this->content->find('form#InteractionAttendForm', 0);
        $this->assertNotNull($form);

        // 5. Ensure that there is a suitably named table to display the results.
        $this->table = $form->find('table#InteractionsTable', 0);
        $this->assertNotNull($this->table);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $this->thead = $this->table->find('thead', 0);
        $this->thead_ths = $this->thead->find('tr th');

        $this->assertEquals($this->thead_ths[0]->id, 'fam_name');
        $this->assertEquals($this->thead_ths[1]->id, 'giv_name');
        $this->assertEquals($this->thead_ths[2]->id, 'attend');
        $column_count = count($this->thead_ths);
        $this->assertEquals($column_count, 3); // no other columns

        // 7. Ensure that the tbody section has the correct quantity of rows.
        // This should be done using a very similar query as used by the controller.
        $this->tbody = $this->table->find('tbody', 0);
        $this->tbody_rows = $this->tbody->find('tr');
        //if(!is_null($clazz_id))
            //$this->interactionsFixture->filterByClazzId($clazz_id);


        $connection = ConnectionManager::get('default');
        $query = "select students.sid, students.giv_name, students.fam_name, cohorts.id, sections.id, clazzes.id
            from students
            left join cohorts on students.cohort_id = cohorts.id
            left join sections on sections.cohort_id = cohorts.id
            left join clazzes on clazzes.section_id = sections.id
            where clazzes.id=" . $clazz_id;

        /s select students.sid, students.giv_name, students.fam_name, cohorts.id as cohorts_id, sections.id as sections_id, clazzes.id as clazzes_id, interactions.id as interactions_id, interactions.itype_id
            from students
            left join cohorts on students.cohort_id = cohorts.id
            left join sections on sections.cohort_id = cohorts.id
            left join clazzes on clazzes.section_id = sections.id
			left join interactions on interactions.clazz_id=clazzes.id
            where clazzes.id=1 and interactions.itype_id=1s/

        $studentsResults = $connection->execute($query)->fetchAll('assoc');
        $s1=count($this->tbody_rows);
        $s2=count($studentsResults);
        $this->assertEquals($s1,$s2);

        // 8. Ensure that the values displayed in each row are correct.
        // The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($studentsResults));
        $iterator->attachIterator(new \ArrayIterator($this->tbody_rows));

        foreach ($iterator as $values) {
            $attendanceRecord = $values[0];
            $this->htmlRow = $values[1];
            $htmlColumns = $this->htmlRow->find('td');

            // 8.0 fam_name.
            //$clazz = $this->clazzes->get($fixtureRecord['clazz_id']);
            $this->assertEquals($attendanceRecord['fam_name'], $htmlColumns[0]->plaintext);

            // 8.1 giv_name.
            //$student = $this->students->get($fixtureRecord['student_id']);
            $this->assertEquals($attendanceRecord['giv_name'], $htmlColumns[1]->plaintext);

            // 8.2 attend.
            $name='quote['.$attendanceRecord['sid'].']'; // name of hidden
            $id='quote-'.$attendanceRecord['sid'];
            $input=$htmlColumns[2]->find('input[id='.$id.']')[0];
            $this->assertNotNull($input);

            $checked=$input->find('input[checked=checked]');
            $this->assertNull($checked);


            // 8.2 No action links

            // 8.9 No other columns
            $this->assertEquals(count($htmlColumns), $column_count);
        }

        // 9. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);

    }

    // GET /attend, no clazz_id parameter
    public function testAttendPOST() {
        //$this->tstAttendGet(null);
    }*/


}
