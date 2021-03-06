// Opções para o logo (imagens)
// algumas correções nas paginas feitas com JS
$(function(){
    
    $("#tmcp_select_5").change(()=>{        
        $("#tmcp_select_6").val("");
        $("#tmcp_select_7").val("");
        $("#tmcp_select_8").val("");
    })
    
    $(".woocommerce-shipping-calculator .shipping-calculator-button").html("<b>CONFIRME SEU CEP AQUI E CLIQUE EM <i>ATUALIZAR</i></b>");

    $(".wc-proceed-to-checkout .checkout-button").css({"display":"none"});
    $(".shipping-calculator-form button.button").click(function(){
        var destino = $(".shipping-calculator-form #calc_shipping_postcode_field .input-text").val();
        if(destino == ""){
            $(".wc-proceed-to-checkout .checkout-button").css({"display":"none"});
        }else{
            $(".wc-proceed-to-checkout .checkout-button").slideDown(500);
        }       
    })    

    if($(window).width() > 600) {
        $(".slide-home").css({'display':'block'});
        $(".slide-mobile").css({'display':'none'});
    }else{
        $(".slide-home").css({'display':'none'});
        $(".slide-mobile").css({'display':'block'});
    }

    $('.js-select-logo .tc-select-option').hide(); 
    
    $(".product-inner .price").html(function(i, html){
        return html.replace("–", "");
    });

    $(".product-inner .amount:nth-child(1)").html(function(i, html){
        return html.replace(/^/, "A partir de ");
    });

    $(".product-inner .amount:nth-child(2)").html("");
    
});

function produtos(){  
    $("#quadro").slideDown(500);
    $("#site").css({"opacity":"0.3"});
}
function fechar(){  
    $("#quadro").css({'display':'none'});
    $("#site").css({"opacity":"1"});
    $(".js-select-logo").css({'display':'block'})
}

// Ao clicar no campo de logos
$(document).on('click', '.js-select-logo', function(){
    $(this).css({'display':'none'})	
    produtos();    
});

// botao fechar 
$(document).on('click', '#quadro span', function(){	
    fechar();
});

// Ao selecionar uma logo
$(document).on('click', '.logo-bordado', function(){	
	var logoEscolhida = $(this).attr("data-value");
    $(".js-select-logo").val(logoEscolhida).trigger("change");
    $(".js-select-localizacao-div, .js-select-observacao-div").slideDown(500);
    if(logoEscolhida == "Sem logo_0"){
        $(".js-select-localizacao-div, .js-select-observacao-div").css({'display':'none'})
    }
	fechar();
});