<?php

echo "<h1> REPORT FIASO </h1>";

?>

<div class="container-fluid">
    <div id="toolbar" class="container-fluid" style="border:1px solid blue;border-radius: 4px;">
        <h4 class="text-left ml-2 mt-0" style="color: #325482; margin-bottom: 30px !important; margin-top: 40px !important;"><?php echo  "REPORT" ?></h4>
        <div style="margin-bottom: 30px; display:flex; flex-direction: column; align-items: center; justify-content: space-between;">

            <div> wobble </div> <p id="userss"></p>

            <div class="form-group col-md-3">
                <label for="export_csv"><br></label>
                <button type="button" id="export_csv" class="form-group btn" style="background-color: #17a2b8;border: none;font-size: 16px; font-weight : bold ;">SCARICA IL REPORT</button>
            </div>
        </div>

    </div>

</div>

<script>

    fetch("https://test.gallerygroup.dvl.to/home/index.php?option=com_gglms&task=api.completamentoCorsoFiaso")
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json(); // Assumes the response is JSON
        })
        .then(data => {
            console.log(data);
        })
        .catch(error => {
            console.error("There was a problem with the fetch operation:", error);
        });

    const exportCsv = document.getElementById('export_csv')
    exportCsv.addEventListener('click', function () {
        fetch("https://test.gallerygroup.dvl.to/home/index.php?option=com_gglms&task=api.getReportCorsoFiaso", {
            method: 'GET'
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = 'report.csv';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
            })
            .catch(error => {
                console.error('There was an error downloading the CSV file:', error);
            });
    });

</script>
