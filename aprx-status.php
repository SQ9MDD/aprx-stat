<?php
error_reporting(0);
/*
 Simple radio beacon status for APRX software.
 Released under GPL.v.2
 
 Remember use logrotate for clean up the log we need daily statistics.
 SQ9MDD rysieklabus (at) gmail (dot) com
 CHANGELOG 2017.01.31 some small fixes
*/

//*****CONFIG******
$callsign = "SQ9MDD";								//your callsign

$primary_interface = "SQ9MDD-1";					//your callsign and ssid for primary interface (see to logfile)
$primary_interface_frequency = "144.800MHz";		//frequency for primary interface

$secondary_interface = "SQ9MDD-2";					//your callsign and ssid for secondary interface (see to logfile)
$secondary_interface_frequency = "432.500MHz";		//frequency for secondary interface

$aprx_log_db = file('/var/log/aprx/aprx-rf.log');	//aprx log-file full path 
//$aprx_log_db = file('/tmp/aprx1-rf.log');

//***END CONFIG****

switch($language){
	case('PL'):
		$label_log_sumary = "Ilosc wszystkich odebranych ramek w logu";
		$label_log_rx = "Ilosc ramek odebranych radiowo";
		$label_log_tx = "Ilosc ramek wyslanych radiowo";
		$label_log_radio_stat = "Statystyki interfejsow radiowych";
		$label_interface = "Interfejs";
		$label_from = "od";
		$label_to = "do";
	break;
	case('EN'):
		$label_log_sumary = "Logged frames";
		$label_log_rx = "Received from radio interfaces";
		$label_log_tx = "Transmitted over radio interfaces";
		$label_log_radio_stat = "Radio interfaces counters";
		$label_interface = "interface";
		$label_from = "from";
		$label_to = "to";		
	break;
}

function interface_traffic(array $wszystkie){
	$licznik = count($wszystkie);
	if($licznik <= 0){
		return(0);
	}
	$unikalne = array_unique($wszystkie);
	$unikalne = array_values(array_unique($unikalne));
	for($a=0;$a<=(count($unikalne)-1);$a++){
		$count = 0;
		foreach ($wszystkie as $item) {
			if ($item == $unikalne[$a]) {
				$count++;
			}
		}
		$count_arr[] = $count;
		$new_table[$unikalne[$a]] = $count;
	}
	array_multisort($new_table,SORT_DESC);	
	return($new_table);
}

echo "<pre>";
$ilosc_wszystkich_ramek = count($aprx_log_db);

//KG4PID-14	<- longest 9
//SQ9M		<-shortest 4
$pri_call_len = strlen($primary_interface);
$sec_call_len = strlen($secondary_interface);

switch($pri_call_len){
	case(9):
		$primary_interface = $primary_interface;
	break;
	case(8):
		$primary_interface = $primary_interface." ";
	break;
	case(7):
		$primary_interface = $primary_interface."  ";
	break;	
	case(6):
		$primary_interface = $primary_interface."   ";
	break;	
	case(5):
		$primary_interface = $primary_interface."    ";
	break;	
	case(4):
		$primary_interface = $primary_interface."     ";
	break;	
}

switch($sec_call_len){
	case(9):
		$secondary_interface = $secondary_interface;
	break;
	case(8):
		$secondary_interface = $secondary_interface." ";
	break;
	case(7):
		$secondary_interface = $secondary_interface."  ";
	break;	
	case(6):
		$secondary_interface = $secondary_interface."   ";
	break;	
	case(5):
		$secondary_interface = $secondary_interface."    ";
	break;	
	case(4):
		$secondary_interface = $secondary_interface."     ";
	break;	
}

for($a=0;$a<=(count($aprx_log_db)-1);$a++){
	$linia = $aprx_log_db[$a];
	if(strpos($linia,"$callsign-") == true){
		$ramki_radiowe[] = $linia;
		if((strpos($linia,"$primary_interface R") == true) OR (strpos($linia,"$primary_interface d") == true)){				//2m odebrane			
			$linia_arr_2m = explode('>',$linia);	
			$tmp_to_clenup_2m = substr($linia_arr_2m[0],36);
			$linia_znak_2m[] = str_replace("*","",$tmp_to_clenup_2m);				//znaki na tym interface
			$ramki_iface_one_arr[] = $linia;							//ramki na tym interface
		}else if((strpos($linia,"$secondary_interface R") == true) OR (strpos($linia,"$secondary_interface d") == true)){ 	//70cm odebrane			
			$linia_arr_70cm = explode('>',$linia);
			$tmp_to_clenup_70cm = substr($linia_arr_70cm[0],36);
			$linia_znak_70cm[] = str_replace("*","",$tmp_to_clenup_70cm);						
			$ramki_iface_two_arr[] = $linia;							
		}else if(strpos($linia,"$primary_interface T") == true){ 		//2m powtorzone
			$linia_arr_2m_tx = explode('>',$linia);
			$linia_znak_2m_tx[] = substr($linia_arr_2m_tx[0],36);	
			$ramki_iface_one_tx_arr[] = $linia;
		}else if(strpos($linia,"$secondary_interface T") == true){		//70cm powtórzone
			$linia_arr_70cm_tx = explode('>',$linia);
			$linia_znak_70cm_tx[] = substr($linia_arr_70cm_tx[0],36);			
			$ramki_iface_two_tx_arr[] = $linia;
		}
	}else{
		$ramki_aprsis[] = $linia;
	}
}

$ilosc_ramek_iface_one = count($ramki_iface_one_arr);
$ilosc_ramek_iface_two = count($ramki_iface_two_arr);
$ilosc_ramek_iface_one_tx = count($ramki_iface_one_tx_arr);
$ilosc_ramek_iface_two_tx = count($ramki_iface_two_tx_arr);

echo ">2M RX_CNT:$ilosc_ramek_iface_one TX_CNT:$ilosc_ramek_iface_one_tx 70CM RX_CNT:$ilosc_ramek_iface_two TX_CNT:$ilosc_ramek_iface_two_tx\n";

?>
