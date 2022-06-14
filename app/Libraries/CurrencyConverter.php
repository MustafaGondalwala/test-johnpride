<?php
namespace App\Libraries;

/*
* File: CurrencyConverter.php
* Author: Simon Jarvis
* Copyright: 2005 Simon Jarvis
* Date: 10/12/05
* Link: http://www.white-hat-web-design.co.uk/articles/php-currency-conversion.php
* 
* This program is free software; you can redistribute it and/or 
* modify it under the terms of the GNU General Public License 
* as published by the Free Software Foundation; either version 2 
* of the License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful, 
* but WITHOUT ANY WARRANTY; without even the implied warranty of 
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
* GNU General Public License for more details: 
* http://www.gnu.org/licenses/gpl.html
*
*/
 
class CurrencyConverter {
   
   //var $xml_file = "www.ecb.int/stats/eurofxref/eurofxref-daily.xml";
   var $xml_file = "www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml";

   var $mysql_host, $mysql_user, $mysql_pass, $mysql_db, $mysql_table;
   var $exchange_rates = array();
 
   //Load Currency Rates
   function __construct() {

      $database_config = config('database');

      $default_database = $database_config['default'];
      $database = $database_config['connections'][$default_database];

      $this->mysql_user = $database['username'];
      $this->mysql_pass = $database['password'];
      $this->mysql_db = $database['database'];
      $this->mysql_host = $database['host'];

      $this->mysql_table = 'currencies';

      $this->checkLastUpdated();

      $conn = mysqli_connect($this->mysql_host,$this->mysql_user,$this->mysql_pass);

      $rs = mysqli_select_db($conn, $this->mysql_db);
      $sql = "SELECT * FROM ".$this->mysql_table;

      $rs =  mysqli_query($conn, $sql);
      
      while($row = mysqli_fetch_array($rs)) {
         $this->exchange_rates[$row['currency']] = $row['rate'];         
      }
   }
 
   /* Perform the actual conversion, defaults to £1.00 GBP to USD */
   function convert($amount=1,$from="GBP",$to="USD",$decimals=0) {
	   if($to != "INR")
	  {
      $decimals=2;
      $currency_price = ($amount/$this->exchange_rates[$from])*$this->exchange_rates[$to];
     //$currency_price = ceil($currency_price + ($currency_price * 10)/100);
	  //$currency_price = ($currency_price + ($currency_price * 10)/100);

     //pr($currency_price);
	  }
	  else
	  {
		  $currency_price = ceil($amount);	  
	  }
	  $currency_price = number_format($currency_price,$decimals);
	  return $currency_price;
   }
 
   /* Check to see how long since the data was last updated */
   function checkLastUpdated() {
   //echo $this->mysql_host. "  ".$this->mysql_user."    ".$this->mysql_pass;
   //exit;
      $conn = mysqli_connect($this->mysql_host,$this->mysql_user,$this->mysql_pass);
 
      $rs = mysqli_select_db($conn, $this->mysql_db);
 
      $sql = "SHOW TABLE STATUS FROM ".$this->mysql_db." LIKE '".$this->mysql_table."'";
 
      $rs =  mysqli_query($conn, $sql);

      //prd($rs);
 
      if(mysqli_num_rows($rs) == 0 ) {
         $this->createTable();
      } else {
         $row = mysqli_fetch_array($rs);
         if(time() > (strtotime($row["Update_time"])+(12*60*60)) ) {
            $this->downloadExchangeRates();         
         }
      }
   }
 
   /* Download xml file, extract exchange rates and store values in database */
   function downloadExchangeRates() {

      //prd('downloadExchangeRates');
	   
      $currency_domain = substr($this->xml_file,0,strpos($this->xml_file,"/"));
      $currency_file = substr($this->xml_file,strpos($this->xml_file,"/"));
      $fp = @fsockopen($currency_domain, 443, $errno, $errstr, 10);
      if($fp) {

         $arrContextOptions=array(
            "ssl"=>array(
               "verify_peer"=>false,
               "verify_peer_name"=>false,
            ),
         ); 


         $xml = file_get_contents("https://".$this->xml_file, false, stream_context_create($arrContextOptions));

         $pattern = "{<Cube\s*currency='(\w*)'\s*rate='([\d\.]*)'/>}is";
         preg_match_all($pattern,$xml,$xml_rates);
         array_shift($xml_rates);

         /*echo 'xml_rates';
         prd($xml_rates);*/


         /*$out = "GET ".$currency_file." HTTP/1.1\r\n";
         $out .= "Host: ".$currency_domain."\r\n";
         $out .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8) Gecko/20051111 Firefox/1.5\r\n";
         $out .= "Connection: Close\r\n\r\n";
         fwrite($fp, $out);

         $buffer = '';
         while (!feof($fp)) {
            $buffer .= fgets($fp, 128);
         }
         fclose($fp);
 
         $pattern = "{<Cube\s*currency='(\w*)'\s*rate='([\d\.]*)'/>}is";
         preg_match_all($pattern,$buffer,$xml_rates);
         array_shift($xml_rates);*/

         $exchange_rate = array();
 
         for($i=0;$i<count($xml_rates[0]);$i++) {
            $exchange_rate[$xml_rates[0][$i]] = $xml_rates[1][$i];
         }

         //prd($exchange_rate);
 
         $conn = mysqli_connect($this->mysql_host,$this->mysql_user,$this->mysql_pass);
 
         $rs = mysqli_select_db($conn, $this->mysql_db);
            
			if($exchange_rate!=''){ 
				 foreach($exchange_rate as $currency=>$rate) {
					if((is_numeric($rate)) && ($rate != 0)) {
					   $sql = "SELECT * FROM ".$this->mysql_table." WHERE currency='".$currency."'";
					   $rs =  mysqli_query($conn, $sql) or die(mysqli_error());
					   if(mysqli_num_rows($rs) > 0) {
						  $sql = "UPDATE ".$this->mysql_table." SET rate=".$rate." WHERE currency='".$currency."'";
					   } else {
                     $updated_at = date('Y-m-d H:i:s');
                    //$sql = "INSERT INTO ".$this->mysql_table." VALUES('".$currency."',".$rate.")";
						  $sql = "INSERT INTO ".$this->mysql_table." SET rate= '".$rate."', currency='".$currency."', updated_at='".$updated_at."' ";

                    //echo $sql; die;
					   }
					   $rs =  mysqli_query($conn, $sql) or die(mysqli_error());
					}
				 }  
			}  
      }
   }
 
   /* Create the currency exchange table */
   function createTable() {
      $conn = mysqli_connect($this->mysql_host,$this->mysql_user,$this->mysql_pass);
 
      $rs = mysqli_select_db($conn, $this->mysql_db);
 
      $sql = "CREATE TABLE ".$this->mysql_table." ( currency char(3) NOT NULL default '', rate float NOT NULL default '0', updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, PRIMARY KEY(currency) ) ENGINE=MyISAM";
      
      $rs =  mysqli_query($conn, $sql) or die(mysqli_error());
 
      $sql = "INSERT INTO ".$this->mysql_table." SET currency='EUR', rate=1";
 
      $rs =  mysqli_query($conn, $sql) or die(mysqli_error());

      //prd($rs);
      
      $this->downloadExchangeRates();   
   }
 
}
 
?>