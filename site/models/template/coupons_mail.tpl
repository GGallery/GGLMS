{* Smarty *}
<html>
    <head>
        <title>Coupon</title>
    </head>
    <body>
        <h1>Registrazione {$ausind.associazione_name}</h1>

        <p>Spett.le {$ausind.ragione_sociale},</p>
        <p>
            la procedura di generazione coupon &egrave; andata buon fine; inoltre &egrave; stato creato un account aziendale sul portale {$ausind.associazione_name} 
            con privilegi speciali, che permette di monitorare i Vostri coupon e prelevare gli attestati dei partecipanti.        
        </p>
        <p>
            Utilizzi queste credenziali per accedere al portale <a href="{$ausind.associazione_url}">{$ausind.associazione_name}</a>:
            <br />
            username: <span style="font-family: monospace;">{$ausind.username}</span><br />
            {if isset($ausind.password)}password: <span style="font-family: monospace;">{$ausind.password}</span><br />{/if}
            Le ricordiamo che saranno valide anche per gli eventuali futuri acquisti di corsi e-learning, custodisca quindi con cura questa mail.<br/>
            Le ricordiamo che queste credenziali <b>NON devono essere utilizzate per seguire i corsi</b>, ma solo per effettuare il monitoraggio da parte del tutor aziendale. <br>Per seguire il corso è necessario che <b>ciascun utente effettui una registrazione individuale, con credenziali scelte autonomamente</b>. 
        </p>
            Ecco {if $ausind.coupon_number>1}i{else}il{/if} {$ausind.coupon_number} coupon da Lei richiesti. I coupon non saranno attivi fino al momento della conferma di avvenuto pagamento.
        </p>

        <h3>{$coursename}</h3>

        <div style="font-family: monospace;">
		  {foreach $coupons as $coupon}
            {$coupon}<br />
        {/foreach}
        </div>

        <p>
	        <b>Per una migliore fruizione del corso consigliamo fortemente di usare browser quali Firefox (versione 4 o superiore), Google Chrome (versione 6 o superiore), Explorer (dalla versione 9)</b>
        <p>
        
        <p>
            Cordiali saluti<br />
            Lo staff {$ausind.associazione_name}
        </p>
        <p>Questa mail è generata automaticamente, si prega di non rispondere.</p>

    </body>
</html>
