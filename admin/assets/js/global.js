$( document ).ready(function() {
	Google();
	Worker();
	
	Checked();
	CheckedBis();
	CheckAllGroup();
	
	$("input.default[type=checkbox]").on("click", Checked);
	$("input.default_bis[type=checkbox]").on("click", CheckedBis);
	
	PopupDelete();
	Upload();
});

function Google() {
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());
	
	gtag('config', 'UA-152611592-1');
}

function Worker() {
	if ('serviceWorker' in navigator) {
		window.addEventListener('load', () => {
			navigator.serviceWorker.register('/service-worker.js');
		});
	}
}

function Checked() {
	var n = $("input.default:checked").length;
	
	if(n === 1){
		$(".slider").html("Oui");
	} else{
		$(".slider").html("Non");
	}
}

function CheckedBis() {
	var n = $("input.default_bis:checked").length;
	
	if(n === 1){
		$(".slider_bis").html("Oui");
	} else{
		$(".slider_bis").html("Non");
	}
}

function CheckAllGroup() {
	$("#CheckAllGroup").click(function(e) {
		var check = $(".custom-checkbox").find(':checkbox');
		if(this.checked){
			check.prop('checked', true);
		}else{
			check.prop('checked', false);
		}
	});
}

function PopupDelete() {
	var theHREF;
	
	$(".confirmModalLink").click(function(e) {
		e.preventDefault();
		theHREF = $(this).attr("href");
		$("#confirmModal").modal("show");
	});
	
	$("#confirmModalYes").click(function(e) {
		window.location.href = theHREF;
	});
}

function Upload() {
	$(".custom-file-input").on("change", function() {
		var fileName = $(this).val().split("\\").pop();
		$(this).siblings(".custom-file-label").addClass("selected").html(fileName);
	});
}