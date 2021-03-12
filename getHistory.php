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

$sql_conn = mysqli_connect($MYSQLHOST, $MYSQLUSER, $MYSQLPASS);
mysqli_select_db($sql_conn, $MYSQLDBNAME);

function ResultsOutput($results,$data=null){
    $return = [];

    if($data!=null){
        $data = explode(",",$data);
    }

    while($row = mysqli_fetch_assoc($results)){
        if(!isset($return[$row['pair']])){
            $return[$row['pair']] = [];
        }

        if($data!=null){
            $return[$row['pair']][$row['date']] = [];

            if(in_array("price",$data)){
                $return[$row['pair']][$row['date']]['price'] = (float) $row['price'];
            }
            if(in_array("open",$data)){
                $return[$row['pair']][$row['date']]['open'] = (float) $row['open'];
            }
            if(in_array("high",$data)){
                $return[$row['pair']][$row['date']]['high'] = (float) $row['high'];
            }
            if(in_array("low",$data)){
                $return[$row['pair']][$row['date']]['low'] = (float) $row['low'];
            }
            if(in_array("volume",$data)){
                $return[$row['pair']][$row['date']]['volume'] = (float) $row['volume'];
            }
        }else{
            $return[$row['pair']][$row['date']] = [
                "price" => (float) $row['price'],
                "open" => (float) $row['open'],
                "high" => (float) $row['high'],
                "low" => (float) $row['low'],
                "volume" => (float) $row['volume']
            ];
        }
        
    }
    echo json_encode($return);
    exit();
}

$pair = null;
if(isset($_GET['pair']) && $_GET['pair']!=""){
    $pair = $_GET['pair'];
}

$date = null;
if(isset($_GET['date']) && $_GET['date']!=""){
    $date = $_GET['date'];
}

$BIGQUERY = false;
if(isset($_GET['bigquery']) && $_GET['bigquery']=="true"){
    $BIGQUERY = true;
}

$data = null;
if(isset($_GET['data']) && $_GET['data']!=""){
    $data = $_GET['data'];
}

if($pair == null && $date == null && $data == null){
    //Return the most recent data, for all pairs, and all columns
    $SQL = "SELECT * FROM crypto.stat_history WHERE date in (SELECT MAX(date) FROM crypto.stat_history)";
    ResultsOutput(mysqli_query($sql_conn, $SQL));
}else{
    $SQL = "SELECT * FROM crypto.stat_history WHERE ";

    $AND = false;
    if($pair!=null){
        $pair = explode(",",$pair);
        $SQL .= "pair in ('".implode("','",$pair)."') AND ";
    }
    if($date!=null){
        $date = date("Y-m-d H:00:00",strtotime($date));
    }else{
        $date = date("Y-m-d H:00:00",strtotime("-5 HOUR"));
    }
    if($BIGQUERY){
        $SQL .= "date >= '".$date."'";
    }else{
        $SQL .= "date = '".$date."'";
    }

    $SQL .= " ORDER BY date DESC";

    // echo $SQL;
    // exit();

    ResultsOutput(mysqli_query($sql_conn, $SQL),$data);
}
