/*
 Navicat MySQL Data Transfer

 Source Server         : localWeb
 Source Server Type    : MySQL
 Source Server Version : 50548
 Source Host           : 192.168.2.40
 Source Database       : winwin

 Target Server Type    : MySQL
 Target Server Version : 50548
 File Encoding         : utf-8

 Date: 05/06/2017 20:11:16 PM
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `conf_carrier_commission_agent`
-- ----------------------------
DROP TABLE IF EXISTS `conf_carrier_commission_agent`;
CREATE TABLE `conf_carrier_commission_agent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL COMMENT '运营商id',
  `agent_level_id` int(11) NOT NULL COMMENT '代理类型名称ID',
  `deposit_fee_undertake_ratio` decimal(5,2) DEFAULT '0.00' COMMENT '存款手续费承担比例',
  `deposit_fee_undertake_max` int(11) DEFAULT '0' COMMENT '存款手续费承担上限',
  `deposit_preferential_undertake_ratio` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '代理存款优惠承担比例 0不承担',
  `deposit_preferential_undertake_max` int(11) NOT NULL DEFAULT '0' COMMENT '代理存款优惠最高承担金额  0表示无上限',
  `rebate_financial_flow_undertake_ratio` decimal(5,2) DEFAULT '0.00' COMMENT '承担返水比例 0表示无上限',
  `rebate_financial_flow_undertake_max` int(11) NOT NULL DEFAULT '0' COMMENT '返水承担上线 0无上限',
  `bonus_undertake_ratio` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '红利承担比例 0无上限',
  `bonus_undertake_max` int(11) NOT NULL DEFAULT '0' COMMENT '红利承担上限  0表示无上限',
  `available_member_monthly_bet_amount` int(11) NOT NULL DEFAULT '0' COMMENT '有效会员当月投注额',
  `available_member_count` int(11) NOT NULL DEFAULT '0' COMMENT '代理佣金有效会员总数',
  `max_commission_amount_per_time` int(11) NOT NULL DEFAULT '0' COMMENT '总佣金单次限额',
  `commission_ratio` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '总佣金比例',
  `commission_step_ratio` varchar(500) DEFAULT NULL COMMENT '总佣金阶梯比例， json格式： 格式待确定',
  `sub_commission_ratio` decimal(5,2) DEFAULT '0.00' COMMENT '下级代理佣金提成比例',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '修改时间',
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='代理佣金设置';

-- ----------------------------
--  Table structure for `conf_carrier_commission_agent_platform_fee`
-- ----------------------------
DROP TABLE IF EXISTS `conf_carrier_commission_agent_platform_fee`;
CREATE TABLE `conf_carrier_commission_agent_platform_fee` (
  `id` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL COMMENT '运营商id',
  `agent_level_id` int(11) NOT NULL,
  `carrier_game_plat_id` int(11) DEFAULT '0' COMMENT '运营商开放的游戏平台id',
  `platform_fee_max` int(11) DEFAULT '0' COMMENT '平台费上限',
  `platform_fee_rate` decimal(5,2) DEFAULT '0.00' COMMENT '平台费比例%',
  `agent_rebate_financial_flow_rate` decimal(5,2) DEFAULT '0.00' COMMENT '代理洗码比例',
  `agent_rebate_financial_flow_max_amount` int(11) DEFAULT '0' COMMENT '代理洗码上限',
  `computing_mode` tinyint(1) DEFAULT '1' COMMENT '计算模式',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8 COMMENT='佣金代理平台费设置';

-- ----------------------------
--  Table structure for `conf_carrier_cost_take_agent`
-- ----------------------------
DROP TABLE IF EXISTS `conf_carrier_cost_take_agent`;
CREATE TABLE `conf_carrier_cost_take_agent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL COMMENT '运营商id',
  `agent_level_id` int(11) NOT NULL COMMENT '代理类型名称ID',
  `deposit_fee_undertake_ratio` decimal(5,2) DEFAULT '0.00' COMMENT '存款手续费承担比例',
  `deposit_fee_undertake_max` int(11) DEFAULT '0' COMMENT '存款手续费承担上限',
  `deposit_preferential_undertake_ratio` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '代理存款优惠承担比例 0不承担',
  `deposit_preferential_undertake_max` int(11) NOT NULL DEFAULT '0' COMMENT '代理存款优惠最高承担金额  0表示无上限',
  `rebate_financial_flow_undertake_ratio` decimal(5,2) DEFAULT '0.00' COMMENT '承担返水比例 0表示无上限',
  `rebate_financial_flow_undertake_max` int(11) NOT NULL DEFAULT '0' COMMENT '返水承担上线 0无上限',
  `bonus_undertake_ratio` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT '红利承担比例 0无上限',
  `bonus_undertake_max` int(11) NOT NULL DEFAULT '0' COMMENT '红利承担上限  0表示无上限',
  `can_player_join_activity` tinyint(1) DEFAULT '0' COMMENT '会员是否跟随网站优惠活动(是否能够参加优惠活动)',
  `is_player_rebate_financial_adapt_carrier_conf` tinyint(1) DEFAULT '0' COMMENT '会员是否跟随网站洗码优惠(玩家洗码配置是否按照运营商配置计算)',
  `cost_take_ration` decimal(5,2) DEFAULT '0.00' COMMENT '占成比例',
  `protection_fund` int(11) DEFAULT '0' COMMENT '保障金',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '修改时间',
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='占成设置';

-- ----------------------------
--  Table structure for `conf_carrier_cost_take_agent_platform_fee`
-- ----------------------------
DROP TABLE IF EXISTS `conf_carrier_cost_take_agent_platform_fee`;
CREATE TABLE `conf_carrier_cost_take_agent_platform_fee` (
  `id` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL COMMENT '运营商id',
  `agent_level_id` int(11) NOT NULL,
  `carrier_game_plat_id` int(11) DEFAULT '0' COMMENT '运营商开放的游戏平台id',
  `platform_fee_max` int(11) DEFAULT '0' COMMENT '平台费上限',
  `platform_fee_rate` decimal(5,2) DEFAULT '0.00' COMMENT '平台费比例%',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8 COMMENT='占成代理平台费设置';

-- ----------------------------
--  Table structure for `conf_carrier_deposit`
-- ----------------------------
DROP TABLE IF EXISTS `conf_carrier_deposit`;
CREATE TABLE `conf_carrier_deposit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL,
  `is_allow_player_deposit` tinyint(1) NOT NULL DEFAULT '1' COMMENT '玩家是否允许存款',
  `is_allow_agent_deposit` tinyint(1) NOT NULL DEFAULT '1' COMMENT '代理是否允许存款',
  `is_allow_third_part_deposit_auto_arrival` tinyint(1) NOT NULL DEFAULT '0' COMMENT '三方存款是否允许自动到账, 如果是 则不需要客服审核即可到账. 如果有优惠自动给玩家返优惠',
  `unreview_deposit_record_limit` int(11) NOT NULL DEFAULT '0' COMMENT '允许未审核存款条数：设置条数，超出的自动过期消失',
  `third_part_deposit_is_open` tinyint(1) NOT NULL DEFAULT '1' COMMENT '三方存款是否开启',
  `company_deposit_is_open` tinyint(1) NOT NULL DEFAULT '1' COMMENT '公司存款是否开启:公司包括转账汇款，扫码支付（公司入款）',
  `is_allow_company_deposit_auto_arrival` tinyint(1) NOT NULL DEFAULT '0' COMMENT '公司存款是否自动到账；是或否（公司存款方式肯定是否，一般都是要审核的）',
  `virtual_card_deposit_is_open` tinyint(1) NOT NULL DEFAULT '1' COMMENT '点卡存款是否开启',
  `is_allow_virtual_card_deposit_auto_arrival` tinyint(1) NOT NULL DEFAULT '0' COMMENT '点卡存款是否自动到账',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `CONF_DEPOSIT_CARRIER_idx` (`carrier_id`),
  CONSTRAINT `CONF_DEPOSIT_CARRIER` FOREIGN KEY (`carrier_id`) REFERENCES `inf_carrier` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='运营商存款设置';

-- ----------------------------
--  Table structure for `conf_carrier_invite_player`
-- ----------------------------
DROP TABLE IF EXISTS `conf_carrier_invite_player`;
CREATE TABLE `conf_carrier_invite_player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) DEFAULT NULL,
  `bet_reward_rule` text COMMENT '投注额奖励规则',
  `bet_reward_settle_period` int(11) DEFAULT '7' COMMENT '投注额奖励结算周期  按天还是按周计算  默认 7天(一周)',
  `deposit_reward_rule` text COMMENT '存款额奖励规则',
  `deposit_reward_settle_period` int(11) DEFAULT '7' COMMENT '存款额奖励结算周期  按天还是按周计算  默认 7天(一周)',
  `invalid_player_deposit_amount` decimal(11,2) DEFAULT '0.00' COMMENT '有效会员达到的存款金额条件',
  `invalid_player_bet_amount` decimal(11,2) DEFAULT '0.00' COMMENT '有效会员达到的投注金额条件',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='邀请好友设置';

-- ----------------------------
--  Table structure for `conf_carrier_password_recovery_site`
-- ----------------------------
DROP TABLE IF EXISTS `conf_carrier_password_recovery_site`;
CREATE TABLE `conf_carrier_password_recovery_site` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) DEFAULT NULL COMMENT '运营商ID',
  `is_open_email_send_function` tinyint(1) DEFAULT '1' COMMENT '是否启用邮件发送功能',
  `smtp_server` varchar(50) DEFAULT NULL COMMENT 'smtp服务器',
  `smtp_service_port` int(11) DEFAULT NULL COMMENT 'smtp服务器端口',
  `mail_sender` varchar(50) DEFAULT NULL COMMENT '邮件发送人',
  `smtp_username` varchar(50) DEFAULT NULL COMMENT 'smtp用户名',
  `smtp_password` varchar(50) DEFAULT NULL COMMENT 'smtp密码',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='运营商密码找回配置表';

-- ----------------------------
--  Table structure for `conf_carrier_rebate_financial_flow_agent`
-- ----------------------------
DROP TABLE IF EXISTS `conf_carrier_rebate_financial_flow_agent`;
CREATE TABLE `conf_carrier_rebate_financial_flow_agent` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL COMMENT '运营商id',
  `agent_level_id` int(11) unsigned NOT NULL COMMENT '代理类型名称id',
  `carrier_game_plat_id` int(11) unsigned NOT NULL COMMENT '运营商开放的游戏平台id',
  `agent_rebate_financial_flow_max_amount` decimal(11,2) unsigned DEFAULT '0.00' COMMENT '代理洗码上限',
  `agent_rebate_financial_flow_rate` decimal(5,2) DEFAULT '0.00' COMMENT '代理洗码比例',
  `player_rebate_financial_flow_rate` decimal(5,2) DEFAULT '0.00' COMMENT '玩家洗码比例',
  `player_rebate_financial_flow_max_amount` decimal(11,2) DEFAULT '0.00' COMMENT '玩家洗码上限',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agent_level_id` (`agent_level_id`,`carrier_game_plat_id`) USING BTREE,
  UNIQUE KEY `carrier_game_plat_id` (`carrier_game_plat_id`,`agent_level_id`) USING BTREE,
  CONSTRAINT `conf_carrier_rebate_financial_flow_agent_ibfk_1` FOREIGN KEY (`agent_level_id`) REFERENCES `inf_carrier_agent_level` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `conf_carrier_rebate_financial_flow_agent_ibfk_2` FOREIGN KEY (`carrier_game_plat_id`) REFERENCES `map_carrier_game_plats` (`game_plat_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='第一级洗码代理类型游戏平台设置';

-- ----------------------------
--  Table structure for `conf_carrier_rebate_financial_flow_agent_base_info`
-- ----------------------------
DROP TABLE IF EXISTS `conf_carrier_rebate_financial_flow_agent_base_info`;
CREATE TABLE `conf_carrier_rebate_financial_flow_agent_base_info` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL COMMENT '运营商id',
  `agent_level_id` int(11) NOT NULL,
  `available_member_monthly_bet_amount` int(11) DEFAULT '0' COMMENT '有效会员当月投注额',
  `available_member_count` int(11) DEFAULT '0' COMMENT '有效会员数',
  `is_player_rebate_financial_adapt_carrier_conf` tinyint(1) DEFAULT '0' COMMENT '会员是否跟随网站洗码优惠(玩家洗码配置是否按照运营商配置计算)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='洗码代理基本设置';

-- ----------------------------
--  Table structure for `conf_carrier_register_login`
-- ----------------------------
DROP TABLE IF EXISTS `conf_carrier_register_login`;
CREATE TABLE `conf_carrier_register_login` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '运营商登陆注册类设置表',
  `carrier_id` int(11) NOT NULL DEFAULT '1' COMMENT '所属运营商',
  `forbidden_login_comment` varchar(55) NOT NULL DEFAULT '密码输入错误超过5次,账号被锁定,有问题请联系客服人员' COMMENT '后台禁止登陆提示原因',
  `carrier_login_failed_count_when_locked` tinyint(1) NOT NULL DEFAULT '5' COMMENT '后台登陆错误导致锁定的次数 0不锁定',
  `is_allow_player_login` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否允许玩家登陆',
  `is_allow_player_register` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否允许玩家注册',
  `player_login_failed_count_when_locked` tinyint(1) NOT NULL DEFAULT '5' COMMENT '玩家登陆失败锁定时的错误次数',
  `player_login_failed_locked_time` int(11) NOT NULL DEFAULT '5' COMMENT '用户登录错误锁定时间',
  `player_register_forbidden_user_names` varchar(500) DEFAULT 'admin,root' COMMENT '玩家注册限制用户名 逗号分隔多个用户名',
  `player_forbidden_login_comment` varchar(255) DEFAULT '密码输入错误超过5次,账号被锁定,有问题请联系客服人员' COMMENT '玩家禁止登陆原因',
  `player_forbidden_register_comment` varchar(255) DEFAULT '注册系统升级中,有疑问请联系客服' COMMENT '玩家禁止注册原因',
  `is_check_exist_player_real_user_name` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否检测玩家真实姓名是否同名',
  `is_allow_user_withdraw_with_password` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否允许玩家取款时输入取款密码,如果允许 则取款时需要输入取款密码并且需要用户设置取款密码.',
  `is_allow_agent_login` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否允许代理登陆',
  `is_allow_agent_register` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否允许代理注册',
  `agent_login_failed_count_when_locked` tinyint(1) NOT NULL DEFAULT '5' COMMENT '当代理登陆失败锁定时的登陆次数',
  `agent_login_failed_locked_time` int(11) NOT NULL DEFAULT '5' COMMENT '代理登录错误锁定时间',
  `agent_register_forbidden_user_names` varchar(255) NOT NULL DEFAULT 'admin,root' COMMENT '代理注册禁止注册的用户名列表 逗号分隔',
  `agent_forbidden_login_comment` varchar(55) NOT NULL DEFAULT '密码输入错误超过5次,账号被锁定,有问题请联系客服人员' COMMENT '代理禁止登陆原因',
  `agent_forbidden_register_comment` varchar(55) NOT NULL DEFAULT '注册系统升级中,有疑问请联系客服' COMMENT '代理禁止注册原因',
  `is_allow_agent_withdraw_with_password` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否允许代理取款时输入取款密码,如果允许 则取款时需要输入取款密码并且需要用户设置取款密码.',
  `is_check_exist_agent_real_user_name` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否检测代理真实姓名是否存在',
  `player_birthday_conf_status` int(1) DEFAULT NULL COMMENT '玩家生日配置项状态(0:无状态;1:显示;2:必填;多种情况下进行按位且运算判断',
  `player_realname_conf_status` int(1) DEFAULT NULL COMMENT '玩家真实姓名配置项状态',
  `player_email_conf_status` int(1) DEFAULT '0' COMMENT '玩家邮箱配置项状态',
  `player_phone_conf_status` int(1) DEFAULT NULL COMMENT '玩家手机配置项状态',
  `player_sex_conf_status` int(1) DEFAULT NULL COMMENT '玩家性别配置项状态',
  `player_qq_conf_status` int(1) DEFAULT NULL COMMENT '玩家qq配置项状态',
  `player_wechat_conf_status` int(1) DEFAULT NULL COMMENT '玩家微信配置项状态',
  `agent_type_conf_status` int(1) DEFAULT NULL COMMENT '代理类型配置项状态',
  `agent_realname_conf_status` int(1) DEFAULT NULL COMMENT '代理真实姓名配置项状态',
  `agent_birthday_conf_status` int(1) DEFAULT NULL COMMENT '代理生日配置项状态',
  `agent_email_conf_status` int(1) DEFAULT NULL COMMENT '代理邮箱配置项状态',
  `agent_phone_conf_status` int(1) DEFAULT NULL COMMENT '代理手机配置项状态',
  `agent_qq_conf_status` int(1) DEFAULT NULL COMMENT '代理qq配置项状态',
  `agent_skype_conf_status` int(1) DEFAULT NULL COMMENT '代理skype配置项状态',
  `agent_wechat_conf_status` int(1) DEFAULT NULL COMMENT '代理微信配置项状态',
  `agent_promotion_url_conf_status` int(1) DEFAULT NULL COMMENT '代理推广网址配置项状态',
  `agent_promotion_idea_conf_status` int(1) DEFAULT NULL COMMENT '代理推广想法配置项状态',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `DASH_SETTING_CARRIER_ID_idx` (`carrier_id`),
  CONSTRAINT `CARRIER_SETTING_LOGIN_CARRIER_ID` FOREIGN KEY (`carrier_id`) REFERENCES `inf_carrier` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='运营商登陆注册类设置表';

-- ----------------------------
--  Table structure for `conf_carrier_subordinate_agent_commission`
-- ----------------------------
DROP TABLE IF EXISTS `conf_carrier_subordinate_agent_commission`;
CREATE TABLE `conf_carrier_subordinate_agent_commission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) unsigned DEFAULT NULL COMMENT '运营商ID',
  `agent_id` int(11) unsigned DEFAULT NULL COMMENT '代理ID',
  `commission_ratio` decimal(5,2) DEFAULT '0.00' COMMENT '总佣金比例',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='运营商下级代理佣金设置';

-- ----------------------------
--  Table structure for `conf_carrier_third_part_pay`
-- ----------------------------
DROP TABLE IF EXISTS `conf_carrier_third_part_pay`;
CREATE TABLE `conf_carrier_third_part_pay` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL,
  `def_pay_channel_id` int(11) NOT NULL COMMENT '三方支付平台ID',
  `merchant_number` varchar(50) DEFAULT NULL COMMENT '商户号',
  `merchant_bind_domain` varchar(255) NOT NULL COMMENT '商户绑定域名',
  `public_key` text COMMENT '公钥',
  `private_key` text COMMENT '私钥',
  `vir_card_no_in` varchar(50) DEFAULT NULL COMMENT '国付宝转入账户',
  `merchant_identify_code` varchar(45) DEFAULT NULL COMMENT '商户识别码',
  `good_name` varchar(50) DEFAULT NULL,
  `pay_ids_json` varchar(50) DEFAULT NULL COMMENT '账户支付渠道',
  `created_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='运营商三方支付接口配置';

-- ----------------------------
--  Table structure for `conf_carrier_web_banners`
-- ----------------------------
DROP TABLE IF EXISTS `conf_carrier_web_banners`;
CREATE TABLE `conf_carrier_web_banners` (
  `banner_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) DEFAULT NULL COMMENT '所属运营商',
  `banner_name` varchar(255) DEFAULT NULL COMMENT 'Banner名称',
  `banner_image_id` int(11) DEFAULT NULL COMMENT '网站图片id',
  `sort` int(11) DEFAULT '1' COMMENT '排序',
  `banner_belong_page` tinyint(1) NOT NULL COMMENT '所属页面 \n1 ''首页'',\n2 ''真人娱乐页'',\n3 ''彩票页面'',\n4 ''电子游戏页'',\n5 ''体育游戏页'',\n6 ''优惠活动页'',\n7 ''帮助页'',\n8 ‘合营代理页''',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`banner_id`),
  KEY `CARRIER_IMAGE_ID_idx` (`banner_image_id`)
) ENGINE=InnoDB AUTO_INCREMENT=246 DEFAULT CHARSET=utf8 COMMENT='运营商网站banner配置表';

-- ----------------------------
--  Table structure for `conf_carrier_web_site`
-- ----------------------------
DROP TABLE IF EXISTS `conf_carrier_web_site`;
CREATE TABLE `conf_carrier_web_site` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL COMMENT '所属运营商',
  `site_title` varchar(50) NOT NULL COMMENT '网站标题',
  `site_key_words` varchar(255) DEFAULT NULL COMMENT '网站关键词',
  `site_description` varchar(255) DEFAULT NULL COMMENT '网站描述',
  `site_javascript` text COMMENT '网站js',
  `site_notice` text COMMENT '网站公告',
  `site_footer_comment` text COMMENT '网站底部说明',
  `common_question_file_path` varchar(255) DEFAULT NULL COMMENT '常见问题文件目录',
  `contact_us_file_path` varchar(255) DEFAULT NULL COMMENT '联系我们文件目录',
  `privacy_policy_file_path` varchar(255) DEFAULT NULL COMMENT '隐私政策文件目录',
  `rule_clause_file_path` varchar(255) DEFAULT NULL COMMENT '规则条款文件目录',
  `with_draw_comment_file_path` varchar(255) DEFAULT NULL COMMENT '提款说明文件目录',
  `net_bank_deposit_comment` varchar(255) DEFAULT NULL COMMENT '网银存款说明',
  `atm_deposit_comment` varchar(255) DEFAULT NULL COMMENT 'ATM存款说明',
  `third_part_deposit_comment` varchar(255) DEFAULT NULL COMMENT '第三方存款说明',
  `commission_policy_file_path` varchar(255) DEFAULT NULL COMMENT '佣金政策文件目录',
  `jointly_operated_agreement_file_path` varchar(255) DEFAULT NULL COMMENT '合营协议文件目录',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `activity_image_resolution` varchar(45) DEFAULT NULL COMMENT '活动图片分辨率 按照*分隔  例如 1024*768 ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='运营商网站基本配置';

-- ----------------------------
--  Table structure for `conf_carrier_withdraw`
-- ----------------------------
DROP TABLE IF EXISTS `conf_carrier_withdraw`;
CREATE TABLE `conf_carrier_withdraw` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL COMMENT '运营商ID',
  `is_allow_player_withdraw` tinyint(1) DEFAULT '1' COMMENT '是否允许玩家取款',
  `is_allow_player_withdraw_decimal` tinyint(1) DEFAULT '0' COMMENT '是否允许玩家取款小数:如(0.88)',
  `player_day_withdraw_success_limit_count` tinyint(1) DEFAULT NULL COMMENT '玩家单日取款成功限制次数',
  `player_day_withdraw_max_sum` int(11) DEFAULT NULL COMMENT '玩家单日取款最大金额',
  `player_once_withdraw_max_sum` int(11) DEFAULT NULL COMMENT '玩家单次取款最大金额',
  `player_once_withdraw_min_sum` int(11) DEFAULT NULL COMMENT '玩家单次取款最小金额',
  `is_display_flow_water_check` tinyint(1) DEFAULT '1' COMMENT '是否显示流水提示:开启后在取款页面有流水限制的提示，未完成的提示已完成流水多少。',
  `is_open_risk_management_check` tinyint(1) DEFAULT '1' COMMENT '是否开启风控审核',
  `is_check_flow_water_when_withdraw` tinyint(1) DEFAULT '1' COMMENT '取款是否检测流水，完成的可取款，未完成的提示流水未完成，不能提款',
  `is_allow_agent_withdraw` tinyint(1) DEFAULT '1' COMMENT '是否允许代理取款',
  `is_allow_agent_withdraw_decimal` tinyint(1) DEFAULT '0' COMMENT '是否允许取款小数',
  `agent_day_withdraw_success_limit_count` tinyint(1) DEFAULT NULL COMMENT '代理单日取款成功限制次数',
  `agent_day_withdraw_max_sum` int(11) DEFAULT NULL COMMENT '代理单日取款最大金额',
  `agent_once_withdraw_max_sum` int(11) DEFAULT NULL COMMENT '代理单次取款最大金额',
  `agent_once_withdraw_min_sum` int(11) DEFAULT NULL COMMENT '代理单次取款最小金额',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='运营商取款设置表';

-- ----------------------------
--  Table structure for `conf_rebate_financial_flow_agent_game_plat`
-- ----------------------------
DROP TABLE IF EXISTS `conf_rebate_financial_flow_agent_game_plat`;
CREATE TABLE `conf_rebate_financial_flow_agent_game_plat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL DEFAULT '0' COMMENT '运营商ID',
  `agent_id` int(11) NOT NULL DEFAULT '0' COMMENT '代理id',
  `carrier_game_plat_id` int(11) DEFAULT '0' COMMENT '运营商开放的游戏平台id',
  `agent_rebate_financial_flow_max_amount` decimal(11,2) DEFAULT '0.00' COMMENT '代理洗码上限',
  `agent_rebate_financial_flow_rate` decimal(5,2) DEFAULT '0.00' COMMENT '代理洗码比例',
  `player_rebate_financial_flow_rate` decimal(5,2) DEFAULT '0.00' COMMENT '玩家洗码比例',
  `player_rebate_financial_flow_max_amount` decimal(11,2) DEFAULT '0.00' COMMENT '玩家洗码上限',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='下级洗码代理游戏平台比例设置';

-- ----------------------------
--  Table structure for `def_bank_types`
-- ----------------------------
DROP TABLE IF EXISTS `def_bank_types`;
CREATE TABLE `def_bank_types` (
  `type_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(150) NOT NULL COMMENT '银行卡名称 如 中国农业银行,微信',
  `bank_type` tinyint(1) NOT NULL DEFAULT '1',
  `bank_background_url` varchar(255) DEFAULT NULL COMMENT '银行背景图片路径',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`type_id`),
  UNIQUE KEY `type_id_UNIQUE` (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='银行卡类型';

-- ----------------------------
--  Table structure for `def_game_plats`
-- ----------------------------
DROP TABLE IF EXISTS `def_game_plats`;
CREATE TABLE `def_game_plats` (
  `game_plat_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `main_game_plat_id` int(11) unsigned DEFAULT NULL COMMENT '所属主游戏平台id',
  `english_game_plat_name` varchar(50) NOT NULL,
  `game_plat_name` varchar(50) NOT NULL COMMENT '游戏平台名称',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态 1 打开  0关闭',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`game_plat_id`),
  KEY `main_game_plat_id` (`main_game_plat_id`),
  CONSTRAINT `def_game_plats_ibfk_1` FOREIGN KEY (`main_game_plat_id`) REFERENCES `def_main_game_plats` (`main_game_plat_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='游戏平台表';

-- ----------------------------
--  Table structure for `def_games`
-- ----------------------------
DROP TABLE IF EXISTS `def_games`;
CREATE TABLE `def_games` (
  `game_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `game_plat_id` int(11) unsigned NOT NULL COMMENT '所属游戏平台id',
  `english_game_name` varchar(255) NOT NULL,
  `game_name` varchar(255) NOT NULL COMMENT '游戏名称',
  `game_code` varchar(45) DEFAULT NULL COMMENT '游戏代码',
  `game_lines` int(11) DEFAULT NULL COMMENT '游戏线路',
  `game_icon_path` varchar(255) DEFAULT NULL COMMENT '游戏图标路径',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态 1正常  0关闭',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`game_id`),
  UNIQUE KEY `game_code` (`game_code`) USING BTREE,
  KEY `game_plat_id` (`game_plat_id`),
  CONSTRAINT `def_games_ibfk_1` FOREIGN KEY (`game_plat_id`) REFERENCES `def_game_plats` (`game_plat_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=189 DEFAULT CHARSET=utf8 COMMENT='游戏列表';

-- ----------------------------
--  Table structure for `def_main_game_plats`
-- ----------------------------
DROP TABLE IF EXISTS `def_main_game_plats`;
CREATE TABLE `def_main_game_plats` (
  `main_game_plat_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `main_game_plat_code` varchar(20) NOT NULL COMMENT '主平台代码',
  `main_game_plat_name` varchar(255) DEFAULT NULL COMMENT '主游戏平台名称',
  `status` tinyint(1) DEFAULT '1' COMMENT '游戏主平台状态 1 正常  0关闭',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`main_game_plat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='主游戏平台表';

-- ----------------------------
--  Table structure for `def_pay_channel_list`
-- ----------------------------
DROP TABLE IF EXISTS `def_pay_channel_list`;
CREATE TABLE `def_pay_channel_list` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `channel_name` varchar(150) NOT NULL COMMENT '银行卡名称 如 中国农业银行,微信',
  `channel_code` varchar(100) NOT NULL COMMENT '编码',
  `pay_channel_type_id` tinyint(3) NOT NULL DEFAULT '1' COMMENT '银行类型  \n1   传统银行 如:中国农业银行\n2  第三方支付 如:微信\n3  网络银行 如:网商银行',
  `is_need_private_key` tinyint(1) DEFAULT '0' COMMENT '是否需要填写私钥',
  `is_need_merchant_code` tinyint(1) DEFAULT '0' COMMENT '是否需要填写商户号',
  `icon_path_url` varchar(255) DEFAULT NULL COMMENT '支付渠道图标',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_id_UNIQUE` (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `def_pay_channel_type`
-- ----------------------------
DROP TABLE IF EXISTS `def_pay_channel_type`;
CREATE TABLE `def_pay_channel_type` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL COMMENT '银行类型名称',
  `parent_id` int(11) DEFAULT '0' COMMENT '父类ID',
  `sort` tinyint(3) DEFAULT '0' COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='支付渠道类型表';

-- ----------------------------
--  Table structure for `def_pins`
-- ----------------------------
DROP TABLE IF EXISTS `def_pins`;
CREATE TABLE `def_pins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `failed_jobs`
-- ----------------------------
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8_unicode_ci NOT NULL,
  `queue` text COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22817 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Table structure for `inf_admin_user`
-- ----------------------------
DROP TABLE IF EXISTS `inf_admin_user`;
CREATE TABLE `inf_admin_user` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL COMMENT '用户名',
  `password` varchar(128) DEFAULT NULL,
  `mobile` char(11) DEFAULT NULL COMMENT '手机号码',
  `email` varchar(50) DEFAULT NULL COMMENT 'email',
  `status` tinyint(1) DEFAULT '1' COMMENT '1 正常 -1关闭',
  `last_login_time` datetime DEFAULT NULL COMMENT '最后一次登录时间',
  `login_ip` int(11) DEFAULT NULL COMMENT ' 登录IP',
  `parent_id` int(11) DEFAULT NULL COMMENT '父ID',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='用户基本表';

-- ----------------------------
--  Table structure for `inf_agent`
-- ----------------------------
DROP TABLE IF EXISTS `inf_agent`;
CREATE TABLE `inf_agent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) DEFAULT NULL COMMENT '用户名',
  `password` varchar(100) DEFAULT NULL COMMENT '密码',
  `realname` varchar(64) DEFAULT NULL COMMENT '真实姓名',
  `agent_level_id` int(11) DEFAULT '0' COMMENT '代理层级ID',
  `amount` decimal(11,2) DEFAULT '0.00' COMMENT '代理余额',
  `pay_password` varchar(64) DEFAULT NULL COMMENT '取款密码',
  `experience_amount` decimal(11,2) DEFAULT '0.00' COMMENT '会员礼金',
  `player_number` int(11) DEFAULT NULL COMMENT '下线玩家数量',
  `birthday` date DEFAULT NULL COMMENT '出生日期',
  `skype` varchar(100) DEFAULT NULL COMMENT 'skype账号',
  `qq` varchar(30) DEFAULT NULL COMMENT 'QQ',
  `wechat` varchar(50) DEFAULT NULL COMMENT '微信',
  `mobile` varchar(15) DEFAULT NULL COMMENT '手机号',
  `email` varchar(50) DEFAULT NULL COMMENT '邮箱',
  `promotion_url` varchar(50) DEFAULT NULL COMMENT '代理推广网址',
  `promotion_url_click_number` int(11) unsigned DEFAULT '0' COMMENT '代理推广网址点击次数',
  `promotion_notion` varchar(255) DEFAULT NULL COMMENT '代理推广介绍',
  `promotion_code` varchar(50) DEFAULT NULL COMMENT '推广码',
  `parent_id` int(11) DEFAULT '0' COMMENT '代理商父ID 介绍人',
  `carrier_id` int(11) DEFAULT NULL COMMENT '运营商ID',
  `status` tinyint(1) DEFAULT '0' COMMENT '代理商账号状态 1 启用 0, 禁用',
  `audit_status` tinyint(1) DEFAULT '0' COMMENT '客服审核状态 1已审核 =0审核中 2拒绝',
  `is_default` tinyint(1) DEFAULT '0' COMMENT '运营商默认代理 1是 0不是',
  `customer_remark` varchar(255) DEFAULT NULL COMMENT '客服备注',
  `customer_time` timestamp NULL DEFAULT NULL COMMENT '客服处理时间',
  `login_time` datetime DEFAULT NULL COMMENT '登录时间',
  `register_ip` varchar(15) DEFAULT NULL COMMENT '注册IP',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '注册时间',
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `operator_id` (`carrier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8 COMMENT='代理商信息表';

-- ----------------------------
--  Table structure for `inf_agent_bank_cards`
-- ----------------------------
DROP TABLE IF EXISTS `inf_agent_bank_cards`;
CREATE TABLE `inf_agent_bank_cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) unsigned NOT NULL COMMENT '运营商ID',
  `agent_id` int(11) NOT NULL COMMENT '代理ID',
  `card_account` varchar(50) DEFAULT NULL COMMENT '取款账号',
  `card_type` tinyint(3) DEFAULT NULL COMMENT '银行卡类型',
  `card_owner_name` varchar(50) DEFAULT NULL COMMENT '持卡人姓名',
  `card_birth_place` varchar(255) DEFAULT NULL COMMENT '开户行地址',
  `status` tinyint(1) DEFAULT '1' COMMENT '是否有效 0无效 1有效',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agent_id` (`agent_id`) USING BTREE,
  UNIQUE KEY `agent_card_account` (`card_account`) USING BTREE,
  CONSTRAINT `inf_agent_bamk_cards_1` FOREIGN KEY (`agent_id`) REFERENCES `inf_agent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='代理银行卡表';

-- ----------------------------
--  Table structure for `inf_agent_domain`
-- ----------------------------
DROP TABLE IF EXISTS `inf_agent_domain`;
CREATE TABLE `inf_agent_domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL COMMENT '代理ID',
  `carrier_id` int(11) NOT NULL COMMENT '运营商ID',
  `website` varchar(255) NOT NULL COMMENT '推广域名',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '添加时间',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='代理推广域名';

-- ----------------------------
--  Table structure for `inf_carrier`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier`;
CREATE TABLE `inf_carrier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT '运营商名称',
  `site_url` varchar(255) DEFAULT NULL COMMENT '站点地址',
  `is_forbidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否禁用 1是  0否',
  `remain_quota` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '当前额度',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `inf_carrier_activity`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier_activity`;
CREATE TABLE `inf_carrier_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL DEFAULT '0' COMMENT '运营商ID',
  `act_type_id` int(11) NOT NULL DEFAULT '0' COMMENT '优惠活动类型ID',
  `name` varchar(100) NOT NULL COMMENT '活动名称',
  `sort` tinyint(2) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) DEFAULT '0' COMMENT '活动状态 1 上架 0下架',
  `current_deposit_amount` int(11) DEFAULT '0' COMMENT '当前存款额',
  `bonuses_type` tinyint(1) DEFAULT '0' COMMENT '红利(返奖)类型 1百分比 2固定金额',
  `rebate_financial_bonuses_step_rate_json` varchar(500) DEFAULT NULL COMMENT '红利类型阶梯比例 josn',
  `flow_want_pattern` tinyint(1) DEFAULT '1' COMMENT '流水要求模式',
  `apply_times` tinyint(1) DEFAULT '0' COMMENT '玩家申请次数',
  `censor_way` tinyint(1) DEFAULT '1' COMMENT '审查方式 1手动，2自动',
  `ip_times` int(11) DEFAULT '0' COMMENT '同一IP限制参与次数',
  `image_id` int(1) DEFAULT '0' COMMENT '活动图片ID 从公用图片库调用',
  `is_deposit_display` tinyint(1) DEFAULT '1' COMMENT '是否显示在存款界面 1是 0否',
  `is_website_display` tinyint(1) DEFAULT '1' COMMENT '网站前台是否显示1是 0否',
  `mutex_parent_id` int(11) DEFAULT '0' COMMENT '互斥活动(不能与某个活动同时参与)',
  `is_bet_amount_enjoy_flow` tinyint(1) DEFAULT '1' COMMENT '活动期间内的投注额是否享受反水  1是 0不是',
  `apply_rule_string` text COMMENT '申请规则',
  `content_file_path` varchar(255) DEFAULT NULL COMMENT '活动内容文件目录',
  `is_active_apply` tinyint(1) DEFAULT '0' COMMENT '是否主动申请 1是 0不是',
  `join_times` int(11) DEFAULT '0' COMMENT '参与次数',
  `join_player_count` int(11) DEFAULT '0' COMMENT '参与人数',
  `join_deposit_amount` decimal(11,2) DEFAULT '0.00' COMMENT '存款总额',
  `join_bonus_amount` decimal(11,2) DEFAULT '0.00' COMMENT '参与红利总额',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='优惠活动';

-- ----------------------------
--  Table structure for `inf_carrier_activity_agent_user`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier_activity_agent_user`;
CREATE TABLE `inf_carrier_activity_agent_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `act_id` int(11) NOT NULL DEFAULT '0' COMMENT '活动ID',
  `carrier_id` int(11) NOT NULL DEFAULT '0' COMMENT '运营商ID',
  `agent_user_id` int(11) NOT NULL DEFAULT '0' COMMENT '代理用户ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='活动代理用户';

-- ----------------------------
--  Table structure for `inf_carrier_activity_amphoteric_game_plat`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier_activity_amphoteric_game_plat`;
CREATE TABLE `inf_carrier_activity_amphoteric_game_plat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `act_id` int(11) NOT NULL,
  `carrier_id` int(11) NOT NULL,
  `carrier_game_plat_id` int(11) NOT NULL DEFAULT '0' COMMENT '运营商开放的游戏平台id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8 COMMENT='正负盈利产生的平台';

-- ----------------------------
--  Table structure for `inf_carrier_activity_audit`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier_activity_audit`;
CREATE TABLE `inf_carrier_activity_audit` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `act_id` int(11) NOT NULL DEFAULT '0' COMMENT '活动ID',
  `carrier_id` int(11) NOT NULL DEFAULT '0' COMMENT '运营商ID',
  `player_id` int(11) NOT NULL DEFAULT '0' COMMENT '玩家ID',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态 1待审核 2通过 -1拒绝',
  `ip` varchar(15) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `process_deposit_amount` decimal(11,2) DEFAULT '0.00' COMMENT '处理存款金额',
  `process_bonus_amount` decimal(11,2) DEFAULT '0.00' COMMENT '处理红利金额',
  `process_withdraw_flow_limit` decimal(11,2) DEFAULT '0.00' COMMENT '处理取款流水',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11445 DEFAULT CHARSET=utf8 COMMENT='活动审核管理';

-- ----------------------------
--  Table structure for `inf_carrier_activity_flow_limited_game_plat`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier_activity_flow_limited_game_plat`;
CREATE TABLE `inf_carrier_activity_flow_limited_game_plat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `act_id` int(11) NOT NULL DEFAULT '0' COMMENT '活动ID',
  `carrier_id` int(11) NOT NULL DEFAULT '0' COMMENT '运营商ID',
  `carrier_game_plat_id` int(11) NOT NULL DEFAULT '0' COMMENT '运营商开放的游戏平台id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='活动流水限平台';

-- ----------------------------
--  Table structure for `inf_carrier_activity_player_level`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier_activity_player_level`;
CREATE TABLE `inf_carrier_activity_player_level` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `act_id` int(11) NOT NULL DEFAULT '0' COMMENT '活动ID',
  `carrier_id` int(11) NOT NULL DEFAULT '0' COMMENT '运营商ID',
  `player_level_id` int(11) NOT NULL DEFAULT '0' COMMENT '玩家等级ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8 COMMENT='活动玩家等级';

-- ----------------------------
--  Table structure for `inf_carrier_activity_type`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier_activity_type`;
CREATE TABLE `inf_carrier_activity_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) DEFAULT NULL COMMENT '运营商ID',
  `type_name` varchar(30) DEFAULT NULL COMMENT '活动类型名称',
  `desc` varchar(255) DEFAULT NULL COMMENT '活动描述',
  `status` tinyint(1) DEFAULT '1' COMMENT '活动类型状态 1正常 0关闭',
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='优惠活动类型';

-- ----------------------------
--  Table structure for `inf_carrier_agent_level`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier_agent_level`;
CREATE TABLE `inf_carrier_agent_level` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL COMMENT '所属运营商',
  `level_name` varchar(45) NOT NULL COMMENT '层级名称',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '代理类型 1佣金代理，2洗码代理，3占成代理',
  `default_player_level` int(11) DEFAULT NULL COMMENT '代理下属玩家默认层级',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是代理默认层级 0否 1是',
  `is_running` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用, 1 启用 0, 禁用',
  `is_multi_agent` tinyint(1) DEFAULT '0' COMMENT '是否支持多级代理',
  `sort` tinyint(2) NOT NULL DEFAULT '0' COMMENT '排序字段',
  `remark` varchar(255) DEFAULT NULL,
  `updated_at` datetime NOT NULL COMMENT '更新时间',
  `created_at` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COMMENT='代理层级表';

-- ----------------------------
--  Table structure for `inf_carrier_back_up_domain`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier_back_up_domain`;
CREATE TABLE `inf_carrier_back_up_domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL,
  `domain` varchar(255) NOT NULL COMMENT '域名',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 可用  0 不可用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='运营商备用域名表';

-- ----------------------------
--  Table structure for `inf_carrier_image_category`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier_image_category`;
CREATE TABLE `inf_carrier_image_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(50) NOT NULL COMMENT '图片类别',
  `carrier_id` int(11) NOT NULL COMMENT '所属运营商',
  `parent_category_id` int(11) DEFAULT NULL COMMENT '上级图片类别id',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_user_id` int(11) NOT NULL COMMENT '创建人员',
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='运营商图片分类数据';

-- ----------------------------
--  Table structure for `inf_carrier_images`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier_images`;
CREATE TABLE `inf_carrier_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL COMMENT '所属运营商id',
  `uploaded_user_id` int(11) NOT NULL COMMENT '上传用户id',
  `image_path` varchar(255) NOT NULL COMMENT '图片路径',
  `image_category` int(3) NOT NULL COMMENT '图片所属类别',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `image_size` varchar(50) DEFAULT NULL COMMENT '图片大小',
  `remark` varchar(50) DEFAULT NULL COMMENT '备注',
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8 COMMENT='运营商图片数据表';

-- ----------------------------
--  Table structure for `inf_carrier_pay_channel`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier_pay_channel`;
CREATE TABLE `inf_carrier_pay_channel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL COMMENT '所属运营商',
  `def_pay_channel_id` tinyint(3) unsigned NOT NULL COMMENT '支付渠道类型ID',
  `binded_third_part_pay_id` int(11) DEFAULT NULL COMMENT '第三方支付绑定配置id',
  `display_name` varchar(50) DEFAULT NULL COMMENT '前台展示名称',
  `balance` decimal(11,2) DEFAULT '0.00' COMMENT '银行卡余额',
  `account` varchar(45) NOT NULL COMMENT '卡号\n1,传统银行，此处必须填写银行卡的卡号，必须填写正确\n2,三方支付银行，此处可以填写商户ID\n3,互联网银行，此处必须填写账号，比如微信账号或者支付宝账号',
  `owner_name` varchar(45) NOT NULL COMMENT '持卡人姓名 银行卡的持卡人姓名（如果该卡用于玩家存款，这个信息一定要保持正确，否则玩家将无法正确存款',
  `fee_bear_id` tinyint(1) DEFAULT '0' COMMENT '手续费承担方',
  `fee_ratio` decimal(5,2) DEFAULT '0.00' COMMENT '手续费',
  `default_preferential_ratio` decimal(5,2) DEFAULT '0.00' COMMENT '默认优惠比例\n如果该卡用于存款，每发生一笔存款时，赠送玩家的存款优惠比例，默认=0，表示不发放存款优惠\n如果设置为1，此时默认比例=1%，假设存款100进入，赠送的存款优惠=100×1%=1',
  `balance_notify_amount` int(11) DEFAULT '0' COMMENT '余额限额提醒,该银行卡的余额达到余额限额提醒时，在客服对玩家存款审核的界面上，将提醒该卡余额超限\n默认=0，代表不提醒，如果设置为10000，则该银行卡余额超过10000时会被提醒',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '启用 1、禁用 0、作废 -1\n\n启用：启用中的银行卡，可以在网站前台在玩家进行存款操作中被看到，或者客户进行取款操作时被看到\n禁用：禁用中的银行卡，可以在客户管理后台进行相关操作，但玩家在网站前台无法看到\n作废：作废中的银行卡，不能被客服看到，注意禁用时会检查银行卡的余额，余额不为0的银行卡，不能被禁用',
  `qrcode` int(11) DEFAULT '0' COMMENT '二维码',
  `use_purpose` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用途:\n1 存款：如果该卡用于存款，则必须选择该项，系统中至少应该有一张用于存款的银行卡\n2 取款：如果该卡用于给玩家出款，则必须选择该项\n3 库房：如果该卡既不是存款又不用于取款，则可设为库房\n注意：系统不允许同一张银行卡既用于存款又用于取款或者库房',
  `card_origin_place` varchar(255) NOT NULL COMMENT '开户行\n1,传统银行，此处必须填写银行卡的开户行，比如：河南郑州工行解放路分理处\n2,三方支付银行，此处可以随意填写一些标识信息\n3,互联网银行，此处可以随意填写一些标识信息',
  `show` tinyint(1) DEFAULT '1' COMMENT '展示位置',
  `single_day_deposit_limit` int(11) DEFAULT '0' COMMENT '单日存款次数限制',
  `single_deposit_minimum` int(11) DEFAULT '0' COMMENT '单次存款最小限额',
  `maximum_single_deposit` int(11) DEFAULT '0' COMMENT '单次存款最大限额',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `CARRIER_PAY_CHANNEL_CHANNLE_ID_idx` (`def_pay_channel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='运营商银行卡设置';

-- ----------------------------
--  Table structure for `inf_carrier_pins`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier_pins`;
CREATE TABLE `inf_carrier_pins` (
  `id` int(11) NOT NULL,
  `carrier_id` int(11) NOT NULL,
  `pin_id` int(11) NOT NULL COMMENT '标签id',
  PRIMARY KEY (`id`),
  KEY `carrier_id` (`carrier_id`),
  KEY `pin_id` (`pin_id`),
  CONSTRAINT `inf_carrier_pins_ibfk_1` FOREIGN KEY (`carrier_id`) REFERENCES `inf_carrier` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `inf_carrier_pins_ibfk_2` FOREIGN KEY (`pin_id`) REFERENCES `def_pins` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `inf_carrier_player_game_plats_rebate_financial_flow`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier_player_game_plats_rebate_financial_flow`;
CREATE TABLE `inf_carrier_player_game_plats_rebate_financial_flow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL COMMENT '运营商id',
  `carrier_player_level_id` int(11) unsigned NOT NULL COMMENT '玩家等级id',
  `carrier_game_plat_id` int(11) unsigned NOT NULL COMMENT '运营商开放的游戏平台id',
  `limit_amount_per_flow` int(11) unsigned DEFAULT '0' COMMENT '单次限额',
  `rebate_financial_flow_rate` decimal(5,2) DEFAULT '0.00' COMMENT '当前玩家等级对应的游戏平台总返水比例',
  `rebate_financial_flow_step_rate_json` varchar(500) DEFAULT NULL COMMENT '当前玩家等级对应的游戏平台阶梯返水比例 json',
  `rebate_type` tinyint(1) DEFAULT '1' COMMENT '发放返水方法   1 客服手动  2 玩家自动获取返水',
  `rebate_manual_period_hours` int(5) DEFAULT '24' COMMENT '客服手动返水周期',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `carrier_player_level_id` (`carrier_player_level_id`,`carrier_game_plat_id`) USING BTREE,
  UNIQUE KEY `carrier_game_plat_id` (`carrier_game_plat_id`,`carrier_player_level_id`) USING BTREE,
  CONSTRAINT `inf_carrier_player_game_plats_rebate_financial_flow_ibfk_1` FOREIGN KEY (`carrier_game_plat_id`) REFERENCES `map_carrier_game_plats` (`game_plat_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8 COMMENT='运营商玩家等级与游戏平台返水比例设置表';

-- ----------------------------
--  Table structure for `inf_carrier_player_level`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier_player_level`;
CREATE TABLE `inf_carrier_player_level` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `level_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户等级名称',
  `remark` varchar(50) DEFAULT NULL COMMENT '备注',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否是默认等级 0否 1是',
  `carrier_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属运营商id',
  `status` tinyint(3) DEFAULT '1' COMMENT '等级状态， 0 禁用  1 启用',
  `sort` tinyint(2) unsigned DEFAULT '1' COMMENT '排序',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT '更新时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `upgrade_rule` text NOT NULL COMMENT '升级规则： json表示。格式见文档',
  `deleted_at` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `inf_carrier_service_team`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier_service_team`;
CREATE TABLE `inf_carrier_service_team` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '部门ID',
  `carrier_id` int(11) DEFAULT NULL COMMENT '运营商ID',
  `team_name` varchar(50) DEFAULT NULL COMMENT '部门名称',
  `is_administrator` tinyint(1) DEFAULT '0' COMMENT '是否是管理员部门',
  `remark` varchar(255) DEFAULT NULL COMMENT '部门备注信息',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态1正常;0关闭',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='运营商客服部门表';

-- ----------------------------
--  Table structure for `inf_carrier_service_team_role`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier_service_team_role`;
CREATE TABLE `inf_carrier_service_team_role` (
  `permission_id` int(11) unsigned NOT NULL COMMENT '权限ID',
  `team_id` int(11) unsigned NOT NULL COMMENT '运营商客服部门ID',
  `carrier_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`permission_id`,`team_id`),
  KEY `team_id` (`team_id`) USING BTREE,
  KEY `carrier_id` (`carrier_id`),
  CONSTRAINT `inf_carrier_service_team_role_ibfk_1` FOREIGN KEY (`carrier_id`) REFERENCES `inf_carrier` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `permission_team_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `permission_team_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `inf_carrier_service_team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `inf_carrier_user`
-- ----------------------------
DROP TABLE IF EXISTS `inf_carrier_user`;
CREATE TABLE `inf_carrier_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL COMMENT '所属运营商',
  `team_id` int(11) DEFAULT NULL COMMENT '所属部门ID',
  `username` varchar(50) DEFAULT NULL COMMENT '用户名',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `pwd_salt` char(10) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1:正常,0: 已锁定, -1冻结',
  `parent_id` int(11) DEFAULT NULL COMMENT '父ID',
  `mobile` char(11) DEFAULT NULL COMMENT '手机号',
  `email` varchar(50) DEFAULT NULL COMMENT '邮箱',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `login_at` timestamp NULL DEFAULT NULL COMMENT '最近登录时间',
  `remember_token` varchar(255) DEFAULT NULL,
  `is_super_admin` tinyint(1) DEFAULT '0' COMMENT '是否是超级管理员, 具备所有权限',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='运营商用户信息表';

-- ----------------------------
--  Table structure for `inf_dashboard_menu`
-- ----------------------------
DROP TABLE IF EXISTS `inf_dashboard_menu`;
CREATE TABLE `inf_dashboard_menu` (
  `menu_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(40) NOT NULL COMMENT '状态 1显示 0不显示',
  `user_type` varchar(20) NOT NULL COMMENT '用户类型 carrier  agent admin',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否可用  0 禁用 1 可用',
  `role_id` int(11) NOT NULL COMMENT '对应的用户角色',
  `parent_menu_id` int(11) DEFAULT NULL COMMENT '父级菜单id',
  `route` varchar(50) DEFAULT NULL COMMENT '路由名称',
  `icon_class` varchar(30) NOT NULL COMMENT '菜单图标icon class',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台菜单表';

-- ----------------------------
--  Table structure for `inf_player`
-- ----------------------------
DROP TABLE IF EXISTS `inf_player`;
CREATE TABLE `inf_player` (
  `player_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(64) NOT NULL COMMENT '账号',
  `mobile` varchar(15) DEFAULT NULL COMMENT '手机号码(登录账号用)',
  `real_name` varchar(64) DEFAULT NULL,
  `password` varchar(64) NOT NULL COMMENT '用户登陆密码',
  `pay_password` varchar(64) DEFAULT NULL COMMENT '支付密码(可以通过运营商设置用户是否需要支付密码)',
  `email` varchar(50) DEFAULT NULL COMMENT '邮箱登录账号用',
  `wechat` varchar(50) DEFAULT NULL COMMENT '微信',
  `consignee` varchar(100) DEFAULT NULL COMMENT '收货人',
  `sex` tinyint(1) DEFAULT NULL COMMENT '性别:0男,1女',
  `delivery_address` varchar(255) DEFAULT NULL COMMENT '收货地址',
  `agent_id` int(11) DEFAULT NULL COMMENT '代理商ID',
  `is_agent_recommend` tinyint(1) DEFAULT '0' COMMENT '是否是代理推荐玩家',
  `recommend_player_id` int(11) DEFAULT NULL COMMENT '推荐玩家ID',
  `carrier_id` int(11) NOT NULL COMMENT '所属运营商id',
  `total_win_loss` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '总输赢, 不需要手动更改. trigger自动维护',
  `score` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户积分',
  `main_account_amount` decimal(11,2) DEFAULT '0.00' COMMENT '主账户余额',
  `frozen_main_account_amount` decimal(11,2) DEFAULT NULL COMMENT '冻结余额',
  `login_ip` varchar(15) DEFAULT NULL COMMENT '登录ip',
  `player_level_id` int(11) unsigned DEFAULT NULL COMMENT '玩家等级id',
  `is_online` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否在线 0不在线  1 在线',
  `user_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户状态:0 表示锁定(某段时间后可以重试登陆) 1表示正常 2表示关闭(用户不能再登陆)',
  `password_wrong_times` tinyint(1) DEFAULT '0' COMMENT '密码输错次数  根据此值会设置用户是否自动锁定',
  `password_wrong_time` timestamp NULL DEFAULT NULL COMMENT '密码输入错误上次输错时间',
  `login_domain` varchar(255) DEFAULT NULL COMMENT '登录域名',
  `referral_code` varchar(50) DEFAULT NULL COMMENT '邀请码',
  `qq_account` varchar(30) DEFAULT NULL COMMENT 'qq号',
  `birthday` date DEFAULT NULL COMMENT '出生日期',
  `register_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '注册ip',
  `login_at` timestamp NULL DEFAULT NULL COMMENT '登录时间',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '注册时间',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT '软删除',
  `updated_at` timestamp NULL DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `remember_token` varchar(255) DEFAULT '0',
  PRIMARY KEY (`player_id`),
  KEY `mobile` (`mobile`) USING BTREE,
  KEY `member_id` (`player_id`) USING BTREE,
  KEY `player_level_id` (`player_level_id`),
  KEY `inf_player_ibfk_1` (`agent_id`),
  CONSTRAINT `inf_player_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `inf_agent` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `inf_player_ibfk_2` FOREIGN KEY (`player_level_id`) REFERENCES `inf_carrier_player_level` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6598 DEFAULT CHARSET=utf8 COMMENT='游戏玩家信息表';

-- ----------------------------
--  Table structure for `inf_player_bank_cards`
-- ----------------------------
DROP TABLE IF EXISTS `inf_player_bank_cards`;
CREATE TABLE `inf_player_bank_cards` (
  `card_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) unsigned NOT NULL COMMENT '运营商ID',
  `player_id` int(11) unsigned NOT NULL COMMENT '所属玩家',
  `card_account` varchar(50) DEFAULT NULL COMMENT '取款账号',
  `card_type` tinyint(3) unsigned DEFAULT NULL COMMENT '银行卡类型 外键',
  `card_owner_name` varchar(50) NOT NULL COMMENT '持卡人姓名',
  `card_birth_place` varchar(255) NOT NULL COMMENT '开户行地址',
  `status` tinyint(1) DEFAULT '1' COMMENT '是否有效 0无效 1有效',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`card_id`),
  UNIQUE KEY `card_account` (`card_account`) USING HASH,
  KEY `player_id` (`player_id`),
  CONSTRAINT `inf_player_bank_cards_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `inf_player` (`player_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `inf_player_game_account`
-- ----------------------------
DROP TABLE IF EXISTS `inf_player_game_account`;
CREATE TABLE `inf_player_game_account` (
  `account_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `main_game_plat_id` int(11) NOT NULL COMMENT '对应的游戏平台id',
  `player_id` int(11) DEFAULT NULL COMMENT '玩家ID',
  `account_user_name` varchar(50) NOT NULL COMMENT '账户用户名  各平台账号用户名不一样  用于注册游戏平台使用',
  `amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '账户余额',
  `is_need_repair` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否需要开启维护 如果开启维护, 那么用户不能登录游戏',
  `is_locked` tinyint(1) DEFAULT '0' COMMENT '账号是否锁定 1锁定 0 未锁定',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` datetime DEFAULT NULL,
  `extra_field` varchar(500) DEFAULT NULL COMMENT '其他自定义数据,  根据不同的游戏平台商的策略的自定义数据. json格式',
  PRIMARY KEY (`account_id`),
  KEY `operator_id` (`player_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='玩家游戏平台账户表';

-- ----------------------------
--  Table structure for `jobs`
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_reserved_at_index` (`queue`,`reserved_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Table structure for `log_admin_dash_operator`
-- ----------------------------
DROP TABLE IF EXISTS `log_admin_dash_operator`;
CREATE TABLE `log_admin_dash_operator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '操作用户id',
  `route_id` int(11) NOT NULL COMMENT '对应的路由id',
  `data` varchar(255) DEFAULT '' COMMENT '操作数据： 路由(Controller地址) + 参数(json)',
  `ip` varchar(15) NOT NULL DEFAULT '' COMMENT 'ip',
  `operate_place` varchar(255) DEFAULT NULL,
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '操作内容，具体业务内容',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='总后台系统操作日志表';

-- ----------------------------
--  Table structure for `log_agent_account`
-- ----------------------------
DROP TABLE IF EXISTS `log_agent_account`;
CREATE TABLE `log_agent_account` (
  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL,
  `agent_id` int(11) DEFAULT NULL COMMENT '玩家ID',
  `amount` decimal(10,2) DEFAULT NULL COMMENT '操作金额',
  `created_at` timestamp NULL DEFAULT NULL,
  `fund_type` tinyint(1) DEFAULT NULL COMMENT '资金类型  1 存款 2 取款 3 红利 ',
  `fund_source` varchar(50) DEFAULT NULL COMMENT '流水来源 例如: 客服调整余额',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `operator_reviewer_id` int(11) unsigned DEFAULT NULL COMMENT '运营商审核的客服id',
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `log_id` (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='代理账户操作日志';

-- ----------------------------
--  Table structure for `log_agent_account_adjust`
-- ----------------------------
DROP TABLE IF EXISTS `log_agent_account_adjust`;
CREATE TABLE `log_agent_account_adjust` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `agent_id` int(11) NOT NULL COMMENT '所属代理用户',
  `carrier_id` int(11) NOT NULL,
  `adjust_type` tinyint(1) NOT NULL COMMENT '调整类型  1 存款 2 佣金',
  `operator` int(11) NOT NULL COMMENT '操作人',
  `amount` decimal(11,3) NOT NULL COMMENT '调整金额',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='代理资金调整记录表';

-- ----------------------------
--  Table structure for `log_agent_deposit_pay`
-- ----------------------------
DROP TABLE IF EXISTS `log_agent_deposit_pay`;
CREATE TABLE `log_agent_deposit_pay` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pay_order_number` varchar(45) NOT NULL COMMENT '订单编号',
  `carrier_id` int(11) NOT NULL,
  `pay_order_channel_trade_number` varchar(45) DEFAULT NULL COMMENT '与支付平台的交易号',
  `agent_id` int(11) NOT NULL COMMENT '代理用户id',
  `amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '存款金额',
  `finally_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '实际到账金额, 如果有红利或者优惠 实际金额可能大于存款金额',
  `benefit_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '优惠金额',
  `bonus_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '红利金额',
  `fee_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '手续费',
  `pay_channel` int(11) NOT NULL COMMENT '支付渠道 外键def_pay_channel_list',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单状态 0 订单创建  1 订单支付成功  -1 订单支付失败 -2审核未通过 2订单待审核',
  `review_user_id` int(11) DEFAULT NULL COMMENT '审核人员id',
  `operate_time` timestamp NULL DEFAULT NULL COMMENT '处理时间',
  `credential` varchar(45) DEFAULT NULL COMMENT '凭据',
  `remark` varchar(45) DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `offline_transfer_deposit_type` tinyint(1) DEFAULT NULL COMMENT '线下转账存款方式   1 ATM机   2 银行转账',
  `offline_transfer_deposit_at` timestamp NULL DEFAULT NULL COMMENT '线下转账会员存款时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='代理账户存款记录表';

-- ----------------------------
--  Table structure for `log_agent_rebate_financial_flow`
-- ----------------------------
DROP TABLE IF EXISTS `log_agent_rebate_financial_flow`;
CREATE TABLE `log_agent_rebate_financial_flow` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL DEFAULT '0' COMMENT '运营商ID',
  `agent_id` int(11) NOT NULL DEFAULT '0' COMMENT '代理用户ID',
  `amount` decimal(11,2) DEFAULT '0.00' COMMENT '金额',
  `game_plat_id` int(11) NOT NULL,
  `log_player_bet_flow_id` int(11) DEFAULT NULL COMMENT '投注记录ID',
  `log_agent_settled_id` int(11) unsigned DEFAULT NULL COMMENT '代理佣金结算ID',
  `flow_rate` decimal(5,2) DEFAULT '0.00' COMMENT '洗码比例',
  `is_settled` tinyint(1) DEFAULT '0' COMMENT '是否已计算 0未结算，1已结算',
  `settled_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_at` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_player_bet_flow_id` (`log_player_bet_flow_id`),
  KEY `log_agent_settled_id` (`log_agent_settled_id`),
  CONSTRAINT `log_agent_rebate_financial_flow_ibfk_1` FOREIGN KEY (`log_player_bet_flow_id`) REFERENCES `log_player_bet_flow` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `log_agent_rebate_financial_flow_ibfk_2` FOREIGN KEY (`log_agent_settled_id`) REFERENCES `log_agent_settle` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8 COMMENT='代理洗码记录表';

-- ----------------------------
--  Table structure for `log_agent_settle`
-- ----------------------------
DROP TABLE IF EXISTS `log_agent_settle`;
CREATE TABLE `log_agent_settle` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL DEFAULT '0' COMMENT '运营商ID',
  `agent_id` int(11) NOT NULL DEFAULT '0' COMMENT '代理用户ID',
  `periods_id` int(11) DEFAULT '0' COMMENT '期数ID',
  `available_member_number` varchar(100) DEFAULT NULL COMMENT '有效会员数(0未达标,最少多少)',
  `game_plat_win_amount` decimal(11,2) DEFAULT '0.00' COMMENT '公司输赢(游戏平台佣金)',
  `available_player_bet_amount` decimal(11,2) DEFAULT '0.00' COMMENT '有效会员投注额',
  `cost_share` decimal(11,2) DEFAULT '0.00' COMMENT '成本分摊(优惠、红利、洗码)',
  `cumulative_last_month` decimal(11,2) DEFAULT '0.00' COMMENT '累加上月',
  `manual_tuneup` decimal(11,2) DEFAULT '0.00' COMMENT '手工调整',
  `this_period_commission` decimal(11,2) DEFAULT '0.00' COMMENT '本期佣金',
  `actual_payment` decimal(11,2) DEFAULT '0.00' COMMENT '实际发放',
  `transfer_next_month` decimal(11,2) DEFAULT '0.00' COMMENT '转结下月',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态 1:初审 2复审 3结算完成',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `created_user_id` int(11) DEFAULT NULL COMMENT '创建人',
  `created_at` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COMMENT='代理佣金结算记录表';

-- ----------------------------
--  Table structure for `log_agent_settle_periods`
-- ----------------------------
DROP TABLE IF EXISTS `log_agent_settle_periods`;
CREATE TABLE `log_agent_settle_periods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) DEFAULT '0',
  `agent_id` int(11) DEFAULT '0',
  `periods` varchar(100) NOT NULL COMMENT '期数',
  `start_time` varchar(50) DEFAULT NULL,
  `end_time` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COMMENT='代理佣金结算期数';

-- ----------------------------
--  Table structure for `log_agent_undertaken`
-- ----------------------------
DROP TABLE IF EXISTS `log_agent_undertaken`;
CREATE TABLE `log_agent_undertaken` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL DEFAULT '0' COMMENT '运营商ID',
  `amount` decimal(11,2) DEFAULT '0.00' COMMENT '金额',
  `agent_id` int(11) NOT NULL DEFAULT '0' COMMENT '代理用户ID',
  `is_settled` tinyint(1) DEFAULT '0' COMMENT '是否已计算 0未结算，1已结算',
  `settled_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '结算时间',
  `undertaken_type` tinyint(1) DEFAULT '0' COMMENT '承担类型 1:代理优惠存款承担 2:洗码承担 3:红利承担 4:取款手续费承担 5:存款手续费承担',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `conf_agent_commission_id` (`agent_id`) USING BTREE,
  CONSTRAINT `log_agent_undertaken_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `inf_agent` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COMMENT='代理优惠、洗码、红利承担表';

-- ----------------------------
--  Table structure for `log_agent_withdraw`
-- ----------------------------
DROP TABLE IF EXISTS `log_agent_withdraw`;
CREATE TABLE `log_agent_withdraw` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` char(15) NOT NULL COMMENT '取款流水单号',
  `carrier_id` int(11) DEFAULT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `apply_amount` decimal(11,2) NOT NULL COMMENT '申请金额',
  `fee_amount` decimal(11,2) DEFAULT NULL COMMENT '手续费',
  `finally_withdraw_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '实取金额',
  `carrier_pay_channel` int(11) DEFAULT NULL,
  `player_bank_card` int(11) DEFAULT NULL COMMENT '用户入款银行',
  `status` tinyint(1) DEFAULT NULL COMMENT '-2 待审核   -1 拒绝  1 出款',
  `reviewed_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '审核时间',
  `withdraw_succeed_at` timestamp NULL DEFAULT NULL COMMENT '出款时间',
  `operator` int(11) DEFAULT NULL COMMENT '审核人',
  `remark` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `log_agent_withdraw_ibfk_1` (`carrier_id`),
  KEY `log_agent_withdraw_ibfk_2` (`agent_id`),
  KEY `log_agent_withdraw_ibfk_3` (`operator`),
  CONSTRAINT `log_agent_withdraw_ibfk_1` FOREIGN KEY (`carrier_id`) REFERENCES `inf_carrier` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `log_agent_withdraw_ibfk_2` FOREIGN KEY (`agent_id`) REFERENCES `inf_agent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `log_agent_withdraw_ibfk_3` FOREIGN KEY (`operator`) REFERENCES `inf_carrier_user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='代理取款申请记录表';

-- ----------------------------
--  Table structure for `log_carrier_dash_operate`
-- ----------------------------
DROP TABLE IF EXISTS `log_carrier_dash_operate`;
CREATE TABLE `log_carrier_dash_operate` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL COMMENT '所属运营商id',
  `user_id` int(11) NOT NULL COMMENT '运营商用户id',
  `route_id` int(11) DEFAULT NULL COMMENT '路由id',
  `data` text COMMENT '操作数据json',
  `ip` varchar(15) NOT NULL COMMENT 'ip',
  `operate_place` varchar(50) DEFAULT NULL,
  `status_code` int(11) DEFAULT NULL COMMENT '状态码',
  `remark` text COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `operator_id` (`carrier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19689 DEFAULT CHARSET=utf8 COMMENT='运营商系统后台操作日志';

-- ----------------------------
--  Table structure for `log_carrier_quota_consumption`
-- ----------------------------
DROP TABLE IF EXISTS `log_carrier_quota_consumption`;
CREATE TABLE `log_carrier_quota_consumption` (
  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) DEFAULT NULL COMMENT '运营商ID',
  `amount` decimal(10,2) DEFAULT NULL COMMENT '操作金额',
  `pay_channel_remain_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `related_pay_channel` int(11) DEFAULT NULL COMMENT '交易支付渠道',
  `consumption_source` varchar(255) DEFAULT NULL COMMENT '消费来源',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL COMMENT '操作时间',
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=315 DEFAULT CHARSET=utf8 COMMENT='运营商额度消费记录';

-- ----------------------------
--  Table structure for `log_carrier_win_lose_stastics`
-- ----------------------------
DROP TABLE IF EXISTS `log_carrier_win_lose_stastics`;
CREATE TABLE `log_carrier_win_lose_stastics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL,
  `register_count` int(11) NOT NULL DEFAULT '0' COMMENT '注册数',
  `login_count` int(11) NOT NULL DEFAULT '0' COMMENT '登录数',
  `deposit_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '存款额',
  `first_deposit_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '首存额',
  `deposit_count` int(11) NOT NULL DEFAULT '0' COMMENT '存款数',
  `first_deposit_count` int(11) NOT NULL DEFAULT '0' COMMENT '首存数',
  `withdraw_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '取款额',
  `winlose_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '公司输赢',
  `bonus_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '红利',
  `rebate_financial_flow_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '洗码',
  `deposit_benefit_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '存款优惠',
  `carrier_income` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '公司收入',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='公司输赢报表';

-- ----------------------------
--  Table structure for `log_game_win_lose_stastics`
-- ----------------------------
DROP TABLE IF EXISTS `log_game_win_lose_stastics`;
CREATE TABLE `log_game_win_lose_stastics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL,
  `game_plat_id` int(11) NOT NULL COMMENT '游戏平台id',
  `bet_player_count` int(11) NOT NULL DEFAULT '0' COMMENT '投注人数',
  `bet_count` int(11) NOT NULL DEFAULT '0' COMMENT '投注次数',
  `bet_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '投注额',
  `win_lose_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '公司输赢',
  `rebate_financial_flow_amount` decimal(11,2) NOT NULL COMMENT '洗码金额',
  `average_bet_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '人均投注额',
  `average_bet_count` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '人均投注次数',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='游戏输赢汇总';

-- ----------------------------
--  Table structure for `log_player_account`
-- ----------------------------
DROP TABLE IF EXISTS `log_player_account`;
CREATE TABLE `log_player_account` (
  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL,
  `player_id` int(11) DEFAULT NULL COMMENT '玩家ID',
  `main_game_plat_id` int(11) unsigned DEFAULT NULL COMMENT '游戏主平台id',
  `amount` decimal(10,2) DEFAULT NULL COMMENT '操作金额',
  `created_at` timestamp NULL DEFAULT NULL,
  `fund_type` tinyint(1) DEFAULT NULL COMMENT '资金类型  1 存款 2 取款 3 红利 4 返水 5转账\r\n1：存款\r\n2：取款\r\n3：红利\r\n4：返水\r\n5：转账',
  `fund_source` varchar(50) DEFAULT NULL COMMENT '流水来源 例如: 从玩家主账户转出到游戏',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `operator_reviewer_id` int(11) unsigned DEFAULT NULL COMMENT '运营商审核的客服id',
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `log_id` (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11905 DEFAULT CHARSET=utf8 COMMENT='玩家账户操作日志';

-- ----------------------------
--  Table structure for `log_player_account_adjust`
-- ----------------------------
DROP TABLE IF EXISTS `log_player_account_adjust`;
CREATE TABLE `log_player_account_adjust` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL COMMENT '所属玩家',
  `carrier_id` int(11) NOT NULL,
  `adjust_type` tinyint(1) NOT NULL COMMENT '调整类型  1 存款 2 返水 3 红利',
  `operator` int(11) NOT NULL COMMENT '操作人',
  `amount` decimal(11,2) NOT NULL COMMENT '调整金额',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8 COMMENT='玩家资金调整记录表';

-- ----------------------------
--  Table structure for `log_player_activity`
-- ----------------------------
DROP TABLE IF EXISTS `log_player_activity`;
CREATE TABLE `log_player_activity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `act_id` int(11) NOT NULL DEFAULT '0' COMMENT '活动ID',
  `carrier_id` int(11) NOT NULL DEFAULT '0' COMMENT '运营商ID',
  `player_id` int(11) NOT NULL DEFAULT '0' COMMENT '玩家ID',
  `amount` decimal(11,2) DEFAULT '0.00' COMMENT '红利金额',
  `handle_way` tinyint(1) DEFAULT '1' COMMENT '处理方式 1人工审核  2自动审核',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态 1待审核 2通过 -1拒绝',
  `handle_at` timestamp NULL DEFAULT NULL COMMENT '处理时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='玩家申请活动记录表';

-- ----------------------------
--  Table structure for `log_player_bet_flow`
-- ----------------------------
DROP TABLE IF EXISTS `log_player_bet_flow`;
CREATE TABLE `log_player_bet_flow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_id` int(11) DEFAULT NULL COMMENT '玩家id',
  `carrier_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL COMMENT '游戏id',
  `game_plat_id` int(11) NOT NULL COMMENT '游戏平台id',
  `game_flow_code` varchar(30) DEFAULT NULL COMMENT '游戏流水号',
  `player_or_banker` tinyint(1) NOT NULL DEFAULT '0' COMMENT '庄闲投注0无, 1庄 2闲 3庄闲都投注',
  `game_status` tinyint(1) DEFAULT '1' COMMENT '游戏状态 1 结算完成, 0 未完成',
  `bet_amount` decimal(10,2) NOT NULL COMMENT '下注金额',
  `company_win_amount` decimal(10,2) NOT NULL COMMENT '公司输赢',
  `available_bet_amount` decimal(10,2) NOT NULL COMMENT '有效投注额',
  `company_payout_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '公司派彩',
  `bet_flow_available` tinyint(1) NOT NULL DEFAULT '1' COMMENT '投注流水是否有效 1 有效 0无效',
  `bet_info` text COMMENT '投注内容',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `progressive_bet` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '彩池投注额',
  `progressive_win` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '彩池输赢',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=573 DEFAULT CHARSET=utf8 COMMENT='玩家投注流水记录';

-- ----------------------------
--  Table structure for `log_player_deposit_pay`
-- ----------------------------
DROP TABLE IF EXISTS `log_player_deposit_pay`;
CREATE TABLE `log_player_deposit_pay` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pay_order_number` varchar(45) NOT NULL COMMENT '订单编号',
  `carrier_id` int(11) NOT NULL,
  `pay_order_channel_trade_number` varchar(45) DEFAULT NULL COMMENT '与支付平台的交易号',
  `player_id` int(11) NOT NULL COMMENT '玩家id',
  `player_bank_card` int(11) DEFAULT NULL COMMENT '会员银行卡   仅线下存款有效',
  `carrier_pay_channel` int(11) NOT NULL COMMENT '运营商入款支付渠道  仅线下存款有效',
  `amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '存款金额',
  `finally_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '实际到账金额, 如果有红利或者优惠 实际金额可能大于存款金额',
  `benefit_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '优惠金额',
  `bonus_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '红利金额',
  `withdraw_flow_limit_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '取款流水限制',
  `fee_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '手续费',
  `carrier_activity_id` int(11) DEFAULT NULL COMMENT '会员参与的活动id',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单状态 0 订单创建  1 订单支付成功  -1 订单支付失败 -2审核未通过 2订单待审核',
  `review_user_id` int(11) DEFAULT NULL COMMENT '审核人员id',
  `operate_time` timestamp NULL DEFAULT NULL COMMENT '处理时间',
  `credential` varchar(45) DEFAULT NULL COMMENT '凭据',
  `remark` varchar(45) DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `offline_transfer_deposit_type` tinyint(1) DEFAULT NULL COMMENT '线下转账存款方式   1 ATM机   2 银行转账',
  `offline_transfer_deposit_at` timestamp NULL DEFAULT NULL COMMENT '线下转账会员存款时间',
  `ip` varchar(50) NOT NULL COMMENT '存款ip',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carrier_id` (`carrier_id`),
  KEY `player_id` (`player_id`)
) ENGINE=InnoDB AUTO_INCREMENT=261 DEFAULT CHARSET=utf8 COMMENT='玩家账户存款记录表';

-- ----------------------------
--  Table structure for `log_player_invite_reward`
-- ----------------------------
DROP TABLE IF EXISTS `log_player_invite_reward`;
CREATE TABLE `log_player_invite_reward` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL COMMENT '奖励的会员对象id ',
  `reward_type` tinyint(1) DEFAULT NULL COMMENT '奖励类型  1  投注额奖励   2  存款奖励',
  `reward_related_player` int(11) DEFAULT NULL COMMENT '奖励出自于哪一个会员id 为空时表示是单独奖励  可能是运营商会给被邀请的玩家也有奖励',
  `reward_amount` decimal(11,2) DEFAULT NULL COMMENT '奖励金额',
  `related_player_deposit_amount` decimal(11,2) DEFAULT NULL COMMENT '关联的会员总存款额',
  `related_player_bet_amount` decimal(11,2) DEFAULT NULL COMMENT '关联的会员投注额',
  `related_player_validate_bet_amount` decimal(11,2) DEFAULT NULL COMMENT '关联的会员有效投注额',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8 COMMENT='会员奖励结算日志';

-- ----------------------------
--  Table structure for `log_player_login`
-- ----------------------------
DROP TABLE IF EXISTS `log_player_login`;
CREATE TABLE `log_player_login` (
  `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` int(11) NOT NULL COMMENT '玩家id',
  `carrier_id` int(11) unsigned NOT NULL COMMENT '运营商id',
  `login_ip` varchar(15) NOT NULL COMMENT '登录ip',
  `login_domain` varchar(255) DEFAULT NULL COMMENT '登录域名',
  `login_time` timestamp NULL DEFAULT NULL COMMENT '登录时间',
  `login_location` varchar(255) DEFAULT NULL COMMENT '登陆地点',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `log_id` (`log_id`) USING BTREE,
  KEY `player_id` (`player_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1081 DEFAULT CHARSET=utf8 COMMENT='会员登录日志';

-- ----------------------------
--  Table structure for `log_player_rebate_financial_flow`
-- ----------------------------
DROP TABLE IF EXISTS `log_player_rebate_financial_flow`;
CREATE TABLE `log_player_rebate_financial_flow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `game_plat` int(11) NOT NULL COMMENT '游戏平台id',
  `bet_times` int(11) NOT NULL DEFAULT '0' COMMENT '投注次数',
  `rebate_financial_flow_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '返水额',
  `bet_flow_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '总投注流水',
  `company_pay_out_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '公司派彩总额',
  `is_already_settled` tinyint(1) DEFAULT '0' COMMENT '是否已结算 1 已结算 0 未结算',
  `settled_at` timestamp NULL DEFAULT NULL COMMENT '结算时间',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='玩家返水记录';

-- ----------------------------
--  Table structure for `log_player_withdraw`
-- ----------------------------
DROP TABLE IF EXISTS `log_player_withdraw`;
CREATE TABLE `log_player_withdraw` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_number` char(15) NOT NULL COMMENT '取款流水单号',
  `carrier_id` int(11) DEFAULT NULL,
  `player_id` int(11) unsigned DEFAULT NULL,
  `apply_amount` decimal(11,2) NOT NULL COMMENT '申请金额',
  `fee_amount` decimal(11,2) DEFAULT NULL COMMENT '手续费',
  `finally_withdraw_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '实取金额',
  `carrier_pay_channel` int(11) DEFAULT NULL COMMENT '运营商出款支付渠道',
  `player_bank_card` int(11) DEFAULT NULL COMMENT '用户入款银行',
  `status` tinyint(1) DEFAULT NULL COMMENT '-2 待审核   -1 拒绝  1 出款',
  `reviewed_at` timestamp NULL DEFAULT NULL COMMENT '审核时间',
  `withdraw_succeed_at` timestamp NULL DEFAULT NULL COMMENT '出款时间',
  `operator` int(11) DEFAULT NULL COMMENT '审核人',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `carrier_id` (`carrier_id`),
  KEY `player_id` (`player_id`),
  KEY `operator` (`operator`),
  CONSTRAINT `log_player_withdraw_ibfk_1` FOREIGN KEY (`carrier_id`) REFERENCES `inf_carrier` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `log_player_withdraw_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `inf_player` (`player_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `log_player_withdraw_ibfk_3` FOREIGN KEY (`operator`) REFERENCES `inf_carrier_user` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='玩家取款申请记录表';

-- ----------------------------
--  Table structure for `log_player_withdraw_flow_limit`
-- ----------------------------
DROP TABLE IF EXISTS `log_player_withdraw_flow_limit`;
CREATE TABLE `log_player_withdraw_flow_limit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `player_account_log` int(11) NOT NULL COMMENT '关联的玩家账户记录表',
  `limit_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '取款流水限制',
  `limit_type` tinyint(1) NOT NULL COMMENT '限额类型\n1  优惠活动\n2 自动返水\n3 手动返水\n4 调整红利\n5 玩家存款\n6 调整余额\n7 调整返水',
  `complete_limit_amount` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '已完成的流水限制',
  `is_finished` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已完成该流水限制',
  `operator_id` int(11) DEFAULT NULL COMMENT '处理人员 运营商用户id',
  `related_activity` int(11) DEFAULT NULL COMMENT '关联的活动id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11690 DEFAULT CHARSET=utf8 COMMENT='玩家取款限制汇总';

-- ----------------------------
--  Table structure for `log_player_withdraw_flow_limit_detail`
-- ----------------------------
DROP TABLE IF EXISTS `log_player_withdraw_flow_limit_detail`;
CREATE TABLE `log_player_withdraw_flow_limit_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `withdraw_flow_limit_id` int(11) NOT NULL COMMENT '流水限制id',
  `game_plat_id` int(11) unsigned DEFAULT NULL COMMENT '游戏平台',
  `game_id` int(11) DEFAULT NULL COMMENT '游戏id',
  `flow_amount` decimal(11,2) DEFAULT '0.00' COMMENT '投注流水',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `withdraw_flow_limit_id` (`withdraw_flow_limit_id`),
  KEY `game_plat_id` (`game_plat_id`),
  CONSTRAINT `log_player_withdraw_flow_limit_detail_ibfk_2` FOREIGN KEY (`game_plat_id`) REFERENCES `def_game_plats` (`game_plat_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `log_player_withdraw_flow_limit_detail_ibfk_3` FOREIGN KEY (`withdraw_flow_limit_id`) REFERENCES `log_player_withdraw_flow_limit` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8 COMMENT='玩家流水限制记录分游戏平台详细数据';

-- ----------------------------
--  Table structure for `log_player_withdraw_flow_limit_game_plats`
-- ----------------------------
DROP TABLE IF EXISTS `log_player_withdraw_flow_limit_game_plats`;
CREATE TABLE `log_player_withdraw_flow_limit_game_plats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `player_withdraw_flow_limit_id` int(11) NOT NULL COMMENT '流水限制记录id',
  `def_game_plat_id` int(11) NOT NULL COMMENT '游戏平台id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8 COMMENT='玩家投注流水限制平台数据';

-- ----------------------------
--  Table structure for `map_carrier_game_plats`
-- ----------------------------
DROP TABLE IF EXISTS `map_carrier_game_plats`;
CREATE TABLE `map_carrier_game_plats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) unsigned NOT NULL,
  `status` tinyint(1) DEFAULT '1' COMMENT '游戏主平台是否开放  1开放  0关闭',
  `game_plat_id` int(11) unsigned NOT NULL COMMENT '对应的游戏平台id',
  `sort` int(5) DEFAULT NULL COMMENT '排序',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `carrier_id` (`carrier_id`,`game_plat_id`) USING BTREE,
  KEY `game_plat_id` (`game_plat_id`),
  CONSTRAINT `map_carrier_game_plats_ibfk_1` FOREIGN KEY (`game_plat_id`) REFERENCES `def_game_plats` (`game_plat_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `map_carrier_games`
-- ----------------------------
DROP TABLE IF EXISTS `map_carrier_games`;
CREATE TABLE `map_carrier_games` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_id` int(11) NOT NULL DEFAULT '1' COMMENT '所属运营商id',
  `game_id` int(11) unsigned NOT NULL COMMENT '游戏主账户ID',
  `display_name` varchar(50) DEFAULT NULL COMMENT '游戏显示名称',
  `sort` int(5) NOT NULL DEFAULT '1' COMMENT '游戏排序',
  `status` int(5) NOT NULL DEFAULT '1' COMMENT '运营商分配的游戏开放状态 1 开放  0关闭',
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `carrier_id` (`carrier_id`,`game_id`) USING BTREE,
  KEY `game_id` (`game_id`),
  CONSTRAINT `map_carrier_games_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `def_games` (`game_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5237 DEFAULT CHARSET=utf8 COMMENT='游戏平台';

-- ----------------------------
--  Table structure for `map_carrier_player_level_pay_channel`
-- ----------------------------
DROP TABLE IF EXISTS `map_carrier_player_level_pay_channel`;
CREATE TABLE `map_carrier_player_level_pay_channel` (
  `map_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_player_level_id` int(11) unsigned NOT NULL COMMENT '对应的玩家层级id',
  `carrier_pay_channle_id` int(11) unsigned NOT NULL COMMENT '对应的支付渠道id',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`map_id`),
  UNIQUE KEY `carrier_player_level_id` (`carrier_player_level_id`,`carrier_pay_channle_id`) USING BTREE,
  CONSTRAINT `map_carrier_player_level_pay_channel_ibfk_1` FOREIGN KEY (`carrier_player_level_id`) REFERENCES `inf_carrier_player_level` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `migrations`
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Table structure for `notifications`
-- ----------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `notifiable_id` int(10) unsigned NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_id_notifiable_type_index` (`notifiable_id`,`notifiable_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Table structure for `password_resets`
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Table structure for `permission_group`
-- ----------------------------
DROP TABLE IF EXISTS `permission_group`;
CREATE TABLE `permission_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '权限组ID',
  `group_name` varchar(50) DEFAULT NULL COMMENT '权限分组名称',
  `sort` int(11) DEFAULT NULL COMMENT '排序',
  `parent_id` int(11) unsigned DEFAULT NULL COMMENT '父分组ID',
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `permission_role`
-- ----------------------------
DROP TABLE IF EXISTS `permission_role`;
CREATE TABLE `permission_role` (
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `permission_role_ibfk_1` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `permission_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `permissions`
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_id` int(11) unsigned DEFAULT NULL COMMENT '权限组ID',
  `name` varchar(255) DEFAULT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=303 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `role_user`
-- ----------------------------
DROP TABLE IF EXISTS `role_user`;
CREATE TABLE `role_user` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `user_type` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '用户角色类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Table structure for `roles`
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `carrier_team_id` int(11) DEFAULT NULL COMMENT '运营商部门id,  仅当user_type为 carrier时生效',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_type` varchar(15) COLLATE utf8_unicode_ci NOT NULL COMMENT 'carrier  agent  admin',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `roles_name_unique` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
--  Triggers structure for table inf_carrier
-- ----------------------------
DROP TRIGGER IF EXISTS `CREATE_CARRIER_PREPARE_WORK`;
delimiter ;;
CREATE TRIGGER `CREATE_CARRIER_PREPARE_WORK` AFTER INSERT ON `inf_carrier` FOR EACH ROW BEGIN

	#新建运营商的准备工作处理
	#新建一个默认的部门 管理员
	INSERT INTO inf_carrier_service_team (carrier_id,team_name,is_administrator) VALUES (NEW.id,'管理员',1);

	#创建默认代理
	INSERT INTO inf_agent (username,realname,agent_level_id,is_default,status,carrier_id) VALUES (CONCAT(NEW.name,'_CARRIER_DEFAULT_AGENT'),CONCAT(NEW.name,'_CARRIER_DEFAULT_AGENT'),null,1,1,NEW.id);

END
 ;;
delimiter ;

delimiter ;;
-- ----------------------------
--  Triggers structure for table inf_carrier_agent_level
-- ----------------------------
 ;;
delimiter ;
DROP TRIGGER IF EXISTS `NEW_AGENT_TYPE`;
delimiter ;;
CREATE TRIGGER `NEW_AGENT_TYPE` AFTER INSERT ON `inf_carrier_agent_level` FOR EACH ROW BEGIN
      DECLARE Done INT DEFAULT 0;
      #声明变量  玩家等级id
      DECLARE GamePlatId INT(11);
      #声明游标
      DECLARE rs CURSOR FOR SELECT game_plat_id FROM map_carrier_game_plats WHERE carrier_id = NEW.carrier_id;
     #将结束标志绑定到游标
     DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;
   IF NEW.TYPE=1 THEN
         INSERT INTO conf_carrier_commission_agent (agent_level_id,carrier_id) VALUES (NEW.id,NEW.carrier_id);
         OPEN rs;    
	FETCH NEXT FROM rs INTO GamePlatId;	
	REPEAT
              
	INSERT INTO conf_carrier_commission_agent_platform_fee (agent_level_id,carrier_game_plat_id,carrier_id) VALUES (NEW.id,GamePlatId,NEW.carrier_id);
	FETCH NEXT FROM rs INTO GamePlatId;
	UNTIL Done END REPEAT;
       CLOSE rs;

   ELSEIF NEW.TYPE = 2 THEN
       INSERT INTO conf_carrier_rebate_financial_flow_agent_base_info (agent_level_id,carrier_id) VALUES (NEW.id,NEW.carrier_id);
       OPEN rs;    
	FETCH NEXT FROM rs INTO GamePlatId;	
	REPEAT
	INSERT INTO conf_carrier_rebate_financial_flow_agent (agent_level_id,carrier_game_plat_id,carrier_id) VALUES (NEW.id,GamePlatId,NEW.carrier_id);
            
              
	FETCH NEXT FROM rs INTO GamePlatId;
	UNTIL Done END REPEAT;
       CLOSE rs;

  ELSEIF NEW.TYPE = 3 THEN
          INSERT INTO conf_carrier_cost_take_agent (agent_level_id,carrier_id) VALUES (NEW.id,NEW.carrier_id);	
         OPEN rs;	
              FETCH NEXT FROM rs INTO GamePlatId;	
              REPEAT
              INSERT INTO conf_carrier_cost_take_agent_platform_fee (agent_level_id,carrier_game_plat_id,carrier_id) VALUES (NEW.id,GamePlatId,NEW.carrier_id);
             
              FETCH NEXT FROM rs INTO GamePlatId;
              UNTIL Done END REPEAT;
        CLOSE rs;

   END IF;
END
 ;;
delimiter ;
DROP TRIGGER IF EXISTS `UPDATA_AGENT_TYPE`;
delimiter ;;
CREATE TRIGGER `UPDATA_AGENT_TYPE` AFTER UPDATE ON `inf_carrier_agent_level` FOR EACH ROW BEGIN
      DECLARE Done INT DEFAULT 0;
      #声明变量  玩家等级id
      DECLARE GamePlatId INT(11);
      #声明游标
      DECLARE rs CURSOR FOR SELECT game_plat_id FROM map_carrier_game_plats WHERE carrier_id = NEW.carrier_id;
     #将结束标志绑定到游标
     DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;
    IF NEW.TYPE != OLD.TYPE THEN
   IF NEW.TYPE=1 THEN
         DELETE FROM conf_carrier_commission_agent WHERE agent_level_id = NEW.id AND carrier_id =NEW.carrier_id; 
         INSERT INTO conf_carrier_commission_agent (agent_level_id,carrier_id) VALUES (NEW.id,NEW.carrier_id);
         DELETE FROM conf_carrier_commission_agent_platform_fee WHERE agent_level_id = NEW.id AND carrier_id =NEW.carrier_id; 
         DELETE FROM conf_carrier_rebate_financial_flow_agent WHERE agent_level_id = NEW.id AND carrier_id =NEW.carrier_id;
         DELETE FROM conf_carrier_rebate_financial_flow_agent_base_info WHERE agent_level_id = NEW.id AND carrier_id =NEW.carrier_id;
       
         DELETE FROM conf_carrier_cost_take_agent WHERE agent_level_id = NEW.id AND carrier_id =NEW.carrier_id; 
         DELETE FROM conf_carrier_cost_take_agent_platform_fee WHERE agent_level_id = NEW.id AND carrier_id =NEW.carrier_id;

         OPEN rs;    
	FETCH NEXT FROM rs INTO GamePlatId;	
	REPEAT 
	    INSERT INTO conf_carrier_commission_agent_platform_fee (agent_level_id,carrier_game_plat_id,carrier_id) VALUES (NEW.id,GamePlatId,NEW.carrier_id);
	FETCH NEXT FROM rs INTO GamePlatId;
	UNTIL Done END REPEAT;
       CLOSE rs;

         
   ELSEIF NEW.TYPE = 2 THEN
              DELETE FROM conf_carrier_commission_agent_platform_fee WHERE agent_level_id = NEW.id AND carrier_id =NEW.carrier_id;
              DELETE FROM conf_carrier_commission_agent WHERE agent_level_id = NEW.id AND carrier_id =NEW.carrier_id; 
              DELETE FROM conf_carrier_rebate_financial_flow_agent WHERE agent_level_id = NEW.id AND carrier_id =NEW.carrier_id;
              DELETE FROM conf_carrier_rebate_financial_flow_agent_base_info WHERE agent_level_id = NEW.id AND carrier_id =NEW.carrier_id;
              INSERT INTO conf_carrier_rebate_financial_flow_agent_base_info (agent_level_id,carrier_id) VALUES (NEW.id,NEW.carrier_id);
              DELETE FROM conf_carrier_cost_take_agent WHERE agent_level_id = NEW.id AND carrier_id =NEW.carrier_id; 
              DELETE FROM conf_carrier_cost_take_agent_platform_fee WHERE agent_level_id = NEW.id AND carrier_id =NEW.carrier_id;

              
       OPEN rs;    
	FETCH NEXT FROM rs INTO GamePlatId;	
	REPEAT
	    INSERT INTO conf_carrier_rebate_financial_flow_agent (agent_level_id,carrier_game_plat_id,carrier_id) VALUES (NEW.id,GamePlatId,NEW.carrier_id);
                 
	FETCH NEXT FROM rs INTO GamePlatId;
	UNTIL Done END REPEAT;
       CLOSE rs;

 ELSEIF NEW.TYPE = 3 THEN
              DELETE FROM conf_carrier_commission_agent_platform_fee WHERE agent_level_id = NEW.id AND carrier_id =NEW.carrier_id;
              DELETE FROM conf_carrier_rebate_financial_flow_agent WHERE agent_level_id = NEW.id AND carrier_id =NEW.carrier_id;
              DELETE FROM conf_carrier_commission_agent  WHERE agent_level_id = NEW.id AND carrier_id =NEW.carrier_id; 
              DELETE FROM conf_carrier_rebate_financial_flow_agent_base_info WHERE agent_level_id = NEW.id AND carrier_id =NEW.carrier_id;

              DELETE FROM conf_carrier_cost_take_agent  WHERE agent_level_id = NEW.id AND carrier_id =NEW.carrier_id; 
              DELETE FROM conf_carrier_cost_take_agent_platform_fee  WHERE agent_level_id = NEW.id AND carrier_id =NEW.carrier_id;
             
              INSERT INTO conf_carrier_cost_take_agent  (agent_level_id,carrier_id) VALUES (NEW.id,NEW.carrier_id);

       OPEN rs;    
	FETCH NEXT FROM rs INTO GamePlatId;	
	REPEAT
	    INSERT INTO conf_carrier_cost_take_agent_platform_fee (agent_level_id,carrier_game_plat_id,carrier_id) VALUES (NEW.id,GamePlatId,NEW.carrier_id);
              
	FETCH NEXT FROM rs INTO GamePlatId;
	UNTIL Done END REPEAT;
       CLOSE rs;
               
               
   END IF;
END IF;
END
 ;;
delimiter ;
DROP TRIGGER IF EXISTS `DELETE_AGENT_TYPE`;
delimiter ;;
CREATE TRIGGER `DELETE_AGENT_TYPE` AFTER DELETE ON `inf_carrier_agent_level` FOR EACH ROW BEGIN
       IF OLD.TYPE=1 THEN
                   DELETE FROM conf_carrier_commission_agent WHERE agent_level_id = OLD.id;
                   DELETE FROM conf_carrier_commission_agent_platform_fee WHERE agent_level_id = OLD.id;

       ELSEIF OLD.TYPE = 2 THEN
                   DELETE FROM conf_carrier_rebate_financial_flow_agent WHERE agent_level_id = OLD.id;
                   DELETE FROM conf_carrier_rebate_financial_flow_agent_base_info WHERE agent_level_id = OLD.id;
                 

       ELSEIF OLD.TYPE = 3 THEN
                   DELETE FROM conf_carrier_cost_take_agent WHERE agent_level_id = OLD.id; 
                   DELETE FROM conf_carrier_cost_take_agent_platform_fee WHERE agent_level_id = OLD.id; 

       END IF;
END
 ;;
delimiter ;

delimiter ;;
-- ----------------------------
--  Triggers structure for table inf_carrier_player_level
-- ----------------------------
 ;;
delimiter ;
DROP TRIGGER IF EXISTS `NEW_PLAYER_REBATE_FINANCIAL_FLOW_WHEN_NEW_PLAY`;
delimiter ;;
CREATE TRIGGER `NEW_PLAYER_REBATE_FINANCIAL_FLOW_WHEN_NEW_PLAY` AFTER INSERT ON `inf_carrier_player_level` FOR EACH ROW BEGIN
	DECLARE Done INT DEFAULT 0;
	#声明变量  玩家等级id
	DECLARE GamePlatId INT(11);
	#声明游标
	DECLARE rs CURSOR FOR SELECT game_plat_id FROM map_carrier_game_plats WHERE carrier_id = NEW.carrier_id;
	#将结束标志绑定到游标
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;
	OPEN rs;
		FETCH NEXT FROM rs INTO GamePlatId;
		IF GamePlatId THEN
			REPEAT
				INSERT INTO inf_carrier_player_game_plats_rebate_financial_flow (carrier_player_level_id,carrier_game_plat_id,carrier_id) VALUES (NEW.id,GamePlatId,NEW.carrier_id);
				FETCH NEXT FROM rs INTO GamePlatId;
			UNTIL Done END REPEAT;
		END IF;
	CLOSE rs;
END
 ;;
delimiter ;
DROP TRIGGER IF EXISTS `DELETE_PLAYER_REBATE_FINANCIAL_FLOW_WHEN_DELETE_LEVEL`;
delimiter ;;
CREATE TRIGGER `DELETE_PLAYER_REBATE_FINANCIAL_FLOW_WHEN_DELETE_LEVEL` AFTER DELETE ON `inf_carrier_player_level` FOR EACH ROW BEGIN

DELETE FROM inf_carrier_player_game_plats_rebate_financial_flow WHERE carrier_player_level_id = OLD.id;


END
 ;;
delimiter ;

delimiter ;;
-- ----------------------------
--  Triggers structure for table inf_carrier_service_team
-- ----------------------------
 ;;
delimiter ;
DROP TRIGGER IF EXISTS `INSERT_NEW_CARRIER_ROLE`;
delimiter ;;
CREATE TRIGGER `INSERT_NEW_CARRIER_ROLE` AFTER INSERT ON `inf_carrier_service_team` FOR EACH ROW BEGIN
	INSERT INTO roles (name,display_name,user_type,carrier_team_id) VALUES (NEW.team_name,NEW.team_name,'carrier',NEW.id);
END
 ;;
delimiter ;
DROP TRIGGER IF EXISTS `DELETE_CARRIER_ROLE`;
delimiter ;;
CREATE TRIGGER `DELETE_CARRIER_ROLE` AFTER DELETE ON `inf_carrier_service_team` FOR EACH ROW BEGIN
	DELETE FROM roles WHERE user_type = 'carrier' AND carrier_team_id = OLD.id;
END
 ;;
delimiter ;

delimiter ;;
-- ----------------------------
--  Triggers structure for table inf_carrier_service_team_role
-- ----------------------------
 ;;
delimiter ;
DROP TRIGGER IF EXISTS `INSERT_NEW_CARRIER_USER_PERMISSION`;
delimiter ;;
CREATE TRIGGER `INSERT_NEW_CARRIER_USER_PERMISSION` AFTER INSERT ON `inf_carrier_service_team_role` FOR EACH ROW BEGIN

	INSERT INTO permission_role (permission_id,role_id) VALUES (NEW.permission_id,(SELECT id FROM roles WHERE carrier_team_id = NEW.team_id AND user_type = 'carrier'));
	
END
 ;;
delimiter ;
DROP TRIGGER IF EXISTS `DELETE_FROM_CARRIER_USER_PERMISSION`;
delimiter ;;
CREATE TRIGGER `DELETE_FROM_CARRIER_USER_PERMISSION` AFTER DELETE ON `inf_carrier_service_team_role` FOR EACH ROW BEGIN
	
	DELETE FROM permission_role WHERE permission_id = OLD.permission_id AND role_id = (SELECT id FROM roles WHERE carrier_team_id = OLD.team_id AND user_type = 'carrier');

END
 ;;
delimiter ;

delimiter ;;
-- ----------------------------
--  Triggers structure for table inf_carrier_user
-- ----------------------------
 ;;
delimiter ;
DROP TRIGGER IF EXISTS `INSERT_CARRIER_USER_NEW_ROLE`;
delimiter ;;
CREATE TRIGGER `INSERT_CARRIER_USER_NEW_ROLE` AFTER INSERT ON `inf_carrier_user` FOR EACH ROW BEGIN
	INSERT INTO role_user (user_id,role_id,user_type) VALUES (NEW.id, (SELECT id FROM roles WHERE carrier_team_id = NEW.team_id AND user_type = 'carrier'),'carrier');
END
 ;;
delimiter ;
DROP TRIGGER IF EXISTS `UPDATE_CARRIER_USER_ROLE`;
delimiter ;;
CREATE TRIGGER `UPDATE_CARRIER_USER_ROLE` AFTER UPDATE ON `inf_carrier_user` FOR EACH ROW BEGIN
	
	IF NEW.team_id <> OLD.team_id THEN
		DELETE FROM role_user WHERE user_id = NEW.id AND user_type = 'carrier';
		INSERT INTO role_user (user_id,role_id,user_type) VALUES (NEW.id, (SELECT id FROM roles WHERE carrier_team_id = NEW.team_id AND user_type = 'carrier'),'carrier');
	END IF;

END
 ;;
delimiter ;

delimiter ;;
-- ----------------------------
--  Triggers structure for table inf_player
-- ----------------------------
 ;;
delimiter ;
DROP TRIGGER IF EXISTS `inf_player_AFTER_UPDATE`;
delimiter ;;
CREATE TRIGGER `inf_player_AFTER_UPDATE` BEFORE UPDATE ON `inf_player` FOR EACH ROW BEGIN

	IF NEW.agent_id = NULL THEN
		SET NEW.carrier_id = NULL;
	ELSEIF NEW.agent_id != OLD.agent_id THEN
		#更新所属运营商id
		SET NEW.carrier_id = (SELECT carrier_id FROM inf_agent WHERE id = NEW.agent_id);
	ELSE
		SET @temp = 1;
	END IF;
END
 ;;
delimiter ;

delimiter ;;
-- ----------------------------
--  Triggers structure for table log_player_bet_flow
-- ----------------------------
 ;;
delimiter ;
DROP TRIGGER IF EXISTS `log_player_bet_flow_AFTER_INSERT`;
delimiter ;;
CREATE TRIGGER `log_player_bet_flow_AFTER_INSERT` AFTER INSERT ON `log_player_bet_flow` FOR EACH ROW BEGIN
	#当有投注流水时,实时更新玩家的投注流水记录
	UPDATE inf_player SET total_win_loss = total_win_loss - NEW.company_win_amount WHERE player_id = NEW.player_id; 

END
 ;;
delimiter ;

delimiter ;;
-- ----------------------------
--  Triggers structure for table map_carrier_game_plats
-- ----------------------------
 ;;
delimiter ;
DROP TRIGGER IF EXISTS `NEW_CARRIER_PLAYER_REBATE_FINANCIAL_FLOW`;
delimiter ;;
CREATE TRIGGER `NEW_CARRIER_PLAYER_REBATE_FINANCIAL_FLOW` AFTER INSERT ON `map_carrier_game_plats` FOR EACH ROW BEGIN

	DECLARE Done INT DEFAULT 0;
	
	#声明变量  玩家等级id
	DECLARE LevelId INT(11);

	#声明游标
	DECLARE rs CURSOR FOR SELECT id FROM inf_carrier_player_level WHERE carrier_id = NEW.carrier_id;
	
	#将结束标志绑定到游标
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;
	
	OPEN rs;
		
		FETCH NEXT FROM rs INTO LevelId;
			
		REPEAT
			IF LevelId > 0 THEN

				INSERT INTO inf_carrier_player_game_plats_rebate_financial_flow (carrier_player_level_id,carrier_game_plat_id,carrier_id) VALUES (LevelId,NEW.game_plat_id,NEW.carrier_id);
			
			END IF;
		
			FETCH NEXT FROM rs INTO LevelId;

		UNTIL Done END REPEAT;
	
	CLOSE rs;


END
 ;;
delimiter ;
DROP TRIGGER IF EXISTS `DELETE_CARRIER_PLAYER_REBATE_FINANCIAL_FLOW`;
delimiter ;;
CREATE TRIGGER `DELETE_CARRIER_PLAYER_REBATE_FINANCIAL_FLOW` AFTER DELETE ON `map_carrier_game_plats` FOR EACH ROW BEGIN
	
DELETE FROM inf_carrier_player_game_plats_rebate_financial_flow WHERE carrier_game_plat_id = OLD.game_plat_id;

END
 ;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
