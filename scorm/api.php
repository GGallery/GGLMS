<?php

/* 

VS SCORM 1.2 RTE - api.php
Rev 2010-04-30-01
Copyright (C) 2009, Addison Robson LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, 
Boston, MA 02110-1301, USA.

*/

require_once "config.php";
require_once "subs.php";

$SCOInstanceID = $_REQUEST['SCOInstanceID'] * 1;
$UserID = $_REQUEST['UserID'];

$db = JFactory::getDBO();

$initializeCache = initializeSCO();


?>
<html>
<head>
    <title></title>
    <script language="javascript">

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

        function hello(){

            console.log("funziona");
            return "true";
        }
        // ------------------------------------------
        //   SCORM RTE Functions - Getting and Setting Values
        // ------------------------------------------
        function LMSGetValue(varname) {
            console.log("LMSGetValue: " + varname + " -> " + cache[varname]);
            // not initialized or already finished
            if ((! flagInitialized) || (flagFinished)) { return "false"; }

            // otherwise, return the requested data
//            switch(varname) {
//                case 'cmi.interactions._count':
//                {
//                    var keys = [];
//                    for(var key in cache) {
//                        if(cache.hasOwnProperty(key)) { //to be safe
//                            keys.push(key);
//                        }
//                    }
//
//                    console.log(keys);
//                    var count;
//                    var search;
//
//                    for(var i=0; i<=keys.length; i++)
//                    {
//                        search = "cmi.interaction."+i;
//                        if(keys[i].indexOf(search))
//                        count = i;
//
//                        console.log("COUNT---->>>"+count);
//                    }
//                    cache[varname]= count;
//                    return count;
//
//
//                }
//
//                    break;
//                default:
//                    return cache[varname];
//            }


            return cache[varname];
        }

        function LMSSetValue(varname,varvalue) {

            // not initialized or already finished
            if ((! flagInitialized) || (flagFinished)) {
                console.log("LMSSetValue: " + varname + " -> "+varvalue + "  l'esito e: NON E' STATO INIZIALIZATO!");
                return "false"; }

            // otherwise, set the requested data, and return success value

            if(!cache[varname]) {
//                console.log(varname + " non è presente nella cache!!!!!!!!");
            }

            //verifico se è una interaction

            var varExplode = varname.split('.');
            console.log('varExplode' + varExplode);
            if(varExplode[1]=='interactions' && varExplode[2]!='_count'&& varExplode[2]!='_children' ) {
                var newcount= parseInt(varExplode[2]) + 1;
                console.log('aggiorno la cache count con valore dal varExplode'+ newcount);
                LMSSetValue('cmi.interactions._count', newcount);
            }
            cache[varname] = varvalue;

            return "true";

        }

        // ------------------------------------------
        //   SCORM RTE Functions - Saving the Cache to the Database
        // ------------------------------------------
        function LMSCommit(dummyString) {
            console.log("LMSCommit: " + dummyString);
            // not initialized or already finished
            if ((! flagInitialized) || (flagFinished)) { return "false"; }

            // create request object
            var req = createRequest();

            // code to prevent caching
            var d = new Date();

            // set up request parameters - uses POST method
            req.open('POST','commit.php',false);

            var currentInteraction = LMSGetValue("cmi.interactions._count");
         
            // create a POST-formatted list of cached data elements
            // include only SCO-writeable data elements
            var params = 'SCOInstanceID=<?php print $SCOInstanceID; ?>&code='+d.getTime();
            params += "&UserID=<?php print $UserID; ?>";


            for(var index in cache) {
                console.log( index + " : " + cache[index] );
                params += "&data["+index+"]="+urlencode(cache[index]);
            }


            /*
            params += "&data[cmi.core.lesson_location]="+urlencode(cache['cmi.core.lesson_location']);
            params += "&data[cmi.core.lesson_status]="+urlencode(cache['cmi.core.lesson_status']);
            params += "&data[cmi.core.exit]="+urlencode(cache['cmi.core.exit']);
            params += "&data[cmi.core.session_time]="+urlencode(cache['cmi.core.session_time']);
            params += "&data[cmi.core.score.raw]="+urlencode(cache['cmi.core.score.raw']);
            params += "&data[cmi.suspend_data]="+urlencode(cache['cmi.suspend_data']);



            params += "&data[cmi.interactions."+ currentInteraction + ".id]="+urlencode(cache['cmi.interactions.'+ currentInteraction + '.id']);
            params += "&data[cmi.interactions."+ currentInteraction + ".type]="+urlencode(cache['cmi.interactions.'+ currentInteraction + '.type']);
            params += "&data[cmi.interactions."+ currentInteraction + ".student_response]="+urlencode(cache['cmi.interactions.'+ currentInteraction + '.student_response']);
            params += "&data[cmi.interactions."+ currentInteraction + ".correct_responses_0.pattern]="+urlencode(cache['cmi.interactions.'+ currentInteraction + '.correct_responses_0.pattern']);
            params += "&data[cmi.interactions."+ currentInteraction + ".result]="+urlencode(cache['cmi.interactions.'+ currentInteraction + '.result']);
            params += "&data[cmi.interactions."+ currentInteraction + ".weighting]="+urlencode(cache['cmi.interactions.'+ currentInteraction + '.weighting']);
            params += "&data[cmi.interactions."+ currentInteraction + ".objectives_0.id]="+urlencode(cache['cmi.interactions.'+ currentInteraction + '.objectives_0.id']);
            params += "&data[cmi.interactions."+ currentInteraction + ".time]="+urlencode(cache['cmi.interactions.'+ currentInteraction + '.time']);
*/


            // totalInteractions =  parseInt(totalInteractions)+1;
            // LMSSetValue("cmi.interactions._count", totalInteractions);

            // request headers
            req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            req.setRequestHeader("Content-length", params.length);
            req.setRequestHeader("Connection", "close");

            // submit to the server for processing
            req.send(params);

            // process returned data - error condition
            if (req.status != 200) {
                alert('Problem with AJAX Request in LMSCommit()');
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
            console.log("urlencode");
            //
            // Ref: http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_urlencode/
            //
            // http://kevin.vanzonneveld.net
            // +   original by: Philip Peterson
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +      input by: AJ
            // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   improved by: Brett Zamir (http://brettz9.blogspot.com)
            // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +      input by: travc
            // +      input by: Brett Zamir (http://brettz9.blogspot.com)
            // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
            // +   improved by: Lars Fischer
            // %          note 1: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
            // *     example 1: urlencode('Kevin van Zonneveld!');
            // *     returns 1: 'Kevin+van+Zonneveld%21'
            // *     example 2: urlencode('http://kevin.vanzonneveld.net/');
            // *     returns 2: 'http%3A%2F%2Fkevin.vanzonneveld.net%2F'
            // *     example 3: urlencode('http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a');
            // *     returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a'

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

<p>&nbsp;

</body>
</html>