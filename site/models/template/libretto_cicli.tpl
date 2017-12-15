{* Smarty HTML 
@param
$data = Array(
corso
data_fine
durata
nome
)

*}

{literal}

    <style>
        #container {
            text-align:center;
        }
        .attestato {
            margin: 0 auto;
            text-align:center;
        }

        h1, h2 {
            text-align:center;
            color: navy;
            font-family: times;
        }

        h1 {
            font-size: 24pt;
        }
        h2 {
            font-size: 18pt;
        }
        p {
            color: #000;
            font-family: times;
            font-size: 14pt;
            text-align:center;
        }
        p.small {
            font-size: 10pt;
        }
        p.big {
            font-size: 16pt;
            text-align:right;
        }
    </style>
{/literal}
<div id="container">



        <div>
            <h1>LIBRETTO FORMATIVO</h1>
            <p>
                di:
            </p>
            <h2>{$data.nome} {$data.cognome}</h2>

        </div>



</div>

    <div id="tracklog">


            {foreach $data.rows as $row}

                <div>

                    <div class="attestato">
                        <h4>{$row.corso}</h4>
                    </div>
                        <div style="margin-left: 40%;">data fine corso:{$row.data_fine}</div>
                        <div style="margin-left: 40%;">durata del corso:{$row.durata} giorni</div>



                </div>

                <hr  style="border-top dashed 3px;">
            {/foreach}


 </div>





