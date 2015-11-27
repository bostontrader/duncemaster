<?php
namespace App\Controller\Component;
use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

class GraderComponent extends Component {

    // Get the grades for a given semester and teacher.
    // For all sections with a given semester and teacher...
    //     Get the grades for a particular section.
    //     (Given a section, we know the semester and teacher.)
    //

    // Get the grades for a particular student from a particular section.
    public function getGradeInfo($section_id = null, $student_id = null) {

        // 1. How many times has this particular section met?
        $clazzes = TableRegistry::get('Clazzes');
        $interactions = TableRegistry::get('Interactions');
        $query = $clazzes->find()->where(['section_id' => $section_id]);
        $clazzCnt=$query->count();

        // 2. How many times has the student attended that section?
        $query = $interactions->find()->contain('Clazzes.Sections')->where(['section_id' => $section_id,'student_id' => $student_id]);
        $attendCnt=$query->count();

        // 3. How many excused absences does the student have?
        // select count(*) from interactions where student_id = student and section_id = section and code=excused absence
        $query = $interactions->find()->contain('Clazzes.Sections');
        $excusedAbsenceCnt=$query->count();

        // 4. How many times has the student been ejected from class?
        // select count(*) from interactions where student_id = student and section_id = section and code=ejected
        $query = $interactions->find()->contain('Clazzes.Sections');
        $ejectedFromClassCnt=$query->count();

        // 5. How many times has the student voluntarily left class early?
        // select count(*) from interactions where student_id = student and section_id = section and code=ejected
        $query = $interactions->find()->contain('Clazzes.Sections');
        $leftClassEarlyCnt=$query->count();

        // A = 2 + 3 - 4 / 1
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
            'leftClassEarlyCnt'=>$leftClassEarlyCnt,
            'classroom_participation'=>[1,2,3],
            'final_exam'=>8
        ];
    }

}