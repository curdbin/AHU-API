
<?php
/**
 * Created by PhpStorm.
 * User: xubowen
 * Date: 2016/10/27
 * Time: ����10:30
 */
echo decodeUnicode(json_encode(classresult("2014010xxxxx","********")));
//unicodeתutf-8
function decodeUnicode($str)
{
    return preg_replace_callback('/\\\\u([0-9a-f]{4})/i',
        create_function(
            '$matches',
            'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
        ),
        $str);
}
//��ȡ����ֵ
function getView(){
    $url = 'http://jw1.ahu.cn/default2.aspx';
    $result = curl_request($url);
    $pattern = '/<input type="hidden" name="__VIEWSTATE" value="(.*?)" \/>/is';
    preg_match_all($pattern, $result, $matches);
    $res[0] = $matches[1][0];
    return $res[0] ;
}
//��ȡcookie
function login($xh,$pwd){
    $url = 'http://jw1.ahu.cn/default2.aspx';
    $post['__VIEWSTATE'] = getView();
    $post['txtUserName'] = $xh; //��дѧ��
    $post['TextBox2'] = $pwd;  //��д����
    $post['txtSecretCode'] = '';
    $post['lbLanguage'] = '';
    $post['hidPdrs'] = '';
    $post['hidsc'] = '';
    $post['RadioButtonList1'] = iconv('utf-8', 'gb2312', 'ѧ��');
    $post['Button1'] = iconv('utf-8', 'gb2312', '��¼');
    $result = curl_request($url,$post,'', 1);
    return $result['cookie'];
}
//���ؽ��Ҳ�ѯҳ�������ֵ
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
    curl_setopt($curl, CURLOPT_REFERER, "http://jw1.ahu.cn"); //��д����ϵͳurl
 
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
//���ؿα��ַ���
function classresult($xh,$pwd)
{
    date_default_timezone_set("PRC"); //ʱ������
    $classList = "";//�����α����
    $cookie = login($xh, $pwd);
    $view = getViewJs($cookie, $xh);//��֤�����Ƿ���ȷ
    //���������ȷ
    if (!empty($view)) {
        $url = "http://jw1.ahu.cn/xskbcx.aspx?xh={$xh}";
        $result = curl_request($url, '', $cookie);
        preg_match_all('/<table id="Table1"[\w\W]*?>([\w\W]*?)<\/table>/', $result, $out);
        $table = mb_convert_encoding($out[0][0],  "utf-8","gb2312");
        preg_match_all('/<td [\w\W]*?>([\w\W]*?)<\/td>/', $table, $out);
        $td = $out[1];
        $length = count($td);
        //��ÿγ��б�
        for ($i = 0; $i < $length; $i++) {
            $td[$i] = str_replace("<br>", "", $td[$i]);
            $reg = "/{(.*)}/";
            if (!preg_match_all($reg, $td[$i], $matches)) {
                unset($td[$i]);
            }
        }
        $td = array_values($td); //���γ��б�������������
        //���α�ת����������ʽ
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
            $week = array("sun" => "����", "mon" => "��һ", "tues" => "�ܶ�", "wed" => "����", "thur" => "����", "fri" => "����", "sat" => "����");
            $order = array('1,2', '3,4', '5,6', '7,8', '9,10');
            foreach ($table as $key => $value) {
                $class = $value;
                foreach ($week as $key => $weekDay) {
                    $pos = strpos($class, $weekDay);
                    if ($pos) {
                        $weekArrayDay = $key; //��ȡlist�����еĵ�һάkey
                        foreach ($order as $key => $orderClass) {
                            $pos = strpos($class, $orderClass);
                            if ($pos) {
                                $weekArrayOrder = $orderClass; //��ȡ�ÿγ��ǵڼ���
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
 
        //���ú���
        return converttoTable($td);
    } else {
        return 0;
    }
}
//����ϰ��
function roomresult(){
    $xh = ""; //����ѧ��
    $pwd = "";  //ѧ�Ŷ�Ӧ������
 
    $cookie = $this->login($xh,$pwd);
    $url = "http://jw1.ahu.cn/xs_main.aspx?xh={$xh}";
    $result = curl_request($url,'',$cookie);  //�����cookies
 
    $url="http://jw1.ahu.cn/xxjsjy.aspx?xh={$xh}";
    $post['Button2'] = iconv('utf-8', 'gb2312', '�ս��Ҳ�ѯ');
    $post['__EVENTARGUMENT']='';
    $post['__EVENTTARGET']='';
    $post['__VIEWSTATE'] = $this->getViewJs($cookie,$xh);
    $post['ddlDsz'] = iconv('utf-8', 'gb2312', '��');
    $post['ddlSyXn'] = '2017-2018'; //ѧ��
    $post['ddlSyxq'] = '1';
    $post['jslb'] = '';
    $post['xiaoq'] = '';
 
    $post['kssj']=$_GET['start'];  //�ύ�Ŀ�ʼ��ѯʱ��
    $post['sjd']=$_GET['class'];//�ύ�Ŀγ̽ڴ�
 
    $post['xn']='2017-2018';//����ѧ��
    $post['xq']='1';//����ѧ��
    $post['xqj']='6';//�������ڼ�
    $post['dpDataGrid1:txtPageSize']=90;//ÿҳ��ʾ����
 
    $result = curl_request($url,$post,$cookie,0);
 
    preg_match_all('/<span[^>]+>[^>]+span>/',$result,$out);
    $tip = iconv('gb2312', 'utf-8', $out[0][3]);//��ȡҳ��ǰ������ʾ����
    preg_match_all('/<table[\w\W]*?>([\w\W]*?)<\/table>/',$result,$out);
    $table = iconv('gb2312', 'utf-8', $out[0][0]); //��ȡ��ѯ�б�
 
    $this->load->view("classroom",array('tip'=>$tip,'table'=>$table));
}