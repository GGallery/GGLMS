<?php
/**
 * Created by IntelliJ IDEA.
 * User: Francesca
 * Date: 26/01/2021
 * Time: 09:10
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.model');
require_once JPATH_COMPONENT . '/models/contenuto.php';
require_once JPATH_COMPONENT . '/models/report.php';
require_once JPATH_COMPONENT . '/models/unita.php';
require_once JPATH_COMPONENT . '/models/config.php';
require_once JPATH_COMPONENT . '/models/generacoupon.php';
require_once JPATH_COMPONENT . '/models/syncdatareport.php';
require_once JPATH_COMPONENT . '/models/syncviewstatouser.php';
require_once JPATH_COMPONENT . '/models/users.php';
require_once JPATH_COMPONENT . '/controllers/zoom.php';
require_once JPATH_COMPONENT . '/controllers/api.php';

class gglmsControllerMt extends JControllerLegacy {

    private $_user;
    private $_japp;
    public $_params;
    public $_db;
    private $_config;
    private $_filterparam;
    public $mail_debug;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_japp = JFactory::getApplication();
        $this->_params = $this->_japp->getParams();

        $this->_user = JFactory::getUser();
        $this->_db = JFactory::getDbo();
        $this->_config = new gglmsModelConfig();

        $this->_filterparam->id_utente = JRequest::getVar('id_utente');
        $this->_filterparam->id_corso = JRequest::getVar('id_corso');
        $this->_filterparam->anno_ref = JRequest::getVar('anno_ref');
        $this->_filterparam->secret = JRequest::getVar('secret');

        $this->mail_debug = $this->_config->getConfigValue('mail_debug');
        $this->mail_debug = ($this->mail_debug == "" || is_null($this->mail_debug)) ? "luca.gallo@gallerygroup.it" : $this->mail_debug;


    }

    private function encrypt_decrypt($action, $string, $secret_key, $secret_iv) {
        //echo "entrato<br>";
        //echo $string;die;
        $output = null;
        // metodo di crypt
        $encrypt_method = "AES-256-CBC";
        // hash
        $key = hash('sha256', $secret_key);
        // AES-256-CBC si aspetta 16 bytes
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        // cripta la chiave
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } // decripta la chiave
        else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

    public function test_() {
        try {

            $paganti2024 = [569,573,574,578,579,581,584,588,589,590,591,592,593,596,599,601,603,604,607,609,611,614,620,621,627,629,630,631,633,635,636,637,641,644,648,649,650,653,656,657,658,659,660,661,663,664,666,667,669,670,671,672,673,674,676,678,682,684,685,686,687,688,689,694,696,697,698,703,704,705,708,711,712,713,714,715,716,719,720,722,724,725,727,730,731,734,735,736,738,739,740,741,743,744,745,746,747,749,750,753,754,755,757,758,759,760,761,762,763,764,766,767,769,770,771,772,774,776,777,781,782,783,787,788,790,794,795,797,800,801,802,803,804,805,806,807,809,810,811,813,814,817,819,820,821,822,823,826,829,830,832,835,836,839,840,842,843,844,845,847,848,850,851,853,854,858,860,861,863,864,865,866,867,868,873,875,876,877,879,880,881,882,883,884,885,888,889,890,891,892,893,894,897,898,903,904,905,908,910,911,912,915,916,917,920,923,924,925,926,927,928,929,931,933,934,935,936,938,939,942,943,946,949,952,953,955,960,961,962,963,964,965,967,968,970,971,973,980,981,982,984,985,986,992,995,1001,1002,1006,1007,1010,1011,1013,1014,1016,1017,1018,1022,1023,1027,1030,1031,1033,1036,1037,1043,1044,1046,1048,1050,1051,1052,1053,1055,1056,1058,1059,1060,1061,1062,1063,1064,1065,1066,1069,1073,1074,1075,1077,1079,1080,1082,1084,1086,1087,1090,1093,1097,1099,1102,1104,1105,1106,1107,1113,1116,1117,1118,1119,1122,1123,1124,1125,1126,1128,1130,1131,1132,1133,1134,1136,1137,1138,1139,1141,1145,1146,1147,1148,1149,1150,1152,1153,1155,1156,1157,1160,1162,1165,1166,1167,1168,1170,1171,1172,1173,1174,1175,1176,1177,1178,1180,1181,1182,1184,1185,1187,1188,1189,1190,1192,1193,1197,1200,1201,1203,1204,1205,1206,1208,1210,1212,1214,1216,1218,1219,1220,1222,1223,1228,1229,1230,1231,1233,1236,1238,1240,1241,1242,1243,1244,1246,1247,1248,1249,1250,1253,1254,1255,1256,1257,1258,1260,1261,1262,1264,1265,1267,1269,1271,1272,1273,1274,1275,1276,1277,1278,1280,1281,1282,1283,1284,1286,1287,1288,1295,1300,1302,1304,1305,1306,1307,1309,1310,1311,1312,1314,1315,1316,1318,1319,1321,1323,1324,1328,1329,1330,1335,1336,1337,1338,1341,1344,1345,1347,1351,1353,1354,1355,1356,1358,1359,1360,1361,1363,1365,1367,1369,1371,1372,1374,1376,1378,1379,1384,1385,1386,1387,1390,1391,1392,1393,1397,1400,1402,1405,1406,1408,1409,1411,1412,1413,1414,1417,1420,1421,1423,1426,1432,1433,1434,1435,1436,1437,1438,1439,1440,1441,1442,1443,1446,1447,1449,1451,1452,1453,1454,1458,1460,1461,1462,1463,1465,1466,1469,1470,1473,1474,1475,1476,1479,1480,1482,1484,1487,1488,1489,1492,1493,1497,1499,1500,1501,1502,1505,1506,1507,1509,1510,1514,1517,1518,1519,1524,1527,1528,1529,1531,1532,1534,1535,1536,1538,1541,1544,1545,1546,1547,1548,1552,1553,1554,1556,1560,1561,1562,1566,1567,1568,1569,1571,1573,1574,1575,1576,1577,1579,1580,1581,1582,1584,1586,1588,1589,1591,1603,1605,1607,1613,1618,1620,1623,1626,1629,1630,1632,1634,1635,1637,1639,1641,1643,1644,1649,1651,1652,1654,1657,1658,1663,1670,1676,1680,1683,1684,1685,1686,1688,1689,1690,1696,1697,1699,1701,1706,1708,1710,1711,1715,1716,1717,1721,1722,1724,1736,1737,1738,1739,1741,1742,1744,1751,1759,1762,1763,1768,1771,1772,1773,1774,1775,1778,1779,1781,1782,1784,1787,1788,1793,1795,1797,1798,1799,1800,1801,1802,1803,1804,1805,1807,1808,1809,1810,1811,1812,1813,1814,1815,1816,1817,1818,1819,1820,1821,1822,1823,1824,1825,1826,1827,1828,1829,1830,1831,1832,1833,1834,1835,1836,1837,1838,1839,1840,1841,1842,1843,1846,1848,1849,1850,1851,1852,1853,1854,1855,1856,1857,1858,1859,1860,1861,1862,1863,1865,1867,1868,1869,1870,1871,1872,1873,1874,1875,1877,1879,1880,1881,1882,1883,1884,1885,1886,1887,1888,1889,1891,1893,1894,1895,1897,1898,1899,1901,1902,1904,1905,1906,1907,1908,1909,1910,1911,1913,1914,1916,1919,1920,1921,1922,1923,1924,1925,1926,1928,1929,1930,1931,1932,1933,1934,1935,1936,1937,1938,1939,1940,1941,1942,1943,1944,1946,1947,1948,1949,1950,1951,1952,1953,1954,1955,1956,1957,1959,1961,1962,1963,1964,1965,1966,1968,1969,1970,1971,1972,1973,1974,1975,1976,1977,1978,1980,1981,1982,1983,1984,1985,1986,1987,1988,1989,1990,1992,1993,1994,1995,1996,1998,1999,2000,2001,2002,2003,2004,2006,2007,2009,2010,2011,2012,2013,2014,2015,2016,2017,2018,2021,2023,2024,2026,2027,2028,2029,2030,2031,2032,2034,2035,2036,2037,2038,2039,2040,2041,2042,2043,2044,2045,2046,2047,2048,2049,2050,2051,2052,2053,2054,2055,2060,2063,2064,2065,2068,2069,2070,2071,2072,2073,2074,2075,2076,2077,2078,2079,2080,2081,2082,2084,2085,2086,2089,2090,2091,2092,2095,2096,2101,2102,2103,2104,2105,2106,2107,2110,2111,2112,2115,2116,2117,2118,2119,2120,2122,2123,2126,2127,2128,2129,2130,2131,2132,2133,2134,2135,2136,2137,2139,2140,2142,2143,2144,2145,2146,2147,2148,2149,2150,2151,2152,2153,2154,2155,2156,2157,2158,2159,2160,2161,2162,2163,2164,2165,2166,2167,2168,2169,2172,2173,2174,2175,2177,2178,2179,2184,2186];
            $paganti2023 = [568,569,573,574,575,581,582,583,584,589,590,591,592,593,595,596,597,598,599,601,603,604,607,609,611,612,614,617,620,621,623,625,627,629,630,631,633,634,635,636,637,638,641,643,648,650,652,653,654,655,656,659,660,661,662,663,664,666,669,670,671,672,673,674,678,679,682,684,685,686,687,688,690,691,694,696,697,698,699,703,704,705,708,711,712,714,715,716,719,720,722,724,725,727,729,730,734,735,736,738,739,740,741,742,743,744,746,747,748,749,750,752,753,754,755,758,759,760,761,762,763,765,766,767,769,770,772,773,774,776,777,780,786,787,788,790,793,794,795,798,800,801,802,803,804,805,806,807,808,809,810,811,813,817,819,820,821,822,823,824,829,830,832,835,836,837,839,840,841,842,843,844,845,847,848,850,851,852,853,854,856,860,861,863,864,865,866,867,868,873,874,875,876,879,880,881,882,883,885,886,888,889,890,891,892,893,894,897,898,901,902,903,904,905,909,910,911,912,915,916,917,918,923,924,925,926,927,928,929,931,934,935,936,938,940,942,943,945,946,948,949,952,953,955,956,958,960,963,964,965,967,968,969,970,971,972,973,975,980,981,982,983,984,986,987,988,989,991,992,994,995,1000,1001,1002,1004,1005,1007,1009,1010,1011,1013,1014,1015,1016,1017,1018,1022,1023,1027,1028,1029,1030,1031,1033,1037,1044,1046,1048,1053,1055,1058,1059,1060,1061,1062,1063,1064,1065,1066,1069,1074,1075,1077,1079,1080,1082,1084,1086,1088,1090,1093,1094,1097,1099,1101,1103,1104,1105,1106,1107,1109,1110,1113,1116,1117,1120,1122,1123,1125,1126,1128,1130,1131,1132,1133,1134,1136,1137,1138,1139,1140,1141,1144,1146,1147,1148,1150,1152,1156,1157,1158,1159,1160,1162,1163,1164,1165,1167,1168,1171,1172,1173,1174,1175,1176,1177,1178,1179,1180,1184,1187,1188,1189,1190,1192,1193,1200,1202,1203,1204,1205,1208,1214,1218,1219,1220,1222,1223,1227,1228,1229,1230,1231,1232,1233,1234,1236,1240,1242,1244,1247,1248,1249,1250,1251,1255,1256,1257,1258,1260,1261,1262,1264,1265,1267,1269,1271,1272,1273,1274,1276,1277,1278,1283,1285,1287,1288,1290,1294,1295,1298,1300,1302,1303,1304,1305,1306,1307,1309,1310,1311,1312,1315,1316,1319,1322,1323,1324,1327,1328,1329,1330,1335,1336,1337,1338,1341,1344,1345,1347,1353,1354,1358,1359,1361,1362,1364,1366,1369,1371,1377,1378,1379,1380,1384,1385,1386,1387,1390,1391,1392,1393,1394,1397,1400,1402,1405,1406,1408,1409,1411,1412,1413,1414,1418,1420,1423,1426,1427,1429,1432,1433,1435,1437,1438,1441,1442,1443,1446,1447,1448,1451,1452,1453,1454,1455,1458,1460,1462,1463,1465,1466,1469,1470,1471,1472,1473,1475,1476,1477,1478,1479,1480,1482,1483,1484,1486,1487,1488,1489,1490,1492,1493,1494,1495,1496,1497,1498,1499,1500,1501,1502,1503,1505,1506,1507,1508,1509,1510,1511,1512,1514,1515,1517,1519,1520,1521,1522,1523,1524,1525,1526,1527,1528,1529,1530,1531,1532,1533,1534,1535,1536,1538,1539,1540,1541,1542,1544,1546,1547,1548,1549,1550,1551,1552,1553,1554,1555,1556,1557,1558,1559,1560,1561,1563,1564,1565,1566,1567,1568,1569,1570,1571,1572,1573,1574,1575,1576,1577,1578,1579,1580,1581,1582,1583,1584,1585,1586,1588,1589,1590,1591,1593,1594,1595,1596,1597,1598,1600,1601,1602,1603,1605,1606,1607,1608,1610,1611,1613,1614,1615,1616,1617,1618,1619,1620,1621,1622,1623,1625,1626,1627,1628,1629,1630,1631,1632,1634,1635,1636,1637,1638,1639,1640,1641,1643,1644,1647,1648,1649,1651,1652,1653,1654,1656,1657,1658,1662,1663,1664,1667,1668,1669,1670,1672,1673,1675,1676,1677,1678,1679,1680,1683,1684,1685,1686,1687,1688,1689,1690,1691,1692,1694,1695,1696,1697,1698,1699,1701,1702,1705,1706,1707,1708,1709,1710,1711,1712,1713,1715,1716,1717,1718,1720,1721,1722,1724,1725,1726,1727,1728,1729,1730,1731,1732,1733,1734,1735,1736,1737,1738,1739,1740,1741,1742,1743,1744,1745,1746,1748,1749,1750,1751,1752,1753,1755,1756,1757,1758,1759,1761,1762,1763,1765,1766,1768,1769,1770,1771,1772,1773,1774,1775,1776,1778,1779,1780,1781,1784,1785,1786,1787,1788,1796];
            $badPayment = [];

            foreach($paganti2023 as $pagante) {
                if (!in_array($pagante, $paganti2024)) $badPayment[] = $pagante;
            }

            echo implode(",", $badPayment);
            
        }
        catch(Exception $e) {
            echo $e->getMessage();
        }
        $this->_japp->close();

    }

    public function test_xml_import()
    {
        $api = new gglmsControllerApi();
        $ragione_sociale = "Utenti privati skillab";
        $piva = "00000000000";
        $email = "skillabfad@skillab.it";
        $get_corsi = [
            'Iscritti_20220214153157.xml',
            'Corsi_20220214153157.xml',
            'Corsi_20220214150901.xml',
            'Iscritti_20220214150901.xml'
        ];

        echo $api->load_corsi_from_xml(16, $ragione_sociale, $piva, $email, true, $get_corsi);
    }

    public function massive_password_update()
    {

        try {

            $secret = trim($this->_filterparam->secret);
            $local_secret = $this->encrypt_decrypt('encrypt', 'GGallery00!!!__', 'GGallery00__', 'GGallery00__');

            if ($secret != $local_secret)
                throw new Exception("Secret key error", E_USER_ERROR);

            $new_password = 'JCcW*3lY7W';

            $query = $this->_db->getQuery(true);
            $query->update("#__users");
            $query->set("password = " . $this->_db->quote(JUserHelper::hashPassword($new_password)));
            $query->where('id > 295');

            $this->_db->setQuery((string) $query);

            if (!$this->_db->execute())
                throw new Exception("update query ko -> " . $query, E_USER_ERROR);

            echo "FATTO!";

        }
        catch(Exception $e) {
            echo $e->getMessage();
        }

        $this->_japp->close();

    }

    public function sinpe_set_morosi()
    {
        try {

            $arr_ids = array(9,22,23,40,44,48,54,56,62,64,65,80,81,100,101,104,119,126,139,143,153,188,198,220,225,235,236,248,250,252,262,266,269,271,275,287,301,320,322,327,361,390,395,401,436,465,473,481,491,510,517,529,538,542,587,600,632,658,665,707,731,733,746,747,767,778,780,788,820,850,872,873,878,886,906,911,927,946,951,958,998,999,1008,1017,1027,1028,1041,1045,1049,1055,1061,1088,1094,1101,1108,1110,1111,1117,1119,1135,1136,1143,1176,1202,1230,1255,1260,1361,1402,1457,1482,1583,1587,1629,1674,1711,1719,1720,1722,1741,1785,1852,1854,1920,1921,1928,1942,1964,2017,2020,2027,3051,3116,3171,3237,3243,3275,3350,3405,3434,3440,3445,3486,3490,3502,3590,3603,3607,3610,3656,3658,3665,3668,3675,3680,3685,3686,3698,3708,3739,3777,3816,3858,3977,3984,3987,4140,4194,4222,4268,4327,4360,4387,4388,4413,4420,4422,4427,4435,4438,4439,4440,4444,4446,4470,4479,4481,4482,4495,4497,4502,4510,4514,4520,4554,4566,4578,4588,4627,4628,4664,4812,4854,4856);

            $del_ug = 23;
            $new_ug = 20;

            $completed = 0;

            $this->_db->transactionStart();

            foreach ($arr_ids as $key => $user_id) {

                $query_sel = "SELECT user_id
                                FROM #__user_usergroup_map
                                WHERE user_id = " . $this->_db->quote($user_id) . "
                                AND group_id = " . $this->_db->quote($new_ug);

                $this->_db->setQuery($query_sel);
                $result = $this->_db->loadResult();

                if (!is_null($result))
                    continue;

                // rimuovo user da online
                $query_del = "DELETE
                                FROM #__user_usergroup_map
                                WHERE user_id = " . $this->_db->quote($user_id) . "
                                AND group_id = " . $this->_db->quote($del_ug);

                $this->_db->setQuery($query_del);
                if (!$this->_db->execute())
                    throw new Exception("delete query ko -> " . $query_del, E_USER_ERROR);


                // aggiungo user in moroso
                $query_ins = "INSERT INTO #__user_usergroup_map (user_id, group_id)
                                VALUES (" . $this->_db->quote($user_id) . ", " . $this->_db->quote($new_ug) . ")";

                $this->_db->setQuery($query_ins);
                if (!$this->_db->execute())
                    throw new Exception("insert query ko -> " . $query_ins, E_USER_ERROR);


                $completed++;

            }

            $this->_db->transactionCommit();

            echo "TOTALI: " . count($arr_ids) . " | ELABORATI: " . $completed;

        }
        catch(Exception $e) {
            $this->_db->transactionRollback();
            echo "ERRORE: " . $e->getMessage();
        }

        $this->_japp->close();
    }

    public function sinpe_get_morosi()
    {

        try {

            if (!isset($this->_filterparam->anno_ref))
                throw new Exception("Anno di riferimento non indicato", E_USER_ERROR);


            $query = "SELECT user_id
                        FROM #__comprofiler
                        WHERE cb_ultimoannoinregola = " . $this->_db->quote($this->_filterparam->anno_ref);

            $this->_db->setQuery($query);
            $rows = $this->_db->loadAssocList();

            if (!count($rows))
                throw new Exception("Nessun risultato per " . $this->_filterparam->anno_ref, 1);

            $check_ug = [23];
            $extra_ug = [25];

            foreach ($rows as $key => $user) {

                $query_ug = "SELECT user_id
                                FROM #__user_usergroup_map
                                WHERE user_id = " . $this->_db->quote($user['user_id']) . "
                                AND group_id IN (" . implode(',', $check_ug) . ")
                                AND group_id NOT IN (" . implode(',', $extra_ug) . ")"
                                ;

                $this->_db->setQuery($query_ug);
                $result = $this->_db->loadResult();

                if (is_null($result)
                    || !$result)
                    continue;

                $extra_arr[] = $user['user_id'];

            }

            echo implode(",", $extra_arr);

        }
        catch(Exception $e) {
            echo "ERRORE: " . $e->getMessage();
        }

        $this->_japp->close();

    }

    public function sinpe_new_extra() {

        try {

            $cf_s = array(
                        23,
                        44,
                        118,
                        206,
                        211,
                        227,
                        236,
                        334,
                        361,
                        436,
                        499,
                        529,
                        587,
                        668,
                        706,
                        733,
                        760,
                        767,
                        769,
                        850,
                        860,
                        879,
                        911,
                        915,
                        927,
                        951,
                        1041,
                        1069,
                        1071,
                        1110,
                        1135,
                        1143,
                        1230,
                        1246,
                        1289,
                        1341,
                        1462,
                        1464,
                        1629,
                        1647,
                        1719,
                        1724,
                        1812,
                        1852,
                        1902,
                        1921,
                        1928,
                        2007,
                        2019,
                        3051,
                        3060,
                        3171,
                        3237,
                        3415,
                        3509,
                        3524,
                        3656,
                        3671,
                        3779,
                        3928,
                        3950,
                        3977,
                        4045,
                        4222,
                        4327,
                        4360,
                        4376,
                        4391,
                        4392,
                        4406,
                        4427,
                        4470,
                        4471,
                        4490,
                        4496,
                        4498,
                        4499,
                        4500,
                        4504,
                        4505,
                        4507,
                        4509,
                        4512,
                        4513,
                        4516,
                        4574,
                        4576,
                        4577,
                        4579,
                        4584,
                        4586,
                        4590,
                        4593,
                        4594,
                        4595,
                        4596,
                        4597,
                        4598,
                        4599,
                        4600,
                        4601,
                        4602,
                        4603,
                        4604,
                        4605,
                        4606,
                        4607,
                        4608,
                        4609,
                        4610,
                        4611,
                        4612,
                        4613,
                        4614,
                        4615,
                        4616,
                        4617,
                        4618,
                        4619,
                        4621,
                        4622,
                        4623,
                        4624,
                        4625,
                        4626,
                        4629,
                        4630,
                        4631,
                        4633,
                        4634,
                        4635,
                        4636,
                        4637,
                        4638,
                        4639,
                        4640,
                        4641,
                        4642,
                        4643,
                        4644,
                        4645,
                        4646,
                        4647,
                        4648,
                        4649,
                        4650,
                        4651,
                        4652,
                        4653,
                        4654,
                        4655,
                        4656,
                        4657,
                        4658,
                        4659,
                        4660,
                        4661,
                        4663,
                        4665,
                        4667,
                        4670,
                        4671,
                        4672,
                        4673,
                        4676,
                        4680,
                        4681,
                        4683,
                        4684,
                        4685,
                        4686,
                        4687,
                        4688,
                        4689,
                        4690,
                        4691,
                        4692,
                        4693,
                        4694,
                        4695,
                        4697,
                        4698,
                        4699,
                        4700,
                        4701,
                        4702,
                        4703,
                        4704,
                        4705,
                        4707,
                        4708,
                        4709,
                        4710,
                        4711,
                        4712,
                        4713,
                        4714,
                        4715,
                        4717,
                        4718);
            $extra_arr = [];

            //$imp = "'" . implode( "','", $cf_s) . "'";
            $imp = implode(",", $cf_s);
            $check_ug = [20,21,23];

            /*WHERE comp.cb_codicefiscale IN (" . $imp . ")*/
            $query = "SELECT usr.id AS user_id, usr.username, comp.cb_codicefiscale
                        FROM #__comprofiler comp
                        JOIN #__users usr ON comp.user_id = usr.id
                        WHERE usr.id IN (" . $imp . ")";

            $this->_db->setQuery($query);
            $rows = $this->_db->loadAssocList();

            foreach ($rows as $key_row => $user) {


                $query_ug = "SELECT user_id
                                FROM #__user_usergroup_map
                                WHERE user_id = " . $this->_db->quote($user['user_id']) . "
                                AND group_id IN (" . implode(',', $check_ug) . ")"
                                ;
                $this->_db->setQuery($query_ug);
                $result = $this->_db->loadResult();

                if (is_null($result)
                    || !$result)
                    $extra_arr[] = $user['user_id'];

                /*
                if (($key = array_search($user['cb_codicefiscale'], $cf_s)) !== false) {
                    unset($cf_s[$key]);
                }
                */

            }

            echo implode(",", $extra_arr);

        }
        catch (Exception $e) {
            echo "ERRORE: " . $e->getMessage();
        }

        $this->_japp->close();

    }

    public function sinpe_insert_quote()
    {

        try {

            $arr_ids = [
                        755,
                        848,
                        1144,
                        493,
                        508,
                        4988,
                        435,
                        467,
                        136,
                        702,
                        1109,
                        358,
                        357,
                        1250
                    ];
            $completed = 0;

            $this->_db->transactionStart();
            foreach ($arr_ids as $key_user => $user_id) {

                // cb_ultimoannoinregola
                $query_update = "UPDATE #__comprofiler
                                SET cb_ultimoannoinregola = 2022
                                WHERE user_id = " . $this->_db->quote($user_id);

                $this->_db->setQuery($query_update);
                if (!$this->_db->execute())
                    throw new Exception("update query ko -> " . $query_update, E_USER_ERROR);

                $now = date('Y-m-d H:i:s');
                $query_quote = "INSERT INTO #__gg_quote_iscrizioni (
                                                user_id,
                                                anno,
                                                tipo_quota,
                                                tipo_pagamento,
                                                data_pagamento,
                                                dettagli_transazione
                                                )
                                            VALUES (
                                                " . $this->_db->quote($user_id) . ",
                                                2022,
                                                'quota',
                                                'bonifico',
                                                " . $this->_db->quote($now) . ",
                                                'Socio onorario 2022'
                                            )";
                $this->_db->setQuery($query_quote);
                if (!$this->_db->execute())
                    throw new Exception("insert quote query ko -> " . $query_quote, E_USER_ERROR);

                $completed++;

            }

            $this->_db->transactionCommit();

            echo "TOTALI: " . count($arr_ids) . " | ELABORATI: " . $completed;

        }
        catch(Exception $e) {
            $this->_db->transactionRollback();
            echo "ERRORE: " . $e->getMessage();
        }

        $this->_japp->close();

    }

    public function sinpe_ug_move() {

        try {

            $arr_ids = [
                4490,
                4496,
                4498,
                4499,
                4500,
                4505,
                4509,
                4512,
                4513,
                4516,
                4574,
                4576,
                4577,
                4579,
                4586,
                4590,
                4593,
                4594,
                4595,
                4596,
                4597,
                4598,
                4600,
                4601,
                4602,
                4603,
                4604,
                4606,
                4607,
                4608,
                4609,
                4610,
                4611,
                4612,
                4613,
                4615,
                4616,
                4617,
                4618,
                4619,
                4621,
                4622,
                4623,
                4624,
                4625,
                4626,
                4631,
                4633,
                4635,
                4636,
                4637,
                4638,
                4639,
                4640,
                4641,
                4642,
                4643,
                4644,
                4645,
                4647,
                4649,
                4651,
                4652,
                4653,
                4655,
                4656,
                4657,
                4658,
                4659,
                4661,
                4663,
                4667,
                4672,
                4676,
                4683,
                4684,
                4685,
                4686,
                4687,
                4688,
                4690,
                4691,
                4692,
                4694,
                4695,
                4697,
                4698,
                4699,
                4700,
                4701,
                4702,
                4703,
                4704,
                4705,
                4707,
                4708,
                4709,
                4710,
                4711,
                4712,
                4713,
                4714,
                4715,
                4717,
                4718];

            $del_ug = [28,30];
            $new_ug = [23,25];
            $completed = 0;

            $this->_db->transactionStart();
            foreach ($arr_ids as $key_user => $user_id) {

                // rimuovo utente da gruppi evento
                $query_del = "DELETE
                                FROM #__user_usergroup_map
                                WHERE user_id = " . $this->_db->quote($user_id) . "
                                AND group_id IN (" . implode(',', $del_ug) . ")";

                $this->_db->setQuery($query_del);
                if (!$this->_db->execute())
                    throw new Exception("delete query ko -> " . $query_del, E_USER_ERROR);

                // aggiungo utente a gruppi istituzionali
                $query_ins = "INSERT INTO #__user_usergroup_map
                                (user_id, group_id) VALUES ";
                foreach ($new_ug as $key_ug => $ug) {
                    $query_ins .= "(" . $this->_db->quote($user_id) . ", " . $this->_db->quote($ug) . "),";
                }

                $query_ins = rtrim(trim($query_ins), ",") . ";";
                $this->_db->setQuery($query_ins);
                if (!$this->_db->execute())
                    throw new Exception("insert query ko -> " . $query_ins, E_USER_ERROR);

                // cb_ultimoannoinregola
                $query_update = "UPDATE #__comprofiler
                                SET cb_ultimoannoinregola = 2022
                                WHERE user_id = " . $this->_db->quote($user_id);
                $this->_db->setQuery($query_update);
                if (!$this->_db->execute())
                    throw new Exception("update query ko -> " . $query_update, E_USER_ERROR);

                $now = date('Y-m-d H:i:s');
                $query_quote = "INSERT INTO #__gg_quote_iscrizioni (
                                                user_id,
                                                anno,
                                                tipo_quota,
                                                tipo_pagamento,
                                                data_pagamento,
                                                dettagli_transazione
                                                )
                                            VALUES (
                                                " . $this->_db->quote($user_id) . ",
                                                2022,
                                                'quota',
                                                'bonifico',
                                                " . $this->_db->quote($now) . ",
                                                'Socio straordinario da Congresso 2021'
                                            )";
                $this->_db->setQuery($query_quote);
                if (!$this->_db->execute())
                    throw new Exception("insert quote query ko -> " . $query_quote, E_USER_ERROR);

                $completed++;

            }

            $this->_db->transactionCommit();

            echo "TOTALI: " . count($arr_ids) . " | ELABORATI: " . $completed;

        }
        catch(Exception $e) {
            $this->_db->transactionRollback();
            echo "ERRORE: " . $e->getMessage();
        }

        $this->_japp->close();

    }



    public function get_tz() {

        $oggi = '2020-11-16T07:21:36Z';
        $dt = new DateTime($oggi, new DateTimeZone('UTC'));
        $dt->setTimezone(new DateTimeZone('Europe/Rome'));
        echo $dt->format('Y-m-d H:i:s');

        $this->_japp->close();

    }

    public function get_user_dt() {

        try {

            $modelUser = new gglmsModelUsers();
            $tmpuser = $modelUser->get_user($this->_filterparam->id_utente, 0, "cp");

            echo json_encode($tmpuser);

        }
        catch (Exception $e) {
            echo $e->getMessage();
        }

        $this->_japp->close();
    }

    public function get_last_insert_coupon() {

        try {

            $_ret = array();

            $query = $this->_db->getQuery(true)
                    ->select('messaggio')
                    ->from('#__gg_error_log')
                    ->where('messaggio LIKE ' . $this->_db->quote('%api_genera_coupon_response%'))
                    ->order('id DESC');

            $this->_db->setQuery($query);
            $result = $this->_db->loadAssoc();

            if (is_null($result)
                || !isset($result['messaggio'])
                || $result['messaggio'] == "")
                throw new Exception("Nessun riferimento trovato", 1);

            $_response = preg_replace('/\s/', '', $result['messaggio']);
            $_response = str_replace("api_genera_coupon_response:", "", $_response);

            $_decode = json_decode($_response);

            if (
                (is_object($_decode) && !isset($_decode->id_iscrizione))
                    || (is_array($_decode) && !isset($_decode['id_iscrizione']))
                )
                throw new Exception("Il riferimento ha un valore non valido", 1);


            $_ret['success'] = (is_object($_decode)) ? $_decode->id_iscrizione : $_decode['id_iscrizione'];

        }
        catch (Exception $e) {
            $_ret['error'] = $e->getMessage();
        }

        echo json_encode($_ret);
        $this->_japp->close();

    }

}
