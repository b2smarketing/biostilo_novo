<?php
if ( ! defined( 'ABSPATH' ) ) {
exit;
}
//se pf ou pj
$fiscal = '';
$customer_id = get_current_user_id();
if(get_user_meta( $customer_id, 'billing_cnpj', true )){
	$fiscal = get_user_meta( $customer_id, 'billing_cnpj', true );
}elseif(get_user_meta( $customer_id, 'billing_cpf', true )){
	$fiscal = get_user_meta( $customer_id, 'billing_cpf', true );
}
$fiscal = preg_replace('/\D/', '',$fiscal);
?>
<?php if($anti_fraude && !empty($oggid)) { ?>
<img style="display:none;" src="https://h.online-metrix.net/fp/clear.png?org_id=<?php echo $oggid;?>&session_id=cielo_webservice<?php echo $hash;?>">
<script src="https://h.online-metrix.net/fp/tags.js?org_id=<?php echo $oggid;?>&session_id=cielo_webservice<?php echo $hash;?>" type="text/javascript"></script>
<?php } ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/3.0.0/jquery.payment.min.js"></script>
<script>
jQuery( function( $ ) {
	//ativa mascaras cartao
	$( document ).on( 'click focus', '.mascaras_cartao_cielo_webservice', function() {
		$(this).payment('formatCardNumber');
	});
	//converte o nome do titular
	$( document ).on( 'keyup', '.nome_titular_cielo_webservice', function() {
		$(this).val(($(this).val()).toUpperCase());
	});
});
</script>
<script type="text/javascript" language="javascript">
//ajax acoes
var ajax_url_cielo_loja5 = "<?php echo admin_url('admin-ajax.php'); ?>";

//dados definidos
var url_cielo_webservice = '<?php echo plugins_url();?>';
var total_pedido_cielo = '<?php echo $total_cart;?>';
var hash_pedido_cielo = '<?php echo sha1(md5($total_cart));?>';

//funcoees
function detectar_bandeira_cartao_credito_cielo_loja5(numero){
	var result = '';
	var bin = (numero).replace(/\D/g,'');
	if (/^3[47][0-9]{13}/.test(bin)) {
		result = "amex";
	}else if(/^(4011(78|79)|43(1274|8935)|45(1416|7393|763(1|2))|50(4175|6699|67[0-7][0-9]|9000)|627780|63(6297|6368)|650(03([^4])|04([0-9])|05(0|1)|4(0[5-9]|3[0-9]|8[5-9]|9[0-9])|5([0-2][0-9]|3[0-8])|9([2-6][0-9]|7[0-8])|541|700|720|901)|651652|655000|655021)/.test(bin)){
		result = "elo";
	} else if (/^4[0-9]{12}(?:[0-9]{3})?$/.test(bin)) {
		result = "visa";
	} else if (/^3(?:0[0-5]|[68][0-9])[0-9]{11}/.test(bin)) {
		result = "diners";
	} else if(/^6(?:011|5[0-9]{2})[0-9]{12}/.test(bin)) {
		result = "discover";	
	} else if(/^(?:2131|1800|35\d{3})\d{11}/.test(bin)) {
		result = "jcb";	
	} else if(/^(50)\d+$/.test(bin)) {
		result = "aura";	
	} else if(/^5[1-5]|^2(2(2[1-9]|[3-9])|[3-6]|7([01]|20))/.test(bin)) {
		result = "mastercard";	
	} else if(/^(38[0-9]{17}|60[0-9]{14})$/.test(bin)) {
		result = "hiper";	
	}
	console.log(result);
	if(result!=''){
		aplicar_bandeira_cielo_webservice(result);
	}
}

function aplicar_bandeira_cielo_webservice(bandeira){
    jQuery(".meio_cielo_webservice_img").css({ opacity: 0.2 });
    jQuery("."+ bandeira ).css({ opacity: 1 });
    jQuery('#parcela-cielo-webservice-credito').html('<option value="">Aguarde...</option>');
    jQuery.post(ajax_url_cielo_loja5, {action : 'parcelas_cielo_webservice', id : bandeira,total: total_pedido_cielo,hash: hash_pedido_cielo }, retorno_parcelamento, 'JSON');
    jQuery('#bandeira-cielo-webservice').val(bandeira);
}

function retorno_parcelamento(data) {
    console.log(data);
    var items = '';
    jQuery.each(data, function(key, val) {
        items += '<option value="' + key + '">' + val + '</option>';
    });
    jQuery('#parcela-cielo-webservice-credito').html(items);
}
</script>

<div id="tela-cielo-webservice" style="width:100%;">

<p style="margin-bottom: 5px;">Selecione abaixo a bandeira qual deseja realizar o pagamento.</p>

<fieldset class="wc-credit-card-form wc-payment-form">

<p id="tela-bandeiras-cielo" class="form-row form-row-wide woocommerce-validated">
<span style="float:left;">
<?php 
foreach($this->meios AS $k=>$b){
?>
<img style="cursor:pointer;float:left;min-height:30px;" class='meio_cielo_webservice_img <?php echo $b;?>' onclick="aplicar_bandeira_cielo_webservice('<?php echo $b;?>')" src='<?php echo plugins_url().'/loja5-woo-cielo-webservice/images/'.$b.'.png';?>' width="50">
<?php 
}
?>
</span>
</p>

<input type="hidden" name="cielo_webservice[bandeira]" id="bandeira-cielo-webservice" value="">
<input type="hidden" name="cielo_webservice[hash]" value="<?php echo $hash;?>">

<p class="form-row form-row-wide woocommerce-validated campos_cielo_webservice">
<label style="padding: 5px 0 5px 5px;">Nome do titular:</label>
<input style="box-shadow: inset 2px 0 0 #0f834d;height:40px;" type="text" class="input-text nome_titular_cielo_webservice" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" placeholder="Nome como impresso no cart&atilde;o" name="cielo_webservice[titular]" value="">
</p>

<?php if(strlen($fiscal)==11 || strlen($fiscal)==14 || $anti_fraude==false){ ?>

<input type="hidden" id="fiscal-cielo-webservice" name="cielo_webservice[fiscal]" value="<?php echo $fiscal;?>">

<?php }else{ ?>

<p class="form-row form-row-wide woocommerce-validated campos_cielo_webservice">
<label style="padding: 5px 0 5px 5px;">CPF/CNPJ:</label>
<input style="box-shadow: inset 2px 0 0 #0f834d;height:40px;" onfocus="jQuery(this).mask('99999999999999')" type="text" class="input-text mascaras_campos_cielo_webservice" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" placeholder="CPF ou CNPJ" id="fiscal-cielo-webservice" name="cielo_webservice[fiscal]" value="<?php echo $fiscal;?>">
</p>

<?php } ?>

<p class="form-row form-row-wide woocommerce-validated campos_cielo_webservice">
<label style="padding: 5px 0 5px 5px;">N&uacute;mero:</label>
<input style="box-shadow: inset 2px 0 0 #0f834d;height:40px;" onblur="detectar_bandeira_cartao_credito_cielo_loja5(this.value)" type="text" class="input-text mascaras_cartao_cielo_webservice" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" placeholder="0000 0000 0000 0000" name="cielo_webservice[numero]" value="">
</p>

<p class="form-row form-row-first woocommerce-validated campos_cielo_webservice">
<label style="padding: 5px 0 5px 5px;">Validade (MM/AAAA):</label>
<input style="box-shadow: inset 2px 0 0 #0f834d;height:40px;" onfocus="jQuery(this).mask('99/9999')" type="text" id="validade-cielo-webservice" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="input-text mascaras_campos_cielo_webservice" placeholder="MM/AAAA" name="cielo_webservice[validade]" value="">
</p>

<p class="form-row form-row-last woocommerce-validated campos_cielo_webservice">
<label style="padding: 5px 0 5px 5px;">CVV:</label>
<input style="box-shadow: inset 2px 0 0 #0f834d;height:40px;" onfocus="jQuery(this).mask('9999')" type="text" id="cvv-cielo-webservice" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" class="input-text mascaras_campos_cielo_webservice" name="cielo_webservice[cvv]" placeholder="3 ou 4 digitos (amex)" value="">
</p>

<p class="form-row form-row-wide woocommerce-validated campos_cielo_webservice">
<label style="padding: 5px 0 5px 5px;">Parcela:</label>
<select style="box-shadow: inset 2px 0 0 #0f834d;height:40px;" name="cielo_webservice[parcela]" id="parcela-cielo-webservice-credito">
<option value="">Selecione uma bandeira...</option>
</select>
</p>

</fieldset>

</div>	