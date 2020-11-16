SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for shorturls
-- ----------------------------
CREATE TABLE IF NOT EXISTS `short_urls` (
  `code` varchar(20) NOT NULL COMMENT '短链接码',
  `url` varchar(255) NOT NULL COMMENT '原始链接',
  `visits` int unsigned NOT NULL DEFAULT '0' COMMENT '被访问的次数',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for visits
-- ----------------------------
CREATE TABLE IF NOT EXISTS `visits` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `short_url` varchar(20) NOT NULL,
  `ip_address` varchar(40) NOT NULL COMMENT '访问者的 ip',
  `browser` varchar(50) NOT NULL default ''COMMENT '',
  `device` varchar(50) NOT NULL default '',
  `os` varchar(50) NOT NULL default '',
  `referer` varchar(255) NOT NULL COMMENT 'http header referer',
  `user_agent` varchar(255) NOT NULL COMMENT 'http header',
  `visited_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '访问时间',
  PRIMARY KEY (`id`),
  KEY `key_shorturl` (`short_url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
