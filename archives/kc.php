
<?php
/**
 * Created by PhpStorm.
 * User: xubowen
 * Date: 2016/10/27
 * Time: 下午10:30
 */
echo decodeUnicode(json_encode(classresult("2014010xxxxx","********")));
//unicode转utf-8
function decodeUnicode($str)
{
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
        create_function(
            '$matches',
            'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
        ),
        $str);
}
//获取隐藏值
function getView(){
    $url = 'http://jw1.ahu.cn/default2.aspx';
    $result = curl_request($url);
    $pattern = '/<input type="hidden" name="__VIEWSTATE" value="(.*?)" \/>/is';
    preg_match_all($pattern, $result, $matches);
    $res[0] = $matches[1][0];
    return $res[0] ;
}
//获取cookie
function login($xh,$pwd){
    $url = 'http://jw1.ahu.cn/default2.aspx';
    $post['__VIEWSTATE'] = getView();
    $post['txtUserName'] = $xh; //填写学号
    $post['TextBox2'] = $pwd;  //填写密码
    $post['txtSecretCode'] = '';
    $post['lbLanguage'] = '';
    $post['hidPdrs'] = '';
    $post['hidsc'] = '';
    $post['RadioButtonList1'] = iconv('utf-8', 'gb2312', '学生');
    $post['Button1'] = iconv('utf-8', 'gb2312', '登录');
    $result = curl_request($url,$post,'', 1);
    return $result['cookie'];
}
//返回教室查询页面的隐藏值
function getViewJs($cookie,$xh){
    $url = "http://jw1.ahu.cn/xxjsjy.aspx?xh={$xh}";
    $result = curl_request($url,'',$cookie);
    $pattern = '/<input type="hidden" name="__VIEWSTATE" value="(.*?)" \/>/is';
    preg_match_all($pattern, $result, $matches);
    $res[0] = $matches[1][0];
    return $res[0] ;
}
function curl_request($url,$post='',$cookie='', $returnCookie=0){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
    curl_setopt($curl, CURLOPT_REFERER, "http://jw1.ahu.cn"); //填写教务系统url
 
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
//返回课表字符串
function classresult($xh,$pwd)
{
    date_default_timezone_set("PRC"); //时区设置
    $classList = "";//声明课表变量
    $cookie = login($xh, $pwd);
    $view = getViewJs($cookie, $xh);//验证密码是否正确
    //如果密码正确
    if (!empty($view)) {
        $url = "http://jw1.ahu.cn/xskbcx.aspx?xh={$xh}";
        $result = curl_request($url, '', $cookie);
        preg_match_all('/<table id="Table1"[\w\W]*?>([\w\W]*?)<\/table>/', $result, $out);
        $table = mb_convert_encoding($out[0][0],  "utf-8","gb2312");
        preg_match_all('/<td [\w\W]*?>([\w\W]*?)<\/td>/', $table, $out);
        $td = $out[1];
        $length = count($td);
        //获得课程列表
        for ($i = 0; $i < $length; $i++) {
            $td[$i] = str_replace("<br>", "", $td[$i]);
            $reg = "/{(.*)}/";
            if (!preg_match_all($reg, $td[$i], $matches)) {
                unset($td[$i]);
            }
        }
        $td = array_values($td); //将课程列表数组重新索引
        //将课表转换成数组形式
        function converttoTable($table)
        {
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
            $week = array("sun" => "周日", "mon" => "周一", "tues" => "周二", "wed" => "周三", "thur" => "周四", "fri" => "周五", "sat" => "周六");
            $order = array('1,2', '3,4', '5,6', '7,8', '9,10');
            foreach ($table as $key => $value) {
                $class = $value;
                foreach ($week as $key => $weekDay) {
                    $pos = strpos($class, $weekDay);
                    if ($pos) {
                        $weekArrayDay = $key; //获取list数组中的第一维key
                        foreach ($order as $key => $orderClass) {
                            $pos = strpos($class, $orderClass);
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
 
        //调用函数
        return converttoTable($td);
    } else {
        return 0;
    }
}
//空自习室
function roomresult(){
    $xh = ""; //设置学号
    $pwd = "";  //学号对应的密码
 
    $cookie = $this->login($xh,$pwd);
    $url = "http://jw1.ahu.cn/xs_main.aspx?xh={$xh}";
    $result = curl_request($url,'',$cookie);  //保存的cookies
 
    $url="http://jw1.ahu.cn/xxjsjy.aspx?xh={$xh}";
    $post['Button2'] = iconv('utf-8', 'gb2312', '空教室查询');
    $post['__EVENTARGUMENT']='';
    $post['__EVENTTARGET']='';
    $post['__VIEWSTATE'] = $this->getViewJs($cookie,$xh);
    $post['ddlDsz'] = iconv('utf-8', 'gb2312', '单');
    $post['ddlSyXn'] = '2017-2018'; //学年
    $post['ddlSyxq'] = '1';
    $post['jslb'] = '';
    $post['xiaoq'] = '';
 
    $post['kssj']=$_GET['start'];  //提交的开始查询时间
    $post['sjd']=$_GET['class'];//提交的课程节次
 
    $post['xn']='2017-2018';//所在学年
    $post['xq']='1';//所在学期
    $post['xqj']='6';//当天星期几
    $post['dpDataGrid1:txtPageSize']=90;//每页显示条数
 
    $result = curl_request($url,$post,$cookie,0);
 
    preg_match_all('/<span[^>]+>[^>]+span>/',$result,$out);
    $tip = iconv('gb2312', 'utf-8', $out[0][3]);//获取页面前部的提示内容
    preg_match_all('/<table[\w\W]*?>([\w\W]*?)<\/table>/',$result,$out);
    $table = iconv('gb2312', 'utf-8', $out[0][0]); //获取查询列表
 
    $this->load->view("classroom",array('tip'=>$tip,'table'=>$table));
}