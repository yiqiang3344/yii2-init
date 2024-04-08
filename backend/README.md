[toc]

## 1、权限控制逻辑
### 1.1、逻辑介绍
**权限：**
权限分为菜单权限和接口权限。

**菜单：**
菜单分为目录、页面和按钮。
目录没有真实页面，用于归类某些页面。
页面的路由（即链接）手动填写，按钮的路由只能下拉选择，列表从系统字典中获取。
菜单可以配置图标，图标只能下拉选择，列表从系统字典中获取。
部分菜单是公共菜单，配置中`是否公共`选择`是`后，不能在角色中配置权限，所有角色都有权限。

**接口：**
接口分为模块和方法。
模块是指控制器，如果控制器有目录分类，那模块标识为`控制器目录/控制器名`，比如：`system/user`。
接口的控制器和方法需要按规范书写注释，系统会通过反射原理把注释转化为接口信息，并存到接口权限表。

接口按权限分为`公共接口`和`非公共接口`2类。
接口按需不需要登录分为`要登录`和`不用登录`2类。
`不用登录`的控制器继承`\backend\controllers\PublicBaseController`基础控制器，且应该注释中标识`公共接口`。
`要登录`的继承`\backend\controllers\AuthBaseController`基础控制器。

**角色：**
角色可以配置多个菜单权限和接口权限。

### 1.2、接口注释规范
**公共接口**
控制器和方法都可以使用，控制器使用后，所属方法全部继承此属性。
```php
/**
 * @public
 */
```

**名称**
模块或方法名称，注释第一行，`*`后要带空格，比如：
```php
/**
 * 获取角色列表
 */
```

**发送接口调用通知**
要求控制器继承`\backend\controllers\AuthBaseController`。
一般是给重要的增删改查接口配置。
只在方法中配置有效。
会通过短信中心发送钉钉消息到标识为`common_config_notify`的钉钉群。
```php
/**
 * @sendNotify
 */
```

## 2、基础功能
### 2.1、系统管理[目录]
#### 2.1.1、系统字典[页面]
搜索：标识（模糊匹配）、名称（模糊匹配）、父级字典（下拉菜单，精确匹配）。
字段：标识、名称、父级字典、状态、级别、备注、操作时间、操作人。
默认查询最近10条，时间倒序排序。


##### 2.1.1.1、添加字典[隐藏页面]
字段：级别（只显示用，默认父级：父级，子级）、父级字典（级别为子级才显示，下拉菜单，布局系统字典信息接口获取）、标识、名称、备注、状态（1 正常，0 禁用）。

##### 2.1.1.2、编辑字典[隐藏页面]
字段：级别（只显示用，默认父级：父级，子级）、父级字典（级别为子级才显示，下拉菜单，布局系统字典信息接口获取）、标识、名称、备注。

##### 2.1.1.3、修改状态[按钮]


#### 2.1.2、登录管理[目录]
##### 2.1.2.1、登录过期时间[页面]
登录过期时间分为两部分：
**无任何操作时：**默认2小时过期。
**有操作时：**默认8小时过期。

配置过期时间，配置值分两部分：数字、单位。
单位包含：小时、天、月。
数字向上取整，必须大于0。

##### 2.1.2.2、密码有效期[页面]
> 账户密码过期之后，用户除了登录、获取个人信息以及修改密码操作之外，其他任何操作都会提示密码过期，然后跳转到`个人信息管理`页面。

默认永久，可选项：1个月，3个月，6个月，1年，永久。

##### 2.1.2.3、初始密码[页面]
默认初始密码`12345678`。

##### 2.1.2.4、登录方式[页面]
默认密码登录。可多选，至少选择一个：
- 1 密码登录
- 2 短信验证码登录
- 3 钉钉扫码登录

#### 2.1.3、接口管理[页面]
搜索：类型（默认全部，下拉菜单：-1 全部，1 模块，2 方法）、名称（模糊匹配）、标识(模糊匹配)、是否公开（默认全部：-1 全部，1 是，0 否）、是否删除（默认全部：-1 全部，1 是，0 否）。
字段：id、类型、名称、标识、是否公开、是否删除、更新时间、操作人。
默认最近10条，按更新时间倒序排序。

##### 2.1.3.1、刷新接口[按钮]

#### 2.1.4、菜单管理[页面]
搜索：菜单名称（模糊匹配）、是否父菜单（默认全部：-1 全部，1 是，0 否）、是否公共（默认全部：-1 全部，1 是，0 否）、是否隐藏（默认全部：-1 全部，1 是，0 否）、状态（默认全部：-1 全部，1 正常，0 禁用）。
字段：id、类型、菜单名称、是否父菜单、状态、父菜单、排序、是否隐藏、是否公共、路由。
默认查询最近10条，时间倒序排序。

##### 2.1.4.1、添加菜单[隐藏页面]
字段：菜单名称、是否隐藏（1 是，0 否）、是否公共（1 是，0 否）、父级菜单、排序、类型（1 目录，2 页面，3 按钮）、路由（如果是目录则可为空，其他不能为空，如果是按钮，则是下拉菜单，布局系统字典信息接口获取）、图标（下拉菜单，布局系统字典信息接口获取）。

##### 2.1.4.2、编辑菜单[隐藏页面]
字段：菜单名称、是否隐藏（1 是，0 否）、是否公共（1 是，0 否）、父级菜单、排序、类型（1 目录，2 页面，3 按钮）、路由（如果是目录则可为空，其他不能为空，如果是按钮，则是下拉菜单，布局系统字典信息接口获取）、图标（下拉菜单，布局系统字典信息接口获取）。

##### 2.1.4.3、修改状态[按钮]

#### 2.1.5、账户管理[目录]
##### 2.1.5.1、账户过期时间[页面]
> 账户过期后，下次操作时，状态会自动变为禁用，如果需要再使用，需要找管理员解禁。

**无任何操作时：**默认2周过期。
配置值分两部分：数字、单位。
单位包含：周、月。
数字向上取整，必须大于0。

##### 2.1.5.2、账户列表[页面]
搜索：姓名（精确匹配，传空表示全部）、角色（精确匹配，传0表示全部）、状态（默认全部：-1 全部，1 正常，0 禁用）。
字段：id、姓名、角色、状态、创建时间、操作人。
默认查询最近10条，时间倒序排序。

###### 2.1.5.2.1、添加账户[隐藏页面]
姓名、手机号、角色（接口获取）、状态（1 正常，0 禁用）。

###### 2.1.5.2.2、编辑账户[隐藏页面]
姓名、手机号、角色（接口获取）。

###### 2.1.5.2.3、修改状态[按钮]

###### 2.1.5.2.4、初始化密码[按钮]

#### 2.1.6、角色管理[页面]
搜索：角色名称（模糊匹配）、状态（默认全部：-1 全部，1 正常，0 禁用）。
字段：id、名称、描述、状态、创建时间、操作人姓名。
默认查询最近10条，时间倒序排序。

##### 2.1.6.1、添加角色[隐藏页面]
字段：名称、描述（可为空）、菜单权限（接口获取）、接口权限（接口获取）、状态（1 正常，0 禁用）。

##### 2.1.6.2、编辑角色[隐藏页面]
字段：名称、描述（可为空）、菜单权限（接口获取）、接口权限（接口获取）。

##### 2.1.6.3、修改状态[按钮]

#### 2.1.7、操作记录[页面]
搜索：起始时间（精确到秒，默认空，表示全部）、截止时间（精确到秒，默认空，表示全部）、ip（精确匹配，默认为空，表示全部）、操作人姓名（精确匹配，默认为空，表示全部）、操作人ID（精确匹配，默认为空，表示全部）、接口名称（精确匹配）、接口标识（精确匹配）。
字段：时间、姓名、操作人ID、菜单、接口名称、接口标识、ip、请求参数、响应信息
默认查询最近10条，时间倒序排序。

### 2.2、个人信息管理[页面、公共菜单]
**查看个人信息**
字段：姓名、手机号、角色。

**修改密码**
字段：原密码、新密码。

## 3、表设计
### 3.1、系统字典表 backend_dictionary
| 字段           | 类型        | 备注            |
|--------------|-----------|---------------|
| id           | int       | ID            |
| sign         | varchar   | 标识            |
| name         | varchar   | 名称            |
| comment      | varchar   | 描述            |
| parent_id    | int       | 父ID,0表示没有父节点  |
| is_deleted   | tinyint   | 是否被删除：1 是，0 否 |
| operator_id  | int       | 操作人ID         |
| created_time | timestamp | 创建时间          |
| updated_time | timestamp | 更新时间          |

### 3.2、系统配置表 backend_system_config
> 登录过期时间、密码管理、登录方式、账户过期时间

| 字段           | 类型        | 备注    |
|--------------|-----------|-------|
| id           | int       | ID    |
| name         | varchar   | 名称    |
| value        | varchar   | 值     |
| operator_id  | int       | 操作人ID |
| created_time | timestamp | 创建时间  |
| updated_time | timestamp | 更新时间  |

### 3.3、接口权限表 backend_interface_node
| 字段           | 类型        | 备注            |
|--------------|-----------|---------------|
| id           | int       | ID            |
| name         | varchar   | 名称            |
| sign         | varchar   | 标识            |
| type         | tinyint   | 类型：1 模块，2 方法  |
| parent_id    | int       | 父ID,0表示没有父节点  |
| is_public    | tinyint   | 是否公开：1 是，0 否  |
| is_deleted   | tinyint   | 是否被删除：1 是，0 否 |
| operator_id  | int       | 操作人ID         |
| created_time | timestamp | 创建时间          |
| updated_time | timestamp | 更新时间          |

### 3.4、菜单权限表 backend_menu_node
| 字段           | 类型        | 备注                |
|--------------|-----------|-------------------|
| id           | int       | ID                |
| name         | varchar   | 名称                |
| type         | tinyint   | 类型：1 目录，2 页面，3 按钮 |
| parent_id    | int       | 父ID,0表示没有父节点      |
| route        | varchar   | 路由                |
| icon         | varchar   | 图标                |
| is_public    | tinyint   | 是否公开：1 是，0 否      |
| order        | int       | 排序                |
| is_hide      | tinyint   | 是否隐藏：1 是，0 否      |
| is_deleted   | tinyint   | 是否删除：1 是，0 否      |
| operator_id  | int       | 操作人ID             |
| created_time | timestamp | 创建时间              |
| updated_time | timestamp | 更新时间              |

### 3.5、账户表 backend_user
| 字段              | 类型        | 备注           |
|-----------------|-----------|--------------|
| id              | int       | ID           |
| name            | varchar   | 名称           |
| mobile          | varchar   | 手机号          |
| password        | varchar   | 密码           |
| password_expire | int       | 密码过期时间，0不过期  |
| is_deleted      | tinyint   | 是否禁用：1 是，0 否 |
| operator_id     | int       | 操作人ID        |
| created_time    | timestamp | 创建时间         |
| updated_time    | timestamp | 更新时间         |

### 3.6、角色表 backend_role
| 字段           | 类型        | 备注           |
|--------------|-----------|--------------|
| id           | int       | ID           |
| name         | varchar   | 名称           |
| comment      | varchar   | 描述           |
| is_deleted   | tinyint   | 是否禁用：1 是，0 否 |
| operator_id  | int       | 操作人ID        |
| created_time | timestamp | 创建时间         |
| updated_time | timestamp | 更新时间         |

### 3.7、角色权限表 backend_role_node
| 字段           | 类型        | 备注           |
|--------------|-----------|--------------|
| id           | int       | ID           |
| role_id      | int       | 角色ID         |
| type         | tinyint   | 类型：1 菜单，2 接口 |
| node_id      | int       | 权限ID         |
| is_deleted   | tinyint   | 是否禁用：1 是，0 否 |
| operator_id  | int       | 操作人ID        |
| created_time | timestamp | 创建时间         |
| updated_time | timestamp | 更新时间         |

### 3.8、账户角色关系表 backend_user_role
| 字段           | 类型        | 备注           |
|--------------|-----------|--------------|
| id           | int       | ID           |
| user_id      | int       | 用户ID         |
| role_id      | int       | 角色ID         |
| is_deleted   | tinyint   | 是否禁用：1 是，0 否 |
| operator_id  | int       | 操作人ID        |
| created_time | timestamp | 创建时间         |
| updated_time | timestamp | 更新时间         |

### 3.9、操作记录表 backend_operation_record
| 字段             | 类型        | 备注    |
|----------------|-----------|-------|
| id             | int       | ID    |
| menu           | varchar   | 菜单    |
| interface_name | varchar   | 接口名称  |
| interface_sign | varchar   | 接口标识  |
| request        | varchar   | 请求参数  |
| response       | varchar   | 响应信息  |
| ip             | varchar   | IP    |
| operator_id    | int       | 操作人ID |
| operator_name  | varchar   | 操作人姓名 |
| created_time   | timestamp | 创建时间  |
| updated_time   | timestamp | 更新时间  |

## 4、接口文档
### 请求方式
只支持`OPTION`和`POST`.

### content-type
- form-data
- x-www-form-urlencoded
- json

### header规范
| 字段       | 必填 | 备注           |
|----------|----|--------------|
| token    | 否  | 用户登录凭证       |
| trace-id | 否  | 追踪ID         |
| menu-id  | 否  | 请求此接口对应的菜单ID |

### 响应规范
| 字段       | 类型     | 备注                 |
|----------|--------|--------------------|
| status   | int    | 业务状态码              |
| message  | string | 响应说明               |
| response | object | 响应内容，json对象，可能是空对象 |
| time     | int    | 时间戳，单位秒            |

### 业务状态码汇总
| 状态码    | 响应说明        | 特殊处理           |
|--------|-------------|----------------|
| 1      | success     | 无              |
| -1     | failed      | 提示             |
| -2     | header异常    | 提示             |
| -3     | 参数异常        | 提示             |
| -30    | 参数存储异常      | 提示             |
| 200000 | 请重新登录       | 跳转至登录页面        |
| 200010 | 无权操作        | 提示             |
| 200020 | 密码已过期，请修改密码 | 提示、跳转至个人信息管理页面 |
| 200030 | 账号已禁用       | 提示             |

### 4.1、布局[不用登录]
#### 4.1.1、获取系统字典信息
**url**

layout/layout/get-dict-by-sign

**入参**

| 字段   | 类型     | 必填 | 备注                         |
|------|--------|----|----------------------------|
| sign | string | 是  | 字典标识，比如：button 按钮, icon 图标 |

**响应内容字段说明**

| 字段             | 类型     | 备注            |
|----------------|--------|---------------|
| id             | string | ID            |
| name           | string | 名称            |
| parent_id      | string | 父ID           |
| is_deleted     | string | 是否删除: 1 是，0 否 |
| sign           | string | 标识            |
| comment        | string | 说明            |
| created_time   | string | 创建时间          |
| operation_name | string | 操作人姓名         |
| child          | array  | 子节点列表         |

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {
        "data": [
            {
                "id": "14",
                "sign": "test1",
                "name": "测试1",
                "comment": "测试1",
                "parent_id": "13",
                "child": [
                    {
                        "id": "15",
                        "sign": "test1-1",
                        "name": "测试1-1",
                        "comment": "测试1-1",
                        "parent_id": "14"
                    }
                ]
            }
        ]
    },
    "time": 1624963622
}
```

### 4.2、登录[不用登录]
#### 4.1.1、获取登录方式列表
**url**

layout/login/get-login-methods

**入参**

| 字段 | 类型 | 必填 | 备注 |
|----|----|----|----|

**响应内容字段说明**

| 字段   | 类型    | 备注                                 |
|------|-------|------------------------------------|
| list | array | 登录方式标识列表：1 密码登录，2 短信验证码登录，3 钉钉扫码登录 |

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {
        "list": [
            1
        ]
    },
    "time": 1625204942
}
```

#### 4.2.2、密码登录
**url**

layout/login/by-password

**入参**

| 字段       | 类型     | 必填 | 备注  |
|----------|--------|----|-----|
| mobile   | string | 是  | 手机号 |
| password | string | 是  | 密码  |

**响应内容字段说明**

| 字段             | 类型     | 备注                  |
|----------------|--------|---------------------|
| token          | string | token               |
| info           | array  | 用户信息                |
| info.id        | int    | 用户ID                |
| info.name      | string | 用户姓名                |
| info.mobile    | string | 用户手机号               |
| menu           | array  | 菜单列表                |
| menu.id        | string | 菜单ID                |
| menu.type      | string | 菜单类型：1 目录，2 页面，3 按钮 |
| menu.name      | string | 菜单名称                |
| menu.parent_id | string | 菜单父ID               |
| menu.is_hide   | string | 菜单是否隐藏：1 是，0 否      |
| menu.is_public | string | 菜单是否公开：1 是，0 否      |
| menu.child     | array  | 菜单的子菜单列表            |

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {
        "token": "eyJ1c2VyX2lkIjoxLCJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJKV1QiLCJzdWIiOiJzdWI6and0IiwiYXVkIjoieWlpMi1pbml0IiwidXNlcl9pZCI6MX0.U0gHuTPtSA4JDCSI-mI_6hYVjcN8K3GA4qyhLmWMRFg",
        "info": {
            "id": 1,
            "name": "张三",
            "mobile": "18888888888"
        },
        "menu": [
            {
                "id": "1",
                "type": "2",
                "name": "个人信息管理",
                "parent_id": "0",
                "is_hide": "0",
                "is_public": "1"
            },
            {
                "id": "2",
                "type": "1",
                "name": "系统管理",
                "parent_id": "0",
                "is_hide": "0",
                "is_public": "0",
                "child": [
                    {
                        "id": "29",
                        "type": "2",
                        "name": "操作记录",
                        "parent_id": "2",
                        "is_hide": "0",
                        "is_public": "0"
                    }
                ]
            }
        ]
    },
    "time": 1625204901
}
```

#### 4.2.3、发送短信验证码
**url**

layout/login/send-captcha

**入参**

| 字段     | 类型     | 必填 | 备注  |
|--------|--------|----|-----|
| mobile | string | 是  | 手机号 |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625210009
}
```


#### 4.2.4、短信验证码登录
**url**

layout/login/by-captcha

**入参**

| 字段     | 类型     | 必填 | 备注  |
|--------|--------|----|-----|
| mobile | string | 是  | 手机号 |

**响应内容字段说明**

| 字段             | 类型     | 备注                  |
|----------------|--------|---------------------|
| token          | string | token               |
| info           | array  | 用户信息                |
| info.id        | int    | 用户ID                |
| info.name      | string | 用户姓名                |
| info.mobile    | string | 用户手机号               |
| menu           | array  | 菜单列表                |
| menu.id        | string | 菜单ID                |
| menu.type      | string | 菜单类型：1 目录，2 页面，3 按钮 |
| menu.name      | string | 菜单名称                |
| menu.parent_id | string | 菜单父ID               |
| menu.is_hide   | string | 菜单是否隐藏：1 是，0 否      |
| menu.is_public | string | 菜单是否公开：1 是，0 否      |
| menu.child     | array  | 菜单的子菜单列表            |

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {
        "token": "eyJ1c2VyX2lkIjoxLCJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJKV1QiLCJzdWIiOiJzdWI6and0IiwiYXVkIjoieWlpMi1pbml0IiwidXNlcl9pZCI6MX0.U0gHuTPtSA4JDCSI-mI_6hYVjcN8K3GA4qyhLmWMRFg",
        "info": {
            "id": 1,
            "name": "张三",
            "mobile": "18888888888"
        },
        "menu": [
            {
                "id": "1",
                "type": "2",
                "name": "个人信息管理",
                "parent_id": "0",
                "is_hide": "0",
                "is_public": "1"
            },
            {
                "id": "2",
                "type": "1",
                "name": "系统管理",
                "parent_id": "0",
                "is_hide": "0",
                "is_public": "0",
                "child": [
                    {
                        "id": "29",
                        "type": "2",
                        "name": "操作记录",
                        "parent_id": "2",
                        "is_hide": "0",
                        "is_public": "0"
                    }
                ]
            }
        ]
    },
    "time": 1625204901
}
```

#### 4.2.5、钉钉扫码登录
**url**

layout/login/by-dingding

**入参**

| 字段   | 类型     | 必填 | 备注 |
|------|--------|----|----|
| code | string | 是  | 代码 |

**响应内容字段说明**

| 字段             | 类型     | 备注                  |
|----------------|--------|---------------------|
| token          | string | token               |
| info           | array  | 用户信息                |
| info.id        | int    | 用户ID                |
| info.name      | string | 用户姓名                |
| info.mobile    | string | 用户手机号               |
| menu           | array  | 菜单列表                |
| menu.id        | string | 菜单ID                |
| menu.type      | string | 菜单类型：1 目录，2 页面，3 按钮 |
| menu.name      | string | 菜单名称                |
| menu.parent_id | string | 菜单父ID               |
| menu.is_hide   | string | 菜单是否隐藏：1 是，0 否      |
| menu.is_public | string | 菜单是否公开：1 是，0 否      |
| menu.child     | array  | 菜单的子菜单列表            |

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {
        "token": "eyJ1c2VyX2lkIjoxLCJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJKV1QiLCJzdWIiOiJzdWI6and0IiwiYXVkIjoieWlpMi1pbml0IiwidXNlcl9pZCI6MX0.U0gHuTPtSA4JDCSI-mI_6hYVjcN8K3GA4qyhLmWMRFg",
        "info": {
            "id": 1,
            "name": "张三",
            "mobile": "18888888888"
        },
        "menu": [
            {
                "id": "1",
                "type": "2",
                "name": "个人信息管理",
                "parent_id": "0",
                "is_hide": "0",
                "is_public": "1"
            },
            {
                "id": "2",
                "type": "1",
                "name": "系统管理",
                "parent_id": "0",
                "is_hide": "0",
                "is_public": "0",
                "child": [
                    {
                        "id": "29",
                        "type": "2",
                        "name": "操作记录",
                        "parent_id": "2",
                        "is_hide": "0",
                        "is_public": "0"
                    }
                ]
            }
        ]
    },
    "time": 1625204901
}
```

### 4.3、用户个人信息[要登录]
#### 4.3.1、登录检查[公共接口]
**url**

user/check-login

**入参**

| 字段   | 类型     | 必填 | 备注 |
|------|--------|----|----|
| code | string | 是  | 代码 |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625210256
}
```

#### 4.3.2、获取个人用户信息[公共接口]
**url**

user/get-info

**入参**

| 字段   | 类型     | 必填 | 备注 |
|------|--------|----|----|
| code | string | 是  | 代码 |

**响应内容字段说明**

| 字段            | 类型     | 备注    |
|---------------|--------|-------|
| info          | array  | 用户信息  |
| info.id       | int    | 用户ID  |
| info.name     | string | 用户姓名  |
| info.mobile   | string | 用户手机号 |
| roles         | array  | 角色列表  |
| roles.id      | string | 角色ID  |
| roles.name    | string | 角色名称  |
| roles.comment | string | 角色描述  |

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {
        "info": {
            "id": 1,
            "name": "张三",
            "mobile": "18888888888"
        },
        "roles": [
            {
                "id": "1",
                "name": "超级管理员",
                "comment": "超级管理员"
            }
        ]
    },
    "time": 1625210301
}
```

#### 4.3.4、修改密码[公共接口]
**url**

user/change-password

**入参**

| 字段       | 类型     | 必填 | 备注 |
|----------|--------|----|----|
| password | string | 是  | 密码 |

**响应内容字段说明**

| 字段            | 类型     | 备注    |
|---------------|--------|-------|
| info          | array  | 用户信息  |
| info.id       | int    | 用户ID  |
| info.name     | string | 用户姓名  |
| info.mobile   | string | 用户手机号 |
| roles         | array  | 角色列表  |
| roles.id      | string | 角色ID  |
| roles.name    | string | 角色名称  |
| roles.comment | string | 角色描述  |

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625211005
}
```

### 4.4、字典管理[要登录]
#### 4.4.1、获取字典列表
**url**

system/dict/list

**入参**

| 字段        | 类型     | 必填 | 备注               |
|-----------|--------|----|------------------|
| sign      | string | 否  | 字典标识，默认为空，空表示全部  |
| name      | string | 否  | 字典名称，默认为空，空表示全部  |
| parent_id | string | 否  | 字典父ID，默认为空，空表示全部 |
| page      | string | 否  | 第几页，默认1          |
| page_size | string | 否  | 每页数据量，默认10       |

**响应内容字段说明**

| 字段            | 类型     | 备注           |
|---------------|--------|--------------|
| total         | string | 总数           |
| list          | array  | 数据列表         |
| id            | string | 字典ID         |
| sign          | string | 字典标识         |
| name          | string | 字典名称         |
| comment       | string | 字典描述         |
| parent_id     | string | 字典父ID        |
| is_deleted    | string | 是否删除：1 是，0 否 |
| operator_id   | string | 操作人ID        |
| created_time  | string | 创建时间         |
| updated_time  | string | 更新时间         |
| operator_name | string | 操作人姓名        |

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {
        "total": "13",
        "list": [
            {
                "id": "13",
                "sign": "test",
                "name": "测试",
                "comment": "测试",
                "parent_id": "0",
                "is_deleted": "1",
                "operator_id": "1",
                "created_time": "2021-07-02 15:08:42",
                "updated_time": "2021-07-02 15:10:21",
                "operator_name": "张三"
            }
        ]
    },
    "time": 1625211138
}
```

#### 4.4.2、添加字典
**url**

system/dict/add

**入参**

| 字段        | 类型     | 必填 | 备注                 |
|-----------|--------|----|--------------------|
| sign      | string | 是  | 字典标识               |
| name      | string | 是  | 字典名称               |
| parent_id | string | 否  | 字典父ID，默认为0         |
| comment   | string | 否  | 字典描述，默认为空          |
| status    | string | 否  | 字典状态，默认0：0 无效，1 有效 |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625211348
}
```

#### 4.4.3、编辑字典
**url**

system/dict/edit

**入参**

| 字段        | 类型     | 必填 | 备注         |
|-----------|--------|----|------------|
| id        | string | 是  | 字典ID       |
| sign      | string | 是  | 字典标识       |
| name      | string | 是  | 字典名称       |
| parent_id | string | 否  | 字典父ID，默认为0 |
| comment   | string | 否  | 字典描述，默认为空  |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625211348
}
```

#### 4.4.4、修改字典状态
**url**

system/dict/change-status

**入参**

| 字段     | 类型     | 必填 | 备注                 |
|--------|--------|----|--------------------|
| id     | string | 是  | 字典ID               |
| status | string | 否  | 字典状态，默认0：0 无效，1 有效 |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625211348
}
```

### 4.5、登录管理[要登录]
#### 4.5.1、获取登录过期时间信息
**url**

system/login/get-login-expire-info

**入参**

| 字段 | 类型 | 必填 | 备注 |
|----|----|----|----|

**响应内容字段说明**

| 字段              | 类型     | 备注                |
|-----------------|--------|-------------------|
| no_action_num   | int    | 无操作过期时间的数量，大于0的整数 |
| no_action_unit  | string | 无操作过期时间的单位        |
| has_action_num  | int    | 有操作过期时间的数量，大于0的整数 |
| has_action_unit | string | 有操作过期时间的单位        |

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {
        "no_action_num": 3,
        "no_action_unit": "小时",
        "has_action_num": 9,
        "has_action_unit": "小时"
    },
    "time": 1625211635
}
```

#### 4.5.2、设置登录过期时间
**url**

system/login/set-login-expire-info

**入参**

| 字段              | 类型     | 必填 | 备注                   |
|-----------------|--------|----|----------------------|
| no_action_num   | string | 是  | 无操作过期时间的数量，大于0的整数    |
| no_action_unit  | string | 是  | 无操作过期时间的单位，范围：小时、天、月 |
| has_action_num  | string | 是  | 有操作过期时间的数量，大于0的整数    |
| has_action_unit | string | 是  | 有操作过期时间的单位，范围：小时、天、月 |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625211811
}
```

#### 4.5.3、获取密码有效期信息
**url**

system/login/get-pwd-expire

**入参**

| 字段 | 类型 | 必填 | 备注 |
|----|----|----|----|

**响应内容字段说明**

| 字段         | 类型     | 备注    |
|------------|--------|-------|
| pwd_expire | string | 密码有效期 |

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {
        "pwd_expire": "1个月"
    },
    "time": 1625211926
}
```

#### 4.5.4、设置密码有效期
**url**

system/login/set-pwd-expire

**入参**

| 字段         | 类型     | 必填 | 备注                         |
|------------|--------|----|----------------------------|
| pwd_expire | string | 是  | 密码有效期，范围：1个月，3个月，6个月，1年，永久 |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625211926
}
```

#### 4.5.6、获取初始密码信息
**url**

system/login/get-init-pwd

**入参**

| 字段 | 类型 | 必填 | 备注 |
|----|----|----|----|

**响应内容字段说明**

| 字段       | 类型     | 备注   |
|----------|--------|------|
| init_pwd | string | 初始密码 |

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {
        "init_pwd": "11111111"
    },
    "time": 1625212085
}
```

#### 4.5.7、设置初始密码
**url**

system/login/set-init-pwd

**入参**

| 字段       | 类型     | 必填 | 备注   |
|----------|--------|----|------|
| init_pwd | string | 是  | 初始密码 |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625211926
}
```

#### 4.5.8、获取登录方式信息
**url**

system/login/get-login-methods

**入参**

| 字段 | 类型 | 必填 | 备注 |
|----|----|----|----|

**响应内容字段说明**

| 字段   | 类型    | 备注                                 |
|------|-------|------------------------------------|
| list | array | 登录方式标识列表：1 密码登录，2 短信验证码登录，3 钉钉扫码登录 |

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {
        "list": [
            1,
            2,
            3
        ]
    },
    "time": 1625212188
}
```

#### 4.5.9、设置登录方式
**url**

system/login/set-login-methods

**入参**

| 字段   | 类型    | 必填 | 备注                                 |
|------|-------|----|------------------------------------|
| list | array | 是  | 登录方式标识列表：1 密码登录，2 短信验证码登录，3 钉钉扫码登录 |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|


**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625212188
}
```

### 4.6、接口管理[要登录]
#### 4.6.1、获取接口列表
**url**

system/interface/list

**入参**

| 字段         | 类型     | 必填 | 备注                        |
|------------|--------|----|---------------------------|
| type       | string | 否  | 接口类型，默认-1：-1 所有，1 模块，2 方法 |
| name       | string | 否  | 接口名称，默认空，表示所有             |
| sign       | string | 否  | 接口表示，默认空，表示所有             |
| is_public  | string | 否  | 是否公共，默认-1：-1 所有，1 是，0 否   |
| is_deleted | string | 否  | 是否删除，默认-1：-1 所有，1 是，0 否   |
| page       | string | 否  | 第几页，默认1                   |
| page_size  | string | 否  | 每页数据量，默认10                |

**响应内容字段说明**

| 字段            | 类型     | 备注           |
|---------------|--------|--------------|
| total         | string | 总数           |
| list          | array  | 数据列表         |
| id            | string | 接口ID         |
| name          | string | 接口名称         |
| sign          | string | 接口标识         |
| type          | string | 接口描述         |
| parent_id     | string | 接口父ID        |
| is_public     | string | 是否公共：1 是，0 否 |
| is_deleted    | string | 是否删除：1 是，0 否 |
| operator_id   | string | 操作人ID        |
| created_time  | string | 创建时间         |
| updated_time  | string | 更新时间         |
| operator_name | string | 操作人姓名        |

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {
        "total": "52",
        "list": [
            {
                "id": "1",
                "name": "用户个人信息",
                "sign": "user",
                "type": "1",
                "parent_id": "0",
                "is_public": "1",
                "is_deleted": "0",
                "operator_id": "0",
                "created_time": "2021-07-02 11:10:08",
                "updated_time": "2021-07-02 11:10:08",
                "operator_name": "系统"
            }
        ]
    },
    "time": 1625212438
}
```

#### 4.6.2、刷新接口列表
**url**

system/interface/refresh

**入参**

| 字段 | 类型 | 必填 | 备注 |
|----|----|----|----|

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625212438
}
```



### 4.7、菜单管理[要登录]
#### 4.7.1、获取菜单列表

**URL**

system/menu/list

**入参**

| 字段        | 类型     | 必填 | 备注                        |
|-----------|--------|----|---------------------------|
| name      | string |    | 菜单名称，模糊匹配                 |
| is_parent | string |    | 是否父菜单（默认全部：-1 全部，1 是，0 否） |
| is_public | string |    | 是否公共（默认全部：-1 全部，1 是，0 否）  |
| is_hide   | string |    | 是否隐藏（默认全部：-1 全部，1 是，0 否）  |
| status    | string |    | 状态（默认全部：-1 全部，1 正常，0 禁用）  |
| page      | string |    | 页码，默认 1                   |
| page_size | string |    | 每页条数，默认10条                |

**响应内容字段说明**

| 字段          | 类型                | 备注                                      |
| ------------ | ------------------- | ----------------------------------------- |
| total        | string              | 总条数                                    |
| id           | string              | 菜单ID                                    |
| name         | string              | 菜单名称                                  |
| type         | string              | 类型（1 目录，2 页面，3 按钮）            |
| parent_id    | string              | 父级ID，为0时表示无父节点                 |
| route        | string              | 路由名称                                  |
| icon         | string              | 图标                                      |
| is_public    | string              | 是否公共（1 是，0 否）                    |
| order        | string              | 排序位                                    |
| is_hide      | string              | 是否隐藏（1 是，0 否）                    |
| is_deleted   | string              | 是否删除（1 是，0 否）                    |
| operator_id  | string              | 操作人ID                                  |
| created_time | string            | 创建时间（YYYY-MM-dd HH:ii:ss 格式）      |
| updated_time | string            | 修改时间（YYYY-MM-dd HH:ii:ss 格式）      |
| parent_name  | string              | 父菜单名称，parent_id=0 时，该字段值为 -- |


**响应内容示例**

```json
{
    "status": 1,
    "message": "success",
    "response": {
        "total": "1",
        "list": [
            {
                "id": "1",
                "name": "个人信息管理",
                "type": "2",
                "parent_id": "0",
                "route": "/user",
                "icon": "",
                "is_public": "1",
                "order": "0",
                "is_hide": "0",
                "is_deleted": "0",
                "operator_id": "0",
                "created_time": "2021-07-01 10:43:46",
                "updated_time": "2021-07-01 10:43:46",
                "parent_name": "--"
            }
        ]
    },
    "time": 1625212024
}
```



#### 4.7.2、添加菜单

**URL**

system/menu/add

**入参**

| 字段        | 类型     | 必填 | 备注                                             |
|-----------|--------|----|------------------------------------------------|
| name      | string | 是  | 菜单名称                                           |
| type      | string | 是  | 类型（1 目录，2 页面，3 按钮）                             |
| is_hide   | string |    | 是否隐藏（1 是，0 否。默认 0）                             |
| is_public | string |    | 是否公共（1 是，0 否。默认 0）                             |
| parent_id | string |    | 父ID（1 是，0 否。默认 0）                              |
| order     | string |    | 排序（默认 0）                                       |
| route     | string |    | 路由（当 type=1 时可为空；当 type=3 时，需要从【布局系统字典信息接口】获取） |
| icon      | string | 是  | 图标（需要从【布局系统字典信息接口】获取）                          |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625210009
}
```



#### 4.7.3、编辑菜单

**URL**

system/menu/edit

**入参**

| 字段        | 类型     | 必填 | 备注                                             |
|-----------|--------|----|------------------------------------------------|
| id        | string | 是  | 菜单ID                                           |
| name      | string | 是  | 菜单名称                                           |
| type      | string | 是  | 类型（1 目录，2 页面，3 按钮）                             |
| is_hide   | string |    | 是否隐藏（1 是，0 否。默认 0）                             |
| is_public | string |    | 是否公共（1 是，0 否。默认 0）                             |
| parent_id | string |    | 父ID（1 是，0 否。默认 0）                              |
| order     | string |    | 排序（默认 0）                                       |
| route     | string |    | 路由（当 type=1 时可为空；当 type=3 时，需要从【布局系统字典信息接口】获取） |
| icon      | string | 是  | 图标（需要从【布局系统字典信息接口】获取）                          |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625210009
}
```



#### 4.7.4、修改菜单状态

**URL**

system/menu/change-status

**入参**

|字段| 类型| 必填| 备注|
|--| --| --| --|
|id|string|是|菜单ID|
|status|string|是| 状态（ 1正常，0 禁用）|

**响应内容字段说明**

|字段| 类型 | 备注|
|-- | --  | --|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625210009
}
```



### 4.8、账户管理[要登录]
#### 4.8.1、获取账户过期时间信息

**URL**

system/user/get-expire

**入参**

|字段| 类型| 必填| 备注|
|--| --| --| --|


**响应内容字段说明**

| 字段 | 类型 | 备注  |
| --- | ---- | ----- |
|num|string|数量|
|unit|string|单位|


**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {
        "num": 2,
        "unit": "周"
    },
    "time": 1625223670
}
```



#### 4.8.2、设置账户过期时间

**URL**

system/user/set-expire

**入参**

| 字段   | 类型     | 必填 | 备注 |
|------|--------|----|----|
| num  | string | 是  | 数量 |
| unit | string | 是  | 单位 |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625210009
}
```



#### 4.8.3、获取账户列表

**URL**

system/user/list
**入参**

| 字段        | 类型     | 必填 | 备注                       |
|-----------|--------|----|--------------------------|
| name      | string |    | 用户名称，模糊匹配                |
| role_id   | string |    | 角色ID                     |
| status    | string |    | 状态（默认全部：-1 全部，1 正常，0 禁用） |
| page      | string |    | 页码，默认 1                  |
| page_size | string |    | 每页条数，默认10条               |

**响应内容字段说明**

| 字段            | 类型     | 备注                           |
|---------------|--------|------------------------------|
| total         | string | 总条数                          |
| id            | string | 用户ID                         |
| name          | string | 用户名称                         |
| is_deleted    | string | 是否删除（1 是，0 否）                |
| operator_name | string | 操作人                          |
| created_time  | string | 创建时间（YYYY-mm-dd HH:ii:ss 格式） |
| roles         | array  | 角色列表                         |
| roles.id      | string | 角色ID                         |
| roles.name    | string | 角色名称                         |
| roles.comment | string | 角色描述                         |


**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {
        "total": "1",
        "list": [
            {
                "id": 1,
                "name": "weiqianyang",
                "is_deleted": 0,
                "created_time": "2021-07-01 10:43:45",
                "operator_name": "系统",
                "roles": [
                    {
                        "id": "1",
                        "name": "超级管理员",
                        "comment": "超级管理员"
                    }
                ]
            }
        ]
    },
    "time": 1625223314
}
```


#### 4.8.4、添加账户

**URL**

system/user/add

**入参**

| 字段     | 类型     | 必填 | 备注            |
|--------|--------|----|---------------|
| name   | string | 是  | 用户名称          |
| mobile | string |    | 手机号           |
| status | string |    | 状态（1 正常，0 禁用） |
| roles  | array  |    | 角色列表          |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625210009
}
```



#### 4.8.5、编辑账户

**URL**

system/user/edit

**入参**

| 字段         | 类型     | 必填 | 备注   |
|------------|--------|----|------|
| id         | string | 是  | 用户ID |
| name       | string | 是  | 用户名称 |
| mobile     | string | 是  | 手机号  |
| roles      | array  |    | 角色列表 |
| interfaces | array  |    | 接口列表 |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625210009
}
```



#### 4.8.6、修改账户状态

**URL**

system/user/change-status

**入参**

| 字段     | 类型     | 必填 | 备注            |
|--------|--------|----|---------------|
| id     | string | 是  | 用户ID          |
| status | string | 是  | 状态（ 1正常，0 禁用） |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625210009
}
```


#### 4.8.7、初始化账户密码

**URL**

system/user/init-pwd

**入参**

| 字段 | 类型     | 必填 | 备注   |
|----|--------|----|------|
| id | string | 是  | 用户ID |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625210009
}
```



### 4.9、角色管理[要登录]
#### 4.9.1、获取角色列表


**URL**

system/role/list
**入参**

|字段| 类型| 必填| 备注|
|--| --| --| --|
|name|string||角色名称，模糊匹配|
|status|string||状态（默认全部：-1 全部，1 正常，0 禁用）|
|page|string||页码，默认 1|
|page_size|string||每页条数，默认10条|


**响应内容字段说明**

| 字段 | 类型 | 备注  |
| --- | ---- | ----- |
| total           | string              | 总条数                                    |
| id | string  | 角色ID |
| name | string  | 角色名称 |
| comment | string  | 角色描述 |
| is_deleted | string  | 是否删除（1 是，0 否）  |
| operator_id | string  | 操作人ID |
| operator_name | string  | 操作人 |
| created_time | string  | 创建时间（YYYY-mm-dd HH:ii:ss 格式） |
| updated_time | string  | 更新时间（YYYY-mm-dd HH:ii:ss 格式） |


**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {
        "total": "1",
        "list": [
            {
                "id": "1",
                "name": "超级管理员",
                "comment": "超级管理员",
                "is_deleted": "0",
                "operator_id": "0",
                "created_time": "2021-07-01 10:43:45",
                "updated_time": "2021-07-01 10:43:45",
                "operator_name": "系统"
            }
        ]
    },
    "time": 1625222243
}
```


#### 4.9.2、添加角色

**URL**

system/role/add

**入参**

| 字段         | 类型     | 必填 | 备注            |
|------------|--------|----|---------------|
| name       | string | 是  | 角色名称          |
| comment    | string |    | 角色描述          |
| status     | string |    | 状态（1 正常，0 禁用） |
| menus      | array  |    | 菜单列表          |
| interfaces | array  |    | 接口列表          |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625210009
}
```



#### 4.9.3、获取角色信息


**URL**

system/role/get-info

**入参**

| 字段 | 类型     | 必填 | 备注   |
|----|--------|----|------|
| id | string | 是  | 角色ID |

**响应内容字段说明**

| 字段 | 类型 | 备注  |
| --- | ---- | ----- |
| id | string  | 角色ID |
| name | string  | 角色名称 |
| comment | string  | 角色描述 |
| select | string  | 是否选中，1选中，0未选中 |
| menus | array  | 角色菜单 |
| menus.id | string  | 菜单 ID |
| menus.type | string  | 类型（1 目录，2 页面，3 按钮） |
| menus.name | string  | 菜单 名称 |
| menus.parent_id | string  | 父ID，为0时表示无父级 |
| menus.is_hide | string  | 是隐藏1 是，0 否  |
| menus.child | array  | 子节点 |
| interfaces.id | string  | 接口ID |
| interfaces.type | string  | 类型（1 模块，2 方法） |
| interfaces.name | string  | 接口 名称 |
| interfaces.parent_id | string  | 父ID，为0时表示无父级 |
| interfaces.child | array  | 子节点 |


**响应内容示例**
```json
{
    "status":1,
    "message":"success",
    "response":{
        "id":1,
        "name":"超级管理员",
        "comment":"超级管理员",
        "menus":[
            {
                "id":"2",
                "type":"1",
                "name":"系统管理",
                "parent_id":"0",
                "is_hide":"0",
                "child":[
                    {
                        "id":"3",
                        "type":"2",
                        "name":"系统字典",
                        "parent_id":"2",
                        "is_hide":"0",
                        "child":[
                            {
                                "id":"6",
                                "type":"3",
                                "name":"修改状态",
                                "parent_id":"3",
                                "is_hide":"0",
                                "select":1
                            }
                        ],
                        "select":1
                    }
                ],
                "select":1
            }
        ],
        "interfaces":[
            {
                "id":"4",
                "type":"1",
                "name":"系统字典管理",
                "parent_id":"0",
                "child":[
                    {
                        "id":"23",
                        "type":"2",
                        "name":"修改字典状态",
                        "parent_id":"4",
                        "select":1
                    }
                ],
                "select":1
            }
        ]
    },
    "time":1625221290
}
```



#### 4.9.4、编辑角色

**URL**

system/role/edit

**入参**

| 字段         | 类型     | 必填 | 备注   |
|------------|--------|----|------|
| id         | string | 是  | 角色ID |
| name       | string | 是  | 角色名称 |
| comment    | string |    | 角色描述 |
| menus      | array  |    | 菜单列表 |
| interfaces | array  |    | 接口列表 |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625210009
}
```



#### 4.9.5、修改角色状态

**URL**

system/role/change-status

**入参**

| 字段     | 类型     | 必填 | 备注            |
|--------|--------|----|---------------|
| id     | string | 是  | 角色ID          |
| status | string | 是  | 状态（ 1正常，0 禁用） |

**响应内容字段说明**

| 字段 | 类型 | 备注 |
|----|----|----|

**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {},
    "time": 1625210009
}
```


#### 4.9.6、获取权限列表

**URL**

system/role/node-list

**入参**

| 字段 | 类型 | 必填 | 备注 |
|----|----|----|----|

**响应内容字段说明**

| 字段 | 类型 | 备注  |
| --- | ---- | ----- |
| menu | array  | 菜单数据 |
| interfaces | string  | 接口数据 |
| id | string | 菜单/接口 ID |
| type| string | 类型，menu取值（1 目录，2 页面，3 按钮），interface取值（1 模块，2 方法） |
| name | string | 菜单/接口 名称 |
| parent_id | string | 父ID，为0时表示无父级 |
| is_hide  | string | 是否隐藏1 是，0 否 |
| child | array | 子节点 |


**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {
        "menus": [
            {
                "id": "2",
                "type": "1",
                "name": "系统管理",
                "parent_id": "0",
                "is_hide": "0",
                "child": [
                    {
                        "id": "25",
                        "type": "2",
                        "name": "角色管理",
                        "parent_id": "2",
                        "is_hide": "0",
                        "child": [
                            {
                                "id": "28",
                                "type": "3",
                                "name": "修改状态",
                                "parent_id": "25",
                                "is_hide": "0"
                            }
                        ]
                    }
                ]
            }
        ],
        "interfaces": [
            {
                "id": "4",
                "type": "1",
                "name": "系统字典管理",
                "parent_id": "0",
                "child": [
                    {
                        "id": "23",
                        "type": "2",
                        "name": "修改字典状态",
                        "parent_id": "4"
                    }
                ]
            }
        ]
    },
    "time": 1625219489
}
```



### 4.10、操作记录[要登录]
#### 4.10.1、获取操作记录列表

**URL**

system/operation/list

**入参**

| 字段             | 类型     | 必填 | 备注                             |
|----------------|--------|----|--------------------------------|
| start_time     | string |    | 起始时间（ YYYY-MM-dd HH:ii:ss 格式 ） |
| end_time       | string |    | 截止时间（ YYYY-MM-dd HH:ii:ss 格式 ） |
| ip             | string |    | ip（ IPv4地址格式，xxx.xxx.xxx.xxx ） |
| operator_name  | string |    | 操作人姓名                          |
| operator_id    | string |    | 操作人ID                          |
| interface_name | string |    | 接口名称                           |
| interface_sign | string |    | 接口标识                           |
| page           | string |    | 第几页                            |
| page_size      | string |    | 每页数据量                          |

**响应内容字段说明**

| 字段 | 类型 | 备注   |
| ------------ | ------------------- | ----------------------------------------- |
| total  | string  | 总条数                                    |
| menu  | string              |     菜单                              |
| interface_name         | string              |       接口名称   |
| interface_sign    | string              |            接口标识   |
| request        | string              | 请求参数                                  |
| response         | string              | 响应信息                                      |
| ip    | string              | ip                    |
| operator_id        | string              | 操作人ID                                    |
| operator_name      | string              | 操作人姓名                   |
| created_time | string | 创建时间（YYYY-MM-dd HH:ii:ss 格式）      |


**响应内容示例**
```json
{
    "status": 1,
    "message": "success",
    "response": {
        "total": "2372",
        "list": [
            {
                "menu": "无",
                "interface_name": "获取菜单列表",
                "interface_sign": "system/menu/list",
                "request": "{\"is_public\":\"1\"}",
                "response": "{\"status\":1,\"message\":\"success\",\"response\":{\"total\":\"1\",\"list\":[{\"id\":\"1\",\"name\":\"个人信息管理\",\"type\":\"2\",\"parent_id\":\"0\",\"route\":\"\/user\",\"icon\":\"\",\"is_public\":\"1\",\"order\":\"0\",\"is_hide\":\"0\",\"is_deleted\":\"0\",\"operator_id\":\"0\",\"created_time\":\"2021-07-01 10:43:46\",\"updated_time\":\"2021-07-01 10:43:46\",\"parent_name\":\"--\"}]},\"time\":1625212024}",
                "ip": "127.0.0.1",
                "operator_id": "1",
                "operator_name": "weiqianyang",
                "created_time": "2021-07-02 15:47:04"
            }
        ]
    },
    "time": 1625218283
}
```

## 5、初始字典数据
### 5.1、按钮 button
| 标识           | 名称   | 备注   |
|--------------|------|------|
| search       | 搜索   | 搜索   |
| add          | 添加   | 添加   |
| edit         | 更新   | 更新   |
| changeStatus | 修改状态 | 修改状态 |

### 5.2、图标 icon
| 标识                      | 名称  | 备注  |
|-------------------------|-----|-----|
| iconzhanghuguanli-01    | 安全  | 锁   |
| iconcaidanguanli-01     | 菜单  | 菜单  |
| iconjiaoseguanli-01     | 用户  | 人   |
| iconzidianpeizhi-01     | 字典  | 书   |
| icongongzuoliuguanli-01 | 工作流 | 流程  |
| icontongjibaobiao-01    | 统计  | 饼状图 |

## 6、初始化脚本
```bash
php yii init/backend 管理员姓名 管理员手机号
```

1、生成基础数据表。
2、生成基础系统字典。
3、生成基础接口数据。
3、生成基础菜单数据。
4、生成超级管理员角色及权限配置。
5、生成超级管理员账户。
6、生成初始系统字典。
7、显示超级管理员账号及密码。

## 7、开发注意
1、前端跨域已在代码中`\backend\controllers\BaseController::behaviors()`实现，请勿在nginx中配置。
2、数据库连接统一使用`backend`，非AR类，统一使用`\common\helper\db\DB::backend()`。
3、脱敏统一使用`\backend\helper\Tuomin::encrypt($dataType, $data)`。

**AR类生成脚本**
> AR类统一生成到`backend/tables`目录，在`backend/models`目录中继承使用。

```bash
php yii gii/model --db='backend' --ns='backend\tables' --useTablePrefix=1 --interactive=0 --overwrite=1 --tableName=backend_user --modelClass=BackendUser
php yii gii/model --db='backend' --ns='backend\tables' --useTablePrefix=1 --interactive=0 --overwrite=1 --tableName=backend_dictionary --modelClass=BackendDictionary
php yii gii/model --db='backend' --ns='backend\tables' --useTablePrefix=1 --interactive=0 --overwrite=1 --tableName=backend_interface_node --modelClass=BackendInterfaceNode
php yii gii/model --db='backend' --ns='backend\tables' --useTablePrefix=1 --interactive=0 --overwrite=1 --tableName=backend_role --modelClass=BackendRole
php yii gii/model --db='backend' --ns='backend\tables' --useTablePrefix=1 --interactive=0 --overwrite=1 --tableName=backend_role_node --modelClass=BackendRoleNode
php yii gii/model --db='backend' --ns='backend\tables' --useTablePrefix=1 --interactive=0 --overwrite=1 --tableName=backend_system_config --modelClass=BackendSystemConfig
php yii gii/model --db='backend' --ns='backend\tables' --useTablePrefix=1 --interactive=0 --overwrite=1 --tableName=backend_menu_node --modelClass=BackendMenuNode
php yii gii/model --db='backend' --ns='backend\tables' --useTablePrefix=1 --interactive=0 --overwrite=1 --tableName=backend_operation_record --modelClass=BackendOperationRecord
php yii gii/model --db='backend' --ns='backend\tables' --useTablePrefix=1 --interactive=0 --overwrite=1 --tableName=backend_operation_record --modelClass=BackendOperationRecord
```
