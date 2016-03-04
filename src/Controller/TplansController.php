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
        $info['major']='Warp Drive Engineering';
        $info['cohorts']='15A1, 15A2';
        $info['instructor']='Spock';

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
        $pdf->AddPage();
        $this->emitRearCover($pdf,17,23);
        $pdf->AddPage();

        // 2.1 shandong Lǚyóu zhíyè xuéyuàn
        // Shandong College of Tourism and Hospitality
        $pdf->SetFontSize(21);
        $pdf->SetXY(44,20);
        $pdf->Cell(74,8,'山 东 旅 游 职 业 学 院',1,0,'C');

        // 2.2 jia4oxue2 ri4li4
        // Teaching plan
        $pdf->SetFontSize(40);
        $pdf->SetXY(21,37);
        $pdf->Cell(123,18,'教     学     日     历',1,0,'C');

        // 2.3 ke4che2ng mi2ngche1ng
        // Course title
        $pdf->SetFontSize(16);
        $pdf->SetXY(37,77);
        $pdf->Cell(22,8,'课程名称',1,0,'C');

        // 2.4 zhua1n ye4
        // Profession
        $pdf->SetXY(37,92);
        $pdf->Cell(11,8,'专业',1,0,'C');

        // nia2n ji2
        // Grade aka cohorts
        $pdf->SetXY(82,92);
        $pdf->Cell(12,8,'年级',1,0,'C');

        // zh3jia3ng jia4o shi1
        // Speaker/teacher
        $pdf->SetFontSize(12);
        $pdf->SetXY(37,102);
        $pdf->Cell(8,5,'主讲',1,0,'C');
        $pdf->SetXY(37,107);
        $pdf->Cell(8,5,'教师',1,0,'C');

        // xi4ng mi2ng
        // Full name
        $pdf->SetFontSize(16);
        $pdf->SetXY(47,103);
        $pdf->Cell(11,8,'姓名',1,0,'C');

        // zhi2 che1ng
        // Job title
        $pdf->SetXY(87,103);
        $pdf->Cell(12,8,'职称',1,0,'C');

        $pdf->SetXY(37,129);
        $pdf->Cell(17,7,'周   数',1,0,'C');
        //$pdf->SetXY(100,100);
        //$pdf->Cell(100,0,'数',0,0,'C');


        $pdf->SetXY(37,138);
        $pdf->Cell(17,7,'讲   课',1,0,'C');
        //$pdf->SetXY(100,120);
        //$pdf->Cell(100,0,'课',0,0,'C');

        $pdf->SetXY(106,138);
        $pdf->Cell(13,7,'学时',1,0,'C');


        $pdf->SetXY(37,147);
        $pdf->Cell(17,7,'实习课',1,0,'C');
        $pdf->SetXY(106,147);
        $pdf->Cell(13,7,'学时',1,0,'C');


        $pdf->SetXY(37,156);
        $pdf->Cell(17,7,'实   验',1,0,'C');
        //$pdf->SetXY(100,170);
        //$pdf->Cell(100,0,'验',0,0,'C');
        $pdf->SetXY(106,156);
        $pdf->Cell(13,7,'学时',1,0,'C');


        $pdf->SetXY(37,165);
        $pdf->Cell(17,7,'总   计',1,0,'C');
        //$pdf->SetXY(100,190);
        //$pdf->Cell(100,0,'计',0,0,'C');
        $pdf->SetXY(106,165);
        $pdf->Cell(13,7,'学时',1,0,'C');


        $pdf->Line(59,83, 118,83); // h
        $pdf->Line(48,97, 84, 97); // h
        $pdf->Line(95,97, 118,97); // h
        $pdf->Line(57,110, 88,110); // h
        $pdf->Line(98,110, 118,110); // h

        $pdf->Line(53,135,118,135); // h
        $pdf->Line(53,144,108,144); // h
        $pdf->Line(53,153,108,153); // h
        $pdf->Line(53,162,108,162); // h
        $pdf->Line(53,172,108,172); // h

        $pdf->SetXY(34,204);
        $pdf->Cell(30,7,'2015-2016',1,0,'C');
        $pdf->SetXY(80,204);
        $pdf->Cell(21,7,'学年第',1,0,'C');
        $pdf->SetXY(109,204);
        $pdf->Cell(11,7,'学期',1,0,'C');


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
            $this->emitPageLeft();
            $this->emitPageRight();
            $extraSheetCnt--;
        }

        $this->emitPageLeft($pdf);
        $pdf->AddPage();
        $this->emitPageRight($pdf);
        // Draw the header box



        // 3. Rear cover
        $pdf->AddPage();
        $this->emitRearCover($pdf);



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
        $pdf->Cell(48,5,'检查本日历完成情况的结果',1,0,'C');

        $this->dmSetXY($pdf,24,192,$ox,$oy);
        $pdf->Cell(11,7,'检查者',1,0,'C');
        $this->dmLine($pdf,35,197,61,197,$ox,$oy); // h
        $this->dmSetXY($pdf,32,203,$ox,$oy);
        $pdf->Cell(35,7,'年         月         日',1,0,'C');

        $this->dmSetXY($pdf,87,192,$ox,$oy);
        $pdf->Cell(15,7,'主讲教师',1,0,'C');
        $this->dmLine($pdf,102,197,133,197,$ox,$oy); // h

        $this->dmSetXY($pdf,99,203,$ox,$oy);
        $pdf->Cell(35,7,'年         月         日',1,0,'C');

        $this->dmSetXY($pdf,5,215,$ox,$oy);
        $pdf->Cell(126,7,'附注: 本日历一式三份, 一份存教务处,一份存教研室, 一份讲授者保存',1,0,'C');
    }

    private function emitPageLeft($pdf) {
        // Draw the header box
        $offsetX=5;
        $offsetY=5;
        $leftX = 0;
        $rightX = 154;
        $topY = 0;
        $bottomY = 37;
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

        $pdf->SetXY(5,14);
        $pdf->Cell(5,7,'月',1,0,'C');
        $pdf->SetXY(5,20);
        $pdf->Cell(5,7,'份',1,0,'C');

        $pdf->SetXY(35,16);
        $pdf->Cell(15,7,'周   次',1,0,'C');
        //$pdf->SetXY(100,40);
        //$pdf->Cell(100,0,'次',0,0,'C');

        $pdf->SetXY(54,13);
        $pdf->Cell(20,7,'讲    课',1,0,'C');
        //$pdf->SetXY(100,60);
        //$pdf->Cell(100,0,'课',0,0,'C');
        $pdf->SetXY(47,19);
        $pdf->Cell(34,7,'（教学大纲章节题目）',1,0,'C');

        $pdf->SetXY(95,13);
        $pdf->Cell(34,7,'实验习题课课堂',1,0,'C');
        $pdf->SetXY(95,19);
        $pdf->Cell(34,7,'讨论及其它作业题目',1,0,'C');

        $pdf->SetXY(143,11);
        $pdf->Cell(5,7,'课',1,0,'C');
        $pdf->SetXY(143,21);
        $pdf->Cell(5,7,'时',1,0,'C');



        // Emit the next 4 (plan elements or blank elements)
        for($i=0; $i<4; $i++) {
            $offsetY2=37+$i*36;
            $pdf->Line($leftX+$offsetX, $topY+$offsetY+$offsetY2,   $leftX+$offsetX, $bottomY+$offsetY+$offsetY2); // v
            $pdf->Line($rightX+$offsetX,$topY+$offsetY+$offsetY2,   $rightX+$offsetX,$bottomY+$offsetY+$offsetY2); // v
            $pdf->Line($leftX+$offsetX, $topY+$offsetY+$offsetY2,   $rightX+$offsetX,$topY+$offsetY+$offsetY2); // h
            $pdf->Line($leftX+$offsetX, $bottomY+$offsetY+$offsetY2,$rightX+$offsetX,$bottomY+$offsetY+$offsetY2); // h

            // Draw the vertical lines
            $pdf->Line($leftX+18+$offsetX, $topY+$offsetY+$offsetY2,   $leftX+18+$offsetX, $bottomY+$offsetY+$offsetY2); // v
            $pdf->Line($leftX+41+$offsetX, $topY+$offsetY+$offsetY2,   $leftX+41+$offsetX, $bottomY+$offsetY+$offsetY2); // v
            $pdf->Line($leftX+89+$offsetX, $topY+$offsetY+$offsetY2,   $leftX+89+$offsetX, $bottomY+$offsetY+$offsetY2); // v
            $pdf->Line($leftX+137+$offsetX, $topY+$offsetY+$offsetY2,   $leftX+137+$offsetX, $bottomY+$offsetY+$offsetY2); // v


            $pdf->SetXY(20,17+$offsetY+$offsetY2);
            $pdf->Cell(13,7,'第一周',1,0,'C');
            $pdf->SetXY(20,23+$offsetY+$offsetY2);
            $pdf->Cell(20,7,'从   到',1,0,'C');

        }

        // Emit the signature block
    }
    private function emitPageRight($pdf) {
        // Draw the header box
        $offsetX=5;
        $offsetY=5;
        $leftX = 0;
        $rightX = 154;
        $topY = 0;
        $bottomY = 37;
        $pdf->SetLineWidth(0.5);
        $pdf->Line($leftX+$offsetX, $topY+$offsetY,   $leftX+$offsetX, $bottomY+$offsetY); // v
        $pdf->Line($rightX+$offsetX,$topY+$offsetY,   $rightX+$offsetX,$bottomY+$offsetY); // v
        $pdf->Line($leftX+$offsetX, $topY+$offsetY,   $rightX+$offsetX,$topY+$offsetY); // h
        $pdf->Line($leftX+$offsetX, $bottomY+$offsetY,$rightX+$offsetX,$bottomY+$offsetY); // h
        // Draw the vertical lines
        $pdf->Line($leftX+60+$offsetX, $topY+$offsetY,   $leftX+60+$offsetX, $bottomY+$offsetY); // v
        $pdf->Line($leftX+121+$offsetX, $topY+$offsetY,   $leftX+121+$offsetX, $bottomY+$offsetY); // v
        $pdf->Line($leftX+137+$offsetX, $topY+$offsetY,   $leftX+137+$offsetX, $bottomY+$offsetY); // v


        $pdf->SetXY(15,16);
        $pdf->Cell(27,7,'阅读主要参考书',1,0,'C');

        $pdf->SetXY(73,16);
        $pdf->Cell(34,7,'辅导形式和主要内容',1,0,'C');

        $pdf->SetXY(125,16);
        $pdf->Cell(10,7,'课时',1,0,'C');

        $pdf->SetXY(140,16);
        $pdf->Cell(11,7,'备注',1,0,'C');




        // Emit the next 4 (plan elements or blank elements)
        for($i=0; $i<4; $i++) {
            $offsetY2=37+$i*36;
            $pdf->Line($leftX+$offsetX, $topY+$offsetY+$offsetY2,   $leftX+$offsetX, $bottomY+$offsetY+$offsetY2); // v
            $pdf->Line($rightX+$offsetX,$topY+$offsetY+$offsetY2,   $rightX+$offsetX,$bottomY+$offsetY+$offsetY2); // v
            $pdf->Line($leftX+$offsetX, $topY+$offsetY+$offsetY2,   $rightX+$offsetX,$topY+$offsetY+$offsetY2); // h
            $pdf->Line($leftX+$offsetX, $bottomY+$offsetY+$offsetY2,$rightX+$offsetX,$bottomY+$offsetY+$offsetY2); // h

            // Draw the vertical lines
            //$pdf->Line($leftX+18+$offsetX, $topY+$offsetY+$offsetY2,   $leftX+18+$offsetX, $bottomY+$offsetY+$offsetY2); // v
            //$pdf->Line($leftX+41+$offsetX, $topY+$offsetY+$offsetY2,   $leftX+41+$offsetX, $bottomY+$offsetY+$offsetY2); // v
            //$pdf->Line($leftX+89+$offsetX, $topY+$offsetY+$offsetY2,   $leftX+89+$offsetX, $bottomY+$offsetY+$offsetY2); // v
            //$pdf->Line($leftX+137+$offsetX, $topY+$offsetY+$offsetY2,   $leftX+137+$offsetX, $bottomY+$offsetY+$offsetY2); // v
            // Draw the vertical lines
            $pdf->Line($leftX+60+$offsetX, $topY+$offsetY+$offsetY2,   $leftX+60+$offsetX, $bottomY+$offsetY+$offsetY2); // v
            $pdf->Line($leftX+121+$offsetX, $topY+$offsetY+$offsetY2,   $leftX+121+$offsetX, $bottomY+$offsetY+$offsetY2); // v
            $pdf->Line($leftX+137+$offsetX, $topY+$offsetY+$offsetY2,   $leftX+137+$offsetX, $bottomY+$offsetY+$offsetY2); // v

            //$pdf->SetXY(20,17+$offsetY+$offsetY2);
            //$pdf->Cell(13,7,'第一周',1,0,'C');
            //$pdf->SetXY(20,23+$offsetY+$offsetY2);
            //$pdf->Cell(20,7,'从   到',1,0,'C');

        }


        $pdf->SetXY(0,100);
        $pdf->Cell(19,7,'主讲老师',1,0,'C');

        $pdf->SetXY(0,120);
        $pdf->Cell(19,7,'教研室主任',1,0,'C');

        $pdf->SetXY(100,100);
        $pdf->Cell(11,7,'系主任',1,0,'C');

        $pdf->SetXY(100,120);
        $pdf->Cell(100,7,'日期年月日',1,0,'C');
        // Emit the signature block
    }
    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $tplan = $this->Tplans->get($id,['contain' => ['TplanElements']]);
        $this->set('tplan', $tplan);
        $this->set('tplan_elements',$tplan->tplan_elements);
    }
}
