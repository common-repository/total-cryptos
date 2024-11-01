<?php
/*
Plugin Name: Total Cryptos
Plugin URI: http://totalcryptos.com
description: Display Crypto Currency Data 
Version: 1.0
Author: Bloc10
Author URI: http://bloc10.com
License: GPL
*/



define('TC_PATH','https://totalcryptos.com/');
/************************** To Include JS Files ******************************/

add_action('wp_enqueue_scripts', 'tc_for_setting_up_scripts');
function tc_for_setting_up_scripts() {
	
	//LOAD CSS
	wp_register_style( 'tc_c3min_css', plugins_url( 'css/c3.min.css', __FILE__), array(),null, false);
	wp_register_style( 'tc_style_css', plugins_url( 'css/style.css', __FILE__), array(),null, false);
	wp_register_style( 'tc_ticker_css', plugins_url( 'css/ticker.css', __FILE__), array(),null, false);	
	
	wp_enqueue_style('tc_c3min_css');
	wp_enqueue_style('tc_style_css');
	wp_enqueue_style('tc_ticker_css');	
	
	
	//LOAD JS
   wp_register_script('tc_c3min_js', plugins_url('js/c3.min.js', __FILE__), array(),null, true);
   wp_register_script('tc_d3min_js', plugins_url('js/d3.min.js', __FILE__), array(),null, true);
   wp_register_script('tc_datatable_js', plugins_url('js/datatables.min.js', __FILE__), array(),null, true);
   wp_register_script('tc_sparkline_js', plugins_url('js/jquery.sparkline.min.js', __FILE__), array(),null, true);
   wp_register_script('tc_js', plugins_url('js/totalcryptos.js', __FILE__), array(),null, true);
   wp_register_script('tc_ticker_js', plugins_url('js/ticker.js', __FILE__), array(),null, true);   
  
   wp_enqueue_script('tc_c3min_js');
   wp_enqueue_script('tc_d3min_js');
   wp_enqueue_script('tc_datatable_js');
   wp_enqueue_script('tc_sparkline_js');
   wp_enqueue_script('tc_ticker_js'); 
      
   wp_enqueue_script('tc_js');
   
}
 add_action( 'admin_enqueue_scripts', 'load_tc_admin_styles' );
function load_tc_admin_styles() {
   wp_register_style( 'tc_admin_style', plugins_url( 'css/admin-style.css', __FILE__), array(),null, false);	
   wp_enqueue_style('tc_admin_style');
  }

/************************* To Get Top Product's Prices *************************/

function tc_top_products(){
	$response=file_get_contents(TC_PATH.'api/biggestGainers');
	$response=json_decode($response);
	echo '<div class="row">';
	echo '<h2>Top Products Based On Market Capitalization</h2>';
	echo '<table class="table table-striped table-bordered table-hover">';
	echo '<thead>';
	echo '<tr>';
	echo '<th>#</th>';
	echo '<th>Name</th>';
	echo '<th>Market</th>';
	echo '<th>Price</th>';
	echo '<th>Volume</th>';
	echo '<th>High</th>';
	echo '<th>Low</th>';
	echo '<th>Chart 1d</th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody id="totalcryptos_market_cap">';
	$i=0;
	if($response->errCode==1){
		foreach($response->data->products as  $value){ 
			echo '<tr id="'.$value->product.'_totalcryptos">';	
 			echo '<td>'.($i+1).'</td>';
			echo '<td>'.strtoupper($value->base_currency).'</td>';
			echo '<td>'.strtoupper(str_replace("_","",$value->product)).'</td>';
			echo '<td>'.number_format($value->price,8).'</td>';		
			echo '<td>'.number_format($value->volume,0).'</td>';		
			echo '<td>'.number_format($value->high,8).'</td>';		
			echo '<td>'.number_format($value->low,8).'</td>';	
			echo '<td>';
			echo '<div class="row">';
			echo '<div class="col-md-12" id="chart_'.$value->product.'_totalcryptos">';
			echo '<input type="hidden" id="hidden_'.$value->product.'_totalcryptos" value="'.implode($value->chart,',').'" />';
			echo '</div>';
			echo '</div>';
			echo '</td>';	
			echo '</tr>';
			$i++;
		}
	}
	else{
		echo '<tr>';
		echo '<td>'.$response->message.'</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	echo '</div>';
	echo '<div class="row">';
	echo '<h2>Top Products Based On USD Price</h2>';
	echo '<table class="table table-striped table-bordered table-hover">';
	echo '<thead>';
	echo '<tr>';
	echo '<th>#</th>';
	echo '<th>Name</th>';
	echo '<th>Market</th>';
	echo '<th>Price</th>';
	echo '<th>Market Cap</th>';
	echo '<th>% Change (24h)</th>';
	echo '<th>Chart 1d</th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody id="totalcryptos_usd">';
	$i=0;
	if($response->errCode==1){
		foreach($response->data->gainers as  $value){ 
			echo '<tr id="'.$value->id.'_totalcryptos">';	
 			echo '<td>'.($i+1).'</td>';
			echo '<td>'.strtoupper($value->name).'</td>';
			echo '<td>'.strtoupper($value->name.'USD').'</td>';
			echo '<td>$'.number_format($value->price_usd,8).'</td>';		
			echo '<td>$'.number_format($value->market_cap_usd,0).'</td>';		
			echo '<td>'.$value->percent_change_24h.'</td>';			
			echo '<td>';
			echo '<div class="row">';
			echo '<div class="col-md-12" id="chart_'.$value->id.'_totalcryptos">';
			echo '<input type="hidden" id="hidden_'.$value->id.'_totalcryptos" value="'.implode($value->chart,',').'" />';
			echo '</div>';
			echo '</div>';
			echo '</td>';	
			echo '</tr>';
			$i++;
		}
	}
	else{
		echo '<tr>';
		echo '<td>'.$response->message.'</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	echo '</div>';
}

/************************ To Get Top Gainers/Losers ****************************/

function tc_gainers_losers(){
	$time='1h';
	$gainers_losers_data = get_option( 'tc_gainers_losers_tables' );	
	if(!empty($gainers_losers_data)){
		$time=$gainers_losers_data;
	}
	$response=file_get_contents(TC_PATH.'api/topGainersLosers/'.$time);
	$response=json_decode($response);
	echo '<div class="row">';
	echo '<div class="col-lg-12">';
	echo '<h2>Gainers</h2>';
	echo '<table class="table table-striped table-bordered table-hover">';
	echo '<thead>';
	echo '<tr>';
	echo '<th>#</th>';
	echo '<th>Name</th>';
	echo '<th>Symbol</th>';
	echo '<th>Price</th>';
	echo '<th>Change %</th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	if($response->errCode==1){
		foreach($response->data->gainers as $key => $value){ 
			echo '<tr>';
			echo '<td>'.($key+1).'</td>';
			echo '<td>'.strtoupper($value->name).'</td>';
			echo '<td>'.strtoupper($value->symbol).'</td>';
			echo '<td>$'.number_format($value->price_usd,8).'</td>';		
			echo '<td>'.number_format($value->percent_change_24h,2).'</td>';		
			echo '</tr>';
		}
	}
	else{
		echo '<tr>';
		echo '<td>'.$response->message.'</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	echo '</div>';
	echo '<div class="col-lg-12">';
	echo '<h2>Losers</h2>';
	echo '<table class="table table-striped table-bordered table-hover">';
	echo '<thead>';
	echo '<tr>';
	echo '<th>#</th>';
	echo '<th>Name</th>';
	echo '<th>Symbol</th>';
	echo '<th>Price</th>';
	echo '<th>Change %</th>';
	echo '</tr>';
	echo '</thead>';
	echo '<tbody>';
	if($response->errCode==1){
		foreach($response->data->losers as $key => $value){ 
			echo '<tr>';	
			echo '<td>'.($key+1).'</td>';
			echo '<td>'.strtoupper($value->name).'</td>';
			echo '<td>'.strtoupper($value->symbol).'</td>';
			echo '<td>$'.number_format($value->price_usd,8).'</td>';		
			echo '<td>'.number_format($value->percent_change_24h,2).'</td>';		
			echo '</tr>';
		}
	}
	else{
		echo '<tr>';
		echo '<td>'.$response->message.'</td>';
		echo '</tr>';
	}
	echo '</tbody>';
	echo '</table>';
	echo '</div>';
	echo '</div>';
}

add_shortcode('tc_top_products', 'tc_top_products');
add_shortcode('tc_gainers_losers','tc_gainers_losers');

/************************ Add plugin menu for admin section ****************************/
add_action('admin_menu', 'tc_menu_pages');
function tc_menu_pages(){
    add_menu_page('Totalcryptos', 'Totalcryptos', 'manage_options', 'totalcryptos', 'tc_settings' );
    add_submenu_page( 'totalcryptos', 'Help', 'Help', 'manage_options', 'my-admin-slug', 'tc_help' );	
  
}

function tc_settings(){
	//GET CHECKBOX DATA
	$option_name = 'tc_currency_slider';
	$currency_data = get_option($option_name);	
	if($currency_data=='1'){ 
		$checked = "checked='checked'"; 
	} 
	else{ 
		$checked =""; 
	}
	
	//GET DROPDOWN DATA FOR SLIDER
	$option_name2 = 'tc_history_slider';
	$data_slider = get_option( $option_name2 );	
	if(empty($history_slider)){ 
		$history_slider='';
	} 
	
	//GET DROPDOWN DATA FOR TABLE
	$option_name3 = 'tc_gainers_losers_tables';
	$gainers_losers_tables = get_option( $option_name3 );	
	if(empty($gainers_losers_tables)){ 
		$gainers_losers_tables='';
	} 
	
	//GET CURRENCIES DATA
	$option_name4 = 'tc_currencies';
	$tc_currencies = get_option( $option_name4 );	
	if(empty($tc_currencies)){ 
		$tc_currencies='';
	} 
	
    $html="";
	$html.="<div class='wrap'> <h1 class='wp-heading-inline'> Settings </h1>";
	if(isset($_POST["save_settings"])){ 
		$html.="<div class='updated notice'><p>Saved successfully.</p></div>";
	}
    $html.="<div class='admin_settings'><form name='tc_admin' method='POST'>";
	
	$html.="<table>";
	
	$html.="<tr>";
	$html.="<td>";
	$html.="<label> Disable Currency Slider: </label>";
	$html.="</td>";
	$html.="<td>";
	$html.="<input type='hidden' name='page' value='totalcryptos'><input type='checkbox' class='currency_slider' name='tc_currency_slider' $checked value='1'>";
	$html.="</td>";
	$html.="</tr>";
	
	$html.="<tr>";
	$html.="<td>";
	$html.="<label> Show Data On Slider: </label>";
	$html.="</td>";
	$html.="<td>";
	$html.="<select name='tc_history_slider' style='width:300px;'>";
	$html.="<option value='1h'";
	if($history_slider=='1h'){
		$html.="selected='selected'";
	}
	$html.=" >1 Hour (default)</option>";
	$html.="<option value='24h'";
	if($history_slider=='24h'){
		$html.="selected='selected'";
	}
	$html.=" >24 Hour</option>";
	$html.="<option value='7d'";
	if($history_slider=='7d'){
		$html.="selected='selected'";
	}
	$html.=" >7 Days</option>";
	$html.="</select>";
	$html.="</td>";
	$html.="</tr>";
	
	$html.="<tr>";
	$html.="<td>";
	$html.="<label> Enter Currencies To Display On slider:<br/>(Leave blank to show default data) </label>";
	$html.="</td>";
	$html.="<td>";
	$html.="<textarea name='tc_currencies' style='width:300px;'>".$tc_currencies."</textarea><br/>Enter comma seperated lists of currencies(eg btc, eth etc).";
	$html.="</td>";
	$html.="</tr>";
	
	$html.="<tr>";
	$html.="<td>";
	$html.="<label> Show Gainers/Losers Data In Tables: </label>";
	$html.="</td>";
	$html.="<td>";
	$html.="<select name='tc_gainers_losers_tables' style='width:300px;'>";
	$html.="<option value='1h'";
	if($gainers_losers_tables=='1h'){
		$html.="selected='selected'";
	}
	$html.=" >1 Hour (default)</option>";
	$html.="<option value='24h'";
	if($gainers_losers_tables=='24h'){
		$html.="selected='selected'";
	}
	$html.=" >24 Hour</option>";
	$html.="<option value='7d'";
	if($gainers_losers_tables=='7d'){
		$html.="selected='selected'";
	}
	$html.=" >7 Days</option>";
	$html.="</select>";
	$html.="</td>";
	$html.="</tr>";
	
	$html.="</table>";
	$html.="<div class='form_footer'>";
    $html.="<input type='submit' name='save_settings' class='button button-primary' value='Save settings'>";
	$html.="</div>";	
    $html.="</form>";	
    $html.="</div>";	
    $html.="</div>";	
	echo  $html;
}

function save_settings()
{
	if(isset($_POST["save_settings"])){
		//SAVE CURRENCY SLIDER DATA
		$option_name = 'tc_currency_slider';
		$new_value = $_POST["tc_currency_slider"];	
		if(get_option($option_name)!==false){
			update_option($option_name,$new_value);
		} 
		else{
			$deprecated = null;
			$autoload = 'no';
			add_option($option_name, $new_value, $deprecated, $autoload);
		} 
		//SAVE GAINERS/LOSERS SLIDER DATA
		$option_name2 = 'tc_history_slider';
		$new_value2 = $_POST["tc_history_slider"];	
		if(get_option($option_name2)!==false){
			update_option($option_name2,$new_value2);
		} 
		else{
			$deprecated = null;
			$autoload = 'no';
			add_option($option_name2, $new_value2, $deprecated, $autoload);
		} 
		//SAVE GAINERS/LOSERS TABLES DATA
		$option_name3 = 'tc_gainers_losers_tables';
		$new_value3 = $_POST["tc_gainers_losers_tables"];	
		if(get_option($option_name3)!==false){
			update_option($option_name3,$new_value3);
		} 
		else{
			$deprecated = null;
			$autoload = 'no';
			add_option($option_name3, $new_value3, $deprecated, $autoload);
		} 
		//SAVE CURRENCIES DATA
		$option_name4 = 'tc_currencies';
		$new_value4 = $_POST["tc_currencies"];
		$new_value4 = preg_replace('/\s+/', '', $new_value4);		
		if(get_option($option_name4)!==false){
			update_option($option_name4,$new_value4);
		} 
		else{
			$deprecated = null;
			$autoload = 'no';
			add_option($option_name4, $new_value4, $deprecated, $autoload);
		} 
	}
}

add_action( 'admin_init', 'save_settings' );

function tc_help()
{
	$html="";
	$html.="<div class='wrap'><h1 class='wp-heading-inline'> Help </h1><div class='admin_help'><h4>Please read following instructions to use this plugin.</h4><p><b>1.</b> Use shortcode [tc_top_products] to show top products.</p><p><b>2.</b> Use shortcode [tc_gainers_losers] to show top gainers/losers.</p><p><b>3.</b> Use shortcode [tc_tc100] to show TC index.</p><p><b>4.</b> By default ticker displays at footer. You can disable ticker by going into 'Settings' section. You can use shortcode [tc_market_marquee] to display this at any other place.</p></div></div>";
	
	echo $html;
}

/************************ Showing market marquee ****************************/

function tc_market_marquee()
{  
	$time='1h';
	$history_slider = get_option( 'tc_history_slider' );	
	if(!empty($history_slider)){
		$time=$history_slider;
	}
	
	$tc_currencies = get_option( 'tc_currencies' );	
	if(empty($tc_currencies)){
		$data=array('time'=>$time,'currencies'=>array());
	}
	else{
		$data=array('time'=>$time,'currencies'=>explode(',',$tc_currencies));
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, TC_PATH.'api/sliderData');
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
		'Content-Length: ' . strlen(json_encode($data)))
	); 
	
	$response = curl_exec($ch);
	$response=json_decode($response);
	if($response->errCode==1){
		$option_name = 'tc_currency_slider';
		if(empty(get_option( $option_name ))){
			if(empty($tc_currencies)){
				$Gainers_ApiData = $response->data->gainers;
				$losers_ApiData = $response->data->losers;
				$html = "";
				$html.="<div class='news gainers_losers_div'><span class='news-item'>";
				foreach($Gainers_ApiData as $key=>$val)
				{
					$g_currency = $val->name;
					$g_symbol = $val->symbol;
					$g_price = $val->price_usd;
					$g_percentage = $val->percent_change_24h;
					if(empty($g_percentage)){  $g_percentage = "0";}		
					$html.= "<div class='gainers_texts'><img src='".TC_PATH."images/currencies/".strtolower($g_symbol).".png' width='20px' height='20px' onError=\"this.src='".TC_PATH."images/currency.png'\" /><p>&nbsp;<span class='gainers_name'>".$g_currency."(".$g_symbol.")</span> &nbsp;<span class='gainers_price'>$".$g_price."</span>&nbsp;<span class='gainers_perc'>".$g_percentage."%</span>&nbsp;&nbsp;&nbsp;|</p></div>";
				}

				foreach($losers_ApiData as $key=>$val2)
				{
					$l_currency = $val2->name;
					$l_symbol = $val2->symbol;
					$l_price = $val2->price_usd;
					$l_percentage = $val2->percent_change_24h;
					if(empty($l_percentage)){  $l_percentage = "0";}
					$html.= "<div class='gainers_texts'><img src='".TC_PATH."images/currencies/".strtolower($l_symbol).".png' width='20px' height='20px' onError=\"this.src='".TC_PATH."images/currency.png'\" /><p>&nbsp;<span class='losers_name'>".$l_currency."(".$l_symbol.")</span> <b>&nbsp;<span class='losers_price'> $".$l_price."</span>&nbsp;<span class='losers_perc'>".$l_percentage."%</span>&nbsp;&nbsp;&nbsp;</p></div>";
				}	
				$html.="</span></div>";
				return $html;
			}
			else{
				$ApiData = $response->data;
				$html = "";
				$html.="<div class='news gainers_losers_div'><span class='news-item'>";
				foreach($ApiData as $key=>$val)
				{
					$currency = $val->name;
					$symbol = $val->symbol;
					$price = $val->price_usd;
					$percentage = $val->percent_change_24h;
					if(empty($percentage)){  $percentage = "0";}		
					$html.= "<div class='gainers_texts'><img src='".TC_PATH."images/currencies/".strtolower($symbol).".png' width='20px' height='20px' onError=\"this.src='".TC_PATH."images/currency.png'\" />&nbsp;<span class='gainers_name'>".$currency."(".$symbol.")</span> &nbsp;";
					if($percentage>0){
						$html.= "<span class='gainers_price'>$".$price."</span>&nbsp;<span class='gainers_perc'>".$percentage."%</span>&nbsp;&nbsp;&nbsp;</p></div>";
					}
					else{
						$html.= "<span class='losers_price'>$".$price."</span>&nbsp;<span class='losers_perc'>".$percentage."%</span>&nbsp;&nbsp;&nbsp;</p></div>";
					}
				}
				$html.="</span></div>";
				return $html;
			}
		}
	}
	else{
		return $response->message;
	}	
}

function tc_market_marquee_footer() {
    echo tc_market_marquee();
}
add_action( 'wp_footer', 'tc_market_marquee_footer', 100 );

add_shortcode("tc_market_marquee","tc_market_marqueeshort");

/************************ Marquee Shortcode ****************************/

function tc_market_marqueeshort()
{  
	$time='1h';
	$history_slider = get_option( 'tc_history_slider' );	
	if(!empty($history_slider)){
		$time=$history_slider;
	}
	
	$tc_currencies = get_option( 'tc_currencies' );	
	if(empty($tc_currencies)){
		$data=array('time'=>$time,'currencies'=>array());
	}
	else{
		$data=array('time'=>$time,'currencies'=>explode(',',$tc_currencies));
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, TC_PATH.'api/sliderData');
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
		'Content-Length: ' . strlen(json_encode($data)))
	); 
	
	$response = curl_exec($ch);
	$response=json_decode($response);
	if($response->errCode==1){
		$option_name = 'tc_currency_slider';
		if(!empty(get_option( $option_name ))){
			if(empty($tc_currencies)){
				$Gainers_ApiData = $response->data->gainers;
				$losers_ApiData = $response->data->losers;
				$html = "";
				$html.="<div class='news'><span class='news-item'>";
				foreach($Gainers_ApiData as $key=>$val)
				{
					$g_currency = $val->name;
					$g_symbol = $val->symbol;
					$g_price = $val->price_usd;
					$g_percentage = $val->percent_change_24h;
					if(empty($g_percentage)){  $g_percentage = "0";}		
					$html.= "<div class='gainers_texts'><img src='".TC_PATH."images/currencies/".strtolower($g_symbol).".png' width='20px' height='20px' onError=\"this.src='".TC_PATH."images/currency.png'\" /><p>&nbsp;<span class='gainers_name'>".$g_currency."(".$g_symbol.")</span> </b>&nbsp;<span class='gainers_price'>$".$g_price."</span>&nbsp;<span class='gainers_perc'>".$g_percentage."%</span>&nbsp;&nbsp;&nbsp;</p></div>";
				}

				foreach($losers_ApiData as $key=>$val2)
				{
					$l_currency = $val2->name;
					$l_symbol = $val2->symbol;
					$l_price = $val2->price_usd;
					$l_percentage = $val2->percent_change_24h;
					if(empty($l_percentage)){  $l_percentage = "0";}
					$html.= "<div class='gainers_texts'><img src='".TC_PATH."images/currencies/".strtolower($l_symbol).".png' width='20px' height='20px' onError=\"this.src='".TC_PATH."images/currency.png'\" /><p>&nbsp;<span class='losers_name'>".$l_currency."(".$l_symbol.")</span> &nbsp;<span class='losers_price'> $".$l_price."</span>&nbsp;<span class='losers_perc'>".$l_percentage."%</span>&nbsp;&nbsp;&nbsp;</p></div>";
				}	
				$html.="</span></div>";
				return $html;
			}
			else{
				$ApiData = $response->data;
				$html = "";
				$html.="<div class='news gainers_losers_div'><span class='news-item'>";
				foreach($ApiData as $key=>$val)
				{
					$currency = $val->name;
					$symbol = $val->symbol;
					$price = $val->price_usd;
					$percentage = $val->percent_change_24h;
					if(empty($percentage)){  $percentage = "0";}		
					$html.= "<div class='gainers_texts'><img src='".TC_PATH."images/currencies/".strtolower($symbol).".png' width='20px' height='20px' onError=\"this.src='".TC_PATH."images/currency.png'\" /><p>&nbsp;<span class='gainers_name'>".$currency."(".$symbol.")</span> &nbsp;";
					if($percentage>0){
						$html.= "<span class='gainers_price'>$".$price."</span>&nbsp;<span class='gainers_perc'>".$percentage."%</span>&nbsp;&nbsp;&nbsp;</p></div>";
					}
					else{
						$html.= "<span class='losers_price'>$".$price."</span>&nbsp;<span class='losers_perc'>".$percentage."%</span>&nbsp;&nbsp;&nbsp;</p></div>"; 
					}
				}
				$html.="</span></div>";
				return $html;
			}
		}
	}
	else{
		return $response->message;
	}	
}
/************************ Showing TC100 Prices ****************************/

function tc_tc100(){
	$response=file_get_contents(TC_PATH.'api/tcPrices');
	$response=json_decode($response);
	if($response->errCode==1){
		echo "<div class='row'>";
		echo "<table class='tc100 table table-striped table-bordered table-hover' width='100%'>";
		echo '<thead>';
		echo "<tr>";
		echo "<th width='33.33%' style='text-align:center;'>";
		echo "TC100";
		echo "</th>";
		echo "<th width='33.33%' style='text-align:center;'>";
		echo "TCw100";
		echo "</th>";
		echo "<th width='33.33%' style='text-align:center;'>";
		echo "Total Market Cap";
		echo "</th>";
		echo "</tr>";
		echo '</thead>';
		echo "<td width='33.33%' style='text-align:center;'>";
		echo '$'.$response->data->tc100;
		echo "</td>";
		echo "<td width='33.33%' style='text-align:center;'>";
		echo $response->data->tc100;
		echo "</td>";
		echo "<td width='33.33%' style='text-align:center;'>";
		echo '$'.$response->data->total_usd_market_cap;
		echo "</td>";
		echo "</tr>";          
		echo "</table>";
		echo "</div>";
	}
	else{
		echo $response->message;
	}
	
}
add_shortcode('tc_tc100', 'tc_tc100');