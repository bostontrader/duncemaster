<?php
namespace App\Controller;

class SectionsController extends AppController {

    public function add() {
        $this->request->allowMethod(['get', 'post']);
        $section = $this->Sections->newEntity();
        if ($this->request->is('post')) {
            $section = $this->Sections->patchEntity($section, $this->request->data);
            if ($this->Sections->save($section)) {
                //$this->Flash->success(__('The section has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The section could not be saved. Please, try again.'));
            }
        }
        $cohorts = $this->Sections->Cohorts->find('list',['contain' => ['Majors']]);
        $semesters = $this->Sections->Semesters->find('list');
        $subjects = $this->Sections->Subjects->find('list');
        $tplans = $this->Sections->Tplans->find('list');
        $this->set(compact('cohorts','section','semesters','subjects','tplans'));
        return null;
    }

    public function attend($id = null) {

        require('tcpdf.php');

        $pdf = new \tcpdf('L','mm','a4');
        $pdf->SetFont('cid0cs', '', 16, '', true);

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();

        $pdf->SetFontSize(22);
        $pdf->SetXY(100,10);
        $pdf->Cell(100,0,'山东旅游职业学院考评登记表',0,0,'C');

        // semester xue qi
        $pdf->SetFontSize(12);
        $pdf->SetXY(236,16);
        $pdf->Cell(0,0,'学期:');
        $pdf->Line(248,21,273,21); // h

        $pdf->SetXY(30,22);
        $pdf->SetFontSize(17);
        $pdf->Cell(0,0,'出勤,作业');

        $pdf->SetFontSize(14);
        $pdf->SetXY(57,24);
        $pdf->Cell(0,0,'(注: 事假: X 旷课: Ø 病假: / 迟到: O 早退: + 迟到又早退:   公假:   正常或完成作业: , 未完成作业: X)');
        //$pdf->Cell(0,0,'/+你好');

        $pdf->SetFontSize(12);
        $pdf->SetXY(32,30);
        $pdf->Cell(0,0,'周次');

        $pdf->SetXY(19,37);
        $pdf->Cell(0,0,'序');
        $pdf->SetXY(19,42);
        $pdf->Cell(0,0,'号');

        $pdf->SetXY(26,40);
        $pdf->Cell(0,0,'学号');

        $pdf->SetXY(39,40);
        $pdf->Cell(0,0,'姓名');

        $pdf->SetXY(26,194);
        $pdf->Cell(0,0,'课程名称:');
        $pdf->Line(47,199,80,199); // h

        $pdf->SetXY(99,194);
        $pdf->Cell(0,0,'班级:');
        $pdf->Line(111,199,136,199); // h

        $pdf->SetXY(162,194);
        $pdf->Cell(0,0,'任课老师:');
        $pdf->Line(183,199,209,199); // h


        // Draw the main outerbox
        $leftX = 19;
        $rightX = 279;
        $topY = 30;
        $bottomY = 191;
        $pdf->SetLineWidth(0.5);
        $pdf->Line($leftX,$topY,$leftX,$bottomY); // v
        $pdf->Line($rightX,$topY,$rightX,$bottomY); // v
        $pdf->Line($leftX,$topY,$rightX,$topY); // h
        $pdf->Line($leftX,$bottomY,$rightX,$bottomY); // h

        // Left most inner lines
        $pdf->SetLineWidth(0.1);
        $pdf->Line(26,35,26,$bottomY); // v
        $pdf->Line(36,35,36,$bottomY); // v

        // Top most inner lines
        $pdf->Line($leftX,35,$rightX,35); // h
        $pdf->Line(54,45,$rightX,45); // h

        // The horizontal lines
        for($y=0; $y<138; $y+=4.71) {
            $y1=$y+49.5;
            $pdf->Line($leftX,$y1,$rightX,$y1); // h
        }

        $priorX1=0;
        for($x=0; $x<225; $x+=18.8) {
            $x1=$x+54;
            $pdf->Line($x1,$topY,$x1,$bottomY); // v
            $pdf->Line($x1+9.4,$topY+15,$x1+9.4,$bottomY); // v

            $pdf->SetXY($x1,$topY);
            $pdf->Cell(18.8,0,'第    周',0,0,'C');



            if($x>0) {
                $pdf->Line($priorX1,45,$x1,35); // sloped
            }
            $priorX1=$x1;
        }
        $pdf->Line($priorX1,45,$rightX,35); // sloped

        $pdf->Output();
        $this->response->type('application/pdf');
        return $this->response;
    }

    public function delete($id = null) {
        $this->request->allowMethod(['post', 'delete']);
        $section = $this->Sections->get($id);
        if ($this->Sections->delete($section)) {
            //$this->Flash->success(__('The section has been deleted.'));
            //} else {
            //$this->Flash->error(__('The section could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

    public function edit($id = null) {
        $this->request->allowMethod(['get', 'put']);
        $section = $this->Sections->get($id,['contain' => ['Clazzes.Sections']]);
        if ($this->request->is(['put'])) {
            $section = $this->Sections->patchEntity($section, $this->request->data);
            if ($this->Sections->save($section)) {
                //$this->Flash->success(__('The section has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                //$this->Flash->error(__('The section could not be saved. Please, try again.'));
            }
        }
        $cohorts = $this->Sections->Cohorts->find('list',['contain' => ['Majors']]);
        $semesters = $this->Sections->Semesters->find('list');
        $subjects = $this->Sections->Subjects->find('list');
        $tplans = $this->Sections->Tplans->find('list');
        $this->set('clazzes',$section->clazzes);
        $this->set(compact('cohorts','section','semesters','subjects','tplans'));
        return null;
    }

    public function index() {
        $this->request->allowMethod(['get']);

        // Ensure that the ordering produced here matches the ordering in SectionsFixture.
        $this->set(
            'sections', $this->Sections->find(
                'all',
                ['contain' => ['Cohorts.Majors','Semesters','Subjects','Tplans'],'order'=>['Semesters.year','Sections.seq']]
            )
        );
    }

    public function scores() {

    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);
        $section = $this->Sections->get($id,['contain'=>['Clazzes.Sections','Cohorts.Majors','Semesters','Subjects','Tplans']]);
        $this->set('section', $section);
        $this->set('clazzes',$section->clazzes);
    }
}
