<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\InteractionsFixture;
use Cake\ORM\TableRegistry;

class InteractionsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.interactions',
        'app.sections',
        'app.students'
    ];

    //public $cohorts;
    //public $sections;
    //public $semesters;
    //public $sectionsFixture;
    //public $semestersFixture;
    //public $subjectsFixture;

    public function setUp() {
        //$this->cohorts = TableRegistry::get('Cohorts');
        //$this->sections = TableRegistry::get('Sections');
        //$this->semesters = TableRegistry::get('Semesters');
        //$this->sectionsFixture = new SectionsFixture();
        //$this->semestersFixture = new SemestersFixture();
        //$this->subjectsFixture = new SubjectsFixture();
    }

    public function testAddGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin();
        $this->get('/interactions/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Ensure that the correct form exists
        $form = $html->find('form#InteractionAddForm]',0);
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

        // 4.3 Ensure that there's a select field for clazz_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->lookForSelect($form,'InteractionClazzId','clazzes')) $unknownSelectCnt--;

        // 4.3 Ensure that there's a select field for student_id, that it has no selection,
        //    and that it has the correct quantity of available choices.
        if($this->lookForSelect($form,'InteractionStudentId','students')) $unknownSelectCnt--;

        // Omit the id field
        // Ensure that there's a field for clazz_id, that is empty
        $input = $form->find('input[id=InteractionClazzId]')[0];
        $this->assertEquals($input->value, false);

        // Ensure that there's a field for sdesc, that is empty
        $input = $form->find('input[id=InteractionSDesc]')[0];
        $this->assertEquals($input->value, false);
    }

    /*public function testAddPOST() {

        $interactionsFixture = new InteractionsFixture();

        $this->fakeLogin();
        $this->post('/interactions/add', $interactionsFixture->newInteractionRecord);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/interactions' );

        // Now verify what we think just got written
        $interactions = TableRegistry::get('Interactions');
        $query = $interactions->find()->where(['id' => $interactionsFixture->newInteractionRecord['id']]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $interaction = $interactions->get($interactionsFixture->newInteractionRecord['id']);
        $this->assertEquals($interaction['title'],$interactionsFixture->newInteractionRecord['title']);
    }

    public function testDeletePOST() {

        $interactionsFixture = new InteractionsFixture();

        $this->fakeLogin();
        $this->post('/interactions/delete/' . $interactionsFixture->interaction1Record['id']);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/interactions' );

        // Now verify that the record no longer exists
        $interactions = TableRegistry::get('Interactions');
        $query = $interactions->find()->where(['id' => $interactionsFixture->interaction1Record['id']]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $interactionsFixture = new InteractionsFixture();

        $this->fakeLogin();
        $this->get('/interactions/edit/' . $interactionsFixture->interaction1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('interaction'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=InteractionEditForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is correctly set
        $input = $form->find('input[id=InteractionTitle]')[0];
        $this->assertEquals($input->value, $interactionsFixture->interaction1Record['title']);

        // Ensure that there's a field for sdesc, that is correctly set
        $input = $form->find('input[id=InteractionSDesc]')[0];
        $this->assertEquals($input->value,  $interactionsFixture->interaction1Record['sdesc']);

    }

    public function testEditPOST() {

        $interactionsFixture = new InteractionsFixture();

        $this->fakeLogin();
        $this->post('/interactions/edit/' . $interactionsFixture->interaction1Record['id'], $interactionsFixture->newInteractionRecord);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Now verify what we think just got written
        $interactions = TableRegistry::get('Interactions');
        $query = $interactions->find()->where(['id' => $interactionsFixture->interaction1Record['id']]);
        $c = $query->count();
        $this->assertEquals(1, $c);

        // Now retrieve that 1 record and compare to what we expect
        $interaction = $interactions->get($interactionsFixture->interaction1Record['id']);
        $this->assertEquals($interaction['title'],$interactionsFixture->newInteractionRecord['title']);

    }*/

    public function testIndexGET() {

        // 1. Simulate login, submit request, examine response.
        $this->fakeLogin();
        $this->get('/interactions/index');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // 2. Parse the html from the response
        $html = str_get_html($this->_response->body());

        // 3. Get a the count of all <A> tags that are presently unaccounted for.
        $content = $html->find('div#interactionsIndex',0);
        $this->assertNotNull($content);
        $unknownATag = count($content->find('a'));

        // 4. Look for the create new interaction link
        $this->assertEquals(1, count($html->find('a#interactionAdd')));
        $unknownATag--;

        // 5. Ensure that there is a suitably named table to display the results.
        $interactions_table = $html->find('table#interactionsTable',0);
        $this->assertNotNull($interactions_table);

        // 6. Ensure that said table's thead element contains the correct
        //    headings, in the correct order, and nothing else.
        $thead = $interactions_table->find('thead',0);
        $thead_ths = $thead->find('tr th');

        $this->assertEquals($thead_ths[0]->id, 'cohort');
        $this->assertEquals($thead_ths[1]->id, 'subject');
        $this->assertEquals($thead_ths[2]->id, 'semester');
        $this->assertEquals($thead_ths[3]->id, 'weekday');
        $this->assertEquals($thead_ths[4]->id, 'start_time');
        $this->assertEquals($thead_ths[5]->id, 'thours');
        $this->assertEquals($thead_ths[6]->id, 'actions');
        $this->assertEquals(count($thead_ths),7); // no other columns

        // 7. Ensure that the tbody section has the same
        //    quantity of rows as the count of interaction records in the fixture.
        $tbody = $interactions_table->find('tbody',0);
        $tbody_rows = $tbody->find('tr');
        $this->assertEquals(count($tbody_rows), count($this->interactionsFixture));

        // 8. Ensure that the values displayed in each row, match the values from
        //    the fixture.  The values should be presented in a particular order
        //    with nothing else thereafter.
        $iterator = new \MultipleIterator();
        $iterator->attachIterator(new \ArrayIterator($this->interactionsFixture->records));
        $iterator->attachIterator(new \ArrayIterator($tbody_rows));

        foreach ($iterator as $values) {
            $fixtureRecord = $values[0];
            $htmlRow = $values[1];
            $htmlColumns = $htmlRow->find('td');

            // 8.0 cohort_nickname
            $cohorts = TableRegistry::get('Cohorts');
            $cohort = $cohorts->get($fixtureRecord['cohort_id'],['contain' => ['Majors']]);
            $this->assertEquals($cohort->nickname, $htmlColumns[0]->plaintext);

            // 8.1 subject
            $subject = $this->subjectsFixture->get($fixtureRecord['subject_id']);
            $this->assertEquals($subject['title'], $htmlColumns[1]->plaintext);

            // 8.2 semester_nickname
            $semesters = TableRegistry::get('Semesters');
            $semester = $semesters->get($fixtureRecord['semester_id']);
            $this->assertEquals($semester->nickname, $htmlColumns[2]->plaintext);

            $this->assertEquals($fixtureRecord['weekday'], $htmlColumns[3]->plaintext);
            $this->assertEquals($fixtureRecord['start_time'], $htmlColumns[4]->plaintext);
            $this->assertEquals($fixtureRecord['thours'], $htmlColumns[5]->plaintext);

            // 8.6 Now examine the action links
            $actionLinks = $htmlRow->find('a');
            $this->assertEquals('interactionView', $actionLinks[0]->name);
            $unknownATag--;
            $this->assertEquals('interactionEdit', $actionLinks[1]->name);
            $unknownATag--;
            $this->assertEquals('interactionDelete', $actionLinks[2]->name);
            $unknownATag--;
        }

        // 9. Ensure that all the <A> tags have been accounted for
        $this->assertEquals(0, $unknownATag);
    }

    /*public function testViewGET() {

        $interactionsFixture = new InteractionsFixture();

        $this->fakeLogin();
        $this->get('/interactions/view/' . $interactionsFixture->interaction1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('interaction'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        //$form = $html->find('form[id=InteractionEditForm]')[0];
        //$this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for class_id, that is correctly set
        //$input = $form->find('input[id=InteractionTitle]')[0];
        //$this->assertEquals($input->value, $interactionsFixture->interaction1Record['title']);

        // Ensure that there's a field for sdesc, that is empty
        //$input = $form->find('input[id=InteractionSDesc]')[0];
        //$this->assertEquals($input->value, false);
    }*/

}
