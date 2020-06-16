/*Author:Leon*/
/*Date:2020年6月16日 0:34:31*/
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `message_board`
--
CREATE DATABASE IF NOT EXISTS `message_board` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `message_board`;

-- --------------------------------------------------------

--
-- 表的结构 `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '留言ID，主键，自增',
  `message_content` varchar(300) COLLATE utf8_unicode_ci NOT NULL COMMENT '留言内容',
  `create_time` datetime NOT NULL COMMENT '留言时间',
  `id` int(11) NOT NULL COMMENT '留言用户id',
  `message_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '留言用户',
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `message`
--

INSERT INTO `message` (`message_id`, `message_content`, `create_time`, `id`, `message_name`) VALUES
(2, 'root留言', '2019-08-18 13:13:31', 1, 'root'),
(3, '白白留言', '2019-08-18 13:20:25', 2, '白白'),
(4, '小明留言', '2019-08-18 13:21:50', 3, '小明'),
(5, '小红留言', '2019-08-18 13:23:19', 4, '小红');

-- --------------------------------------------------------

--
-- 表的结构 `reply`
--

CREATE TABLE IF NOT EXISTS `reply` (
  `reply_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '回复id，主键，自增',
  `reply_content` varchar(500) COLLATE utf8_unicode_ci NOT NULL COMMENT '回复内容',
  `message_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '留言的用户',
  `create_time` datetime NOT NULL COMMENT '回复时间',
  `message_id` int(11) NOT NULL COMMENT '评论id',
  `id` int(11) NOT NULL COMMENT '回复用户id',
  `reply_name` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '回复的用户',
  PRIMARY KEY (`reply_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `reply`
--

INSERT INTO `reply` (`reply_id`, `reply_content`, `message_name`, `create_time`, `message_id`, `id`, `reply_name`) VALUES
(1, '白白回复', 'root', '2019-08-18 13:20:36', 2, 2, '白白'),
(2, 'root回复', '白白', '2019-08-18 13:21:01', 3, 2, '白白'),
(3, '小明回复', 'root', '2019-08-18 13:22:05', 2, 3, '小明'),
(4, '小红回复', '小明', '2019-08-18 13:23:31', 4, 4, '小红');

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户ID，主键，自增',
  `username` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '用户名，字符串',
  `userpass` varchar(35) COLLATE utf8_unicode_ci NOT NULL COMMENT '用户密码',
  `create_time` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '用户创建时间',
  `imgpath` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '头像',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `username`, `userpass`, `create_time`, `imgpath`) VALUES
(1, 'root', '63a9f0ea7bb98050796b649e85481845', '1534597686', '15922358084039.jpg'),
(2, '白白', '56d662e6060d8b64929ba4859146f0be', '1534598404', '15345984049767.jpg'),
(3, '小明', '97304531204ef7431330c20427d95481', '1534598486', '15345984866471.jpg'),
(4, '小红', '1167eac4687a0d8aae4d01efe9274cda', '1534598579', '15922359653774.jpg');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

/*创建管理用户*/
CREATE USER 'message'@'localhost' IDENTIFIED BY 'message';
GRANT ALL ON message_board.* TO 'message'@'localhost';