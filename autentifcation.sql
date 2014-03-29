-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Мар 22 2014 г., 14:16
-- Версия сервера: 5.5.25
-- Версия PHP: 5.3.13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
USE autentification;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `autentifcation`
--

-- --------------------------------------------------------

--
-- Структура таблицы `entrance`
--

CREATE TABLE IF NOT EXISTS `entrance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(10) unsigned NOT NULL,
  `IP` varchar(20) DEFAULT NULL,
  `failed_attempts` int(11) NOT NULL DEFAULT '0',
  `blocking` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `id_user` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Дамп данных таблицы `entrance`
--

INSERT INTO `entrance` (`id`, `id_user`, `IP`, `failed_attempts`, `blocking`) VALUES
(6, 1, '127.0.0.1', 0, 0),
(7, 2, '127.0.0.1', 6, 1),
(8, 1, '127.0.0.2', 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `id_role` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(20) NOT NULL,
  PRIMARY KEY (`id_role`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `role`
--

INSERT INTO `role` (`id_role`, `role`) VALUES
(0, 'user'),
(1, 'admin');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(25) NOT NULL,
  `login` varchar(25) NOT NULL,
  `password` varchar(40) NOT NULL,
  `salt` varchar(20) NOT NULL,
  `date_last_login` date DEFAULT NULL,
  `IP` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `role` int(11) NOT NULL DEFAULT '2',
  `id_session` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`user_id`, `username`, `login`, `password`, `salt`, `date_last_login`, `IP`, `email`, `role`, `id_session`) VALUES
(1, 'dimp', 'dimp', '1000e0598c04ebb98a3534cc84c9f227b9baa893', 'xi55zba5zf2n5mc0', '2014-03-22', '127.0.0.1', 'dimp@mail.com', 1, 'ca126596bd56ec579002aeb4f12ef270208046d6'),
(2, 'dimp1', 'dimp1', '07bba2afcb27009d933ed3170744f7ceef8c7842', 'lk7i2khcvpgg56lt', '2014-03-22', '127.0.0.1', 'dimp1@mail.com', 0, '1e71e611be05cdadb51a792bda91bdf7b45cb9f6');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
