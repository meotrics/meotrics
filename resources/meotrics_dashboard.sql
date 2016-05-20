SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `apps`
-- ----------------------------
DROP TABLE IF EXISTS `apps`;
CREATE TABLE `apps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `ownerid` int(11) NOT NULL,
  `code` varchar(30) NOT NULL,
  `backendid` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `codename` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of apps
-- ----------------------------
INSERT INTO `apps` VALUES ('1', 'Xa lo van su', '1', 'XALOVANSU', '');
INSERT INTO `apps` VALUES ('2', 'Speedy Apply', '1', 'SPEEDYAPPLY', '');
INSERT INTO `apps` VALUES ('3', 'Speedy Access', '1', 'SPEEDYACCESS', '');
INSERT INTO `apps` VALUES ('4', 'Okr', '1', 'OKR', '');

-- ----------------------------
-- Table structure for `users`
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` tinyint(1) DEFAULT NULL,
  `company` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fb` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `remember_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `verified` int(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES ('1', '', '$2y$10$vlR4xsKws521k0JDWkW73ezOHBXDGZePi048qkiGfCklW/KHsD9KK', null, 'thanh@gmail.com', null, null, null, null, '10', '2016', '2016', 'Thanh', null, 1);
INSERT INTO `users` VALUES ('2', '', '$2y$10$xaERgAC4H.p5BLE1efhoWeDmzTNyFaS4D0pKbhmHET1AwA31sIYF2', null, 'van@gmail.com', null, null, null, null, '10', '2016', '2016', 'Hai van', null, 1);

-- ----------------------------
-- Table structure for `user_app`
-- ----------------------------
DROP TABLE IF EXISTS `user_app`;
CREATE TABLE `user_app` (
  `appid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `can_perm` tinyint(1) NOT NULL,
  `can_struct` tinyint(1) NOT NULL,
  `can_report` tinyint(1) NOT NULL,
  PRIMARY KEY (`appid`,`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_app
-- ----------------------------
INSERT INTO `user_app` VALUES ('1', '1', '1', '1', '1');
INSERT INTO `user_app` VALUES ('4', '1', '1', '1', '1');
INSERT INTO `user_app` VALUES ('4', '2', '0', '1', '1');
