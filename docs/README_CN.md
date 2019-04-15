<p align="center">
  <img src="./docs/images/logo.png">
</p>

# 介绍
安徽大学通用API库. Made with ❤ by [@lolimay](https://github.com/lolimay), [@WesteryCN](https://github.com/WesteryCN) and [@JOHNYXUU](https://github.com/JOHNYXUU).

# 开发
## 前提准备
Please **ensure** that you have installed following stuff before officially starting your development:
- pip >= 19.0.3
- python3 >= 3.6.5
- Node.js >= 11.12.0
- npm >= 6.9.0
- VS Code >= 1.33.1 (Recommended)
- VS Code Extension: Python (Recommended)

## 获取源码
````bash
git clone git@github.com:curdbin/AHU-API.git
````
## 安装依赖
````bash
cd AHU-API
pip install -r requirements.txt
npm install
````
## 配置密码
````bash
cd src
mv auth.example.ini auth.ini # Only for linux, use RENAME in windows instead.
````
Rename `auth.example.ini` to `auth.ini`，and write your student number and password into this file.

## 运行
### Linux
````
python3 main.py
````
### Windows
````
python.exe main.py
````

# 项目架构
````bash
.
|_ docs
|_ archives
|_ src
````

# 引用
1. [http://zf.ahu.cn](http://zf.ahu.cn)
2. [http://portal.ahu.edu.cn](http://portal.ahu.edu.cn)

# 开源协议
The project is open sourced under the [MIT](./LICENSE) License.

# 贡献者
[<img alt="nujhong" src="https://avatars3.githubusercontent.com/u/32427260?s=460&v=4&s=117" width="117">](https://github.com/lolimay)[<img alt="westeryCN" src="https://avatars1.githubusercontent.com/u/37997096?s=460&v=4&s=117" width="117">](https://github.com/westeryCN)[<img alt="westeryCN" src="https://avatars1.githubusercontent.com/u/49187119?s=460&s=117" width="117">](https://github.com/JOHNYXUU)