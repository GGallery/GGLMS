{* Smarty *}
<html>
    <head>
        <title>Registrazione  {$piattaforma_name}</title>
    </head>
    <body>
        <h1>Registrazione  {$piattaforma_name}</h1>

        <p>Spett.le {$company_name},</p>
        <p>la informiamo che è stato creato un account aziendale sulla piattaforma <a href="{$piattaforma_link}">{$piattaforma_name}</a></p>
        <p>
            Per accedere in qualità di tutor aziendale e monitorare la formazione degli utenti, è possibile utilizzare le seguenti credenziali:
        </p>

        <div style="font-family: monospace;">
            <b> USERNAME:</b> {$user_name} <br>
           <b> PASSWORD:</b> {$user_password}
        </div>
        <p>
	        <b>Per una migliore fruizione dei contenuti della piattaforma le consigliamo fortemente di usare browser quali Firefox (versione 4 o superiore), Google Chrome (versione 6 o superiore)</b>
        </p>
        <p>
            Le ricordiamo che queste credenziali NON devono invece essere usate per seguire i corsi
        <p>


        <p>
            Cordiali saluti<br />
            Lo staff {$piattaforma_name}
        </p>
        <p>Questa mail è generata automaticamente, si prega di non rispondere.</p>

    </body>
</html>
