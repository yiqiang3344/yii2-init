#!/bin/bash
starttime=$(date +'%Y-%m-%d %H:%M:%S')
echo "$starttime begin..."
runtime() {
  endtime=$(date +'%Y-%m-%d %H:%M:%S')
  start_seconds=$(dateToSeconds "$starttime")
  end_seconds=$(dateToSeconds "$endtime")
  seconds=$((end_seconds - start_seconds))
  hour=$(($seconds / 3600))
  min=$((($seconds - ${hour} * 3600) / 60))
  sec=$(($seconds - ${hour} * 3600 - ${min} * 60))
  HMS=$(echo ${hour}:${min}:${sec})
  echo "$endtime 本次运行时间：${hour}小时${min}分${sec}秒"
}
error() {
  rm -rf build.sh
  runtime
  exit 0
}
dateToSeconds() {
  if [[ $(uname -a | cut -d ' ' -f 1) == "Linux" ]]; then
    echo $(date --date="$1" +%s)
  else
    echo $(date -j -f "%Y-%m-%d %H:%M:%S" "$1" "+%s")
  fi
}
end() {
  rm -rf build.sh
  runtime
  exit 1
}
help() {
  cat <<-EOF
    Desc: 在当前目录下创建一个自定义yii2框架的基础项目，根据项目类型选择基础的应用。
    Usage: ... | bash /dev/stdin 项目类型 项目标识 项目名称
    项目类型:
        backend: 包含后台和脚本应用
        api: 包含接口和脚本应用
        console: 只包含脚本应用
        all: 包含所有应用
    项目标识: 项目文件夹名，也会设置为框架中的 PROJECT_NAME 常量。
    项目中文名: 会设置为框架中的 PROJECT_NAME_ZH 常量。
EOF
  error
}
composerInstall() {
  if command -v composer >/dev/null 2>&1; then
    echo "安装依赖..."
    echo -e "\033[32m 此过程会比较长，如有特殊需求，可终止安装，自行在$(pwd)下执行: composer install \033[0m"
    composer install --profile --prefer-dist
    if [ $? -eq 0 ]; then
      echo -e "\033[32m 依赖构建成功 \033[0m"
    else
      echo -e "\033[31m 依赖构建失败 \033[0m"
      error
    fi
  else
    echo -e "\033[32m 项目下载完毕，但composer未安装，无法自动构建，请安装composer，并在$(pwd)下执行: composer install。 \033[0m"
    error
  fi

  if [[ $1 == "backend" ]]; then
    echo '初始化后台数据...'
    if command -v php >/dev/null 2>&1; then
      php yii init/backend $2 $3
      if [ $? -eq 0 ]; then
        echo -e "\033[32m 后台数据初始化完毕 \033[0m"
      else
        echo -e "\033[31m 后台数据初始化失败 \033[0m"
        error
      fi
    else
      echo -e "\033[31m 项目下载完毕，但php未安装，无法初始化后台数据，请安装php，并在$(pwd)下执行: php yii init/backend $2 $3 \033[0m"
      error
    fi
  fi

  echo ''
  echo -e "\033[32m 构建完毕，地址: $(pwd) \033[0m"
}
replace() {
  if [[ $(uname -a | cut -d ' ' -f 1) == "Linux" ]]; then
    sed -i "s/$1/$2/g" $3
  else
    sed -i "" "s/$1/$2/g" $3
  fi
}

if [ $# -lt 1 ]; then
  help
fi

type=$1        #类型
project=$2     #项目标识
projectName=$3 #项目中文名

in_array() {
  local array="$1[@]"
  shift
  local needle=$1
  shift
  local result=1
  for element in "${!array}"; do
    if [[ $element == $needle ]]; then
      result=0
      break
    fi
  done
  return $result
}

types=(backend api console all)
if ! in_array types $type; then
  cat <<-EOF
    项目类型错误:
        backend: 包含后台和脚本应用
        api: 包含接口和脚本应用
        console: 只包含脚本应用
        all: 包含所有应用
EOF
  error
fi
if [[ $project == "" ]]; then
  echo -e "\033[31m 项目标识不能为空 \033[0m"
  error
fi
if [[ $projectName == "" ]]; then
  echo -e "\033[31m 项目名称不能为空 \033[0m"
  error
fi
if [[ $type == "backend" ]]; then
  if [[ $adminName == "" ]]; then
    read -p "请输入超级管理员姓名:" adminName
  fi
  if [[ $adminMobile == "" ]]; then
    read -p "请输入超级管理员手机号:" adminMobile
  fi
fi

# 判断目录是否已存在
if [[ -d $(pwd)/$project ]]; then
  read -p "项目已存在，是否覆盖[y/n]:" recover
  if [[ $recover == "n" ]]; then
    error
  fi
  rm -rf $(pwd)/$project
fi

# 拉代码
git clone git@gitlab.xinyongfei.cn:php/yii2-init.git $project
cd $project
git checkout master
rm -rf .git

# 设置项目标识和项目名称
replace 'yii2-init' $project common/config/bootstrap.php
replace 'yii2基础框架' $projectName common/config/bootstrap.php

# 添加local配置
cp -rf backend/config/prod backend/config/local
cp -rf console/config/prod console/config/local
cp -rf api/config/prod api/config/local
cp -rf common/config/prod common/config/local

# 配置日志目录
read -p "请输入存储日志的目录[直接回车跳过]:" logPath
if [[ $logPath != "" ]]; then
  mkdir -p $logPath/$project
  echo "" >>common/config/local/bootstrap.php
  echo "Yii::setAlias('@customLog', '$logPath/' . PROJECT_NAME);" >>common/config/local/bootstrap.php
fi

db='db'
backend=''
if [[ $type == "backend" ]]; then
  echo '初始化后台和脚本应用...'
  rm -rf api shell
  backend='backend'
elif [[ $type == "api" ]]; then
  echo '初始化接口和脚本应用...'
  rm -rf backend shell
elif [[ $type == "console" ]]; then
  echo '初始化脚本应用...'
  rm -rf backend api shell
else
  echo '初始化所有应用...'
  backend='backend'
fi

# 配置数据库
if [[ $db != "" ]]; then
  read -p "请输入db数据库主机地址[直接回车跳过]:" dbHost
  if [[ $dbHost != "" ]]; then
    replace 'dbHost' $dbHost common/config/local/db.php
    read -p "请输入db数据库库名[直接回车跳过]:" dbDatabase
    if [[ $dbDatabase != "" ]]; then
      replace 'dbDatabase' $dbDatabase common/config/local/db.php
      read -p "请输入db数据库用户名[直接回车跳过]:" dbUser
      if [[ $dbUser != "" ]]; then
        replace 'dbUser' $dbUser common/config/local/db.php
        read -p "请输入db数据库密码[直接回车跳过]:" dbPwd
        if [[ $dbPwd != "" ]]; then
          replace 'dbPwd' $dbPwd common/config/local/db.php
        fi
      fi
    fi
  fi
fi
if [[ $backend != "" ]]; then
  read -p "请输入backend数据库主机地址[直接回车跳过]:" dbBackendHost
  if [[ $dbBackendHost != "" ]]; then
    replace 'dbBackendHost' $dbBackendHost common/config/local/db.php
    read -p "请输入backend数据库库名[直接回车跳过]:" dbBackendDatabase
    if [[ $dbBackendDatabase != "" ]]; then
      replace 'dbBackendDatabase' $dbBackendDatabase common/config/local/db.php
      read -p "请输入backend数据库用户名[直接回车跳过]:" dbBackendUser
      if [[ $dbBackendUser != "" ]]; then
        replace 'dbBackendUser' $dbBackendUser common/config/local/db.php
        read -p "请输入backend数据库密码[直接回车跳过]:" dbBackendPwd
        if [[ $dbBackendPwd != "" ]]; then
          replace 'dbBackendPwd' $dbBackendPwd common/config/local/db.php
        fi
      fi
    fi
  fi
fi

# 配置redis
read -p "请输入redis主机地址[直接回车跳过]:" redisHost
if [[ $redisHost != "" ]]; then
  replace 'redisHost' $redisHost common/config/local/redis.php
  read -p "请输入redis库名[直接回车跳过,默认为0]:" redisDatabase
  if [[ $redisDatabase != "" ]]; then
    replace 'redisDatabase' $redisDatabase common/config/local/redis.php
  else
    replace 'redisDatabase' '0' common/config/local/redis.php
  fi
  read -p "请输入redis端口[直接回车跳过,默认6379]:" redisPort
  if [[ $redisPort != "" ]]; then
    replace 'redisPort' $redisPort common/config/local/redis.php
  else
    replace 'redisPort' '6379' common/config/local/redis.php
  fi
  read -p "请输入redis密码[直接回车跳过,默认为空]:" redisPwd
  if [[ $redisPwd != "" ]]; then
    replace 'redisPwd' $redisPwd common/config/local/redis.php
  else
    replace 'redisPwd' '' common/config/local/redis.php
  fi
fi

composerInstall $type $adminName $adminMobile

end
