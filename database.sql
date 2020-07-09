SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `links` (
  `ID` int(11) NOT NULL,
  `ciphertext` text NOT NULL COMMENT 'encrypted links in json format',
  `passwordHash` varchar(64) DEFAULT NULL COMMENT 'sha256 hash',
  `enableCaptcha` tinyint(1) DEFAULT NULL COMMENT '1 or 0',
  `enableClicknload` tinyint(1) DEFAULT NULL COMMENT '1 or 0',
  `expireDate` int(10) UNSIGNED DEFAULT NULL COMMENT 'timestamp'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `links`
  ADD PRIMARY KEY (`ID`);


ALTER TABLE `links`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
