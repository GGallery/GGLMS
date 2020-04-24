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


        <h3>{$course_name}</h3>

        <div style="font-family: monospace;">
		  {foreach $coupons as $coupon}
            {$coupon}<br />
        {/foreach}
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


        <a href="mailto:training@consorzioglobal.com">training@consorzioglobal.com</a> </br>
        Consorzio Global </br>
        Sede Legale Via A. Cantore 17-1° - 16149 Genova </br>
        Telefono 010.6445842  Fax 06.56562967 </br>
        Sede Operativa Via A. Cantore 30B/8 – 16149 Genova </br>
        Telefono 0100996660 Fax 0656562967 </br></br>



        <img src="http://fad.consorzioglobal.com/site/logo.jpg" width="150">
    

    </body>
</html>
