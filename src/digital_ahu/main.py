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

import urlutil
import requests
import json
from bs4 import BeautifulSoup
import re
import sys
import random

home_url = urlutil.geturl(3)

UAs = [
    "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; AcooBrowser; .NET CLR 1.1.4322; .NET CLR 2.0.50727)",
    "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; Acoo Browser; SLCC1; .NET CLR 2.0.50727; Media Center PC 5.0; .NET CLR 3.0.04506)",
    "Mozilla/4.0 (compatible; MSIE 7.0; AOL 9.5; AOLBuild 4337.35; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727)",
    "Mozilla/5.0 (Windows; U; MSIE 9.0; Windows NT 9.0; en-US)",
    "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 2.0.50727; Media Center PC 6.0)",
    "Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; .NET CLR 1.0.3705; .NET CLR 1.1.4322)",
    "Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.2; .NET CLR 1.1.4322; .NET CLR 2.0.50727; InfoPath.2; .NET CLR 3.0.04506.30)",
    "Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN) AppleWebKit/523.15 (KHTML, like Gecko, Safari/419.3) Arora/0.3 (Change: 287 c9dfb30)",
    "Mozilla/5.0 (X11; U; Linux; en-US) AppleWebKit/527+ (KHTML, like Gecko, Safari/419.3) Arora/0.6",
    "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.2pre) Gecko/20070215 K-Ninja/2.1.1",
    "Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9) Gecko/20080705 Firefox/3.0 Kapiko/3.0",
    "Mozilla/5.0 (X11; Linux i686; U;) Gecko/20070322 Kazehakase/0.4.5",
    "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.0.8) Gecko Fedora/1.9.0.8-1.fc10 Kazehakase/0.5.6",
    "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11",
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_3) AppleWebKit/535.20 (KHTML, like Gecko) Chrome/19.0.1036.7 Safari/535.20",
    "Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; fr) Presto/2.9.168 Version/11.52"
    "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/65.0.3325.181 Safari/537.36"
]

# res = requests.post(home_url)
# set_cookie = res.headers['Set-Cookie']
# cookie_array = re.split('[;,]', set_cookie)
# for item in cookie_array:
#     print(item)

# requests.adapters.DEFAULT_RETRIES = 5 # add retries

# headers = {
#     'User-Agent': random.choice(UAs), # select random user-agent
#     'Connection': 'close'
# }

# postData = {
#     'term_no': '1028-2019-2',
#     'week': 10,
#     'json': True
# }

# fap_session = requests.Session()
# fap_session.keep_alive = False # close extra connections

# fap_session.post(home_url)
# res = fap_session.post('http://101.76.160.244:8080/User/GetStuClass', data=postData, headers = headers)

# print(res.json())

# fap_session = requests.Session()
# fap_session.keep_alive = False # close extra connections

# res = fap_session.post('http://101.76.160.244:8080/User/GetStuClass', headers = headers)

# print(res.text)

# soup = BeautifulSoup(res.text, features="lxml")

# form_attributes = soup.find_all('form', attrs={ 'action': True })
# input_attributes = soup.find_all('input', attrs={ 'name': True, 'value': True })
# for action in form_attributes:
#     target_action = action.attrs['action']
# for item in input_attributes:
#     target_name = item.attrs['name']
#     target_value = item.attrs['value']

# print(target_action, target_name, target_value)

s = requests.session() # Fetch SessionId
res = s.get(home_url)
c = requests.cookies.RequestsCookieJar()
s.cookies.update(c)

session_id = s.cookies.get_dict()['ASP.NET_SessionId']
cookie = 'ASP.NET_SessionId=' + session_id

headers = {
    'User-Agent': random.choice(UAs), # select random user-agent
    'Referer': home_url,
    'Connection': 'close',
    'Cookie': cookie
}

# print(cookie)

# postData = {
#     'term_no': '1028-2019-2',
#     'week': 10,
#     'json': True
# }

res = requests.post('http://jw3.ahu.cn/xskbcx.aspx?xh=E21714049&amp;xm=梅世祺', headers = headers)

soup = BeautifulSoup(res.text, features='lxml')

table = soup.find_all('table')[1]
trs = table.find_all('tr')
for index_r,tr in enumerate(trs):
    if index_r > 0:
        tds = tr.find_all('td')
        for index_c,td in enumerate(tds):
            if index_c > 0:
                course_info = td.get_text()
                if (course_info != ' ') and (re.search('^第', course_info) == None):
                    regObj = re.search('^(.+)(周.+)(第.+节)({第.+周})(.{2,3})', course_info)
                    print(regObj.group(1), regObj.group(2), regObj.group(3), regObj.group(4), regObj.group(5))
                    # print(course_info)
