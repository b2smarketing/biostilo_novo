<?php
    class WC_Gateway_Loja5_Woo_Cielo_Webservice_TEF extends WC_Payment_Gateway {
        
        public function __construct() {
            global $woocommerce;
            $this->id           = 'loja5_woo_cielo_webservice_tef';
            $this->icon         = apply_filters( 'woocommerce_loja5_woo_cielo_webservice_tef', plugins_url().'/loja5-woo-cielo-webservice/images/cielo.png' );
            $this->has_fields   = false;
            $this->supports   = array('products');
            $this->description = true;
			$this->method_description = __( 'Ativa o pagamento por Transferência Online via Cielo.', 'loja5-woo-cielo-webservice-boleto' );
            $this->method_title = 'Cielo API 3.0 - TEF';
            $this->init_settings();
            $this->init_form_fields();
			$this->instalar_mysql_cielo_webservice();
            
            foreach ( $this->settings as $key => $val ) $this->$key = $val;
            
            if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) ){
                add_action('woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
            }else{
                add_action('woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );
            }
            
            add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
            
            if ( !$this->is_valid_for_use() ) $this->enabled = false;
        }
        
        public function thankyou_page( $order_id ) {
            global $wpdb;
            //pega o pedido
            $order = new WC_Order((int)($order_id));

            //dados cielo mysql
            $cielo = (array)$wpdb->get_row("SELECT * FROM `wp_cielo_api_loja5` WHERE `pedido` = '".(int)($order_id)."' ORDER BY id DESC;");

			//define o status do pedido
			$status_cielo = isset($cielo['status'])?$cielo['status']:'0';
			switch($status_cielo){
				case '2':
					$status = '<span style="color: #20bb20;">Aprovada</span>';
				break;
				case '1':
					$status = '<span style="color: #2196f3;">Autorizada</span>';
				break;
				case '3':
					$status = '<span style="color: red;">Negada</span>';
				break;
				case '10':
				case '13':
					$status = '<span style="color: red;">Cancelada</span>';
				break;
				default:
					$status = 'Aguardando Pagamento';
			}
            
            //layout
            include_once(dirname(__FILE__) . '/cupom_tef_boleto.php'); 
        }
	
        public function is_valid_for_use() {
            if ( ! in_array( get_woocommerce_currency(), apply_filters( 'woocommerce_loja5_woo_cielo_webservice_tef_supported_currencies', array( 'BRL' ) ) ) ) return false;
            return true;
        }
		
		public function instalar_mysql_cielo_webservice(){
            global $wpdb;
            $wpdb->query("CREATE TABLE IF NOT EXISTS `wp_cielo_api_loja5` (
            `id` int(10) NOT NULL AUTO_INCREMENT,
			`metodo` varchar(40) NOT NULL,
			`id_pagamento` varchar(40) NOT NULL,
            `tid` varchar(40) NOT NULL,
            `pedido` varchar(40) NOT NULL,
            `bandeira` varchar(40) NOT NULL,
            `parcela` varchar(40) NOT NULL,
            `lr` varchar(20) NOT NULL,
			`lr_log` varchar(180) NOT NULL,
            `total` float(10,2) NOT NULL,
            `status` varchar(40) NOT NULL,
            `bin` varchar(40) NOT NULL,
			`link` varchar(255) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
        }
	
        public function get_status_pagamento(){
            if(function_exists('wc_get_order_statuses')){
                return wc_get_order_statuses();
            }else{
                $taxonomies = array( 
                    'shop_order_status',
                );
                $args = array(
                    'orderby'       => 'name', 
                    'order'         => 'ASC',
                    'hide_empty'    => false, 
                    'exclude'       => array(), 
                    'exclude_tree'  => array(), 
                    'include'       => array(),
                    'number'        => '', 
                    'fields'        => 'all', 
                    'slug'          => '', 
                    'parent'         => '',
                    'hierarchical'  => true, 
                    'child_of'      => 0, 
                    'get'           => '', 
                    'name__like'    => '',
                    'pad_counts'    => false, 
                    'offset'        => '', 
                    'search'        => '', 
                    'cache_domain'  => 'core'
                ); 
                foreach(get_terms( $taxonomies, $args ) AS $status){
                    $s[$status->slug] = __( $status->slug, 'woocommerce' );
                }
                return $s;
            }
        }
	
        public function admin_options() {
            ?>
            <?php if ( $this->is_valid_for_use() ) : ?>
                <table class="form-table">
                <?php
                    $this->generate_settings_html();
                ?>
                </table>
            <?php else : ?>
                <div class="inline error"><p><strong><?php _e( 'Gateway Desativado', 'woocommerce' ); ?></strong>: <?php _e( 'Cielo Webservice n&atilde;o aceita o tipo e moeda de sua loja, apenas BRL.', 'woocommerce' ); ?></p></div>
            <?php
                endif;
        }

        public function init_form_fields() {
            //especifico por versao
			if(version_compare(PHP_VERSION, '5.4.0', '<')) {
				include(CIELO_WEBSERVICE_WOO_PATH.'/include/php53/config_tef.php' );
			}elseif(version_compare(PHP_VERSION, '5.5.0', '<')) {
				include(CIELO_WEBSERVICE_WOO_PATH.'/include/php54/config_tef.php' );
			}elseif(version_compare(PHP_VERSION, '5.6.0', '<')){
				include(CIELO_WEBSERVICE_WOO_PATH.'/include/php55/config_tef.php' );
			}elseif(version_compare(PHP_VERSION, '7.1.0', '<')){
				include(CIELO_WEBSERVICE_WOO_PATH.'/include/php56/config_tef.php' );
			}elseif(version_compare(PHP_VERSION, '7.2.0', '<')){
				include(CIELO_WEBSERVICE_WOO_PATH.'/include/php71/config_tef.php' );
			}else{
				include(CIELO_WEBSERVICE_WOO_PATH.'/include/php72/config_tef.php' );
			}			
			$this->form_fields = $config;
        }
	
        public function payment_fields() {
            global $woocommerce;
            if(!isset($_GET['pay_for_order'])){
				$total_cart = number_format($this->get_order_total(), 2, '.', '');
			}else{
				$order_id = wc_get_order_id_by_order_key($_GET['key']);
				$order = new WC_Order( $order_id );
				$total_cart = number_format($order->get_total(), 2, '.', '');
			}
            include(dirname(__FILE__) . '/layout_tef.php'); 
        }
	
        public function validate_fields() {
            global $woocommerce;
            if($_POST['payment_method']=='loja5_woo_cielo_webservice_tef'){
                $erros = 0;
				if($this->get_post('fiscal')==''){
					$this->tratar_erro("Informe um CPF/CNPJ v&aacute;lido!");
					$erros++;
				}
				$cpf_cnpj = new ValidaCPFCNPJ($this->get_post('fiscal'));
				if(!$cpf_cnpj->valida()){
					$this->tratar_erro("O CPF/CNPJ n&atilde;o &eacute; v&aacute;lido!");
					$erros++;
				}
                if($this->get_post('bandeira')==''){
					$this->tratar_erro("Selecione o banco qual deseja concluir o pagamento!");
					$erros++;
                }
                if($erros>0){
                    return false;
                }
            }
            return true;
        }
        
        private function get_post( $name ) {
                if (isset($_POST['cielo_webservice_tef'][$name])) {
                    return $_POST['cielo_webservice_tef'][$name];
                }
                return null;
        }
        
        public function tratar_erro($erro){
            global $woocommerce;
            if(function_exists('wc_add_notice')){
				wc_add_notice($erro,$notice_type = 'error' );
            }else{
				$woocommerce->add_error($erro);
            }
        }
		
		public function limpar($str) {
			$replaces = array(
				'S'=>'S', 's'=>'s', 'Z'=>'Z', 'z'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
				'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
				'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
				'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
				'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y'
			);
			
			return preg_replace('/[^0-9A-Za-z;,.\- ]/', '', strtoupper(strtr(trim($str), $replaces)));
		}
        
        public function process_payment($order_id) {
            global $woocommerce,$wpdb;
            $order = new WC_Order( $order_id );
            
            //funcoes cielo
			if(version_compare(PHP_VERSION, '5.4.0', '<')) {
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php53/include.php' );
			}elseif(version_compare(PHP_VERSION, '5.5.0', '<')) {
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php54/include.php' );
			}elseif(version_compare(PHP_VERSION, '5.6.0', '<')){
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php55/include.php' );
			}elseif(version_compare(PHP_VERSION, '7.1.0', '<')){
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php56/include.php' );
			}elseif(version_compare(PHP_VERSION, '7.2.0', '<')){
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php71/include.php' );
			}else{
				include_once(CIELO_WEBSERVICE_WOO_PATH.'/include/php72/include.php' );
			}
			
			if($this->settings['testmode']=='yes'){
				$provider = 'Simulado';
				$urlweb = "https://apisandbox.cieloecommerce.cielo.com.br/1/";
			}else{
				$provider = 'Cielo';
				$urlweb = "https://api.cieloecommerce.cielo.com.br/1/";
			}
			$objResposta = array();
			$headers = array(
				"Content-Type" => "application/json",
				"Accept" => "application/json",
				"MerchantId" =>trim($this->settings['afiliacao']),
				"MerchantKey" => trim($this->settings['chave']),
				"RequestId" => "",
			);
			$dados = array();
			
			//cliente
			$dados['MerchantOrderId'] = $order->get_id();
			
			$fiscal_valor = preg_replace('/\D/', '', $this->get_post('fiscal'));
			$dados['Customer'] = array(
				'Name'=>$this->limpar($order->get_billing_first_name()) . ' ' . $this->limpar($order->get_billing_last_name()),
				"Identity" => $fiscal_valor,
				'Email'=>$order->get_billing_email(),
				"IdentityType" => (strlen($fiscal_valor)==11?'CPF':'CNPJ'),
				'Address'=>array(
					'Street'=>$this->limpar($order->get_billing_address_1()),
					'Number'=>(isset($order->billing_number)?$order->billing_number:'*'),
					'District'=>$this->limpar((isset($order->billing_neighborhood)?$order->billing_neighborhood:$order->get_billing_address_2())),
					'Complement' => $this->limpar((isset($order->billing_neighborhood)?$order->get_billing_address_2():'')),
					'ZipCode'=>preg_replace('/\D/', '', $order->get_billing_postcode()),
					'City'=>$this->limpar($order->get_billing_city()),
					'State'=>$this->limpar($order->get_billing_state()),
					'Country'=>substr($order->get_billing_country(),0,2),
				)
			);

			$dados['Payment'] = array(
				'Type' => 'EletronicTransfer',
				'Provider' => $this->get_post('bandeira'),
				'Amount' => number_format($order->get_total(), 2, '', ''),
				'ReturnUrl' => admin_url('admin-ajax.php').'?tipo=tef&action=retorno_debito_cielo_webservice',
			);
			
			//rest
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

			$api = new RestClient(array(
				'base_url' => $urlweb, 
				'headers' => $headers, 
			));
			$response = $api->post("sales",json_encode($dados));
			$dados_pedido = @json_decode($response->response,true);
			
			//debug
			if ( 'yes' === $this->settings['debug'] ) {
				$logs = new WC_Logger();
				$logs->add( $this->id, 'Debug Cielo: '.print_r($dados,true) );
				$logs->add( $this->id, 'Resultado: '.print_r($response,true) );
			}

			//trata o resultado
			if(($response->status==200 || $response->status==201) && isset($dados_pedido['Payment']['PaymentId'])){
				$erro = false;
				$objResposta['tipo'] = 'tef';
				$objResposta['tid'] = isset($dados_pedido['Payment']['Tid'])?$dados_pedido['Payment']['Tid']:'';
				$objResposta['status'] = $dados_pedido['Payment']['Status'];
				$objResposta['lr'] = isset($dados_pedido['Payment']['ReturnCode'])?$dados_pedido['Payment']['ReturnCode']:'';
				$objResposta['lr_log'] = isset($dados_pedido['Payment']['ReturnMessage'])?$dados_pedido['Payment']['ReturnMessage']:'';
				$objResposta['parcelas'] = 1;
				$objResposta['bin'] = isset($dados_pedido['Payment']['Url'])?$dados_pedido['Payment']['Url']:'';
				$objResposta['bandeira'] = $dados_pedido['Payment']['Provider'];
				$objResposta['id_pagamento'] = $dados_pedido['Payment']['PaymentId'];
			}elseif(isset($dados_pedido[0]['Message'])){
				$erro = true;
				$objResposta['mensagem'] = $dados_pedido[0]['Message'];
				$objResposta['codigo'] = $dados_pedido[0]['Code'];
			}elseif(isset($dados_pedido['Message'])){
				$erro = true;
				$objResposta['mensagem'] = $dados_pedido['Message'];
				$objResposta['codigo'] = $dados_pedido['Code'];
			}else{
				$erro = true;
				$objResposta['mensagem'] = isset($dados_pedido['Payment']['ReasonMessage'])?$dados_pedido['Payment']['ReasonMessage']:'Erro cielo desconhecido ao processar pagamento Cielo, verificar se o mesmo encontra-se online (ver logs)!';
				$objResposta['codigo'] = isset($dados_pedido['Payment']['ReasonCode'])?$dados_pedido['Payment']['ReasonCode']:'999';
			}
			
			//se ocorreu erro salva os logs 
			if($erro == true){
				$logs = new WC_Logger();
				$logs->add( $this->id, 'Erro Cielo Pedido: '.$order->get_id() .' em '.date('d/m/Y H:i:s').'');
				$logs->add( $this->id, 'Mensagem: '.$objResposta['codigo'].' - '.$objResposta['mensagem'] );
				$logs->add( $this->id, 'Log: '.print_r($objResposta,true) );
			}

			//se nao retornou erro
            if(isset($objResposta['tid']) && $erro==false){
				
				//cria no banco de dados 
				$wpdb->query("INSERT INTO `wp_cielo_api_loja5` (`id`, `metodo`, `id_pagamento`, `tid`, `pedido`, `bandeira`, `parcela`, `lr`, `lr_log`, `total`, `status`, `bin`, `link`) VALUES (NULL, 'tef', '".$objResposta['id_pagamento']."', '".$objResposta['tid']."', '".$order->get_id()."', '".$objResposta['bandeira']."', '".$objResposta['parcelas']."', '".$objResposta['lr']."', '".$objResposta['lr_log']."', '".$order->get_total()."', '".$objResposta['status']."', '', '".$objResposta['bin']."');");
				
				//cria uma nota no pedido
				$order->add_order_note("Transa&ccedil;&atilde;o TEF - ID ".$objResposta['id_pagamento']." via ".strtoupper($objResposta['bandeira'])."");
				
				//status
				$order->update_status('wc-on-hold');		 

				//limpa o carrinho
				$woocommerce->cart->empty_cart();
					
				//se precisar autenticar
				if(false==true && $objResposta['status']==0){
					$urlAutenticacaoLink = $objResposta['bin'];
				}else{
					$urlAutenticacaoLink = add_query_arg(array('pedido'=>$order->get_id(),'hash'=>md5($order->get_id().$order->get_id())),$this->get_return_url( $order ));
				}
				
				//reduz um estoque se aprovado ou autorizado
				if(CIELO_WEBSERVICE_WOO_BAIXA_STOCK && ($objResposta['status']==1 || $objResposta['status']==2)){
					$order->reduce_order_stock();
				}
				
				return array(
					'result' 	=> 'success',
					'redirect'	=>  $urlAutenticacaoLink
				);
                
            }elseif(isset($objResposta['codigo'])){
                
                $this->tratar_erro("Erro: ".$objResposta['codigo'].": ".$objResposta['mensagem']."");
                return false;
                
            }else{
                
                $this->tratar_erro("Erro cielo desconhecido ao processar pagamento Cielo, verificar se o mesmo encontra-se online (ver logs)!");
                return false;
                
            }
            
        }
            
        public function obj2array($obj){
            return json_decode(json_encode($obj),true);
        }
        
        public function json2array($obj){
            return json_decode($obj,true);
        }
        
        public function restore_order_stock($order_id) {
                $order = new WC_Order( $order_id );
                if ( ! get_option('woocommerce_manage_stock') == 'yes' && ! sizeof( $order->get_items() ) > 0 ) {
                    return;
                }
                foreach ( $order->get_items() as $item ) {
                    if ( $item['product_id'] > 0 ) {
                        $_product = $order->get_product_from_item( $item );
                        if ( $_product && $_product->exists() && $_product->managing_stock() ) {
                            $old_stock = $_product->stock;
                            $qty = apply_filters( 'woocommerce_order_item_quantity', $item['qty'], $this, $item );
                            $new_quantity = $_product->increase_stock( $qty );
                            do_action( 'woocommerce_auto_stock_restored', $_product, $item );
                            $order->add_order_note( sprintf( __( 'Estoque do item #%s incrementado de %s para %s', 'woocommerce' ), $item['product_id'], $old_stock, $new_quantity) );
                            $order->send_stock_notifications( $_product, $new_quantity, $item['qty'] );
                        }
                    }
                }
        }
        
        public function tipo_par($a){
            if($a=='A'){
                return 'D&eacute;bito';
            }elseif($a=='2'){
                return 'Cr&eacute;dito sem Juros';
            }elseif($a=='3'){
                return 'Cr&eacute;dito com Juros';
            }elseif($a=='1'){
                return 'Cr&eacute;dito &agrave; vista';
            }
        }
        
        private function get_user_login() {
            global $user_login;
            get_currentuserinfo();
            return $user_login;
        }
    }    
?>