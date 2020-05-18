SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema testzdmg
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `testzdmg` ;
CREATE SCHEMA IF NOT EXISTS `testzdmg` DEFAULT CHARACTER SET latin1 ;
USE `testzdmg` ;

-- -----------------------------------------------------
-- Table `testzdmg`.`accounts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testzdmg`.`accounts` ;

CREATE TABLE IF NOT EXISTS `testzdmg`.`accounts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `account_name` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `testzdmg`.`bugs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testzdmg`.`bugs` ;

CREATE TABLE IF NOT EXISTS `testzdmg`.`bugs` (
  `bug_id` INT(11) NOT NULL,
  `bug_description` VARCHAR(100) NULL DEFAULT NULL,
  `bug_status` VARCHAR(20) NULL DEFAULT NULL,
  `reported_by` INT NULL DEFAULT NULL,
  `assigned_to` INT NULL DEFAULT NULL,
  `verified_by` INT NULL DEFAULT NULL,
  PRIMARY KEY (`bug_id`),
  INDEX `bugs_ibfk_1_idx` (`reported_by` ASC),
  INDEX `bugs_ibfk_2_idx` (`assigned_to` ASC),
  INDEX `bugs_ibfk_3_idx` (`verified_by` ASC),
  CONSTRAINT `bugs_ibfk_1`
    FOREIGN KEY (`reported_by`)
    REFERENCES `testzdmg`.`accounts` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `bugs_ibfk_2`
    FOREIGN KEY (`assigned_to`)
    REFERENCES `testzdmg`.`accounts` (`id`),
  CONSTRAINT `bugs_ibfk_3`
    FOREIGN KEY (`verified_by`)
    REFERENCES `testzdmg`.`accounts` (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `testzdmg`.`products`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testzdmg`.`products` ;

CREATE TABLE IF NOT EXISTS `testzdmg`.`products` (
  `product_id` INT(11) NOT NULL,
  `product_name` VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (`product_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `testzdmg`.`bugs_products`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testzdmg`.`bugs_products` ;

CREATE TABLE IF NOT EXISTS `testzdmg`.`bugs_products` (
  `bug_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  PRIMARY KEY (`bug_id`, `product_id`),
  INDEX `product_id` (`product_id` ASC),
  CONSTRAINT `bugs_products_ibfk_1`
    FOREIGN KEY (`bug_id`)
    REFERENCES `testzdmg`.`bugs` (`bug_id`),
  CONSTRAINT `bugs_products_ibfk_2`
    FOREIGN KEY (`product_id`)
    REFERENCES `testzdmg`.`products` (`product_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `testzdmg`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `testzdmg`.`user` ;

CREATE TABLE IF NOT EXISTS `testzdmg`.`user` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
