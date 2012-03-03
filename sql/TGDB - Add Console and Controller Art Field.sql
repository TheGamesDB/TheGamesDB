ALTER TABLE `platforms`
ADD COLUMN `controller`  varchar(100) NULL AFTER `icon`;
ALTER TABLE `platforms`
ADD COLUMN `console`  varchar(100) NULL AFTER `icon`;