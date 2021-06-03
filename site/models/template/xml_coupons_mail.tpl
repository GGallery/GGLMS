{* Smarty *}
<html>
<head>
    <title>Coupon</title>
</head>
<body>
<h1>Generazione coupon {$company_name} </h1>

<p>Spett.le {$recipient_name},</p>
<p>
    la procedura di generazione coupon &egrave; andata buon fine. Ecco {if ($coupons_count)>1}i{else}il{/if} {$coupons_count} coupon da Lei richiesti.
</p>

{$company_tutor}
{$company_users}

<h3>{$course_name}</h3>

<div style="font-family: monospace;">
   {$coupons}
</div>

<p>
<p>Per accedere al corso registrati, o se hai già effettuato una registrazione, accedi con le credenziali scelte su <a href="{$piattaforma_link}">{$piattaforma_name} </a> </p>
<b>Per una migliore fruizione del corso consigliamo fortemente di usare browser quali Firefox (versione 4 o superiore), Google Chrome (versione 6 o superiore)</b>
<p>

<p>
    Cordiali saluti<br />
    Lo staff {$piattaforma_name}
</p>
<p>Questa mail è generata automaticamente, si prega di non rispondere.</p>

</body>
</html>
