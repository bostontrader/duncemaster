<?php
namespace App\Controller;

class TplansController extends AppController {

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $tplan = $this->Tplans->newEntity();
        if ($this->request->is('post')) {
            $tplan = $this->Tplans->patchEntity($tplan, $this->request->data);
            if ($this->Tplans->save($tplan)) {
                //$this->Flash->success(__('The tplan has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The tplan could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('tplan'));
        return null;
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $tplan = $this->Tplans->get($id);
        if ($this->Tplans->delete($tplan)) {
            //$this->Flash->success(__('The tplan has been deleted.'));
            //} else {
            //$this->Flash->error(__('The tplan could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);
        $tplan = $this->Tplans->get($id,['contain' => 'TplanElements']);
        if ($this->request->is(['put'])) {
            $tplan = $this->Tplans->patchEntity($tplan, $this->request->data);
            if ($this->Tplans->save($tplan)) {
                //$this->Flash->success(__('The tplan has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The tplan could not be saved. Please, try again.'));
            }
        }
        $this->set('tplan_elements',$tplan->tplan_elements);
        $this->set(compact('tplan'));
        return null;
    }

    public function index() {
        $this->request->allowMethod(['get']);
        $this->set('tplans', $this->Tplans->find('all'));
    }

    // This function will produce PDF output to display a teaching plan.
    // It will emit individual pages, sequentially, with the understanding that these
    // pages will be printed four per sheet of paper (two pages, side-by-side, on each sheet),
    // in booklet form.  Hence the quantity of document pages should be a multiple of 4.
    public function pdf($id = null) {

        // 1. Get started
        require('tcpdf.php');

        // The primary method of specifying position is to measure mm from the origin,
        // where the origin is at the upper-left corner of the paper, moving right increases x
        // and moving down increases y.

        $pdf = new \tcpdf('L','mm','A3');
        $pdf->SetFont('cid0cs', '', 16, '', true);

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();


        // 1. Page 1, The cover

        // 2. The Plan Elements

        // The Plan Elements are each printed across one side of two consecutive pages.

        // Each set of two-pages contains 5 Plan Elements, with the exception of the last set.

        // The last set of two-pages only contains 4 Plan Elements, with the area normally containing
        // the 5th element, on the left page, being blank, and the same area on the right page
        // containing date and signature.

        // Two sets of two-pages, containing 10 Plan Elements, can be printed on a single sheet of paper.
        // One set on the front, and one set on the back.
        //
        // Upon careful reflection we can determine that:
        // 1. Any plan containing <=4 plan elements can be fully printed on a single sheet of paper.
        // 2. Each additional sheet of paper will accommodate up to 10 additional plan elements.

        // Our basic strategy will be to determine how many extra sheets of paper are required
        // and then emit 10 (plan elements or blank elements) to fill each extra sheet.
        // Then continue to emit 10 () to fill the final set of two-pages.
        $tplanElementCnt=4;
        $extraSheetCnt=intval(($tplanElementCnt+5)/10);

        while($extraSheetCnt>0) {
            // emit the next 10 (plan elements or blank elements)
            $extraSheetCnt--;
        }

        // Emit the next 4 (plan elements or blank elements)
        // Emit the signature block

        // 2. Rear cover





        // Draw the main outerbox
        $offsetX=5;
        $offsetY=5;
        $leftX = 0;
        $rightX = 154;
        $topY = 0;
        $bottomY = 219;
        $pdf->SetLineWidth(0.5);
        $pdf->Line($leftX+$offsetX, $topY+$offsetY,   $leftX+$offsetX, $bottomY+$offsetY); // v
        $pdf->Line($rightX+$offsetX,$topY+$offsetY,   $rightX+$offsetX,$bottomY+$offsetY); // v
        $pdf->Line($leftX+$offsetX, $topY+$offsetY,   $rightX+$offsetX,$topY+$offsetY); // h
        $pdf->Line($leftX+$offsetX, $bottomY+$offsetY,$rightX+$offsetX,$bottomY+$offsetY); // h

        // Draw the vertical lines
        $pdf->Line($leftX+18+$offsetX, $topY+$offsetY,   $leftX+18+$offsetX, $bottomY+$offsetY); // v
        $pdf->Line($leftX+41+$offsetX, $topY+$offsetY,   $leftX+41+$offsetX, $bottomY+$offsetY); // v
        $pdf->Line($leftX+89+$offsetX, $topY+$offsetY,   $leftX+89+$offsetX, $bottomY+$offsetY); // v
        $pdf->Line($leftX+137+$offsetX, $topY+$offsetY,   $leftX+137+$offsetX, $bottomY+$offsetY); // v

        // Draw the horizontal lines
        $pdf->Line($leftX+$offsetX, $topY+37+$offsetY,   $rightX+$offsetX,$topY+37+$offsetY); // h
        $pdf->Line($leftX+$offsetX, $topY+74+$offsetY,   $rightX+$offsetX,$topY+74+$offsetY); // h
        $pdf->Line($leftX+$offsetX, $topY+110+$offsetY,   $rightX+$offsetX,$topY+110+$offsetY); // h
        $pdf->Line($leftX+$offsetX, $topY+147+$offsetY,   $rightX+$offsetX,$topY+147+$offsetY); // h
        $pdf->Line($leftX+$offsetX, $topY+183+$offsetY,   $rightX+$offsetX,$topY+183+$offsetY); // h


        // Draw the main outerbox
        $offsetX=170;
        $offsetY=5;
        $leftX = 0;
        $rightX = 154;
        $topY = 0;
        $bottomY = 219;
        $pdf->SetLineWidth(0.5);
        $pdf->Line($leftX+$offsetX, $topY+$offsetY,   $leftX+$offsetX, $bottomY+$offsetY); // v
        $pdf->Line($rightX+$offsetX,$topY+$offsetY,   $rightX+$offsetX,$bottomY+$offsetY); // v
        $pdf->Line($leftX+$offsetX, $topY+$offsetY,   $rightX+$offsetX,$topY+$offsetY); // h
        $pdf->Line($leftX+$offsetX, $bottomY+$offsetY,$rightX+$offsetX,$bottomY+$offsetY); // h

        // Draw the vertical lines
        $pdf->Line($leftX+60+$offsetX, $topY+$offsetY,   $leftX+60+$offsetX, $bottomY+$offsetY); // v
        $pdf->Line($leftX+121+$offsetX, $topY+$offsetY,   $leftX+121+$offsetX, $bottomY+$offsetY); // v
        $pdf->Line($leftX+137+$offsetX, $topY+$offsetY,   $leftX+137+$offsetX, $bottomY+$offsetY); // v

        // Draw the horizontal lines
        $pdf->Line($leftX+$offsetX, $topY+37+$offsetY,   $rightX+$offsetX,$topY+37+$offsetY); // h
        $pdf->Line($leftX+$offsetX, $topY+74+$offsetY,   $rightX+$offsetX,$topY+74+$offsetY); // h
        $pdf->Line($leftX+$offsetX, $topY+110+$offsetY,   $rightX+$offsetX,$topY+110+$offsetY); // h
        $pdf->Line($leftX+$offsetX, $topY+147+$offsetY,   $rightX+$offsetX,$topY+147+$offsetY); // h
        $pdf->Line($leftX+$offsetX, $topY+183+$offsetY,   $rightX+$offsetX,$topY+183+$offsetY); // h






        // 2. Lǚyóu zhíyè xuéyuàn kǎopíng dēngjì biǎo
        // Tourism College Evaluation Registration Form
        $pdf->SetFontSize(22);
        $pdf->SetXY(100,10);
        //$pdf->Cell(100,0,'山东旅游职业学院考评登记表',0,0,'C');

        $pdf->AddPage();

        // Draw the main outerbox
        $leftX = 20;
        $rightX = 150;
        $topY = 30;
        $bottomY = 191;
        $pdf->SetLineWidth(0.5);
        $pdf->Line($leftX,$topY,$leftX,$bottomY); // v
        $pdf->Line($rightX,$topY,$rightX,$bottomY); // v
        $pdf->Line($leftX,$topY,$rightX,$topY); // h
        $pdf->Line($leftX,$bottomY,$rightX,$bottomY); // h

        // Draw the main outerbox
        $leftX += 170;
        $rightX += 170;
        //$topY = 30;
        //$bottomY = 191;
        //$pdf->SetLineWidth(0.5);
        $pdf->Line($leftX,$topY,$leftX,$bottomY); // v
        $pdf->Line($rightX,$topY,$rightX,$bottomY); // v
        $pdf->Line($leftX,$topY,$rightX,$topY); // h
        $pdf->Line($leftX,$bottomY,$rightX,$bottomY); // h


        // 3. xue qi semester
        $pdf->SetFontSize(12);
        $pdf->SetXY(236,16);
        //$pdf->Cell(0,0,'学期:');
        $pdf->SetXY(249,15);
        //$pdf->Cell(0,0,$section->semester->nickname);
        $pdf->Line(248,21,273,21); // h

        // 4.
        /*$pdf->SetXY(30,22);
        $pdf->SetFontSize(17);
        $pdf->Cell(0,0,'出勤,作业');

        $pdf->SetFontSize(14);
        $pdf->SetXY(57,24);
        $pdf->Cell(0,0,'(注: 事假: X 旷课: Ø 病假: / 迟到: O 早退: + 迟到又早退: ⊕ 公假: ∆ 正常或完成作业: ✓ , 未完成作业: X)');

        // 5. zhōu cì week
        $pdf->SetFontSize(12);
        $pdf->SetXY(32,30);
        $pdf->Cell(0,0,'周次');

        // 6. Xùhào No.
        $pdf->SetXY(19,37);
        $pdf->Cell(0,0,'序');
        $pdf->SetXY(19,42);
        $pdf->Cell(0,0,'号');

        // 7. xué hào Student Id
        $pdf->SetXY(26,40);
        $pdf->Cell(0,0,'学号');

        // 8. xìngmíng full name
        $pdf->SetXY(39,40);
        $pdf->Cell(0,0,'姓名');

        // 9. Kèchéng míngchēng Course title
        $pdf->SetXY(26,194);
        $pdf->Cell(0,0,'课程名称:');
        $pdf->SetXY(48,193);
        $pdf->Cell(0,0,$section->subject->title);
        $pdf->Line(47,199,80,199); // h

        // 10.Bānjí class
        $pdf->SetXY(99,194);
        $pdf->Cell(0,0,'班级:');
        $pdf->SetXY(112,193);
        $n=$section->cohort->nickname;
        $pdf->Cell(0,0,$n);
        $pdf->Line(111,199,136,199); // h

        // 11.Rènkè lǎoshī Instructor
        $pdf->SetXY(162,194);
        $pdf->Cell(0,0,'任课老师:');
        $pdf->SetXY(184,193);
        $pdf->Cell(0,0,$section->teacher->fam_name);
        $pdf->Line(183,199,209,199); // h

        // In addition to the fundamental system of positioning, as described
        // earlier, we also use a 2nd level positioning system.
        //
        // The main body of the form contains an array of 30 rows x 27 columns.
        // As an aid to the subsequent population of this form, we will build two
        // data structures $cx and $cy which together contain the level 1 coordinates (x,y in mm) for
        // each of the level two elements. We can then print any desired element using
        // this higher level of abstraction.
        //
        // $c is structured as follows:
        // $cx['a'], $cx['b'], $cx['c'],
        // $cx['weeks'][w]['a'] or ['b'] where w = 0-11
        // where the content is a numeric x value
        //
        // $c[y] where y = 0 to 29
        // where the content is a numeric y value
        //

        // 12. Draw the main outerbox
        $leftX = 19;
        $rightX = 279;
        $topY = 30;
        $bottomY = 191;
        $pdf->SetLineWidth(0.5);
        $pdf->Line($leftX,$topY,$leftX,$bottomY); // v
        $pdf->Line($rightX,$topY,$rightX,$bottomY); // v
        $pdf->Line($leftX,$topY,$rightX,$topY); // h
        $pdf->Line($leftX,$bottomY,$rightX,$bottomY); // h
        $cx['a']=$leftX;

        // Left most inner vertical lines
        $pdf->SetLineWidth(0.1);
        $cx['b']=26;
        $pdf->Line($cx['b'],35,$cx['b'],$bottomY); // v
        $cx['c']=36;
        $pdf->Line($cx['c'],35,$cx['c'],$bottomY); // v

        // Top most inner lines
        $pdf->Line($leftX,35,$rightX,35); // h
        $pdf->Line(54,45,$rightX,45); // h



        // The horizontal lines
        for($y=0, $yidx=0; $y<138; $y+=4.71) {
            $y1=$y+49.5;
            $cy[$yidx++]=$y1;
            $pdf->Line($leftX,$y1,$rightX,$y1); // h
        }

        // Vertical lines
        $priorX1=0;
        for($x=0, $widx=0; $x<225; $x+=18.8) {
            $x1=$x+54;
            $cx['weeks'][$widx]['a']=$x1;
            $cx['weeks'][$widx]['b']=$x1+9.4;
            $pdf->Line($x1,$topY,$x1,$bottomY); // v
            $pdf->Line($x1+9.4,$topY+15,$x1+9.4,$bottomY); // v
            $widx++;

            $pdf->SetXY($x1,$topY);
            $pdf->Cell(18.8,0,"第 $widx 周",0,0,'C');


            if($x>0) {
                $pdf->Line($priorX1,45,$x1,35); // sloped
            }
            $priorX1=$x1;
        }
        $pdf->Line($priorX1,45,$rightX,35); // sloped

        // WARNING: SQL Injection risk!
        // This query collects the students names and sids, in sid order. Other queries collect
        // other info, also in sid order. Be careful to ensure that each query contains
        // exactly the same students.
        // |-------------------|-------------------|--------------|
        // | students.fam_name | students.giv_name | students.sid |
        // section 2, cohort 3*/


        /* @var \Cake\Database\Connection $connection */
        //$connection = ConnectionManager::get('default');
        //$query = "SELECT sid, fam_name, giv_name from students
            //left join cohorts  on students.cohort_id=cohorts.id
            //left join sections on cohorts.id=sections.cohort_id
            //where sections.id=$id order by sid";
        //$classRoster = $connection->execute($query)->fetchAll('assoc');


        // This query collects attendance info, in sid order. Other queries collect
        // other info, also in sid order. Be careful to ensure that each query contains
        // exactly the same students.
        // |--------------|--|
        // | students.sid |
        /* @var \Cake\Database\Connection $connection */
        /*$connection = ConnectionManager::get('default');
        /*$query = "select students.id as student_id, students.sort, students.sid, students.giv_name, students.fam_name, students.phonetic_name, interactions.itype_id, cohorts.id as cohort_id, sections.id as section_id, clazzes.id as clazz_id
                from students
                left join cohorts on students.cohort_id = cohorts.id
                left join sections on sections.cohort_id = cohorts.id
                left join interactions on interactions.clazz_id=clazzes.id and interactions.student_id=students.id and interactions.itype_id=".ItypesController::ATTEND." where clazzes.id=".$clazz_id.
            " order by sort";

        $attendResults = $connection->execute($query)->fetchAll('assoc');*/

        // Fill in the form
        /*foreach($cy as $yidx=>$y2) {
            $pdf->SetXY($cx['a'],$y2);
            $pdf->Cell(7,0,$yidx+1,0,0,'C');    // sequence no

            $pdf->SetXY($cx['b']+1.0,$y2);
            $sid=substr($classRoster[$yidx]['sid'],-4);
            $pdf->Cell(10,0,$sid,0,0,'R');

            $pdf->SetXY($cx['c'],$y2);
            $fullName=$classRoster[$yidx]['fam_name'].$classRoster[$yidx]['giv_name'];
            $pdf->Cell(20,0,$fullName,0,0,'L');

            for($i=0; $i<=11; $i++) {
                $pdf->SetXY($cx['weeks'][$i]['a'],$y2);
                //$pdf->Cell(9,0,'aaa',0,0,'C');

                $pdf->SetXY($cx['weeks'][$i]['b'],$y2);
                //$pdf->Cell(9,0,'bbb',0,0,'C');
            }
        }*/


        $pdf->Output();
        $this->response->type('application/pdf');
        return $this->response;
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $tplan = $this->Tplans->get($id,['contain' => ['TplanElements']]);
        $this->set('tplan', $tplan);
        $this->set('tplan_elements',$tplan->tplan_elements);
    }
}
