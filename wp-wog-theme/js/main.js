jQuery(document).ready(function () {

    /* carousel generico */
    jQuery('.carousel-gen').owlCarousel({
        loop: true,
        autoplay: true,
        autoplayTimeout: 4000,
        autoplayHoverPause: true,
        margin: 0,
        dots: false,
        nav: true,
        responsiveClass: true,
        responsive: {
            0: {
                items: 1
            },
            768: {
                items: 1
            },
            990: {
                items: 2
            }
        }
    });


    /* toTop button */
    var btn = jQuery('#toTop');
    jQuery(window).scroll(function () {
        var offset = jQuery(document).scrollTop();
        var opacity = 0;
        if (offset <= 0) {
            opacity = 0;
        } else if (offset > 0 & offset <= 200) {
            opacity = (offset - 1) / 200;
        }
        else {
            opacity = 1;
        }
        jQuery('#toTop').css('opacity', opacity);
    });

    btn.on('click', function (e) {
        e.preventDefault();
        jQuery('html, body').animate({ scrollTop: 0 }, '300');
    });


    /* simulación validación bases legales */
    jQuery(".trans").on("click", function () {
        jQuery(".legal").addClass("error");
    });

    jQuery("#aceptarbases").on('click', function () {
        if (jQuery(this).is(':checked')) {
            jQuery(".trans").hide();
        } else {
            jQuery(".trans").show();
        }
        jQuery(".legal").removeClass("error");
    });
    
    jQuery(".alertacurso i").on('click', function () {
        jQuery(".alertacurso").fadeOut();
    });

    //Botón redimir código QR
    jQuery('a.btn-redeem').on('click', function(){  
      jQuery(".alertacurso").css('display', 'none');
      if (jQuery("#qr-code").val()){
         var txt_link=jQuery(this).text();
         jQuery(this).html("Enviando...").css({
                "cursor": "wait",
                "pointer-events": "none"
            });
         jQuery.ajax({
             type : "post",
             context: this,
             url : '/wp-admin/admin-ajax.php',
             data : {
                 action: "exchange_click", 
                 course:jQuery(this).data('value'),
                 qr:jQuery("#qr-code").val(),
                 state:0
             },
             error: function(response){
                 console.log(response);
             },
             success: function(response) {
                 // Actualiza el mensaje con la respuesta
                 if (response.state=='1'){
                    jQuery(".alertacurso em").html("Descuento canjeado");
                    dataLayer.push({'event': 'druidEvent',
                        'druidCategory': 'Disccount',
                        'druidAction': 'Redeemed',
                        'druidLabel': 'Descuento curso iniciacion'});
                }
                 else if (response.state=='2')jQuery(".alertacurso em").html("Código ya redimido anteriormente");
                 else if (response.state=='-1') jQuery(".alertacurso em").html("Código NO válido");
                 jQuery(".alertacurso").css('display', 'block');
             },
             complete:function(){jQuery(this).html(txt_link).css({
                "cursor": "pointer",
                "pointer-events": "auto"
             });}
         })
      }
      else
        jQuery("#qr-code").focus();
    return false;
   });
    // FIN llamada redimir coódigo QR


});


/* controlar menu sticky */
function interiorShop(nVar) {

    if (nVar == 0) {
        var winW = jQuery(window).width();

        jQuery(window).scroll(function () {
            var scroll = jQuery(window).scrollTop();
            if (winW > 1024) {
                if (scroll >= 90) {
                    jQuery("#masthead").addClass('shrink');
                } else {
                    jQuery("#masthead").removeClass("shrink");
                }
            }
        });

        if (winW <= 1024) {
            jQuery("#masthead").addClass('shrink');
        } else {
            jQuery("#masthead").removeClass("shrink");
        }
    } else if (nVar == 1) {
        jQuery("#masthead").addClass('shrink');
    }

}


