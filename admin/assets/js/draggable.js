$(document).ready(function() {
	$( "#draggable" ).sortable({
		axis: 'y',
		stop: function (event, ui) {
			var position = $(this).sortable('serialize');
			changePosition(position);
		}
	});
});

function changePosition(position) {
	$.ajax({
		type:"POST",
		url : document.location.href,
		async: true,
		dataType: "json",
		data : {
			ajax: "1",
			function: "ChangePosition",
			data: position,
		},
		success : function(ajax){
			$.each(ajax.position, function (position, id) {
				$('.item-'+id).html(position);
			})
		},
	});
}