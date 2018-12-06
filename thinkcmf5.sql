/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : thinkcmf5

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-10-26 15:38:56
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cmf_admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `cmf_admin_menu`;
CREATE TABLE `cmf_admin_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父菜单id',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '菜单类型;1:有界面可访问菜单,2:无界面可访问菜单,0:只作为菜单',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态;1:显示,0:不显示',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `app` varchar(40) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '应用名',
  `controller` varchar(30) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '控制器名',
  `action` varchar(30) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '操作名称',
  `param` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '额外参数',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单名称',
  `icon` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '菜单图标',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `parent_id` (`parent_id`),
  KEY `controller` (`controller`)
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=utf8mb4 COMMENT='后台菜单表';

-- ----------------------------
-- Records of cmf_admin_menu
-- ----------------------------
INSERT INTO `cmf_admin_menu` VALUES ('1', '0', '0', '1', '20', 'admin', 'Plugin', 'default', '', '插件中心', 'cloud', '插件中心');
INSERT INTO `cmf_admin_menu` VALUES ('2', '1', '1', '1', '10000', 'admin', 'Hook', 'index', '', '钩子管理', '', '钩子管理');
INSERT INTO `cmf_admin_menu` VALUES ('3', '2', '1', '0', '10000', 'admin', 'Hook', 'plugins', '', '钩子插件管理', '', '钩子插件管理');
INSERT INTO `cmf_admin_menu` VALUES ('4', '2', '2', '0', '10000', 'admin', 'Hook', 'pluginListOrder', '', '钩子插件排序', '', '钩子插件排序');
INSERT INTO `cmf_admin_menu` VALUES ('5', '2', '1', '0', '10000', 'admin', 'Hook', 'sync', '', '同步钩子', '', '同步钩子');
INSERT INTO `cmf_admin_menu` VALUES ('6', '0', '0', '1', '0', 'admin', 'Setting', 'default', '', '设置', 'cogs', '系统设置入口');
INSERT INTO `cmf_admin_menu` VALUES ('7', '6', '1', '1', '50', 'admin', 'Link', 'index', '', '友情链接', '', '友情链接管理');
INSERT INTO `cmf_admin_menu` VALUES ('8', '7', '1', '0', '10000', 'admin', 'Link', 'add', '', '添加友情链接', '', '添加友情链接');
INSERT INTO `cmf_admin_menu` VALUES ('9', '7', '2', '0', '10000', 'admin', 'Link', 'addPost', '', '添加友情链接提交保存', '', '添加友情链接提交保存');
INSERT INTO `cmf_admin_menu` VALUES ('10', '7', '1', '0', '10000', 'admin', 'Link', 'edit', '', '编辑友情链接', '', '编辑友情链接');
INSERT INTO `cmf_admin_menu` VALUES ('11', '7', '2', '0', '10000', 'admin', 'Link', 'editPost', '', '编辑友情链接提交保存', '', '编辑友情链接提交保存');
INSERT INTO `cmf_admin_menu` VALUES ('12', '7', '2', '0', '10000', 'admin', 'Link', 'delete', '', '删除友情链接', '', '删除友情链接');
INSERT INTO `cmf_admin_menu` VALUES ('13', '7', '2', '0', '10000', 'admin', 'Link', 'listOrder', '', '友情链接排序', '', '友情链接排序');
INSERT INTO `cmf_admin_menu` VALUES ('14', '7', '2', '0', '10000', 'admin', 'Link', 'toggle', '', '友情链接显示隐藏', '', '友情链接显示隐藏');
INSERT INTO `cmf_admin_menu` VALUES ('15', '6', '1', '1', '10', 'admin', 'Mailer', 'index', '', '邮箱配置', '', '邮箱配置');
INSERT INTO `cmf_admin_menu` VALUES ('16', '15', '2', '0', '10000', 'admin', 'Mailer', 'indexPost', '', '邮箱配置提交保存', '', '邮箱配置提交保存');
INSERT INTO `cmf_admin_menu` VALUES ('17', '15', '1', '0', '10000', 'admin', 'Mailer', 'template', '', '邮件模板', '', '邮件模板');
INSERT INTO `cmf_admin_menu` VALUES ('18', '15', '2', '0', '10000', 'admin', 'Mailer', 'templatePost', '', '邮件模板提交', '', '邮件模板提交');
INSERT INTO `cmf_admin_menu` VALUES ('19', '15', '1', '0', '10000', 'admin', 'Mailer', 'test', '', '邮件发送测试', '', '邮件发送测试');
INSERT INTO `cmf_admin_menu` VALUES ('20', '6', '1', '0', '10000', 'admin', 'Menu', 'index', '', '后台菜单', '', '后台菜单管理');
INSERT INTO `cmf_admin_menu` VALUES ('21', '20', '1', '0', '10000', 'admin', 'Menu', 'lists', '', '所有菜单', '', '后台所有菜单列表');
INSERT INTO `cmf_admin_menu` VALUES ('22', '20', '1', '0', '10000', 'admin', 'Menu', 'add', '', '后台菜单添加', '', '后台菜单添加');
INSERT INTO `cmf_admin_menu` VALUES ('23', '20', '2', '0', '10000', 'admin', 'Menu', 'addPost', '', '后台菜单添加提交保存', '', '后台菜单添加提交保存');
INSERT INTO `cmf_admin_menu` VALUES ('24', '20', '1', '0', '10000', 'admin', 'Menu', 'edit', '', '后台菜单编辑', '', '后台菜单编辑');
INSERT INTO `cmf_admin_menu` VALUES ('25', '20', '2', '0', '10000', 'admin', 'Menu', 'editPost', '', '后台菜单编辑提交保存', '', '后台菜单编辑提交保存');
INSERT INTO `cmf_admin_menu` VALUES ('26', '20', '2', '0', '10000', 'admin', 'Menu', 'delete', '', '后台菜单删除', '', '后台菜单删除');
INSERT INTO `cmf_admin_menu` VALUES ('27', '20', '2', '0', '10000', 'admin', 'Menu', 'listOrder', '', '后台菜单排序', '', '后台菜单排序');
INSERT INTO `cmf_admin_menu` VALUES ('28', '20', '1', '0', '10000', 'admin', 'Menu', 'getActions', '', '导入新后台菜单', '', '导入新后台菜单');
INSERT INTO `cmf_admin_menu` VALUES ('29', '6', '1', '1', '30', 'admin', 'Nav', 'index', '', '导航管理', '', '导航管理');
INSERT INTO `cmf_admin_menu` VALUES ('30', '29', '1', '0', '10000', 'admin', 'Nav', 'add', '', '添加导航', '', '添加导航');
INSERT INTO `cmf_admin_menu` VALUES ('31', '29', '2', '0', '10000', 'admin', 'Nav', 'addPost', '', '添加导航提交保存', '', '添加导航提交保存');
INSERT INTO `cmf_admin_menu` VALUES ('32', '29', '1', '0', '10000', 'admin', 'Nav', 'edit', '', '编辑导航', '', '编辑导航');
INSERT INTO `cmf_admin_menu` VALUES ('33', '29', '2', '0', '10000', 'admin', 'Nav', 'editPost', '', '编辑导航提交保存', '', '编辑导航提交保存');
INSERT INTO `cmf_admin_menu` VALUES ('34', '29', '2', '0', '10000', 'admin', 'Nav', 'delete', '', '删除导航', '', '删除导航');
INSERT INTO `cmf_admin_menu` VALUES ('35', '29', '1', '0', '10000', 'admin', 'NavMenu', 'index', '', '导航菜单', '', '导航菜单');
INSERT INTO `cmf_admin_menu` VALUES ('36', '35', '1', '0', '10000', 'admin', 'NavMenu', 'add', '', '添加导航菜单', '', '添加导航菜单');
INSERT INTO `cmf_admin_menu` VALUES ('37', '35', '2', '0', '10000', 'admin', 'NavMenu', 'addPost', '', '添加导航菜单提交保存', '', '添加导航菜单提交保存');
INSERT INTO `cmf_admin_menu` VALUES ('38', '35', '1', '0', '10000', 'admin', 'NavMenu', 'edit', '', '编辑导航菜单', '', '编辑导航菜单');
INSERT INTO `cmf_admin_menu` VALUES ('39', '35', '2', '0', '10000', 'admin', 'NavMenu', 'editPost', '', '编辑导航菜单提交保存', '', '编辑导航菜单提交保存');
INSERT INTO `cmf_admin_menu` VALUES ('40', '35', '2', '0', '10000', 'admin', 'NavMenu', 'delete', '', '删除导航菜单', '', '删除导航菜单');
INSERT INTO `cmf_admin_menu` VALUES ('41', '35', '2', '0', '10000', 'admin', 'NavMenu', 'listOrder', '', '导航菜单排序', '', '导航菜单排序');
INSERT INTO `cmf_admin_menu` VALUES ('42', '1', '1', '1', '10000', 'admin', 'Plugin', 'index', '', '插件列表', '', '插件列表');
INSERT INTO `cmf_admin_menu` VALUES ('43', '42', '2', '0', '10000', 'admin', 'Plugin', 'toggle', '', '插件启用禁用', '', '插件启用禁用');
INSERT INTO `cmf_admin_menu` VALUES ('44', '42', '1', '0', '10000', 'admin', 'Plugin', 'setting', '', '插件设置', '', '插件设置');
INSERT INTO `cmf_admin_menu` VALUES ('45', '42', '2', '0', '10000', 'admin', 'Plugin', 'settingPost', '', '插件设置提交', '', '插件设置提交');
INSERT INTO `cmf_admin_menu` VALUES ('46', '42', '2', '0', '10000', 'admin', 'Plugin', 'install', '', '插件安装', '', '插件安装');
INSERT INTO `cmf_admin_menu` VALUES ('47', '42', '2', '0', '10000', 'admin', 'Plugin', 'update', '', '插件更新', '', '插件更新');
INSERT INTO `cmf_admin_menu` VALUES ('48', '42', '2', '0', '10000', 'admin', 'Plugin', 'uninstall', '', '卸载插件', '', '卸载插件');
INSERT INTO `cmf_admin_menu` VALUES ('49', '110', '0', '1', '10000', 'admin', 'User', 'default', '', '管理组', '', '管理组');
INSERT INTO `cmf_admin_menu` VALUES ('50', '49', '1', '1', '10000', 'admin', 'Rbac', 'index', '', '角色管理', '', '角色管理');
INSERT INTO `cmf_admin_menu` VALUES ('51', '50', '1', '0', '10000', 'admin', 'Rbac', 'roleAdd', '', '添加角色', '', '添加角色');
INSERT INTO `cmf_admin_menu` VALUES ('52', '50', '2', '0', '10000', 'admin', 'Rbac', 'roleAddPost', '', '添加角色提交', '', '添加角色提交');
INSERT INTO `cmf_admin_menu` VALUES ('53', '50', '1', '0', '10000', 'admin', 'Rbac', 'roleEdit', '', '编辑角色', '', '编辑角色');
INSERT INTO `cmf_admin_menu` VALUES ('54', '50', '2', '0', '10000', 'admin', 'Rbac', 'roleEditPost', '', '编辑角色提交', '', '编辑角色提交');
INSERT INTO `cmf_admin_menu` VALUES ('55', '50', '2', '0', '10000', 'admin', 'Rbac', 'roleDelete', '', '删除角色', '', '删除角色');
INSERT INTO `cmf_admin_menu` VALUES ('56', '50', '1', '0', '10000', 'admin', 'Rbac', 'authorize', '', '设置角色权限', '', '设置角色权限');
INSERT INTO `cmf_admin_menu` VALUES ('57', '50', '2', '0', '10000', 'admin', 'Rbac', 'authorizePost', '', '角色授权提交', '', '角色授权提交');
INSERT INTO `cmf_admin_menu` VALUES ('58', '0', '1', '0', '10000', 'admin', 'RecycleBin', 'index', '', '回收站', '', '回收站');
INSERT INTO `cmf_admin_menu` VALUES ('59', '58', '2', '0', '10000', 'admin', 'RecycleBin', 'restore', '', '回收站还原', '', '回收站还原');
INSERT INTO `cmf_admin_menu` VALUES ('60', '58', '2', '0', '10000', 'admin', 'RecycleBin', 'delete', '', '回收站彻底删除', '', '回收站彻底删除');
INSERT INTO `cmf_admin_menu` VALUES ('61', '6', '1', '1', '10000', 'admin', 'Route', 'index', '', 'URL美化', '', 'URL规则管理');
INSERT INTO `cmf_admin_menu` VALUES ('62', '61', '1', '0', '10000', 'admin', 'Route', 'add', '', '添加路由规则', '', '添加路由规则');
INSERT INTO `cmf_admin_menu` VALUES ('63', '61', '2', '0', '10000', 'admin', 'Route', 'addPost', '', '添加路由规则提交', '', '添加路由规则提交');
INSERT INTO `cmf_admin_menu` VALUES ('64', '61', '1', '0', '10000', 'admin', 'Route', 'edit', '', '路由规则编辑', '', '路由规则编辑');
INSERT INTO `cmf_admin_menu` VALUES ('65', '61', '2', '0', '10000', 'admin', 'Route', 'editPost', '', '路由规则编辑提交', '', '路由规则编辑提交');
INSERT INTO `cmf_admin_menu` VALUES ('66', '61', '2', '0', '10000', 'admin', 'Route', 'delete', '', '路由规则删除', '', '路由规则删除');
INSERT INTO `cmf_admin_menu` VALUES ('67', '61', '2', '0', '10000', 'admin', 'Route', 'ban', '', '路由规则禁用', '', '路由规则禁用');
INSERT INTO `cmf_admin_menu` VALUES ('68', '61', '2', '0', '10000', 'admin', 'Route', 'open', '', '路由规则启用', '', '路由规则启用');
INSERT INTO `cmf_admin_menu` VALUES ('69', '61', '2', '0', '10000', 'admin', 'Route', 'listOrder', '', '路由规则排序', '', '路由规则排序');
INSERT INTO `cmf_admin_menu` VALUES ('70', '61', '1', '0', '10000', 'admin', 'Route', 'select', '', '选择URL', '', '选择URL');
INSERT INTO `cmf_admin_menu` VALUES ('71', '6', '1', '1', '0', 'admin', 'Setting', 'site', '', '网站信息', '', '网站信息');
INSERT INTO `cmf_admin_menu` VALUES ('72', '71', '2', '0', '10000', 'admin', 'Setting', 'sitePost', '', '网站信息设置提交', '', '网站信息设置提交');
INSERT INTO `cmf_admin_menu` VALUES ('73', '6', '1', '0', '10000', 'admin', 'Setting', 'password', '', '密码修改', '', '密码修改');
INSERT INTO `cmf_admin_menu` VALUES ('74', '73', '2', '0', '10000', 'admin', 'Setting', 'passwordPost', '', '密码修改提交', '', '密码修改提交');
INSERT INTO `cmf_admin_menu` VALUES ('75', '6', '1', '1', '10000', 'admin', 'Setting', 'upload', '', '上传设置', '', '上传设置');
INSERT INTO `cmf_admin_menu` VALUES ('76', '75', '2', '0', '10000', 'admin', 'Setting', 'uploadPost', '', '上传设置提交', '', '上传设置提交');
INSERT INTO `cmf_admin_menu` VALUES ('77', '6', '1', '0', '10000', 'admin', 'Setting', 'clearCache', '', '清除缓存', '', '清除缓存');
INSERT INTO `cmf_admin_menu` VALUES ('78', '6', '1', '1', '40', 'admin', 'Slide', 'index', '', '幻灯片管理', '', '幻灯片管理');
INSERT INTO `cmf_admin_menu` VALUES ('79', '78', '1', '0', '10000', 'admin', 'Slide', 'add', '', '添加幻灯片', '', '添加幻灯片');
INSERT INTO `cmf_admin_menu` VALUES ('80', '78', '2', '0', '10000', 'admin', 'Slide', 'addPost', '', '添加幻灯片提交', '', '添加幻灯片提交');
INSERT INTO `cmf_admin_menu` VALUES ('81', '78', '1', '0', '10000', 'admin', 'Slide', 'edit', '', '编辑幻灯片', '', '编辑幻灯片');
INSERT INTO `cmf_admin_menu` VALUES ('82', '78', '2', '0', '10000', 'admin', 'Slide', 'editPost', '', '编辑幻灯片提交', '', '编辑幻灯片提交');
INSERT INTO `cmf_admin_menu` VALUES ('83', '78', '2', '0', '10000', 'admin', 'Slide', 'delete', '', '删除幻灯片', '', '删除幻灯片');
INSERT INTO `cmf_admin_menu` VALUES ('84', '78', '1', '0', '10000', 'admin', 'SlideItem', 'index', '', '幻灯片页面列表', '', '幻灯片页面列表');
INSERT INTO `cmf_admin_menu` VALUES ('85', '84', '1', '0', '10000', 'admin', 'SlideItem', 'add', '', '幻灯片页面添加', '', '幻灯片页面添加');
INSERT INTO `cmf_admin_menu` VALUES ('86', '84', '2', '0', '10000', 'admin', 'SlideItem', 'addPost', '', '幻灯片页面添加提交', '', '幻灯片页面添加提交');
INSERT INTO `cmf_admin_menu` VALUES ('87', '84', '1', '0', '10000', 'admin', 'SlideItem', 'edit', '', '幻灯片页面编辑', '', '幻灯片页面编辑');
INSERT INTO `cmf_admin_menu` VALUES ('88', '84', '2', '0', '10000', 'admin', 'SlideItem', 'editPost', '', '幻灯片页面编辑提交', '', '幻灯片页面编辑提交');
INSERT INTO `cmf_admin_menu` VALUES ('89', '84', '2', '0', '10000', 'admin', 'SlideItem', 'delete', '', '幻灯片页面删除', '', '幻灯片页面删除');
INSERT INTO `cmf_admin_menu` VALUES ('90', '84', '2', '0', '10000', 'admin', 'SlideItem', 'ban', '', '幻灯片页面隐藏', '', '幻灯片页面隐藏');
INSERT INTO `cmf_admin_menu` VALUES ('91', '84', '2', '0', '10000', 'admin', 'SlideItem', 'cancelBan', '', '幻灯片页面显示', '', '幻灯片页面显示');
INSERT INTO `cmf_admin_menu` VALUES ('92', '84', '2', '0', '10000', 'admin', 'SlideItem', 'listOrder', '', '幻灯片页面排序', '', '幻灯片页面排序');
INSERT INTO `cmf_admin_menu` VALUES ('93', '6', '1', '1', '10000', 'admin', 'Storage', 'index', '', '文件存储', '', '文件存储');
INSERT INTO `cmf_admin_menu` VALUES ('94', '93', '2', '0', '10000', 'admin', 'Storage', 'settingPost', '', '文件存储设置提交', '', '文件存储设置提交');
INSERT INTO `cmf_admin_menu` VALUES ('95', '6', '1', '1', '20', 'admin', 'Theme', 'index', '', '模板管理', '', '模板管理');
INSERT INTO `cmf_admin_menu` VALUES ('96', '95', '1', '0', '10000', 'admin', 'Theme', 'install', '', '安装模板', '', '安装模板');
INSERT INTO `cmf_admin_menu` VALUES ('97', '95', '2', '0', '10000', 'admin', 'Theme', 'uninstall', '', '卸载模板', '', '卸载模板');
INSERT INTO `cmf_admin_menu` VALUES ('98', '95', '2', '0', '10000', 'admin', 'Theme', 'installTheme', '', '模板安装', '', '模板安装');
INSERT INTO `cmf_admin_menu` VALUES ('99', '95', '2', '0', '10000', 'admin', 'Theme', 'update', '', '模板更新', '', '模板更新');
INSERT INTO `cmf_admin_menu` VALUES ('100', '95', '2', '0', '10000', 'admin', 'Theme', 'active', '', '启用模板', '', '启用模板');
INSERT INTO `cmf_admin_menu` VALUES ('101', '95', '1', '0', '10000', 'admin', 'Theme', 'files', '', '模板文件列表', '', '启用模板');
INSERT INTO `cmf_admin_menu` VALUES ('102', '95', '1', '0', '10000', 'admin', 'Theme', 'fileSetting', '', '模板文件设置', '', '模板文件设置');
INSERT INTO `cmf_admin_menu` VALUES ('103', '95', '1', '0', '10000', 'admin', 'Theme', 'fileArrayData', '', '模板文件数组数据列表', '', '模板文件数组数据列表');
INSERT INTO `cmf_admin_menu` VALUES ('104', '95', '2', '0', '10000', 'admin', 'Theme', 'fileArrayDataEdit', '', '模板文件数组数据添加编辑', '', '模板文件数组数据添加编辑');
INSERT INTO `cmf_admin_menu` VALUES ('105', '95', '2', '0', '10000', 'admin', 'Theme', 'fileArrayDataEditPost', '', '模板文件数组数据添加编辑提交保存', '', '模板文件数组数据添加编辑提交保存');
INSERT INTO `cmf_admin_menu` VALUES ('106', '95', '2', '0', '10000', 'admin', 'Theme', 'fileArrayDataDelete', '', '模板文件数组数据删除', '', '模板文件数组数据删除');
INSERT INTO `cmf_admin_menu` VALUES ('107', '95', '2', '0', '10000', 'admin', 'Theme', 'settingPost', '', '模板文件编辑提交保存', '', '模板文件编辑提交保存');
INSERT INTO `cmf_admin_menu` VALUES ('108', '95', '1', '0', '10000', 'admin', 'Theme', 'dataSource', '', '模板文件设置数据源', '', '模板文件设置数据源');
INSERT INTO `cmf_admin_menu` VALUES ('109', '95', '1', '0', '10000', 'admin', 'Theme', 'design', '', '模板设计', '', '模板设计');
INSERT INTO `cmf_admin_menu` VALUES ('110', '0', '0', '1', '10', 'user', 'AdminIndex', 'default', '', '用户管理', 'group', '用户管理');
INSERT INTO `cmf_admin_menu` VALUES ('111', '49', '1', '1', '10000', 'admin', 'User', 'index', '', '管理员', '', '管理员管理');
INSERT INTO `cmf_admin_menu` VALUES ('112', '111', '1', '0', '10000', 'admin', 'User', 'add', '', '管理员添加', '', '管理员添加');
INSERT INTO `cmf_admin_menu` VALUES ('113', '111', '2', '0', '10000', 'admin', 'User', 'addPost', '', '管理员添加提交', '', '管理员添加提交');
INSERT INTO `cmf_admin_menu` VALUES ('114', '111', '1', '0', '10000', 'admin', 'User', 'edit', '', '管理员编辑', '', '管理员编辑');
INSERT INTO `cmf_admin_menu` VALUES ('115', '111', '2', '0', '10000', 'admin', 'User', 'editPost', '', '管理员编辑提交', '', '管理员编辑提交');
INSERT INTO `cmf_admin_menu` VALUES ('116', '111', '1', '0', '10000', 'admin', 'User', 'userInfo', '', '个人信息', '', '管理员个人信息修改');
INSERT INTO `cmf_admin_menu` VALUES ('117', '111', '2', '0', '10000', 'admin', 'User', 'userInfoPost', '', '管理员个人信息修改提交', '', '管理员个人信息修改提交');
INSERT INTO `cmf_admin_menu` VALUES ('118', '111', '2', '0', '10000', 'admin', 'User', 'delete', '', '管理员删除', '', '管理员删除');
INSERT INTO `cmf_admin_menu` VALUES ('119', '111', '2', '0', '10000', 'admin', 'User', 'ban', '', '停用管理员', '', '停用管理员');
INSERT INTO `cmf_admin_menu` VALUES ('120', '111', '2', '0', '10000', 'admin', 'User', 'cancelBan', '', '启用管理员', '', '启用管理员');
INSERT INTO `cmf_admin_menu` VALUES ('121', '0', '0', '1', '30', 'portal', 'AdminIndex', 'default', '', '门户管理', 'th', '门户管理');
INSERT INTO `cmf_admin_menu` VALUES ('122', '121', '1', '1', '10000', 'portal', 'AdminArticle', 'index', '', '文章管理', '', '文章列表');
INSERT INTO `cmf_admin_menu` VALUES ('123', '122', '1', '0', '10000', 'portal', 'AdminArticle', 'add', '', '添加文章', '', '添加文章');
INSERT INTO `cmf_admin_menu` VALUES ('124', '122', '2', '0', '10000', 'portal', 'AdminArticle', 'addPost', '', '添加文章提交', '', '添加文章提交');
INSERT INTO `cmf_admin_menu` VALUES ('125', '122', '1', '0', '10000', 'portal', 'AdminArticle', 'edit', '', '编辑文章', '', '编辑文章');
INSERT INTO `cmf_admin_menu` VALUES ('126', '122', '2', '0', '10000', 'portal', 'AdminArticle', 'editPost', '', '编辑文章提交', '', '编辑文章提交');
INSERT INTO `cmf_admin_menu` VALUES ('127', '122', '2', '0', '10000', 'portal', 'AdminArticle', 'delete', '', '文章删除', '', '文章删除');
INSERT INTO `cmf_admin_menu` VALUES ('128', '122', '2', '0', '10000', 'portal', 'AdminArticle', 'publish', '', '文章发布', '', '文章发布');
INSERT INTO `cmf_admin_menu` VALUES ('129', '122', '2', '0', '10000', 'portal', 'AdminArticle', 'top', '', '文章置顶', '', '文章置顶');
INSERT INTO `cmf_admin_menu` VALUES ('130', '122', '2', '0', '10000', 'portal', 'AdminArticle', 'recommend', '', '文章推荐', '', '文章推荐');
INSERT INTO `cmf_admin_menu` VALUES ('131', '122', '2', '0', '10000', 'portal', 'AdminArticle', 'listOrder', '', '文章排序', '', '文章排序');
INSERT INTO `cmf_admin_menu` VALUES ('132', '121', '1', '1', '10000', 'portal', 'AdminCategory', 'index', '', '分类管理', '', '文章分类列表');
INSERT INTO `cmf_admin_menu` VALUES ('133', '132', '1', '0', '10000', 'portal', 'AdminCategory', 'add', '', '添加文章分类', '', '添加文章分类');
INSERT INTO `cmf_admin_menu` VALUES ('134', '132', '2', '0', '10000', 'portal', 'AdminCategory', 'addPost', '', '添加文章分类提交', '', '添加文章分类提交');
INSERT INTO `cmf_admin_menu` VALUES ('135', '132', '1', '0', '10000', 'portal', 'AdminCategory', 'edit', '', '编辑文章分类', '', '编辑文章分类');
INSERT INTO `cmf_admin_menu` VALUES ('136', '132', '2', '0', '10000', 'portal', 'AdminCategory', 'editPost', '', '编辑文章分类提交', '', '编辑文章分类提交');
INSERT INTO `cmf_admin_menu` VALUES ('137', '132', '1', '0', '10000', 'portal', 'AdminCategory', 'select', '', '文章分类选择对话框', '', '文章分类选择对话框');
INSERT INTO `cmf_admin_menu` VALUES ('138', '132', '2', '0', '10000', 'portal', 'AdminCategory', 'listOrder', '', '文章分类排序', '', '文章分类排序');
INSERT INTO `cmf_admin_menu` VALUES ('139', '132', '2', '0', '10000', 'portal', 'AdminCategory', 'delete', '', '删除文章分类', '', '删除文章分类');
INSERT INTO `cmf_admin_menu` VALUES ('140', '121', '1', '1', '10000', 'portal', 'AdminPage', 'index', '', '页面管理', '', '页面管理');
INSERT INTO `cmf_admin_menu` VALUES ('141', '140', '1', '0', '10000', 'portal', 'AdminPage', 'add', '', '添加页面', '', '添加页面');
INSERT INTO `cmf_admin_menu` VALUES ('142', '140', '2', '0', '10000', 'portal', 'AdminPage', 'addPost', '', '添加页面提交', '', '添加页面提交');
INSERT INTO `cmf_admin_menu` VALUES ('143', '140', '1', '0', '10000', 'portal', 'AdminPage', 'edit', '', '编辑页面', '', '编辑页面');
INSERT INTO `cmf_admin_menu` VALUES ('144', '140', '2', '0', '10000', 'portal', 'AdminPage', 'editPost', '', '编辑页面提交', '', '编辑页面提交');
INSERT INTO `cmf_admin_menu` VALUES ('145', '140', '2', '0', '10000', 'portal', 'AdminPage', 'delete', '', '删除页面', '', '删除页面');
INSERT INTO `cmf_admin_menu` VALUES ('146', '121', '1', '1', '10000', 'portal', 'AdminTag', 'index', '', '文章标签', '', '文章标签');
INSERT INTO `cmf_admin_menu` VALUES ('147', '146', '1', '0', '10000', 'portal', 'AdminTag', 'add', '', '添加文章标签', '', '添加文章标签');
INSERT INTO `cmf_admin_menu` VALUES ('148', '146', '2', '0', '10000', 'portal', 'AdminTag', 'addPost', '', '添加文章标签提交', '', '添加文章标签提交');
INSERT INTO `cmf_admin_menu` VALUES ('149', '146', '2', '0', '10000', 'portal', 'AdminTag', 'upStatus', '', '更新标签状态', '', '更新标签状态');
INSERT INTO `cmf_admin_menu` VALUES ('150', '146', '2', '0', '10000', 'portal', 'AdminTag', 'delete', '', '删除文章标签', '', '删除文章标签');
INSERT INTO `cmf_admin_menu` VALUES ('151', '0', '1', '0', '10000', 'user', 'AdminAsset', 'index', '', '资源管理', 'file', '资源管理列表');
INSERT INTO `cmf_admin_menu` VALUES ('152', '151', '2', '0', '10000', 'user', 'AdminAsset', 'delete', '', '删除文件', '', '删除文件');
INSERT INTO `cmf_admin_menu` VALUES ('153', '110', '0', '1', '10000', 'user', 'AdminIndex', 'default1', '', '用户组', '', '用户组');
INSERT INTO `cmf_admin_menu` VALUES ('154', '153', '1', '1', '10000', 'user', 'AdminIndex', 'index', '', '本站用户', '', '本站用户');
INSERT INTO `cmf_admin_menu` VALUES ('155', '154', '2', '0', '10000', 'user', 'AdminIndex', 'ban', '', '本站用户拉黑', '', '本站用户拉黑');
INSERT INTO `cmf_admin_menu` VALUES ('156', '154', '2', '0', '10000', 'user', 'AdminIndex', 'cancelBan', '', '本站用户启用', '', '本站用户启用');
INSERT INTO `cmf_admin_menu` VALUES ('157', '153', '1', '1', '10000', 'user', 'AdminOauth', 'index', '', '第三方用户', '', '第三方用户');
INSERT INTO `cmf_admin_menu` VALUES ('158', '157', '2', '0', '10000', 'user', 'AdminOauth', 'delete', '', '删除第三方用户绑定', '', '删除第三方用户绑定');
INSERT INTO `cmf_admin_menu` VALUES ('159', '6', '1', '1', '10000', 'user', 'AdminUserAction', 'index', '', '用户操作管理', '', '用户操作管理');
INSERT INTO `cmf_admin_menu` VALUES ('160', '159', '1', '0', '10000', 'user', 'AdminUserAction', 'edit', '', '编辑用户操作', '', '编辑用户操作');
INSERT INTO `cmf_admin_menu` VALUES ('161', '159', '2', '0', '10000', 'user', 'AdminUserAction', 'editPost', '', '编辑用户操作提交', '', '编辑用户操作提交');
INSERT INTO `cmf_admin_menu` VALUES ('162', '159', '1', '0', '10000', 'user', 'AdminUserAction', 'sync', '', '同步用户操作', '', '同步用户操作');
INSERT INTO `cmf_admin_menu` VALUES ('163', '153', '1', '1', '10000', 'user', 'AdminIndex', 'audit_list', '', '实名认证审核', '', '实名认证审核');
INSERT INTO `cmf_admin_menu` VALUES ('164', '0', '1', '1', '10000', 'admin', 'Score', 'index', '', '平台管理', 'television', '');
INSERT INTO `cmf_admin_menu` VALUES ('165', '164', '1', '1', '10000', 'admin', 'Score', 'score_change', '', '兑换管理', '', '');
INSERT INTO `cmf_admin_menu` VALUES ('166', '164', '1', '1', '10000', 'admin', 'Setting', 'parameter', '', '参数设置', '', '');
INSERT INTO `cmf_admin_menu` VALUES ('167', '164', '1', '1', '10000', 'device', 'AdminIndex', 'index', '', '设备管理', '', '');
INSERT INTO `cmf_admin_menu` VALUES ('168', '164', '1', '1', '10000', 'device', 'AdminIndex', 'audit', '', '设备审核', '', '');
INSERT INTO `cmf_admin_menu` VALUES ('169', '0', '1', '1', '10000', 'admin', 'History', 'index', '', '历史审核', 'history', '');

-- ----------------------------
-- Table structure for cmf_asset
-- ----------------------------
DROP TABLE IF EXISTS `cmf_asset`;
CREATE TABLE `cmf_asset` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `file_size` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小,单位B',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:可用,0:不可用',
  `download_times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载次数',
  `file_key` varchar(64) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '文件惟一码',
  `filename` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '文件名',
  `file_path` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '文件路径,相对于upload目录,可以为url',
  `file_md5` varchar(32) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '文件md5值',
  `file_sha1` varchar(40) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `suffix` varchar(10) NOT NULL DEFAULT '' COMMENT '文件后缀名,不包括点',
  `more` text COMMENT '其它详细信息,JSON格式',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='资源表';

-- ----------------------------
-- Records of cmf_asset
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_auth_access
-- ----------------------------
DROP TABLE IF EXISTS `cmf_auth_access`;
CREATE TABLE `cmf_auth_access` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL COMMENT '角色',
  `rule_name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识,全小写',
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT '权限规则分类,请加应用前缀,如admin_',
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  KEY `rule_name` (`rule_name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限授权表';

-- ----------------------------
-- Records of cmf_auth_access
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `cmf_auth_rule`;
CREATE TABLE `cmf_auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '规则id,自增主键',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否有效(0:无效,1:有效)',
  `app` varchar(40) NOT NULL DEFAULT '' COMMENT '规则所属app',
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT '权限规则分类，请加应用前缀,如admin_',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则唯一英文标识,全小写',
  `param` varchar(100) NOT NULL DEFAULT '' COMMENT '额外url参数',
  `title` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '规则描述',
  `condition` varchar(200) NOT NULL DEFAULT '' COMMENT '规则附加条件',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE,
  KEY `module` (`app`,`status`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=utf8mb4 COMMENT='权限规则表';

-- ----------------------------
-- Records of cmf_auth_rule
-- ----------------------------
INSERT INTO `cmf_auth_rule` VALUES ('1', '1', 'admin', 'admin_url', 'admin/Hook/index', '', '钩子管理', '');
INSERT INTO `cmf_auth_rule` VALUES ('2', '1', 'admin', 'admin_url', 'admin/Hook/plugins', '', '钩子插件管理', '');
INSERT INTO `cmf_auth_rule` VALUES ('3', '1', 'admin', 'admin_url', 'admin/Hook/pluginListOrder', '', '钩子插件排序', '');
INSERT INTO `cmf_auth_rule` VALUES ('4', '1', 'admin', 'admin_url', 'admin/Hook/sync', '', '同步钩子', '');
INSERT INTO `cmf_auth_rule` VALUES ('5', '1', 'admin', 'admin_url', 'admin/Link/index', '', '友情链接', '');
INSERT INTO `cmf_auth_rule` VALUES ('6', '1', 'admin', 'admin_url', 'admin/Link/add', '', '添加友情链接', '');
INSERT INTO `cmf_auth_rule` VALUES ('7', '1', 'admin', 'admin_url', 'admin/Link/addPost', '', '添加友情链接提交保存', '');
INSERT INTO `cmf_auth_rule` VALUES ('8', '1', 'admin', 'admin_url', 'admin/Link/edit', '', '编辑友情链接', '');
INSERT INTO `cmf_auth_rule` VALUES ('9', '1', 'admin', 'admin_url', 'admin/Link/editPost', '', '编辑友情链接提交保存', '');
INSERT INTO `cmf_auth_rule` VALUES ('10', '1', 'admin', 'admin_url', 'admin/Link/delete', '', '删除友情链接', '');
INSERT INTO `cmf_auth_rule` VALUES ('11', '1', 'admin', 'admin_url', 'admin/Link/listOrder', '', '友情链接排序', '');
INSERT INTO `cmf_auth_rule` VALUES ('12', '1', 'admin', 'admin_url', 'admin/Link/toggle', '', '友情链接显示隐藏', '');
INSERT INTO `cmf_auth_rule` VALUES ('13', '1', 'admin', 'admin_url', 'admin/Mailer/index', '', '邮箱配置', '');
INSERT INTO `cmf_auth_rule` VALUES ('14', '1', 'admin', 'admin_url', 'admin/Mailer/indexPost', '', '邮箱配置提交保存', '');
INSERT INTO `cmf_auth_rule` VALUES ('15', '1', 'admin', 'admin_url', 'admin/Mailer/template', '', '邮件模板', '');
INSERT INTO `cmf_auth_rule` VALUES ('16', '1', 'admin', 'admin_url', 'admin/Mailer/templatePost', '', '邮件模板提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('17', '1', 'admin', 'admin_url', 'admin/Mailer/test', '', '邮件发送测试', '');
INSERT INTO `cmf_auth_rule` VALUES ('18', '1', 'admin', 'admin_url', 'admin/Menu/index', '', '后台菜单', '');
INSERT INTO `cmf_auth_rule` VALUES ('19', '1', 'admin', 'admin_url', 'admin/Menu/lists', '', '所有菜单', '');
INSERT INTO `cmf_auth_rule` VALUES ('20', '1', 'admin', 'admin_url', 'admin/Menu/add', '', '后台菜单添加', '');
INSERT INTO `cmf_auth_rule` VALUES ('21', '1', 'admin', 'admin_url', 'admin/Menu/addPost', '', '后台菜单添加提交保存', '');
INSERT INTO `cmf_auth_rule` VALUES ('22', '1', 'admin', 'admin_url', 'admin/Menu/edit', '', '后台菜单编辑', '');
INSERT INTO `cmf_auth_rule` VALUES ('23', '1', 'admin', 'admin_url', 'admin/Menu/editPost', '', '后台菜单编辑提交保存', '');
INSERT INTO `cmf_auth_rule` VALUES ('24', '1', 'admin', 'admin_url', 'admin/Menu/delete', '', '后台菜单删除', '');
INSERT INTO `cmf_auth_rule` VALUES ('25', '1', 'admin', 'admin_url', 'admin/Menu/listOrder', '', '后台菜单排序', '');
INSERT INTO `cmf_auth_rule` VALUES ('26', '1', 'admin', 'admin_url', 'admin/Menu/getActions', '', '导入新后台菜单', '');
INSERT INTO `cmf_auth_rule` VALUES ('27', '1', 'admin', 'admin_url', 'admin/Nav/index', '', '导航管理', '');
INSERT INTO `cmf_auth_rule` VALUES ('28', '1', 'admin', 'admin_url', 'admin/Nav/add', '', '添加导航', '');
INSERT INTO `cmf_auth_rule` VALUES ('29', '1', 'admin', 'admin_url', 'admin/Nav/addPost', '', '添加导航提交保存', '');
INSERT INTO `cmf_auth_rule` VALUES ('30', '1', 'admin', 'admin_url', 'admin/Nav/edit', '', '编辑导航', '');
INSERT INTO `cmf_auth_rule` VALUES ('31', '1', 'admin', 'admin_url', 'admin/Nav/editPost', '', '编辑导航提交保存', '');
INSERT INTO `cmf_auth_rule` VALUES ('32', '1', 'admin', 'admin_url', 'admin/Nav/delete', '', '删除导航', '');
INSERT INTO `cmf_auth_rule` VALUES ('33', '1', 'admin', 'admin_url', 'admin/NavMenu/index', '', '导航菜单', '');
INSERT INTO `cmf_auth_rule` VALUES ('34', '1', 'admin', 'admin_url', 'admin/NavMenu/add', '', '添加导航菜单', '');
INSERT INTO `cmf_auth_rule` VALUES ('35', '1', 'admin', 'admin_url', 'admin/NavMenu/addPost', '', '添加导航菜单提交保存', '');
INSERT INTO `cmf_auth_rule` VALUES ('36', '1', 'admin', 'admin_url', 'admin/NavMenu/edit', '', '编辑导航菜单', '');
INSERT INTO `cmf_auth_rule` VALUES ('37', '1', 'admin', 'admin_url', 'admin/NavMenu/editPost', '', '编辑导航菜单提交保存', '');
INSERT INTO `cmf_auth_rule` VALUES ('38', '1', 'admin', 'admin_url', 'admin/NavMenu/delete', '', '删除导航菜单', '');
INSERT INTO `cmf_auth_rule` VALUES ('39', '1', 'admin', 'admin_url', 'admin/NavMenu/listOrder', '', '导航菜单排序', '');
INSERT INTO `cmf_auth_rule` VALUES ('40', '1', 'admin', 'admin_url', 'admin/Plugin/default', '', '插件中心', '');
INSERT INTO `cmf_auth_rule` VALUES ('41', '1', 'admin', 'admin_url', 'admin/Plugin/index', '', '插件列表', '');
INSERT INTO `cmf_auth_rule` VALUES ('42', '1', 'admin', 'admin_url', 'admin/Plugin/toggle', '', '插件启用禁用', '');
INSERT INTO `cmf_auth_rule` VALUES ('43', '1', 'admin', 'admin_url', 'admin/Plugin/setting', '', '插件设置', '');
INSERT INTO `cmf_auth_rule` VALUES ('44', '1', 'admin', 'admin_url', 'admin/Plugin/settingPost', '', '插件设置提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('45', '1', 'admin', 'admin_url', 'admin/Plugin/install', '', '插件安装', '');
INSERT INTO `cmf_auth_rule` VALUES ('46', '1', 'admin', 'admin_url', 'admin/Plugin/update', '', '插件更新', '');
INSERT INTO `cmf_auth_rule` VALUES ('47', '1', 'admin', 'admin_url', 'admin/Plugin/uninstall', '', '卸载插件', '');
INSERT INTO `cmf_auth_rule` VALUES ('48', '1', 'admin', 'admin_url', 'admin/Rbac/index', '', '角色管理', '');
INSERT INTO `cmf_auth_rule` VALUES ('49', '1', 'admin', 'admin_url', 'admin/Rbac/roleAdd', '', '添加角色', '');
INSERT INTO `cmf_auth_rule` VALUES ('50', '1', 'admin', 'admin_url', 'admin/Rbac/roleAddPost', '', '添加角色提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('51', '1', 'admin', 'admin_url', 'admin/Rbac/roleEdit', '', '编辑角色', '');
INSERT INTO `cmf_auth_rule` VALUES ('52', '1', 'admin', 'admin_url', 'admin/Rbac/roleEditPost', '', '编辑角色提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('53', '1', 'admin', 'admin_url', 'admin/Rbac/roleDelete', '', '删除角色', '');
INSERT INTO `cmf_auth_rule` VALUES ('54', '1', 'admin', 'admin_url', 'admin/Rbac/authorize', '', '设置角色权限', '');
INSERT INTO `cmf_auth_rule` VALUES ('55', '1', 'admin', 'admin_url', 'admin/Rbac/authorizePost', '', '角色授权提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('56', '1', 'admin', 'admin_url', 'admin/RecycleBin/index', '', '回收站', '');
INSERT INTO `cmf_auth_rule` VALUES ('57', '1', 'admin', 'admin_url', 'admin/RecycleBin/restore', '', '回收站还原', '');
INSERT INTO `cmf_auth_rule` VALUES ('58', '1', 'admin', 'admin_url', 'admin/RecycleBin/delete', '', '回收站彻底删除', '');
INSERT INTO `cmf_auth_rule` VALUES ('59', '1', 'admin', 'admin_url', 'admin/Route/index', '', 'URL美化', '');
INSERT INTO `cmf_auth_rule` VALUES ('60', '1', 'admin', 'admin_url', 'admin/Route/add', '', '添加路由规则', '');
INSERT INTO `cmf_auth_rule` VALUES ('61', '1', 'admin', 'admin_url', 'admin/Route/addPost', '', '添加路由规则提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('62', '1', 'admin', 'admin_url', 'admin/Route/edit', '', '路由规则编辑', '');
INSERT INTO `cmf_auth_rule` VALUES ('63', '1', 'admin', 'admin_url', 'admin/Route/editPost', '', '路由规则编辑提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('64', '1', 'admin', 'admin_url', 'admin/Route/delete', '', '路由规则删除', '');
INSERT INTO `cmf_auth_rule` VALUES ('65', '1', 'admin', 'admin_url', 'admin/Route/ban', '', '路由规则禁用', '');
INSERT INTO `cmf_auth_rule` VALUES ('66', '1', 'admin', 'admin_url', 'admin/Route/open', '', '路由规则启用', '');
INSERT INTO `cmf_auth_rule` VALUES ('67', '1', 'admin', 'admin_url', 'admin/Route/listOrder', '', '路由规则排序', '');
INSERT INTO `cmf_auth_rule` VALUES ('68', '1', 'admin', 'admin_url', 'admin/Route/select', '', '选择URL', '');
INSERT INTO `cmf_auth_rule` VALUES ('69', '1', 'admin', 'admin_url', 'admin/Setting/default', '', '设置', '');
INSERT INTO `cmf_auth_rule` VALUES ('70', '1', 'admin', 'admin_url', 'admin/Setting/site', '', '网站信息', '');
INSERT INTO `cmf_auth_rule` VALUES ('71', '1', 'admin', 'admin_url', 'admin/Setting/sitePost', '', '网站信息设置提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('72', '1', 'admin', 'admin_url', 'admin/Setting/password', '', '密码修改', '');
INSERT INTO `cmf_auth_rule` VALUES ('73', '1', 'admin', 'admin_url', 'admin/Setting/passwordPost', '', '密码修改提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('74', '1', 'admin', 'admin_url', 'admin/Setting/upload', '', '上传设置', '');
INSERT INTO `cmf_auth_rule` VALUES ('75', '1', 'admin', 'admin_url', 'admin/Setting/uploadPost', '', '上传设置提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('76', '1', 'admin', 'admin_url', 'admin/Setting/clearCache', '', '清除缓存', '');
INSERT INTO `cmf_auth_rule` VALUES ('77', '1', 'admin', 'admin_url', 'admin/Slide/index', '', '幻灯片管理', '');
INSERT INTO `cmf_auth_rule` VALUES ('78', '1', 'admin', 'admin_url', 'admin/Slide/add', '', '添加幻灯片', '');
INSERT INTO `cmf_auth_rule` VALUES ('79', '1', 'admin', 'admin_url', 'admin/Slide/addPost', '', '添加幻灯片提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('80', '1', 'admin', 'admin_url', 'admin/Slide/edit', '', '编辑幻灯片', '');
INSERT INTO `cmf_auth_rule` VALUES ('81', '1', 'admin', 'admin_url', 'admin/Slide/editPost', '', '编辑幻灯片提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('82', '1', 'admin', 'admin_url', 'admin/Slide/delete', '', '删除幻灯片', '');
INSERT INTO `cmf_auth_rule` VALUES ('83', '1', 'admin', 'admin_url', 'admin/SlideItem/index', '', '幻灯片页面列表', '');
INSERT INTO `cmf_auth_rule` VALUES ('84', '1', 'admin', 'admin_url', 'admin/SlideItem/add', '', '幻灯片页面添加', '');
INSERT INTO `cmf_auth_rule` VALUES ('85', '1', 'admin', 'admin_url', 'admin/SlideItem/addPost', '', '幻灯片页面添加提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('86', '1', 'admin', 'admin_url', 'admin/SlideItem/edit', '', '幻灯片页面编辑', '');
INSERT INTO `cmf_auth_rule` VALUES ('87', '1', 'admin', 'admin_url', 'admin/SlideItem/editPost', '', '幻灯片页面编辑提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('88', '1', 'admin', 'admin_url', 'admin/SlideItem/delete', '', '幻灯片页面删除', '');
INSERT INTO `cmf_auth_rule` VALUES ('89', '1', 'admin', 'admin_url', 'admin/SlideItem/ban', '', '幻灯片页面隐藏', '');
INSERT INTO `cmf_auth_rule` VALUES ('90', '1', 'admin', 'admin_url', 'admin/SlideItem/cancelBan', '', '幻灯片页面显示', '');
INSERT INTO `cmf_auth_rule` VALUES ('91', '1', 'admin', 'admin_url', 'admin/SlideItem/listOrder', '', '幻灯片页面排序', '');
INSERT INTO `cmf_auth_rule` VALUES ('92', '1', 'admin', 'admin_url', 'admin/Storage/index', '', '文件存储', '');
INSERT INTO `cmf_auth_rule` VALUES ('93', '1', 'admin', 'admin_url', 'admin/Storage/settingPost', '', '文件存储设置提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('94', '1', 'admin', 'admin_url', 'admin/Theme/index', '', '模板管理', '');
INSERT INTO `cmf_auth_rule` VALUES ('95', '1', 'admin', 'admin_url', 'admin/Theme/install', '', '安装模板', '');
INSERT INTO `cmf_auth_rule` VALUES ('96', '1', 'admin', 'admin_url', 'admin/Theme/uninstall', '', '卸载模板', '');
INSERT INTO `cmf_auth_rule` VALUES ('97', '1', 'admin', 'admin_url', 'admin/Theme/installTheme', '', '模板安装', '');
INSERT INTO `cmf_auth_rule` VALUES ('98', '1', 'admin', 'admin_url', 'admin/Theme/update', '', '模板更新', '');
INSERT INTO `cmf_auth_rule` VALUES ('99', '1', 'admin', 'admin_url', 'admin/Theme/active', '', '启用模板', '');
INSERT INTO `cmf_auth_rule` VALUES ('100', '1', 'admin', 'admin_url', 'admin/Theme/files', '', '模板文件列表', '');
INSERT INTO `cmf_auth_rule` VALUES ('101', '1', 'admin', 'admin_url', 'admin/Theme/fileSetting', '', '模板文件设置', '');
INSERT INTO `cmf_auth_rule` VALUES ('102', '1', 'admin', 'admin_url', 'admin/Theme/fileArrayData', '', '模板文件数组数据列表', '');
INSERT INTO `cmf_auth_rule` VALUES ('103', '1', 'admin', 'admin_url', 'admin/Theme/fileArrayDataEdit', '', '模板文件数组数据添加编辑', '');
INSERT INTO `cmf_auth_rule` VALUES ('104', '1', 'admin', 'admin_url', 'admin/Theme/fileArrayDataEditPost', '', '模板文件数组数据添加编辑提交保存', '');
INSERT INTO `cmf_auth_rule` VALUES ('105', '1', 'admin', 'admin_url', 'admin/Theme/fileArrayDataDelete', '', '模板文件数组数据删除', '');
INSERT INTO `cmf_auth_rule` VALUES ('106', '1', 'admin', 'admin_url', 'admin/Theme/settingPost', '', '模板文件编辑提交保存', '');
INSERT INTO `cmf_auth_rule` VALUES ('107', '1', 'admin', 'admin_url', 'admin/Theme/dataSource', '', '模板文件设置数据源', '');
INSERT INTO `cmf_auth_rule` VALUES ('108', '1', 'admin', 'admin_url', 'admin/Theme/design', '', '模板设计', '');
INSERT INTO `cmf_auth_rule` VALUES ('109', '1', 'admin', 'admin_url', 'admin/User/default', '', '管理组', '');
INSERT INTO `cmf_auth_rule` VALUES ('110', '1', 'admin', 'admin_url', 'admin/User/index', '', '管理员', '');
INSERT INTO `cmf_auth_rule` VALUES ('111', '1', 'admin', 'admin_url', 'admin/User/add', '', '管理员添加', '');
INSERT INTO `cmf_auth_rule` VALUES ('112', '1', 'admin', 'admin_url', 'admin/User/addPost', '', '管理员添加提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('113', '1', 'admin', 'admin_url', 'admin/User/edit', '', '管理员编辑', '');
INSERT INTO `cmf_auth_rule` VALUES ('114', '1', 'admin', 'admin_url', 'admin/User/editPost', '', '管理员编辑提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('115', '1', 'admin', 'admin_url', 'admin/User/userInfo', '', '个人信息', '');
INSERT INTO `cmf_auth_rule` VALUES ('116', '1', 'admin', 'admin_url', 'admin/User/userInfoPost', '', '管理员个人信息修改提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('117', '1', 'admin', 'admin_url', 'admin/User/delete', '', '管理员删除', '');
INSERT INTO `cmf_auth_rule` VALUES ('118', '1', 'admin', 'admin_url', 'admin/User/ban', '', '停用管理员', '');
INSERT INTO `cmf_auth_rule` VALUES ('119', '1', 'admin', 'admin_url', 'admin/User/cancelBan', '', '启用管理员', '');
INSERT INTO `cmf_auth_rule` VALUES ('120', '1', 'portal', 'admin_url', 'portal/AdminArticle/index', '', '文章管理', '');
INSERT INTO `cmf_auth_rule` VALUES ('121', '1', 'portal', 'admin_url', 'portal/AdminArticle/add', '', '添加文章', '');
INSERT INTO `cmf_auth_rule` VALUES ('122', '1', 'portal', 'admin_url', 'portal/AdminArticle/addPost', '', '添加文章提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('123', '1', 'portal', 'admin_url', 'portal/AdminArticle/edit', '', '编辑文章', '');
INSERT INTO `cmf_auth_rule` VALUES ('124', '1', 'portal', 'admin_url', 'portal/AdminArticle/editPost', '', '编辑文章提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('125', '1', 'portal', 'admin_url', 'portal/AdminArticle/delete', '', '文章删除', '');
INSERT INTO `cmf_auth_rule` VALUES ('126', '1', 'portal', 'admin_url', 'portal/AdminArticle/publish', '', '文章发布', '');
INSERT INTO `cmf_auth_rule` VALUES ('127', '1', 'portal', 'admin_url', 'portal/AdminArticle/top', '', '文章置顶', '');
INSERT INTO `cmf_auth_rule` VALUES ('128', '1', 'portal', 'admin_url', 'portal/AdminArticle/recommend', '', '文章推荐', '');
INSERT INTO `cmf_auth_rule` VALUES ('129', '1', 'portal', 'admin_url', 'portal/AdminArticle/listOrder', '', '文章排序', '');
INSERT INTO `cmf_auth_rule` VALUES ('130', '1', 'portal', 'admin_url', 'portal/AdminCategory/index', '', '分类管理', '');
INSERT INTO `cmf_auth_rule` VALUES ('131', '1', 'portal', 'admin_url', 'portal/AdminCategory/add', '', '添加文章分类', '');
INSERT INTO `cmf_auth_rule` VALUES ('132', '1', 'portal', 'admin_url', 'portal/AdminCategory/addPost', '', '添加文章分类提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('133', '1', 'portal', 'admin_url', 'portal/AdminCategory/edit', '', '编辑文章分类', '');
INSERT INTO `cmf_auth_rule` VALUES ('134', '1', 'portal', 'admin_url', 'portal/AdminCategory/editPost', '', '编辑文章分类提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('135', '1', 'portal', 'admin_url', 'portal/AdminCategory/select', '', '文章分类选择对话框', '');
INSERT INTO `cmf_auth_rule` VALUES ('136', '1', 'portal', 'admin_url', 'portal/AdminCategory/listOrder', '', '文章分类排序', '');
INSERT INTO `cmf_auth_rule` VALUES ('137', '1', 'portal', 'admin_url', 'portal/AdminCategory/delete', '', '删除文章分类', '');
INSERT INTO `cmf_auth_rule` VALUES ('138', '1', 'portal', 'admin_url', 'portal/AdminIndex/default', '', '门户管理', '');
INSERT INTO `cmf_auth_rule` VALUES ('139', '1', 'portal', 'admin_url', 'portal/AdminPage/index', '', '页面管理', '');
INSERT INTO `cmf_auth_rule` VALUES ('140', '1', 'portal', 'admin_url', 'portal/AdminPage/add', '', '添加页面', '');
INSERT INTO `cmf_auth_rule` VALUES ('141', '1', 'portal', 'admin_url', 'portal/AdminPage/addPost', '', '添加页面提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('142', '1', 'portal', 'admin_url', 'portal/AdminPage/edit', '', '编辑页面', '');
INSERT INTO `cmf_auth_rule` VALUES ('143', '1', 'portal', 'admin_url', 'portal/AdminPage/editPost', '', '编辑页面提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('144', '1', 'portal', 'admin_url', 'portal/AdminPage/delete', '', '删除页面', '');
INSERT INTO `cmf_auth_rule` VALUES ('145', '1', 'portal', 'admin_url', 'portal/AdminTag/index', '', '文章标签', '');
INSERT INTO `cmf_auth_rule` VALUES ('146', '1', 'portal', 'admin_url', 'portal/AdminTag/add', '', '添加文章标签', '');
INSERT INTO `cmf_auth_rule` VALUES ('147', '1', 'portal', 'admin_url', 'portal/AdminTag/addPost', '', '添加文章标签提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('148', '1', 'portal', 'admin_url', 'portal/AdminTag/upStatus', '', '更新标签状态', '');
INSERT INTO `cmf_auth_rule` VALUES ('149', '1', 'portal', 'admin_url', 'portal/AdminTag/delete', '', '删除文章标签', '');
INSERT INTO `cmf_auth_rule` VALUES ('150', '1', 'user', 'admin_url', 'user/AdminAsset/index', '', '资源管理', '');
INSERT INTO `cmf_auth_rule` VALUES ('151', '1', 'user', 'admin_url', 'user/AdminAsset/delete', '', '删除文件', '');
INSERT INTO `cmf_auth_rule` VALUES ('152', '1', 'user', 'admin_url', 'user/AdminIndex/default', '', '用户管理', '');
INSERT INTO `cmf_auth_rule` VALUES ('153', '1', 'user', 'admin_url', 'user/AdminIndex/default1', '', '用户组', '');
INSERT INTO `cmf_auth_rule` VALUES ('154', '1', 'user', 'admin_url', 'user/AdminIndex/index', '', '本站用户', '');
INSERT INTO `cmf_auth_rule` VALUES ('155', '1', 'user', 'admin_url', 'user/AdminIndex/ban', '', '本站用户拉黑', '');
INSERT INTO `cmf_auth_rule` VALUES ('156', '1', 'user', 'admin_url', 'user/AdminIndex/cancelBan', '', '本站用户启用', '');
INSERT INTO `cmf_auth_rule` VALUES ('157', '1', 'user', 'admin_url', 'user/AdminOauth/index', '', '第三方用户', '');
INSERT INTO `cmf_auth_rule` VALUES ('158', '1', 'user', 'admin_url', 'user/AdminOauth/delete', '', '删除第三方用户绑定', '');
INSERT INTO `cmf_auth_rule` VALUES ('159', '1', 'user', 'admin_url', 'user/AdminUserAction/index', '', '用户操作管理', '');
INSERT INTO `cmf_auth_rule` VALUES ('160', '1', 'user', 'admin_url', 'user/AdminUserAction/edit', '', '编辑用户操作', '');
INSERT INTO `cmf_auth_rule` VALUES ('161', '1', 'user', 'admin_url', 'user/AdminUserAction/editPost', '', '编辑用户操作提交', '');
INSERT INTO `cmf_auth_rule` VALUES ('162', '1', 'user', 'admin_url', 'user/AdminUserAction/sync', '', '同步用户操作', '');
INSERT INTO `cmf_auth_rule` VALUES ('163', '1', 'user', 'admin_url', 'user/AdminIndex/audit_list', '', '实名认证审核', '');
INSERT INTO `cmf_auth_rule` VALUES ('164', '1', 'admin', 'admin_url', 'admin/Score/index', '', '平台管理', '');
INSERT INTO `cmf_auth_rule` VALUES ('165', '1', 'admin', 'admin_url', 'admin/Score/score_change', '', '兑换管理', '');
INSERT INTO `cmf_auth_rule` VALUES ('166', '1', 'admin', 'admin_url', 'admin/Setting/parameter', '', '参数设置', '');
INSERT INTO `cmf_auth_rule` VALUES ('167', '1', 'device', 'admin_url', 'device/AdminIndex/index', '', '设备管理', '');
INSERT INTO `cmf_auth_rule` VALUES ('168', '1', 'device', 'admin_url', 'device/AdminIndex/audit', '', '设备审核', '');
INSERT INTO `cmf_auth_rule` VALUES ('169', '1', 'admin', 'admin_url', 'admin/History/index', '', '历史审核', '');

-- ----------------------------
-- Table structure for cmf_comment
-- ----------------------------
DROP TABLE IF EXISTS `cmf_comment`;
CREATE TABLE `cmf_comment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '被回复的评论id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发表评论的用户id',
  `to_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '被评论的用户id',
  `object_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论内容 id',
  `like_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `dislike_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '不喜欢数',
  `floor` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '楼层数',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论时间',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:已审核,0:未审核',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '评论类型；1实名评论',
  `table_name` varchar(64) NOT NULL DEFAULT '' COMMENT '评论内容所在表，不带表前缀',
  `full_name` varchar(50) NOT NULL DEFAULT '' COMMENT '评论者昵称',
  `email` varchar(255) NOT NULL DEFAULT '' COMMENT '评论者邮箱',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '层级关系',
  `url` text COMMENT '原文地址',
  `content` text CHARACTER SET utf8mb4 COMMENT '评论内容',
  `more` text CHARACTER SET utf8mb4 COMMENT '扩展属性',
  PRIMARY KEY (`id`),
  KEY `table_id_status` (`table_name`,`object_id`,`status`),
  KEY `object_id` (`object_id`) USING BTREE,
  KEY `status` (`status`) USING BTREE,
  KEY `parent_id` (`parent_id`) USING BTREE,
  KEY `create_time` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='评论表';

-- ----------------------------
-- Records of cmf_comment
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_device
-- ----------------------------
DROP TABLE IF EXISTS `cmf_device`;
CREATE TABLE `cmf_device` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL COMMENT '设备名称。',
  `device_id` varchar(50) NOT NULL COMMENT '设备ID',
  `device_sn` varchar(50) DEFAULT NULL COMMENT '设备序列号',
  `product_id` varchar(50) DEFAULT NULL,
  `create_time` int(11) DEFAULT '0' COMMENT '添加时间',
  `is_bind` int(2) DEFAULT '0' COMMENT '是否已被绑定。0否1是',
  `is_del` int(2) DEFAULT '0' COMMENT '是否删除。0否1是',
  `site_id` int(10) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_device
-- ----------------------------
INSERT INTO `cmf_device` VALUES ('3', '测试设备1', '765189220', '0864797033019551', '160fa2b3062403e9160fa2b306248601', '1527736829', '0', '0', '1');
INSERT INTO `cmf_device` VALUES ('7', 'XXXZ设备', '324234', '088730182', '160fa2b3062403e9160fa2b306248601', '1527736829', '0', '0', '1');
INSERT INTO `cmf_device` VALUES ('8', '测', '123454352', '576547456', null, '1527736829', '0', '1', '1');
INSERT INTO `cmf_device` VALUES ('9', 'test', '860705501', '0865886036543318', '160fa2b3062403e9160fa2b306248601', '1527738827', '0', '0', '1');
INSERT INTO `cmf_device` VALUES ('10', 'test测试设备', '86023213', 'asdas2312312', null, '1539251581', '0', '1', '1');
INSERT INTO `cmf_device` VALUES ('11', '测试设备啊啊啊', '2321312312', 'sadasd32131asdsa', null, '1539255225', '0', '1', '1');

-- ----------------------------
-- Table structure for cmf_device_action_log
-- ----------------------------
DROP TABLE IF EXISTS `cmf_device_action_log`;
CREATE TABLE `cmf_device_action_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `device_id` varchar(50) NOT NULL,
  `action_user_id` bigint(20) NOT NULL COMMENT '操作者ID。如是用户本人则是他自己，如是后台管理人员删除则是后台管理人员',
  `type` int(2) NOT NULL COMMENT '操作类型。1绑定、2解绑、3删除',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `user_type` int(10) NOT NULL COMMENT '1是admin 2是会员',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4087 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_device_action_log
-- ----------------------------
INSERT INTO `cmf_device_action_log` VALUES ('4080', '763326884', '2716', '1', '1532403718', '0');
INSERT INTO `cmf_device_action_log` VALUES ('4081', '962786563', '2716', '1', '1532403749', '0');
INSERT INTO `cmf_device_action_log` VALUES ('4082', '601319401', '2716', '1', '1532403767', '0');
INSERT INTO `cmf_device_action_log` VALUES ('4083', '763325462', '2716', '1', '1532403849', '0');
INSERT INTO `cmf_device_action_log` VALUES ('4084', '86023213', '1', '3', '1539252147', '0');
INSERT INTO `cmf_device_action_log` VALUES ('4086', '2321312312', '1', '3', '1539312792', '0');

-- ----------------------------
-- Table structure for cmf_device_receive_log
-- ----------------------------
DROP TABLE IF EXISTS `cmf_device_receive_log`;
CREATE TABLE `cmf_device_receive_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `device_id` varchar(20) DEFAULT NULL,
  `param` text,
  `time` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_device_receive_log
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_device_user_rel
-- ----------------------------
DROP TABLE IF EXISTS `cmf_device_user_rel`;
CREATE TABLE `cmf_device_user_rel` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `device_id` varchar(50) NOT NULL,
  `user_id` bigint(20) NOT NULL COMMENT '积分ID',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '绑定状态。0无效，1有效、2待审核',
  `user_referrer_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '推介关系表ID，第一次绑定并填写推介人才有',
  `audit_user_id` bigint(20) DEFAULT '0' COMMENT '审核人。',
  `update_time` int(11) DEFAULT NULL COMMENT '审核更新时间',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '注册时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_device_user_rel
-- ----------------------------
INSERT INTO `cmf_device_user_rel` VALUES ('26', '765189220', '3', '1', '0', '1', '1540345110', '1527663813');
INSERT INTO `cmf_device_user_rel` VALUES ('27', '860705501', '4', '2', '0', '1', '1539326149', '1527736952');

-- ----------------------------
-- Table structure for cmf_device_use_log
-- ----------------------------
DROP TABLE IF EXISTS `cmf_device_use_log`;
CREATE TABLE `cmf_device_use_log` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `package_num` int(5) NOT NULL DEFAULT '1' COMMENT '药包数量',
  `device_id` varchar(50) NOT NULL,
  `user_id` bigint(20) NOT NULL COMMENT '用户ID',
  `score_log_id` bigint(20) NOT NULL COMMENT '积分ID',
  `start_time` int(11) NOT NULL DEFAULT '0' COMMENT '设备传回开始时间',
  `end_time` int(11) DEFAULT '0' COMMENT '设备传回结束时间',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '注册时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_device_use_log
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_device_use_package
-- ----------------------------
DROP TABLE IF EXISTS `cmf_device_use_package`;
CREATE TABLE `cmf_device_use_package` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `num` int(10) NOT NULL COMMENT '数量',
  `user_id` bigint(20) DEFAULT NULL COMMENT '用户ID',
  `device_id` varchar(20) DEFAULT NULL COMMENT '设备ID',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_device_use_package
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_device_use_tmp
-- ----------------------------
DROP TABLE IF EXISTS `cmf_device_use_tmp`;
CREATE TABLE `cmf_device_use_tmp` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `device_id` int(10) NOT NULL,
  `strart_time` int(11) NOT NULL DEFAULT '0' COMMENT '业务逻辑开始时间',
  `end_time` int(11) NOT NULL DEFAULT '0' COMMENT '业务逻辑结束时间',
  `over_time` int(11) DEFAULT NULL COMMENT '完毕时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_device_use_tmp
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_hook
-- ----------------------------
DROP TABLE IF EXISTS `cmf_hook`;
CREATE TABLE `cmf_hook` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '钩子类型(1:系统钩子;2:应用钩子;3:模板钩子;4:后台模板钩子)',
  `once` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否只允许一个插件运行(0:多个;1:一个)',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '钩子名称',
  `hook` varchar(50) NOT NULL DEFAULT '' COMMENT '钩子',
  `app` varchar(15) NOT NULL DEFAULT '' COMMENT '应用名(只有应用钩子才用)',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8mb4 COMMENT='系统钩子表';

-- ----------------------------
-- Records of cmf_hook
-- ----------------------------
INSERT INTO `cmf_hook` VALUES ('1', '1', '0', '应用初始化', 'app_init', 'cmf', '应用初始化');
INSERT INTO `cmf_hook` VALUES ('2', '1', '0', '应用开始', 'app_begin', 'cmf', '应用开始');
INSERT INTO `cmf_hook` VALUES ('3', '1', '0', '模块初始化', 'module_init', 'cmf', '模块初始化');
INSERT INTO `cmf_hook` VALUES ('4', '1', '0', '控制器开始', 'action_begin', 'cmf', '控制器开始');
INSERT INTO `cmf_hook` VALUES ('5', '1', '0', '视图输出过滤', 'view_filter', 'cmf', '视图输出过滤');
INSERT INTO `cmf_hook` VALUES ('6', '1', '0', '应用结束', 'app_end', 'cmf', '应用结束');
INSERT INTO `cmf_hook` VALUES ('7', '1', '0', '日志write方法', 'log_write', 'cmf', '日志write方法');
INSERT INTO `cmf_hook` VALUES ('8', '1', '0', '输出结束', 'response_end', 'cmf', '输出结束');
INSERT INTO `cmf_hook` VALUES ('9', '1', '0', '后台控制器初始化', 'admin_init', 'cmf', '后台控制器初始化');
INSERT INTO `cmf_hook` VALUES ('10', '1', '0', '前台控制器初始化', 'home_init', 'cmf', '前台控制器初始化');
INSERT INTO `cmf_hook` VALUES ('11', '1', '1', '发送手机验证码', 'send_mobile_verification_code', 'cmf', '发送手机验证码');
INSERT INTO `cmf_hook` VALUES ('12', '3', '0', '模板 body标签开始', 'body_start', '', '模板 body标签开始');
INSERT INTO `cmf_hook` VALUES ('13', '3', '0', '模板 head标签结束前', 'before_head_end', '', '模板 head标签结束前');
INSERT INTO `cmf_hook` VALUES ('14', '3', '0', '模板底部开始', 'footer_start', '', '模板底部开始');
INSERT INTO `cmf_hook` VALUES ('15', '3', '0', '模板底部开始之前', 'before_footer', '', '模板底部开始之前');
INSERT INTO `cmf_hook` VALUES ('16', '3', '0', '模板底部结束之前', 'before_footer_end', '', '模板底部结束之前');
INSERT INTO `cmf_hook` VALUES ('17', '3', '0', '模板 body 标签结束之前', 'before_body_end', '', '模板 body 标签结束之前');
INSERT INTO `cmf_hook` VALUES ('18', '3', '0', '模板左边栏开始', 'left_sidebar_start', '', '模板左边栏开始');
INSERT INTO `cmf_hook` VALUES ('19', '3', '0', '模板左边栏结束之前', 'before_left_sidebar_end', '', '模板左边栏结束之前');
INSERT INTO `cmf_hook` VALUES ('20', '3', '0', '模板右边栏开始', 'right_sidebar_start', '', '模板右边栏开始');
INSERT INTO `cmf_hook` VALUES ('21', '3', '0', '模板右边栏结束之前', 'before_right_sidebar_end', '', '模板右边栏结束之前');
INSERT INTO `cmf_hook` VALUES ('22', '3', '1', '评论区', 'comment', '', '评论区');
INSERT INTO `cmf_hook` VALUES ('23', '3', '1', '留言区', 'guestbook', '', '留言区');
INSERT INTO `cmf_hook` VALUES ('24', '2', '0', '后台首页仪表盘', 'admin_dashboard', 'admin', '后台首页仪表盘');
INSERT INTO `cmf_hook` VALUES ('25', '4', '0', '后台模板 head标签结束前', 'admin_before_head_end', '', '后台模板 head标签结束前');
INSERT INTO `cmf_hook` VALUES ('26', '4', '0', '后台模板 body 标签结束之前', 'admin_before_body_end', '', '后台模板 body 标签结束之前');
INSERT INTO `cmf_hook` VALUES ('27', '2', '0', '后台登录页面', 'admin_login', 'admin', '后台登录页面');
INSERT INTO `cmf_hook` VALUES ('28', '1', '1', '前台模板切换', 'switch_theme', 'cmf', '前台模板切换');
INSERT INTO `cmf_hook` VALUES ('29', '3', '0', '主要内容之后', 'after_content', '', '主要内容之后');
INSERT INTO `cmf_hook` VALUES ('30', '2', '0', '文章显示之前', 'portal_before_assign_article', 'portal', '文章显示之前');
INSERT INTO `cmf_hook` VALUES ('31', '2', '0', '后台文章保存之后', 'portal_admin_after_save_article', 'portal', '后台文章保存之后');
INSERT INTO `cmf_hook` VALUES ('32', '2', '1', '获取上传界面', 'fetch_upload_view', 'user', '获取上传界面');
INSERT INTO `cmf_hook` VALUES ('33', '3', '0', '主要内容之前', 'before_content', 'cmf', '主要内容之前');
INSERT INTO `cmf_hook` VALUES ('34', '1', '0', '日志写入完成', 'log_write_done', 'cmf', '日志写入完成');
INSERT INTO `cmf_hook` VALUES ('35', '1', '1', '后台模板切换', 'switch_admin_theme', 'cmf', '后台模板切换');
INSERT INTO `cmf_hook` VALUES ('36', '1', '1', '验证码图片', 'captcha_image', 'cmf', '验证码图片');
INSERT INTO `cmf_hook` VALUES ('37', '2', '1', '后台模板设计界面', 'admin_theme_design_view', 'admin', '后台模板设计界面');
INSERT INTO `cmf_hook` VALUES ('38', '2', '1', '后台设置网站信息界面', 'admin_setting_site_view', 'admin', '后台设置网站信息界面');
INSERT INTO `cmf_hook` VALUES ('39', '2', '1', '后台清除缓存界面', 'admin_setting_clear_cache_view', 'admin', '后台清除缓存界面');
INSERT INTO `cmf_hook` VALUES ('40', '2', '1', '后台导航管理界面', 'admin_nav_index_view', 'admin', '后台导航管理界面');
INSERT INTO `cmf_hook` VALUES ('41', '2', '1', '后台友情链接管理界面', 'admin_link_index_view', 'admin', '后台友情链接管理界面');
INSERT INTO `cmf_hook` VALUES ('42', '2', '1', '后台幻灯片管理界面', 'admin_slide_index_view', 'admin', '后台幻灯片管理界面');
INSERT INTO `cmf_hook` VALUES ('43', '2', '1', '后台管理员列表界面', 'admin_user_index_view', 'admin', '后台管理员列表界面');
INSERT INTO `cmf_hook` VALUES ('44', '2', '1', '后台角色管理界面', 'admin_rbac_index_view', 'admin', '后台角色管理界面');
INSERT INTO `cmf_hook` VALUES ('45', '2', '1', '门户后台文章管理列表界面', 'portal_admin_article_index_view', 'portal', '门户后台文章管理列表界面');
INSERT INTO `cmf_hook` VALUES ('46', '2', '1', '门户后台文章分类管理列表界面', 'portal_admin_category_index_view', 'portal', '门户后台文章分类管理列表界面');
INSERT INTO `cmf_hook` VALUES ('47', '2', '1', '门户后台页面管理列表界面', 'portal_admin_page_index_view', 'portal', '门户后台页面管理列表界面');
INSERT INTO `cmf_hook` VALUES ('48', '2', '1', '门户后台文章标签管理列表界面', 'portal_admin_tag_index_view', 'portal', '门户后台文章标签管理列表界面');
INSERT INTO `cmf_hook` VALUES ('49', '2', '1', '用户管理本站用户列表界面', 'user_admin_index_view', 'user', '用户管理本站用户列表界面');
INSERT INTO `cmf_hook` VALUES ('50', '2', '1', '资源管理列表界面', 'user_admin_asset_index_view', 'user', '资源管理列表界面');
INSERT INTO `cmf_hook` VALUES ('51', '2', '1', '用户管理第三方用户列表界面', 'user_admin_oauth_index_view', 'user', '用户管理第三方用户列表界面');
INSERT INTO `cmf_hook` VALUES ('52', '2', '1', '后台首页界面', 'admin_index_index_view', 'admin', '后台首页界面');
INSERT INTO `cmf_hook` VALUES ('53', '2', '1', '后台回收站界面', 'admin_recycle_bin_index_view', 'admin', '后台回收站界面');
INSERT INTO `cmf_hook` VALUES ('54', '2', '1', '后台菜单管理界面', 'admin_menu_index_view', 'admin', '后台菜单管理界面');
INSERT INTO `cmf_hook` VALUES ('55', '2', '1', '后台自定义登录是否开启钩子', 'admin_custom_login_open', 'admin', '后台自定义登录是否开启钩子');
INSERT INTO `cmf_hook` VALUES ('56', '4', '0', '门户后台文章添加编辑界面右侧栏', 'portal_admin_article_edit_view_right_sidebar', 'portal', '门户后台文章添加编辑界面右侧栏');
INSERT INTO `cmf_hook` VALUES ('57', '4', '0', '门户后台文章添加编辑界面主要内容', 'portal_admin_article_edit_view_main', 'portal', '门户后台文章添加编辑界面主要内容');
INSERT INTO `cmf_hook` VALUES ('58', '2', '1', '门户后台文章添加界面', 'portal_admin_article_add_view', 'portal', '门户后台文章添加界面');
INSERT INTO `cmf_hook` VALUES ('59', '2', '1', '门户后台文章编辑界面', 'portal_admin_article_edit_view', 'portal', '门户后台文章编辑界面');
INSERT INTO `cmf_hook` VALUES ('60', '2', '1', '门户后台文章分类添加界面', 'portal_admin_category_add_view', 'portal', '门户后台文章分类添加界面');
INSERT INTO `cmf_hook` VALUES ('61', '2', '1', '门户后台文章分类编辑界面', 'portal_admin_category_edit_view', 'portal', '门户后台文章分类编辑界面');
INSERT INTO `cmf_hook` VALUES ('62', '2', '1', '门户后台页面添加界面', 'portal_admin_page_add_view', 'portal', '门户后台页面添加界面');
INSERT INTO `cmf_hook` VALUES ('63', '2', '1', '门户后台页面编辑界面', 'portal_admin_page_edit_view', 'portal', '门户后台页面编辑界面');
INSERT INTO `cmf_hook` VALUES ('64', '2', '1', '后台幻灯片页面列表界面', 'admin_slide_item_index_view', 'admin', '后台幻灯片页面列表界面');
INSERT INTO `cmf_hook` VALUES ('65', '2', '1', '后台幻灯片页面添加界面', 'admin_slide_item_add_view', 'admin', '后台幻灯片页面添加界面');
INSERT INTO `cmf_hook` VALUES ('66', '2', '1', '后台幻灯片页面编辑界面', 'admin_slide_item_edit_view', 'admin', '后台幻灯片页面编辑界面');
INSERT INTO `cmf_hook` VALUES ('67', '2', '1', '后台管理员添加界面', 'admin_user_add_view', 'admin', '后台管理员添加界面');
INSERT INTO `cmf_hook` VALUES ('68', '2', '1', '后台管理员编辑界面', 'admin_user_edit_view', 'admin', '后台管理员编辑界面');
INSERT INTO `cmf_hook` VALUES ('69', '2', '1', '后台角色添加界面', 'admin_rbac_role_add_view', 'admin', '后台角色添加界面');
INSERT INTO `cmf_hook` VALUES ('70', '2', '1', '后台角色编辑界面', 'admin_rbac_role_edit_view', 'admin', '后台角色编辑界面');
INSERT INTO `cmf_hook` VALUES ('71', '2', '1', '后台角色授权界面', 'admin_rbac_authorize_view', 'admin', '后台角色授权界面');

-- ----------------------------
-- Table structure for cmf_hook_plugin
-- ----------------------------
DROP TABLE IF EXISTS `cmf_hook_plugin`;
CREATE TABLE `cmf_hook_plugin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态(0:禁用,1:启用)',
  `hook` varchar(50) NOT NULL DEFAULT '' COMMENT '钩子名',
  `plugin` varchar(50) NOT NULL DEFAULT '' COMMENT '插件',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统钩子插件表';

-- ----------------------------
-- Records of cmf_hook_plugin
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_link
-- ----------------------------
DROP TABLE IF EXISTS `cmf_link`;
CREATE TABLE `cmf_link` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:显示;0:不显示',
  `rating` int(11) NOT NULL DEFAULT '0' COMMENT '友情链接评级',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '友情链接描述',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '友情链接地址',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '友情链接名称',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT '友情链接图标',
  `target` varchar(10) NOT NULL DEFAULT '' COMMENT '友情链接打开方式',
  `rel` varchar(50) NOT NULL DEFAULT '' COMMENT '链接与网站的关系',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='友情链接表';

-- ----------------------------
-- Records of cmf_link
-- ----------------------------
INSERT INTO `cmf_link` VALUES ('1', '1', '1', '8', 'thinkcmf官网', 'http://www.thinkcmf.com', 'ThinkCMF', '', '_blank', '');

-- ----------------------------
-- Table structure for cmf_message
-- ----------------------------
DROP TABLE IF EXISTS `cmf_message`;
CREATE TABLE `cmf_message` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL COMMENT '消息标题',
  `content` text COMMENT '消息内容',
  `more` text COMMENT '用于打开对象的相关页面以及相关参数的JSON。如{id:1,href:''index.html'',barstyle:''dark''}',
  `type` int(2) NOT NULL DEFAULT '1' COMMENT '消息类型。1系统消息、2积分变化、……',
  `send_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '发送者用户ID，0表示自动系统消息',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_message
-- ----------------------------
INSERT INTO `cmf_message` VALUES ('1', '<font style=\"color:red;\">积分兑换活动，点开查看内容</font>', '积分兑换活动，天天开奖，好礼等你拿积分兑换活动，天天开奖，好礼等你拿积分兑换活动，天天开奖，好礼等你拿积分兑换活动，天天开奖，好礼等你拿', '{\"item\":{\"id\":1},\"href\":\"http:\\/\\/www.baidu.com\",\"barstyle\":\"dark\"}', '1', '0', '1524302366');
INSERT INTO `cmf_message` VALUES ('19', '积分变化', '你的积分于有新变化，变化值：5', null, '2', '0', '1527436814');
INSERT INTO `cmf_message` VALUES ('20', '积分变化', '你的积分于有新变化，变化值：10', null, '2', '0', '1527436814');
INSERT INTO `cmf_message` VALUES ('21', '积分变化', '你的积分于有新变化，变化值：5', null, '2', '0', '1527436814');
INSERT INTO `cmf_message` VALUES ('22', '积分变化', '你的积分于有新变化，变化值：15', null, '2', '0', '1527440517');
INSERT INTO `cmf_message` VALUES ('23', '积分变化', '你的积分于有新变化，变化值：5', null, '2', '0', '1527443844');
INSERT INTO `cmf_message` VALUES ('24', '积分变化', '你的积分于有新变化，变化值：10', null, '2', '0', '1527443844');
INSERT INTO `cmf_message` VALUES ('25', '积分变化', '你的积分于有新变化，变化值：5', null, '2', '0', '1527443844');
INSERT INTO `cmf_message` VALUES ('26', '积分变化', '你的积分于有新变化，变化值：5', null, '2', '0', '1527443844');
INSERT INTO `cmf_message` VALUES ('27', '积分变化', '你的积分于有新变化，变化值：-12', null, '2', '0', '1527575066');
INSERT INTO `cmf_message` VALUES ('28', '积分变化', '你的积分于有新变化，变化值：100', null, '2', '0', '1527699359');
INSERT INTO `cmf_message` VALUES ('29', '积分变化', '你的积分于有新变化，变化值：100', null, '2', '0', '1527702392');
INSERT INTO `cmf_message` VALUES ('30', '积分变化', '你的积分于有新变化，变化值：-1', null, '2', '0', '1527705840');
INSERT INTO `cmf_message` VALUES ('31', '积分变化', '你的积分于有新变化，变化值：100', null, '2', '0', '1527711241');
INSERT INTO `cmf_message` VALUES ('32', '积分变化', '你的积分于有新变化，变化值：100', null, '2', '0', '1527711379');
INSERT INTO `cmf_message` VALUES ('33', '积分变化', '你的积分于有新变化，变化值：100', null, '2', '0', '1527711820');
INSERT INTO `cmf_message` VALUES ('34', '积分变化', '你的积分于有新变化，变化值：-1', null, '2', '0', '1527711882');
INSERT INTO `cmf_message` VALUES ('35', '设备绑定', '你的新设备‘测试设备1’绑定审核成功', null, '3', '0', '1539250524');

-- ----------------------------
-- Table structure for cmf_message_user_rel
-- ----------------------------
DROP TABLE IF EXISTS `cmf_message_user_rel`;
CREATE TABLE `cmf_message_user_rel` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '发送者用户ID，0表示系统消息',
  `message_id` bigint(20) NOT NULL COMMENT '关联的消息ID',
  `read_time` int(11) DEFAULT '0' COMMENT '阅读时间',
  `is_del` int(2) DEFAULT '0' COMMENT '是否删除。0否1是',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_message_user_rel
-- ----------------------------
INSERT INTO `cmf_message_user_rel` VALUES ('1', '7', '1', '0', '0');
INSERT INTO `cmf_message_user_rel` VALUES ('19', '10', '19', '0', '0');
INSERT INTO `cmf_message_user_rel` VALUES ('20', '9', '20', '0', '0');
INSERT INTO `cmf_message_user_rel` VALUES ('21', '7', '21', '0', '0');
INSERT INTO `cmf_message_user_rel` VALUES ('22', '9', '22', '0', '0');
INSERT INTO `cmf_message_user_rel` VALUES ('23', '10', '23', '0', '0');
INSERT INTO `cmf_message_user_rel` VALUES ('24', '9', '24', '0', '0');
INSERT INTO `cmf_message_user_rel` VALUES ('25', '7', '25', '0', '0');
INSERT INTO `cmf_message_user_rel` VALUES ('26', '8', '26', '0', '0');
INSERT INTO `cmf_message_user_rel` VALUES ('27', '11', '27', '0', '0');
INSERT INTO `cmf_message_user_rel` VALUES ('28', '7', '28', '0', '0');
INSERT INTO `cmf_message_user_rel` VALUES ('29', '7', '29', '0', '0');
INSERT INTO `cmf_message_user_rel` VALUES ('30', '7', '30', '0', '0');
INSERT INTO `cmf_message_user_rel` VALUES ('31', '7', '31', '0', '0');
INSERT INTO `cmf_message_user_rel` VALUES ('32', '7', '32', '0', '0');
INSERT INTO `cmf_message_user_rel` VALUES ('33', '7', '33', '0', '0');
INSERT INTO `cmf_message_user_rel` VALUES ('34', '7', '34', '0', '0');
INSERT INTO `cmf_message_user_rel` VALUES ('35', '7', '35', '0', '0');

-- ----------------------------
-- Table structure for cmf_name_audit
-- ----------------------------
DROP TABLE IF EXISTS `cmf_name_audit`;
CREATE TABLE `cmf_name_audit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `real_name` varchar(64) NOT NULL COMMENT '真是姓名',
  `id_card` varchar(64) NOT NULL COMMENT '身份证号',
  `sfz_front_img` varchar(255) DEFAULT NULL COMMENT '身份证正面照',
  `sfz_back_img` varchar(255) DEFAULT NULL COMMENT '身份证背面照',
  `sfz_sc_img` varchar(255) DEFAULT NULL COMMENT '手持身份证',
  `add_time` varchar(64) NOT NULL COMMENT '提交时间',
  `status` smallint(1) NOT NULL DEFAULT '0' COMMENT '0待审核1通过2拒绝',
  `remark` varchar(64) NOT NULL COMMENT '备注',
  `examine_time` varchar(64) NOT NULL COMMENT '审核时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_name_audit
-- ----------------------------
INSERT INTO `cmf_name_audit` VALUES ('1', '3', 'test', '213123', '', '', '', '1538097092', '0', '', '');
INSERT INTO `cmf_name_audit` VALUES ('2', '2', 'czxc', '524524', '/upload/img/22.png', '', '/upload/img/22.png', '1538097092', '1', '11', '1539078783');
INSERT INTO `cmf_name_audit` VALUES ('7', '6', '小林', '123456789123456789', 'http://b478dq.natappfree.cc/upload/sfz_img/20181025101002_8413.jpeg', 'http://b478dq.natappfree.cc/upload/sfz_img/20181025101002_3879.jpeg', 'http://b478dq.natappfree.cc/upload/sfz_img/20181025101002_9952.jpeg', '1540433402', '0', '', '');

-- ----------------------------
-- Table structure for cmf_nav
-- ----------------------------
DROP TABLE IF EXISTS `cmf_nav`;
CREATE TABLE `cmf_nav` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_main` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否为主导航;1:是;0:不是',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '导航位置名称',
  `remark` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='前台导航位置表';

-- ----------------------------
-- Records of cmf_nav
-- ----------------------------
INSERT INTO `cmf_nav` VALUES ('1', '1', '主导航', '主导航');
INSERT INTO `cmf_nav` VALUES ('2', '0', '底部导航', '');

-- ----------------------------
-- Table structure for cmf_nav_menu
-- ----------------------------
DROP TABLE IF EXISTS `cmf_nav_menu`;
CREATE TABLE `cmf_nav_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nav_id` int(11) NOT NULL COMMENT '导航 id',
  `parent_id` int(11) NOT NULL COMMENT '父 id',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:显示;0:隐藏',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单名称',
  `target` varchar(10) NOT NULL DEFAULT '' COMMENT '打开方式',
  `href` varchar(100) NOT NULL DEFAULT '' COMMENT '链接',
  `icon` varchar(20) NOT NULL DEFAULT '' COMMENT '图标',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '层级关系',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='前台导航菜单表';

-- ----------------------------
-- Records of cmf_nav_menu
-- ----------------------------
INSERT INTO `cmf_nav_menu` VALUES ('1', '1', '0', '1', '0', '首页', '', 'home', '', '0-1');

-- ----------------------------
-- Table structure for cmf_option
-- ----------------------------
DROP TABLE IF EXISTS `cmf_option`;
CREATE TABLE `cmf_option` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `autoload` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '是否自动加载;1:自动加载;0:不自动加载',
  `option_name` varchar(64) NOT NULL DEFAULT '' COMMENT '配置名',
  `option_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '配置值',
  `site_id` smallint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `option_name` (`option_name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='全站配置表';

-- ----------------------------
-- Records of cmf_option
-- ----------------------------
INSERT INTO `cmf_option` VALUES ('1', '1', 'site_info', '{\"site_name\":\"ThinkCMF\\u5185\\u5bb9\\u7ba1\\u7406\\u6846\\u67b6\",\"site_seo_title\":\"ThinkCMF\\u5185\\u5bb9\\u7ba1\\u7406\\u6846\\u67b6\",\"site_seo_keywords\":\"ThinkCMF,php,\\u5185\\u5bb9\\u7ba1\\u7406\\u6846\\u67b6,cmf,cms,\\u7b80\\u7ea6\\u98ce, simplewind,framework\",\"site_seo_description\":\"ThinkCMF\\u662f\\u7b80\\u7ea6\\u98ce\\u7f51\\u7edc\\u79d1\\u6280\\u53d1\\u5e03\\u7684\\u4e00\\u6b3e\\u7528\\u4e8e\\u5feb\\u901f\\u5f00\\u53d1\\u7684\\u5185\\u5bb9\\u7ba1\\u7406\\u6846\\u67b6\",\"site_icp\":\"\",\"site_gwa\":\"\",\"site_admin_email\":\"\",\"site_analytics\":\"\"}', '1');
INSERT INTO `cmf_option` VALUES ('4', '1', 'cmf_settings', '{\"open_registration\":\"0\",\"banned_usernames\":\"\"}', '1');
INSERT INTO `cmf_option` VALUES ('5', '1', 'cdn_settings', '{\"cdn_static_root\":\"\"}', '1');
INSERT INTO `cmf_option` VALUES ('6', '1', 'admin_settings', '{\"admin_password\":\"\",\"admin_style\":\"flatadmin\"}', '1');
INSERT INTO `cmf_option` VALUES ('7', '1', 'parameter_settings', '{\"admin_phone\":\"15757851183\",\"admin_phone_code\":\"\",\"exchange_lower_limit\":\"1000\",\"exchange_fee\":\"0\",\"wn_stepgoal\":\"3000\",\"wn_upgrade_days\":\"6\",\"wn_downgrade_days\":\"5\",\"wn_jb_score\":\"10\",\"wn_add_score\":\"5\",\"tz_stepgoal\":\"10000\",\"tz_upgrade_days\":\"6\",\"tz_downgrade_days\":\"5\",\"tz_jb_score\":\"10\",\"tz_add_score\":\"8\",\"lb_stepgoal\":\"30000\",\"lb_upgrade_days\":\"6\",\"lb_downgrade_days\":\"5\",\"lb_jb_score\":\"10\",\"lb_add_score\":\"9\",\"system_version\":\"1.0.0\",\"app_down_url\":\"http:\\/\\/down.zmiaosh.com\\/xunzhengyi\\/zmxz103.apk\",\"range_days\":\"7\",\"use_time_range\":\"\"}', '1');
INSERT INTO `cmf_option` VALUES ('8', '1', 'admin_dashboard_widgets', '[{\"name\":\"CmfHub\",\"is_system\":1},{\"name\":\"CmfDocuments\",\"is_system\":1},{\"name\":\"MainContributors\",\"is_system\":1},{\"name\":\"Contributors\",\"is_system\":1},{\"name\":\"Custom1\",\"is_system\":1},{\"name\":\"Custom2\",\"is_system\":1},{\"name\":\"Custom3\",\"is_system\":1},{\"name\":\"Custom4\",\"is_system\":1},{\"name\":\"Custom5\",\"is_system\":1}]', '1');

-- ----------------------------
-- Table structure for cmf_plugin
-- ----------------------------
DROP TABLE IF EXISTS `cmf_plugin`;
CREATE TABLE `cmf_plugin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '插件类型;1:网站;8:微信',
  `has_admin` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否有后台管理,0:没有;1:有',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:开启;0:禁用',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '插件安装时间',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '插件标识名,英文字母(惟一)',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '插件名称',
  `demo_url` varchar(50) NOT NULL DEFAULT '' COMMENT '演示地址，带协议',
  `hooks` varchar(255) NOT NULL DEFAULT '' COMMENT '实现的钩子;以“,”分隔',
  `author` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '插件作者',
  `author_url` varchar(50) NOT NULL DEFAULT '' COMMENT '作者网站链接',
  `version` varchar(20) NOT NULL DEFAULT '' COMMENT '插件版本号',
  `description` varchar(255) NOT NULL COMMENT '插件描述',
  `config` text COMMENT '插件配置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='插件表';

-- ----------------------------
-- Records of cmf_plugin
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_portal_category
-- ----------------------------
DROP TABLE IF EXISTS `cmf_portal_category`;
CREATE TABLE `cmf_portal_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '分类父id',
  `post_count` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '分类文章数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:发布,0:不发布',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '分类描述',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '分类层级关系路径',
  `seo_title` varchar(100) NOT NULL DEFAULT '',
  `seo_keywords` varchar(255) NOT NULL DEFAULT '',
  `seo_description` varchar(255) NOT NULL DEFAULT '',
  `list_tpl` varchar(50) NOT NULL DEFAULT '' COMMENT '分类列表模板',
  `one_tpl` varchar(50) NOT NULL DEFAULT '' COMMENT '分类文章页模板',
  `more` text COMMENT '扩展属性',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='portal应用 文章分类表';

-- ----------------------------
-- Records of cmf_portal_category
-- ----------------------------
INSERT INTO `cmf_portal_category` VALUES ('1', '0', '0', '1', '0', '10000', '帮助中心', '', '0-1', '', '', '', 'list', 'article', '{\"thumbnail\":\"\"}');

-- ----------------------------
-- Table structure for cmf_portal_category_post
-- ----------------------------
DROP TABLE IF EXISTS `cmf_portal_category_post`;
CREATE TABLE `cmf_portal_category_post` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '文章id',
  `category_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '分类id',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:发布;0:不发布',
  PRIMARY KEY (`id`),
  KEY `term_taxonomy_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='portal应用 分类文章对应表';

-- ----------------------------
-- Records of cmf_portal_category_post
-- ----------------------------
INSERT INTO `cmf_portal_category_post` VALUES ('1', '1', '1', '10000', '0');
INSERT INTO `cmf_portal_category_post` VALUES ('2', '2', '1', '10000', '1');

-- ----------------------------
-- Table structure for cmf_portal_post
-- ----------------------------
DROP TABLE IF EXISTS `cmf_portal_post`;
CREATE TABLE `cmf_portal_post` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
  `post_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '类型,1:文章;2:页面',
  `post_format` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '内容格式;1:html;2:md',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '发表者用户id',
  `post_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:已发布;0:未发布;',
  `comment_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '评论状态;1:允许;0:不允许',
  `is_top` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否置顶;1:置顶;0:不置顶',
  `recommended` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐;1:推荐;0:不推荐',
  `post_hits` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '查看数',
  `post_favorites` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收藏数',
  `post_like` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `comment_count` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `published_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `post_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'post标题',
  `post_keywords` varchar(150) NOT NULL DEFAULT '' COMMENT 'seo keywords',
  `post_excerpt` varchar(500) NOT NULL DEFAULT '' COMMENT 'post摘要',
  `post_source` varchar(150) NOT NULL DEFAULT '' COMMENT '转载文章的来源',
  `thumbnail` varchar(100) NOT NULL DEFAULT '' COMMENT '缩略图',
  `post_content` text COMMENT '文章内容',
  `post_content_filtered` text COMMENT '处理过的文章内容',
  `more` text COMMENT '扩展属性,如缩略图;格式为json',
  PRIMARY KEY (`id`),
  KEY `type_status_date` (`post_type`,`post_status`,`create_time`,`id`),
  KEY `parent_id` (`parent_id`),
  KEY `user_id` (`user_id`),
  KEY `create_time` (`create_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='portal应用 文章表';

-- ----------------------------
-- Records of cmf_portal_post
-- ----------------------------
INSERT INTO `cmf_portal_post` VALUES ('1', '0', '1', '1', '1', '1', '1', '0', '0', '1', '0', '0', '0', '1538962641', '1538962763', '1538962620', '0', '帮助中心', '', '', '', '', '&lt;p&gt;啊啊啊啊啊啊啊啊啊啊啊啊啊啊快圣诞节疯狂老师大嫁风尚宽带连接&lt;/p&gt;', null, '{\"audio\":\"\",\"video\":\"\",\"thumbnail\":\"\",\"template\":\"\"}');
INSERT INTO `cmf_portal_post` VALUES ('2', '0', '1', '1', '1', '1', '1', '0', '0', '0', '0', '0', '0', '1538962822', '1538962822', '1538963086', '0', '关于我们', '', '', '', '', '&lt;p&gt;关于我们咔咔咔咔咔咔扩扩扩扩扩扩扩扩扩扩扩扩扩扩扩&lt;br&gt;&lt;/p&gt;', null, '{\"audio\":\"\",\"video\":\"\",\"thumbnail\":\"\",\"template\":\"\"}');

-- ----------------------------
-- Table structure for cmf_portal_tag
-- ----------------------------
DROP TABLE IF EXISTS `cmf_portal_tag`;
CREATE TABLE `cmf_portal_tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:发布,0:不发布',
  `recommended` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否推荐;1:推荐;0:不推荐',
  `post_count` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '标签文章数',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '标签名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='portal应用 文章标签表';

-- ----------------------------
-- Records of cmf_portal_tag
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_portal_tag_post
-- ----------------------------
DROP TABLE IF EXISTS `cmf_portal_tag_post`;
CREATE TABLE `cmf_portal_tag_post` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `tag_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '标签 id',
  `post_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '文章 id',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:发布;0:不发布',
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='portal应用 标签文章对应表';

-- ----------------------------
-- Records of cmf_portal_tag_post
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_recycle_bin
-- ----------------------------
DROP TABLE IF EXISTS `cmf_recycle_bin`;
CREATE TABLE `cmf_recycle_bin` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `object_id` int(11) DEFAULT '0' COMMENT '删除内容 id',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '创建时间',
  `table_name` varchar(60) DEFAULT '' COMMENT '删除内容所在表名',
  `name` varchar(255) DEFAULT '' COMMENT '删除内容名称',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT=' 回收站';

-- ----------------------------
-- Records of cmf_recycle_bin
-- ----------------------------
INSERT INTO `cmf_recycle_bin` VALUES ('1', '1', '1538043927', 'portal_category', '', '0');
INSERT INTO `cmf_recycle_bin` VALUES ('2', '1', '1538963113', 'portal_post', '帮助中心', '1');

-- ----------------------------
-- Table structure for cmf_role
-- ----------------------------
DROP TABLE IF EXISTS `cmf_role`;
CREATE TABLE `cmf_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父角色ID',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态;0:禁用;1:正常',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `list_order` float NOT NULL DEFAULT '0' COMMENT '排序',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '角色名称',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='角色表';

-- ----------------------------
-- Records of cmf_role
-- ----------------------------
INSERT INTO `cmf_role` VALUES ('1', '0', '1', '1329633709', '1329633709', '0', '超级管理员', '拥有网站最高管理员权限！');
INSERT INTO `cmf_role` VALUES ('2', '0', '1', '1329633709', '1329633709', '0', '普通管理员', '权限由最高管理员分配！');

-- ----------------------------
-- Table structure for cmf_role_user
-- ----------------------------
DROP TABLE IF EXISTS `cmf_role_user`;
CREATE TABLE `cmf_role_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '角色 id',
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户角色对应表';

-- ----------------------------
-- Records of cmf_role_user
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_route
-- ----------------------------
DROP TABLE IF EXISTS `cmf_route`;
CREATE TABLE `cmf_route` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '路由id',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态;1:启用,0:不启用',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'URL规则类型;1:用户自定义;2:别名添加',
  `full_url` varchar(255) NOT NULL DEFAULT '' COMMENT '完整url',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '实际显示的url',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='url路由表';

-- ----------------------------
-- Records of cmf_route
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_score_log
-- ----------------------------
DROP TABLE IF EXISTS `cmf_score_log`;
CREATE TABLE `cmf_score_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `num` int(10) DEFAULT '0' COMMENT '兑换积分数量',
  `remark` varchar(64) NOT NULL COMMENT '备注',
  `add_time` varchar(64) NOT NULL COMMENT '兑换时间',
  `wallet_address` varchar(128) DEFAULT NULL COMMENT '钱包地址',
  `third_party_platform` varchar(64) NOT NULL COMMENT '第三方平台',
  `status` smallint(1) NOT NULL DEFAULT '0' COMMENT '0待审核1通过2拒绝',
  `reason` varchar(64) NOT NULL COMMENT '积分变化原因',
  `audit_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '后台审核人id',
  `update_time` varchar(64) NOT NULL COMMENT '审核时间',
  `txid` int(64) NOT NULL DEFAULT '0',
  `rejected` varchar(255) NOT NULL COMMENT '拒绝理由',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_score_log
-- ----------------------------
INSERT INTO `cmf_score_log` VALUES ('2', '3', '5', '', '1538125733', '', '', '1', '步行', '0', '', '0', '');
INSERT INTO `cmf_score_log` VALUES ('3', '3', '-5', '', '1538125733', '1NRZ1H7ooKuTuht7fQySug3MW52ZtWRz4L', 'ETH', '0', '兑换', '1', '1539153800', '12', '测试啊');
INSERT INTO `cmf_score_log` VALUES ('8', '4', '-100', '', '1539142860', '0NRZ1H7ooKuTuht7fQySug3MW52ZtWRz4L', '', '0', '兑换', '0', '', '0', '');
INSERT INTO `cmf_score_log` VALUES ('24', '6', '0', '', '1540457999', null, '', '1', '签到', '0', '', '0', '');
INSERT INTO `cmf_score_log` VALUES ('25', '6', '0', '', '1540458036', null, '', '1', '签到', '0', '', '0', '');
INSERT INTO `cmf_score_log` VALUES ('26', '6', '0', '', '1540458036', null, '', '1', '签到', '0', '', '0', '');
INSERT INTO `cmf_score_log` VALUES ('27', '6', '0', '', '1540458048', null, '', '1', '签到', '0', '', '0', '');
INSERT INTO `cmf_score_log` VALUES ('28', '6', '0', '', '1540458048', null, '', '1', '签到', '0', '', '0', '');
INSERT INTO `cmf_score_log` VALUES ('29', '6', '0', '', '1540458110', null, '', '1', '签到', '0', '', '0', '');
INSERT INTO `cmf_score_log` VALUES ('30', '6', '0', '', '1540458881', null, '', '1', '签到', '0', '', '0', '');
INSERT INTO `cmf_score_log` VALUES ('31', '6', '0', '', '1540458950', null, '', '1', '签到', '0', '', '0', '');
INSERT INTO `cmf_score_log` VALUES ('32', '6', '0', '', '1540458994', null, '', '1', '签到', '0', '', '0', '');
INSERT INTO `cmf_score_log` VALUES ('33', '6', '0', '', '1540459079', null, '', '1', '签到', '0', '', '0', '');
INSERT INTO `cmf_score_log` VALUES ('34', '6', '0', '', '1540459124', null, '', '1', '签到', '0', '', '0', '');
INSERT INTO `cmf_score_log` VALUES ('35', '6', '0', '', '1540459470', null, '', '1', '签到', '0', '', '0', '');
INSERT INTO `cmf_score_log` VALUES ('36', '6', '0', '', '1540461990', null, '', '1', '签到', '0', '', '0', '');
INSERT INTO `cmf_score_log` VALUES ('37', '6', '0', '', '1540519400', null, '', '1', '签到', '0', '', '0', '');

-- ----------------------------
-- Table structure for cmf_sign_score
-- ----------------------------
DROP TABLE IF EXISTS `cmf_sign_score`;
CREATE TABLE `cmf_sign_score` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `score` int(10) NOT NULL COMMENT '奖励的积分',
  `user_id` int(10) NOT NULL COMMENT '会员id',
  `sign_time` varchar(64) NOT NULL COMMENT '签到时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_sign_score
-- ----------------------------
INSERT INTO `cmf_sign_score` VALUES ('13', '5', '6', '1540461989');
INSERT INTO `cmf_sign_score` VALUES ('14', '10', '6', '1540519400');

-- ----------------------------
-- Table structure for cmf_slide
-- ----------------------------
DROP TABLE IF EXISTS `cmf_slide`;
CREATE TABLE `cmf_slide` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:显示,0不显示',
  `delete_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `name` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '幻灯片分类',
  `remark` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '分类备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='幻灯片表';

-- ----------------------------
-- Records of cmf_slide
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_slide_item
-- ----------------------------
DROP TABLE IF EXISTS `cmf_slide_item`;
CREATE TABLE `cmf_slide_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `slide_id` int(11) NOT NULL DEFAULT '0' COMMENT '幻灯片id',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态,1:显示;0:隐藏',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '幻灯片名称',
  `image` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '幻灯片图片',
  `url` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '幻灯片链接',
  `target` varchar(10) NOT NULL DEFAULT '' COMMENT '友情链接打开方式',
  `description` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '幻灯片描述',
  `content` text CHARACTER SET utf8 COMMENT '幻灯片内容',
  `more` text COMMENT '扩展信息',
  PRIMARY KEY (`id`),
  KEY `slide_id` (`slide_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='幻灯片子项表';

-- ----------------------------
-- Records of cmf_slide_item
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_theme
-- ----------------------------
DROP TABLE IF EXISTS `cmf_theme`;
CREATE TABLE `cmf_theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '安装时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后升级时间',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '模板状态,1:正在使用;0:未使用',
  `is_compiled` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否为已编译模板',
  `theme` varchar(20) NOT NULL DEFAULT '' COMMENT '主题目录名，用于主题的维一标识',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '主题名称',
  `version` varchar(20) NOT NULL DEFAULT '' COMMENT '主题版本号',
  `demo_url` varchar(50) NOT NULL DEFAULT '' COMMENT '演示地址，带协议',
  `thumbnail` varchar(100) NOT NULL DEFAULT '' COMMENT '缩略图',
  `author` varchar(20) NOT NULL DEFAULT '' COMMENT '主题作者',
  `author_url` varchar(50) NOT NULL DEFAULT '' COMMENT '作者网站链接',
  `lang` varchar(10) NOT NULL DEFAULT '' COMMENT '支持语言',
  `keywords` varchar(50) NOT NULL DEFAULT '' COMMENT '主题关键字',
  `description` varchar(100) NOT NULL DEFAULT '' COMMENT '主题描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cmf_theme
-- ----------------------------
INSERT INTO `cmf_theme` VALUES ('1', '0', '0', '0', '0', 'simpleboot3', 'simpleboot3', '1.0.2', 'http://demo.thinkcmf.com', '', 'ThinkCMF', 'http://www.thinkcmf.com', 'zh-cn', 'ThinkCMF模板', 'ThinkCMF默认模板');

-- ----------------------------
-- Table structure for cmf_theme_file
-- ----------------------------
DROP TABLE IF EXISTS `cmf_theme_file`;
CREATE TABLE `cmf_theme_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_public` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否公共的模板文件',
  `list_order` float NOT NULL DEFAULT '10000' COMMENT '排序',
  `theme` varchar(20) NOT NULL DEFAULT '' COMMENT '模板名称',
  `name` varchar(20) NOT NULL DEFAULT '' COMMENT '模板文件名',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '操作',
  `file` varchar(50) NOT NULL DEFAULT '' COMMENT '模板文件，相对于模板根目录，如Portal/index.html',
  `description` varchar(100) NOT NULL DEFAULT '' COMMENT '模板文件描述',
  `more` text COMMENT '模板更多配置,用户自己后台设置的',
  `config_more` text COMMENT '模板更多配置,来源模板的配置文件',
  `draft_more` text COMMENT '模板更多配置,用户临时保存的配置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of cmf_theme_file
-- ----------------------------
INSERT INTO `cmf_theme_file` VALUES ('1', '0', '10', 'simpleboot3', '文章页', 'portal/Article/index', 'portal/article', '文章页模板文件', '{\"vars\":{\"hot_articles_category_id\":{\"title\":\"Hot Articles\\u5206\\u7c7bID\",\"value\":\"1\",\"type\":\"text\",\"tip\":\"\",\"rule\":[]}}}', '{\"vars\":{\"hot_articles_category_id\":{\"title\":\"Hot Articles\\u5206\\u7c7bID\",\"value\":\"1\",\"type\":\"text\",\"tip\":\"\",\"rule\":[]}}}', null);
INSERT INTO `cmf_theme_file` VALUES ('2', '0', '10', 'simpleboot3', '联系我们页', 'portal/Page/index', 'portal/contact', '联系我们页模板文件', '{\"vars\":{\"baidu_map_info_window_text\":{\"title\":\"\\u767e\\u5ea6\\u5730\\u56fe\\u6807\\u6ce8\\u6587\\u5b57\",\"name\":\"baidu_map_info_window_text\",\"value\":\"ThinkCMF<br\\/><span class=\'\'>\\u5730\\u5740\\uff1a\\u4e0a\\u6d77\\u5e02\\u5f90\\u6c47\\u533a\\u659c\\u571f\\u8def2601\\u53f7<\\/span>\",\"type\":\"text\",\"tip\":\"\\u767e\\u5ea6\\u5730\\u56fe\\u6807\\u6ce8\\u6587\\u5b57,\\u652f\\u6301\\u7b80\\u5355html\\u4ee3\\u7801\",\"rule\":[]},\"company_location\":{\"title\":\"\\u516c\\u53f8\\u5750\\u6807\",\"value\":\"\",\"type\":\"location\",\"tip\":\"\",\"rule\":{\"require\":true}},\"address_cn\":{\"title\":\"\\u516c\\u53f8\\u5730\\u5740\",\"value\":\"\\u4e0a\\u6d77\\u5e02\\u5f90\\u6c47\\u533a\\u659c\\u571f\\u8def0001\\u53f7\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"address_en\":{\"title\":\"\\u516c\\u53f8\\u5730\\u5740\\uff08\\u82f1\\u6587\\uff09\",\"value\":\"NO.0001 Xie Tu Road, Shanghai China\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"email\":{\"title\":\"\\u516c\\u53f8\\u90ae\\u7bb1\",\"value\":\"catman@thinkcmf.com\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"phone_cn\":{\"title\":\"\\u516c\\u53f8\\u7535\\u8bdd\",\"value\":\"021 1000 0001\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"phone_en\":{\"title\":\"\\u516c\\u53f8\\u7535\\u8bdd\\uff08\\u82f1\\u6587\\uff09\",\"value\":\"+8621 1000 0001\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"qq\":{\"title\":\"\\u8054\\u7cfbQQ\",\"value\":\"478519726\",\"type\":\"text\",\"tip\":\"\\u591a\\u4e2a QQ\\u4ee5\\u82f1\\u6587\\u9017\\u53f7\\u9694\\u5f00\",\"rule\":{\"require\":true}}}}', '{\"vars\":{\"baidu_map_info_window_text\":{\"title\":\"\\u767e\\u5ea6\\u5730\\u56fe\\u6807\\u6ce8\\u6587\\u5b57\",\"name\":\"baidu_map_info_window_text\",\"value\":\"ThinkCMF<br\\/><span class=\'\'>\\u5730\\u5740\\uff1a\\u4e0a\\u6d77\\u5e02\\u5f90\\u6c47\\u533a\\u659c\\u571f\\u8def2601\\u53f7<\\/span>\",\"type\":\"text\",\"tip\":\"\\u767e\\u5ea6\\u5730\\u56fe\\u6807\\u6ce8\\u6587\\u5b57,\\u652f\\u6301\\u7b80\\u5355html\\u4ee3\\u7801\",\"rule\":[]},\"company_location\":{\"title\":\"\\u516c\\u53f8\\u5750\\u6807\",\"value\":\"\",\"type\":\"location\",\"tip\":\"\",\"rule\":{\"require\":true}},\"address_cn\":{\"title\":\"\\u516c\\u53f8\\u5730\\u5740\",\"value\":\"\\u4e0a\\u6d77\\u5e02\\u5f90\\u6c47\\u533a\\u659c\\u571f\\u8def0001\\u53f7\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"address_en\":{\"title\":\"\\u516c\\u53f8\\u5730\\u5740\\uff08\\u82f1\\u6587\\uff09\",\"value\":\"NO.0001 Xie Tu Road, Shanghai China\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"email\":{\"title\":\"\\u516c\\u53f8\\u90ae\\u7bb1\",\"value\":\"catman@thinkcmf.com\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"phone_cn\":{\"title\":\"\\u516c\\u53f8\\u7535\\u8bdd\",\"value\":\"021 1000 0001\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"phone_en\":{\"title\":\"\\u516c\\u53f8\\u7535\\u8bdd\\uff08\\u82f1\\u6587\\uff09\",\"value\":\"+8621 1000 0001\",\"type\":\"text\",\"tip\":\"\",\"rule\":{\"require\":true}},\"qq\":{\"title\":\"\\u8054\\u7cfbQQ\",\"value\":\"478519726\",\"type\":\"text\",\"tip\":\"\\u591a\\u4e2a QQ\\u4ee5\\u82f1\\u6587\\u9017\\u53f7\\u9694\\u5f00\",\"rule\":{\"require\":true}}}}', null);
INSERT INTO `cmf_theme_file` VALUES ('3', '0', '5', 'simpleboot3', '首页', 'portal/Index/index', 'portal/index', '首页模板文件', '{\"vars\":{\"top_slide\":{\"title\":\"\\u9876\\u90e8\\u5e7b\\u706f\\u7247\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"admin\\/Slide\\/index\",\"multi\":false},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u9876\\u90e8\\u5e7b\\u706f\\u7247\",\"tip\":\"\\u9876\\u90e8\\u5e7b\\u706f\\u7247\",\"rule\":{\"require\":true}}},\"widgets\":{\"features\":{\"title\":\"\\u5feb\\u901f\\u4e86\\u89e3ThinkCMF\",\"display\":\"1\",\"vars\":{\"sub_title\":{\"title\":\"\\u526f\\u6807\\u9898\",\"value\":\"Quickly understand the ThinkCMF\",\"type\":\"text\",\"placeholder\":\"\\u8bf7\\u8f93\\u5165\\u526f\\u6807\\u9898\",\"tip\":\"\",\"rule\":{\"require\":true}},\"features\":{\"title\":\"\\u7279\\u6027\\u4ecb\\u7ecd\",\"value\":[{\"title\":\"MVC\\u5206\\u5c42\\u6a21\\u5f0f\",\"icon\":\"bars\",\"content\":\"\\u4f7f\\u7528MVC\\u5e94\\u7528\\u7a0b\\u5e8f\\u88ab\\u5206\\u6210\\u4e09\\u4e2a\\u6838\\u5fc3\\u90e8\\u4ef6\\uff1a\\u6a21\\u578b\\uff08M\\uff09\\u3001\\u89c6\\u56fe\\uff08V\\uff09\\u3001\\u63a7\\u5236\\u5668\\uff08C\\uff09\\uff0c\\u4ed6\\u4e0d\\u662f\\u4e00\\u4e2a\\u65b0\\u7684\\u6982\\u5ff5\\uff0c\\u53ea\\u662fThinkCMF\\u5c06\\u5176\\u53d1\\u6325\\u5230\\u4e86\\u6781\\u81f4\\u3002\"},{\"title\":\"\\u7528\\u6237\\u7ba1\\u7406\",\"icon\":\"group\",\"content\":\"ThinkCMF\\u5185\\u7f6e\\u4e86\\u7075\\u6d3b\\u7684\\u7528\\u6237\\u7ba1\\u7406\\u65b9\\u5f0f\\uff0c\\u5e76\\u53ef\\u76f4\\u63a5\\u4e0e\\u7b2c\\u4e09\\u65b9\\u7ad9\\u70b9\\u8fdb\\u884c\\u4e92\\u8054\\u4e92\\u901a\\uff0c\\u5982\\u679c\\u4f60\\u613f\\u610f\\u751a\\u81f3\\u53ef\\u4ee5\\u5bf9\\u5355\\u4e2a\\u7528\\u6237\\u6216\\u7fa4\\u4f53\\u7528\\u6237\\u7684\\u884c\\u4e3a\\u8fdb\\u884c\\u8bb0\\u5f55\\u53ca\\u5206\\u4eab\\uff0c\\u4e3a\\u60a8\\u7684\\u8fd0\\u8425\\u51b3\\u7b56\\u63d0\\u4f9b\\u6709\\u6548\\u53c2\\u8003\\u6570\\u636e\\u3002\"},{\"title\":\"\\u4e91\\u7aef\\u90e8\\u7f72\",\"icon\":\"cloud\",\"content\":\"\\u901a\\u8fc7\\u9a71\\u52a8\\u7684\\u65b9\\u5f0f\\u53ef\\u4ee5\\u8f7b\\u677e\\u652f\\u6301\\u4e91\\u5e73\\u53f0\\u7684\\u90e8\\u7f72\\uff0c\\u8ba9\\u4f60\\u7684\\u7f51\\u7ad9\\u65e0\\u7f1d\\u8fc1\\u79fb\\uff0c\\u5185\\u7f6e\\u5df2\\u7ecf\\u652f\\u6301SAE\\u3001BAE\\uff0c\\u6b63\\u5f0f\\u7248\\u5c06\\u5bf9\\u4e91\\u7aef\\u90e8\\u7f72\\u8fdb\\u884c\\u8fdb\\u4e00\\u6b65\\u4f18\\u5316\\u3002\"},{\"title\":\"\\u5b89\\u5168\\u7b56\\u7565\",\"icon\":\"heart\",\"content\":\"\\u63d0\\u4f9b\\u7684\\u7a33\\u5065\\u7684\\u5b89\\u5168\\u7b56\\u7565\\uff0c\\u5305\\u62ec\\u5907\\u4efd\\u6062\\u590d\\uff0c\\u5bb9\\u9519\\uff0c\\u9632\\u6cbb\\u6076\\u610f\\u653b\\u51fb\\u767b\\u9646\\uff0c\\u7f51\\u9875\\u9632\\u7be1\\u6539\\u7b49\\u591a\\u9879\\u5b89\\u5168\\u7ba1\\u7406\\u529f\\u80fd\\uff0c\\u4fdd\\u8bc1\\u7cfb\\u7edf\\u5b89\\u5168\\uff0c\\u53ef\\u9760\\uff0c\\u7a33\\u5b9a\\u7684\\u8fd0\\u884c\\u3002\"},{\"title\":\"\\u5e94\\u7528\\u6a21\\u5757\\u5316\",\"icon\":\"cubes\",\"content\":\"\\u63d0\\u51fa\\u5168\\u65b0\\u7684\\u5e94\\u7528\\u6a21\\u5f0f\\u8fdb\\u884c\\u6269\\u5c55\\uff0c\\u4e0d\\u7ba1\\u662f\\u4f60\\u5f00\\u53d1\\u4e00\\u4e2a\\u5c0f\\u529f\\u80fd\\u8fd8\\u662f\\u4e00\\u4e2a\\u5168\\u65b0\\u7684\\u7ad9\\u70b9\\uff0c\\u5728ThinkCMF\\u4e2d\\u4f60\\u53ea\\u662f\\u589e\\u52a0\\u4e86\\u4e00\\u4e2aAPP\\uff0c\\u6bcf\\u4e2a\\u72ec\\u7acb\\u8fd0\\u884c\\u4e92\\u4e0d\\u5f71\\u54cd\\uff0c\\u4fbf\\u4e8e\\u7075\\u6d3b\\u6269\\u5c55\\u548c\\u4e8c\\u6b21\\u5f00\\u53d1\\u3002\"},{\"title\":\"\\u514d\\u8d39\\u5f00\\u6e90\",\"icon\":\"certificate\",\"content\":\"\\u4ee3\\u7801\\u9075\\u5faaApache2\\u5f00\\u6e90\\u534f\\u8bae\\uff0c\\u514d\\u8d39\\u4f7f\\u7528\\uff0c\\u5bf9\\u5546\\u4e1a\\u7528\\u6237\\u4e5f\\u65e0\\u4efb\\u4f55\\u9650\\u5236\\u3002\"}],\"type\":\"array\",\"item\":{\"title\":{\"title\":\"\\u6807\\u9898\",\"value\":\"\",\"type\":\"text\",\"rule\":{\"require\":true}},\"icon\":{\"title\":\"\\u56fe\\u6807\",\"value\":\"\",\"type\":\"text\"},\"content\":{\"title\":\"\\u63cf\\u8ff0\",\"value\":\"\",\"type\":\"textarea\"}},\"tip\":\"\"}}},\"last_news\":{\"title\":\"\\u6700\\u65b0\\u8d44\\u8baf\",\"display\":\"1\",\"vars\":{\"last_news_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/Category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}}}}', '{\"vars\":{\"top_slide\":{\"title\":\"\\u9876\\u90e8\\u5e7b\\u706f\\u7247\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"admin\\/Slide\\/index\",\"multi\":false},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u9876\\u90e8\\u5e7b\\u706f\\u7247\",\"tip\":\"\\u9876\\u90e8\\u5e7b\\u706f\\u7247\",\"rule\":{\"require\":true}}},\"widgets\":{\"features\":{\"title\":\"\\u5feb\\u901f\\u4e86\\u89e3ThinkCMF\",\"display\":\"1\",\"vars\":{\"sub_title\":{\"title\":\"\\u526f\\u6807\\u9898\",\"value\":\"Quickly understand the ThinkCMF\",\"type\":\"text\",\"placeholder\":\"\\u8bf7\\u8f93\\u5165\\u526f\\u6807\\u9898\",\"tip\":\"\",\"rule\":{\"require\":true}},\"features\":{\"title\":\"\\u7279\\u6027\\u4ecb\\u7ecd\",\"value\":[{\"title\":\"MVC\\u5206\\u5c42\\u6a21\\u5f0f\",\"icon\":\"bars\",\"content\":\"\\u4f7f\\u7528MVC\\u5e94\\u7528\\u7a0b\\u5e8f\\u88ab\\u5206\\u6210\\u4e09\\u4e2a\\u6838\\u5fc3\\u90e8\\u4ef6\\uff1a\\u6a21\\u578b\\uff08M\\uff09\\u3001\\u89c6\\u56fe\\uff08V\\uff09\\u3001\\u63a7\\u5236\\u5668\\uff08C\\uff09\\uff0c\\u4ed6\\u4e0d\\u662f\\u4e00\\u4e2a\\u65b0\\u7684\\u6982\\u5ff5\\uff0c\\u53ea\\u662fThinkCMF\\u5c06\\u5176\\u53d1\\u6325\\u5230\\u4e86\\u6781\\u81f4\\u3002\"},{\"title\":\"\\u7528\\u6237\\u7ba1\\u7406\",\"icon\":\"group\",\"content\":\"ThinkCMF\\u5185\\u7f6e\\u4e86\\u7075\\u6d3b\\u7684\\u7528\\u6237\\u7ba1\\u7406\\u65b9\\u5f0f\\uff0c\\u5e76\\u53ef\\u76f4\\u63a5\\u4e0e\\u7b2c\\u4e09\\u65b9\\u7ad9\\u70b9\\u8fdb\\u884c\\u4e92\\u8054\\u4e92\\u901a\\uff0c\\u5982\\u679c\\u4f60\\u613f\\u610f\\u751a\\u81f3\\u53ef\\u4ee5\\u5bf9\\u5355\\u4e2a\\u7528\\u6237\\u6216\\u7fa4\\u4f53\\u7528\\u6237\\u7684\\u884c\\u4e3a\\u8fdb\\u884c\\u8bb0\\u5f55\\u53ca\\u5206\\u4eab\\uff0c\\u4e3a\\u60a8\\u7684\\u8fd0\\u8425\\u51b3\\u7b56\\u63d0\\u4f9b\\u6709\\u6548\\u53c2\\u8003\\u6570\\u636e\\u3002\"},{\"title\":\"\\u4e91\\u7aef\\u90e8\\u7f72\",\"icon\":\"cloud\",\"content\":\"\\u901a\\u8fc7\\u9a71\\u52a8\\u7684\\u65b9\\u5f0f\\u53ef\\u4ee5\\u8f7b\\u677e\\u652f\\u6301\\u4e91\\u5e73\\u53f0\\u7684\\u90e8\\u7f72\\uff0c\\u8ba9\\u4f60\\u7684\\u7f51\\u7ad9\\u65e0\\u7f1d\\u8fc1\\u79fb\\uff0c\\u5185\\u7f6e\\u5df2\\u7ecf\\u652f\\u6301SAE\\u3001BAE\\uff0c\\u6b63\\u5f0f\\u7248\\u5c06\\u5bf9\\u4e91\\u7aef\\u90e8\\u7f72\\u8fdb\\u884c\\u8fdb\\u4e00\\u6b65\\u4f18\\u5316\\u3002\"},{\"title\":\"\\u5b89\\u5168\\u7b56\\u7565\",\"icon\":\"heart\",\"content\":\"\\u63d0\\u4f9b\\u7684\\u7a33\\u5065\\u7684\\u5b89\\u5168\\u7b56\\u7565\\uff0c\\u5305\\u62ec\\u5907\\u4efd\\u6062\\u590d\\uff0c\\u5bb9\\u9519\\uff0c\\u9632\\u6cbb\\u6076\\u610f\\u653b\\u51fb\\u767b\\u9646\\uff0c\\u7f51\\u9875\\u9632\\u7be1\\u6539\\u7b49\\u591a\\u9879\\u5b89\\u5168\\u7ba1\\u7406\\u529f\\u80fd\\uff0c\\u4fdd\\u8bc1\\u7cfb\\u7edf\\u5b89\\u5168\\uff0c\\u53ef\\u9760\\uff0c\\u7a33\\u5b9a\\u7684\\u8fd0\\u884c\\u3002\"},{\"title\":\"\\u5e94\\u7528\\u6a21\\u5757\\u5316\",\"icon\":\"cubes\",\"content\":\"\\u63d0\\u51fa\\u5168\\u65b0\\u7684\\u5e94\\u7528\\u6a21\\u5f0f\\u8fdb\\u884c\\u6269\\u5c55\\uff0c\\u4e0d\\u7ba1\\u662f\\u4f60\\u5f00\\u53d1\\u4e00\\u4e2a\\u5c0f\\u529f\\u80fd\\u8fd8\\u662f\\u4e00\\u4e2a\\u5168\\u65b0\\u7684\\u7ad9\\u70b9\\uff0c\\u5728ThinkCMF\\u4e2d\\u4f60\\u53ea\\u662f\\u589e\\u52a0\\u4e86\\u4e00\\u4e2aAPP\\uff0c\\u6bcf\\u4e2a\\u72ec\\u7acb\\u8fd0\\u884c\\u4e92\\u4e0d\\u5f71\\u54cd\\uff0c\\u4fbf\\u4e8e\\u7075\\u6d3b\\u6269\\u5c55\\u548c\\u4e8c\\u6b21\\u5f00\\u53d1\\u3002\"},{\"title\":\"\\u514d\\u8d39\\u5f00\\u6e90\",\"icon\":\"certificate\",\"content\":\"\\u4ee3\\u7801\\u9075\\u5faaApache2\\u5f00\\u6e90\\u534f\\u8bae\\uff0c\\u514d\\u8d39\\u4f7f\\u7528\\uff0c\\u5bf9\\u5546\\u4e1a\\u7528\\u6237\\u4e5f\\u65e0\\u4efb\\u4f55\\u9650\\u5236\\u3002\"}],\"type\":\"array\",\"item\":{\"title\":{\"title\":\"\\u6807\\u9898\",\"value\":\"\",\"type\":\"text\",\"rule\":{\"require\":true}},\"icon\":{\"title\":\"\\u56fe\\u6807\",\"value\":\"\",\"type\":\"text\"},\"content\":{\"title\":\"\\u63cf\\u8ff0\",\"value\":\"\",\"type\":\"textarea\"}},\"tip\":\"\"}}},\"last_news\":{\"title\":\"\\u6700\\u65b0\\u8d44\\u8baf\",\"display\":\"1\",\"vars\":{\"last_news_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/Category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}}}}', null);
INSERT INTO `cmf_theme_file` VALUES ('4', '0', '10', 'simpleboot3', '文章列表页', 'portal/List/index', 'portal/list', '文章列表模板文件', '{\"vars\":[],\"widgets\":{\"hottest_articles\":{\"title\":\"\\u70ed\\u95e8\\u6587\\u7ae0\",\"display\":\"1\",\"vars\":{\"hottest_articles_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}},\"last_articles\":{\"title\":\"\\u6700\\u65b0\\u53d1\\u5e03\",\"display\":\"1\",\"vars\":{\"last_articles_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}}}}', '{\"vars\":[],\"widgets\":{\"hottest_articles\":{\"title\":\"\\u70ed\\u95e8\\u6587\\u7ae0\",\"display\":\"1\",\"vars\":{\"hottest_articles_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}},\"last_articles\":{\"title\":\"\\u6700\\u65b0\\u53d1\\u5e03\",\"display\":\"1\",\"vars\":{\"last_articles_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}}}}', null);
INSERT INTO `cmf_theme_file` VALUES ('5', '0', '10', 'simpleboot3', '单页面', 'portal/Page/index', 'portal/page', '单页面模板文件', '{\"widgets\":{\"hottest_articles\":{\"title\":\"\\u70ed\\u95e8\\u6587\\u7ae0\",\"display\":\"1\",\"vars\":{\"hottest_articles_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}},\"last_articles\":{\"title\":\"\\u6700\\u65b0\\u53d1\\u5e03\",\"display\":\"1\",\"vars\":{\"last_articles_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}}}}', '{\"widgets\":{\"hottest_articles\":{\"title\":\"\\u70ed\\u95e8\\u6587\\u7ae0\",\"display\":\"1\",\"vars\":{\"hottest_articles_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}},\"last_articles\":{\"title\":\"\\u6700\\u65b0\\u53d1\\u5e03\",\"display\":\"1\",\"vars\":{\"last_articles_category_id\":{\"title\":\"\\u6587\\u7ae0\\u5206\\u7c7bID\",\"value\":\"\",\"type\":\"text\",\"dataSource\":{\"api\":\"portal\\/category\\/index\",\"multi\":true},\"placeholder\":\"\\u8bf7\\u9009\\u62e9\\u5206\\u7c7b\",\"tip\":\"\",\"rule\":{\"require\":true}}}}}}', null);
INSERT INTO `cmf_theme_file` VALUES ('6', '0', '10', 'simpleboot3', '搜索页面', 'portal/search/index', 'portal/search', '搜索模板文件', '{\"vars\":{\"varName1\":{\"title\":\"\\u70ed\\u95e8\\u641c\\u7d22\",\"value\":\"1\",\"type\":\"text\",\"tip\":\"\\u8fd9\\u662f\\u4e00\\u4e2atext\",\"rule\":{\"require\":true}}}}', '{\"vars\":{\"varName1\":{\"title\":\"\\u70ed\\u95e8\\u641c\\u7d22\",\"value\":\"1\",\"type\":\"text\",\"tip\":\"\\u8fd9\\u662f\\u4e00\\u4e2atext\",\"rule\":{\"require\":true}}}}', null);
INSERT INTO `cmf_theme_file` VALUES ('7', '1', '0', 'simpleboot3', '模板全局配置', 'public/Config', 'public/config', '模板全局配置文件', '{\"vars\":{\"enable_mobile\":{\"title\":\"\\u624b\\u673a\\u6ce8\\u518c\",\"value\":1,\"type\":\"select\",\"options\":{\"1\":\"\\u5f00\\u542f\",\"0\":\"\\u5173\\u95ed\"},\"tip\":\"\"}}}', '{\"vars\":{\"enable_mobile\":{\"title\":\"\\u624b\\u673a\\u6ce8\\u518c\",\"value\":1,\"type\":\"select\",\"options\":{\"1\":\"\\u5f00\\u542f\",\"0\":\"\\u5173\\u95ed\"},\"tip\":\"\"}}}', null);
INSERT INTO `cmf_theme_file` VALUES ('8', '1', '1', 'simpleboot3', '导航条', 'public/Nav', 'public/nav', '导航条模板文件', '{\"vars\":{\"company_name\":{\"title\":\"\\u516c\\u53f8\\u540d\\u79f0\",\"name\":\"company_name\",\"value\":\"ThinkCMF\",\"type\":\"text\",\"tip\":\"\",\"rule\":[]}}}', '{\"vars\":{\"company_name\":{\"title\":\"\\u516c\\u53f8\\u540d\\u79f0\",\"name\":\"company_name\",\"value\":\"ThinkCMF\",\"type\":\"text\",\"tip\":\"\",\"rule\":[]}}}', null);

-- ----------------------------
-- Table structure for cmf_third_party_user
-- ----------------------------
DROP TABLE IF EXISTS `cmf_third_party_user`;
CREATE TABLE `cmf_third_party_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '本站用户id',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'access_token过期时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '绑定时间',
  `login_times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态;1:正常;0:禁用',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `third_party` varchar(20) NOT NULL DEFAULT '' COMMENT '第三方惟一码',
  `app_id` varchar(64) NOT NULL DEFAULT '' COMMENT '第三方应用 id',
  `last_login_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `access_token` varchar(512) NOT NULL DEFAULT '' COMMENT '第三方授权码',
  `openid` varchar(40) NOT NULL DEFAULT '' COMMENT '第三方用户id',
  `union_id` varchar(64) NOT NULL DEFAULT '' COMMENT '第三方用户多个产品中的惟一 id,(如:微信平台)',
  `more` text COMMENT '扩展信息',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='第三方用户表';

-- ----------------------------
-- Records of cmf_third_party_user
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_user
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user`;
CREATE TABLE `cmf_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '用户类型;1:admin;2:会员',
  `sex` tinyint(2) NOT NULL DEFAULT '0' COMMENT '性别;0:保密,1:男,2:女',
  `birthday` int(11) NOT NULL DEFAULT '0' COMMENT '生日',
  `last_login_time` int(11) NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `total_score` int(255) DEFAULT '0' COMMENT '累计总积分',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '用户积分',
  `coin` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '金币',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '注册时间',
  `user_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '用户状态;0:禁用,1:正常,2:未验证',
  `user_login` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `user_pass` varchar(64) NOT NULL DEFAULT '' COMMENT '登录密码;cmf_password加密',
  `salt` varchar(32) NOT NULL,
  `user_nickname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户昵称',
  `user_email` varchar(100) NOT NULL DEFAULT '' COMMENT '用户登录邮箱',
  `user_url` varchar(100) NOT NULL DEFAULT '' COMMENT '用户个人网址',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `signature` varchar(255) NOT NULL DEFAULT '' COMMENT '个性签名',
  `last_login_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `user_activation_key` varchar(60) NOT NULL DEFAULT '' COMMENT '激活码',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '中国手机不带国家代码，国际手机号格式为：国家代码-手机号',
  `more` text COMMENT '扩展属性',
  `year` varchar(16) NOT NULL COMMENT '生日年份',
  `month` varchar(16) NOT NULL COMMENT '生日月份',
  `stepgoal` int(10) NOT NULL DEFAULT '0' COMMENT '目标步数',
  `height` int(10) NOT NULL DEFAULT '0' COMMENT '身高',
  `weight` int(10) NOT NULL DEFAULT '0' COMMENT '体重',
  `stride` int(10) NOT NULL DEFAULT '0' COMMENT '步距',
  `unit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '单位模式0公制1英制',
  `age` int(10) DEFAULT '0' COMMENT '年龄',
  `true_name` varchar(64) NOT NULL COMMENT '真实姓名',
  `id_card` varchar(64) NOT NULL COMMENT '身份证号',
  `pay_password` varchar(64) NOT NULL COMMENT '支付密码',
  `level` smallint(1) NOT NULL DEFAULT '0' COMMENT '会员等级',
  `parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '邀请人id',
  PRIMARY KEY (`id`),
  KEY `user_login` (`user_login`),
  KEY `user_nickname` (`user_nickname`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- ----------------------------
-- Records of cmf_user
-- ----------------------------
INSERT INTO `cmf_user` VALUES ('1', '1', '0', '0', '1540431243', '0', '0', '0', '0.00', '1537948598', '1', 'admin', '###ff7df59369b44866f968ed6dce7ecb38', '', 'admin', '1447488453@qq.com', '', '', '', '127.0.0.1', '', '', null, '0', '0', '0', '0', '0', '0', '0', '0', '', '', '', '0', '0');
INSERT INTO `cmf_user` VALUES ('3', '2', '0', '0', '1540347594', '230', '230', '0', '0.00', '1537948598', '1', '13688886666', '797fd27d2719ac5a739062560926deff', '6s8zXk', 'test', '1447488453@qq.com', '', '', '', '127.0.0.1', '', '13688886666', null, '0', '0', '1000', '200', '100', '0', '0', '88', '', '', '797fd27d2719ac5a739062560926deff', '0', '0');
INSERT INTO `cmf_user` VALUES ('4', '2', '0', '0', '1538182648', '1000', '200', '0', '0.00', '1537948598', '1', '13558455555', '797fd27d2719ac5a739062560926deff', '6s8zXk', 'test222', '', '', '', '', '', '', '13558455555', null, '', '', '0', '0', '0', '0', '0', '0', '', '', '797fd27d2719ac5a739062560926deff', '0', '0');
INSERT INTO `cmf_user` VALUES ('6', '2', '1', '0', '1540519389', '195', '195', '0', '0.00', '1540190167', '1', '', 'b16da2a775513981945d04254038bbfe', 'vdI3U1', '小明2号', '', '', 'http://b478dq.natappfree.cc/upload/avatar/20181025100846_5395.jpeg', '', '127.0.0.1', '', '18814374961', null, '1993', '11', '26000', '166', '46', '0', '1', '0', '', '', 'cb567ba643e2c36ae7dcbf57a1d86f50', '0', '0');

-- ----------------------------
-- Table structure for cmf_user_action
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_action`;
CREATE TABLE `cmf_user_action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '更改积分，可以为负',
  `coin` int(11) NOT NULL DEFAULT '0' COMMENT '更改金币，可以为负',
  `reward_number` int(11) NOT NULL DEFAULT '0' COMMENT '奖励次数',
  `cycle_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '周期类型;0:不限;1:按天;2:按小时;3:永久',
  `cycle_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '周期时间值',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户操作名称',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '用户操作名称',
  `app` varchar(50) NOT NULL DEFAULT '' COMMENT '操作所在应用名或插件名等',
  `url` text COMMENT '执行操作的url',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='用户操作表';

-- ----------------------------
-- Records of cmf_user_action
-- ----------------------------
INSERT INTO `cmf_user_action` VALUES ('1', '1', '1', '1', '2', '1', '用户登录', 'login', 'user', '');

-- ----------------------------
-- Table structure for cmf_user_action_log
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_action_log`;
CREATE TABLE `cmf_user_action_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '访问次数',
  `last_visit_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后访问时间',
  `object` varchar(100) NOT NULL DEFAULT '' COMMENT '访问对象的id,格式:不带前缀的表名+id;如posts1表示xx_posts表里id为1的记录',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '操作名称;格式:应用名+控制器+操作名,也可自己定义格式只要不发生冲突且惟一;',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT '用户ip',
  PRIMARY KEY (`id`),
  KEY `user_object_action` (`user_id`,`object`,`action`),
  KEY `user_object_action_ip` (`user_id`,`object`,`action`,`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='访问记录表';

-- ----------------------------
-- Records of cmf_user_action_log
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_user_balance_log
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_balance_log`;
CREATE TABLE `cmf_user_balance_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户 id',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `change` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '更改余额',
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '更改后余额',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户余额变更日志表';

-- ----------------------------
-- Records of cmf_user_balance_log
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_user_blood_pressure
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_blood_pressure`;
CREATE TABLE `cmf_user_blood_pressure` (
  `id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `blood_pressure` varchar(64) NOT NULL COMMENT '血压值',
  `blood_oxygen` varchar(64) NOT NULL COMMENT '血氧',
  `add_time` varchar(64) NOT NULL,
  `fatigue` varchar(64) NOT NULL COMMENT '疲劳度',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_user_blood_pressure
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_user_favorite
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_favorite`;
CREATE TABLE `cmf_user_favorite` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户 id',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '收藏内容的标题',
  `thumbnail` varchar(100) NOT NULL DEFAULT '' COMMENT '缩略图',
  `url` varchar(255) DEFAULT NULL COMMENT '收藏内容的原文地址，JSON格式',
  `description` text COMMENT '收藏内容的描述',
  `table_name` varchar(64) NOT NULL DEFAULT '' COMMENT '收藏实体以前所在表,不带前缀',
  `object_id` int(10) unsigned DEFAULT '0' COMMENT '收藏内容原来的主键id',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '收藏时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户收藏表';

-- ----------------------------
-- Records of cmf_user_favorite
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_user_heart_rate
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_heart_rate`;
CREATE TABLE `cmf_user_heart_rate` (
  `id` int(10) NOT NULL,
  `min_heart_rate` varchar(32) NOT NULL COMMENT '最小心率',
  `max_heart_rate` varchar(32) NOT NULL COMMENT '最大心率',
  `avg_heart_rate` varchar(32) NOT NULL COMMENT '平均心率',
  `resting_heart_rate` varchar(32) NOT NULL COMMENT '静息心率',
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `add_time` varchar(32) NOT NULL COMMENT '记录时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_user_heart_rate
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_user_like
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_like`;
CREATE TABLE `cmf_user_like` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '用户 id',
  `object_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '内容原来的主键id',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `table_name` varchar(64) NOT NULL DEFAULT '' COMMENT '内容以前所在表,不带前缀',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '内容的原文地址，不带域名',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '内容的标题',
  `thumbnail` varchar(100) NOT NULL DEFAULT '' COMMENT '缩略图',
  `description` text COMMENT '内容的描述',
  PRIMARY KEY (`id`),
  KEY `uid` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户点赞表';

-- ----------------------------
-- Records of cmf_user_like
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_user_login_attempt
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_login_attempt`;
CREATE TABLE `cmf_user_login_attempt` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `login_attempts` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '尝试次数',
  `attempt_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '尝试登录时间',
  `locked_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '锁定时间',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT '用户 ip',
  `account` varchar(100) NOT NULL DEFAULT '' COMMENT '用户账号,手机号,邮箱或用户名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='用户登录尝试表';

-- ----------------------------
-- Records of cmf_user_login_attempt
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_user_message
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_message`;
CREATE TABLE `cmf_user_message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `title` varchar(64) NOT NULL COMMENT '标题',
  `content` varchar(255) NOT NULL COMMENT '消息内容',
  `add_time` varchar(64) NOT NULL COMMENT '发布时间',
  `category` int(10) NOT NULL DEFAULT '0' COMMENT '消息类型',
  `status` smallint(1) NOT NULL DEFAULT '0' COMMENT '0未读1已读',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_user_message
-- ----------------------------
INSERT INTO `cmf_user_message` VALUES ('1', '3', 'test', 'dhsajdhaskjcsadasd', '', '1', '1');

-- ----------------------------
-- Table structure for cmf_user_referrer
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_referrer`;
CREATE TABLE `cmf_user_referrer` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL COMMENT '用户ID',
  `parent_user_id` bigint(20) DEFAULT '0' COMMENT '上一级推介人',
  `path` varchar(20) DEFAULT '' COMMENT '上下层级关系。0-1-2',
  `status` int(2) NOT NULL DEFAULT '2' COMMENT '有效状态。0无，1有，2待审核',
  `type` int(2) DEFAULT '0' COMMENT '关系类型。1普通会员三级分销关系，2代理各级的关系',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_user_referrer
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_user_run
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_run`;
CREATE TABLE `cmf_user_run` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户id',
  `step_num` int(10) DEFAULT '0' COMMENT '步数',
  `stride` varchar(32) NOT NULL COMMENT '步距',
  `consume` varchar(255) NOT NULL COMMENT '消耗',
  `time_long` varchar(64) NOT NULL COMMENT '时长',
  `add_time` varchar(64) NOT NULL COMMENT '记录时间',
  `device_sn` varchar(32) NOT NULL COMMENT '设备序列号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_user_run
-- ----------------------------
INSERT INTO `cmf_user_run` VALUES ('5', '3', '4565', '', '', '', '1538125733', '0864797033019551');
INSERT INTO `cmf_user_run` VALUES ('6', '4', '1234', '', '', '', '1538125733', '0864797033019551');
INSERT INTO `cmf_user_run` VALUES ('58', '3', '0', '', '', '', '1539850800', '');

-- ----------------------------
-- Table structure for cmf_user_score_log
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_score_log`;
CREATE TABLE `cmf_user_score_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户 id',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `action` varchar(50) NOT NULL DEFAULT '' COMMENT '用户操作名称',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '更改积分，可以为负',
  `coin` int(11) NOT NULL DEFAULT '0' COMMENT '更改金币，可以为负',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户操作积分等奖励日志表';

-- ----------------------------
-- Records of cmf_user_score_log
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_user_sleep
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_sleep`;
CREATE TABLE `cmf_user_sleep` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `deep_sleep` varchar(64) NOT NULL COMMENT '深睡时长',
  `light_sleep` varchar(64) NOT NULL COMMENT '浅睡时长',
  `sleep_time` varchar(64) NOT NULL COMMENT '总睡眠时长',
  `clear_headed` varchar(64) NOT NULL COMMENT '清醒时长',
  `add_time` varchar(64) NOT NULL COMMENT '记录时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_user_sleep
-- ----------------------------

-- ----------------------------
-- Table structure for cmf_user_token
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_token`;
CREATE TABLE `cmf_user_token` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户id',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT ' 过期时间',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `token` varchar(64) NOT NULL DEFAULT '' COMMENT 'token',
  `device_type` varchar(10) NOT NULL DEFAULT '' COMMENT '设备类型;mobile,android,iphone,ipad,web,pc,mac,wxapp',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COMMENT='用户客户端登录 token 表';

-- ----------------------------
-- Records of cmf_user_token
-- ----------------------------
INSERT INTO `cmf_user_token` VALUES ('1', '1', '1553500616', '1537948616', '9551de64de668f9cd61ef6679c6b70a89551de64de668f9cd61ef6679c6b70a8', 'web');
INSERT INTO `cmf_user_token` VALUES ('9', '3', '1541833546', '1539241546', 'd4b612fd4123896741648b46c604a944d4b612fd4123896741648b46c604a944', '');
INSERT INTO `cmf_user_token` VALUES ('10', '3', '1541833558', '1539241558', '2f7bd61edb865fbf40e819ea2ea07e162f7bd61edb865fbf40e819ea2ea07e16', '');
INSERT INTO `cmf_user_token` VALUES ('11', '3', '1541901253', '1539309253', '7dc3761272e2b302df3dca49377c1a857dc3761272e2b302df3dca49377c1a85', '');
INSERT INTO `cmf_user_token` VALUES ('12', '3', '1542444306', '1539309254', '1a68c73b0157752bef4389fa6475d3591a68c73b0157752bef4389fa6475d359', '');
INSERT INTO `cmf_user_token` VALUES ('13', '5', '1542779701', '1540187701', '7ba3bd6fa8db8e45b22d6ccbe51860597ba3bd6fa8db8e45b22d6ccbe5186059', '');
INSERT INTO `cmf_user_token` VALUES ('14', '5', '1542781323', '1540189323', 'c1ba0c862004853255c2a6f4724a6139c1ba0c862004853255c2a6f4724a6139', '');
INSERT INTO `cmf_user_token` VALUES ('15', '5', '1542781395', '1540189395', 'e608436c34051fc86b7b97d9364247e7e608436c34051fc86b7b97d9364247e7', '');
INSERT INTO `cmf_user_token` VALUES ('16', '5', '1542781576', '1540189576', 'f6bf95be08d870108bbd2e816f8f51a8f6bf95be08d870108bbd2e816f8f51a8', '');
INSERT INTO `cmf_user_token` VALUES ('17', '6', '1542782167', '1540190167', '74476169ea74abf9a536f17a5c5d304674476169ea74abf9a536f17a5c5d3046', '');
INSERT INTO `cmf_user_token` VALUES ('18', '3', '1542782396', '1540190396', '05d49808d4bd537ed16253d215356f1205d49808d4bd537ed16253d215356f12', '');
INSERT INTO `cmf_user_token` VALUES ('19', '3', '1542782483', '1540190396', '25153e1d74d06ca4b0a144cad1e5583325153e1d74d06ca4b0a144cad1e55833', '');
INSERT INTO `cmf_user_token` VALUES ('20', '6', '1542783369', '1540191369', '58edf051817d3c90969094bcc27d1b1858edf051817d3c90969094bcc27d1b18', '');
INSERT INTO `cmf_user_token` VALUES ('21', '6', '1542793242', '1540196098', 'b2ef5dad6cfaa6ab191fe86d07e67066b2ef5dad6cfaa6ab191fe86d07e67066', '');
INSERT INTO `cmf_user_token` VALUES ('22', '6', '1542796820', '1540201406', 'c151426549813feee13ae19cbd4a7f8fc151426549813feee13ae19cbd4a7f8f', '');
INSERT INTO `cmf_user_token` VALUES ('23', '6', '1542851945', '1540258099', '5537a607f492962ce184cff1cf9ba19e5537a607f492962ce184cff1cf9ba19e', '');
INSERT INTO `cmf_user_token` VALUES ('24', '3', '1542850988', '1540258988', '42ec1a593fb2be0d46183d171eeac39842ec1a593fb2be0d46183d171eeac398', '');
INSERT INTO `cmf_user_token` VALUES ('25', '3', '1542884070', '1540258995', 'e83e24493521ee39a4f0b8cb7225de4be83e24493521ee39a4f0b8cb7225de4b', '');
INSERT INTO `cmf_user_token` VALUES ('26', '6', '1542851346', '1540259344', 'cfa0d4407919e33ea626f6cd136ca728cfa0d4407919e33ea626f6cd136ca728', '');
INSERT INTO `cmf_user_token` VALUES ('27', '6', '1542856951', '1540263658', '50934433d7f03117b66a4a20f9f32be150934433d7f03117b66a4a20f9f32be1', '');
INSERT INTO `cmf_user_token` VALUES ('28', '6', '1542857620', '1540264958', '3b071dad3f3e7d350afea328a5569a023b071dad3f3e7d350afea328a5569a02', '');
INSERT INTO `cmf_user_token` VALUES ('29', '6', '1542867724', '1540265998', '4fd04988d8390e441efe1939914fbb914fd04988d8390e441efe1939914fbb91', '');
INSERT INTO `cmf_user_token` VALUES ('30', '6', '1542868055', '1540275969', '4e5358c6c572abe2d14488fc2447ca604e5358c6c572abe2d14488fc2447ca60', '');
INSERT INTO `cmf_user_token` VALUES ('31', '6', '1542869302', '1540276075', 'b9f6b92f8b7a75947599fa014a9dacfcb9f6b92f8b7a75947599fa014a9dacfc', '');
INSERT INTO `cmf_user_token` VALUES ('32', '6', '1542869379', '1540277321', 'e97a6d02ef5660efa7d0e774bdfb6db2e97a6d02ef5660efa7d0e774bdfb6db2', '');
INSERT INTO `cmf_user_token` VALUES ('33', '6', '1542869392', '1540277390', '7e324935ca9bb08632966ab70d3120837e324935ca9bb08632966ab70d312083', '');
INSERT INTO `cmf_user_token` VALUES ('34', '6', '1542873372', '1540277405', 'e76029243f2b96bd3c8ac71c77cb6273e76029243f2b96bd3c8ac71c77cb6273', '');
INSERT INTO `cmf_user_token` VALUES ('35', '6', '1542873419', '1540281386', '1e85e93a210f1441a52e05364bd553a41e85e93a210f1441a52e05364bd553a4', '');
INSERT INTO `cmf_user_token` VALUES ('36', '6', '1542873430', '1540281428', '5a9a440160a8bab939ec2133ec4743255a9a440160a8bab939ec2133ec474325', '');
INSERT INTO `cmf_user_token` VALUES ('37', '6', '1542876850', '1540281484', '287132024951b8bf812e39af3f91b4b7287132024951b8bf812e39af3f91b4b7', '');
INSERT INTO `cmf_user_token` VALUES ('38', '6', '1542877083', '1540285081', '6b9b6fdc69a045e46782d8dce80f7b166b9b6fdc69a045e46782d8dce80f7b16', '');
INSERT INTO `cmf_user_token` VALUES ('39', '6', '1542877928', '1540285925', '3cfc42394f59f749e99fba78fa6b1bed3cfc42394f59f749e99fba78fa6b1bed', '');
INSERT INTO `cmf_user_token` VALUES ('40', '6', '1542879127', '1540287125', '882f22ec1093367f7b73a7751995f9f7882f22ec1093367f7b73a7751995f9f7', '');
INSERT INTO `cmf_user_token` VALUES ('41', '6', '1542879699', '1540287697', '81c2bce4c1073f24461ffbfd4ba700e881c2bce4c1073f24461ffbfd4ba700e8', '');
INSERT INTO `cmf_user_token` VALUES ('42', '6', '1542879902', '1540287900', '6ccee45a8bfca9c72b2daaad089d7b616ccee45a8bfca9c72b2daaad089d7b61', '');
INSERT INTO `cmf_user_token` VALUES ('43', '6', '1542880498', '1540288495', '7a676905ffd5d9f75b1d7b308a9112bb7a676905ffd5d9f75b1d7b308a9112bb', '');
INSERT INTO `cmf_user_token` VALUES ('44', '6', '1542880659', '1540288657', '625e3ef21bad743289cc3260caffc291625e3ef21bad743289cc3260caffc291', '');
INSERT INTO `cmf_user_token` VALUES ('45', '6', '1542881320', '1540289317', '9ff719ca2d14e928f5878400d22c7e379ff719ca2d14e928f5878400d22c7e37', '');
INSERT INTO `cmf_user_token` VALUES ('46', '6', '1542882961', '1540290958', 'd5b5b55f3e6b8ef6083be762db2d1c3ed5b5b55f3e6b8ef6083be762db2d1c3e', '');
INSERT INTO `cmf_user_token` VALUES ('47', '6', '1542936579', '1540344515', '88e44898a8f3031dd3c950e073973b7988e44898a8f3031dd3c950e073973b79', '');
INSERT INTO `cmf_user_token` VALUES ('48', '6', '1542936575', '1540344572', '001c345976b1639346fb6a616b6689d1001c345976b1639346fb6a616b6689d1', '');
INSERT INTO `cmf_user_token` VALUES ('49', '6', '1542936892', '1540344889', '177850800589a8a9236599adca371cfe177850800589a8a9236599adca371cfe', '');
INSERT INTO `cmf_user_token` VALUES ('50', '6', '1542936993', '1540344991', 'b4017438c5e8dbd44027ef02d5c90ac4b4017438c5e8dbd44027ef02d5c90ac4', '');
INSERT INTO `cmf_user_token` VALUES ('51', '6', '1542937275', '1540345046', 'ef4bb1f53a0ec658ed7cdd7228a63775ef4bb1f53a0ec658ed7cdd7228a63775', '');
INSERT INTO `cmf_user_token` VALUES ('52', '6', '1542937109', '1540345106', '6d75fbd089cc9f74a605ff02429297cd6d75fbd089cc9f74a605ff02429297cd', '');
INSERT INTO `cmf_user_token` VALUES ('53', '6', '1542937188', '1540345186', '4a9d08a46f686b9da7a51381a0b074894a9d08a46f686b9da7a51381a0b07489', '');
INSERT INTO `cmf_user_token` VALUES ('54', '6', '1542937347', '1540345344', '7a31c3756fe5f248e512eef08f9ac0df7a31c3756fe5f248e512eef08f9ac0df', '');
INSERT INTO `cmf_user_token` VALUES ('55', '6', '1542937460', '1540345457', 'd964e7ef0b25deb5d2f7429130e2f488d964e7ef0b25deb5d2f7429130e2f488', '');
INSERT INTO `cmf_user_token` VALUES ('56', '6', '1542937751', '1540345748', '3118f4f05d8e4ea2d22150956d959b2b3118f4f05d8e4ea2d22150956d959b2b', '');
INSERT INTO `cmf_user_token` VALUES ('57', '6', '1542937802', '1540345799', '12762653e5b859c0769a0bce1abfac1612762653e5b859c0769a0bce1abfac16', '');
INSERT INTO `cmf_user_token` VALUES ('58', '6', '1542938413', '1540346413', 'ae5f75bc4da1af60731fd6632c7e756eae5f75bc4da1af60731fd6632c7e756e', '');
INSERT INTO `cmf_user_token` VALUES ('59', '6', '1542938530', '1540346491', 'e5aeed81b7dd521a2b69d9042fe4075ae5aeed81b7dd521a2b69d9042fe4075a', '');
INSERT INTO `cmf_user_token` VALUES ('60', '6', '1542938750', '1540346540', '3e72e111c2050561c43dc4a87d13c2073e72e111c2050561c43dc4a87d13c207', '');
INSERT INTO `cmf_user_token` VALUES ('61', '6', '1542938714', '1540346714', 'db6385bb47979528e5c52b12dd21909cdb6385bb47979528e5c52b12dd21909c', '');
INSERT INTO `cmf_user_token` VALUES ('62', '6', '1542939171', '1540346745', '6acc7ab693353637376275c2249290626acc7ab693353637376275c224929062', '');
INSERT INTO `cmf_user_token` VALUES ('63', '6', '1542943895', '1540346762', '8fe47322c201494208c584555f8f1f888fe47322c201494208c584555f8f1f88', '');
INSERT INTO `cmf_user_token` VALUES ('64', '6', '1543029651', '1540346843', '76e1527b5a746a927407b1b578670a2f76e1527b5a746a927407b1b578670a2f', '');
INSERT INTO `cmf_user_token` VALUES ('65', '3', '1542939593', '1540347593', '039fcce612440783b57ddbb294da82bb039fcce612440783b57ddbb294da82bb', '');
INSERT INTO `cmf_user_token` VALUES ('66', '3', '1542940110', '1540347594', '67a6195309bbc0a6bcf868552f96fb2467a6195309bbc0a6bcf868552f96fb24', '');
INSERT INTO `cmf_user_token` VALUES ('67', '6', '1542961965', '1540353011', 'c7ae0c56714838b15e5e8403b1889e07c7ae0c56714838b15e5e8403b1889e07', '');
INSERT INTO `cmf_user_token` VALUES ('69', '6', '1543029678', '1540437335', 'e43deba2d15c55cc4914fe1350bc61d0e43deba2d15c55cc4914fe1350bc61d0', '');
INSERT INTO `cmf_user_token` VALUES ('81', '6', '1543117710', '1540518431', 'f3a8a7b742fe65983b0fa07685267eb0f3a8a7b742fe65983b0fa07685267eb0', '');
INSERT INTO `cmf_user_token` VALUES ('83', '6', '1543128526', '1540519389', 'b9135c53e8860e943f5169ea506d31b1b9135c53e8860e943f5169ea506d31b1', '');

-- ----------------------------
-- Table structure for cmf_user_wallet
-- ----------------------------
DROP TABLE IF EXISTS `cmf_user_wallet`;
CREATE TABLE `cmf_user_wallet` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `wallet_url` varchar(128) NOT NULL COMMENT '钱包地址',
  `remark` varchar(64) NOT NULL COMMENT '备注',
  `third_party` varchar(255) NOT NULL COMMENT '第三方',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of cmf_user_wallet
-- ----------------------------
INSERT INTO `cmf_user_wallet` VALUES ('1', '3', 'qqqqqq', 'ssss', 'ffff');

-- ----------------------------
-- Table structure for cmf_verification_code
-- ----------------------------
DROP TABLE IF EXISTS `cmf_verification_code`;
CREATE TABLE `cmf_verification_code` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '当天已经发送成功的次数',
  `send_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后发送成功时间',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '验证码过期时间',
  `code` varchar(8) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '最后发送成功的验证码',
  `account` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '手机号或者邮箱',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='手机邮箱数字验证码表';

-- ----------------------------
-- Records of cmf_verification_code
-- ----------------------------
INSERT INTO `cmf_verification_code` VALUES ('1', '1', '1540362103', '1540363903', '1591', '15757851183');
INSERT INTO `cmf_verification_code` VALUES ('2', '4', '1540454358', '1540456158', '2341', '18814374961');
INSERT INTO `cmf_verification_code` VALUES ('3', '1', '1540177822', '1540179622', '3686', '15228554610');
