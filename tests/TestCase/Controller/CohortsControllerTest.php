<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\CohortsFixture;
use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\MajorsFixture;
use Cake\ORM\TableRegistry;

class CohortsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.cohorts',
        'app.majors',
        'app.roles',
        'app.roles_users',
        'app.users'
    ];

    private $cohorts;
    private $cohortsFixture;
    private $majors;
    private $majorsFixture;
    //private $usersFixture;

    public function setUp() {
        parent::setUp();
        $this->cohorts = TableRegistry::get('Cohorts');
        $this->majors = TableRegistry::get('Majors');
        $this->cohortsFixture = new CohortsFixture();
        $this->majorsFixture = new MajorsFixture();
        //$this->usersFixture = new UsersFixture();
    }

    // Try the roles that should _not_ be authorized for add get
    public function testAddGETUnauthorized() {

        // Anonymous users should not be authorized.
        $this->tstUnauthorizedRequest('get', '/cohorts/add');

        $this->fakeLogin(FixtureConstants::userArnoldAdvisorId);
        $this->tstUnauthorizedRequest('get', '/cohorts/add');

        $this->fakeLogin(FixtureConstants::userSallyStudentId);
        $this->tstUnauthorizedRequest('get', '/cohorts/add');

        $this->fakeLogin(FixtureConstants::userTommyTeacherId);
        $this->tstUnauthorizedRequest('get', '/cohorts/add');
    }

    // test that a logged in user with zero roles will not work
    // test that a logged in user with two roles will have all roles considered
    public function testAddGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin();
        $this->get('/cohorts/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#CohortAddForm',0);
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

        // 4.3 Ensure that there's an input field for start_year, of type text, and that it is empty
        $input = $form->find('input#CohortStartYear',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // 4.4 Ensure that there's an input field for seq, of type text, and that it is empty
        $input = $form->find('input#CohortSeq',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
        $unknownInputCnt--;

        // 4.5 Ensure that there's a select field for major_id, that it has no selection,
        // and that it has the correct quantity of available choices.
        if($this->lookForSelect($form,'CohortMajorId','majors')) $unknownSelectCnt--;

        // 4.9 Have all the input and select fields been accounted for?  Are there
        // any extras?
        $this->assertEquals(0, $unknownInputCnt);
        $this->assertEquals(0, $unknownSelectCnt);

        // 5. Examine the <A> tags on this page.  There should be zero links.
        $content = $html->find('div#CohortsAdd',0);
        $this->assertNotNull($content);
        $links = $content->find('a');
        $this->assertEquals(0,count($links));
    }

    public function testAddPOST() {

        $this->fakeLogin();
        $this->post('/cohorts/add', $this->cohortsFixture->newCohortRecord);
        $this->assertResponseSuccess(); // 2xx,3xx
        $this->assertRedirect( '/cohorts' );

        // Now verify what we think just got written
        $new_id = FixtureConstants::cohort1_id + 1;
        $query = $this->cohorts->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_cohort = $this->cohorts->get($new_id);
        $this->assertEquals($new_cohort['start_year'],$this->cohortsFixture->newCohortRecord['start_year']);
        $this->assertEquals($new_cohort['seq'],$this->cohortsFixture->newCohortRecord['seq']);
        $this->assertEquals($new_cohort['major_id'],$this->cohortsFixture->newCohortRecord['major_id']);
    }

    public function testDeletePOST() {

        $this->fakeLogin();
        $cohort_id = $this->cohortsFixture->cohort1Record['id'];
        $this->post('/cohorts/delete/' . $cohort_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/cohorts' );

        // Now verify that the record no longer exists
        $query = $this->cohorts->find()->where(['id' => $cohort_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $cohortsFixture = new CohortsFixture();
        $majorsFixture = new MajorsFixture();

        $this->fakeLogin();
        $cohort_id = $cohortsFixture->cohort1Record['id'];
        $this->get('/cohorts/edit/' . $cohort_id);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure these view vars are set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('cohort'));
        $this->assertNotNull($this->viewVariable('majors'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#CohortEditForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // Ensure that there's an input field for start_year, of type text, and that it is correctly set
        $input = $form->find('input#CohortStartYear',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $cohortsFixture->cohort1Record['start_year']);

        // Ensure that there's an input field for seq, of type text, and that it is correctly set
        $input = $form->find('input#CohortSeq',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value,  $cohortsFixture->cohort1Record['seq']);

        // Ensure that there's a select field for major_id and that it is correctly set
        $option = $form->find('select#CohortMajorId option[selected]',0);
        $major_id = $cohortsFixture->cohort1Record['major_id'];
        $this->assertEquals($option->value, $major_id);

        // Even though major_id is correct, we don't display major_id.  Instead we display the title
        // from the related Majors table. Verify that title is displayed correctly.
        $major = $majorsFixture->get($major_id);
        $this->assertEquals($major['title'], $option->plaintext);
    }

    public function testEditPOST() {

        $cohortsFixture = new CohortsFixture();

        $this->fakeLogin();
        $cohort_id = $cohortsFixture->cohort1Record['id'];
        $this->put('/cohorts/edit/' . $cohort_id, $cohortsFixture->newCohortRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/cohorts');

        // Now verify what we think just got written
        $cohorts = TableRegistry::get('Cohorts');
        $query = $cohorts->find()->where(['id' => $cohort_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $cohort = $cohorts->get($cohort_id);
        $this->assertEquals($cohort['start_year'],$cohortsFixture->newCohortRecord['start_year']);
        $this->assertEquals($cohort['seq'],$cohortsFixture->newCohortRecord['seq']);
        $this->assertEquals($cohort['major_id'],$cohortsFixture->newCohortRecord['major_id']);
    }

    public function testIndexGET() {

        $this->fakeLogin();
        $this->get('/cohorts/index');
        $this->assertResponseOk(); //2xx
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('cohorts'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // How shall we test the index?

        // 1. Ensure that there is a suitably named table to display the results.
        $cohorts_table = $html->find('table#cohorts',0);
        $this->assertNotNull($cohorts_table);

        // 2. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $cohorts_table->find('thead',0);
        $thead_ths = $thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'id');
        $this->assertEquals($thead_ths[1]->id, 'start_year');
        $this->assertEquals($thead_ths[2]->id, 'major');
        $this->assertEquals($thead_ths[3]->id, 'seq');
        $this->assertEquals($thead_ths[4]->id, 'nickname');
        $this->assertEquals($thead_ths[5]->id, 'actions');
        $this->assertEquals(count($thead_ths),6); // no other columns

        // 3. Ensure that the tbody section has the same
        //    quantity of rows as the count of cohort records in the fixture.
        $cohortsFixture = new CohortsFixture();
        $majorsFixture = new MajorsFixture();
        $tbody = $cohorts_table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($cohortsFixture));

        // 4. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.  In order to do this we'll also need
        //    to read from the Cohorts table.
        $cohorts = TableRegistry::get('Cohorts');
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($cohortsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');
            $this->assertEquals($fixtureRecord['id'], $htmlColumns[0]->plaintext);
            $this->assertEquals($fixtureRecord['start_year'],  $htmlColumns[1]->plaintext);

            // major_id requires finding the related value in the MajorsFixture
            $major_id = $fixtureRecord['major_id'];
            $major = $majorsFixture->get($major_id);
            $this->assertEquals($major['sdesc'], $htmlColumns[2]->plaintext);

            $this->assertEquals($fixtureRecord['seq'],  $htmlColumns[3]->plaintext);

            // nickname is computed by the Cohort Entity.
            $cohort = $cohorts->get($fixtureRecord['id'], ['contain' => ['Majors']]);
            $this->assertEquals($cohort->nickname, $htmlColumns[4]->plaintext);
            // Ignore the action links
        }

    }

    public function testViewGET() {

        $cohortsFixture = new CohortsFixture();

        $this->fakeLogin();
        $this->get('/cohorts/view/' . $cohortsFixture->cohort1Record['id']);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('cohort'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // How shall we test the view?  It doesn't have any enclosing table or structure so just
        // ignore that part.  Instead, look for individual display fields.
        $field = $html->find('td#id',0);
        $this->assertEquals($cohortsFixture->cohort1Record['id'], $field->plaintext);

        $field = $html->find('td#start_year',0);
        $this->assertEquals($cohortsFixture->cohort1Record['start_year'], $field->plaintext);

        // major_id requires finding the related value in the MajorsFixture
        $field = $html->find('td#major_title',0);
        $majorsFixture = new MajorsFixture();
        $major_id = $cohortsFixture->cohort1Record['major_id'];
        $major = $majorsFixture->get($major_id);
        $this->assertEquals($major['title'], $field->plaintext);

        $field = $html->find('td#seq',0);
        $this->assertEquals($cohortsFixture->cohort1Record['seq'], $field->plaintext);

        // nickname is computed by the Cohort Entity.
        $field = $html->find('td#nickname',0);
        $cohorts = TableRegistry::get('Cohorts');
        $cohort = $cohorts->get($cohortsFixture->cohort1Record['id'], ['contain' => ['Majors']]);
        $this->assertEquals($cohort->nickname, $field->plaintext);
    }

}
