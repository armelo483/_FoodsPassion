// loads the jquery package from node_modules
import '../css/app.scss';
//var $ = require('jquery');
require('bootstrap');
require ('loaders.css/loaders.css');
//require('animate.css');
//require('hover.css');

//notify



$(document).ready(function() {

    successClick();
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

    $("button[type='submit']").click(function(e) {
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

//var monPlatObject = {id: null, qte:0};
function _bindClickCommandButton(){
    nbArticlesSession = $('.badge.badge-danger').html();
    $( ".commander" ).click(function() {
        var $inputQte = $(this).next();
        var idMet = $inputQte.val();
        //var nbArticlesMet = $inputQte.data('qte');

        //nbArticles =(mesCommandesArray.hasOwnProperty(idMet))?$inputQte.data('qte'):1;

        mesCommandesArray.push(idMet);

        console.log(mesCommandesArray);
        __ajouterPanier(idMet, $(this));

    });

}

function __ajouterPanier(metId, $clickedButton){

    if(nbArticlesSession!=''){
        $('.badge.badge-danger').html(++nbArticlesSession);
    }else{
        $('.badge.badge-danger').html(++nbArticles);
    }

    $clickedButton.unbind();
    $clickedButton.show();

    $clickedButton.html('Déjà cliqué');
    $clickedButton.addClass('deja-clique');

    $clickedButton.parent().css('opacity', '0.3');
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