$( document ).ready(function() {
	$("input[type=radio][name='cover']").click(function(e) {
		var id = $(this).attr('id').split('_');
		changeCover(id[1]);
	});
});

function changeCover(id_image) {
	$.ajax({
		type:"POST",
		url : document.location.href,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			function: "UpdateCoverImage",
			data: {
				id_product:$('#id_product').val(),
				id_products_image:id_image,
			},
		},
	});
}
