## 1、环境标识

| 标识                | 说明                |
|-------------------|-------------------|
| local             | 本地，不在版本库中，自行在本地配置 |
| dev               | 开发环境              |
| `qa1/qa2/qa3/qa4` | 集成测试环境            |
| sit               | 回归测试环境            |
| prod              | 生产环境              |

### 1.1、配置方式
**cli**
```bash
vim /etc/profile #添加
export WEB_ENV='dev'
```
**web**
```bash
vim nginx_path/nginx/fastcgi_params #添加fastcgi参数
fastcgi_param WEB_ENV 'dev';
```

## 2、公共类库
使用composer加载`xyf-php-lib`，命名空间为`yiqiang3344\yii2_lib`.
库中定义了常用的中台SDK，比如：消息中心；还有常用的工具类，比如：配置文件、数据库、web请求、加密、OSS、Redis、参数校验、状态码，以及数组、字符串、金额、图片和时间等。
具体参见：
[xyf-php-lib介绍](http://gitlab.xinyongfei.cn/php/xyf-php-lib)
[xyf-php-lib开发规范](http://gitlab.xinyongfei.cn/php/xyf-php-lib/blob/master/STANDARD.md)

## 3、配置文件
### 3.1、项目标识常量
修改`common/config/bootstrap.php`中的全局变量`PROJECT_NAME`对应项目标识，一般为项目名对应的值。

### 3.2、多级目录及优先级
公共目录：`common/config/`
应用目录：`应用文件夹/config/`
环境目录：`应用文件夹/config/环境标识/`

优先级：环境配置>应用配置>公共配置。
其中应用目录的环境配置优先级高于公共目录的环境配置。

## 4、自定义目录规定
### 4.1、`common/tables` 活动记录模型
公共的通过脚手架自动生成的数据表对应的类，不能做任何修改。

### 4.2、`common/models` 公共模型
公共的模型，可继承数据表对应的类，也可以实现各种设计模式，但请根据业务建立对应的目录，以便和其他业务区分。

### 4.3、`common/helper` 公共工具
与业务无关的通用工具，比如数组、字符串、校验、加密等。

### 4.4、`common/facade` 公共业务门面类
相对独立的业务的门面类，门面类的业务逻辑对外是透明的。

### 4.5、`common/exception` 公共异常类
公用的异常类。

### 4.6、`common/filters` 公共过滤器
公共过滤器，主要用在各应用的`BaseController::behaviors`中。

### 4.7、`common/ssh-key` 公共秘钥
公用的秘钥信息。

### 4.8、`common/components` 公共组件
无法通过composer引入的多文件工具类。

## 5、日志
### 5.1、根目录
日志统一写到`@customLog`中，在`common/config/bootstrap.php`中配置。
```php
Yii::setAlias('@customLog', '/data/logs/' . PROJECT_NAME);
```
也可以在各应用目录或环境目录的`bootstrap.php`中根据需要设置。

### 5.2 日志格式
目前日志是写入各服务器文件，通过logstash收集到阿里云日志服务。为了方便通过阿里云日志服务查询和分析，日志统一采用json格式。
可参考使用 `common\logging\JsonFileTarget` 将日志使用json格式记录。

### 5.3 日志文件大小
日志单个文件大小上限为1G，最多有5个同名的日志文件。配置方法：
```php
\common\logging\FileTarget::$maxFileSize = 1024 * 1024;
\common\logging\FileTarget::$maxLogFiles = 5;
```

### 5.4、日志匹配规则
`common/config/main.php`中的`components.log.targets`中配置，也可以在各级配置目录中按需求配置，下面是常用配置说明：

| 属性             | 实例                                                           | 说明                                                                      |
|----------------|--------------------------------------------------------------|-------------------------------------------------------------------------|
| class          | `common\logging\JsonFileTarget`                              | 日志文件类                                                                   |
| levels         | `['info']`                                                   | 包含的日志级别                                                                 |
| categories     | `['web_client']`                                             | 包含的日志类型                                                                 |
| except         | `['yii\base\UserException']`                                 | 不包含的日志类型                                                                |
| logVars        | `['_POST']`                                                  | 要显示的参数变量，为空表示不显示，可选值：`_GET, _POST, _FILES, _COOKIE, _SESSION 和 _SERVER` |
| exportInterval | `1`                                                          | 导出间隔，生产的web要配置为大于或等于1000，否则频繁写入日志会影响性能                                  |
| logFileParams  | `['base_path' => '@customLog/web_client','format' => 'Ymd']` | 日志文件路径配置，其中`base_path`表示日志目录，`format`日志文件日志命令格式                         |
| ignoreLog      | `true`                                                       | 是否忽略匹配的日志，可用来暂时忽略匹配规则                                                   |

### 5.5、公共字段
| 字段                   | 类型      | 必填     | 备注                                      |
|----------------------|---------|--------|:----------------------------------------|
| time                 | string  | 必填     | 日志记录时间，格式：yyyy-mm-dd hh:mm:ss           |
| message_tag          | string  | 选填     | 日志标签，用来区分业务及其子类，尽量用唯一的方便查询的标识           |
| message              | `string | array` | 必填                                      | 日志内容
| request_float_number | string  | 必填     | 请求流水号，同一个web请求的所有日志流水号都一致，常驻进程需要自行刷新流水号 |
| ip                   | string  | 必填     | 客户端ip                                   |
| level                | string  | 必填     | 日志级别：info 信息，warning 警告，error 错误        |
| category             | string  | 必填     | 类型                                      |
| host_name            | string  | 必填     | 服务器名称                                   |
| debug                | array   | 选填     | 追踪信息，写日志位置的前3层路径                        |

### 5.6、日志目录及写入方法
为了方便跨项目排查问题，各项目尽量使用统一的日志目录规范。
一下日志目录规范的日志匹配配置，都已在`common/config/bootstrap.php`中配置。

日志匹配规则配置好之后，只需要调用`\Yii::info('message', $category)`即可写入日志。
为了方便管理，`common\logging`中已经封装好对应的日志类，方便调用。

#### 5.6.1、web访问日志 `@customLog/access/年月日.log`
可通过`controller`继承`\common\controllers\BaseController`或`use yiqiang3344\yii2_lib\helper\log\TAccessLog`实现。

#### 5.6.2、脚本执行日志：`@customLog/console/年月日.log`
> 非常驻进程会记录请求信息和响应信息，常驻进程只请求信息，是否是常驻进程可参考下文中的常驻进程章节。

可通过`controller`继承`\console\controllers\BaseController`实现。

#### 5.6.3、调试日志：`@customLog/debug/年月日.log`
调试日志，统一使用`\common\logging\DebugLog`的`error()`、`info()`和`warning()`来写入日志。

#### 5.6.4、数据库异常日志：`@customLog/db/年月日.log`
使用yii自带数据库操作类，异常类型为`yii\db\*`，会命中对应日志匹配规则，并记录到此目录中。

#### 5.6.5、对外请求日志：`@customLog/web_client/年月日.log`
使用`yiqiang3344\yii2_lib\helper\webClient\WebClientV2`或`yiqiang3344\yii2_lib\helper\webClient\WebClient`发起的请求会自动记录日志。

#### 5.6.6、对外请求超时日志：`@customLog/web_client_timeout/年月日.log`
使用`yiqiang3344\yii2_lib\helper\webClient\WebClientV2`或`yiqiang3344\yii2_lib\helper\webClient\WebClient`发起的请求会自动记录日志。

#### 5.6.7、系统错误日志：`@customLog/error/年月日.log`
代码异常的错误日志，一般需要排除掉业务异常。

#### 5.6.8、系统异常日志及未被捕获的日志：`@customLog/app/年月日.log`
这是兜底的，保证其他规则都没匹配到的日志会落到这里来。确定没用的日志，可通过`except`过滤。

### 5.7、异常通知
在日志匹配规则基础上，通过把`class`配置为`common\logging\EmailTarget`，可以通过消息中心，把错误信息发送到钉钉群及指定人员的邮箱中。
**匹配规则示例：**
```php
[
    'class' => 'common\logging\EmailTarget',
    'levels' => ['error'], //包含的错误级别
    'except' => [ //忽略的日志类型
        'yii\web\HttpException:404',
        'yii\web\HttpException:400',
        'yii\web\HttpException:403',
        'yiqiang3344\yii2_lib\helper\exception\ParamsInvalidException',
        'yiqiang3344\yii2_lib\helper\exception\OptionsException',
        'common\exception\CUserException',
        'yiqiang3344\yii2_lib\helper\exception\UserException',
        'yii\base\UserException',
    ],
    'message' => [
        'from' => ['sms-api'], //随便配置一个，不能为空
        'subject' => '[' . $_SERVER['WEB_ENV'] . '][' . gethostname() . ']['. PROJECT_NAME .']', //公共主题
    ],
],
```

## 6、环境变量
通过`\common\helper\Env::$globalAttributes`来定义可以全局配置的变量。
通过`\common\helper\Env::setAttr()`方法来定义。
通过定义对应的get方法来使用，不能直接从`\common\helper\Env::$globalAttributes`读取，因为全局变量很可能是需要二次处理的。

## 7、异常处理及响应
为了方便统一管理响应信息的http状态码以及影响参数，统一使用错误异常捕获类`\common\error\ErrorHandler`来处理异常。
响应状态码统一使用`\common\helper\CodeMessage`来处理响应值。
具体参考：[接口响应规范](https://www.tapd.cn/60211538/markdown_wikis/show/#1160211538001007010)

多http状态码响应可通过配置关闭：
common/config/params.php
```bash
    'switch' => [
        'response' => [
            'httpStatus' => false,
        ],
    ],
```


## 8、入参及验签
[v2版本](https://www.tapd.cn/20090981/markdown_wikis/show/#1120090981001003599)
参考`\api\controllers\TestController`

[v3版本](https://www.tapd.cn/20090981/markdown_wikis/show/#1120090981001007769)


[v3.1版本](https://www.tapd.cn/20090981/markdown_wikis/show/#1120090981001008661)
参考`\api\controllers\TestV3Controller`

## 9、事件分发
基于yii2的事件组件：https://www.yiichina.com/doc/guide/2.0/concept-events，
封装了`\yiqiang3344\yii2_lib\helper\event\Event`事件门面类。

事件类：需要继承`yii\base\Event`

监听者类：需要实现`\yiqiang3344\yii2_lib\helper\event\ListenerInterface`接口

在项目配置的components下配置事件及监听者，示例：
```php
'event' => [
    'class' => \yiqiang3344\yii2_lib\helper\event\Event::class,
    'listen' => [
        \yiqiang3344\yii2_lib\helper\event\events\SlowSqlEvent::class => [
            \common\event\listeners\SlowSqlAlter::class,
        ],
    ],
],
```

触发事件：
```php
\yiqiang3344\yii2_lib\helper\event\Event::event(new \yiqiang3344\yii2_lib\helper\event\events\SlowSqlEvent([
    'sql' => $rawSql,
    'cost' => $cost,
    'slowSqlTime' => 2,
]));
```

## 10、常驻进程
为了方便记录脚本执行日志，把控制器分为常驻进程和非常驻进程；
比如：
- 订单非常驻进程：`console/controllers/OrderController.php`
- 订单常驻进程：`console/controllers/LpOrderController.php`

> 注意：
> 使用常驻进程的controller和不使用常驻进程的controller要分开，这样才能正确的记录脚本执行日志。
> 因为非常驻进程如果有异常，执行记录会包含异常结果，
> 但常驻进程运行时间太长，会先记录执行记录，如果有异常再额外记录一条异常日志，两条记录`request_float_number`是一致的。

对应`controller`中`use \console\models\LongProcessTrait`，
然后可使用`longProcess(callable $callback)`实现常驻进程，使用`kill -15 pid` 来安全终止进程。
即使不用`longProcess()`方法来实现常驻进程，为了保证日志正常记录，对应`controller`也需要`use \console\models\LongProcessTrait`。


## 11、参数校验
推荐使用`\common\helper\Validator::checkParams(&$params, $needParams)`方法来校验输入参数。
支持的参数类型参见`\common\helper\Validator::$builtInValidators`列表，且可自行扩展。
比如：
```php
$params = $this->request->getBodyParams();
Validator::checkParams($params, [
    'test' => ['name' => '测试', 'type' => 'string']
]);
```

## 12、配置文件
推荐使用`\common\facade\Config`来管理。
本地文件配置可直接使用`\common\helper\config\Config`来获取`\Yii::$app->params`的参数，支持用点来分隔数组。
比如，下面两个结果一样。
```php
var_dump(common\helper\config\Config::getString('secret.self_sign_key', ''));
var_dump(\Yii::$app->params['secret']['self_sign_key'] ?? '');
```

## 13、多数据库及事务
**\common\helper\db\DB 数据库管理类**
yii2默认数据库连接为`common/config/环境/db.php`中的db，可通过`Yii::$app->db`来使用，
但其他数据库配置，比如`db2`，虽然也可以通过`Yii::$app->db2`使用，但没有注释定义，所有没有语法提示。
为了增强使用体验，建议在`\common\helper\db\DB`中给每个数据库创建一个方法来连接。
比如：`\common\helper\db\DB::default()`为默认数据库连接，`\common\helper\db\DB::db2()`为`db2`的数据库连接。

**事务**
可使用yii2自带的事务方式：
```php
    $t = DB::default()->beginTransaction();
    $t->rollBack();
    $t->commit();
```
也可以使用兼容信用飞老框架的方法：
```php
\common\helper\db\DB::transaction($callback, Connection $connection = null);
```

## 14、短信
### 14.1、通用方法
`\common\facade\Sms::send($mobile, $templateId, $data, $app, $innerApp, $channel = '', $notifyUrl = '')`

### 14.2、短信验证码
发送`\common\facade\Captcha::sendSmsCode($mobile, $bizType)`
校验`\common\facade\Captcha::validate($mobile, $bizType, $captcha)`

## 16、后台应用
[参考文档](http://gitlab.xinyongfei.cn/php/yii2-init/tree/master/backend)

## 17、脚手架
[使用文档](https://www.tapd.cn/60211538/markdown_wikis/show/#1160211538001007137)

## 18、慢sql日志及预警
db配置中class替换为`\yiqiang3344\yii2_lib\helper\db\Connection::class`，增加`slowSqlTime`表示超过多少秒算慢sql（也可以不配置，自行在监听者中判断），示例：
```php
'db' => [
    'class' => \yiqiang3344\yii2_lib\helper\db\Connection::class,
    'slowSqlTime' => 2,
    'dsn' => 'mysql:host=mysql;dbname=dbname',
    'username' => 'user',
    'password' => 'password',
    'charset' => 'utf8',
    'tablePrefix' => '',
],
```

在项目配置的components下配置事件及监听者，示例：
```php
'event' => [
    'class' => \yiqiang3344\yii2_lib\helper\event\Event::class,
    'listen' => [
        \yiqiang3344\yii2_lib\helper\event\events\SlowSqlEvent::class => [
            \common\event\listeners\SlowSqlAlter::class,
        ],
    ],
],
```

日志配置中db目录捕获慢sql日志，app目录中忽略慢sql日志：
```php
[
    'class' => 'common\logging\JsonFileTarget',
    'levels' => ['error', 'warning'],
    'logVars' => [],
    'except' => [
        'yii\httpclient\*',
        'yiqiang3344\yii2_lib\helper\exception\ParamsInvalidException',
        'yiqiang3344\yii2_lib\helper\exception\OptionsException',
        'common\exception\CUserException',
        'yiqiang3344\yii2_lib\helper\exception\UserException',
        'yii\base\UserException',
        'yii\base\InvalidRouteException',
        'slowSql', //增加slowSql
    ],
    'exportInterval' => $_exportInterval,
    'logFileParams' => [
        'base_path' => '@customLog/app',
        'format' => 'Ymd',
    ],
],
[
    'class' => 'common\logging\JsonFileTarget',
    'levels' => ['error', 'warning'],
    'categories' => ['yii\db\*', 'slowSql'], //增加slowSql
    'logVars' => [],
    'exportInterval' => $_exportInterval,
    'logFileParams' => [
        'base_path' => '@customLog/db',
        'format' => 'Ymd',
    ],
],
```

`\common\event\listeners\SlowSqlAlter::handle()`中实现了日志记录及预警逻辑。


