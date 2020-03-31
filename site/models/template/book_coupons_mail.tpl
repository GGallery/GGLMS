{* Smarty *}
<html>
<head>
    <title>Coupon</title>
</head>
<body>
<h1>Prenotazione coupon {$company_name} </h1>

<p>Spett.le {$recipient_name},</p>
<p>
    E' stata effettuata una richiesta di prenotazione di <strong>{$qty}</strong> coupon per il corso
    <strong>{$titolo_corso}</strong> ( codice: {$codice_corso} ) da parte dell'azienda {$company_name} <br>
    Di seguito i dati dell'azienda:

<p>
    <strong>Partita IVA: </strong> {$piva} <br>
    <strong>Ragione Sociale: </strong> {$company_name} <br>
    <strong>Email referente aziendale: </strong> {$email} <br>
    <strong>Codice ATECO: </strong> {$ateco} <br>


</p>


<p>
    L'azienda afferma di {if {$associato} ==='false'} NON {/if} essere associata a {$piattaforma_name}, il prezzo
    stimato è {if (is_numeric($_prezzo))}  €  {/if} {$_prezzo}
</p>


<p>
    Cordiali saluti<br/>
    Lo staff {$piattaforma_alias}
</p>
<p>Questa mail è generata automaticamente, si prega di non rispondere.</p>

</body>
</html>
