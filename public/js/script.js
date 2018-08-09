/*!
 * Author: Lucas R. Brelaz
 * URL Author: www.lucasbrelaz.com
 * Date: September/2017
 * Project: Webvendas by FlexPeak Tecnologia
 */
$(function () {
  $('[data-toggle="tooltip"]').tooltip({
  	container: 'body'
  })
})
$(document).ready(function(){
	/* Ancora home */
	$(document).on('click', 'a.scroll', function(event){
		event.preventDefault();
		$('html, body').animate({
			scrollTop: $( $.attr(this, 'href') ).offset().top
		}, 500);
	});

	/* Contratar plano */
	var cont_dependente = 0;

	function criarCardContratarPlano(tipo, indice) {
		var cont_dependente = indice;
		var contratante = '';
		if (tipo == "contratante") {
			contratante = "bg-primary-green1";
		}
		var cardHTML = "";
		cardHTML += '<div class="card text-light '+tipo+' '+contratante+'">';
		cardHTML +=   '<div class="title">';
		cardHTML +=       '<h3>Informações do '+tipo+'</h3>';
		if (tipo != "contratante") {
			cardHTML +=       '<span class="fa fa-fw fa-close close_button"></span>';
		}
		cardHTML +=   '</div>';
		cardHTML += '<div class="content">';
		cardHTML +=   '<div class="row">';
		cardHTML +=       '<div class="col-md-6 col-sm-12">';
		cardHTML +=           '<div class="form-group">';
		cardHTML +=               '<label for="nome_'+tipo+''+cont_dependente+'">Nome</label>';
		cardHTML +=               '<input required class="form-control" type="text" name="nome_'+tipo+'[]" id="'+tipo+'_dependente'+cont_dependente+'">';
		cardHTML +=           '</div>';
		cardHTML +=       '</div>';
		cardHTML +=       '<div class="col-md-6 col-sm-12">';
		cardHTML +=           '<div class="form-group">';
		cardHTML +=               '<label for="nascimento_'+tipo+''+cont_dependente+'">Data de nascimento</label>';
        cardHTML +=               '<input required class="form-control isDate" type="text" name="nascimento_'+tipo+'[]" id="nascimento_'+tipo+''+cont_dependente+'">';
		cardHTML +=           '</div>';
		cardHTML +=       '</div>'; /*end col*/
		cardHTML +=       '</div>'; /*end row*/
		cardHTML += '</div>'; /*end content*/

		return cardHTML;
	}

	$("#add_dependente").on('click', function(event) {
		event.preventDefault();

		var dependenteHTML = criarCardContratarPlano("dependente", cont_dependente);

		$("#card_dependentes").removeClass("hidden");
		$("#card_dependentes").append(dependenteHTML).hide().fadeIn(300);
		cont_dependente++;

        $('.isDate').datepicker({
            language: "pt-BR",
            endDate: "today",
            autoclose: true,
            todayHighlight: true
        });
	});

	$(document).on('click', '.close_button', function() {
		$(this).parent().parent().fadeOut(300, function() {
			$(this).remove();
		});
	});

	$("input[name=contratante_titular]").change(function() {
		if($('input[name=contratante_titular]:checked').val() == "no") {
			var contratanteHTML = criarCardContratarPlano("contratante", null);
			$("#card_contratante").removeClass("hidden");
			$("#card_contratante").append(contratanteHTML).hide().fadeIn(300);
		}
	});

	$("input[name=contratante_titular]").change(function() {
		if($('input[name=contratante_titular]:checked').val() == "yes") {
			$("#card_contratante").fadeOut(300, function() {
				$(this).addClass("hidden");
				$(".card.contratante").remove();
			});
		}
	});
	/* END / Contratar plano */

	/* Planos */
	$(".btn_more_info").click(function() {
		$(this).parent().parent().children(".more_info").toggle(function() {
			$(this).toggleClass("show");
		});
	});
	/* END / Planos */

    /* Previne letras no campo com a clase isNumber */
    $(".isNumber").keydown(function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            (e.keyCode >= 35 && e.keyCode <= 40)) {
                 return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    /* Coloca calendário nos campos com a classe isDate, desde que ela não esteja readonly */
    $('.isDate:not([readonly])').datepicker({
        language: "pt-BR",
        autoclose: true,
        todayHighlight: true,
        endDate: "today"
    });

    /* Coloca mascara e desativa o autocomplete dos campos com a classe isDate */
    $('.isDate').mask('00/00/0000');
    $('.isDate').attr('autocomplete', 'off');

    $("#ufnaturalidade_titular").change(function() {
        $('#naturalidade_titular').html("<option value=''>Selecione</option>");
        var request = $.ajax({
            url     : "/ajax/municipios",
            method  : "POST",
            data    : {
                uf      : $("#ufnaturalidade_titular").val(),
                _token  : $("#_token").val()
            }
        });

        request.done(function(retorno) {
            $.each(retorno, function(key, value) {
                $('#naturalidade_titular').append('<option value="'+key+'">'+value+'</option>');
            });
        });
    });

    $("select[name='dados_titular[uf_residencia]']").change(function() {
        $("select[name='dados_titular[municipio_residencia]']").html("<option value=''>Selecione</option>");
        var request = $.ajax({
            url     : "/ajax/municipios",
            method  : "POST",
            data    : {
                uf      : $("select[name='dados_titular[uf_residencia]']").val(),
                _token  : $("#_token").val()
            }
        });

        request.done(function(retorno) {
            $.each(retorno, function(key, value) {
                $("select[name='dados_titular[municipio_residencia]']").append('<option value="'+key+'">'+value+'</option>');
            });
        });
    });

    $("#cep_residencia").change(function() {
        var request = $.ajax({
            url     : "/ajax/buscacep",
            method  : "POST",
            data    : {
                cep     : $("#cep_residencia").val(),
                _token  : $("#_token").val()
            }
        });

        request.done(function(retorno) {
            if (retorno == "") {
                alert('CEP não encontrado');
            } else {
                $("select[name='dados_titular[uf_residencia]']").val(retorno[0].NUP_EST_SIGLA);
                $("select[name='dados_titular[municipio_residencia]']").val(retorno[0].NUP_CODIGO_MUNICIPIO);
                $("input[name='dados_titular[bairro_residencia]']").val(retorno[0].BRR_NOME);
                $("select[name='dados_titular[tiplogradouro_residencia]']").val(retorno[0].TPL_CODIGO_TIPO);
                $("input[name='dados_titular[logradouro_residencia]']").val(retorno[0].NML_NOME);
                $("select[name='dados_titular[municipio_residencia]']").html("<option value=''>Selecione</option>");
                $("select[name='dados_titular[municipio_residencia]']").append('<option value="'+retorno[0].NUP_CODIGO_MUNICIPIO+'">'+retorno[0].NUP_NOME+'</option>');
                $("select[name='dados_titular[municipio_residencia]']").val(retorno[0].NUP_CODIGO_MUNICIPIO);
            }
        });
    });

    $("select[name='dados_titular[uf_correspondencia]']").change(function() {
        $("select[name='dados_titular[municipio_correspondencia]']").html("<option value=''>Selecione</option>");
        var request = $.ajax({
            url     : "/ajax/municipios",
            method  : "POST",
            data    : {
                uf      : $("select[name='dados_titular[uf_correspondencia]']").val(),
                _token  : $("#_token").val()
            }
        });

        request.done(function(retorno) {
            $.each(retorno, function(key, value) {
                $("select[name='dados_titular[municipio_correspondencia]']").append('<option value="'+key+'">'+value+'</option>');
            });
        });
    });

    $("#cep_correspondencia").change(function() {
        var request = $.ajax({
            url     : "/ajax/buscacep",
            method  : "POST",
            data    : {
                cep     : $("#cep_correspondencia").val(),
                _token  : $("#_token").val()
            }
        });

        request.done(function(retorno) {
            if (retorno == "") {
                alert("CEP não encontrado.");
            } else {
                $("select[name='dados_titular[uf_correspondencia]']").val(retorno[0].NUP_EST_SIGLA);
                $("select[name='dados_titular[municipio_correspondencia]']").val(retorno[0].NUP_CODIGO_MUNICIPIO);
                $("input[name='dados_titular[bairro_correspondencia]']").val(retorno[0].BRR_NOME);
                $("select[name='dados_titular[tiplogradouro_correspondencia]']").val(retorno[0].TPL_CODIGO_TIPO);
                $("input[name='dados_titular[logradouro_correspondencia]']").val(retorno[0].NML_NOME);
                $("select[name='dados_titular[municipio_correspondencia]']").html("<option value=''>Selecione</option>");
                $("select[name='dados_titular[municipio_correspondencia]']").append('<option value="'+retorno[0].NUP_CODIGO_MUNICIPIO+'">'+retorno[0].NUP_NOME+'</option>');
                $("select[name='dados_titular[municipio_correspondencia]']").val(retorno[0].NUP_CODIGO_MUNICIPIO);
                $("input[name='dados_titular[bairro_correspondencia]']").attr('readonly', true);
                $("input[name='dados_titular[logradouro_correspondencia]']").attr('readonly', true);
            }
        });
    });

    $("input[name='dados_titular[mesmo_endereco]']").click(function() {
        /* Caso o endereço de correspondencia do titular não seja o mesmo que o endereço residencial (valor 0), habilitar a div de preenchimento do endereço de correspondência e colocar nos devidos campos a obrigatoriedade */
        if (this.value == "0") {
            $("#titular_correspondencia").attr("style", "display: block !important");
            $("input[name='dados_titular[cep_correspondencia]']").prop('required',true);
            $("select[name='dados_titular[uf_correspondencia]']").prop('required',true);
            $("select[name='dados_titular[municipio_correspondencia]']").prop('required',true);
            $("input[name='dados_titular[bairro_correspondencia]']").prop('required',true);
            $("select[name='dados_titular[tiplogradouro_correspondencia]']").prop('required',true);
            $("input[name='dados_titular[logradouro_correspondencia]']").prop('required',true);
            $("input[name='dados_titular[numero_correspondencia]']").prop('required',true);
        } else {
            $("#titular_correspondencia").attr("style", "display: none !important");
            $("input[name='dados_titular[cep_correspondencia]']").removeAttr('required');
            $("select[name='dados_titular[uf_correspondencia]']").removeAttr('required');
            $("select[name='dados_titular[municipio_correspondencia]']").removeAttr('required');
            $("input[name='dados_titular[bairro_correspondencia]']").removeAttr('required');
            $("select[name='dados_titular[tiplogradouro_correspondencia]']").removeAttr('required');
            $("input[name='dados_titular[logradouro_correspondencia]']").removeAttr('required');
            $("input[name='dados_titular[numero_correspondencia]']").removeAttr('required');
        }
    });

    $("input[data-seletor='dependente_mesmo_endereco']").click(function() {
        var codigo = $(this).data('codigo');
        var div = ($(".endereco_residencia[data-codigo='"+codigo+"']"));
        if (this.value == "0") {
            div.attr("style", "display: block !important");
            $("input[name='dados_dependentes["+codigo+"][cep_residencia]']").prop('required',true);
            $("select[name='dados_dependentes["+codigo+"][uf_residencia]']").prop('required',true);
            $("select[name='dados_dependentes["+codigo+"][municipio_residencia]']").prop('required',true);
            $("input[name='dados_dependentes["+codigo+"][bairro_residencia]']").prop('required',true);
            $("select[name='dados_dependentes["+codigo+"][tiplogradouro_residencia]']").prop('required',true);
            $("input[name='dados_dependentes["+codigo+"][logradouro_residencia]']").prop('required',true);
            $("input[name='dados_dependentes["+codigo+"][numero_residencia]']").prop('required',true);
        } else {
            div.attr("style", "display: none !important");
            $("input[name='dados_dependentes["+codigo+"][cep_residencia]']").removeAttr('required');
            $("select[name='dados_dependentes["+codigo+"][uf_residencia]']").removeAttr('required');
            $("select[name='dados_dependentes["+codigo+"][municipio_residencia]']").removeAttr('required');
            $("input[name='dados_dependentes["+codigo+"][bairro_residencia]']").removeAttr('required');
            $("select[name='dados_dependentes["+codigo+"][tiplogradouro_residencia]']").removeAttr('required');
            $("input[name='dados_dependentes["+codigo+"][logradouro_residencia]']").removeAttr('required');
            $("input[name='dados_dependentes["+codigo+"][numero_residencia]']").removeAttr('required');
        }
    });

    $(".isCEP").change(function() {
        var codigo = $(this).data('codigo');
        var request = $.ajax({
            url     : "/ajax/buscacep",
            method  : "POST",
            data    : {
                cep     : $(this).val(),
                _token  : $("#_token").val()
            }
        });

        request.done(function(retorno) {
            if (retorno == "") {
                alert('CEP não encontrado');
            } else {
                $("select[name='dados_dependentes["+codigo+"][uf_residencia]']").val(retorno[0].NUP_EST_SIGLA);
                $("select[name='dados_dependentes["+codigo+"][municipio_residencia]']").val(retorno[0].NUP_CODIGO_MUNICIPIO);
                $("input[name='dados_dependentes["+codigo+"][bairro_residencia]']").val(retorno[0].BRR_NOME);
                $("select[name='dados_dependentes["+codigo+"][tiplogradouro_residencia]']").val(retorno[0].TPL_CODIGO_TIPO);
                $("input[name='dados_dependentes["+codigo+"][logradouro_residencia]']").val(retorno[0].NML_NOME);
                $("select[name='dados_dependentes["+codigo+"][municipio_residencia]']").html("<option value=''>Selecione</option>");
                $("select[name='dados_dependentes["+codigo+"][municipio_residencia]']").append('<option value="'+retorno[0].NUP_CODIGO_MUNICIPIO+'">'+retorno[0].NUP_NOME+'</option>');
                $("select[name='dados_dependentes["+codigo+"][municipio_residencia]']").val(retorno[0].NUP_CODIGO_MUNICIPIO);
                $("input[name='dados_dependentes["+codigo+"][bairro_residencia]']").attr('readonly', true);
                $("input[name='dados_dependentes["+codigo+"][logradouro_residencia]']").attr('readonly', true);
            }
        });
    });

    $(".isUF").change(function() {
        var codigo = $(this).data('codigo');
        $("select[name='dados_dependentes["+codigo+"][municipio_residencia]']").html("<option value=''>Selecione</option>");
        var request = $.ajax({
            url     : "/ajax/municipios",
            method  : "POST",
            data    : {
                uf      : $(this).val(),
                _token  : $("#_token").val()
            }
        });

        request.done(function(retorno) {
            $.each(retorno, function(key, value) {
                $("select[name='dados_dependentes["+codigo+"][municipio_residencia]']").append('<option value="'+key+'">'+value+'</option>');
            });
        });
    });

    $("select[data-seletor='ufnaturalidade_dependente']").change(function() {
        var codigo = $(this).data('codigo');
        $("select[name='dados_dependentes["+codigo+"][naturalidade_dependente]']").html("<option value=''>Selecione</option>");
        var request = $.ajax({
            url     : "/ajax/municipios",
            method  : "POST",
            data    : {
                uf      : $("select[name='dados_dependentes["+codigo+"][ufnaturalidade_dependente]']").val(),
                _token  : $("#_token").val()
            }
        });

        request.done(function(retorno) {
            $.each(retorno, function(key, value) {
                $("select[name='dados_dependentes["+codigo+"][naturalidade_dependente]']").append('<option value="'+key+'">'+value+'</option>');
            });
        });
    });

    $("#continuar_formulario_contratos").click(function() {
        $('#accordion .collapse').collapse('show');
        setTimeout(function(){
            $("#formulario_contrato_principal").find('[type="submit"]').trigger('click');
        }, 1000);
    });

    $("#continuar_declaracao_de_saude").click(function() {
        $("#declaracao-de-saude-final").addClass("hide");
        $("#accordion").removeClass("hide");
        $('#accordion .collapse').collapse('show');
        if ($('.panel-body:not(:has(:radio:checked))').length) {
            alert("É obrigatório o preenchimento de todas as perguntas.");
            return false;
        }
        setTimeout(function(){
            $("#formulario_contrato_principal").find('[type="submit"]').trigger('click');
        }, 1000);
    });

    $("#declaracao_de_saude").click(function() {
        $("#declaracao-de-saude-final").removeClass("hide");
        $("#accordion").addClass("hide");
        $("input[type=radio]:checked").each(function() {
           if ($(this).val() == "S") {
               if ($("input[name='medico_declaracao']:checked").val() != "SIM") {
                   $("#dirijase").removeClass("hide");
                   $("input[name=tipo_de_dlp][value=cpt]").prop('checked', true);
               }
           }
        });
        if ($("input[name='medico_declaracao']:checked").val() == "SIM") {
            $("#declaracao_com_medico").removeClass("hide");
        }
    });

    $("#ufnaturalidade_contratante").change(function() {
        $('#naturalidade_contratante').html("<option value=''>Selecione</option>");
        var request = $.ajax({
            url     : "/ajax/municipios",
            method  : "POST",
            data    : {
                uf      : $("#ufnaturalidade_contratante").val(),
                _token  : $("#_token").val()
            }
        });

        request.done(function(retorno) {
            $.each(retorno, function(key, value) {
                $('#naturalidade_contratante').append('<option value="'+key+'">'+value+'</option>');
            });
        });
    });

    $("select[name='dados_contratante[uf_correspondencia]']").change(function() {
        $("select[name='dados_contratante[municipio_correspondencia]']").html("<option value=''>Selecione</option>");
        var request = $.ajax({
            url     : "/ajax/municipios",
            method  : "POST",
            data    : {
                uf      : $("select[name='dados_contratante[uf_correspondencia]']").val(),
                _token  : $("#_token").val()
            }
        });

        request.done(function(retorno) {
            $.each(retorno, function(key, value) {
                $("select[name='dados_contratante[municipio_correspondencia]']").append('<option value="'+key+'">'+value+'</option>');
            });
        });
    });

    $("input[name='dados_contratante[cep_correspondencia]']").change(function() {
        var request = $.ajax({
            url     : "/ajax/buscacep",
            method  : "POST",
            data    : {
                cep     : $("input[name='dados_contratante[cep_correspondencia]']").val(),
                _token  : $("#_token").val()
            }
        });

        request.done(function(retorno) {
            if (retorno == "") {
                alert('CEP não encontrado');
            } else {
                $("select[name='dados_contratante[uf_correspondencia]']").val(retorno[0].NUP_EST_SIGLA);
                $("select[name='dados_contratante[municipio_correspondencia]']").val(retorno[0].NUP_CODIGO_MUNICIPIO);
                $("input[name='dados_contratante[bairro_correspondencia]']").val(retorno[0].BRR_NOME);
                $("input[name='dados_contratante[bairro_correspondencia]']").attr('readonly', true);
                $("select[name='dados_contratante[tiplogradouro_correspondencia]']").val(retorno[0].TPL_CODIGO_TIPO);
                $("input[name='dados_contratante[logradouro_correspondencia]']").val(retorno[0].NML_NOME);
                $("input[name='dados_contratante[logradouro_correspondencia]']").attr('readonly', true);
                $("select[name='dados_contratante[municipio_correspondencia]']").html("<option value=''>Selecione</option>");
                $("select[name='dados_contratante[municipio_correspondencia]']").append('<option value="'+retorno[0].NUP_CODIGO_MUNICIPIO+'">'+retorno[0].NUP_NOME+'</option>');
                $("select[name='dados_contratante[municipio_correspondencia]']").val(retorno[0].NUP_CODIGO_MUNICIPIO);
            }
        });
    });

    $("input[name='dados_contratante[mesmo_endereco]']").click(function() {
        /* Caso o endereço de correspondencia do titular não seja o mesmo que o endereço residencial (valor 0), habilitar a div de preenchimento do endereço de correspondência e colocar nos devidos campos a obrigatoriedade */
        if (this.value == "0") {
            $("#contratante_correspondencia").attr("style", "display: block !important");
            $("input[name='dados_contratante[cep_correspondencia]']").prop('required',true);
            $("select[name='dados_contratante[uf_correspondencia]']").prop('required',true);
            $("select[name='dados_contratante[municipio_correspondencia]']").prop('required',true);
            $("input[name='dados_contratante[bairro_correspondencia]']").prop('required',true);
            $("select[name='dados_contratante[tiplogradouro_correspondencia]']").prop('required',true);
            $("input[name='dados_contratante[logradouro_correspondencia]']").prop('required',true);
            $("input[name='dados_contratante[numero_correspondencia]']").prop('required',true);
        } else {
            $("#contratante_correspondencia").attr("style", "display: none !important");
            $("input[name='dados_contratante[cep_correspondencia]']").removeAttr('required');
            $("select[name='dados_contratante[uf_correspondencia]']").removeAttr('required');
            $("select[name='dados_contratante[municipio_correspondencia]']").removeAttr('required');
            $("input[name='dados_contratante[bairro_correspondencia]']").removeAttr('required');
            $("select[name='dados_contratante[tiplogradouro_correspondencia]']").removeAttr('required');
            $("input[name='dados_contratante[logradouro_correspondencia]']").removeAttr('required');
            $("input[name='dados_contratante[numero_correspondencia]']").removeAttr('required');
        }
    });

    $(".toggle-radio input[type=radio]").change(function() {
        var parent_id = ($(this).closest('div').attr('id'));
        if (this.value == "S") {
            $("." + parent_id).html('<i class="fa fa-check" aria-hidden="true"></i>');
            $("#" + parent_id + "-yes").css('color','#fff');
            $("#" + parent_id + "-no").css('color','rgba(0,0,0,0.2)');
            $("#" + parent_id + " .switch").css('background-color', '#3cb371');
            $("#" + parent_id + "-complemento").removeClass('hidden');
            $("#" + parent_id + "-complemento input").attr("required", "required");
        } else {
            $("." + parent_id).html('<i class="fa fa-close" aria-hidden="true"></i>');
            $("#" + parent_id + "-no").css('color','#fff');
            $("#" + parent_id + "-yes").css('color','rgba(0,0,0,0.2)');
            $("#" + parent_id + " .switch").css('background-color', '#f47920');
            $("#" + parent_id + "-complemento").addClass('hidden');
            $("#" + parent_id + "-complemento input").removeAttr("required", "required");
        }
    });

    $(".alert-documento a").click(function() {
        $("#accordion").removeClass("hidden");
        $("#continuar_declaracao_de_saude").removeClass("hide");
    });

    $('.isTelefone').mask('(00)00000-0000');

    var request = $.ajax({
        url     : "/status-contrato",
        method  : "GET",
        data    : {}
    });

    request.done(function(retorno) {
        if (retorno == "") {
        } else {
            ProgressBar.init(
              [
                  'Informando seus dados',
                  'Enviando comprovantes',
                  'Declaração de saúde',
                  'Finalização da proposta',
                  'Verificação da Unimed'
              ],
              retorno, 'progress-bar-wrapper'
            );
            ProgressBar.singleStepAnimation = 1;
        }
    });

    $(".valueCidade").change(function() {
        location.href = '/selecionar-cidade/' + $(this).val();
    });

    $("#solicitar-cancelamento").click(function() {
        $('#motivo-de-cancelamento').show(300);
    });

});
