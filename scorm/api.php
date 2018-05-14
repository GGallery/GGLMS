<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");

require_once "config.php";

$SCOInstanceID = $_REQUEST['SCOInstanceID'] * 1;
$UserID = $_REQUEST['UserID'];

$db = JFactory::getDBO();

require_once "subs.php";
$initializeCache = initializeSCO();

?>
<html>
<head>

    <title></title>
    <script language="javascript">


        console.log("APIOBJECT");
        // ------------------------------------------
        //   Status Flags
        // ------------------------------------------
        var flagFinished = false;
        var flagInitialized = false;

        // ------------------------------------------
        //   SCO Data Cache - Initialization
        // ------------------------------------------
        <?php print $initializeCache; ?>

        // ------------------------------------------
        //   SCORM RTE Functions - Initialization
        // ------------------------------------------
        function LMSInitialize(dummyString) {
            console.log("LMSInitialize: ");

            // already initialized or already finished
            if ((flagInitialized) || (flagFinished)) { return "false"; }

            // set initialization flag
            flagInitialized = true;

            // return success value
            return "true";

        }

        // ------------------------------------------
        //   SCORM RTE Functions - Getting and Setting Values
        // ------------------------------------------
        function LMSGetValue(varname) {
            console.log("LMSGetValue: " + varname + " -> " + cache[varname]);
            // not initialized or already finished
            if ((! flagInitialized) || (flagFinished)) { return "false"; }


            if(!cache[varname])
                return "null";

            if(varname === 'cmi.interactions._count')
                LMSSetValue(varname, Number(cache[varname])+1);

            return cache[varname];
        }

        function LMSSetValue(varname,varvalue) {

            // not initialized or already finished
            if ((! flagInitialized) || (flagFinished)) {
                return "false";
            }

            cache[varname] = varvalue;

            return "true";

        }

        // ------------------------------------------
        //   SCORM RTE Functions - Saving the Cache to the Database
        // ------------------------------------------
        function LMSCommit(dummyString) {
            // console.log("LMSCommit " + dummyString);
            // not initialized or already finished
            if ((! flagInitialized) || (flagFinished)) { return "false"; }

            // create request object
            var req = createRequest();

            // code to prevent caching
            var d = new Date();

            // set up request parameters - uses POST method
            req.open('POST','commit.php',false);

            // var currentInteraction = LMSGetValue("cmi.interactions._count");

            // create a POST-formatted list of cached data elements
            // include only SCO-writeable data elements
            var params = 'SCOInstanceID=<?php print $SCOInstanceID; ?>&code='+d.getTime();
            params += "&UserID=<?php print $UserID; ?>";


            for(var index in cache) {
                console.log( index + " : " + cache[index] );
                params += "&data["+urlencode(index)+"]="+urlencode(cache[index]);
            }

            // request headers
            req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

            // submit to the server for processing
            req.send(params);

            // process returned data - error condition
            if (req.status != 200) {
                alert('Problema con la connessione alla piattaforma. Chiudere questa finestra e riprovare. Se il problema persiste contattare l\'amministratore della piattaforma');
                console.log(params);
                return "false";
            }

            // process returned data - OK
            else {
                return "true";
            }

        }

        // ------------------------------------------
        //   SCORM RTE Functions - Closing The Session
        // ------------------------------------------
        function LMSFinish(dummyString) {
            console.log("LMSFinish" + dummyString);
            // not initialized or already finished
            if ((! flagInitialized) || (flagFinished)) { return "false"; }

            // commit cached values to the database
            LMSCommit('');

            // create request object
            var req = createRequest();

            // code to prevent caching
            var d = new Date();

            // set up request parameters - uses GET method
            req.open('GET','finish.php?SCOInstanceID=<?php print $SCOInstanceID; ?>&UserID=<?php print $UserID; ?>&code='+d.getTime(),false);

            // submit to the server for processing
            req.send(null);

            // process returned data - error condition
            if (req.status != 200) {
                alert('Problem with AJAX Request in LMSFinish()');
                return "";
            }

            // set finish flag
            flagFinished = true;

            // return to calling program
            return "true";

        }

        // ------------------------------------------
        //   SCORM RTE Functions - Error Handling
        // ------------------------------------------
        function LMSGetLastError() {
            console.log("LMSGetLastError");
            return 0;
        }

        function LMSGetDiagnostic(errorCode) {
            console.log("LMSGetDiagnostic");
            return "diagnostic string";
        }

        function LMSGetErrorString(errorCode) {
            console.log("LMSGetErrorString");
            return "error string";
        }

        // ------------------------------------------
        //   AJAX Request Handling
        // ------------------------------------------
        function createRequest() {
            console.log("createRequest");
            var request;
            try {
                request = new XMLHttpRequest();
            }
            catch (tryIE) {
                try {
                    request = new ActiveXObject("Msxml2.XMLHTTP");
                }
                catch (tryOlderIE) {
                    try {
                        request = new ActiveXObject("Microsoft.XMLHTTP");
                    }
                    catch (failed) {
                        alert("Error creating XMLHttpRequest");
                    }
                }
            }
            return request;
        }

        // ------------------------------------------
        //   URL Encoding
        // ------------------------------------------
        function urlencode( str ) {

            var histogram = {}, unicodeStr='', hexEscStr='';
            var ret = (str+'').toString();

            var replacer = function(search, replace, str) {
                var tmp_arr = [];
                tmp_arr = str.split(search);
                return tmp_arr.join(replace);
            };

            // The histogram is identical to the one in urldecode.
            histogram["'"]   = '%27';
            histogram['(']   = '%28';
            histogram[')']   = '%29';
            histogram['*']   = '%2A';
            histogram['~']   = '%7E';
            histogram['!']   = '%21';
            histogram['%20'] = '+';
            histogram['\u00DC'] = '%DC';
            histogram['\u00FC'] = '%FC';
            histogram['\u00C4'] = '%D4';
            histogram['\u00E4'] = '%E4';
            histogram['\u00D6'] = '%D6';
            histogram['\u00F6'] = '%F6';
            histogram['\u00DF'] = '%DF';
            histogram['\u20AC'] = '%80';
            histogram['\u0081'] = '%81';
            histogram['\u201A'] = '%82';
            histogram['\u0192'] = '%83';
            histogram['\u201E'] = '%84';
            histogram['\u2026'] = '%85';
            histogram['\u2020'] = '%86';
            histogram['\u2021'] = '%87';
            histogram['\u02C6'] = '%88';
            histogram['\u2030'] = '%89';
            histogram['\u0160'] = '%8A';
            histogram['\u2039'] = '%8B';
            histogram['\u0152'] = '%8C';
            histogram['\u008D'] = '%8D';
            histogram['\u017D'] = '%8E';
            histogram['\u008F'] = '%8F';
            histogram['\u0090'] = '%90';
            histogram['\u2018'] = '%91';
            histogram['\u2019'] = '%92';
            histogram['\u201C'] = '%93';
            histogram['\u201D'] = '%94';
            histogram['\u2022'] = '%95';
            histogram['\u2013'] = '%96';
            histogram['\u2014'] = '%97';
            histogram['\u02DC'] = '%98';
            histogram['\u2122'] = '%99';
            histogram['\u0161'] = '%9A';
            histogram['\u203A'] = '%9B';
            histogram['\u0153'] = '%9C';
            histogram['\u009D'] = '%9D';
            histogram['\u017E'] = '%9E';
            histogram['\u0178'] = '%9F';

            // Begin with encodeURIComponent, which most resembles PHP's encoding functions
            ret = encodeURIComponent(ret);

            for (unicodeStr in histogram) {
                hexEscStr = histogram[unicodeStr];
                ret = replacer(unicodeStr, hexEscStr, ret); // Custom replace. No regexing
            }

            // Uppercase for full PHP compatibility
            return ret.replace(/(\%([a-z0-9]{2}))/g, function(full, m1, m2) {
                return "%"+m2.toUpperCase();
            });
        }

    </script>

</head>
<body>
</body>
</html>