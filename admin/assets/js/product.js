$( document ).ready(function() {
	PrixTTC();
	Popup('Promo');
	Popup('Feature');
});

function PrixTTC() {
	getCoefTax($("#inputPrixVente").val(), $("#select_tax").val());
	
	$("#inputPrixVente").keyup(function() {
		getCoefTax($(this).val(), $("#select_tax").val());
	});
	
	$("#select_tax").change(function() {
		getCoefTax($("#inputPrixVente").val(), $(this).val());
	});
}

function getCoefTax(price, id) {
	$.ajax({
		type:"POST",
		url : document.location.href,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			function: "CoefTax",
			data: id
		},
		success : function(ajax){
			
			var prix = displayPrice(price);
			var deee = displayPrice($('input[name="ecotax"]').val());
			
			var prix_ttc = ((prix + deee) * (1 + (ajax.coef /100)));
			
			$("#inputPrixVenteTtc").val(prix_ttc.toFixed(2));
		}
	});
}

function Popup(action) {
	$(".confirmModal"+action+"Link").click(function(e) {
		e.preventDefault();
		$("#confirmModal"+action).modal("show");
	});
	
	$("#confirmModal"+action+"Yes").click(function(e) {
		postProcess(action);
		location.reload(true);
	});
	
	window["popup"+action]();
}

function popupFeature() {
	$("#list_feature").change(function () {
		if ($(this).val() != '') {
			$('#new_feature').attr('disabled', true);
		} else {
			$('#new_feature').attr('disabled', false);
		}
	});
}

function popupPromo() {
	$("#searchClient").click(function() {
		searchClient($('#inputSearchClient').val());
	});
	
	$("#selectTypeRemise").change(function () {
		
		var prepend = '';
		
		if($(this).val() == 'remise'){
			prepend += 'Réduction';
		}
		else {
			prepend += 'Prix net';
		}
		
		$("#prependReduction").html(prepend);
	});
	
	$.datepicker.setDefaults($.datepicker.regional["fr"]);
	$('input#date_from, input#date_to').datepicker({
		todayBtn: "linked",
		regional: "fr",
		autoclose: true,
		todayHighlight: true,
		dateFormat: 'dd-mm-yy',
		beforeShow: function() {
			setTimeout(function(){
				$('.ui-datepicker').css('z-index', 1051);
			}, 0);
		}
	});
}

function postProcess(action) {
	$.ajax({
		type:"POST",
		url : document.location.href,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			function: "Add"+action,
			data: $('#formContent').serialize()
		}
	});
}

function searchClient(client) {
	$.ajax({
		type:"POST",
		url : document.location.href,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			function: "SearchClient",
			data: client
		},
		success : function(ajax)
		{
			var html = '';
			if(ajax.client.length > 0){
				
				html = '<select class="form-control col-lg-12" name="id_customer">';
				html += '<option value="">-- Choisir le client --</option>';
				
				for (var item in ajax.client) {
					
					var value = ajax.client[item];
					html += '<option value="'+value.id_customer+'">'+value.sexe+' '+value.name+' ('+value.email+')</option>';
				}
				
				html += '</select>';
			} else{
				html += '<div class="alert alert-danger col-lg-12" role="alert">';
				html +=		'Aucun client trouvé';
				html += '</div>';
			}
			
			$( "#view_search_customer" ).html(html);
		}
	});
}

function displayPrice(price) {
	return parseFloat(price.replace(',','.'));
}