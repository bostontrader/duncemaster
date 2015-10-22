<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\FixtureConstants;
use App\Test\Fixture\SubjectsFixture;
use Cake\ORM\TableRegistry;

class SubjectsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        'app.subjects'
    ];

    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/subjects/add');
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('subject'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#SubjectAddForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // Ensure that there's an input field for title, of type text, and that it is empty
        $input = $form->find('input#SubjectTitle',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, false);
    }

    public function testAddPOST() {

        $subjectsFixture = new SubjectsFixture();

        $this->fakeLogin();
        $this->post('/subjects/add', $subjectsFixture->newSubjectRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/subjects' );

        // Now verify what we think just got written
        $subjects = TableRegistry::get('Subjects');
        $new_id = FixtureConstants::subject1_id + 1;
        $query = $subjects->find()->where(['id' => $new_id]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $new_subject = $subjects->get($new_id);
        $this->assertEquals($new_subject['title'],$subjectsFixture->newSubjectRecord['title']);
    }

    public function testDeletePOST() {

        $subjectsFixture = new SubjectsFixture();

        $this->fakeLogin();
        $subject_id = $subjectsFixture->subject1Record['id'];
        $this->post('/subjects/delete/' . $subject_id);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect( '/subjects' );

        // Now verify that the record no longer exists
        $subjects = TableRegistry::get('Subjects');
        $query = $subjects->find()->where(['id' => $subject_id]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $subjectsFixture = new SubjectsFixture();

        $this->fakeLogin();
        $subject_id = $subjectsFixture->subject1Record['id'];
        $this->get('/subjects/edit/' . $subject_id);
        $this->assertResponseOk(); // 2xx
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('subject'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form#SubjectEditForm',0);
        $this->assertNotNull($form);

        // Omit the id field

        // Ensure that there's an input field for title, of type text, that is correctly set
        $input = $form->find('input#SubjectTitle',0);
        $this->assertEquals($input->type, "text");
        $this->assertEquals($input->value, $subjectsFixture->subject1Record['title']);
    }

    public function testEditPOST() {

        $subjectsFixture = new SubjectsFixture();

        $this->fakeLogin();
        $subject_id = $subjectsFixture->subject1Record['id'];
        $this->post('/subjects/edit/' . $subject_id, $subjectsFixture->newSubjectRecord);
        $this->assertResponseSuccess(); // 2xx, 3xx
        $this->assertRedirect('/subjects');

        // Now verify what we think just got written
        $subjects = TableRegistry::get('Subjects');
        $query = $subjects->find()->where(['id' => $subjectsFixture->subject1Record['id']]);
        $c = $query->count();
        $this->assertEquals(1, $c);

        // Now retrieve that 1 record and compare to what we expect
        $subject = $subjects->get($subjectsFixture->subject1Record['id']);
        $this->assertEquals($subject['title'],$subjectsFixture->newSubjectRecord['title']);

    }

    public function testIndexGET() {

        $this->fakeLogin();
        $result = $this->get('/subjects/index');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($result);

        // 1. Ensure that the single row of the thead section
        //    has a column for id and title, in that order
        //$rows = $html->find('table[id=subjects]',0)->find('thead',0)->find('tr');
        //$row_cnt = count($rows);
        //$this->assertEqual($row_cnt, 1);

        // 2. Ensure that the thead section has a heading
        //    for id, title, is_active, and is_admin.
        //$columns = $rows[0]->find('td');
        //$this->assertEqual($columns[0]->plaintext, 'id');
        //$this->assertEqual($columns[1]->plaintext, 'title');
        //$this->assertEqual($columns[2]->plaintext, 'is_active');
        //$this->assertEqual($columns[3]->plaintext, 'is_admin');

        // 3. Ensure that the tbody section has the same
        //    quantity of rows as the count of subject records in the fixture.
        //    For each of these rows, ensure that the id and title match
        //$subjectFixture = new SubjectFixture();
        //$rowsInHTMLTable = $html->find('table[id=subjects]',0)->find('tbody',0)->find('tr');
        //$this->assertEqual(count($subjectFixture->records), count($rowsInHTMLTable));
        //$iterator = new MultipleIterator;
        //$iterator->attachIterator(new ArrayIterator($subjectFixture->records));
        //$iterator->attachIterator(new ArrayIterator($rowsInHTMLTable));

        //foreach ($iterator as $values) {
        //$fixtureRecord = $values[0];
        //$htmlRow = $values[1];
        //$htmlColumns = $htmlRow->find('td');
        //$this->assertEqual($fixtureRecord['id'],        $htmlColumns[0]->plaintext);
        //$this->assertEqual($fixtureRecord['title'],  $htmlColumns[1]->plaintext);
        //$this->assertEqual($fixtureRecord['is_active'], $htmlColumns[2]->plaintext);
        //$this->assertEqual($fixtureRecord['is_admin'],  $htmlColumns[3]->plaintext);
        //}
    }

    public function testViewGET() {

        $subjectsFixture = new SubjectsFixture();

        $this->fakeLogin();
        $this->get('/subjects/view/' . $subjectsFixture->subject1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('subject'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        //$form = $html->find('form[id=SubjectEditForm]')[0];
        //$this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is correctly set
        //$input = $form->find('input[id=SubjectTitle]')[0];
        //$this->assertEquals($input->value, $subjectsFixture->subject1Record['title']);

    }

}
