<?php
error_reporting(0);
/*
 Simple radio beacon status for APRX software.
 Released under GPL.v.2
 
 Remember use logrotate for clean up the log we need daily statistics.
 SQ9MDD rysieklabus (at) gmail (dot) com
*/

//*****CONFIG******
$callsign = "SQ9MDD";

$primary_interface = "SQ9MDD-1";
$primary_interface_frequency = "144.800MHz";

$secondary_interface = "SQ9MDD-2";
$secondary_interface_frequency = "432.500MHz";

$aprx_log_db = file('/var/log/aprx/aprx-rf.log');
 
//***END CONFIG****

$ilosc_wszystkich_ramek = count($aprx_log_db);

for($a=0;$a<=(count($aprx_log_db)-1);$a++){
	$linia = $aprx_log_db[$a];
	if(strpos($linia,"$callsign-") == true){
		$ramki_radiowe[] = $linia;
		if(strpos($linia,"$primary_interface  R") == true){				//2m odebrane			
			$linia_arr_2m = explode('>',$linia);						
			$linia_znak_2m[] = substr($linia_arr_2m[0],36);				//znaki na tym interface
			$ramki_iface_one_arr[] = $linia;							//ramki na tym interface
		}else if(strpos($linia,"$secondary_interface  R") == true){ 	//70cm odebrane			
			$linia_arr_70cm = explode('>',$linia);
			$linia_znak_70cm[] = substr($linia_arr_70cm[0],36);						
			$ramki_iface_two_arr[] = $linia;							
		}else if(strpos($linia,"$primary_interface  T") == true){ 		//2m powtorzone
			$linia_arr_2m_tx = explode('>',$linia);
			$linia_znak_2m_tx[] = substr($linia_arr_2m_tx[0],36);	
			$ramki_iface_one_tx_arr[] = $linia;
		}else if(strpos($linia,"$secondary_interface  T") == true){		//70cm powtórzone
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
