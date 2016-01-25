<?php
namespace App\Test\TestCase\Controller\Clazzes;

use App\Controller\ClazzesController;
use App\Test\Fixture\FixtureConstants;
use Cake\Datasource\ConnectionManager;

class ClazzesControllerTestAddPOST extends ClazzesControllerTest {

    public function setUp() {
        parent::setUp();
    }

    /**
     * POST /clazzes/add?section_id=n
     * Saves a new clazz.
     *
     * Only admin or teacher can access this method. A teacher could modify
     * the section_id so ensure that a teacher cannot change the section_id
     * to that of another teacher.
     */

    public function testAddPOST() {

        // 1. Build a list of all sections that have the same semester and teacher
        // as the given typical section
        //$query = "SELECT DISTINCT b.id
            //FROM sections AS a, sections AS b
            //WHERE a.semester_id=b.semester_id
            //AND a.teacher_id=b.teacher_id
            //AND a.id=" . FixtureConstants::sectionTypical;

        /* @var \Cake\Database\Connection $connection */
        //$connection = ConnectionManager::get('default');
        //$allSectionsForThisTeacherAndSemester = $connection->execute($query)->fetchAll('assoc');

        // 2. Positive tests. Test of functionality that should be handled by the controller.
        // 2.1 admin   POST /clazzes/add  Success
        $this->tstAddPOST(
            FixtureConstants::userAndyAdminUsername, FixtureConstants::userAndyAdminPw,
            $this->clazzesFixture->newClazzRecord, 302, ClazzesController::CLAZZ_SAVED
        );

        // 2.2 teacher1 POST /clazzes/add Success
        //     Note: newClazzRecord has a section that has a teacher. Make sure that said
        //     teacher is userTommyTeacher.
        $this->tstAddPOST(
            FixtureConstants::userTommyTeacherUsername, FixtureConstants::userTommyTeacherPw,
            $this->clazzesFixture->newClazzRecord, 302, ClazzesController::CLAZZ_SAVED
        );

        // 2.3 teacher2 POST /clazzes/add Fail
        //     Note: newClazzRecordFail has a section that has a teacher. Make sure that said
        //     teacher is _not_ userTommyTeacher.
        $this->tstAddPOST(
            FixtureConstants::userTerryTeacherUsername, FixtureConstants::userTerryTeacherPw,
            $this->clazzesFixture->newClazzRecord, 200, ClazzesController::UR_NOT_THE_TEACHER
        );

        // 3. Negative tests. Requests that should _not_ get to the controller.

        // 3.1 Test that users who do not have correct roles will get redirected to the home page.
        $this->tstAddPOST(FixtureConstants::userArnoldAdvisorUsername, FixtureConstants::userArnoldAdvisorPw, 302, null, FixtureConstants::sectionTypical, $allSectionsForThisTeacherAndSemester);
        $this->tstAddPOST(FixtureConstants::userSallyStudentUsername, FixtureConstants::userSallyStudentPw, 302, null, FixtureConstants::sectionTypical, $allSectionsForThisTeacherAndSemester);

        // 3.2 Test that unauthenticated users will get redirected to the login url.
        $this->session(['Auth' => null]);
        $this->get('/clazzes/add');
        $this->assertResponseCode(302);
        $this->assertRedirect('/users/login');
    }

    private function tstAddPOST($username, $password, $newRecord, $expectedResponseCode, $expectedErrorMessage, $allSectionsForThisTeacherAndSemester = []) {

        // 1. Attempt to login.
        $credentials = ['username' => $username, 'password' => $password];
        $this->post('/users/login', $credentials);
        $authUser = $this->_controller->Auth->user();
        $this->session(['Auth' => ['User' => $authUser]]);

        // 2. Submit request...
        //if (is_null($section_id)) {
            $this->post('/clazzes/add',$newRecord);
        //} else {
            //$this->get('/clazzes/add?section_id=' . $section_id);
        //}

        // 3. Examine response.
        $this->assertResponseCode($expectedResponseCode);
        switch ($expectedResponseCode) {
            //case 200:  // All is well... keep on truckin'
                //$this->assertNoRedirect();
                //break;
            case 302:
                if(is_null())
                $this->assertRedirect('/clazzes');

                // Verify the flash message
                $flash = $this->_controller->request->session()->read("Flash");
                $n = $flash['flash'][0]['message'];
                $this->assertEquals(ClazzesController::CLAZZ_SAVED, $n);

                return;
            case 400:
                $this->assertResponseContains($expectedErrorMessage);
                return;
            default:
                $this->fail('Unexpected response');
        }

        // 1. Login, POST a suitable record to the url, redirect, and return the record just
        // posted, as read from the db.
        //$fixtureRecord=$this->clazzesFixture->newClazzRecord;
        //$fromDbRecord=$this->genericAddPostProlog(
            //FixtureConstants::userAndyAdminId,
            //'/clazzes/add', $fixtureRecord,
            //'/clazzes', $this->clazzes
        //);

        //$this->fakeLogin($user_id);
        //$this->post($url, $newRecord);
        //$this->assertResponseSuccess(); // 2xx, 3xx
        //$this->assertRedirect( $redirect_url );

        // Now retrieve the newly written record.
        $connection = ConnectionManager::get('test');
        $query=new Query($connection,$table);
        $fromDbRecord=$query->find('all')->order(['id' => 'DESC'])->first();

        //return $fromDbRecord;




        // 2. Now validate that record.
        $this->assertEquals($fromDbRecord['section_id'],$fixtureRecord['section_id']);
        $this->assertEquals($fromDbRecord['event_datetime'],$fixtureRecord['event_datetime']);
        $this->assertEquals($fromDbRecord['comments'],$fixtureRecord['comments']);
    }
}
