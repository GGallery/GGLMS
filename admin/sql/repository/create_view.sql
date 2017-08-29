CREATE
VIEW `report`AS
SELECT corso.titolo as titolo_corso, corso.id_event_booking, corso.id_contenuto_completamento, unita.titolo as titolo_unita, contenuti.titolo as titolo_contenuto, r.*
FROM un_gg_report as r
INNER JOIN un_gg_unit as corso on corso.id = r.id_corso
INNER JOIN un_gg_unit as unita on unita.id = r.id_unita
INNER JOIN un_gg_contenuti as contenuti on contenuti.id = r.id_contenuto ;



#add TIMESTAMP
ALTER TABLE un_gg_scormvars ADD `timestamp` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;