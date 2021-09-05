$( document ).ready(function() {
	$("#search_client").on("click", '#searchClient', function() {
		searchClient($('#inputSearchClient').val());
	})
	
	$("#formContent").on('click','button.select_customer', function() {
		selectClient($(this));
	})
	
	$("#formContent").on('click','button.change_customer', function() {
		resetSearch($(this));
	})
});

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
				
				html = '<div class="card col-12">'
					html += '<div class="card-header text-left"><i class="fa fa-user"></i> Client</div>'
					html += '<div class="card-body">'
						html += '<div class="row">'
					
							for (var item in ajax.client) {
								
								var value = ajax.client[item];
								html += '<div class="card card-customer col-md-6 col-lg-4 mb-3 mb-lg-0">';
									html += '<div class="card-header">';
										html += value.sexe+' '+value.name;
									html += '</div>';
									html += '<div class="card-body">';
										html += '<span>'+value.email+'</span>'
									html += '</div>';
									html += '<div class="card-footer">';
										html += '<button type="button" value="'+value.id_customer+'" class="btn btn-themes select_customer"><i class="fa fa-arrow-right"></i> Choisir</button>';
									html += '</div>';
								html += '</div>';
							}
						
						html += '</div>';
					html += '</div>';
				html += '</div>';
			}
			
			$( "#view_search_customer" ).html(html);
		}
	});
}

function selectClient(client) {
	client.removeClass('select_customer').addClass('change_customer').html('<i class="fa fa-retweet"></i> Modifié');
	client.closest('.card-customer').addClass('select');
	$('#formulaire').removeClass('none');
	$('.card-customer').not('.select').remove();
	
	html = '<div class="col-lg-8">';
		html += '<input type="hidden" name="id_customer" id="inputSearchClient" class="form-control" value="'+client.val()+'" required>';
	html += '</div>';
	
	$('#search_client').html(html);
}

function resetSearch(client) {
	
	html = '<label for="inputSearchClient" class="control-label col-lg-4 margin-auto text-center">Client</label>';
	html += '<div class="col-lg-8 row">';
		html += '<div class="col-md-12 col-lg-6 col-xl-7 mb-3 mb-lg-0">';
			html += '<input type="text" name="client" id="inputSearchClient" class="form-control" required>';
		html += '</div>';
		html += '<div class="col-md-12 col-lg-6 col-xl-5">';
			html += '<button type="button" id="searchClient" class="col-12 btn btn-themes">Rechercher</button>';
		html += '</div>';
	html += '</div>';
	
	client.removeClass('change_customer').addClass('select_customer').html('<i class="fa fa-arrow-right"></i> Choisir');
	$('#formulaire').addClass('none');
	$('#search_client').html(html);
}