<?php
/*
Plugin Name: Cielo API 3.0 - Loja5
Description: Integra&ccedil;&atilde;o de Pagamento ao Cielo API 3.0.
Version: 3.0
Author: Loja5.com.br
Author URI: https://loja5.com.br/
Copyright: © 2009-2019 Loja5.
License: Commercial
*/

//define a pasta do modulo
define('CIELO_WEBSERVICE_WOO_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ));
define('LOJA5_CIELO_WEBSERVICE_WOO_MODULO_COMERCIAL', __FILE__ );
define('CIELO_WEBSERVICE_WOO_CURL_SSL', 6);
define('CIELO_WEBSERVICE_WOO_PRAZO_GESTOR', 5);
define('CIELO_WEBSERVICE_WOO_BAIXA_STOCK', false);
define('CIELO_WEBSERVICE_WOO_REESTOCK', false);

//atalhos
function plugin_action_links_loja5_woo_cielo_webservice( $links ) {
    $plugin_links = array();
    if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.1', '>=' ) ) {
        $plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=loja5_woo_cielo_webservice' ) ) . '">' . __( 'Crédito', 'loja5-woo-cielo-webservice' ) . '</a>';
        $plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=loja5_woo_cielo_webservice_debito' ) ) . '">' . __( 'Débito', 'loja5-woo-cielo-webservice' ) . '</a>';
		$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=loja5_woo_cielo_webservice_boleto' ) ) . '">' . __( 'Boleto', 'loja5-woo-cielo-webservice' ) . '</a>';
		$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=loja5_woo_cielo_webservice_tef' ) ) . '">' . __( 'TEF', 'loja5-woo-cielo-webservice' ) . '</a>';
    } else {
        $plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_gateway_loja5_woo_cielo_webservice' ) ) . '">' . __( 'Crédito', 'loja5-woo-cielo-webservice' ) . '</a>';
        $plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_gateway_loja5_woo_cielo_webservice_debito' ) ) . '">' . __( 'Débito', 'loja5-woo-cielo-webservice' ) . '</a>';
		$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_gateway_loja5_woo_cielo_webservice_boleto' ) ) . '">' . __( 'Boleto', 'loja5-woo-cielo-webservice' ) . '</a>';
		$plugin_links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_gateway_loja5_woo_cielo_webservice_tef' ) ) . '">' . __( 'TEF', 'loja5-woo-cielo-webservice' ) . '</a>';
    }
    return array_merge( $plugin_links, $links );
}
if(is_admin()) {
    add_filter('plugin_action_links_'.plugin_basename( __FILE__ ),'plugin_action_links_loja5_woo_cielo_webservice');
}

//funcao de inicializacao
function loja5_woo_cielo_webservice_init() {
	//se possui ioncube loads
	if(extension_loaded("IonCube Loader")) {
	
		//chama as classes do modulo
		if ( !class_exists( 'WC_Payment_Gateway' ) ) return;
		
		//versao 
		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '<' ) ) {
			add_action( 'admin_notices', 'loja5_woo_cielo_webservice_alerta_versao' );
		}
		
		if ( !class_exists( 'WC_Gateway_Loja5_Woo_Cielo_Webservice' ) ){
			//especifico por versao
			if(version_compare(PHP_VERSION, '5.4.0', '<')) {
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php53/class.loja5.php' );
			}elseif(version_compare(PHP_VERSION, '5.5.0', '<')) {
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php54/class.loja5.php' );
			}elseif(version_compare(PHP_VERSION, '5.6.0', '<')){
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php55/class.loja5.php' );
			}elseif(version_compare(PHP_VERSION, '7.1.0', '<')){
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php56/class.loja5.php' );
			}elseif(version_compare(PHP_VERSION, '7.2.0', '<')){
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php71/class.loja5.php' );
			}else{
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php72/class.loja5.php' );
			}
			//em comum
			include_once(CIELO_WEBSERVICE_WOO_PATH.'/classes/class.cielo_credito.php' );
			include_once(CIELO_WEBSERVICE_WOO_PATH.'/classes/class.cielo_debito.php' );
			include_once(CIELO_WEBSERVICE_WOO_PATH.'/classes/class.cielo_tef.php' );
			include_once(CIELO_WEBSERVICE_WOO_PATH.'/classes/class.cielo_boleto.php' );
		}
		
		//class validar cpf/cnpj
		if ( !class_exists( 'ValidaCPFCNPJ' ) ){
			include_once(CIELO_WEBSERVICE_WOO_PATH.'/classes/class.fiscal.php' );
		}
		
		//cria um metabox em detalhes do pedido
		if ( !class_exists( 'WC_Cielo_Webservice_Loja5_Metabox' ) ){
			require_once(CIELO_WEBSERVICE_WOO_PATH.'/classes/class.metabox.cielo.php');
			new WC_Cielo_Webservice_Loja5_Metabox;
		}
		
		//permissao de escrita 
		if(!is_writable(CIELO_WEBSERVICE_WOO_PATH)){
			add_action( 'admin_notices', 'loja5_woo_cielo_webservice_alerta_escrita' );
		}
		
		//admin
		if ( !class_exists( 'WC_Cielo_Webservice_Loja5_Admin' ) ){
			//especifico por versao
			if(version_compare(PHP_VERSION, '5.4.0', '<')) {
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php53/admin.php' );
			}elseif(version_compare(PHP_VERSION, '5.5.0', '<')) {
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php54/admin.php' );
			}elseif(version_compare(PHP_VERSION, '5.6.0', '<')){
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php55/admin.php' );
			}elseif(version_compare(PHP_VERSION, '7.1.0', '<')){
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php56/admin.php' );
			}elseif(version_compare(PHP_VERSION, '7.2.0', '<')){
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php71/admin.php' );
			}else{
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php72/admin.php' );
			}
			new WC_Cielo_Webservice_Loja5_Admin;
		}
		
		//adiciona o plugin ao woocommerce
		function woocommerce_add_loja5_woo_cielo_webservice($methods) {
			$methods[] = 'WC_Gateway_Loja5_Woo_Cielo_Webservice';
			$methods[] = 'WC_Gateway_Loja5_Woo_Cielo_Webservice_Debito';
			$methods[] = 'WC_Gateway_Loja5_Woo_Cielo_Webservice_TEF';
			$methods[] = 'WC_Gateway_Loja5_Woo_Cielo_Webservice_Boleto';
			return $methods;
		}
		add_filter('woocommerce_payment_gateways', 'woocommerce_add_loja5_woo_cielo_webservice');
	
	}else{
		//alerta ioncube
		add_action( 'admin_notices', 'loja5_woo_cielo_webservice_alerta_ioncube' );
	}
}

//alerta versao
function loja5_woo_cielo_webservice_alerta_versao(){
	echo '<div class="error">';
	echo '<p><strong>Cielo API [Loja5]:</strong> Requer vers&atilde;o Woo 3.x ou superior, atualize seu Woo para vers&atilde;o compativel!</p>';
	echo '</div>';
}

//alerta permissao de escrita
function loja5_woo_cielo_webservice_alerta_escrita(){
	echo '<div class="error">';
	echo '<p><strong>Cielo API [Loja5]:</strong> Aplique permiss&atilde;o de escrita ao diretorio <u>'.CIELO_WEBSERVICE_WOO_PATH.'</u> para que o m&oacute;dulo possa ser ativado corretamente!</p>';
	echo '</div>';
}

//alerta ioncube 
function loja5_woo_cielo_webservice_alerta_ioncube(){
	echo '<div class="error">';
	echo '<p><strong>Cielo API [Loja5]:</strong> Sua hospedagem n&atilde;o possui o Ioncube ativado, solicite a mesma ativar ou veja com o gestor de seu host!</p>';
	echo '</div>';
}

//inicializa o modulo no wordpress
add_action('plugins_loaded', 'loja5_woo_cielo_webservice_init', 0);

//cron de pagamento boleto e tef 
add_action( 'wp_ajax_cron_boleto_tef_cielo_webservice', 'cron_boleto_tef_cielo_webservice');
add_action( 'wp_ajax_nopriv_cron_boleto_tef_cielo_webservice','cron_boleto_tef_cielo_webservice');
function cron_boleto_tef_cielo_webservice(){
	global $wpdb;
	//faz o include das config cielo
	if(version_compare(PHP_VERSION, '5.4.0', '<')) {
		include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php53/restclient.php' );
	}elseif(version_compare(PHP_VERSION, '5.5.0', '<')) {
		include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php54/restclient.php' );
	}elseif(version_compare(PHP_VERSION, '5.6.0', '<')){
		include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php55/restclient.php' );
	}elseif(version_compare(PHP_VERSION, '7.1.0', '<')){
		include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php56/restclient.php' );
	}elseif(version_compare(PHP_VERSION, '7.2.0', '<')){
		include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php71/restclient.php' );
	}else{
		include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php72/restclient.php' );
	}
	//consulta
	$pedidos = $wpdb->get_results("SELECT * FROM `wp_cielo_api_loja5` WHERE (metodo='boleto' OR metodo='tef') AND status='0' ORDER BY id DESC LIMIT 50;", 'ARRAY_A');
	foreach($pedidos as $registro){
		//pedido no woo 
		$order = new WC_Order((int)($registro['pedido']));
		$status_atual = str_replace('wc-','',$order->get_status());
		
		//config de acordo o tipo de pagamento 
		if($order->get_payment_method()=='loja5_woo_cielo_webservice'){
			$config = new WC_Gateway_Loja5_Woo_Cielo_Webservice();
		}elseif($order->get_payment_method()=='loja5_woo_cielo_webservice_debito'){
			$config = new WC_Gateway_Loja5_Woo_Cielo_Webservice_Debito();
		}elseif($order->get_payment_method()=='loja5_woo_cielo_webservice_tef'){
			$config = new WC_Gateway_Loja5_Woo_Cielo_Webservice_TEF();
		}elseif($order->get_payment_method()=='loja5_woo_cielo_webservice_boleto'){
			$config = new WC_Gateway_Loja5_Woo_Cielo_Webservice_Boleto();
		}
		
		//somente pedidos aguardando pagamento
		if($status_atual==str_replace('wc-','','wc-on-hold')){
			
			//cielo api
			if($config->testmode=='yes'){
				$provider = 'Simulado';
				$urlweb = "https://apiquerysandbox.cieloecommerce.cielo.com.br/1/";
			}else{
				$provider = 'Cielo';
				$urlweb = "https://apiquery.cieloecommerce.cielo.com.br/1/";
			}
			$objResposta = array();
			$headers = array(
				"Content-Type" => "application/json",
				"Accept" => "application/json",
				"MerchantId" =>trim($config->afiliacao),
				"MerchantKey" => trim($config->chave),
				"RequestId" => "",
			);
			$api = new RestClient(array(
				'base_url' => $urlweb, 
				'headers' => $headers, 
			));
			$response = $api->get("sales/".$registro['id_pagamento']."");
			$dados_pedido = @json_decode($response->response,true);
			if(($response->status==200 || $response->status==201) && isset($dados_pedido['Payment']['Status'])){
				if($dados_pedido['Payment']['Status']==2){
					//atualiza
					$order->update_status($config->pago);
					echo $registro['pedido'].' pago!<br>';
					//atualiza o pedido no banco de dados
					$wpdb->query("UPDATE `wp_cielo_api_loja5` SET `status` =  '2' WHERE `pedido` = '".(int)($registro['pedido'])."';");
				}elseif($dados_pedido['Payment']['Status']==3 || $dados_pedido['Payment']['Status']==10 || $dados_pedido['Payment']['Status']==13){
					//atualiza
					$order->update_status($config->cancelado);
					echo $registro['pedido'].' cancelado!<br>';
					//atualiza o pedido no banco de dados
					$wpdb->query("UPDATE `wp_cielo_api_loja5` SET `status` =  '10' WHERE `pedido` = '".(int)($registro['pedido'])."';");
				}else{
					//exibe
					echo $registro['pedido'].' aguardando!<br>';
				}
			}

		}		
	}
	echo '<br>CRON - OK';
	exit;
}

//retorno de dados postback cielo
add_action( 'wp_ajax_retorno_ipn_cielo_webservice', 'retorno_ipn_cielo_webservice');
add_action( 'wp_ajax_nopriv_retorno_ipn_cielo_webservice','retorno_ipn_cielo_webservice');
function retorno_ipn_cielo_webservice(){
	global $wpdb;
	if(isset($_REQUEST['PaymentId']) && isset($_REQUEST['ChangeType'])){
		$id_pagamento = trim($_REQUEST['PaymentId']);
		$tipo_req = (int)$_REQUEST['ChangeType'];
		$id_meio_woo = 'loja5_woo_cielo_webservice';
		if($tipo_req==1){
			//faz o include das config cielo
			if(version_compare(PHP_VERSION, '5.4.0', '<')) {
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php53/restclient.php' );
			}elseif(version_compare(PHP_VERSION, '5.5.0', '<')) {
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php54/restclient.php' );
			}elseif(version_compare(PHP_VERSION, '5.6.0', '<')){
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php55/restclient.php' );
			}elseif(version_compare(PHP_VERSION, '7.1.0', '<')){
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php56/restclient.php' );
			}elseif(version_compare(PHP_VERSION, '7.2.0', '<')){
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php71/restclient.php' );
			}else{
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php72/restclient.php' );
			}
			//config 
			$config = new WC_Gateway_Loja5_Woo_Cielo_Webservice();
			
			//cielo api
			if($config->testmode=='yes'){
				$provider = 'Simulado';
				$urlweb = "https://apiquerysandbox.cieloecommerce.cielo.com.br/1/";
			}else{
				$provider = 'Cielo';
				$urlweb = "https://apiquery.cieloecommerce.cielo.com.br/1/";
			}
			$objResposta = array();
			$headers = array(
				"Content-Type" => "application/json",
				"Accept" => "application/json",
				"MerchantId" =>trim($config->afiliacao),
				"MerchantKey" => trim($config->chave),
				"RequestId" => "",
			);
			$api = new RestClient(array(
				'base_url' => $urlweb, 
				'headers' => $headers, 
			));
			$response = $api->get("sales/".$id_pagamento."");
			$dados_pedido = @json_decode($response->response,true);
			
			//debug
			if ( 'yes' === $config->debug ) {
				$logs = new WC_Logger();
				$logs->add( $id_meio_woo, 'Log Postback Cielo '.strtoupper($tipo).': '.$response->response );
			}
			
			//se ocorreu erro salva os logs 
			if($response->status < 200 || $response->status > 201){
				$logs = new WC_Logger();
				$logs->add( $id_meio_woo, 'Erro Cielo Postback em '.date('d/m/Y H:i:s').'');
				$logs->add( $id_meio_woo, 'Log: '.$response->response );
			}
			
			//resultado
			if(($response->status==200 || $response->status==201) && isset($dados_pedido['Payment']['PaymentId'])){
				//infors
				$pedido_id = $dados_pedido['MerchantOrderId'];
				$status_id = $dados_pedido['Payment']['Status'];
				$lr = isset($dados_pedido['Payment']['ReturnCode'])?$dados_pedido['Payment']['ReturnCode']:'';
				$lr_log = isset($dados_pedido['Payment']['ReturnMessage'])?$dados_pedido['Payment']['ReturnMessage']:'';
				
				//pega o pedido
				$order = new WC_Order((int)($pedido_id));
				if(!$order->get_id()){
					die('Pedido nao encontrado!');
				}
				$status_atual = str_replace('wc-','',$order->get_status());
				
				//status titulo
				switch($status_id){
					case '2':
						$status_mudar = $config->pago;
						$status = 'Aprovada';
					break;
					case '1':
					$status_mudar = $config->autorizado;
						$status = 'Autorizada';
					break;
					case '3':
						$status_mudar = $config->negado;
						$status = 'Negada';
					break;
					case '10':
					case '13':
						$status_mudar = $config->cancelado;
						$status = 'Cancelada';
					break;
				}
				
				//cria uma nota no pedido
				if(isset($status_mudar)){
					if($order->get_payment_method()=='loja5_woo_cielo_webservice_debito'){
						$order->add_order_note("Transa&ccedil;&atilde;o D&eacute;bito Cielo - TID ".$dados_pedido['Payment']['Tid']." - ".$status." (POST)");
					}elseif($order->get_payment_method()=='loja5_woo_cielo_webservice_tef'){
						$order->add_order_note("Transa&ccedil;&atilde;o TEF Cielo - ID ".$dados_pedido['Payment']['PaymentId']." - ".$status." (POST)");
					}elseif($order->get_payment_method()=='loja5_woo_cielo_webservice_boleto'){
						$order->add_order_note("Transa&ccedil;&atilde;o Boleto Cielo - ID ".$dados_pedido['Payment']['PaymentId']." - ".$status." (POST)");
					}else{
						$order->add_order_note("Transa&ccedil;&atilde;o Cr&eacute;dito Cielo - TID ".$dados_pedido['Payment']['Tid']." - ".$status." (POST)");
					}
				}
					
				//status
				if(isset($status_mudar) && str_replace('wc-','',$status_mudar)!=$status_atual){
					$order->update_status($status_mudar);
				}
				
				//atualiza o pedido no banco de dados
				$wpdb->query("UPDATE `wp_cielo_api_loja5` SET  `lr` =  '".$lr."',  `lr_log` =  '".$lr_log."', `status` =  '".$status_id."' WHERE `pedido` = '".(int)($pedido_id)."';");
				
			}
			
		}
	}
	echo 'IPN - OK';
	exit;
}

//retorno de dados debito/tef
add_action( 'wp_ajax_retorno_debito_cielo_webservice', 'retorno_debito_cielo_webservice');
add_action( 'wp_ajax_nopriv_retorno_debito_cielo_webservice','retorno_debito_cielo_webservice');
function retorno_debito_cielo_webservice(){
	global $wpdb;
	if(isset($_REQUEST['PaymentId']) && !empty($_REQUEST['PaymentId'])){
		//faz o include das config cielo
		if(version_compare(PHP_VERSION, '5.4.0', '<')) {
			include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php53/restclient.php' );
		}elseif(version_compare(PHP_VERSION, '5.5.0', '<')) {
			include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php54/restclient.php' );
		}elseif(version_compare(PHP_VERSION, '5.6.0', '<')){
			include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php55/restclient.php' );
		}elseif(version_compare(PHP_VERSION, '7.1.0', '<')){
			include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php56/restclient.php' );
		}elseif(version_compare(PHP_VERSION, '7.2.0', '<')){
			include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php71/restclient.php' );
		}else{
			include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php72/restclient.php' );
		}
		$tipo = isset($_REQUEST['tipo'])?$_REQUEST['tipo']:'debito';
		
		//config
		if($tipo=='debito'){
			$config = new WC_Gateway_Loja5_Woo_Cielo_Webservice_Debito();
			$id_meio_woo = 'loja5_woo_cielo_webservice_debito';
		}elseif($tipo=='tef'){
			$config = new WC_Gateway_Loja5_Woo_Cielo_Webservice_TEF();
			$id_meio_woo = 'loja5_woo_cielo_webservice_tef';
		}elseif($tipo=='boleto'){
			$config = new WC_Gateway_Loja5_Woo_Cielo_Webservice_Boleto();
			$id_meio_woo = 'loja5_woo_cielo_webservice_boleto';
		}else{
			$config = new WC_Gateway_Loja5_Woo_Cielo_Webservice();
			$id_meio_woo = 'loja5_woo_cielo_webservice';
		}
		
		//cielo api
		if($config->testmode=='yes'){
			$provider = 'Simulado';
			$urlweb = "https://apiquerysandbox.cieloecommerce.cielo.com.br/1/";
		}else{
			$provider = 'Cielo';
			$urlweb = "https://apiquery.cieloecommerce.cielo.com.br/1/";
		}
		$objResposta = array();
		$headers = array(
			"Content-Type" => "application/json",
			"Accept" => "application/json",
			"MerchantId" =>trim($config->afiliacao),
			"MerchantKey" => trim($config->chave),
			"RequestId" => "",
		);
		$api = new RestClient(array(
			'base_url' => $urlweb, 
			'headers' => $headers, 
		));
		$response = $api->get("sales/".trim($_REQUEST['PaymentId'])."");
		$dados_pedido = @json_decode($response->response,true);
		
		//debug
		if ( 'yes' === $config->debug ) {
			$logs = new WC_Logger();
			$logs->add( $id_meio_woo, 'Log Retorno Cielo '.strtoupper($tipo).': '.$response->response );
		}
		
		//se ocorreu erro salva os logs 
		if($response->status < 200 || $response->status > 201){
			$logs = new WC_Logger();
			$logs->add( $id_meio_woo, 'Erro Cielo Retorno '.strtoupper($tipo).' em '.date('d/m/Y H:i:s').'');
			$logs->add( $id_meio_woo, 'Log: '.$response->response );
		}
		
		//resultado
		if(($response->status==200 || $response->status==201) && isset($dados_pedido['Payment']['PaymentId'])){
			//infors
			$pedido_id = $dados_pedido['MerchantOrderId'];
			$status_id = $dados_pedido['Payment']['Status'];
			$lr = isset($dados_pedido['Payment']['ReturnCode'])?$dados_pedido['Payment']['ReturnCode']:'';
			$lr_log = isset($dados_pedido['Payment']['ReturnMessage'])?$dados_pedido['Payment']['ReturnMessage']:'';
			
			//pega o pedido
			$order = new WC_Order((int)($pedido_id));
			
			//status titulo
			switch($status_id){
				case '2':
					$status = 'Aprovada';
				break;
				case '1':
					$status = 'Autorizada';
				break;
				case '3':
					$status = 'Negada';
				break;
				case '10':
				case '13':
					$status = 'Cancelada';
				break;
				default:
					$status = 'Aguardando Pagamento';
			}
			
			//cria uma nota no pedido
			if($tipo=='debito'){
				$order->add_order_note("Transa&ccedil;&atilde;o D&eacute;bito Cielo - TID ".$dados_pedido['Payment']['Tid']." - ".$status."");
			}elseif($tipo=='tef'){
				$order->add_order_note("Transa&ccedil;&atilde;o TEF Cielo - ID ".$dados_pedido['Payment']['PaymentId']." - ".$status."");
			}elseif($tipo=='boleto'){
				$order->add_order_note("Transa&ccedil;&atilde;o Boleto Cielo - ID ".$dados_pedido['Payment']['PaymentId']." - ".$status."");
			}else{
				$order->add_order_note("Transa&ccedil;&atilde;o Cr&eacute;dito Cielo - TID ".$dados_pedido['Payment']['Tid']." - ".$status."");
			}
				
			//status
			switch($status_id){
				case '2':
					$order->update_status($config->pago);
				break;
				case '1':
					$order->update_status($config->autorizado);
				break;
				case '3':
					$order->update_status($config->negado);
				break;
				case '10':
				case '13':
					$order->update_status($config->cancelado);
				break;
			}
			
			//atualiza o pedido no banco de dados
			$wpdb->query("UPDATE `wp_cielo_api_loja5` SET  `lr` =  '".$lr."',  `lr_log` =  '".$lr_log."', `status` =  '".$status_id."' WHERE `pedido` = '".(int)($pedido_id)."';");
			
			//redireciona ao cupom
			$link = add_query_arg(array('pedido'=>$pedido_id,'hash'=>md5($pedido_id.$pedido_id)),$config->get_return_url( $order ));
			wp_redirect($link);
			exit;
			
		}else{
			//senao fim
			$link = get_option('woocommerce_myaccount_page_id');
			$link = get_permalink($link);
			wp_redirect($link);
			exit;
		}
	}else{
		//senao fim
		$link = get_option('woocommerce_myaccount_page_id');
		$link = get_permalink($link);
		wp_redirect($link);
		exit;
	}
}

//carregamento de parcelas
add_action( 'wp_ajax_parcelas_cielo_webservice', 'parcelas_cielo_webservice');
add_action( 'wp_ajax_nopriv_parcelas_cielo_webservice','parcelas_cielo_webservice');
function parcelas_cielo_webservice(){
    //carrega as config do modulo
    $config = new WC_Gateway_Loja5_Woo_Cielo_Webservice();
    //envia o juros incluso
    $enviar_juros_embutido = true;
    //valida o valor esta correto
    if(isset($_POST['id']) && isset($_POST['total']) && sha1(md5($_POST['total']))==$_POST['hash']){
        //pega os dados e vars
        $minimo = (float)$config->minimo;
        $desconto = 0;
        $divmax = $config->div;
        $divsem = $config->sem;
        $juros  = $config->juros;
        $total  = (float)$_POST['total'];

        //corrije bug erro etapa2
        $total = $total_limpo = number_format($total, 2, '.', '');

        //calcula os minimos
        $split = (int)$total/$minimo;
        if($split>=$divmax){
			$div = (int)$divmax;
        }elseif($split<$divmax){
			$div = (int)$split;
        }elseif($total<=$minimo){
			$div = 1;
        }
		
		//juros por parcela 
		$juros_p = array();
		$juros_p[2] = $juros;
		$juros_p[3] = $juros;
		$juros_p[4] = $juros;
		$juros_p[5] = $juros;
		$juros_p[6] = $juros;
		$juros_p[7] = $juros;
		$juros_p[8] = $juros;
		$juros_p[9] = $juros;
		$juros_p[10] = $juros;
		$juros_p[11] = $juros;
		$juros_p[12] = $juros;

        //inicio
        $linhas[''] = "-- Selecione --";

        //seleta o tipo de parcelamento
        if($config->parcelamento=='operadora'){
			$pcom = 3;
        }else{
			$pcom = 2;
        }

        //avista
        if($desconto>0){
        $desconto_valor = ($total/100)*$desconto;
        $linhas[base64_encode('1|1|'.number_format(($total-$desconto_valor), 2, '.', '').'|'.base64_encode($_POST['id']).'|'.base64_encode($total).'|'.md5(($total-$desconto_valor)))] = "&Agrave; vista por ".wc_price(number_format(($total-$desconto_valor), 2, '.', ''))." (j&aacute; com ".$desconto."% off)";
        }else{
        $linhas[base64_encode('1|1|'.number_format(($total), 2, '.', '').'|'.base64_encode($_POST['id']).'|'.base64_encode($total).'|'.md5($total))] = "&Agrave; vista por ".wc_price(number_format(($total), 2, '.', ''))."";
        }

        //se tiver parcelado
        if($_POST['id']!='visaelectron' && $_POST['id']!='maestro' && $_POST['id']!='elodebito' && $_POST['id']!='discover' && $_POST['id']!='jcb'){
            if($div>=2){
                for($i=1;$i<=$div;$i++){
                    if($i>1){
                        if($i<=$divsem){
                            $total = number_format($total, 2, '.', '');
                            $linhas[base64_encode(''.$i.'|2|'.number_format(($total), 2, '.', '').'|'.base64_encode($_POST['id']).'|'.base64_encode($total).'|'.md5($total))] = $i."x de ".wc_price(number_format(($total/$i), 2, '.', ''))." sem juros (".wc_price(number_format($total, 2, '.', '')).")";
                        }else{
							$juros_par = isset($juros_p[$i])?$juros_p[$i]:$juros;
                            $parcela_com_juros = $config->calcular_juros_cielo_webservice($total_limpo, $juros_par, $i);
                            //juros imbutido
                            if($enviar_juros_embutido){
                                $total = number_format(($parcela_com_juros*$i), 2, '.', '');
                            }
                            $linhas[base64_encode(''.$i.'|'.$pcom.'|'.number_format(($total), 2, '.', '').'|'.base64_encode($_POST['id']).'|'.base64_encode($total).'|'.md5($total))] = $i."x de ".wc_price(number_format(($parcela_com_juros), 2, '.', ''))." com juros (".wc_price(number_format(($parcela_com_juros*$i), 2, '.', '')).")";
                        }
                    }
                }
            }
        }
        //converte json
        echo json_encode($linhas);
    }else{
        $linhas[''] = 'Ops, total invalido!';
        echo json_encode($linhas);
    }
    die();
}
?>