<?php

namespace console\controllers;


use backend\facade\InterfaceNode;
use backend\facade\Login;
use backend\facade\Menu;
use backend\models\BackendDictionary;
use backend\models\BackendMenuNode;
use backend\models\BackendRole;
use backend\models\BackendRoleNode;
use backend\models\BackendUserRole;
use backend\models\User;
use common\helper\db\DB;

/**
 * 初始化
 */
class InitController extends BaseController
{
    /**
     * 初始化后台
     * @param string $adminName 超级管理员姓名
     * @param string $adminMobile 超级管理员手机号
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionBackend($adminName, $adminMobile)
    {
        $db = DB::backend();
        //初始化表
        $db->createCommand("
DROP TABLE IF EXISTS `backend_dictionary`;
CREATE TABLE `backend_dictionary` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sign` varchar(64) NOT NULL COMMENT '标识',
  `name` varchar(64) NOT NULL COMMENT '名称',
  `comment` varchar(255) NOT NULL COMMENT '说明',
  `parent_id` int(11) NOT NULL COMMENT '父ID,0表示没有父节点',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除：1 是，0 否',
  `operator_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作人ID',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `sign` (`sign`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统字典表';

DROP TABLE IF EXISTS `backend_interface_node`;
CREATE TABLE `backend_interface_node` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL COMMENT '名称',
  `sign` varchar(64) NOT NULL COMMENT '标识',
  `type` tinyint(4) NOT NULL COMMENT '类型：1 模块，2 方法',
  `parent_id` int(11) NOT NULL COMMENT '父ID,0表示没有父节点',
  `is_public` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否公开：1 是，0 否',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除：1 是，0 否',
  `operator_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作人ID',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `parent_id` (`parent_id`),
  KEY `operator_id` (`operator_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COMMENT='接口权限表';

DROP TABLE IF EXISTS `backend_menu_node`;
CREATE TABLE `backend_menu_node` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL COMMENT '名称',
  `type` tinyint(3) NOT NULL COMMENT '类型：1 目录，2 页面，3 按钮',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父ID,0表示没有父节点',
  `route` varchar(64) NOT NULL DEFAULT '' COMMENT '路由',
  `icon` varchar(64) NOT NULL DEFAULT '' COMMENT '图标',
  `is_public` tinyint(3) NOT NULL DEFAULT '0' COMMENT '是否公开',
  `order` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_hide` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `operator_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作人ID',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `parent_id` (`parent_id`),
  KEY `operator_id` (`operator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='菜单权限表';

DROP TABLE IF EXISTS `backend_role`;
CREATE TABLE `backend_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL COMMENT '名称',
  `comment` varchar(128) NOT NULL COMMENT '描述',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除：1 是，0 否',
  `operator_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作人ID',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `name` (`name`),
  KEY `operator_id` (`operator_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COMMENT='角色表';

DROP TABLE IF EXISTS `backend_role_node`;
CREATE TABLE `backend_role_node` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `type` tinyint(4) NOT NULL COMMENT '类型：1 菜单，2 接口',
  `node_id` int(11) NOT NULL COMMENT '权限ID',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除：1 是，0 否',
  `operator_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作人ID',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `operator_id` (`operator_id`),
  KEY `role_id` (`role_id`),
  KEY `node_id` (`node_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COMMENT='角色权限表';

DROP TABLE IF EXISTS `backend_system_config`;
CREATE TABLE `backend_system_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '名称',
  `value` varchar(255) DEFAULT '' COMMENT '值',
  `operator_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作人ID',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COMMENT='系统配置表';

DROP TABLE IF EXISTS `backend_user`;
CREATE TABLE `backend_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL COMMENT '姓名',
  `mobile` varchar(32) NOT NULL COMMENT '手机',
  `password` varchar(64) NOT NULL COMMENT '密码',
  `password_expire` int(11) NOT NULL COMMENT '密码过期时间，0不过期',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否禁用：1 是，0 否',
  `operator_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作人ID',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `mobile` (`mobile`),
  KEY `operator_id` (`operator_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COMMENT='后台用户表';

DROP TABLE IF EXISTS `backend_user_role`;
CREATE TABLE `backend_user_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户ID',
  `role_id` int(11) NOT NULL COMMENT '角色ID',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除：1 是，0 否',
  `operator_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作人ID',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `role_id` (`role_id`),
  KEY `operator_id` (`operator_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8mb4 COMMENT='用户角色关系表';

DROP TABLE IF EXISTS `backend_operation_record`;
CREATE TABLE `backend_operation_record` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu` varchar(64) NOT NULL COMMENT '菜单',
  `interface_name` varchar(128) NOT NULL COMMENT '接口名称',
  `interface_sign` varchar(128) NOT NULL COMMENT '接口标识',
  `request` text COMMENT '请求参数',
  `response` text COMMENT '响应信息',
  `ip` varchar(32) NOT NULL COMMENT 'ip',
  `operator_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作人ID',
  `operator_name` varchar(32) NOT NULL DEFAULT '' COMMENT '操作人姓名',
  `created_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `interface_name` (`interface_name`),
  KEY `interface_sign` (`interface_sign`),
  KEY `ip` (`ip`),
  KEY `operator_id` (`operator_id`),
  KEY `operator_name` (`operator_name`),
  KEY `created_time` (`created_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='操作记录表';
        ")->execute();

        //初始化基本数据
        $role = new BackendRole([
            'name' => '超级管理员',
            'comment' => '超级管理员',
        ]);
        $role->msave();
        $user = new User([
            'name' => $adminName,
            'mobile' => $adminMobile,
            'password' => User::generatePassword(Login::getInitPwd()),
            'password_expire' => 0,
        ]);
        $user->msave();
        $userRole = new BackendUserRole([
            'user_id' => $user->id,
            'role_id' => $role->id,
        ]);
        $userRole->msave();

        //刷新接口
        InterfaceNode::refresh();

        //给超级管理员设置所有非公开接口权限
        foreach (InterfaceNode::getAll() as $node) {
            $roleNode = new BackendRoleNode([
                'role_id' => $role->id,
                'type' => BackendRoleNode::TYPE_INTERFACE,
                'node_id' => $node['id'],
            ]);
            $roleNode->msave();
        }

        //初始化基础功能菜单
        $menus = [
            '个人信息管理' => ['type' => 2, 'order' => 0, 'route' => '/user', 'is_hide' => 0, 'is_public' => 1, 'child' => []],
            '系统管理' => ['type' => 1, 'order' => 1, 'route' => '', 'is_hide' => 0, 'is_public' => 0, 'child' => [
                '系统字典' => ['type' => 2, 'order' => 1, 'route' => '/system-dict', 'is_hide' => 0, 'is_public' => 0, 'child' => [
                    '添加字典' => ['type' => 2, 'order' => 1, 'route' => '/system-dict/add', 'is_hide' => 1, 'is_public' => 0, 'child' => []],
                    '编辑字典' => ['type' => 2, 'order' => 2, 'route' => '/system-dict/edit', 'is_hide' => 1, 'is_public' => 0, 'child' => []],
                    '修改状态' => ['type' => 3, 'order' => 3, 'route' => 'changeStatus', 'is_hide' => 0, 'is_public' => 0, 'child' => []],
                ]],
                '登录管理' => ['type' => 1, 'order' => 2, 'route' => '', 'is_hide' => 0, 'is_public' => 0, 'child' => [
                    '登录过期时间' => ['type' => 2, 'order' => 1, 'route' => '/login-manage/login-expire', 'is_hide' => 0, 'is_public' => 0, 'child' => []],
                    '密码有效期' => ['type' => 2, 'order' => 2, 'route' => '/login-manage/pwd-expire', 'is_hide' => 0, 'is_public' => 0, 'child' => []],
                    '初始密码' => ['type' => 2, 'order' => 3, 'route' => '/login-manage/init-pwd', 'is_hide' => 0, 'is_public' => 0, 'child' => []],
                    '登录方式' => ['type' => 2, 'order' => 4, 'route' => '/login-manage/login-methods', 'is_hide' => 0, 'is_public' => 0, 'child' => []],
                ]],
                '接口管理' => ['type' => 2, 'order' => 3, 'route' => '/interface-manage', 'is_hide' => 0, 'is_public' => 0, 'child' => [
                    '刷新接口' => ['type' => 3, 'order' => 1, 'route' => '/interface-manage/refresh', 'is_hide' => 0, 'is_public' => 0, 'child' => []],
                ]],
                '菜单管理' => ['type' => 2, 'order' => 4, 'route' => '/menu-manage', 'is_hide' => 0, 'is_public' => 0, 'child' => [
                    '添加菜单' => ['type' => 2, 'order' => 1, 'route' => '/menu-manage/add', 'is_hide' => 1, 'is_public' => 0, 'child' => []],
                    '编辑菜单' => ['type' => 2, 'order' => 2, 'route' => '/menu-manage/edit', 'is_hide' => 1, 'is_public' => 0, 'child' => []],
                    '修改状态' => ['type' => 3, 'order' => 3, 'route' => 'changeStatus', 'is_hide' => 0, 'is_public' => 0, 'child' => []],
                ]],
                '账户管理' => ['type' => 1, 'order' => 5, 'route' => '/user-manage/edit', 'is_hide' => 0, 'is_public' => 0, 'child' => [
                    '账户过期时间' => ['type' => 2, 'order' => 1, 'route' => '/user-manage/expire', 'is_hide' => 0, 'is_public' => 0, 'child' => []],
                    '账户列表' => ['type' => 2, 'order' => 2, 'route' => '/user-manage/list', 'is_hide' => 0, 'is_public' => 0, 'child' => []],
                    '添加账户' => ['type' => 2, 'order' => 3, 'route' => '/user-manage/add', 'is_hide' => 1, 'is_public' => 0, 'child' => []],
                    '编辑账户' => ['type' => 2, 'order' => 4, 'route' => '/user-manage/edit', 'is_hide' => 1, 'is_public' => 0, 'child' => []],
                    '修改状态' => ['type' => 3, 'order' => 5, 'route' => 'changeStatus', 'is_hide' => 0, 'is_public' => 0, 'child' => []],
                    '初始化密码' => ['type' => 3, 'order' => 6, 'route' => '/user-manage/init-pwd', 'is_hide' => 0, 'is_public' => 0, 'child' => []],
                ]],
                '角色管理' => ['type' => 2, 'order' => 6, 'route' => '/role-manage', 'is_hide' => 0, 'is_public' => 0, 'child' => [
                    '添加角色' => ['type' => 2, 'order' => 1, 'route' => '/role-manage/add', 'is_hide' => 1, 'is_public' => 0, 'child' => []],
                    '编辑角色' => ['type' => 2, 'order' => 2, 'route' => '/role-manage/edit', 'is_hide' => 1, 'is_public' => 0, 'child' => []],
                    '修改状态' => ['type' => 3, 'order' => 3, 'route' => 'changeStatus', 'is_hide' => 0, 'is_public' => 0, 'child' => []],
                ]],
                '操作记录' => ['type' => 2, 'order' => 7, 'route' => '/operations', 'is_hide' => 0, 'is_public' => 0, 'child' => []],
            ]],
        ];
        $this->initMenus($menus);

        //设置超级管理员的菜单权限
        foreach (Menu::getAll() as $menu) {
            $roleNode = new BackendRoleNode([
                'role_id' => $role->id,
                'type' => BackendRoleNode::TYPE_MENU,
                'node_id' => $menu['id'],
            ]);
            $roleNode->msave();
        }

        //系统字典初始值
        $routes = [
            'button' => ['name' => '按钮', 'comment' => '按钮的路由', 'child' => [
                'search' => ['name' => '搜索', 'comment' => '搜索', 'child' => []],
                'add' => ['name' => '添加', 'comment' => '添加', 'child' => []],
                'edit' => ['name' => '更新', 'comment' => '更新', 'child' => []],
                'changeStatus' => ['name' => '修改状态', 'comment' => '修改状态', 'child' => []],
            ]],
            'icon' => ['name' => '图标', 'comment' => '图标的路由', 'child' => [
                'iconzhanghuguanli-01' => ['name' => '安全', 'comment' => '锁', 'child' => []],
                'iconcaidanguanli-01' => ['name' => '菜单', 'comment' => '菜单', 'child' => []],
                'iconjiaoseguanli-01' => ['name' => '用户', 'comment' => '人', 'child' => []],
                'iconzidianpeizhi-01' => ['name' => '字典', 'comment' => '书', 'child' => []],
                'icongongzuoliuguanli-01' => ['name' => '工作流', 'comment' => '流程', 'child' => []],
                'icontongjibaobiao-01' => ['name' => '统计', 'comment' => '饼状图', 'child' => []],
            ]],
        ];
        $this->initDict($routes);

        $this->output('超级管理员账号:' . $adminMobile);
        $this->output('超级管理员密码:' . Login::getInitPwd());
        $this->output('请及时修改密码。');
    }

    private function initMenus($list, $parentId = 0)
    {
        foreach ($list as $k => $item) {
            $m = new BackendMenuNode([
                'name' => $k,
                'type' => $item['type'],
                'parent_id' => $parentId,
                'route' => $item['route'],
                'icon' => '',
                'is_public' => $item['is_public'],
                'order' => $item['order'],
                'is_hide' => $item['is_hide'],
                'operator_id' => 0,
            ]);
            $m->msave();
            if (!empty($item['child'])) {
                $this->initMenus($item['child'], $m->id);
            }
        }
        return true;
    }

    private function initDict($list, $parentId = 0)
    {
        foreach ($list as $k => $item) {
            $m = new BackendDictionary([
                'sign' => $k,
                'name' => $item['name'],
                'comment' => $item['comment'],
                'parent_id' => $parentId,
                'operator_id' => 0,
            ]);
            $m->msave();
            if (!empty($item['child'])) {
                $this->initDict($item['child'], $m->id);
            }
        }
        return true;
    }
}