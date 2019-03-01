<?php
error_reporting(0);
//$ck=$_SESSION['ck'];

function curl_request($url,$post='',$cookie='', $returnCookie=0){
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
            curl_setopt($curl, CURLOPT_REFERER, "http://jw2.ahu.cn/default2.aspx"); //填写教务系统url
            if($post) {
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
            }
            if($cookie) {
                curl_setopt($curl, CURLOPT_COOKIE, $cookie);
            }
            curl_setopt($curl, CURLOPT_HEADER, $returnCookie);
            curl_setopt($curl, CURLOPT_TIMEOUT, 20);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($curl);
            if (curl_errno($curl)) {
                return curl_error($curl);
            }
            curl_close($curl);
            if($returnCookie){
                list($header, $body) = explode("\r\n\r\n", $data, 2);
                preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
                $info['cookie']  = substr($matches[1][0], 1);
                $info['content'] = $body;
                return $info;
            }else{
                return $data;
            }
    }
     
function getView(){
    $url = 'http://jw2.ahu.cn/default2.aspx';
    $result = curl_request($url);
    $pattern = '/<input type="hidden" name="__VIEWSTATE" value="(.*?)" \/>/is';
    preg_match_all($pattern, $result, $matches);
    $res[0] = $matches[1][0];
           
    return $res[0] ;
}
function getView2(){
     $res;
     $url = 'http://jw2.ahu.cn/default2.aspx';
     $result = curl_request($url);
     $pattern = '/<input type="hidden" name="__VIEWSTATE" value="(.*?)" \/>/is';
     preg_match_all($pattern, $result, $matches);
     $res[0] = $matches[1][0];
      $pattern = '/<input type="hidden" name="__VIEWSTATEGENERATOR" value="(.*?)" \/>/is';
     preg_match_all($pattern, $result, $matches);
     $res[1] = $matches[1][0];
     return $res;
}
function login($xh,$pwd,$cd,$ck){
        $url = 'http://jw2.ahu.cn/default2.aspx';
		$fh=array();
		$fh=getView2();
		echo "</br>".$fh[0].$fh[1]."</br>";
        $post['__VIEWSTATE'] = $fh[0];
        $post['txtUserName'] = $xh; //填写学号
        $post['TextBox2'] = $pwd;  //填写密码
        $post['txtSecretCode'] = $cd;
        $post['lbLanguage'] = '';
        $post['hidPdrs'] = '';
        $post['hidsc'] = '';
        $post['RadioButtonList1'] = iconv('utf-8', 'gb2312', '学生');
        $post['Button1'] = iconv('utf-8', 'gb2312', '登录');
        $result = curl_request($url,$post,$ck, 1);
		//print_r($result);
        return $result['cookie'];
    }
 
function converttoTable($table){
                $list = array(
                    'sun' => array(
                        '1,2' => '',
                        '3,4' => '',
                        '5,6' => '',
                        '7,8' => '',
                        '9,10' => ''
                    ),
                    'mon' => array(
                        '1,2' => '',
                        '3,4' => '',
                        '5,6' => '',
                        '7,8' => '',
                        '9,10' => ''
                    ),
                    'tues' => array(
                        '1,2' => '',
                        '3,4' => '',
                        '5,6' => '',
                        '7,8' => '',
                        '9,10' => ''
                    ),
                    'wed' => array(
                        '1,2' => '',
                        '3,4' => '',
                        '5,6' => '',
                        '7,8' => '',
                        '9,10' => ''
                    ),
                    'thur' => array(
                        '1,2' => '',
                        '3,4' => '',
                        '5,6' => '',
                        '7,8' => '',
                        '9,10' => ''
                    ),
                    'fri' => array(
                        '1,2' => '',
                        '3,4' => '',
                        '5,6' => '',
                        '7,8' => '',
                        '9,10' => ''
                    ),
                    'sat' => array(
                        '1,2' => '',
                        '3,4' => '',
                        '5,6' => '',
                        '7,8' => '',
                        '9,10' => ''
                    )
                );
                $week = array("sun"=>"周日","mon"=>"周一","tues"=>"周二","wed"=>"周三","thur"=>"周四","fri"=>"周五","sat"=>"周六");
                $order = array('1,2','3,4','5,6','7,8','9,10');
                foreach ($table as $key => $value) {
                    $class = $value;
                    foreach ($week as $key => $weekDay) {
                        $pos = strpos($class,$weekDay);
                        // echo $pos;
                        if ($pos) {
                            $weekArrayDay = $key; //获取list数组中的第一维key 
                            foreach ($order as $key => $orderClass) {
                                $pos = strpos($class,$orderClass);
                                if ($pos) {
                                    $weekArrayOrder = $orderClass; //获取该课程是第几节
                                    break;
                                }
                            }
                            break;
                        }
                    }
                    $list[$weekArrayDay][$weekArrayOrder] = $class;
                }
                return $list;
            }
     
function classresult($xh,$cookie){
        date_default_timezone_set("PRC"); //时区设置
        $classList = "";//声明课表变量
        $view = 1;
 
        //如果密码正确
        if (!empty($view)) {
            $url = "http://jw2.ahu.cn/xskbcx.aspx?xh={$xh}";
            $result = curl_request($url,'',$cookie);  //保存的cookies
           // print_r($result);
            preg_match_all('/<table id="Table1"[\w\W]*?>([\w\W]*?)<\/table>/',$result,$out);
            $table = $out[0][0]; //获取整个课表
 
            preg_match_all('/<td [\w\W]*?>([\w\W]*?)<\/td>/',$table,$out);
            $td = $out[1];
            $length = count($td);
 
            //获得课程列表
            for ($i=0; $i < $length; $i++) { 
                $td[$i] = str_replace("<br>", "", $td[$i]);
 
                $reg = "/{(.*)}/";
             
                if (!preg_match_all($reg, $td[$i], $matches)) {
                    unset($td[$i]);
                }
            }
 
            $td = array_values($td); //将课程列表数组重新索引
            $tdLength = count($td);
            for ($i=0; $i < $tdLength; $i++) { 
                $td[$i] = iconv('GB2312','UTF-8',$td[$i]);
            }
                     
            //调用函数
            return converttoTable($td);
        }else{
            return 0;
        }
    }



    function unicodeDecode($name){
        $json = '{"str":"'.$name.'"}';
        $arr = json_decode($json,true);
        if(empty($arr)) return ''; 
        return $arr['str'];
      }


function chengji($xh,$cookie){ 
    $url2="http://jw2.ahu.cn/xscjcx.aspx?xh=".$xh;
    $resultt = curl_request($url2,"",$cookie);
    //echo "</br>huoqude: ";
    //print_r($resultt);
    //echo "</br>enennen";
    //$viewstate=curl_request($url2,$post3,"");
    $pattern = '/<input type="hidden" name="__VIEWSTATE" value="(.*?)" \/>/is';
    preg_match_all($pattern, $resultt, $matches);
    $res[0] = $matches[1][0];
     $pattern = '/<input type="hidden" name="__VIEWSTATEGENERATOR" value="(.*?)" \/>/is';
    preg_match_all($pattern, $resultt, $matches);
    $res[1] = $matches[1][0];





   //preg_match_all('/<input type="hidden" name="__VIEWSTATE" value="([^<>]+)" \/>/', $viewstate, $vs);
   // $state=$vs[1][0];  //$state存放一会post的__VIEWSTATE
    echo "aw afaw:::::".$res[0];
    $post3=array(
        '__EVENTTARGET'=>'',
        '__EVENTARGUMENT'=>'',
        '__VIEWSTATE'=>$res[0],
        'hidLanguage'=>'',
       // 'ddlXN'=>'2017-2018',  //当前学年
       // 'ddlXQ'=>'1',  //当前学期
        'ddl_kcxz'=>'',
        'btn_zcj'=>iconv('utf-8', 'gb2312', '历年成绩')
        //'btn_xq'=>'%D1%A7%C6%DA%B3%C9%BC%A8'  //“学期成绩”的gbk编码，视情况而定
        
    );
    $result3=curl_request($url2,$post3,$cookie);
    print_r($result3);
    

    //   $content=login_post($url2,$cookie,http_build_query($post));
    //   echo $content;


    



}

function kebiao($xh,$cookie){
        $url = "http://jw2.ahu.cn/xskbcx.aspx?xh={$xh}";
        $result = curl_request($url,'',$cookie);  //保存的cookies
        print_r($result);
                 
    }

$xh=$_GET["xh"];
$pwd=$_GET["pwd"];
$cd=$_GET["cd"];
$ck="ASP.NET_SessionId=".$_GET["ck"];
login($xh,$pwd,$cd,$ck);
//echo "awra".$xh.$pwd.$cd;
kebiao($xh,$ck);
//echo unicodeDecode(json_encode(classresult($xh,$ck)));
//echo "kebiao:".json_encode(classresult($xh,$ck))."</br>chengji:";
chengji($xh,$ck);


?>