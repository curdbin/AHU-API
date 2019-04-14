#!/usr/bin/python3

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

"""
Get direct url of digital AHU.
"""

import urllib.request
import zlib
from io import BytesIO
import gzip
import re
import configparser
import os


def __load_data(url):
    req = urllib.request.Request(url)
    req.add_header('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36')
    page = urllib.request.urlopen(req)
    content = page.read()
    encoding = page.info().get('Content-Encoding')
    if encoding == 'gzip':
        content = __gzipp(content)
    elif encoding == 'deflate':
        content = __deflate(content)
    return content


def __post_data(username, pswd, lt, service_name):
    url = 'http://101.76.160.28:8001/cas/login'
    req = urllib.request.Request(url)
    data = urllib.parse.urlencode({
        'encodedService': 'http%3a%2f%2fportal.ahu.edu.cn%3a8001%2fdcp%2findex.jsp',
        'service': service_name,
        'serviceName': 'null',
        'loginErrCnt':'0',
        'username':username,
        'password':pswd,
        'lt':lt
    })
    data = data.encode('utf-8')
    req.add_header('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36')
    page = urllib.request.urlopen(req, data)
    content = page.read()
    encoding = page.info().get('Content-Encoding')
    if encoding == 'gzip':
        content = __gzipp(content)
    elif encoding == 'deflate':
        content = __deflate(content)
    return content


def __gzipp(data):
    buf = BytesIO(data)
    file = gzip.GzipFile(fileobj=buf)
    return file.read()


def __deflate(data):
    try:
        return zlib.decompress(data, -zlib.MAX_WBITS)
    except zlib.error:
        return zlib.decompress(data)


def geturl(service_type):
    """
    获取数字安大的URL (service_type : integer)
    1: 数字安大
    2: 旧版教务系统
    3: 电子课表
    4：校园卡服务中心
    5: 自助报账
    """
    # load configrations
    parentpath = os.path.abspath(os.path.dirname(os.getcwd()))
    cfgpath = os.path.join(parentpath, 'src/auth.ini')
    conf = configparser.ConfigParser()
    conf.read(cfgpath, encoding='utf-8')
    items = conf.items('ALL')
    stu_id = items[0][1] # 学生学号
    d_passwd = items[2][1] # 数字安大登录密码（默认是身份证号）

    res = __load_data('http://i.ahu.cn').decode('utf-8')
    pat = re.compile('<input type="hidden" name="lt" value="(.*)" />')
    mats = pat.findall(res)

    if service_type == 1:
        target_url = 'http://portal.ahu.edu.cn:8001/dcp/index.jsp'
    elif service_type == 2:
        target_url = 'http://jw3.ahu.cn/login_cas.aspx'
    elif service_type == 3:
        target_url = 'http://101.76.160.244:8080/User/Schedule'
    elif service_type == 4:
        target_url = 'http://101.76.160.144/CASahu/ahucas?redirectUrl='
    else:
        target_url = 'http://bz.ahu.edu.cn/LoginByCas'
    res2 = __post_data(stu_id, d_passwd, mats[0], target_url).decode('gbk')

    pat2 = re.compile('window.location.href="(.*)";')
    mats2 = pat2.findall(res2)

    return mats2[0]
