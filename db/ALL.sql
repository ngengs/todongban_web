SET FOREIGN_KEY_CHECKS=0

DROP TABLE IF EXISTS `USER` CASCADE
;

DROP TABLE IF EXISTS `HELP_TYPE` CASCADE
;

DROP TABLE IF EXISTS `USER_HELP_CONF` CASCADE
;

DROP TABLE IF EXISTS `HELP_RESPONSE` CASCADE
;

DROP TABLE IF EXISTS `HELP_REQUEST` CASCADE
;

DROP TABLE IF EXISTS `USER_LOCATION` CASCADE
;

DROP TABLE IF EXISTS `GARAGE` CASCADE
;

CREATE TABLE `USER`
(
	`ID` VARCHAR(36) NOT NULL,
	`USERNAME` CHAR(16) NOT NULL,
	`EMAIL` VARCHAR(320) NOT NULL,
	`PASSWORD` VARCHAR(128) NOT NULL,
	`FULL_NAME` VARCHAR(320) NOT NULL,
	`NAME_ALT` VARCHAR(50) NULL,
	`PHONE` VARCHAR(50) NOT NULL,
	`GENDER` TINYINT NOT NULL,
	`AVATAR` VARCHAR(40) NOT NULL,
	`IDENTITY_NUMBER` VARCHAR(20) NOT NULL,
	`IDENTITY_PICTURE` VARCHAR(40) NOT NULL,
	`ADDRESS` TEXT NOT NULL,
	`DEVICE_ID` VARCHAR(256) NOT NULL,
	`ID_CREATE` VARCHAR(36) NULL,
	`DATE_CREATE` DATETIME(0) NULL,
	`ID_UPDATE` VARCHAR(36) NULL,
	`DATE_UPDATE` DATETIME(0) NULL,
	`TYPE` TINYINT NOT NULL COMMENT 'TYPE: 0 = ADMIN 1 = BIASA 2 = BENGKEL',
	`STATUS` TINYINT NOT NULL COMMENT 'STATUS: 0 = HAPUS 1 = AKTIF 2 = BELUM AKTIF 3 = DITOLAK',
	CONSTRAINT `PK_USER` PRIMARY KEY (`ID`)
)
;

CREATE TABLE `HELP_TYPE`
(
	`ID` VARCHAR(36) NOT NULL,
	`VECHILE` TINYINT NOT NULL COMMENT 'VECHILE: 1 = MOTOR 2 = MOBIL',
	`NAME` TEXT NOT NULL,
	`STATUS` TINYINT NOT NULL COMMENT 'STATUS: 0 = TIDAK AKTIF 1 = AKTIF',
	CONSTRAINT `PK_HELP_TYPE` PRIMARY KEY (`ID`)
)
;

CREATE TABLE `USER_HELP_CONF`
(
	`ID` VARCHAR(36) NOT NULL,
	`ID_USER` VARCHAR(36) NOT NULL,
	`ID_HELP_TYPE` VARCHAR(36) NOT NULL,
	`STATUS` TINYINT NOT NULL COMMENT 'STATUS: 0 = TIDAK AKTIF (TIDAK MENANGANI) 1 = AKTIF (MENANGANI)',
	CONSTRAINT `PK_USER_HELP_CONF` PRIMARY KEY (`ID`)
)
;

CREATE TABLE `HELP_RESPONSE`
(
	`ID` VARCHAR(36) NOT NULL,
	`ID_USER` VARCHAR(36) NOT NULL,
	`ID_HELP_REQUEST` VARCHAR(36) NOT NULL,
	`RESPONSE` TINYINT NOT NULL COMMENT 'RESPONSE: 0=MENOLAK 1=MENERIMA',
	`RATING` TINYINT NULL COMMENT 'RATING: Rating poing dari penerima bantuan setelah selesai Range 1-5',
	`ID_CREATE` VARCHAR(36) NULL,
	`DATE_CREATE` DATETIME(0) NULL,
	`ID_UPDATE` VARCHAR(36) NULL,
	`DATE_UPDATE` DATETIME(0) NULL,
	`STATUS` TINYINT NOT NULL COMMENT 'STATUS: 0 = NO SELECTED 1 = SELECTED',
	CONSTRAINT `PK_HELP_RESPONSE` PRIMARY KEY (`ID`)
)
;

CREATE TABLE `HELP_REQUEST`
(
	`ID` VARCHAR(36) NOT NULL,
	`ID_USER` VARCHAR(36) NOT NULL,
	`ID_HELP_TYPE` VARCHAR(36) NOT NULL,
	`MESSAGE` TEXT NULL,
	`LATITUDE` DOUBLE NOT NULL,
	`LONGTITUDE` DOUBLE NOT NULL,
	`LOCATION_NAME` TEXT NULL,
	`ID_CREATE` VARCHAR(36) NOT NULL,
	`DATE_CREATE` DATETIME(0) NOT NULL,
	`ID_UPDATE` VARCHAR(36) NULL,
	`DATE_UPDATE` DATETIME(0) NULL,
	`STATUS` TINYINT NOT NULL COMMENT 'STATUS: 0 = CANCELED 1 = REQUESTED 2 = PROCESS 3 = FINISH',
	CONSTRAINT `PK_HELP_REQUEST` PRIMARY KEY (`ID`)
)
;

CREATE TABLE `USER_LOCATION`
(
	`ID` VARCHAR(36) NOT NULL,
	`ID_USER` VARCHAR(36) NOT NULL,
	`LATITUDE` DOUBLE NOT NULL,
	`LONGTITUDE` DOUBLE NOT NULL,
	`DATE` DATETIME(0) NOT NULL,
	`STATUS` TINYINT NOT NULL COMMENT 'STATUS: 0 = TIDAK AKTIF 1 = AKTIF',
	CONSTRAINT `PK_USER_LOCATION` PRIMARY KEY (`ID`)
)
;

CREATE TABLE `GARAGE`
(
	`ID` VARCHAR(36) NOT NULL,
	`ID_USER` VARCHAR(36) NULL,
	`NAME` VARCHAR(125) NOT NULL,
	`OPEN_HOUR` TIME(0) NOT NULL,
	`CLOSE_HOUR` TIME(0) NOT NULL,
	`ADDRESS` TEXT NOT NULL,
	`LATITUDE` DECIMAL(10,7) NULL,
	`LONGTITUDE` DECIMAL(10,7) NULL,
	`ID_CREATE` VARCHAR(36) NULL,
	`DATE_CREATE` VARCHAR(36) NULL,
	`ID_UPDATE` VARCHAR(36) NULL,
	`DATE_UPDATE` VARCHAR(36) NULL,
	`TYPE` TINYINT NOT NULL COMMENT 'TYPE: 1 = ALAMAT BIASA 2 = ALAMAT BENGKEL',
	`STATUS` TINYINT NOT NULL COMMENT 'STATUS: 0 = TIDAK AKTIF 1 = AKTIF',
	CONSTRAINT `PK_GARAGE` PRIMARY KEY (`ID`)
)
;

ALTER TABLE `USER_HELP_CONF` 
 ADD INDEX `IXFK_USER_HELP_CONF_HELP_TYPE` (`ID_HELP_TYPE` ASC)
;

ALTER TABLE `USER_HELP_CONF` 
 ADD INDEX `IXFK_USER_HELP_CONF_USER` (`ID_USER` ASC)
;

ALTER TABLE `HELP_RESPONSE` 
 ADD INDEX `IXFK_HELP_RESPONSE_HELP_REQUEST` (`ID_HELP_REQUEST` ASC)
;

ALTER TABLE `HELP_RESPONSE` 
 ADD INDEX `IXFK_HELP_RESPONSE_USER` (`ID_USER` ASC)
;

ALTER TABLE `HELP_REQUEST` 
 ADD INDEX `IXFK_HELP_REQUEST_HELP_TYPE` (`ID_HELP_TYPE` ASC)
;

ALTER TABLE `HELP_REQUEST` 
 ADD INDEX `IXFK_HELP_REQUEST_USER` (`ID_USER` ASC)
;

ALTER TABLE `USER_LOCATION` 
 ADD INDEX `IXFK_USER_LOCATION_USER` (`ID_USER` ASC)
;

ALTER TABLE `GARAGE` 
 ADD INDEX `IXFK_GARAGE_USER` (`ID_USER` ASC)
;

ALTER TABLE `USER_HELP_CONF` 
 ADD CONSTRAINT `FK_USER_HELP_CONF_HELP_TYPE`
	FOREIGN KEY (`ID_HELP_TYPE`) REFERENCES `HELP_TYPE` (`ID`) ON DELETE No Action ON UPDATE No Action
;

ALTER TABLE `USER_HELP_CONF` 
 ADD CONSTRAINT `FK_USER_HELP_CONF_USER`
	FOREIGN KEY (`ID_USER`) REFERENCES `USER` (`ID`) ON DELETE No Action ON UPDATE No Action
;

ALTER TABLE `HELP_RESPONSE` 
 ADD CONSTRAINT `FK_HELP_RESPONSE_HELP_REQUEST`
	FOREIGN KEY (`ID_HELP_REQUEST`) REFERENCES `HELP_REQUEST` (`ID`) ON DELETE No Action ON UPDATE No Action
;

ALTER TABLE `HELP_RESPONSE` 
 ADD CONSTRAINT `FK_HELP_RESPONSE_USER`
	FOREIGN KEY (`ID_USER`) REFERENCES `USER` (`ID`) ON DELETE No Action ON UPDATE No Action
;

ALTER TABLE `HELP_REQUEST` 
 ADD CONSTRAINT `FK_HELP_REQUEST_HELP_TYPE`
	FOREIGN KEY (`ID_HELP_TYPE`) REFERENCES `HELP_TYPE` (`ID`) ON DELETE No Action ON UPDATE No Action
;

ALTER TABLE `HELP_REQUEST` 
 ADD CONSTRAINT `FK_HELP_REQUEST_USER`
	FOREIGN KEY (`ID_USER`) REFERENCES `USER` (`ID`) ON DELETE No Action ON UPDATE No Action
;

ALTER TABLE `USER_LOCATION` 
 ADD CONSTRAINT `FK_USER_LOCATION_USER`
	FOREIGN KEY (`ID_USER`) REFERENCES `USER` (`ID`) ON DELETE No Action ON UPDATE No Action
;

ALTER TABLE `GARAGE` 
 ADD CONSTRAINT `FK_GARAGE_USER`
	FOREIGN KEY (`ID_USER`) REFERENCES `USER` (`ID`) ON DELETE Set Null ON UPDATE Set Null
;

SET FOREIGN_KEY_CHECKS=1
