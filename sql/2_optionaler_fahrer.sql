ALTER TABLE `reservation`
    ADD COLUMN `driver_id` INT NULL DEFAULT NULL AFTER `thermo`,
    ADD CONSTRAINT `FK_reservation_employee` FOREIGN KEY (`driver_id`) REFERENCES `employee` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION;
