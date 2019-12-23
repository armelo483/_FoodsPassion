// loads the jquery package from node_modules
import '../css/app.scss';

//var $ = require('jquery');
require('bootstrap');
require ('loaders.css/loaders.css');
//require('animate.css');
//require('hover.css');

//notify



$(document).ready(function() {

    sessionStorage.setItem('BAEAnim', '1');
    $(".fa-search").click(function() {
        $(".search-box").toggle();
        $("input[type='text']").focus();
    });



    /*$( '#login-dp' ).on( 'showRemoved', function() {
        var $element = $( this );
        var timer = setInterval( function() {
            if( $element.hasClass( 'show' ) ) {
                //);$element.removeClass('fadeInDown'
               // $element.addClass('lightSpeedOut ');
            }
        }, 10000 );
    }).trigger( 'showRemoved' );

    $('#login-dp').click(function(){
        $(this).removeClass('fadeInDown');
        $(this).addClass('lightSpeedOut');
    });*/

    if(typeof(notificationBAE) != "undefined" && notificationBAE !== null){
        setTimeout(notificationBAE, 15000);
    }



    var btn = $('.backToTop');

    $(window).scroll(function() {
        if ($(window).scrollTop() > 300) {
            btn.addClass('show');
        } else {
            btn.removeClass('show');
        }
    });

    btn.on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({scrollTop:0}, '300');
    });

    if(typeof(notificationBienvenue) != "undefined" && notificationBienvenue !== null){
        setTimeout(notificationBienvenue, 41000);
    }

    /*$('#login-dp').on('hide.bs.dropdown', function(){
        //$(this).find('.dropdown-menu.animated').first().addClass('fadeOutUp').removeClass('fadeInDown');
        alert('test');
    });*/

    //$('.main').show();
    /**
     * City Cometic text animation
     */
    let spans = document.querySelectorAll('.word span');
    spans.forEach((span, idx) => {
        // Initial animation
        setTimeout(() => {
            span.classList.add('active');
        }, 750 * (idx+1))
    });
    //animation end
    _bindClickCommandButton();
    __onChangeInput();

    $("form").submit(function(){
        $('.main').show();
    });

    $(".list-group-item").click(function(e) {
        if($(this).hasClass('active')){
            $(this).removeClass('active');
        }else{
            $(this).addClass('active');
        }

    });
    $("input[type=number]").click(function(e) {

        $(this).attr( 'value', $(this).val() );

    });

    $(".close").click(function(){
        $('.alert-success').addClass('alert-success_out');
        $('.alert-danger').addClass('alert-success_out');
    });


    /*
    * Confirmation Paiement
    * */

    var time = 800;
    $('.testLazyloading').each(function() {
        var $that = $(this);

        setTimeout( function(){ $that.addClass('animateClass'); }, time)
        time += 1800;
    });

    /*$('#form').validate({
        rules: {
            field: {
                // 必須
                required: true,
                // 数値のみ
                digits: true,
                // 最大文字数
                maxlength: 9,
                // 最小値
                min: 1
            }
        }
    });*/
});



var nbArticles = 0;
var nbArticlesSession = 0;
var mesCommandesArray = [];
var $alreadyClicked = '';

//var monPlatObject = {id: null, qte:0};
function _bindClickCommandButton(){
    nbArticlesSession = $('.badge.badge-panier').html();
    $( ".commander" ).click(function() {
        var $inputQte = $(this).next();
        var $overLay = $(this).parent().find('.overlay');
        /*var $taillesDispo = $(this).parent().find('.taille');
        var $reduction = $(this).parent().find('.reduction');
        var $article = $(this).parent().find('.article');
        $taillesDispo.show();
        $reduction.hide();
        $article.hide();*/
        var idMet = $inputQte.val();
        $alreadyClicked = $(this).next().next();
        console.log($alreadyClicked);
        //var nbArticlesMet = $inputQte.data('qte');

        //nbArticles =(mesCommandesArray.hasOwnProperty(idMet))?$inputQte.data('qte'):1;

        mesCommandesArray.push(idMet);

        console.log(mesCommandesArray);
        __ajouterPanier(idMet, $(this), $alreadyClicked,$overLay);

    });

}

function __ajouterPanier(metId, $clickedButton, $alreadyClicked, $overLay){

    if($alreadyClicked.val() == '0'){
        $clickedButton.css('display','block');
        $clickedButton.html('<i class="fa fa-times fa-w-11 fa-spin fa-lg"></i> Retirez l\'article! ');
        $clickedButton.addClass('deja-clique');
        $alreadyClicked.val(1);
        $overLay.css('display', 'block');
        if(nbArticlesSession!=''){
            $('.badge.badge-panier').html(++nbArticlesSession);
        }else{
            $('.badge.badge-panier').html(++nbArticles);
        }
        addItemPanier(metId);

    }else{
        $clickedButton.removeClass('deja-clique');
        $clickedButton.removeAttr('style');
        $overLay.css('display', 'none');
        $clickedButton.html('<span>Ajouter au panier</span>');

        if(nbArticlesSession!=''){

            $('.badge.badge-panier').html(--nbArticlesSession);
        }else{
            $('.badge.badge-panier').html(--nbArticles);
        }

        $alreadyClicked.val(0);
        removeItemPanier(metId);

    }

}

function removeItemPanier(metId){
    //$clickedButton.parent().css('opacity', '0.3');
    $.ajax({
        type: "GET",
        url: "ecomm/remove",
        data: {"metId": metId},
        async: true,

    })
        .done(function( data ) {
            $('#shoppingLink').attr('data-toggle','dropdown');

            console.log(data);
        });
}


function animateCSS(element, animationName, callback) {
    const node = document.querySelector(element)
    node.classList.add('animated', animationName)

    function handleAnimationEnd() {
        node.classList.remove('animated', animationName)
        node.removeEventListener('animationend', handleAnimationEnd)

        if (typeof callback === 'function') callback()
    }

    node.addEventListener('animationend', handleAnimationEnd)
}

function addItemPanier(metId){

    //$clickedButton.parent().css('opacity', '0.3');
    $.ajax({
        type: "GET",
        url: "ecomm/ajouter",
        data: {"metId": metId},
        async: true,

    })
        .done(function( data ) {
            $('#shoppingLink').attr('data-toggle','dropdown');

            console.log(data);
        });
}
function __onChangeInput(){

    $('.qte-input').change(function(){
        var newQte = parseInt($(this).val());
        var idPrix = 'prix_'+$(this).attr('id').split('_')[1];
        var prixUnitaire = parseFloat($('#'+idPrix).data('prix'));
        var newPrix = parseFloat($('#'+idPrix).html().split('#')[0]);

        $('#'+idPrix).html(prixUnitaire*newQte+'€');

        var prixTotal = parseFloat(0);
        var QteTotal = parseFloat(0);
        $( ".prix-qte" ).each(function() {
            //var idPrix = 'prix'+$qteInput.attr('id').split('_')[1];

            var newPrixOther = parseFloat($(this).find('td span.prix').html().split('€')[0]);
            var QteOther = parseFloat($(this).find('.qte-input').val());

            QteTotal += QteOther;
            prixTotal += newPrixOther;


        });

        $('#prixTotal').html(prixTotal+'€');
        $('#qteTotale').html(QteTotal);
    });


}

$('.add-comment').click(function(){
    $("html, body").animate({ scrollTop: $(document).height() }, 1800);
    setTimeout(function(){ $('.new-comment').addClass('fadeInLeft slower'); }, 1000);

});
