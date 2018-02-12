<?php 

/* 

VS SCORM 1.2 RTE - commit.php
Rev 2009-11-30-01
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

// require "FirePHPCore/fb.php";

//  essential functions
require "subs.php";

//  read database login information and connect
require "config.php";

$db = JFactory::getDBO();

// read SCOInstanceID

$SCOInstanceID = $_REQUEST['SCOInstanceID'] * 1;
$UserID = $_REQUEST['UserID'];
$data = $_REQUEST['data'];


if (! is_array($data)) { $data = array($data); }

// iterate through the data elements
foreach ($data as $varname => $varvalue) {

	// save data to the 'scormvars' table
 	writeElement($varname,$varvalue);

	// special cases - set appropriate values in the LMS tables when they are set by the course
	if ($varname == "cmi.core.score.raw") { setInLMS('TestScore',$varvalue); }
	if ($varname == "cmi.core.lesson_status") { setInLMS('Finished',$varvalue); }

}

// return value to the calling program
print "true";
