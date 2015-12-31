<?php
namespace App\Test\TestCase\Controller;

use Cake\Datasource\ConnectionManager;
use App\Controller\ItypesController;
use App\Test\Fixture\FixtureConstants;
//use App\Test\Fixture\ClazzesFixture;
//use App\Test\Fixture\ItypesFixture;
//use App\Test\Fixture\InteractionsFixture;
//use App\Test\Fixture\StudentsFixture;
//use Cake\ORM\TableRegistry;

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
    //private $clazzes;

    /* @var \App\Model\Table\InteractionsTable */
    //private $interactions;

    /* @var \App\Model\Table\StudentsTable */
    //private $students;

    /* @var \App\Test\Fixture\ClazzesFixture */
    //private $clazzesFixture;

    /* @var \App\Test\Fixture\ItypesFixture */
    //private $itypesFixture;

    /* @var \App\Test\Fixture\InteractionsFixture */
    //private $interactionsFixture;

    /* @var \App\Test\Fixture\StudentsFixture */
    //private $studentsFixture;

    public function setUp() {
        parent::setUp();
        //$this->clazzes = TableRegistry::get('Clazzes');
        //$this->interactions = TableRegistry::get('Interactions');
        //$this->students = TableRegistry::get('Students');
        //$this->clazzesFixture = new ClazzesFixture();
        //$this->itypesFixture = new ItypesFixture();
        //$this->interactionsFixture = new InteractionsFixture();
        //$this->studentsFixture = new StudentsFixture();
    }

    // Test that unauthenticated users, when submitting a request to
    // an action, will get redirected to the login url.
    //public function testUnauthenticatedActionsAndUsers() {
        //$this->tstUnauthenticatedActionsAndUsers('interactions');
    //}

    // Test that users who do not have correct roles, when submitting a request to
    // an action, will get redirected to the home page.
    //public function testUnauthorizedActionsAndUsers() {
        //$this->tstUnauthorizedActionsAndUsers('interactions');
    //}

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
    public function testAttendGetClazzId() {
        $this->tstAttendGet(FixtureConstants::clazzTypical);
    }

    // GET /attend, with clazz_id parameter, for a class with existing attendance
    //public function testAttendGetClazzId() {
        //$this->tstAttendGet($this->clazzesFixture->clazz1Record['id']);
    //}

    private function tstAttendGET($clazz_id=null) {

        if(is_null($clazz_id))
            $url='/interactions/attend';
        else
            $url='/interactions/attend?clazz_id='.$clazz_id;

        // 1. Login, GET the url, parse the response and send it back.
        $html=$this->loginRequestResponse(FixtureConstants::userAndyAdminId,$url);

        // 2. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#InteractionAttendForm',0);
        $this->assertNotNull($form);

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $this->content = $html->find('div#InteractionsAttend',0);
        $this->assertNotNull($this->content);
        $unknownATag = count($this->content->find('a'));

        // 4. Ensure that there is a suitably named table to display the results.
        $this->table = $form->find('table#InteractionsTable', 0);
        $this->assertNotNull($this->table);

        // 5. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $this->thead = $this->table->find('thead', 0);
        $this->thead_ths = $this->thead->find('tr th');

        $this->assertEquals($this->thead_ths[0]->id, 'sort');
        $this->assertEquals($this->thead_ths[1]->id, 'sid');
        $this->assertEquals($this->thead_ths[2]->id, 'fam_name');
        $this->assertEquals($this->thead_ths[3]->id, 'giv_name');
        $this->assertEquals($this->thead_ths[4]->id, 'phonetic_name');
        $this->assertEquals($this->thead_ths[5]->id, 'attend');
        $column_count = count($this->thead_ths);
        $this->assertEquals($column_count, 6); // no other columns

        // 6. Ensure that the tbody section has the correct quantity of rows.
        // This should be done using a very similar query as used by the controller.
        $this->tbody = $this->table->find('tbody', 0);
        $this->tbody_rows = $this->tbody->find('tr');
        //if(!is_null($clazz_id))
            //$this->interactionsFixture->filterByClazzId($clazz_id);
        /* @var \Cake\Database\Connection $connection */


        $connection = ConnectionManager::get('default');

        // This query should be essentially the same as the query in InteractionsController.attend
        $query = "select students.sort, students.sid, students.id as student_id, students.giv_name, students.fam_name, students.phonetic_name, cohorts.id, sections.id, clazzes.id
            from students
            left join cohorts on students.cohort_id = cohorts.id
            left join sections on sections.cohort_id = cohorts.id
            left join clazzes on clazzes.section_id = sections.id
            left join interactions on interactions.clazz_id=clazzes.id and interactions.student_id=students.id and interactions.itype_id=".ItypesController::ATTEND." where clazzes.id=".$clazz_id.
            " order by sort";
        $studentsResults = $connection->execute($query)->fetchAll('assoc');
        $s1=count($this->tbody_rows);
        $s2=count($studentsResults);
        $this->assertEquals($s1,$s2);

        // 7. Ensure that the values displayed in each row are correct.
        // The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($studentsResults));
        $iterator->attachIterator(new \ArrayIterator($this->tbody_rows));

        foreach ($iterator as $values) {
            $attendanceRecord = $values[0];
            $this->htmlRow = $values[1];
            $htmlColumns = $this->htmlRow->find('td');

            // 7.0 sort.
            $this->assertEquals($attendanceRecord['sort'], $htmlColumns[0]->plaintext);

            // 7.1 sid.
            $s1=$htmlColumns[1]->plaintext;
            $this->assertEquals($attendanceRecord['sid'], $htmlColumns[1]->plaintext);

            // 7.2 fam_name.
            $this->assertEquals($attendanceRecord['fam_name'], $htmlColumns[2]->plaintext);

            // 7.3 giv_name.
            $this->assertEquals($attendanceRecord['giv_name'], $htmlColumns[3]->plaintext);

            // 7.4 phonetic_name.
            $this->assertEquals($attendanceRecord['phonetic_name'], $htmlColumns[4]->plaintext);

            // 7.5 attend.
            //$name='attend['.$attendanceRecord['id'].']'; // name of hidden field
            $student_id='attend-'.$attendanceRecord['student_id'];

            /* @var \simple_html_dom_node $td */
            $td=$htmlColumns[5];

            /* @var \simple_html_dom_node $input */
            $input=$td->find('input[id='.$student_id.']')[0];
            $this->assertNotNull($input);

            $checked=$input->find('input[checked=checked]');
            $this->assertEquals(0,count($checked));


            // No action links

            // 7.9 No other columns
            $this->assertEquals(count($htmlColumns), $column_count);
        }

        // 8. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    // Start with a class that has a known existing set of attendance records.
    public function testAttendPOST() {

        // Build a suitable array of data to POST
        // 1. Not marked here, the post data not here
        // 2. Not marked here, the post data says here
        // 3. Marked here, post data say not here
        // 4. Marked here, post data says here.
        // Login

        // Post

        // Now read the updated records and validate.

        //
        // Original....
        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        //$fixtureRecord=$this->interactionsFixture->newInteractionRecord;
        //$fromDbRecord=$this->genericEditPutProlog(
        //FixtureConstants::userAndyAdminId,
        //'/interactions/edit', $fixtureRecord,
        //'/interactions', $this->interactions
        //);

        // 2. Now validate that record.
        //$this->assertEquals($fromDbRecord['clazz_id'], $fixtureRecord['clazz_id']);
        //$this->assertEquals($fromDbRecord['student_id'], $fixtureRecord['student_id']);
        //$this->assertEquals($fromDbRecord['itype_id'], $fixtureRecord['itype_id']);

        // From DMIntegration test...
        //$connection = ConnectionManager::get('test');
        //$query=new Query($connection,$table);

        // Retrieve the record with the lowest id.
        //$originalRecord=$query->find('all')->order(['id' => 'ASC'])->first();
        //$edit_id=$originalRecord['id'];

        //$this->fakeLogin($user_id);
        //$this->put($url.'/'.$edit_id, $post_data);
        //$this->assertResponseSuccess(); // 2xx, 3xx
        //$this->assertRedirect( $redirect_url );

        // Now retrieve that 1 record and send it back.
        //$query=new Query($connection,$table);
        //return $query->find('all')->where(['id' => $edit_id])->first();


    }
}
