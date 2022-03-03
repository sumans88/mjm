$(document).ready(function() {
	// $('.browse-image').next().next().text('size: '+max_width_cropzoom +' x '+max_height_cropzoom+'px');

	$(".browse-file").click(function(event) {
		// $("#fileManager").modal("show");
		$("#fileManager").modal({
			backdrop: "static",
			keyboard: false
		});
	});
});
$(".uploadFile").change(function(){
	var id = $(this).attr('id');
	var out = id.replace('fl','');
	/*alert(out); return false;*/
	/*$(this)[0].files[0];*/
	var nameFile = $(this)[0].files[0].name;
 	var formData = new FormData();
 	formData.append('userfile',$(this)[0].files[0],$(this)[0].files[0].name);
 	/*console.log(formData.get('userfile')); return false;*/
 	$.ajax({
 		url: base_url+'apps/home/check_valid_file',
 		type: 'POST',
 		dataType: 'json',
 		data: formData,
 		processData: false,
    	contentType: false,
 		success: function(ret){
 			if (ret.error == 1) {
 				$('#'+id).val('');
 				$('.output-file'+out).val('');
 				$('.msg-info'+out).html(ret.message);
 			} else {
 				$('.output-file'+out).val(nameFile); 				
 				$('.msg-info'+out).html('');
 			}
 		}
 	})
 	
})