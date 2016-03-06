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

        // 1. In the beginning...
        // 1.1 Obtain the data to print.
        $info['subject']='Intro to Warp Theory';
        $info['major']='Engineering';
        $info['cohorts']='15A1, 15A2';
        $info['instructor']='Spock';
        $info['class_cnt']=18;
        $info['teaching_hrs_per_class']=2;
        $info['semester_seq']=2; // 1st or 2nd

        // 1.2 Initialize the pdf
        require('tcpdf.php');

        // The primary method of specifying position is to measure mm from the origin,
        // where the origin is at the upper-left corner of the paper, moving right increases x
        // and moving down increases y.

        $pdf = new \tcpdf('P','mm','A4');
        $pdf->SetFont('cid0cs', '', 16, '', true);

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        
        // 2. Page 1, The cover
        //$pdf->AddPage();
        //$this->emitPageLeft($info,$pdf,0,0);

        $pdf->AddPage();
        $this->emitFrontCover($info,$pdf,10,10);

        // 3. The Plan Elements
        $pdf->AddPage();

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
        $extraSheetCnt=intval(($tplanElementCnt+5)/10); // model this in excel and see that it works

        while($extraSheetCnt>0) {
            // emit the next 10 (plan elements or blank elements)
            $this->emitPageLeft($info,$pdf,5,5);
            $this->emitPageRight($info,$pdf,5,5);
            $extraSheetCnt--;
        }

        $this->emitPageLeft($info,$pdf,5,5);
        $pdf->AddPage();
        $this->emitPageRight($info,$pdf,5,5);

        // 3. Rear cover
        $pdf->AddPage();
        $this->emitRearCover($pdf,17,23);


        $pdf->Output();
        $this->response->type('application/pdf');
        return $this->response;
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

        $this->dmSetXY($pdf,59,77,$ox,$oy);
        $pdf->Cell(100,8,$info['subject'],0,0,'L');

        $this->dmLine($pdf,60,84, 118,84,$ox,$oy); // h

        // 2.4 zhua1n ye4
        // Profession
        $this->dmSetXY($pdf,37,92,$ox,$oy);
        $pdf->Cell(11,8,'专业',0,0,'C');

        $this->dmSetXY($pdf,48,92,$ox,$oy);
        $pdf->Cell(100,8,$info['major'],0,0,'L');

        $this->dmLine($pdf,49,99, 82, 99,$ox,$oy); // h

        // 2.5 nia2n ji2
        // Grade aka cohorts
        $this->dmSetXY($pdf,82,92,$ox,$oy);
        $pdf->Cell(12,8,'年级',0,0,'C');

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

        $this->dmSetXY($pdf,57,103,$ox,$oy);
        $pdf->Cell(100,8,$info['instructor'],0,0,'L');

        $this->dmLine($pdf,58,110, 86,110,$ox,$oy); // h

        // 2.6.2 zhi2 che1ng
        // Job title
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

    private function emitPageLeft($info,$pdf,$ox=0,$oy=0) {

        $pdf->SetFontSize(12);

        // Draw the header box
        //$offsetX=5;
        //$offsetY=5;
        $leftX = 0;
        $rightX = 154;
        $topY = 0;
        $bottomY = 37;
        $pdf->SetLineWidth(0.5);
        $this->dmLine($pdf,$leftX, $topY,   $leftX, $bottomY,$ox,$oy); // v
        $this->dmLine($pdf,$rightX,$topY,   $rightX,$bottomY,$ox,$oy); // v
        $this->dmLine($pdf,$leftX, $topY,   $rightX,$topY,$ox,$oy); // h
        $this->dmLine($pdf,$leftX, $bottomY,$rightX,$bottomY,$ox,$oy); // h

        // Draw the vertical lines
        $this->dmLine($pdf,$leftX+18, $topY,   $leftX+18, $bottomY,$ox,$oy); // v
        $this->dmLine($pdf,$leftX+41, $topY,   $leftX+41, $bottomY,$ox,$oy); // v
        $this->dmLine($pdf,$leftX+89, $topY,   $leftX+89, $bottomY,$ox,$oy); // v
        $this->dmLine($pdf,$leftX+137, $topY,   $leftX+137, $bottomY,$ox,$oy); // v

        $this->dmSetXY($pdf,7,12,$ox,$oy);
        $pdf->Cell(5,7,'月',0,0,'C');
        $this->dmSetXY($pdf,7,18,$ox,$oy);
        $pdf->Cell(5,7,'份',0,0,'C');

        $this->dmSetXY($pdf,23,15,$ox,$oy);
        $pdf->Cell(15,7,'周   次',0,0,'C');

        $this->dmSetXY($pdf,54,12,$ox,$oy);
        $pdf->Cell(20,7,'讲    课',0,0,'C');
        $this->dmSetXY($pdf,47,18,$ox,$oy);
        $pdf->Cell(34,7,'（教学大纲章节题目）',0,0,'C');

        $this->dmSetXY($pdf,95,12,$ox,$oy);
        $pdf->Cell(34,7,'实验,  习题课,  课堂',0,0,'C');
        $this->dmSetXY($pdf,95,18,$ox,$oy);
        $pdf->Cell(34,7,'讨论及其它作业题目',0,0,'C');

        $this->dmSetXY($pdf,143,11,$ox,$oy);
        $pdf->Cell(5,7,'课',0,0,'C');
        $this->dmSetXY($pdf,143,21,$ox,$oy);
        $pdf->Cell(5,7,'时',0,0,'C');


        // Emit the next 4 (plan elements or blank elements)
        for($i=0; $i<4; $i++) {

            // I need this again because something (I think SetXY) resets it.
            $pdf->SetLineWidth(0.5);

            $offsetY2=37+$i*36;
            $this->dmLine($pdf,$leftX, $topY,   $leftX, $bottomY, $ox,$oy+$offsetY2); // v
            $this->dmLine($pdf,$rightX,$topY,   $rightX,$bottomY, $ox,$oy+$offsetY2); // v
            //$this->dmLine($pdf,$leftX, $topY,   $rightX,$topY, $ox,$oy+$offsetY2); // h
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

            $this->dmSetXY($pdf,18,19,$ox,$oy+$offsetY2);
            $pdf->Cell(20,7,'从   到',0,0,'C');
            $this->dmLine($pdf,26, 25,30,25, $ox,$oy+$offsetY2); // h
            $this->dmLine($pdf,34, 25,38,25, $ox,$oy+$offsetY2); // h

        }

        // Emit the signature block
    }
    private function itohz($i) {
        $a=['','一','二','三','四'];
        return $a[$i];
    }

    private function emitPageRight($info,$pdf,$ox=0,$oy=0) {

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

        // Draw the vertical lines
        $this->dmLine($pdf,$leftX+60, $topY,   $leftX+60, $bottomY,$ox,$oy); // v
        $this->dmLine($pdf,$leftX+121, $topY,   $leftX+121, $bottomY,$ox,$oy); // v
        $this->dmLine($pdf,$leftX+137, $topY,   $leftX+137, $bottomY,$ox,$oy); // v


        $this->dmSetXY($pdf,15,14,$ox,$oy);
        $pdf->Cell(27,7,'阅读主要参考书',0,0,'C');

        $this->dmSetXY($pdf,73,14,$ox,$oy);
        $pdf->Cell(34,7,'辅导形式和主要内容',0,0,'C');

        $this->dmSetXY($pdf,125,14,$ox,$oy);
        $pdf->Cell(10,7,'课时',0,0,'C');

        $this->dmSetXY($pdf,140,14,$ox,$oy);
        $pdf->Cell(11,7,'备注',0,0,'C');


        // Emit the next 4 (plan elements or blank elements)
        for($i=0; $i<4; $i++) {

            // I need this again because something (I think SetXY) resets it.
            $pdf->SetLineWidth(0.5);

            $offsetY2=37+$i*36;
            $this->dmLine($pdf,$leftX, $topY,   $leftX, $bottomY, $ox,$oy+$offsetY2); // v
            $this->dmLine($pdf,$rightX,$topY,   $rightX,$bottomY, $ox,$oy+$offsetY2); // v
            //$this->dmLine($pdf,$leftX, $topY,   $rightX,$topY); // h
            $this->dmLine($pdf,$leftX, $bottomY,$rightX,$bottomY, $ox,$oy+$offsetY2); // h

            // Draw the vertical lines
            $this->dmLine($pdf,$leftX+60, $topY,   $leftX+60, $bottomY, $ox,$oy+$offsetY2); // v
            $this->dmLine($pdf,$leftX+121, $topY,   $leftX+121, $bottomY, $ox,$oy+$offsetY2); // v
            $this->dmLine($pdf,$leftX+137, $topY,   $leftX+137, $bottomY, $ox,$oy+$offsetY2); // v

        }

        //$pdf->SetLineWidth(0.3);

        $this->dmSetXY($pdf,0,207,$ox,$oy);
        $pdf->Cell(19,7,'主 讲 老 师',0,0,'L');
        $this->dmLine($pdf,19,212,43,212, $ox,$oy); // h

        $this->dmSetXY($pdf,0,215,$ox,$oy);
        $pdf->Cell(19,7,'教研室主任',0,0,'L');
        $this->dmLine($pdf,19,220,43,220, $ox,$oy); // h

        $this->dmSetXY($pdf,100,207,$ox,$oy);
        $pdf->Cell(12,7,'系主任',0,0,'L');
        $this->dmLine($pdf,112,212,149,212, $ox,$oy); // h

        $this->dmSetXY($pdf,100,215,$ox,$oy);
        $pdf->Cell(10,7,'日期',0,0,'L');
        $this->dmSetXY($pdf,117,215,$ox,$oy);
        $pdf->Cell(100,7,'年           月           日',0,0,'L');
        // Emit the signature block
    }
    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $tplan = $this->Tplans->get($id,['contain' => ['TplanElements']]);
        $this->set('tplan', $tplan);
        $this->set('tplan_elements',$tplan->tplan_elements);
    }
}
