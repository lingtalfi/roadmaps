


-- -----------------------------------------------------
-- Table `roadmaps`.`project`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `roadmaps`.`project` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
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

