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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>
<div id="tela-cielo-webservice-boleto" style="width:100%;">

<p style="margin-bottom: 5px;">Conclua seu pagamento via Boleto Banc&aacute;rio e lembre-se de pagar o mesmo ao finalizar o pedido na loja.</p>

<fieldset class="wc-credit-card-form wc-payment-form">

<?php if(strlen($fiscal)==11 || strlen($fiscal)==14){ ?>

<input type="hidden" id="fiscal-cielo-webservice" name="cielo_webservice_boleto[fiscal]" value="<?php echo $fiscal;?>">

<?php }else{ ?>

<p class="form-row form-row-wide woocommerce-validated campos_cielo_webservice">
<label style="padding: 5px 0 5px 5px;">CPF/CNPJ:</label>
<input style="box-shadow: inset 2px 0 0 #0f834d;height:40px;" onfocus="jQuery(this).mask('99999999999999')" type="text" class="input-text mascaras_campos_cielo_webservice" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" placeholder="CPF ou CNPJ" id="fiscal-cielo-webservice-boleto" name="cielo_webservice_boleto[fiscal]" value="<?php echo $fiscal;?>">
</p>

<?php } ?>

</fieldset>

</div>	