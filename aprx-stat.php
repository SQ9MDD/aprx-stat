<?php
/*
 Simple web stat for APRX software.
 Released under GPL.v.2
 
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
function interface_traffic(array $wszystkie){
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

$ilosc_ramek_aprsis = count($ramki_aprsis);
$ilosc_ramek_iface_one = count($ramki_iface_one_arr);
$ilosc_ramek_iface_two = count($ramki_iface_two_arr);
$ilosc_ramek_radiowych = $ilosc_ramek_iface_one + $ilosc_ramek_iface_two;
$ilosc_ramek_iface_one_tx = count($ramki_iface_one_tx_arr);
$ilosc_ramek_iface_two_tx = count($ramki_iface_two_tx_arr);
$ilosc_ramek_radiowych_tx = $ilosc_ramek_iface_one_tx + $ilosc_ramek_iface_two_tx;

echo "<html><body fontfamily=tahoma><center><table border=0 width=90%><tr><td colspan=4><hr noshade size=1 width=100%></td></tr><tr><td colspan=4>";
echo "Ilosc wszystkich odebranych ramek w logu: <b>$ilosc_wszystkich_ramek</b>";
echo "<br>";
echo "Ilosc ramek odebranych z APRSIS <b>$ilosc_ramek_aprsis</b>";
echo "<br>";
echo "</td></tr><tr><td colspan=4><hr noshade size=1 width=100%></td></tr><tr><td colspan=4><font color=green>";
echo "Ilosc wszystkich odebranych ramek odebranych radiowo: <b>$ilosc_ramek_radiowych</b>";
echo "<br>";
echo "$primary_interface ($primary_interface_frequency) - <b>$ilosc_ramek_iface_one</b>";
echo "<br>";
echo "$secondary_interface ($secondary_interface_frequency) - <b>$ilosc_ramek_iface_two</b>";
echo "<br>";
echo "</td></tr><tr><td colspan=4><hr noshade size=1 width=100%></td></tr><tr><td colspan=4><font color=red>";
echo "Ilosc wszystkich wyslanych ramek: <b>$ilosc_ramek_radiowych_tx</b>";
echo "<br>";
echo "$primary_interface ($primary_interface_frequency) - <b>$ilosc_ramek_iface_one_tx</b>";
echo "<br>";
echo "$secondary_interface ($secondary_interface_frequency) - <b>$ilosc_ramek_iface_two_tx</b>";
echo "</td></tr><tr><td colspan=4><hr noshade size=1 width=100%>Statystyki interfejsow radiowych:<br></td></tr>";
echo "<tr>";
echo "<td valign=top><font color=blue> RX Interfejs $primary_interface ($primary_interface_frequency)<br>";	
	$income = interface_traffic($linia_znak_2m);
	echo"<pre>";
	echo"<b>CALLSIGN \t pkt.</b>\n";
	while (list($key, $value) = each($income)) {
		$len = strlen($key);
		if($len <= 6){
			$tabs = "\t\t";
		}else{
			$tabs = "\t";
		}
		echo "$key $tabs ($value)\n";
	}
	echo "<br>";
echo "<td valign=top><font color=blue> TX Interfejs $primary_interface ($primary_interface_frequency)<br>";
	$income = interface_traffic($linia_znak_2m_tx);
	echo"<pre>";
	echo"<b>CALLSIGN \t pkt.</b>\n";	
	while (list($key, $value) = each($income)) {
		$len = strlen($key);
		if($len <= 6){
			$tabs = "\t\t";
		}else{
			$tabs = "\t";
		}
		echo "$key $tabs ($value)\n";
	}
	echo "<br>";
echo "</td><td valign=top><font color=blue> RX Interfejs $secondary_interface ($secondary_interface_frequency)<br>";
	$income = interface_traffic($linia_znak_70cm);
	echo"<pre>";
	echo"<b>CALLSIGN \t pkt.</b>\n";	
	while (list($key, $value) = each($income)) {
		$len = strlen($key);
		if($len <= 6){
			$tabs = "\t\t";
		}else{
			$tabs = "\t";
		}
		echo "$key $tabs ($value)\n";
	}
	echo "<br>";	
echo "<td valign=top><font color=blue> TX Interfejs $secondary_interface ($secondary_interface_frequency)<br>";	
	$income = interface_traffic($linia_znak_70cm_tx);
	echo"<pre>";
	echo"<b>CALLSIGN \t pkt.</b>\n";	
	while (list($key, $value) = each($income)) {
		$len = strlen($key);
		if($len <= 6){
			$tabs = "\t\t";
		}else{
			$tabs = "\t";
		}
		echo "$key $tabs ($value)\n";
	}
	echo "<br>";	
echo "</td></tr></table>";
?>