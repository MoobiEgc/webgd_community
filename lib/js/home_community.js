var CFG_COMMUNITY = window.location.origin + '/moodle/blocks/webgd_community/';
var CFG_COMMUNITY_GLOSSARY = CFG_COMMUNITY + 'menus/glossary/';


$(document).ready(function () {



    $('#menu-tab li').click(function () {
        $('#menu-tab li').removeClass('active');
        $(this).addClass('active');
        $('.tab-glossary').addClass('tab-hide');
        $('#' + $(this).html()).removeClass('tab-hide');
    });

    function atualizaTimeline() {
        if ($('#ultimo_post').val() != null && $('#comunidade_id').val() != null) {
            $.post(CFG_COMMUNITY + 'atualiza_timeline.php', {ultimo_post: $('#ultimo_post').val(), id_comunidade: $('#comunidade_id').val()}, function (x) {
                if (x.atualizar == '1') {
                    $('#ultimo_post').val(x.ultimo_post);
                    $('#posts').prepend(x.mensagem);
                    //Falta montar a mensagem dentro da função atualiza Timeline e após dar um append do x.mensagem na div da timeline
                }
            }, 'jSON');
        } else {
            // ou nao ha comunidade, ou nao tem como saber o ultimo post, neste caso nao faz nada, pois se nao da erro em campos como o menu arquivo
        }
    }

    setInterval(function () {
        atualizaTimeline();
    }, 2000);

    $('.estrelaGlossario').on('click', function () {
        var dados = $(this).attr('rel');
        var aux = dados.split('_');
        var valorVoto = aux[0];
        var glossario = aux[1];
        $.post(CFG_COMMUNITY_GLOSSARY + 'saveVotation.php', {idGlossario: glossario, votacao: valorVoto}, function (x) {
            pinta_estrelas(x, $('#vot_' + glossario));
            valorVoto = valorVoto / 10;
            $('#span_votacao_' + glossario).html(valorVoto);
            alert("Obrigado pela sua avaliação!");

        });
        return false;
    });

    $('.like').on('click', function () {

        var dados = $(this).attr('rel');
        var aux = dados.split('_');
        var valorVoto = aux[0];
        var post = aux[1];
        $.post(CFG_COMMUNITY + 'saveLikeDislike.php', {idPost: post, votacao: valorVoto}, function (x) {
            var aux = x.split('_');
            $('#likes_' + post).html(aux[0]);
            $('#dislikes_' + post).html(aux[1]);
            alert('O seu voto foi computado. Obrigado!');
        });
        return false;
    });

});

function pinta_estrelas(valor, glossario) {
    var endOld = glossario[0].children[0].getAttribute("src");
    var staron = endOld.replace("star-off", "star-on");
    var staroff = endOld.replace("star-on", "star-off");
    glossario[0].children[0].setAttribute("src", staroff);
    glossario[0].children[1].setAttribute("src", staroff);
    glossario[0].children[2].setAttribute("src", staroff);
    glossario[0].children[3].setAttribute("src", staroff);
    glossario[0].children[4].setAttribute("src", staroff);
    if (valor >= 1) {
        glossario[0].children[0].setAttribute("src", staron);
    }
    if (valor >= 2) {
        glossario[0].children[1].setAttribute("src", staron);
    }
    if (valor >= 3) {
        glossario[0].children[2].setAttribute("src", staron);
    }
    if (valor >= 4) {
        glossario[0].children[3].setAttribute("src", staron);
    }
    if (valor == 5) {
        glossario[0].children[4].setAttribute("src", staron);
    }
    return false;
}
;

function open_participantes() {
    $('#modal_participantes').toggle();
    return false;
}
;

function fechar_participantes() {
    $('#modal_participantes').hide();
    return false;
}
;

function salvar() {
    $.ajax({
        type: 'POST',
        data: $("#form").serialize(),
        url: 'savePost.php',
        success: function (retorno) {
            //$("#posts").html(retorno);
            //$("#status_message").val('');
            location.reload();
        }
    });
}
;

function camera() {
    $(function () {
        $.ajax({
            type: 'POST',
            url: 'lib/cam/index.php',
            success: function (retorno) {
                $("#camera").html(retorno);
            }
        });
    });

}

function enviaComentario(data) {
    $.ajax({
        type: 'POST',
        data: $("#form-comment-" + data).serialize(),
        url: 'savePostComment.php',
        success: function (retorno) {
            //$("#posts").html(retorno);
            //$("#status_message").val('');
            location.reload();
        }
    });
    return false;
}
