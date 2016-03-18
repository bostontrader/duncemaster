<?php
namespace App\Controller;

use Cake\ORM\TableRegistry;

class TplansController extends AppController {


    // Nothing is authorized unless a controller says so.
    // Admin and teachers are always authorized. It is the responsibility
    // of this controller to restrict access to info for a teacher
    // to only his information and no other teacher.
    public function isAuthorized($userArray) {
        return $this->isAdmin || $this->isTeacher;
    }

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

    /**
     * @param null $id
     * @return \Cake\Network\Response|null
     *
     * This function will produce PDF output to display a Teaching Plan.
     *
     * Said Plan is composed of the following sections:
     * 1. A cover sheet.
     * 2. Zero or more full-sized plan element listings (PEL).
     * 3. One short-form PEL.
     * 4. A rear cover sheet.
     *
     * A plan starts as a sequence of A4 sized pages.  Henceforth a "page"
     * will specifically refer to an A4 sized page.
     *
     * The front and rear cover sheets are ordinary pages.
     *
     * Each PEL is a sequence of two pages, a left-side and a right-side,
     * intended to be viewed side-by-side.
     * A PEL contains up to 5 Plan Elements, listed from top-to-bottom, with
     * the exception of the final short-form PEL.
     *
     * The short-form PEL only contains 4 Plan Elements.  Where the 5th Element
     * would normally be printed, the short-form PEL contains a blank area for the
     * left page, and a signature block for the right page.
     *
     * Physical Paper:
     *
     * With suitable software, the pages can be printed in booklet form, on double-sized
     * paper, with four pages per physical sheet of paper, two pages on each side. This
     * closely simulates the original hand-drawn form in pre-printed booklets. The downsides
     * of this method include:
     *
     *  * We need to find large paper and a printer that can print on both sides of it.
     *  * We need to find suitable booklet printing software and expertise.
     *  * There's also quite a lot of wasted form-space.
     *
     * We can also print these pages sequentially, on the front and back of A4 paper.
     * If we do this then the PELs can be readily viewed as intended, side-by-side, simply
     * by turning the pages.
     *
     * The downside of this method is that it's such a good idea, within its narrow context,
     * and so radically different from the original form, that the Bureacratic Gods that
     * lurk in the shadows must certainly frown upon this approach.  Nevertheless, I hereby taunt fate,
     * live dangerously, and chose this method.  May the Gods of Idiocy strike me down!
     */

    public function pdf($id = null) {

        // 1. In the beginning...

        // 1.1 Obtain the data to print.

        // 1.1.1 We'll obviously need info about the teaching plan itself, as well as
        // its associated elements
        $tplan_id=$id;
        $tplan = $this->Tplans->get($tplan_id,['contain' => 'TplanElements']);

        // 1.1.2 Get the front cover info.
        // This query finds all sections that use this teaching plan. Most of the selected
        // fields should be the same, such as the course title, teacher, and teaching_hours_per_class.
        // However, the cohorts should all be different. (This would be nice to verify.)
        $tableSections=TableRegistry::get('Sections');

        $query = $tableSections->find('all')
            ->contain(['Cohorts.Majors','Semesters','Subjects','Teachers'])
            ->order('Cohorts.seq')
            ->where(['Sections.tplan_id'=>$tplan_id]);

        $cohortList = null;
        foreach($query as $tplanUser) {
            $cohortNickname=$tplanUser->cohort->nickname;
            (is_null($cohortList)) ? $cohortList=$cohortNickname : $cohortList.=','.$cohortNickname;
        }

        // Assuming all of these fields are the same, for each record,
        // then its ok to retrieve their values from the 1st record.
        // WARNING! Maybe they're not all the same!
        $n=$query->first();

        $info['subject']=$n->subject->title;
        $info['major']=$n->cohort->major->title;
        $info['cohorts']=$cohortList;
        $info['instructor']=$n->teacher->fam_name;
        $info['class_cnt']=$tplan['session_cnt'];
        $info['teaching_hrs_per_class']=$n->thours;

        // The desired semester sequence printed, is the reverse of
        // what's in the db.
        $info['semester_seq']=($n->semester->seq=1)?2:1;

        // 1.1.4. Now get the plan elements
        $info['elements']=[];
        foreach($tplan->tplan_elements as $tplanElement) {
            $element=[
                'start_thour'=>$tplanElement->start_thour,
                'stop_thour'=>$tplanElement->stop_thour,
                'col1'=>$tplanElement->col1,
                'col2'=>$tplanElement->col2,
                'col3'=>$tplanElement->col3,
                'col4'=>$tplanElement->col4,
            ];
            $info['elements'][] = $element;
        }

        // 2. Initialize the pdf
        require('tcpdf.php');

        // The primary method of specifying position is to measure mm from the origin,
        // where the origin is at the upper-left corner of the paper, moving right increases x
        // and moving down increases y.
        $pdf = new \tcpdf('P','mm','A4');
        $pdf->SetFont('cid0cs', '', 16, '', true);

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        
        // 3. Page 1, The cover
        $pdf->AddPage();
        $this->emitFrontCover($info,$pdf,10,10);

        // 4. The Plan Elements
        //
        // We can print the PELs, double-side, on ordinary paper, or we can do it
        // booklet-style on large paper. You decide.
        $bookletStyle=false;
        if($bookletStyle) {
            // Upon careful reflection we can determine that:
            // 1. Any plan containing <=4 plan elements can be fully printed on a single short-form PEL.
            // 2. Each additional sheet of paper will accommodate up to 10 additional plan elements.

            // Our basic strategy will be to determine how many extra sheets of paper are required
            // and then emit 10 (plan elements or blank elements) to fill each extra sheet.
            // Then continue to emit 10 () to fill the final set of two-pages.
            $tplanElementCnt=count($info['elements']);
            $extraSheetCnt=intval(($tplanElementCnt+5)/10); // model this in excel and see that it works

            $tplanElementIdx=0;
            while($extraSheetCnt>0) {
                // emit the next 10 (plan elements or blank elements)
                $tplanElementIdx+=$this->emitPEL($info,$pdf,$tplanElementIdx,false);
                $extraSheetCnt--;
            }

            $tplanElementIdx+=$this->emitPEL($info,$pdf,$tplanElementIdx,true);
        } else {
            // Excellent choice.  Let's print these pages, double-sided, on ordinary paper.
            $tplanElementCnt=count($info['elements']);
            $tplanElementIdx=0; // which element to print next? zero based idx.

            // Emit all the full-sized PELs, if any.
            while( ($tplanElementCnt-$tplanElementIdx) >= 5) {
                // emit the next 5 plan elements
                $tplanElementIdx+=$this->emitPEL($info,$pdf,$tplanElementIdx,false); // not last PEL
            }

            // Always emit at least one short-form PEL.
            $tplanElementIdx+=$this->emitPEL($info,$pdf,$tplanElementIdx,true); // last PEL
        }

        // 5. Rear cover
        $pdf->AddPage();
        $this->emitRearCover($pdf,17,23);

        $pdf->Output();
        $this->response->type('application/pdf');

        return $this->response;
    }

    private function emitPEL($info,$pdf,$tplanElementIdx,$lastPEL) {
        $pdf->AddPage();
        $this->emitPageLeft($info,$pdf,$tplanElementIdx,$lastPEL,5,5);
        $pdf->AddPage();
        $elementOutputCnt=$this->emitPageRight($info,$pdf,$tplanElementIdx,$lastPEL,5,5);
        return $elementOutputCnt;
    }

    // Emit a line, but apply an x,y offset to the endpoints.
    // This function may not seem to add a lot of value, but it does
    // remove a lot of clutter from the code.
    private function dmLine($pdf,$x1,$y1,$x2,$y2,$ox,$oy) {
        $pdf->Line($x1+$ox,$y1+$oy,$x2+$ox,$y2+$oy);
    }

    private function dmSetXY($pdf,$x,$y,$ox,$oy) {
        $pdf->SetXY($x+$ox,$y+$oy);
    }

    private function emitFrontCover($info,$pdf,$ox=0,$oy=0) {

        // 2.1 shandong Lǚyóu zhíyè xuéyuàn
        // Shandong College of Tourism and Hospitality
        $pdf->SetFontSize(20);
        $this->dmSetXY($pdf,44,20,$ox,$oy);
        $pdf->Cell(74,8,'山 东 旅 游 职 业 学 院',0,0,'C');

        // 2.2 jia4oxue2 ri4li4
        // Teaching plan
        $pdf->SetFontSize(39);
        $this->dmSetXY($pdf,21,37,$ox,$oy);
        $pdf->Cell(123,18,'教      学      日      历',0,0,'C');

        // 2.3 ke4che2ng mi2ngche1ng
        // Course title
        $pdf->SetFontSize(16);
        $this->dmSetXY($pdf,37,77,$ox,$oy);
        $pdf->Cell(22,8,'课程名称',0,0,'C');

        $pdf->SetFontSize(14);
        $this->dmSetXY($pdf,59,77,$ox,$oy);
        $pdf->Cell(100,8,$info['subject'],0,0,'L');

        $this->dmLine($pdf,60,84, 118,84,$ox,$oy); // h

        // 2.4 zhua1n ye4
        // Profession
        $pdf->SetFontSize(16);
        $this->dmSetXY($pdf,37,92,$ox,$oy);
        $pdf->Cell(11,8,'专业',0,0,'C');

        $pdf->SetFontSize(14);
        $this->dmSetXY($pdf,48,92,$ox,$oy);
        $pdf->Cell(100,8,$info['major'],0,0,'L');

        $this->dmLine($pdf,49,99, 82, 99,$ox,$oy); // h

        // 2.5 nia2n ji2
        // Grade aka cohorts
        $pdf->SetFontSize(16);
        $this->dmSetXY($pdf,82,92,$ox,$oy);
        $pdf->Cell(12,8,'年级',0,0,'C');

        $pdf->SetFontSize(14);
        $this->dmSetXY($pdf,95,92,$ox,$oy);
        $pdf->Cell(100,8,$info['cohorts'],0,0,'L');

        $this->dmLine($pdf,95,99, 118,99,$ox,$oy); // h

        // 2.6 zh3jia3ng jia4o shi1
        // Speaker/teacher
        $pdf->SetFontSize(12);
        $this->dmSetXY($pdf,37,102,$ox,$oy);
        $pdf->Cell(8,5,'主讲',0,0,'C');
        $this->dmSetXY($pdf,37,107,$ox,$oy);
        $pdf->Cell(8,5,'教师',0,0,'C');

        // 2.6.1 xi4ng mi2ng
        // Full name
        $pdf->SetFontSize(16);
        $this->dmSetXY($pdf,47,103,$ox,$oy);
        $pdf->Cell(11,8,'姓名',0,0,'C');

        $pdf->SetFontSize(14);
        $this->dmSetXY($pdf,57,103,$ox,$oy);
        $pdf->Cell(100,8,$info['instructor'],0,0,'L');

        $this->dmLine($pdf,58,110, 86,110,$ox,$oy); // h

        // 2.6.2 zhi2 che1ng
        // Job title
        $pdf->SetFontSize(16);
        $this->dmSetXY($pdf,87,103,$ox,$oy);
        $pdf->Cell(12,8,'职称',0,0,'C');

        $this->dmLine($pdf,99,110, 118,110,$ox,$oy); // h


        // 2.7
        $this->dmSetXY($pdf,37,133,$ox,$oy);
        $pdf->Cell(17,7,'周   数',0,0,'C');

        $this->dmSetXY($pdf,57,132,$ox,$oy);
        $pdf->Cell(20,8,$info['class_cnt'],0,0,'R');

        $this->dmLine($pdf,54,139,118,139,$ox,$oy); // h


        // 2.8
        $this->dmSetXY($pdf,37,142,$ox,$oy);
        $pdf->Cell(17,7,'讲   课',0,0,'C');
        $this->dmSetXY($pdf,57,141,$ox,$oy);
        $pdf->Cell(20,8,$info['teaching_hrs_per_class'],0,0,'R');
        $this->dmLine($pdf,54,148,105,148,$ox,$oy); // h

        $this->dmSetXY($pdf,106,142,$ox,$oy);
        $pdf->Cell(13,7,'学时',0,0,'C');


        // 2.9
        $this->dmSetXY($pdf,37,151,$ox,$oy);
        $pdf->Cell(17,7,'实习课',0,0,'C');
        $this->dmLine($pdf,54,157,105,157,$ox,$oy); // h
        $this->dmSetXY($pdf,106,151,$ox,$oy);
        $pdf->Cell(13,7,'学时',0,0,'C');


        // 2.10
        $this->dmSetXY($pdf,37,160,$ox,$oy);
        $pdf->Cell(17,7,'实   验',0,0,'C');
        $this->dmLine($pdf,54,166,105,166,$ox,$oy); // h
        $this->dmSetXY($pdf,106,160,$ox,$oy);
        $pdf->Cell(13,7,'学时',0,0,'C');


        // 2.11
        $this->dmSetXY($pdf,37,169,$ox,$oy);
        $pdf->Cell(17,7,'总   计',0,0,'C');

        $this->dmSetXY($pdf,57,169,$ox,$oy);
        $pdf->Cell(20,8,$info['teaching_hrs_per_class']*$info['class_cnt'],0,0,'R');

        $this->dmLine($pdf,54,176,105,176,$ox,$oy); // h

        $this->dmSetXY($pdf,106,169,$ox,$oy);
        $pdf->Cell(13,7,'学时',0,0,'C');


        // 2.12
        $this->dmSetXY($pdf,34,208,$ox,$oy);
        $pdf->Cell(30,7,'2015-2016',0,0,'C');
        $this->dmSetXY($pdf,80,208,$ox,$oy);
        $pdf->Cell(21,7,'学年第',0,0,'C');

        $this->dmSetXY($pdf,100,208,$ox,$oy);
        $pdf->Cell(8,8,$info['semester_seq'],0,0,'C');

        $this->dmSetXY($pdf,109,208,$ox,$oy);
        $pdf->Cell(11,7,'学期',0,0,'C');
    }

    private function emitRearCover($pdf,$ox=0,$oy=0) {

        // Draw the main outerbox
        $leftX = 0;
        $rightX = 154;
        $topY = 0;
        $bottomY = 215;
        $pdf->SetLineWidth(0.5);
        $this->dmLine($pdf,$leftX,$topY,$leftX,$bottomY,$ox,$oy); // v
        $this->dmLine($pdf,$rightX,$topY,$rightX,$bottomY,$ox,$oy); // v
        $this->dmLine($pdf,$leftX,$topY,$rightX,$topY,$ox,$oy); // h
        $this->dmLine($pdf,$leftX,$bottomY,$rightX,$bottomY,$ox,$oy); // h

        $pdf->SetFontSize(12);
        $this->dmSetXY($pdf,6,7,$ox,$oy);
        $pdf->Cell(48,5,'检查本日历完成情况的结果:',0,0,'C');

        $this->dmSetXY($pdf,24,192,$ox,$oy);
        $pdf->Cell(11,7,'检查者',0,0,'C');
        $this->dmLine($pdf,36,197,61,197,$ox,$oy); // h
        $this->dmSetXY($pdf,32,203,$ox,$oy);
        $pdf->Cell(35,7,'年         月         日',0,0,'C');

        $this->dmSetXY($pdf,87,192,$ox,$oy);
        $pdf->Cell(15,7,'主讲教师',0,0,'C');
        $this->dmLine($pdf,103,197,133,197,$ox,$oy); // h

        $this->dmSetXY($pdf,99,203,$ox,$oy);
        $pdf->Cell(35,7,'年         月         日',0,0,'C');

        $this->dmSetXY($pdf,5,215,$ox,$oy);
        $pdf->Cell(126,7,'附注: 本日历一式三份, 一份存教务处,一份存教研室, 一份讲授者保存',0,0,'C');
    }

    private function emitPageLeft($info,$pdf,$tplanElementIdx,$lastSheet,$ox=0,$oy=0) {

        $pdf->SetFontSize(10);

        // Draw the header box
        $leftX = 0;
        $rightX = 154;
        $topY = 0;
        $bottomY = 37;
        $pdf->SetLineWidth(0.5);
        $this->dmLine($pdf,$leftX, $topY,   $leftX, $bottomY,$ox,$oy); // v
        $this->dmLine($pdf,$rightX,$topY,   $rightX,$bottomY,$ox,$oy); // v
        $this->dmLine($pdf,$leftX, $topY,   $rightX,$topY,$ox,$oy); // h
        $this->dmLine($pdf,$leftX, $bottomY,$rightX,$bottomY,$ox,$oy); // h

        // Draw the vertical lines for the header
        $this->dmLine($pdf,$leftX+18, $topY,   $leftX+18, $bottomY,$ox,$oy); // v
        $this->dmLine($pdf,$leftX+41, $topY,   $leftX+41, $bottomY,$ox,$oy); // v
        $this->dmLine($pdf,$leftX+89, $topY,   $leftX+89, $bottomY,$ox,$oy); // v
        $this->dmLine($pdf,$leftX+137, $topY,   $leftX+137, $bottomY,$ox,$oy); // v

        // Labels for the header

        // Month
        // yue4fe4n
        $this->dmSetXY($pdf,7,12,$ox,$oy);
        $pdf->Cell(5,7,'月',0,0,'C');
        $this->dmSetXY($pdf,7,18,$ox,$oy);
        $pdf->Cell(5,7,'份',0,0,'C');

        // week
        // zho1u ci4
        $this->dmSetXY($pdf,23,15,$ox,$oy);
        $pdf->Cell(15,7,'周   次',0,0,'C');

        // Lecture (Syllabus chapter title)
        // jia4oxue2 da4ga1ng zha1ngjie2 ti1mu4
        $this->dmSetXY($pdf,54,12,$ox,$oy);
        $pdf->Cell(20,7,'讲    课',0,0,'C');
        $this->dmSetXY($pdf,47,18,$ox,$oy);
        $pdf->Cell(34,7,'（教学大纲章节题目）',0,0,'C');

        // Ugly hack. Zombies coming over wall. Gotta do this NOW!
        $this->dmSetXY($pdf,47,24,$ox,$oy);
        $pdf->Cell(34,7,'视听说教程-2,Stempleski',0,0,'C');


        // Experiment, recitation, classroom, discussions and other job title
        // shi2ya4n xi2ti2 ke4 ke4ta2ng ta3olu4n ji2 qi2ta1 zuo4ye4 ti2mu4
        $this->dmSetXY($pdf,95,12,$ox,$oy);
        $pdf->Cell(34,7,'实验,  习题课,  课堂',0,0,'C');
        $this->dmSetXY($pdf,95,18,$ox,$oy);
        $pdf->Cell(34,7,'讨论及其它作业题目',0,0,'C');

        // Class
        // Ke4shi2
        $this->dmSetXY($pdf,143,11,$ox,$oy);
        $pdf->Cell(5,7,'课',0,0,'C');
        $this->dmSetXY($pdf,143,21,$ox,$oy);
        $pdf->Cell(5,7,'时',0,0,'C');

        // Determine how many elements to emit.  Whether an element is filled or blank will
        // be determined later.
        $lastSheet ? $elementCnt=4 : $elementCnt=5;

        // Emit the next (plan elements or blank elements)
        $elementsPrintedCnt=0;
        $tplanElementsCnt=count($info['elements']);
        for($i=0; $i<$elementCnt; $i++) {

            // I need this again because something (I think SetXY) resets it.
            $pdf->SetLineWidth(0.5);

            // Print the box
            $offsetY2=37+$i*36;
            $this->dmLine($pdf,$leftX, $topY,   $leftX, $bottomY, $ox,$oy+$offsetY2); // v
            $this->dmLine($pdf,$rightX,$topY,   $rightX,$bottomY, $ox,$oy+$offsetY2); // v
            $this->dmLine($pdf,$leftX, $bottomY,$rightX,$bottomY, $ox,$oy+$offsetY2); // h

            // Draw the vertical lines
            $this->dmLine($pdf,$leftX+18, $topY,   $leftX+18, $bottomY, $ox,$oy+$offsetY2); // v
            $this->dmLine($pdf,$leftX+41, $topY,   $leftX+41, $bottomY, $ox,$oy+$offsetY2); // v
            $this->dmLine($pdf,$leftX+89, $topY,   $leftX+89, $bottomY, $ox,$oy+$offsetY2); // v
            $this->dmLine($pdf,$leftX+137, $topY,   $leftX+137, $bottomY, $ox,$oy+$offsetY2); // v

            // Which week?
            $this->dmSetXY($pdf,22,13,$ox,$oy+$offsetY2);
            $hz=$this->itohz($i+1);
            $pdf->Cell(13,7,'第'.$hz.'周',0,0,'C');

            $this->dmSetXY($pdf, 17, 19, $ox, $oy + $offsetY2);
            $pdf->Cell(20, 7, '从     到', 0, 0, 'C');
            $this->dmLine($pdf, 25, 24.5, 29, 24.5, $ox, $oy + $offsetY2); // h
            $this->dmLine($pdf, 34, 24.5, 38, 24.5, $ox, $oy + $offsetY2); // h

            // Now print the contents, if any
            if($tplanElementIdx<$tplanElementsCnt) {
                $element=$info['elements'][$tplanElementIdx];

                // The start and stop weeks
                $this->dmSetXY($pdf, 25, 19, $ox, $oy + $offsetY2);
                $pdf->Cell(5, 7, $element['start_thour'], 0, 0, 'R');
                $this->dmSetXY($pdf, 34, 19, $ox, $oy + $offsetY2);
                $pdf->Cell(5, 7, $element['stop_thour'], 0, 0, 'R');

                // col1
                $this->dmSetXY($pdf, 41, 1, $ox, $oy + $offsetY2);
                $pdf->MultiCell(48,0,$element['col1'],0,'L');

                // col2
                $this->dmSetXY($pdf, 89, 1, $ox, $oy + $offsetY2);
                $pdf->MultiCell(48,0,$element['col2'],0,'L');

                $elementsPrintedCnt++;
                $tplanElementIdx++;
            }

        }
        return $elementsPrintedCnt;
    }

    private function itohz($i) {
        return ['','一','二','三','四','五','六','七','八','九','十','十一','十二','十三','十四','十五','十六','十七','十八','十九','二十','二十一','二十二','二十三','二十四'][$i];
    }

    private function emitPageRight($info,$pdf,$tplanElementIdx,$lastSheet,$ox=0,$oy=0) {

        $pdf->SetFontSize(10);

        // Draw the header box
        $leftX = 0;
        $rightX = 154;
        $topY = 0;
        $bottomY = 37;
        $pdf->SetLineWidth(0.5);
        $this->dmLine($pdf,$leftX, $topY,   $leftX, $bottomY,$ox,$oy); // v
        $this->dmLine($pdf,$rightX,$topY,   $rightX,$bottomY,$ox,$oy); // v
        $this->dmLine($pdf,$leftX, $topY,   $rightX,$topY,$ox,$oy); // h
        $this->dmLine($pdf,$leftX, $bottomY,$rightX,$bottomY,$ox,$oy); // h

        // Draw the vertical lines for the header
        $this->dmLine($pdf,$leftX+60, $topY,   $leftX+60, $bottomY,$ox,$oy); // v
        $this->dmLine($pdf,$leftX+121, $topY,   $leftX+121, $bottomY,$ox,$oy); // v
        $this->dmLine($pdf,$leftX+137, $topY,   $leftX+137, $bottomY,$ox,$oy); // v

        // Labels for the header

        // Reading the main reference.
        // Yue4 du2 zhu3ya4o ca1nka3o shu1
        $this->dmSetXY($pdf,15,14,$ox,$oy);
        $pdf->Cell(27,7,'阅读主要参考书',0,0,'C');

        // Counseling form and the main content
        // fu3da3o xi2ngshi4 he2 zhu3ya4o ne4iro2ng
        $this->dmSetXY($pdf,73,14,$ox,$oy);
        $pdf->Cell(34,7,'辅导形式和主要内容',0,0,'C');

        // class
        // ke4shi2
        $this->dmSetXY($pdf,125,14,$ox,$oy);
        $pdf->Cell(10,7,'课时',0,0,'C');

        // remark
        // be4izhu4
        $this->dmSetXY($pdf,140,14,$ox,$oy);
        $pdf->Cell(11,7,'备注',0,0,'C');

        // Determine how many elements to emit.  Whether an element is filled or blank will
        // be determined later.
        $lastSheet ? $elementCnt=4 : $elementCnt=5;

        // Emit the next (plan elements or blank elements)
        $elementsPrintedCnt=0;
        $tplanElementsCnt=count($info['elements']);
        for($i=0; $i<$elementCnt; $i++) {

            // I need this again because something (I think SetXY) resets it.
            $pdf->SetLineWidth(0.5);

            // Print the box
            $offsetY2=37+$i*36;
            $this->dmLine($pdf,$leftX, $topY,   $leftX, $bottomY, $ox,$oy+$offsetY2); // v
            $this->dmLine($pdf,$rightX,$topY,   $rightX,$bottomY, $ox,$oy+$offsetY2); // v
            $this->dmLine($pdf,$leftX, $bottomY,$rightX,$bottomY, $ox,$oy+$offsetY2); // h

            // Draw the vertical lines
            $this->dmLine($pdf,$leftX+60, $topY,   $leftX+60, $bottomY, $ox,$oy+$offsetY2); // v
            $this->dmLine($pdf,$leftX+121, $topY,   $leftX+121, $bottomY, $ox,$oy+$offsetY2); // v
            $this->dmLine($pdf,$leftX+137, $topY,   $leftX+137, $bottomY, $ox,$oy+$offsetY2); // v

            // Now print the contents, if any
            //$elementsPrintedCnt=0;
            if($tplanElementIdx<$tplanElementsCnt) {
                $element=$info['elements'][$tplanElementIdx];

                // col3
                $this->dmSetXY($pdf, 0, 1, $ox, $oy + $offsetY2);
                $pdf->MultiCell(59,0,$element['col3'],0,'L');

                // col4
                $this->dmSetXY($pdf, 60, 1, $ox, $oy + $offsetY2);
                $pdf->MultiCell(61,0,$element['col4'],0,'L');

                // h (teaching hours, this session)
                $this->dmSetXY($pdf, 127, 1, $ox, $oy + $offsetY2);
                $pdf->Cell(5, 7, $info['teaching_hrs_per_class'], 0, 0, 'C');

                $elementsPrintedCnt++;
                $tplanElementIdx++;
            }

        }


        // Emit signature block, if necessary
        if($lastSheet) {
            $this->dmSetXY($pdf, 0, 207, $ox, $oy);
            $pdf->Cell(19, 7, '主 讲 老 师', 0, 0, 'L');
            $this->dmLine($pdf, 19, 212, 43, 212, $ox, $oy); // h

            $this->dmSetXY($pdf, 0, 215, $ox, $oy);
            $pdf->Cell(19, 7, '教研室主任', 0, 0, 'L');
            $this->dmLine($pdf, 19, 220, 43, 220, $ox, $oy); // h

            $this->dmSetXY($pdf, 100, 207, $ox, $oy);

            $pdf->Cell(12, 7, '系主任', 0, 0, 'L');
            $this->dmLine($pdf, 112, 212, 149, 212, $ox, $oy); // h

            $this->dmSetXY($pdf, 100, 215, $ox, $oy);
            $pdf->Cell(10, 7, '日期', 0, 0, 'L');
            $this->dmSetXY($pdf, 117, 215, $ox, $oy);
            $pdf->Cell(100, 7, '年           月           日', 0, 0, 'L');
        }

        return $elementsPrintedCnt;
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $tplan = $this->Tplans->get($id,['contain' => ['TplanElements']]);
        $this->set('tplan', $tplan);
        $this->set('tplan_elements',$tplan->tplan_elements);
    }
}
