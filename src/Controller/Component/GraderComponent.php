<?php
namespace App\Controller\Component;
use App\Controller\ItypesController;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

class GraderComponent extends Component {


    // Get the grades for a given semester and teacher.
    // For all sections with a given semester and teacher...
    //     Get the grades for a particular section.
    //     (Given a section, we know the semester and teacher.)
    //

    private function itypeCounter($interactions, $itype_id, $section_id, $student_id) {
        $query = $interactions->
            find()->
            contain('Clazzes.Sections')->
            where(
                [
                    'section_id' => $section_id,
                    'student_id' => $student_id,
                    'itype_id' => $itype_id
                ]
            );
        return $query->count();
    }

    // Get the grades for a particular student from a particular section.
    public function getGradeInfo($section_id = null, $student_id = null) {

        // 1. How many times has this particular section met?
        $clazzes = TableRegistry::get('Clazzes');
        $interactions = TableRegistry::get('Interactions');
        $query = $clazzes->find()->where(['section_id' => $section_id]);
        $clazzCnt=$query->count();

        // 2. How many times has the student attended that section?
        $attendCnt=$this->itypeCounter($interactions, ItypesController::ATTEND, $section_id, $student_id);

        // 3. How many excused absences does the student have?
        $excusedAbsenceCnt=$this->itypeCounter($interactions, ItypesController::EXCUSED, $section_id, $student_id);

        // 4. How many times has the student been ejected from class?
        $ejectedFromClassCnt=$this->itypeCounter($interactions, ItypesController::EJECT, $section_id, $student_id);

        // 5. How many times has the student left the class?
        $leftClassCnt=$this->itypeCounter($interactions, ItypesController::LEAVE, $section_id, $student_id);

        // A = sum(2+-5) / 1
        if($clazzCnt == 0) {
            $scoreAttendance=0;
        } else {
            $scoreAttendance=($attendCnt+$excusedAbsenceCnt-$ejectedFromClassCnt-$leftClassCnt)/$clazzCnt;
        }
        // What is the average class participation ?
        // select avg from interactions where student_id = student and section_id = section and code=participation

        // Homework = cp
        // What is the final exam?
        // select from interactions where student_id = student and section_id = section and code=final exam

        //$Sections = TableRegistry::get('Sections');
        //$sections_list = $Sections->find('list');

        return [
            'clazzCnt'=>$clazzCnt,
            'attendCnt'=>$attendCnt,
            'excusedAbsenceCnt'=>$excusedAbsenceCnt,
            'ejectedFromClassCnt'=>$ejectedFromClassCnt,
            'leftClassCnt'=>$leftClassCnt,
            'scoreAttendance'=>$scoreAttendance,
            'classroom_participation'=>[1,2,3],
            'final_exam'=>8
        ];
    }

}