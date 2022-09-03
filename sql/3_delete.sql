ALTER TABLE `vehicle`
    ADD COLUMN `deleted` DATETIME NULL AFTER `vehicle_type_id`;

ALTER TABLE `employee`
    ADD COLUMN `deleted` DATETIME NULL AFTER `phonenumber`;
