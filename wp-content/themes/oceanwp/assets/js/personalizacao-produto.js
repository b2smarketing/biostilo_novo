// Opções para o logo (imagens)
// algumas correções nas paginas feitas com JS
$(function(){
    $('.js-select-logo .tc-select-option').hide(); 
    
    $(".product-inner .price").html(function(i, html){
        return html.replace("–", "");
    });

    $(".product-inner .amount:nth-child(1)").html(function(i, html){
        return html.replace(/^/, "A partir de ");
    });

    $(".product-inner .amount:nth-child(2)").html("");

    $(".single-page-article .entry").html(function(i, html){
        return html.replace("[vc_row][vc_column][vc_column_text]", "");
    });

    $(".single-page-article .entry").html(function(i, html){
        return html.replace("[/vc_column_text][/vc_column][/vc_row]", "");
    });
    
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