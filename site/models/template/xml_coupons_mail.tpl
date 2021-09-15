{* Smarty *}
<html>
<head>
    <title>Coupon</title>
</head>
<body>
<h1>Generazione coupon {$company_name} </h1>

<p>Gentile azienda,</p>
<p>
    la procedura di generazione coupon &egrave; andata buon fine. </p>
</p>

<p>In questa email In questa mail troverà: <br />
    <ul>
        <li>
            <b>gli account creati per i nuovi utenti</b> non ancora registrati in piattaforma (l'username corrisponde al codice fiscale). Gli utenti già registrati dovranno invece continuare a utilizzare le credenziali già in loro possesso.
        </li>
        <li>
            <b>{if ($coupons_count)>1}i{else}il{/if} {$coupons_count} coupon</b> da Lei richiesti per il corso {$course_name}
        </li>
    </ul>
</p>

<p>
    Dovrà distribuire a ogni utente le proprie credenziali e un codice coupon per lo sblocco dell'iscrizione al corso. Il codice va inserito da ciascun utente, solo al primo accesso.
</p>

<p>
    <b>Di seguito le istruzioni operative da inoltrare ai partecipanti ai corsi per fruire dei coupon: </b> <br />
    <ol>
        <li>Visitare <a href="{$piattaforma_link}">{$piattaforma_link}</a> e inserire username e password nella sezione "Accedi-Registrati" </li>
        <li>Andare nella sezione "Codice coupon" e inserire il codice alfa-numerico ricevuto (solo al primo accesso al corso) </li>
        <li>Andare alla sezione "I miei corsi" e accedere al proprio corso</li>
    </ol>
</p>

<p>
    <h3>Ecco le credenziali per gli utenti non ancora registrati alla piattaforma {$piattaforma_link}</h3>
</p>

<div style="font-family: monospace;">
{$company_users}
</div>

<h3>Ecco i coupon per il corso {$course_name}</h3>

<div style="font-family: monospace;">
   {$coupons}
</div>

<p>
    Cordiali saluti<br />
    Lo staff {$piattaforma_name}
</p>
<p>Questa mail è generata automaticamente, si prega di non rispondere.</p>

</body>
</html>
