<?php
/**
 * Copyright (C) 2021 Mikedmor
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

include 'config.php';

$service_url = 'https://min-api.cryptocompare.com/data/pricemultifull?fsyms='.$CRYPTOCOMPARECOINS.'&tsyms='.$CRYPTOCOMPARECURRENCY.'&e='.$CRYPTOCOMPAREEXCHANGE;

$sql_conn = mysqli_connect($MYSQLHOST, $MYSQLUSER, $MYSQLPASS);
mysqli_select_db($sql_conn, $MYSQLDBNAME);


$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'authorization: Apikey ' . $CRYPTOCOMPAREKEY
    ));

$curl_response = curl_exec($curl);
curl_close($curl);
$json=json_decode($curl_response);
//echo "<pre>".print_r($json,true)."</pre><br>"; 
$sql = "INSERT INTO `crypto`.`stat_history`(`date`,`pair`,`price`,`open`,`high`,`low`,`volume`) VALUES ";
$comma = false;
foreach($json->RAW as $COIN){
    if($comma){
        $sql .= ",";
    }

    $sql .= "(DATE_ADD( DATE_FORMAT( DATE_ADD( NOW(), INTERVAL 2 HOUR ), '%Y-%m-%d %H:00:00' ), INTERVAL IF( MINUTE( DATE_ADD( NOW(), INTERVAL 2 HOUR ) ) < 59, 0, 1 ) HOUR  ),
              '".$COIN->USD->FROMSYMBOL."/".$COIN->USD->TOSYMBOL."',
              ".$COIN->USD->PRICE.",
              ".$COIN->USD->OPENHOUR.",
              ".$COIN->USD->HIGHHOUR.",
              ".$COIN->USD->LOWHOUR.",
              ".$COIN->USD->VOLUMEHOUR."
            )";

    $comma = true;

}

$sql .= " ON DUPLICATE KEY UPDATE `price`=VALUES(`price`),`open`=VALUES(`open`),`high`=VALUES(`high`),`low`=VALUES(`low`),`volume`=VALUES(`volume`)";

// echo $sql;
// exit;

if(mysqli_query($sql_conn, $sql)){
    echo "Successful";
}else{
    echo "Error: ".mysqli_error($sql_conn);
}