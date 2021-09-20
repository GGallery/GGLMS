{* Smarty *}
<html>
    <head>
        <title>Registrazione  {$piattaforma_name}</title>
    </head>
    <body>
        <h1>Registrazione  {$piattaforma_name}</h1>

        <p>Gentile azienda,</p>
        <p>
            A seguito delle iscrizioni effettuate sui corsi "{$piattaforma_alias}", la informiamo che è stato creato il vostro account aziendale sulla piattaforma <a href="{$piattaforma_link}">{$piattaforma_name}</a>
        </p>

        <p>
            Per accedere in qualità di tutor aziendale e monitorare la formazione degli utenti è possibile utilizzare le seguenti credenziali:
        </p>

        <div style="font-family: monospace;">
            <b> USERNAME:</b> {$user_name} <br />
           <b> PASSWORD:</b> {$user_password}
        </div>

        <p>
            Queste credenziali non vanno utilizzate per seguire la formazione, ma solo per le attività di monitoraggio.
        </p>

        <p>
            Riceverà con successive mail i coupon richiesti in fase di iscrizione, con le istruzioni per il loro utilizzo.
        </p>

        <p>
            Cordiali saluti<br />
            Lo staff {$piattaforma_link}
        </p>

        <p>Questa mail è generata automaticamente, si prega di non rispondere.</p>

    </body>
</html>
