ALTER TABLE `#__gg_unit`
DROP COLUMN `tipologia`,
ADD COLUMN `is_corso`  int(10) NULL AFTER `unitapadre`,
ADD COLUMN `id_contenuto_completamento`  int(10) NULL AFTER `id_event_booking`;

CREATE TRIGGER `Protect_corsi` BEFORE UPDATE ON `#__gg_unit`
FOR EACH ROW
        BEGIN
           IF OLD.id = 1 THEN
                SET NEW.unitapadre = 0 ,
                NEW.id = 1,
                NEW.ordinamento = 0;
           END IF;
END;
