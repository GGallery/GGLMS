<?php
/**
 * @package		Joomla.Tutorials
 * @subpackage	Component
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		License GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');

?>

<div id="j-main-container" class="span12">

    <div class="span6">
        <canvas id="myChart" width="400" height="400"></canvas>
    </div>

    <div class="span6">
        <canvas id="myChart2" width="400" height="400"></canvas>
    </div>

</div>

<?php
echo "report aggiornato al :" .$this->state->get('params')->get('data_sync');
?>


<script>
    var ctx = document.getElementById("myChart").getContext('2d');
    var ctx2 = document.getElementById("myChart2").getContext('2d');

    var myChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: ["Utenti che hanno completato", "Utenti che non hanno completato"],
            datasets: [{
                label: '% corsi completati',
                data: [12, 19 ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)'

                ],
                borderWidth: 1
            }]
        },

    });

    var myChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ["Utenti che hanno completato", "Utenti che non hanno completato"],
            datasets: [{
                label: '% corsi completati',
                data: [20, 55 ],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)'

                ],
                borderWidth: 1
            }]
        },

    });

</script>