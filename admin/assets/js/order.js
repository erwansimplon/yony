$( document ).ready(function() {
	formViewDate();
	PopupCarrier();
	Popup('delivery', 'livraison');
	Popup('invoice', 'facturation');
	
	$("#search_client").on("click", '#searchClient', function() {
		searchClient($('#inputSearchClient').val());
	});
	
	$("#formContent").on('click','button.select_customer', function() {
		selectClient($(this));
		addCart($(this).val());
		searchAddress($(this).val());
		searchPayment($(this).val());
	});
	
	$("#formContent").on('click','button.change_customer', function() {
		resetSearch($(this));
		deleteCart($('#id_cart').val());
		document.location.reload(true);
	});
	
	$("#btnProduct").on('click', function() {
		searchProduct($('#inputSearchProduct').val());
	});
	
	$("#view_search_product").on('click', '#addCartProduct', function() {
		addProduct($('#id_cart').val(), $('#selectViewsProduct').val(), $('#quantity_product').val(), 0, 0);
	});
	
	$('#panier').on('click', '.cart_up', function () {
		var cart = this.id.split('_');
		addProduct(cart[0], cart[1], Math.abs(cart[2]), 0, 0);
	});
	
	$('#panier').on('click', '.cart_down', function () {
		var cart = this.id.split('_');
		addProduct(cart[0], cart[1], -Math.abs(cart[2]), 0, 0);
	});
	
	$('#panier').on('click', '.cart_del', function () {
		var qte = $('#qte_'+this.id).val();
		var cart = this.id.split('_');
		addProduct(cart[0], cart[1], -Math.abs(qte), 0, 0);
	});
	
	$('#panier').on('change', '.cart_qte', function () {
		var qte = $(this).val();
		var cart = this.id.split('_');
		
		if(qte != '') {
			addProduct(cart[1], cart[2], qte, 0, 1);
		}
	});
	
	$('#panier').on('change', '.unit_price', function () {
		var cart  = this.id.split('_');
		var price = $(this).val();
		
		if(price > 0 && price != ''){
			addProduct(cart[0], cart[1], 0, $(this).val(),0);
		}
	});
	
	$('#delivery_option').change(function () {
		updateIdCarrier($(this).val());
	});
	
	$('#change_currente_state').change(function () {
		$("#change_currente_state option:selected").attr('selected','true').siblings().removeAttr('selected');
	});
});

function formViewDate() {
	
	$.datepicker.setDefaults($.datepicker.regional["fr"]);
	$('input#date_from, input#date_to').datepicker({
		todayBtn: "linked",
		regional: "fr",
		autoclose: true,
		todayHighlight: true,
		dateFormat: 'dd-mm-yy'
	});
}

function PopupCarrier() {
	
	$(".confirmModalCarrier").click(function(e) {
		e.preventDefault();
		$("#confirmModalCarrier").modal("show");
	});
	
	$('#confirmModalCarrierYes').click(function(e) {
		updateTrackingNumber();
	});
}

function Popup(action, title) {
	
	$("#button_"+action).click(function(e) {
		e.preventDefault();
		$('#modal_alias').val(ucfirst(title));
		$('#title_address').html(title);
		getAddressCart($('#id_cart').val(), action, ucfirst(title));
		$(".confirmModalAddressYes").prop("id", "confirm_"+action+"_yes");
		$("#confirmModalAddress").modal("show");
	});
	
	$('#confirmModalAddress').on('click', "#confirm_"+action+"_yes", function(e) {
		var require = 0;
		$('#formModalOrder').find('input').each(function () {
			if($(this).prop('required') && (!$(this).val() || $.trim($(this).val()).length === 0)){
				require += 1;
				$(this).addClass('is-invalid');
			} else{
				$(this).addClass('is-valid').removeClass('is-invalid');
			}
		});
		
		if(require === 0) {
			updateAddress($('#id_cart').val(), action);
		}
	});
}

function updateTrackingNumber() {
	$.ajax({
		type:"POST",
		url : document.location.href,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			function: "UpdateTrackingNumber",
			data: $('#formModalCarrier').serialize()
		},
		success : function (ajax) {
			$('#tracking_number').html(ajax.tracking_number);
			$("#confirmModalCarrier").modal("hide");
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

function searchAddress(client) {
	$.ajax({
		type:"POST",
		url : document.location.href,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			function: "SearchAddressCustomer",
			data: client
		},
		success : function(ajax)
		{
			var delivery = addressHtml(ajax.address.delivery, 'delivery', 1);
			var invoice = addressHtml(ajax.address.invoice, 'invoice', 1);
			
			$("#address_delivery").html(delivery);
			$("#address_invoice").html(invoice);
		}
	});
}

function addressHtml(tabs, key, strict) {
	
	var html = '';
	
	if(tabs.id_customer != null){
		html += '<span class="font-weight-bold">'+tabs.lastname+'</span><br>';
		html += '<span>'+tabs.voie+'</span><br>';
		if(tabs.complement_voie != '') {
			html += '<span>' + tabs.complement_voie + '</span><br>';
		}
		html += '<span>'+tabs.postcode+' '+tabs.ville+'</span><br>';
		html += '<span>'+tabs.phone+'</span>';
		
		$('#button_'+key).html(responsiveButton('edit', 'Modifier')).show();
		
		if(strict === 1 && key == 'invoice') {
			$('#button_'+key).hide();
		}
	} else {
		$('#button_'+key).html(responsiveButton('user-plus', 'Ajouter')).show();
	}
	
	return html;
}

function responsiveButton(icon, text) {
	var button = '<i class="fa fa-'+icon+'"></i>';
	
	if($(this).width() >= 1120){
		button += ' '+text;
	}
	
	return button;
}

function getAddressCart(id_cart, action, title) {
	$.ajax({
		type:"POST",
		url : document.location.href,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			function: "GetAddressCart",
			data: {
				'id_cart':id_cart,
				'action':action
			},
		},
		success : function(ajax)
		{
			$('#modal_alias').val(title);
			$('#nom').val(ajax.address.lastname);
			$('#adresse').val(ajax.address.voie);
			$('#complement_voie').val(ajax.address.complement_voie);
			$('#postcode').val(ajax.address.postcode);
			$('#ville').val(ajax.address.ville);
			$('#phone').val(ajax.address.phone);
		}
	});
}

function updateAddress(id_cart, action) {
	$.ajax({
		type:"POST",
		url : document.location.href,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			function: "UpdateAddress",
			data: {
				'id_cart':id_cart,
				'action':action,
				'form':$('#formModalOrder').serialize()
			},
		},
		success : function(ajax)
		{
			$('#formModalOrder').find('input').each(function () {
				$(this).val('').removeClass('is-valid');
			});
			
			var address = addressHtml(ajax.address, action, 0);
			
			$("#address_"+action).html(address);
			$("#confirmModalAddress").modal("hide");
			
			if(ajax.cart.id_address_delivery > 0 && ajax.cart.id_address_invoice > 0){
				$('#carrier, #summary, #submit_order').css('display', 'flex');
			}
		}
	});
}

function searchCarrier(id_cart) {
	$.ajax({
		type:"POST",
		url : document.location.href,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			function: "SearchCarrier",
			data: id_cart
		},
		success : function(ajax)
		{
			var option = '';
			
			var first_id_carrier = ajax.carrier[0].id_carrier;
			var default_carrier  = ajax.carrier[0].default_carrier;
			
			var carrier = (default_carrier > 0) ? default_carrier : first_id_carrier;
			
			for (var item in ajax.carrier) {
				var value = ajax.carrier[item];
				
				var tarif = (value.price > 0) ? formatPrice(value.price) : 'gratuit';
				var selected = (value.id_carrier == carrier) ? 'selected' : '';
				
				option += '<option value="'+value.id_carrier+'" '+selected+'>'+value.name+' ('+tarif+')</option>'
			}
			
			updateIdCarrier(carrier);
			$("#delivery_option").html(option);
		}
	});
}

function updateIdCarrier(id_carrier) {
	
	$.ajax({
		type:"POST",
		url : document.location.href,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			function: "UpdateIdCarrier",
			data: {
				'id_cart':$('#id_cart').val(),
				'id_carrier':id_carrier
			},
		},
		success : function (ajax) {
			summary(ajax.id_cart);
		}
	});
}

function searchProduct(product) {
	$.ajax({
		type:"POST",
		url : document.location.href,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			function: "SearchProduct",
			data: product
		},
		success : function(ajax)
		{
			var html = '';
			if(ajax.product.length > 0){
				html += '<div class="input-group form-group">';
				html += '<label for="selectViewsProduct" class="control-label col-lg-4 margin-auto text-center">Produits</label>';
				html += '<select id="selectViewsProduct" class="form-control">';
				
				for (var item in ajax.product) {
					
					var value = ajax.product[item];
					
					html += '<option value="'+value.id_product+'">['+value.reference+'] '+value.name+' / En stock : '+value.quantity+'</option>'
				}
				
				html += '</select>';
				html += '</div>';
				
				html += '<div class="input-group">';
					html += '<label for="quantity_product" class="control-label col-lg-4 text-center">Quantité</label>';
					html += '<input type="text" name="quantity_product" id="quantity_product" class="form-control col-lg-2 mb-3 mb-lg-0" value="'+value.conditionnement+'">';
					html += '<div class="col-md-12 col-lg-3">';
						html += '<button type="button" id="addCartProduct" class="col-12 btn btn-themes">Ajouter</button>';
					html += '</div>';
				html += '</div>';
			}
			
			$("#view_search_product").html(html);
		}
	});
}

function addCart(id_customer) {
	$.ajax({
		type:"POST",
		url : document.location.href,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			function: "AddCart",
			data: id_customer
		},
		success : function(ajax)
		{
			$("#id_cart").val(ajax.id_cart);
			$('#panier').css('display', 'flex');
		}
	});
}

function deleteCart(id_cart) {
	$.ajax({
		type:"POST",
		url : document.location.href,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			function: "DeleteCart",
			data: id_cart
		}, 
		success : function(ajax)
		{
			$("#id_cart").val('');
			$('#panier').css('display', 'none');
		}
	});
}

function addProduct(id_cart, id_product, quantity, price, reset_qte) {
	$.ajax({
		type:"POST",
		url : document.location.href,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			function: "AddProductInCart",
			data: {
				'id_cart'    : id_cart,
				'id_product' : id_product,
				'quantity'   : quantity,
				'price'      : price,
				'reset_qte'  : reset_qte
			}
		},
		success : function(ajax)
		{
			var html = '';
			
			searchCarrier(id_cart);
			
			if(ajax.product.length > 0){
				
				html += '<table id="form-list" class="table table-bordered footab">';
					html +=	'<thead class="thead_pc_display">';
						html += '<tr class="item">';
							html += '<th class="item width_10">'
								html += 'Image';
							html += '</th>';
							html += '<th class="item">';
								html += 'Référence';
							html += '</th>';
							html += '<th class="item">';
								html += 'Désignation';
							html += '</th>';
							html += '<th class="item width_10">';
								html += 'Prix unitaire';
							html += '</th>';
							html += '<th class="item width_10">';
								html += 'Quantité';
							html += '</th>';
							html += '<th class="item width_10">';
								html += 'Écotaxe';
							html += '</th>';
							html += '<th class="item width_10">';
								html += 'Prix';
							html += '</th>';
						html += '</tr>';
					html += '</thead>';
					html += '<tbody>';
						
						for (var item in ajax.product) {
							
							var value = ajax.product[item];
							var img = false;
							
							if(value.image) {
								var link_img = '../assets/img/product/' + value.image + '/';
								img = link_img + value.image + '_product_cart' + value.image_ext;
							}
							
							html += '<tr class="item">';
								html += '<td>';
								if (img){
									html += '<span class="detail_td bold">Image :&nbsp;</span>';
									html += '<img src="'+img+'" />';
								}
								html += '</td>';
								html += '<td>';
									html += '<span class="detail_td bold">Référence :&nbsp;</span>';
									html += value.reference;
								html += '</td>';
								html += '<td>';
									html += '<span class="detail_td bold">Désignation :&nbsp;</span>';
									html += value.name;
								html += '</td>';
								html += '<td>';
									html += '<span class="detail_td bold">Prix unitaire :&nbsp;</span>';
									html += '<input class="unit_price width_input_price form-control" id="'+value.id_cart+'_'+value.id_product+'" value="'+value.pu_vente+'">';
								html += '</td>';
								html += '<td>';
									html += '<span class="detail_td bold">Quantité :&nbsp;</span>';
									html += '<div class="input-group width_qte_up_down">';
										html += '<div class="input-group-prepend up_down">';
											html +=	'<button type="button" class="cart_up btn-caret btn btn-outline-secondary" id="'+value.id_cart+'_'+value.id_product+'_'+value.conditionnement+'"><i class="fa fa-caret-up"></i></button>';
											html +=	'<button type="button" class="cart_down btn-caret btn btn-outline-secondary" id="'+value.id_cart+'_'+value.id_product+'_'+value.conditionnement+'"><i class="fa fa-caret-down"></i></button>';
										html +=	'</div>';
										html +=	'<input type="text" class="cart_qte form-control" id="qte_'+value.id_cart+'_'+value.id_product+'" value="'+value.quantity+'">';
										html +=	'<div class="input-group-append">';
											html +=	'<button type="button" class="cart_del btn-caret btn btn-outline-secondary" id="'+value.id_cart+'_'+value.id_product+'"><i class="fa fa-times"></i></button>';
										html +=	'</div>';
									html +=	'</div>';
								html += '</td>';
								html += '<td>';
									html += '<span class="detail_td bold">Écotaxe :&nbsp;</span>';
									html += formatPrice(value.total_ecotax);
								html += '</td>';
								html += '<td>';
									html += '<span class="detail_td bold">Prix :&nbsp;</span>';
									html += formatPrice(value.prix_vente);
								html += '</td>';
							html += '</tr>';
						}
				
					html += '</tbody>';
				html += '</table>';
				
				$('#address').css('display', 'flex');
				
				if(ajax.cart.id_address_delivery > 0 && ajax.cart.id_address_invoice > 0){
					$('#carrier, #summary, #submit_order').css('display', 'flex');
				}
			} else{
				$('#address, #carrier, #summary, #submit_order').css('display', 'none');
			}
			
			$("#view_shopping_cart").html(html);
		}
	});
}

function summary(id_cart) {
	$.ajax({
		type:"POST",
		url : document.location.href,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			function: "Summary",
			data: id_cart
		},
		success : function(ajax)
		{
			var html = '';
			
			html += '<div class="col-lg-6 col-xl-3 summary_margin">';
			html += '<div class="row">';
			html += '<span class="col-12 form-group">Total produits</span>';
			html += '<span id="total_products" class="col-12 size_l text-success">'+formatPrice(ajax.summary.product_ht)+'</span>';
			html += '</div>';
			html += '</div>';
			
			html += '<div class="col-lg-6 col-xl-3 summary_margin">';
			html += '<div class="row">';
			html += '<span class="col-12 form-group">Total frais de port (HT)</span>';
			html += '<span id="total_shipping" class="col-12 size_l">'+formatPrice(ajax.summary.shipping_ht)+'</span>';
			html += '</div>';
			html += '</div>';
			
			html += '<div class="col-lg-6 col-xl-3 summary_margin">';
			html += '<div class="row">';
			html += '<span class="col-12 form-group">Total (HT)</span>';
			html += '<span id="total_without_taxes" class="col-12 size_l">'+formatPrice(ajax.summary.total_ht)+'</span>';
			html += '</div>';
			html += '</div>';
			
			html += '<div class="col-lg-6 col-xl-3 summary_margin">';
			html += '<div class="row data-focus-themes">';
			html += '<span class="col-12 form-group">Total (TTC)</span>';
			html += '<span id="total_with_taxes" class="col-12 size_l">'+formatPrice(ajax.summary.total_ttc)+'</span>';
			html += '</div>';
			html += '</div>';
			
			$("#summary_content").html(html);
		}
	});
}

function searchPayment(client) {
	$.ajax({
		type:"POST",
		url : document.location.href,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			function: "SearchPayment",
			data: client
		},
		success : function(ajax)
		{
			var html = '';
			
			for (var item in ajax.payment_access) {
				var value = ajax.payment_access[item];
				
				html += '<option value="'+value.id_order_payment+'">'+value.name+'</option>';
			}
			
			$("#payment").html(html);
		}
	});
}

function ucfirst(string) {
	return string.charAt(0).toUpperCase() + string.slice(1);
}

function formatPrice(price) {
	return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
}