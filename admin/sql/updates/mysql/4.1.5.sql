Create or REPLACE VIEW #__view_report AS
SELECT
	`coupon`.`coupon` AS `coupon`,
	`coupon`.`creation_time` AS `data_creazione`,
	`coupon`.`data_utilizzo` AS `data_utilizzo`,
	`corso_map`.`idunita` AS `id_corso`,
	`corsi`.`title` AS `titolo_corso`,
	`soc`.`id` AS `id_azienda`,
	`soc`.`title` AS `azienda`,
	`soc`.`parent_id` AS `id_piattaforma`,
	`coupon`.`venditore` AS `venditore`,
	COALESCE (
		concat(
			`ru`.`nome`,
			' ',
			`ru`.`cognome`
		),
		''
	) AS `user_`,
	`ru`.`nome` AS `nome`,
	`ru`.`cognome` AS `cognome`,
	`ru`.`id_user` AS `id_user`,
	`comp`.`cb_codicefiscale` AS `cb_codicefiscale`,
	`comp`.`cb_codiceestrenocdc3` AS `cod_farmacia`,
	(
		CASE
		WHEN isnull(`ru`.`id_user`) THEN
			- (1)
		ELSE
			COALESCE (`vuc`.`stato`, 0)
		END
	) AS `stato`,
	`vuc`.`data_inizio` AS `data_inizio`,
	`vuc`.`data_fine` AS `data_fine`,
	`vuc`.`data_inizio_extra` AS `data_inizio_extra`,
	`vuc`.`data_fine_extra` AS `data_fine_extra`,
	(
		CASE
		WHEN (
			(
				`coupon`.`data_utilizzo` + INTERVAL `coupon`.`durata` DAY
			) < now()
		) THEN
			1
		ELSE
			0
		END
	) AS `scaduto`
FROM
	(
		(
			(
				(
					(
						(
							(
								`#__gg_coupon` `coupon`
								JOIN `#__usergroups` `corsi` ON (
									(
										`coupon`.`id_gruppi` = `corsi`.`id`
									)
								)
							)
							JOIN `#__usergroups` `soc` ON (
								(
									`soc`.`id` = `coupon`.`id_societa`
								)
							)
						)
						JOIN `#__gg_usergroup_map` `corso_map` ON (
							(
								`corso_map`.`idgruppo` = `corsi`.`id`
							)
						)
					)
					LEFT JOIN `#__users` `u` ON (
						(
							`coupon`.`id_utente` = `u`.`id`
						)
					)
				)
				LEFT JOIN `#__gg_report_users` `ru` ON ((`ru`.`id_user` = `u`.`id`))
			)
			LEFT JOIN `#__gg_view_stato_user_corso` `vuc` ON (
				(
					(
						`vuc`.`id_corso` = `corso_map`.`idunita`
					)
					AND (
						`vuc`.`id_anagrafica` = `ru`.`id`
					)
				)
			)
		)
		LEFT JOIN `#__comprofiler` `comp` ON (
			(
				`comp`.`user_id` = `ru`.`id_user`
			)
		)
	)
