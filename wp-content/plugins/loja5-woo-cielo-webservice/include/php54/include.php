<?php
function detectar_bandeira_cartao_loja5($bandeira,$tipo_parcela){
	$bandeira = strtoupper($bandeira);
	switch($bandeira){
		//Indicador de autoriza��o:  
		//0 � N�o autorizar (somente autenticar). 
		//1 � Autorizar somente se autenticada. 
		//2 � Autorizar autenticada e n�o autenticada. 
		//3 � Autorizar sem passar por autentica��o (somente para cr�dito) � tamb�m conhecida como Autoriza��o Direta. 
		//Obs.: Para Diners, Discover, Elo, Amex, Aura e JCB o valor ser� sempre 3. 
		case 'VISA':
		$op = 'visa';
		$autorizar = 3;
		$parcela = $tipo_parcela;
		break;
		case 'VISAELECTRON':
		$op = 'visa';
		$autorizar = 2;
		$parcela = 'A';
		break;
		case 'ELODEBITO':
		$op = 'elo';
		$autorizar = 2;
		$parcela = 'A';
		break;
		case 'MASTERCARD':
		$op = 'mastercard';
		$autorizar = 3;
		$parcela = $tipo_parcela;
		break;
		case 'MAESTRO':
		$op = 'mastercard';
		$autorizar = 2;
		$parcela = 'A';
		break;
		case 'ELO':
		$op = 'elo';
		$autorizar = 3;
		$parcela = $tipo_parcela;
		break;
		case 'DINERS':
		$op = 'diners';
		$autorizar = 3;
		$parcela = $tipo_parcela;
		break;
		case 'DISCOVER':
		$op = 'discover';
		$autorizar = 3;
		$parcela = $tipo_parcela;
		break;
		case 'AMEX':
		$op = 'amex';
		$autorizar = 3;
		$parcela = $tipo_parcela;
		break;
		case 'AURA':
		$op = 'aura';
		$autorizar = 3;
		$parcela = $tipo_parcela;
		break;
		case 'JCB':
		$op = 'jcb';
		$autorizar = 3;
		$parcela = $tipo_parcela;
		break;
		case 'HIPER':
		case 'HIPERCARD':
		$op = 'hipercard';
		$autorizar = 3;
		$parcela = $tipo_parcela;
		break;
	}
	return array('cc'=>$op,'au'=>$autorizar,'tp'=>$parcela);
}

function get_ip_cielo_webservice() {
    $variables = array('REMOTE_ADDR',
                       'HTTP_X_FORWARDED_FOR',
                       'HTTP_X_FORWARDED',
                       'HTTP_FORWARDED_FOR',
                       'HTTP_FORWARDED',
                       'HTTP_X_COMING_FROM',
                       'HTTP_COMING_FROM',
                       'HTTP_CLIENT_IP');

    $return = 'Unknown';
    foreach ($variables as $variable)
    {
        if (isset($_SERVER[$variable]))
        {
            $return = $_SERVER[$variable];
            break;
        }
    }
    return $return;
}
?>