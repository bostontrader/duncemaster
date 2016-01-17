<?php

namespace App\Test\Fixture;

/**
 * Class FixtureConstants
 * @package App\Test\Fixture

 * The test fixtures are extracted from a fixture db. The fixture db is maintained using the
 * actual application. The actual data required to support the byzantine collection of test cases
 * is just too complicated to assemble by hand. Hence these contortions.
 *
 * After using the app to tweak the fixture db, we will frequently want to reference specific records
 * in our testing. In this class, we tediously maintain constants pointing to these specific records,
 * using the value of the primary key.
 * Making these constants point to the correct records is of critical importance.
 */

class FixtureConstants {

    const clazzTypical = 1;

    const cohortTypical = 1;

    //const interaction1_id = 1;

    //const itypeAttend_id = 1;
    //const itypeEject_id = 2;
    //const itypeLeave_id = 3;
    //const itypeParticipate_id = 4;
    //const itypeExcusedAbsence_id = 5;

    const majorTypical = 1;

    const roleAdminId = 1;
    const roleAdvisorId = 2;
    const roleStudentId = 3;
    const roleTeacherId = 4;

    const sectionTypical = 1;

    const semesterTypical = 1;

    const studentTypical = 10;
    const studentCohortIdNull = 79;
    const studentUserIdNull = 142;

    const subjectTypical = 1;

    const teacherTypical = 1;
    const teacherUserIdNull = 2;

    const tplanTypical =1;

    //const tplan_element1_id =1;
    //const tplan_element2_id =2;

    const userAndyAdminId = 45;
    const userAndyAdminUsername = 'AndyAdmin';
    const userAndyAdminPw = 'passwordAndyAdmin';

    const userArnoldAdvisorId = 46;
    const userArnoldAdvisorUsername = 'ArnoldAdvisor';
    const userArnoldAdvisorPw = 'passwordArnoldAdvisor';

    const userSallyStudentId = 47;
    const userSallyStudentUsername = 'SallyStudent';
    const userSallyStudentPw = 'passwordSallyStudent';

    const userTommyTeacherId = 48;
    const userTommyTeacherUsername = 'TommyTeacher';
    const userTommyTeacherPw = 'passwordTommyTeacher';

    // TammyTeacherAndStudent is a teacher and a student
    const userTammyTeacherAndStudentId = 49;
    const userTammyTeacherAndStudentUsername = 'TammyTeacherAndStudent';
    const userTammyTeacherAndStudentPw = 'passwordTammyTeacherAndStudent';

    // When computing scores we need to very carefully weave the above records together such that
    // the following grading results are produced (and thus testable)

    // What are the grading stats for one student and one section?
    const studentToGrade = FixtureConstants::studentTypical;
    const sectionToGrade = FixtureConstants::sectionTypical;

    // How many times did this class meet for this section?
    const clazzCnt = 16;
    const attendCnt = 0;
    const excusedAbsenceCnt = 0;
    const ejectedFromClassCnt = 0;
    const leftClassCnt = 0;
}