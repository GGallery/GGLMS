{* Smarty *}
<html>
<head>
    <title>Coupon</title>
</head>
<body>
<h1>Generazione coupon {$company_name} </h1>

<p>Gentile azienda,</p>

<p>
    la procedura di attivazione delle iscrizioni &egrave; andata buon fine. </p>
</p>

{$creazione_tutor}

{$company_users}

<p>Pu&ograve; comunicare agli utenti che l'accesso &egrave; stato attivato, inoltrando a ciascuno le seguenti istruzioni operative:</p>
<p>
    <ol>
       <li>Visitare {$piattaforma_link} e inserire username e password nella sezione "Accedi-Registrati". <br />
        Se &egrave; il primo accesso, sia username che password corrispondono al codice fiscale; suggeriamo di cambiare la propria password <b>(Menu Accedi/Registrati > Modifica Dati)</b>. <br />
        Se avevi gi&agrave; effettuato un accesso utilizza le tue credenziali.
       </li>
       <li>Andare alla sezione "I miei corsi" e accedere al proprio corso</li>
    </ol>
</p>

<p>
    Cordiali saluti<br />
    Lo staff {$piattaforma_name}
</p>
<p>Questa mail è generata automaticamente, si prega di non rispondere.</p>

</body>
</html>
