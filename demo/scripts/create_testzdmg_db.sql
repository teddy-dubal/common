SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema common
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `common` ;
CREATE SCHEMA IF NOT EXISTS `common` DEFAULT CHARACTER SET utf8 ;
USE `common` ;

-- -----------------------------------------------------
-- Table `common`.`accounts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `common`.`accounts` ;

CREATE TABLE IF NOT EXISTS `common`.`accounts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `account_name` VARCHAR(100) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `common`.`bugs`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `common`.`bugs` ;

CREATE TABLE IF NOT EXISTS `common`.`bugs` (
  `bug_id` INT(11) NOT NULL,
  `bug_description` VARCHAR(100) NULL DEFAULT NULL,
  `bug_status` VARCHAR(20) NULL DEFAULT NULL,
  `reported_by` INT NULL DEFAULT NULL,
  `assigned_to` INT NULL DEFAULT NULL,
  `verified_by` INT NULL DEFAULT NULL,
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `update_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`bug_id`),
  INDEX `bugs_ibfk_1_idx` (`reported_by` ASC),
  INDEX `bugs_ibfk_2_idx` (`assigned_to` ASC),
  INDEX `bugs_ibfk_3_idx` (`verified_by` ASC),
  CONSTRAINT `bugs_ibfk_1`
    FOREIGN KEY (`reported_by`)
    REFERENCES `common`.`accounts` (`id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `bugs_ibfk_2`
    FOREIGN KEY (`assigned_to`)
    REFERENCES `common`.`accounts` (`id`),
  CONSTRAINT `bugs_ibfk_3`
    FOREIGN KEY (`verified_by`)
    REFERENCES `common`.`accounts` (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `common`.`products`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `common`.`products` ;

CREATE TABLE IF NOT EXISTS `common`.`products` (
  `product_id` INT(11) NOT NULL,
  `product_name` VARCHAR(100) NULL DEFAULT NULL,
  PRIMARY KEY (`product_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `common`.`bugs_products`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `common`.`bugs_products` ;

CREATE TABLE IF NOT EXISTS `common`.`bugs_products` (
  `bug_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  PRIMARY KEY (`bug_id`, `product_id`),
  INDEX `product_id` (`product_id` ASC),
  CONSTRAINT `bugs_products_ibfk_1`
    FOREIGN KEY (`bug_id`)
    REFERENCES `common`.`bugs` (`bug_id`),
  CONSTRAINT `bugs_products_ibfk_2`
    FOREIGN KEY (`product_id`)
    REFERENCES `common`.`products` (`product_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `common`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `common`.`user` ;

CREATE TABLE IF NOT EXISTS `common`.`user` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
