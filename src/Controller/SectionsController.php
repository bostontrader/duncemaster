<?php
namespace App\Controller;

use Cake\Datasource\ConnectionManager;

require_once 'simple_html_dom.php';

class SectionsController extends AppController {

    // Nothing is authorized unless a controller says so.
    // Admin and teachers are always authorized. It is the responsibility
    // of this controller to restrict access to info for a teacher
    // to only his information and no other teacher.
    public function isAuthorized($userArray) {
        return $this->isAdmin || $this->isTeacher;
    }

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
        $teachers = $this->Sections->Teachers->find('list');
        $subjects = $this->Sections->Subjects->find('list');
        $tplans = $this->Sections->Tplans->find('list');
        $this->set(compact('cohorts','section','semesters','subjects','teachers','tplans'));
        return null;
    }

    public function attend($id = null) {

        //select teachers.fam_name, teachers.giv_name, subjects.title
        //from sections
        //left join teachers on sections.teacher_id = teachers.id
        //left join subjects on sections.subject_id = subjects.id
        $section = $this->Sections->get($id,['contain' => ['Cohorts.Majors','Semesters','Subjects','Teachers']]);

        // 1. Get started
        require('tcpdf.php');

        // The primary method of specifying position is to measure mm from the origin,
        // where the origin is at the upper-left corner of the paper, moving right increases x
        // and moving down increases y. We will also use a 2nd level of positioning, described
        // shortly, on top of this level one positioning.

        $pdf = new \tcpdf('L','mm','a4');
        $pdf->SetFont('cid0cs', '', 16, '', true);

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();

        // 2. Lǚyóu zhíyè xuéyuàn kǎopíng dēngjì biǎo
        // Tourism College Evaluation Registration Form
        $pdf->SetFontSize(22);
        $pdf->SetXY(100,10);
        $pdf->Cell(100,0,'山东旅游职业学院考评登记表',0,0,'C');

        // 3. xue qi semester
        $pdf->SetFontSize(12);
        $pdf->SetXY(236,16);
        $pdf->Cell(0,0,'学期:');
        $pdf->SetXY(249,15);
        $pdf->Cell(0,0,$section->semester->nickname);
        $pdf->Line(248,21,273,21); // h

        // 4.
        $pdf->SetXY(30,22);
        $pdf->SetFontSize(17);
        $pdf->Cell(0,0,'出勤,作业');

        $pdf->SetFontSize(14);
        $pdf->SetXY(57,24);
        $pdf->Cell(0,0,'(注: 事假: X 旷课: Ø 病假: / 迟到: O 早退: + 迟到又早退:   公假:   正常或完成作业: , 未完成作业: X)');

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
        // section 2, cohort 3


        /* @var \Cake\Database\Connection $connection */
        $connection = ConnectionManager::get('default');
        $query = "SELECT sid, fam_name, giv_name from students
            left join cohorts  on students.cohort_id=cohorts.id
            left join sections on cohorts.id=sections.cohort_id
            where sections.id=$id order by sid";
        $classRoster = $connection->execute($query)->fetchAll('assoc');


        // This query collects attendance info, in sid order. Other queries collect
        // other info, also in sid order. Be careful to ensure that each query contains
        // exactly the same students.
        // |--------------|--|
        // | students.sid |
        /* @var \Cake\Database\Connection $connection */
        /*$connection = ConnectionManager::get('default');
        $query = "select students.id as student_id, students.sort, students.sid, students.giv_name, students.fam_name, students.phonetic_name, interactions.itype_id, cohorts.id as cohort_id, sections.id as section_id, clazzes.id as clazz_id
                from students
                left join cohorts on students.cohort_id = cohorts.id
                left join sections on sections.cohort_id = cohorts.id
                left join interactions on interactions.clazz_id=clazzes.id and interactions.student_id=students.id and interactions.itype_id=".ItypesController::ATTEND." where clazzes.id=".$clazz_id.
            " order by sort";

        $attendResults = $connection->execute($query)->fetchAll('assoc');*/

        // Fill in the form
        foreach($cy as $yidx=>$y2) {
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
        }


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
        $teachers = $this->Sections->Teachers->find('list');
        $tplans = $this->Sections->Tplans->find('list');
        $this->set('clazzes',$section->clazzes);
        $this->set(compact('cohorts','section','semesters','subjects','teachers','tplans'));
        return null;
    }

    public function index() {
        $this->request->allowMethod(['get']);

        /* @var \Cake\ORM\Query $query */
        $query = $this->Sections->find('all')
            ->contain(['Cohorts.Majors','Semesters','Subjects','Teachers','Tplans'])
            ->order(['Semesters.year','Sections.seq']);

        if($this->isTeacher)
            $query->where(['teacher_id'=>$this->Auth->user('teacher_id')]);
        $this->set('sections',$query);

    }

    public function scores($id = null){

        $this->request->allowMethod(['get', 'post']);
        if ($this->request->is(['post'])) {

            $section = $this->Sections->get($id,['contain' => ['Cohorts.Majors']]);

            // The login credentials for he school's computer.
            $username=$this->request->data['username'];
            $password=$this->request->data['password'];

            $scores=[
                //['sid'=>'1000','a'=>20,'b'=>30]
            ];

            // 1. GET the login form and determine the session_id.
            $content=$this->getHomePage();
            $session_id=$this->get_session_id($content);

            // 1.1 The home page also has the __VIEWSTATE input which will be used
            // in a subsequent POST. Grab that now.
            $html = str_get_html($content);
            $vs1=urlencode($html->find("input[name='__VIEWSTATE']",0)->value);

            // 2. POST the login form.
            $content=$this->postLoginForm($username, $password, $session_id, $vs1);

            // 3. GET jsmainfs. Successful login will give us 302 redirect to this page.
            // But we probably don't really need to read this.
            //$content=$this->getJsmainfs($session_id);

            // 4. GET jsleft.aspx. This contains the list of available sections and we need this to find the URL for
            // this particular section.
            $url="http://60.216.13.32/jsleft.aspx/?flag=cjlr&xn=2015-2016&xq=1";
            $content=$this->getJsleft($url,$session_id);

            // 4.1 Parse the response and find the A tag that points to the scores form, for this section.
            $html = str_get_html($content);
            $atags=$html->find("a");
            $n = $section->cohort->nickname;
            foreach($atags as $atag) {
                if(strpos($atag->title,$n)) {
                    $scoresUrl="http://60.216.13.32/".$atag->href;
                    break;
                }
            }

            // 5. GET the scores form.
            $scoresUrl="http://60.216.13.32/cjlr1.aspx?xh=11164&kc=%282015-2016-1%29-1103016-11164-1&kclx=%B1%D8%D0%DE%BF%CE&cjxn=2015-2016&cjxq=1";
            $scoresUrl="http://60.216.13.32/cjlr1.aspx?xh=$username&kc=%282015-2016-1%29-1103016-$username-1&kclx=%B1%D8%D0%DE%BF%CE&cjxn=2015-2016&cjxq=1";
            $content=$this->getScoresForm($scoresUrl,$session_id);

            // 5.1 The scores form also has another __VIEWSTATE input which will be used
            // in a subsequent POST. Grab that now.
            $html = str_get_html($content);
            $vs2=urlencode($html->find("input[name='__VIEWSTATE']",0)->value);
            $table=$html->find("table[id='DataGrid1']",0);
            $trs=$table->find("tr");
            $c=count($trs);

            $myscores=[];
            $myscores['201402010103']=['a'=>'50','b'=>'60'];
            $myscores['201402010106']=['a'=>'60','b'=>'70'];
            $myscores['201402010110']=['a'=>'70','b'=>'80'];

            foreach($trs as $tr) {
                $tds=$tr->find("td");
                $idx=$tds[0]->innertext;
                $sid=$tds[2]->innertext;

                if(is_numeric($idx)) {
                    if(array_key_exists($sid, $myscores)) {
                        $ms = $myscores[$sid];
                        $scores[$idx] = ['gidx' => $idx + 1, 'a' => $ms['a'], 'b' => $ms['b']];
                    } else
                        $scores[$idx] = ['gidx'=>$idx+1,'a'=>'','b'=>''];

                }
            }



            // 6. POST the scores form.
            $result=$this->postScoresForm($scoresUrl,$session_id,$vs2,$scores);

            if($result=="") $result="Success!";
            $this->set(compact('result'));

            //$this->Flash->success(__('The section has been saved.'));
            return $this->redirect(['action' => 'index']);
        }

        //$this->set('clazzes',$section->clazzes);
        //$this->set(compact('cohorts','section','semesters','subjects','teachers','tplans'));
        return null;
    }

    private function getHomePage() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://60.216.13.32/");

        $headers = [
            //"User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:43.0) Gecko/20100101 Firefox/43.0",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language: en-US,en;q=0.5",
            "Accept-Encoding: gzip, deflate",
            "Connection: keep-alive"
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER  ,true);         // need this to read the cookie
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,true); // read into a string
        $content=curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    private function postLoginForm($username, $password, $session_id, $viewState) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,"http://60.216.13.32/Default3.aspx");
        $headers = [
            //"User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:43.0) Gecko/20100101 Firefox/43.0",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language: en-US,en;q=0.5",
            "Accept-Encoding: gzip, deflate",
            //"Referer: http://60.216.13.32/",
            "Connection: keep-alive"
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_COOKIE, "ASP.NET_SessionId=$session_id");
        curl_setopt($ch, CURLOPT_HEADER  ,true);         // need this to read the cookie
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "__VIEWSTATE=$viewState&tbYHM=$username&tbPSW=$password&ddlSF=%BD%CC%CA%A6&imgDL.x=32&imgDL.y=12");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $content=curl_exec($ch);
        curl_close ($ch);
        return $content;
    }

    /*private function getJsmainfs($session_id) {
        $ch = curl_init();

        $url="http://60.216.13.32/jsmainfs.aspx?xh=$username";
        curl_setopt($ch, CURLOPT_URL,$url);

        $headers = [
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,s/s;q=0.8",
            "Accept-Language: en-US,en;q=0.5",
            "Accept-Encoding: gzip, deflate",
            "Connection: keep-alive",
            //"User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:43.0) Gecko/20100101 Firefox/43.0",
            "Host: 60.216.13.32"
            //"Referer: http://60.216.13.32/"
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_COOKIE, "ASP.NET_SessionId=$session_id");
        curl_setopt($ch, CURLOPT_HEADER  ,true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,true); // read into a string

        $content=curl_exec($ch);
        curl_close ($ch);
        return $content;
    }*/

    private function getScoresForm($url,$session_id) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);

        $headers = [
            //"User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:43.0) Gecko/20100101 Firefox/43.0",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language: en-US,en;q=0.5",
            "Accept-Encoding: gzip, deflate",
            "Connection: keep-alive",
            "Host: 60.216.13.32",
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_COOKIE, "ASP.NET_SessionId=$session_id");
        curl_setopt($ch, CURLOPT_HEADER  ,true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,true); // read into a string

        $content=curl_exec($ch);
        curl_close ($ch);
        return $content;
    }

    private function getJsleft($url,$session_id) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);

        $headers = [
            //"User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:43.0) Gecko/20100101 Firefox/43.0",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language: en-US,en;q=0.5",
            "Accept-Encoding: gzip, deflate",
            "Connection: keep-alive",
            "Host: 60.216.13.32",
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_COOKIE, "ASP.NET_SessionId=$session_id");
        curl_setopt($ch, CURLOPT_HEADER  ,true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,true); // read into a string

        $content=curl_exec($ch);
        curl_close ($ch);
        return $content;
    }

    private function postScoresForm($url,$session_id,$viewState,$scores) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);

        $headers = [
            //"User-Agent: Mozilla/5.0 (Windows NT 5.1; rv:43.0) Gecko/20100101 Firefox/43.0",
            "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
            "Accept-Language: en-US,en;q=0.5",
            "Accept-Encoding: gzip, deflate",
            "Connection: keep-alive",
            "Host: 60.216.13.32",
            //"Referer: http://60.216.13.32$url"
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_COOKIE, "ASP.NET_SessionId=$session_id");
        curl_setopt($ch, CURLOPT_POST, 1);

        $pf="__EVENTTARGET=Button1".
            "&__EVENTARGUMENT="."&__VIEWSTATE=$viewState".
            "&Dgfcj=%BB%BA%BF%BC&txtChanged=1".
            "&jfz=%B0%D9%B7%D6%D6%C6".
            "&psb=60". // classroom
            "&qmb=40". // final
            "&Dzpcj=%B0%D9%B7%D6%D6%C6";

            foreach($scores as $score) {
                $gidx=$score['gidx'];
                $pf.="&DataGrid1%3A_ctl$gidx%3Aps=".$score['a'];
                $pf.="&DataGrid1%3A_ctl$gidx%3Aqm=".$score['b'];
                //"&DataGrid1%3A_ctl2%3Azp=". // ??
            }

            /*"&DataGrid1%3A_ctl2%3Aps=".$scores[1]['a'].
            "&DataGrid1%3A_ctl2%3Aqm=".$scores[1]['b'].
            "&DataGrid1%3A_ctl2%3Azp=". // ??

            "&DataGrid1%3A_ctl3%3Aps=".$scores[2]['a'].
            "&DataGrid1%3A_ctl3%3Aqm=".$scores[2]['b'].
            "&DataGrid1%3A_ctl3%3Azp=".

            "&DataGrid1%3A_ctl4%3Aps=1".
            "&DataGrid1%3A_ctl4%3Aqm=1".
            "&DataGrid1%3A_ctl4%3Azp=".

            "&DataGrid1%3A_ctl5%3Aps=7&DataGrid1%3A_ctl5%3Aqm=8&DataGrid1%3A_ctl5%3Azp=8&DataGrid1
%3A_ctl6%3Aps=9&DataGrid1%3A_ctl6%3Aqm=10&DataGrid1%3A_ctl6%3Azp=10&DataGrid1%3A_ctl7%3Aps=11&DataGrid1
%3A_ctl7%3Aqm=12&DataGrid1%3A_ctl7%3Azp=12&DataGrid1%3A_ctl8%3Aps=13&DataGrid1%3A_ctl8%3Aqm=14&DataGrid1
%3A_ctl8%3Azp=14&DataGrid1%3A_ctl9%3Aps=15&DataGrid1%3A_ctl9%3Aqm=16&DataGrid1%3A_ctl9%3Azp=16&DataGrid1
%3A_ctl10%3Aps=17&DataGrid1%3A_ctl10%3Aqm=18&DataGrid1%3A_ctl10%3Azp=18&DataGrid1%3A_ctl11%3Aps=19&DataGrid1
%3A_ctl11%3Aqm=20&DataGrid1%3A_ctl11%3Azp=20&DataGrid1%3A_ctl12%3Aps=21&DataGrid1%3A_ctl12%3Aqm=22&DataGrid1
%3A_ctl12%3Azp=22&DataGrid1%3A_ctl13%3Aps=23&DataGrid1%3A_ctl13%3Aqm=24&DataGrid1%3A_ctl13%3Azp=24&DataGrid1
%3A_ctl14%3Aps=25&DataGrid1%3A_ctl14%3Aqm=26&DataGrid1%3A_ctl14%3Azp=26&DataGrid1%3A_ctl15%3Aps=27&DataGrid1
%3A_ctl15%3Aqm=28&DataGrid1%3A_ctl15%3Azp=28&DataGrid1%3A_ctl16%3Aps=29&DataGrid1%3A_ctl16%3Aqm=30&DataGrid1
%3A_ctl16%3Azp=30&DataGrid1%3A_ctl17%3Aps=31&DataGrid1%3A_ctl17%3Aqm=32&DataGrid1%3A_ctl17%3Azp=32&DataGrid1
%3A_ctl18%3Aps=33&DataGrid1%3A_ctl18%3Aqm=34&DataGrid1%3A_ctl18%3Azp=34&DataGrid1%3A_ctl19%3Aps=35&DataGrid1
%3A_ctl19%3Aqm=36&DataGrid1%3A_ctl19%3Azp=36&DataGrid1%3A_ctl20%3Aps=37&DataGrid1%3A_ctl20%3Aqm=38&DataGrid1
%3A_ctl20%3Azp=38&DataGrid1%3A_ctl21%3Aps=39&DataGrid1%3A_ctl21%3Aqm=40&DataGrid1%3A_ctl21%3Azp=40&DataGrid1
%3A_ctl22%3Aps=41&DataGrid1%3A_ctl22%3Aqm=42&DataGrid1%3A_ctl22%3Azp=42&DataGrid1%3A_ctl23%3Aps=43&DataGrid1
%3A_ctl23%3Aqm=44&DataGrid1%3A_ctl23%3Azp=44&DataGrid1%3A_ctl24%3Aps=45&DataGrid1%3A_ctl24%3Aqm=46&DataGrid1
%3A_ctl24%3Azp=46&DataGrid1%3A_ctl25%3Aps=47&DataGrid1%3A_ctl25%3Aqm=48&DataGrid1%3A_ctl25%3Azp=48&DataGrid1
%3A_ctl26%3Aps=49&DataGrid1%3A_ctl26%3Aqm=50&DataGrid1%3A_ctl26%3Azp=50&DataGrid1%3A_ctl27%3Aps=51&DataGrid1
%3A_ctl27%3Aqm=52&DataGrid1%3A_ctl27%3Azp=52&DataGrid1%3A_ctl28%3Aps=53&DataGrid1%3A_ctl28%3Aqm=54&DataGrid1
%3A_ctl28%3Azp=54&DataGrid1%3A_ctl29%3Aps=55&DataGrid1%3A_ctl29%3Aqm=56&DataGrid1%3A_ctl29%3Azp=56*/
        $pf.="&rbntl=Excel%CA%E4%B3%F6&pslr=&Txt_save=false&tbXXMC=%C9%BD%B6%AB%C2%C3%D3%CE%D6%B0%D2%B5%D1%A7%D4%BA";
        $sz=strlen($pf);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $pf);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $content = curl_exec ($ch);
        $s=curl_error($ch);
        curl_close ($ch);
        return $s;
    }


    private function get_session_id($content) {
        // 1.1 Get the session id
        $cookies = array();
        preg_match_all('/Set-Cookie:(?<cookie>\s{0,}.*)$/im', $content, $cookies);
        parse_str($cookies['cookie'][0],$target);
        $c=substr($target['ASP_NET_SessionId'],0,24);
        return $c;
    }

    public function view($id = null) {
        $this->request->allowMethod(['get']);

        $section = $this->Sections->get(
            $id,[
                'contain'=>['Cohorts.Majors','Semesters','Subjects','Teachers','Tplans'],
            ]
        );
        $this->set('section', $section);

        // Now get the classes associated with this section
        $query=$this->Sections->Clazzes->find()
            ->where(['section_id'=>$id])
            ->order(['event_datetime'=>'desc']);
        $this->set('clazzes',$query);
    }
}
