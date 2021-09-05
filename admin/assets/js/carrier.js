$( document ).ready(function() {
	changeModeTarification();
	
	freeCarrier();
	popupPlages();
	
	$("input[name='free_carrier']").click(function () {
		freeCarrier();
	});
});

function changeModeTarification() {
	if(url('t') > 0) {
		$("#select_facturation").change();
	}
	
	$("#select_facturation").change(function() {
		if($(this).val() == '1'){
			$('.switch_name_facturation').html('prix');
			$('.switch_facturation').html('€');
		} else{
			$('.switch_name_facturation').html('poids');
			$('.switch_facturation').html('Kg');
		}
	});
	
	$("input[name='free_carrier']").click(function () {
		if($(this).is(':checked')){
			$('#form-list').addClass('d-none');
		} else{
			$('#form-list').removeClass('d-none');
		}
	});
}

function freeCarrier() {
	if($("input[name='free_carrier']").is(':checked')){
		$('#form-list').addClass('d-none');
	} else{
		$('#form-list').removeClass('d-none');
	}
}

function popupPlages() {
	$(".confirmModalPlagesLink").click(function(e) {
		e.preventDefault();
		$("#confirmModalPlages").modal("show");
	});
	
	$("#confirmModalPlagesYes").click(function(e) {
		addPlage();
		location.reload(true);
	});
}

function addPlage() {
	$.ajax({
		type:"POST",
		url : document.location.href,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			function: "AddPlage",
			data: $('#formContent').serialize()
		}
	});
}

function url(key) {
	var results = new RegExp('[\\?&]'+key+'=([^&#]*)').exec(window.location.href);
	
	if(results != null){
		return results[1];
	}
	
	return 0;
}