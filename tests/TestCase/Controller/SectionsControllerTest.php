<?php
namespace App\Test\TestCase\Controller;

use App\Test\Fixture\SectionsFixture;
use Cake\ORM\TableRegistry;

class SectionsControllerTest extends DMIntegrationTestCase {

    public $fixtures = [
        //'app.cohorts',
        'app.sections'
        //'app.subjects'
    ];

    public function testAddGET() {

        $this->fakeLogin();
        $this->get('/sections/add');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('section'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=SectionAddForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for weekday, that is empty
        $input = $form->find('input[id=SectionWeekday]')[0];
        $this->assertEquals($input->value, false);
    }

    public function testAddPOST() {

        $sectionsFixture = new SectionsFixture();

        $this->fakeLogin();
        $this->post('/sections/add', $sectionsFixture->newSectionRecord);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/sections' );

        // Now verify what we think just got written
        $sections = TableRegistry::get('Sections');
        $query = $sections->find()->where(['id' => $sectionsFixture->newSectionRecord['id']]);
        $this->assertEquals(1, $query->count());

        // Now retrieve that 1 record and compare to what we expect
        $section = $sections->get($sectionsFixture->newSectionRecord['id']);
        $this->assertEquals($section['weekday'],$sectionsFixture->newSectionRecord['weekday']);
    }

    public function testDeletePOST() {

        $sectionsFixture = new SectionsFixture();

        $this->fakeLogin();
        $this->post('/sections/delete/' . $sectionsFixture->section1Record['id']);
        $this->assertResponseSuccess();
        $this->assertRedirect( '/sections' );

        // Now verify that the record no longer exists
        $sections = TableRegistry::get('Sections');
        $query = $sections->find()->where(['id' => $sectionsFixture->section1Record['id']]);
        $this->assertEquals(0, $query->count());
    }

    public function testEditGET() {

        $sectionsFixture = new SectionsFixture();

        $this->fakeLogin();
        $this->get('/sections/edit/' . $sectionsFixture->section1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set, to keep the FormHelper happy
        $this->assertNotNull($this->viewVariable('section'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        $form = $html->find('form[id=SectionEditForm]')[0];
        $this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for weekday, that is correctly set
        $input = $form->find('input[id=SectionWeekday]')[0];
        $this->assertEquals($input->value, $sectionsFixture->section1Record['weekday']);

    }

    public function testEditPOST() {

        $sectionsFixture = new SectionsFixture();

        $this->fakeLogin();
        $this->post('/sections/edit/' . $sectionsFixture->section1Record['id'], $sectionsFixture->newSectionRecord);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Now verify what we think just got written
        $sections = TableRegistry::get('Sections');
        $query = $sections->find()->where(['id' => $sectionsFixture->section1Record['id']]);
        $c = $query->count();
        $this->assertEquals(1, $c);

        // Now retrieve that 1 record and compare to what we expect
        $section = $sections->get($sectionsFixture->section1Record['id']);
        $this->assertEquals($section['weekday'],$sectionsFixture->newSectionRecord['weekday']);

    }

    public function testIndexGET() {

        $this->fakeLogin();
        $result = $this->get('/sections/index');
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Parse the html from the response
        $html = str_get_html($result);

        // 1. Ensure that the single row of the thead section
        //    has a column for id and title, in that order
        //$rows = $html->find('table[id=sections]',0)->find('thead',0)->find('tr');
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
        //    quantity of rows as the count of section records in the fixture.
        //    For each of these rows, ensure that the id and title match
        //$sectionFixture = new SectionsFixture();
        //$rowsInHTMLTable = $html->find('table[id=sections]',0)->find('tbody',0)->find('tr');
        //$this->assertEqual(count($sectionFixture->records), count($rowsInHTMLTable));
        //$iterator = new MultipleIterator;
        //$iterator->attachIterator(new ArrayIterator($sectionFixture->records));
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

        $sectionsFixture = new SectionsFixture();

        $this->fakeLogin();
        $this->get('/sections/view/' . $sectionsFixture->section1Record['id']);
        $this->assertResponseOk();
        $this->assertNoRedirect();

        // Make sure this view var is set
        $this->assertNotNull($this->viewVariable('section'));

        // Parse the html from the response
        $html = str_get_html($this->_response->body());

        // Ensure that the correct form exists
        //$form = $html->find('form[id=SectionsEditForm]')[0];
        //$this->assertNotNull($form);

        // Omit the id field
        // Ensure that there's a field for title, that is correctly set
        //$input = $form->find('input[id=SectionsTitle]')[0];
        //$this->assertEquals($input->value, $sectionsFixture->section1Record['title']);

        // Ensure that there's a field for sdesc, that is empty
        //$input = $form->find('input[id=SectionsSDesc]')[0];
        //$this->assertEquals($input->value, false);
    }

}
