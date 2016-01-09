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

        // 1. Attedance
        // 1.1 How many times has this particular section met?
        $clazzes = TableRegistry::get('Clazzes');
        $interactions = TableRegistry::get('Interactions');
        $query = $clazzes->find()->where(['section_id' => $section_id]);
        $clazzCnt=$query->count();

        // 1.2 How many times has the student attended that section?
        $attendCnt=$this->itypeCounter($interactions, ItypesController::ATTEND, $section_id, $student_id);

        // 1.3 How many excused absences does the student have?
        $excusedAbsenceCnt=$this->itypeCounter($interactions, ItypesController::EXCUSED, $section_id, $student_id);

        // 1.4 How many times has the student been ejected from class?
        $ejectedFromClassCnt=$this->itypeCounter($interactions, ItypesController::EJECT, $section_id, $student_id);

        // 1.5 How many times has the student left the class?
        $leftClassCnt=$this->itypeCounter($interactions, ItypesController::LEAVE, $section_id, $student_id);

        // A = sum(2+-5) / 1
        if($clazzCnt == 0) {
            $scoreAttendance=0;
        } else {
            $scoreAttendance=($attendCnt+$excusedAbsenceCnt-$ejectedFromClassCnt-$leftClassCnt)/$clazzCnt;
        }

        // 2. What is the average class participation ?
        $query=$interactions->find('all')
            ->select(['avg' => $query->func()->avg('participate')])
            ->leftJoinWith('Clazzes.Sections')
            ->where(
                [
                    'section_id' => $section_id,
                    'student_id' => $student_id,
                    'itype_id' => ItypesController::PARTICIPATE
                ]);

        $c = $query->execute($query)->fetchAll('assoc');


        return [
            'clazzCnt'=>$clazzCnt,
            'attendCnt'=>$attendCnt,
            'excusedAbsenceCnt'=>$excusedAbsenceCnt,
            'ejectedFromClassCnt'=>$ejectedFromClassCnt,
            'leftClassCnt'=>$leftClassCnt,
            'scoreAttendance'=>$scoreAttendance,
            'scoreParticipation'=>$c[0]['avg'],
            'scoreHomework'=>$c[0]['avg'],
            'final_exam'=>$c[0]['avg']
        ];
    }

}