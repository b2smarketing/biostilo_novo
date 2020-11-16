// Opções para o logo (imagens)
$(function(){
<<<<<<< HEAD
	$('.js-select-logo .tc-select-option').hide();	
=======
    $('.js-select-logo .tc-select-option').hide(); 
    
    $(".product-inner .price").html(function(i, html){
        return html.replace("–", "");
    });

    $(".product-inner .amount:nth-child(2)").html("");
    
>>>>>>> db70b13cdd0b1e53eb6c139980c125fcd7613d9b
});

function produtos(){  
    $("#quadro").slideDown(500);
    $("#site").css({"opacity":"0.3"});
}
function fechar(){  
    $("#quadro").css({'display':'none'});
    $("#site").css({"opacity":"1"});
<<<<<<< HEAD
=======
    $(".js-select-logo").css({'display':'block'})
>>>>>>> db70b13cdd0b1e53eb6c139980c125fcd7613d9b
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
<<<<<<< HEAD
$(document).on('click', '.logo-bordado', function(){
    $(".js-select-logo").css({'display':'block'})	
	var logoEscolhida = $(this).attr("data-value");
    $(".js-select-logo").val(logoEscolhida).trigger("change");   
=======
$(document).on('click', '.logo-bordado', function(){	
	var logoEscolhida = $(this).attr("data-value");
    $(".js-select-logo").val(logoEscolhida).trigger("change");
    $(".js-select-localizacao-div, .js-select-observacao-div").slideDown(500);
    if(logoEscolhida == "Sem logo_0"){
        $(".js-select-localizacao-div, .js-select-observacao-div").css({'display':'none'})
    }
>>>>>>> db70b13cdd0b1e53eb6c139980c125fcd7613d9b
	fechar();
});