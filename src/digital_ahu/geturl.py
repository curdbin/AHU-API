#coding:utf-8
import urllib.request
import zlib
from io import BytesIO
import gzip
import re
import time
def loadData(url):
    req = urllib.request.Request(url)
    req.add_header('User-Agent','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36')
    page = urllib.request.urlopen(req)
    content = page.read()
    encoding = page.info().get('Content-Encoding')
    if encoding == 'gzip':
        content = gzipp(content)
    elif encoding == 'deflate':
        content = deflate(content)
    return content

def postData(username,pswd,lt,servicename):
    url="http://101.76.160.28:8001/cas/login"
    req = urllib.request.Request(url)
    data = urllib.parse.urlencode({'encodedService': 'http%3a%2f%2fportal.ahu.edu.cn%3a8001%2fdcp%2findex.jsp', 'service': servicename, 'serviceName': 'null','loginErrCnt':'0','username':username,'password':pswd,'lt':lt})
    data = data.encode('utf-8')
    #print(data)
    req.add_header('User-Agent','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36')
    page = urllib.request.urlopen(req,data)
    content = page.read()
    encoding = page.info().get('Content-Encoding')
    if encoding == 'gzip':
        content = gzipp(content)
    elif encoding == 'deflate':
        content = deflate(content)
    return content

def gzipp(data):
    buf = BytesIO(data)
    f = gzip.GzipFile(fileobj=buf)
    return f.read()

def deflate(data):
    try:
        return zlib.decompress(data, -zlib.MAX_WBITS)
    except zlib.error:
        return zlib.decompress(data)
time1=time.time()
res= loadData("http://i.ahu.cn").decode('utf-8')
pat = re.compile('<input type="hidden" name="lt" value="(.*)" />')
mats = pat.findall(res);
#print (mats[0])
#本程序 Westery 开发 https://westery.cn https://2git.cn
username=""  #安大在线学号
password=""  #密码
servicename1="http://portal.ahu.edu.cn:8001/dcp/index.jsp" #安大在线
servicename2="http://jw3.ahu.cn/login_cas.aspx" #教务
servicename3="http://101.76.160.244:8080/User" #电子课表
servicename4="http://101.76.160.144/CASahu/ahucas?redirectUrl=" #校园卡服务中心
servicename5="http://bz.ahu.edu.cn/LoginByCas"  #自助报账
res2= postData(username,password,mats[0],servicename5).decode('gbk')
#print (res2)
pat2 = re.compile('window.location.href="(.*)";')
mats2 = pat2.findall(res2);
time2=time.time()
print (mats2[0])
print ("生成用时:"+ str (time2-time1))
