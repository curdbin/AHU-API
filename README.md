# AHU-API

# Introduction
封装安大常用的接口

# Development
## Prerequisites
Please ensure that you have installed following stuffs before officially starting you development:
- pip >= 19.0.3
- python3 >= 3.6.5
- Node.js >= 11.12.0
- npm >= 6.9.0

## Fetch the source code
````bash
git clone git@github.com:curdbin/AHU-API.git
````
## Install dependencies
````bash
cd AHU-API
pip install -r requirements.txt
npm install
````
## Configure your password

````
cd src
mv auth.example.ini auth.ini
````
Rename `auth.example.ini` to `auth.ini`，and write your student number and password into this file.

# Architecture
````bash
.
|_ docs
|_ archives
|_ src
````

# References
- [zf.ahu.cn](http://zf.ahu.cn) 新版正方教务系统
- [portal.ahu.edu.cn](http://portal.ahu.edu.cn) 数字安大

# Contributors
[<img alt="nujhong" src="https://avatars3.githubusercontent.com/u/32427260?s=460&v=4&s=117" width="117">](https://github.com/lolimay)[<img alt="westeryCN" src="https://avatars1.githubusercontent.com/u/37997096?s=460&v=4&s=117" width="117">](https://github.com/westeryCN)