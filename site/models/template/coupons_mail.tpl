{* Smarty *}
<html>
    <head>
        <title>Coupon</title>
    </head>
    <body>
        <h1>Generazione coupon {$company_name} </h1>

        <p>{$recipient_name},</p>
        <p>
            la procedura di generazione coupon &egrave; andata buon fine. Ecco {if ($coupons_count)>1}i{else}il{/if} {$coupons_count} coupon da Lei richiesti.


        <h3>{$course_name}</h3>

        <div style="font-family: monospace;">
		  {foreach $coupons as $coupon}
            {$coupon}<br />
        {/foreach}
        </div>


        <p>I codici devono essere distribuiti agli utenti che devono effettuare la formazione, uno per ciascuno.</p>
        <p>ISTRUZIONI PER I PARTECIPANTI: accedere alla piattaforma <a href="{$piattaforma_link}" target="_blank">{$piattaforma_name}</a> con il proprio account (o registrarne uno se non hanno mai effettuato l'accesso, scegliendo autonomamente username e password) e inserire il codice alla voce CODICE COUPON per sbloccare l'iscrizione. Il corso sarà disponibile alla voce I MIEI CORSI, senza più necessità di inserire il codice.<br/>
        {$coupon_duration}.<br/>
        </p>


	        <b>Per una migliore fruizione del corso consigliamo fortemente di usare browser quali Firefox o Google Chrome</b>
        <p>

        <p>
            Cordiali saluti<br />
            Lo staff {$piattaforma_name}
        </p>
        <p>Questa mail è generata automaticamente, si prega di non rispondere.</p>

    </body>
</html>
