<?php
namespace App\Test\TestCase\Controller\Clazzes;

use App\Controller\ClazzesController;
use App\Test\Fixture\FixtureConstants;
use Cake\Datasource\ConnectionManager;

class ClazzesControllerTestAddGET extends ClazzesControllerTest {

    public function setUp() {
        parent::setUp();
    }

    /**
     * GET /clazzes/add?section_id=n
     * Returns the new clazz form.
     *
     * The section_id parameter is mandatory. Because...
     * A clazz needs an associated section. The entry form can easily enough present a select-list
     * of section choices. But if the request _does not_ specify a section then which sections should
     * appear in the select list? Including all of them will involve a long and cumbersome list
     * because it's filled with sections from semesters past. But pruning that list is a remarkably
     * slippery and needlessly tedious issue best left as an exercise for the reader.
     *
     * As a practical matter, this is a non-issue. The creation of a new clazz should be in the
     * context of a section (and its teacher and semester) that has already been determined.
     * We'll still use a select-list, but now we can populate it with only those sections from
     * the same semester, with the same teacher, as the section specified by the request.
     *
     * 1. The new class form can only be seen by an admin or the teacher of the specified section.
     *    We don't want to leak _any_ information to users who are not properly authorized. This
     *    form will contain a list of sections, so redirect to /clazzes/index if not properly authorized.
     * 2. A class must have an associated section.
     * 3. The form will present a select list of candidate sections and by default no option will be selected.
     *    If (the section_id param matches an available choice) then
     *      the value of that param will be used by the form to set the initial selection in the select list.
     * 4. The avail choices for the select list are:
     *    all sections from the same semester, with the same teacher, as the section specified by the request.
     */

    public function testAddGet()
    {

        // 1. Build a list of all sections that have the same semester and teacher
        // as the given typical section
        $query = "SELECT DISTINCT b.id
            FROM sections AS a, sections AS b
            WHERE a.semester_id=b.semester_id
            AND a.teacher_id=b.teacher_id
            AND a.id=" . FixtureConstants::sectionTypical;

        /* @var \Cake\Database\Connection $connection */
        $connection = ConnectionManager::get('default');
        $allSectionsForThisTeacherAndSemester = $connection->execute($query)->fetchAll('assoc');

        // 2. Positive tests. Test of functionality that should be handled by the controller.
        // 2.1 admin   GET /clazzes/add
        //      Error. The section_id param is missing and mandatory. Doesn't matter who the user is.
        $this->tstAddGet(FixtureConstants::userAndyAdminUsername, FixtureConstants::userAndyAdminPw, 400, ClazzesController::NEED_SECTION_ID);

        // 2.2 teacher1 GET /clazzes/add?section_id=n
        //     Note: Make sure that the teacher for sectionTypical is connected to userTommyTeacher.  Success.
        $this->tstAddGet(FixtureConstants::userTommyTeacherUsername, FixtureConstants::userTommyTeacherPw, 200, null, FixtureConstants::sectionTypical, $allSectionsForThisTeacherAndSemester);

        // 2.3 teacher2 GET /clazzes/add?section_id=n
        //     Note: Make sure that the teacher for sectionTypical is not connected to userTammyTeacher.  Error.
        $this->tstAddGet(FixtureConstants::userTerryTeacherUsername, FixtureConstants::userTerryTeacherPw, 400, ClazzesController::UR_NOT_THE_TEACHER, FixtureConstants::sectionTypical, $allSectionsForThisTeacherAndSemester);

        // 2.4 admin   GET /clazzes/add?section_id=n
        //     An admin can do this but the select list is still populated with the same sections as for
        //     a teacher.
        $this->tstAddGet(FixtureConstants::userAndyAdminUsername, FixtureConstants::userAndyAdminPw, 200, null, FixtureConstants::sectionTypical, $allSectionsForThisTeacherAndSemester);

        // 3. Negative tests. Requests that should _not_ get to the controller.

        // 3.1 Test that users who do not have correct roles will get redirected to the home page.
        $this->tstAddGet(FixtureConstants::userArnoldAdvisorUsername, FixtureConstants::userArnoldAdvisorPw, 302, null, FixtureConstants::sectionTypical, $allSectionsForThisTeacherAndSemester);
        $this->tstAddGet(FixtureConstants::userSallyStudentUsername, FixtureConstants::userSallyStudentPw, 302, null, FixtureConstants::sectionTypical, $allSectionsForThisTeacherAndSemester);

        // 3.2 Test that unauthenticated users will get redirected to the login url.
        $this->session(['Auth' => null]);
        $this->get('/clazzes/add');
        $this->assertResponseCode(302);
        $this->assertRedirect('/users/login');
    }

    private function tstAddGET($username, $password, $expectedResponseCode, $expectedErrorMessage, $section_id = null, $allSectionsForThisTeacherAndSemester = [])
    {

        // 1. Attempt to login.
        $credentials = ['username' => $username, 'password' => $password];
        $this->post('/users/login', $credentials);
        $authUser = $this->_controller->Auth->user();
        $this->session(['Auth' => ['User' => $authUser]]);

        // 2. Submit request...
        if (is_null($section_id)) {
            $this->get('/clazzes/add');
        } else {
            $this->get('/clazzes/add?section_id=' . $section_id);
        }

        // 3. Examine response.
        $this->assertResponseCode($expectedResponseCode);
        switch ($expectedResponseCode) {
            case 200:  // All is well... keep on truckin'
                $this->assertNoRedirect();
                break;
            case 302:   // Authenticated but unauthorized user. Redirect to home.
                $this->assertRedirect('/');
                return;
            case 400:   // you need a section_id parameter
                $this->assertResponseContains($expectedErrorMessage);
                return;
            default:
                $this->fail('Unexpected response');
        }

        // 4. Parse the html from the response
        /* @var \simple_html_dom_node $html */
        $html = str_get_html($this->_response->body());

        // 5. Ensure that the correct form exists
        /* @var \simple_html_dom_node $form */
        $form = $html->find('form#ClazzAddForm', 0);
        $this->assertNotNull($form);

        // 6. Now inspect the fields on the form.  We want to know that:
        // A. The correct fields are there and no other fields.
        // B. The fields have correct values. This includes verifying that select
        //    lists contain options.
        //
        //  The actual order that the fields are listed on the form is hereby deemed unimportant.

        // 6.1 These are counts of the select and input fields on the form.  They
        // are presently unaccounted for.
        $unknownSelectCnt = count($form->find('select'));
        $unknownInputCnt = count($form->find('input'));

        // 6.2 Look for the hidden POST input
        if ($this->lookForHiddenInput($form)) $unknownInputCnt--;

        // 6.3 test the ClazzSectionId select.
        // shouldn't ever be null
        if ($this->tstSectionIdSelect($form, $section_id, count($allSectionsForThisTeacherAndSemester) + 1)) $unknownSelectCnt--;

        // 6.4 Ensure that there's an input field for event_datetime, of type text, and that it is empty
        if ($this->inputCheckerA($form, 'input#ClazzDatetime')) $unknownInputCnt--;

        // 6.5 Ensure that there's an input field for comments, of type text, and that it is empty
        if ($this->inputCheckerA($form, 'input#ClazzComments')) $unknownInputCnt--;

        // 7. Have all the input, select, and Atags been accounted for?
        $this->expectedInputsSelectsAtagsFound($unknownInputCnt, $unknownSelectCnt, $html, 'div#ClazzesAdd');

        // 8. Verify no flash message
        $flash = $this->_controller->request->session()->read("Flash");
        $this->assertNull($flash);
    }
}
