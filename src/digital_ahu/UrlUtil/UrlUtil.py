#!/usr/bin/python3

# MIT License

# Copyright (c) 2019 Westery <i@2git.cn>
#                    lolimay <lolimay@lolimay.cn>
# Permission is hereby granted, free of charge, to any person obtaining a copy
# of this software and associated documentation files (the "Software"), to deal
# in the Software without restriction, including without limitation the rights
# to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
# copies of the Software, and to permit persons to whom the Software is
# furnished to do so, subject to the following conditions:

# The above copyright notice and this permission notice shall be included in all
# copies or substantial portions of the Software.

# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
# OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
# SOFTWARE.

# coding=utf-8

import urllib.request
import zlib
from io import BytesIO
import gzip
import re
import configparser
import os

def __loadData(url):
    req = urllib.request.Request(url)
    req.add_header('User-Agent','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36')
    page = urllib.request.urlopen(req)
    content = page.read()
    encoding = page.info().get('Content-Encoding')
    if encoding == 'gzip':
        content = __gzipp(content)
    elif encoding == 'deflate':
        content = __deflate(content)
    return content

def __postData(username, pswd, lt, servicename):
    url="http://101.76.160.28:8001/cas/login"
    req = urllib.request.Request(url)
    data = urllib.parse.urlencode({'encodedService': 'http%3a%2f%2fportal.ahu.edu.cn%3a8001%2fdcp%2findex.jsp', 'service': servicename, 'serviceName': 'null','loginErrCnt':'0','username':username,'password':pswd,'lt':lt})
    data = data.encode('utf-8')
    req.add_header('User-Agent','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36')
    page = urllib.request.urlopen(req,data)
    content = page.read()
    encoding = page.info().get('Content-Encoding')
    if encoding == 'gzip':
        content = __gzipp(content)
    elif encoding == 'deflate':
        content = __deflate(content)
    return content

def __gzipp(data):
    buf = BytesIO(data)
    f = gzip.GzipFile(fileobj=buf)
    return f.read()

def __deflate(data):
    try:
        return zlib.decompress(data, -zlib.MAX_WBITS)
    except zlib.error:
        return zlib.decompress(data)
def geturl(serviceType):
    """获取数字安大的URL
    serviceType : integer
    1: 数字安大
    """
    # load configrations
    parentpath = os.path.abspath(os.path.dirname(os.getcwd()))
    cfgpath = os.path.join(parentpath, "auth.ini")
    conf = configparser.ConfigParser()
    conf.read(cfgpath, encoding="utf-8")
    items=conf.items('ALL')
    stuId=items[0][1] # 学生学号
    dPasswd=items[2][1] # 数字安大登录密码（默认是身份证号）

    res= __loadData("http://i.ahu.cn").decode('utf-8')
    pat = re.compile('<input type="hidden" name="lt" value="(.*)" />')
    mats = pat.findall(res)

    service1="http://portal.ahu.edu.cn:8001/dcp/index.jsp" # 数字安大
    service2="http://jw3.ahu.cn/login_cas.aspx" # 旧版教务系统
    service3="http://101.76.160.244:8080/User" # 电子课表
    service4="http://101.76.160.144/CASahu/ahucas?redirectUrl=" # 校园卡服务中心
    service5="http://bz.ahu.edu.cn/LoginByCas"  # 自助报账
    res2= __postData(stuId, dPasswd, mats[0], service2).decode('gbk')

    pat2 = re.compile('window.location.href="(.*)";')
    mats2 = pat2.findall(res2)

    return mats2[0]