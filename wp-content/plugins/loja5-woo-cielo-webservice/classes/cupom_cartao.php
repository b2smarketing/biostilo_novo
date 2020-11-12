<?php 

if(isset($cielo['tid'])){

	$html = '<p>Sua transa&ccedil;&atilde;o refer&ecirc;nte ao pedido <b>#'.$order->get_order_number().'</b> foi processada junto a operadora.<br>

	A sua transa&ccedil;&atilde;o encontra-se <b>'.strtoupper($status).'</b>.<br><br>

	<b>TID:</b>  '.$cielo['tid'].'<br>

	<b>Bandeira:</b> '.ucfirst($cielo['bandeira']).' em '.$cielo['parcela'].'x<br>

	<b>BIN:</b>  '.$cielo['bin'].'<br>';

	if(isset($cielo['lr']) && !empty($cielo['lr'])){

		$html .= '<b>LR:</b>  '.$cielo['lr'].' - '.$cielo['lr_log'].'<br>';

	}

	$html .= '<b>ID Pagamento:</b>  '.$cielo['id_pagamento'].'<br><br>

    Caso tenha alguma d&uacute;vida referente a transa&ccedil;&atilde;o entre em contato com o atendimento da loja.</p>';
    
    if ($status_cielo == '3') {
        $html .= '<p class="text-center"><a class="button pay" href="' . esc_url( $order->get_checkout_payment_url() ) . '" class="button pay">Tentar Novamente</a>';
        if ( is_user_logged_in() ) :
            $html .='<a class="button pay" style="margin-left: 5px;" href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '" class="button pay">Minha Conta</a>';
        endif;
        $html .= '</p>';
        $html .= '<script>document.addEventListener("DOMContentLoaded",function(e){let t=document.querySelectorAll(".xlwcty_userN")[0],c=t.innerText.split(" ")[1],l=document.querySelectorAll(".xlwcty-fa.xlwcty-fa-check")[0];t.innerText=c+", algo deu errado",l.classList.remove("xlwcty-fa-check"),l.classList.add("xlwcty-fa-remove")});</script>';
    }

	echo wpautop(wptexturize($html));

}

?>