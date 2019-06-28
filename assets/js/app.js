// loads the jquery package from node_modules
import '../css/app.scss';
var $ = require('jquery');

require('bootstrap');
require('animate.css');
//require('hover.css');

$(document).ready(function() {
   // _bindClickCommandButton();

    $('#form').validate({
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
    });
});



var nbArticles = 1;
var mesCommandesArray = [];

var monPlatObject = {id: null, qte:0};
function _bindClickCommandButton(){

    $( ".commander" ).click(function() {
        var $inputQte = $(this).next();
        var idMet = $inputQte.val();
        var nbArticles = $inputQte.data('qte');
        monPlatObject.id = idMet;
        nbArticles =(mesCommandesArray.hasOwnProperty(idMet))?$inputQte.data('qte'):1;
        monPlatObject.qte = nbArticles;
        mesCommandesArray[idMet] = monPlatObject;

        console.log(mesCommandesArray);

        $('.badge.badge-danger').html(nbArticles);
    });

}



