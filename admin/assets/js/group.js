$( document ).ready(function() {
	Popup('Category');
	Popup('Fabricant');
});

function Popup(action) {
	$(".confirmModal"+action+"Link").click(function(e) {
		e.preventDefault();
		$("#confirmModal"+action).modal("show");
	});
	
	$("#confirmModal"+action+"Yes").click(function(e) {
		postProcess(action);
		location.reload(true);
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
			function: "AddRemise"+action,
			data: $('#formGroup').serialize()
		}
	});
}