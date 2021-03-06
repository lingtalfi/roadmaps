-- MySQL Script generated by MySQL Workbench
-- Fri Mar 10 07:52:15 2017
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema roadmaps
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema roadmaps
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `roadmaps` DEFAULT CHARACTER SET utf8 ;
USE `roadmaps` ;

-- -----------------------------------------------------
-- Table `roadmaps`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `roadmaps`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `pseudo` VARCHAR(64) NOT NULL,
  `pass` VARCHAR(64) NOT NULL,
  `avatar` VARCHAR(128) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `roadmaps`.`project`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `roadmaps`.`project` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `current` VARCHAR(64) NULL,
  `users_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_project_users1_idx` (`users_id` ASC),
  CONSTRAINT `fk_project_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `roadmaps`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `roadmaps`.`task`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `roadmaps`.`task` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(128) NOT NULL,
  `start_date` DATETIME NULL,
  `end_date` DATETIME NULL,
  `description` TEXT NOT NULL,
  `parent_task_id` INT NULL,
  `done` TINYINT NOT NULL,
  `project_id` INT NOT NULL,
  `order` INT NOT NULL,
  `color` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_task_task_idx` (`parent_task_id` ASC),
  INDEX `fk_task_project1_idx` (`project_id` ASC),
  CONSTRAINT `fk_task_task`
    FOREIGN KEY (`parent_task_id`)
    REFERENCES `roadmaps`.`task` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_task_project1`
    FOREIGN KEY (`project_id`)
    REFERENCES `roadmaps`.`project` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `roadmaps`.`compte_mail`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `roadmaps`.`compte_mail` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `pseudo` VARCHAR(64) NOT NULL,
  `email` VARCHAR(64) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `roadmaps`.`users_has_task`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `roadmaps`.`users_has_task` (
  `users_id` INT NOT NULL,
  `task_id` INT NOT NULL,
  `compte_mail_id` INT NOT NULL,
  `mail_sent` INT NOT NULL,
  PRIMARY KEY (`users_id`, `task_id`, `compte_mail_id`),
  INDEX `fk_users_has_task_task1_idx` (`task_id` ASC),
  INDEX `fk_users_has_task_users1_idx` (`users_id` ASC),
  INDEX `fk_users_has_task_compte_mail1_idx` (`compte_mail_id` ASC),
  CONSTRAINT `fk_users_has_task_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `roadmaps`.`users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_users_has_task_task1`
    FOREIGN KEY (`task_id`)
    REFERENCES `roadmaps`.`task` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_users_has_task_compte_mail1`
    FOREIGN KEY (`compte_mail_id`)
    REFERENCES `roadmaps`.`compte_mail` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `roadmaps`.`historique_mail`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `roadmaps`.`historique_mail` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `date_envoi` DATETIME NOT NULL,
  `task_id` INT NOT NULL,
  `task_label` VARCHAR(128) NOT NULL,
  `task_start_date` DATETIME NOT NULL,
  `project_id` VARCHAR(64) NOT NULL,
  `project_name` VARCHAR(64) NOT NULL,
  `compte_mail_pseudo` VARCHAR(64) NOT NULL,
  `compte_mail_email` VARCHAR(64) NOT NULL,
  `task_end_date` DATETIME NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
