ALTER TABLE `#__gg_csv_report`
ADD COLUMN `tempo_lavorativo` TIME NULL AFTER `hacompletato`,
ADD COLUMN `tempo_straordinario` TIME NULL AFTER `tempo_lavorativo`;


