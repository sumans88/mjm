$(document).ready(function(){
	$('#print_data').click(function(){
		window.print();
	});

	$('#save,#save_approve').click(function(){
		var send_approval = $(this).attr('id');
		var back_url = $(this).attr('data-back') || '';
		loading();
		var ckData = '';
		var ckId = '';
		var ckVal = '';
		
		var formData = new FormData($('#form1')[0]);
		if (send_approval != 'save') {
			formData.append('send_approval',1);
		}
		
		$('.ckeditor').each(function(){
			ckId = $(this).attr('id');
			val = CKEDITOR.instances[ckId].getData();
			CKEDITOR.instances[ckId].updateElement();
			ckData += '&'+ckId+'='+escape(val);
			formData.append(ckId,escape(val));
		})

		// if (1 == 1 ) {
		if ($('#form1').parsley().validate()) {
			$.ajax({
				url         : $('#form1').attr('action'),
				type        : "POST",
				dataType	: 'json',
				/*data        : $('#form1').serialize()+ckData+send_approval,*/
				data        : formData,
				processData: false,
    			contentType: false,
				error		: function () {
								//notify('error!');
								// clear_form_elements('#form1');
								$('#save-schedule').modal('hide')
								loadingcomplete();
							},
				success     : function(ret){
								if (ret.error==1) {
									$.gritter.add({title:page_name,text:ret.message});
									$('#save-schedule').modal('hide')
									loadingcomplete();
								}else{
									window.location.href=this_controller + back_url;
									clear_form_elements('#form1');
								}		
				}
			})
		} else {
			$('#save-schedule').modal('hide')
			loadingcomplete();
		}
		return false;
	})
	$('#save_with_file').click(function(){
		var back_url = $(this).attr('data-back') || '';
		loading();
		var ckData = '';
		var ckId = '';
		var ckVal = '';
		if ($('#form1').parsley().validate()) {
			$.ajaxFileUpload({
				url         : $('#form1').attr('action'),
				dataType	: 'json',
				secureuri		: false,
				fileElementId 	: 'file_name',
				data        : $('#form1').serializeObject(),
				error		: function () {
								//notify('error!');
								clear_form_elements('#form1');
								$('#save-schedule').modal('hide')
								loadingcomplete();
							},
				success     : function(ret){
								if (ret.error==1) {
									$.gritter.add({title:page_name,text:ret.message});
									$('#save-schedule').modal('hide')
									loadingcomplete();
								}
								else{
									window.location.href=this_controller + back_url;
									clear_form_elements('#form1');
								}
								
				}
			})
		} else {
			$('#save-schedule').modal('hide')
			loadingcomplete();
		}
		return false;
	})
	//pages, news
	$('#title,#uri_path').keyup(function(){
		$('#uri_path').val( convert_to_uri( $(this).val() ) );
	})
	//news
	$('#approve,#revise').click(function(){
		var proses = $(this).attr('id');
		var comment = $('#comment').val();
		if(proses == 'revise' && !comment){
			notify('comment is required','error','#approval-area','top');
			return;
		}

		if(confirm('Update to '+ proses+ ' ?')){
			loading();
			var id_news = $('#id_news').val();
			$.ajax({
				url         : this_controller+'proses_approval',
				type        : "POST",
				dataType	: 'json',
				data        : 'id_news='+id_news+'&proses='+proses+'&comment='+comment,
				error		: function () {
								notify('error!','error','#approval-area','top');
								loadingcomplete();
							},
				success     : function(ret){
								if (ret.error==1) {
									console.log(ret);
									notify(ret.message,'error','#approval-area','top');
									loadingcomplete();
								}
								else{
									window.location.href=this_controller;
								}
				}
			})		
		}
	})
	
	

	$('#reply-contactus').click(function(){
		if ($('#form1').parsley().validate()) {
			var id = $(this).attr('data-id');
			loading();
			$.ajax({
				url         : this_controller+'reply',
				type        : "POST",
				dataType	: 'json',
				data        : $('#form1').serialize(),
				error		: function () {
								$.gritter.add({title:page_name,text:'Please try again'});
								$('#popDetail').modal('hide');
								loadingcomplete();
							},
				success     : function(ret){
								if(ret.error == 1){
									$.gritter.add({title:page_name,text:'message not. '+ret.message});
								}
								else{
									$.gritter.add({title:page_name,text:'Message Sent'});
									
									$('#grid1 .reload').trigger('click');
								}
								$('#popDetail').modal('hide');
								the_grid('grid1',this_controller+'records');
								loadingcomplete();
				}
			});
		} else {
			loadingcomplete();
		}
		return false;
	});
	$('#multiple_delete').click(function(){
		if(confirm('Delete Data ?')){
			var send_approval = $(this).attr('id') == 'save' ? '' : '&send_approval=1';
			var back_url = $(this).attr('data-back') || '';
			loading();
			var ckData = '';
			var ckId = '';
			var ckVal = '';
			$('.ckeditor').each(function(){
				ckId = $(this).attr('id');
				val = CKEDITOR.instances[ckId].getData();
				ckData += '&'+ckId+'='+escape(val);
			})
			$.ajax({
				url         : $('#form1').attr('action'),
				type        : "POST",
				dataType	: 'json',
				data        : $('#form1').serialize()+ckData+send_approval,
				error		: function () {
								notify('error!');
								loadingcomplete();
							},
				success     : function(ret){
								if (ret.error==1) {
									console.log(ret);
									$.gritter.add({title:page_name,text:ret.message});
									loadingcomplete();
								}
								else{
									window.location.href=this_controller + back_url;
								}
				}
			});
		}
	})

})






$('#btnAdd_1').click(function () {
	console.log(1);
    var num     = $('.clonedInput_1').length, // Checks to see how many "duplicatable" input fields we currently have
        newNum  = new Number(num + 1),      // The numeric ID of the new input field being added, increasing by 1 each time
        newElem = $('#entry' + num).clone().attr('id', 'entry' + newNum).fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value

/*  This is where we manipulate the name/id values of the input inside the new, cloned element
    Below are examples of what forms elements you can clone, but not the only ones.
    There are 2 basic structures below: one for an H2, and one for form elements.
    To make more, you can copy the one for form elements and simply update the classes for its label and input.
    Keep in mind that the .val() method is what clears the element when it gets cloned. Radio and checkboxes need .val([]) instead of .val('').
*/
    // Label for email field
    newElem.find('.label_email').attr('id', 'ID' + newNum + '_reference').attr('name', 'ID' + newNum + '_reference').html('Email #' + newNum);

    // Email input - text
    newElem.find('.label_email').attr('for', 'ID' + newNum + '_email_address');
    newElem.find('.input_email').attr('id', 'ID' + newNum + '_email_address').attr('name', 'ID' + newNum + '_email_address').val('');

// Insert the new element after the last "duplicatable" input field
    $('#entry' + num).after(newElem);
    $('#ID' + newNum + '_title').focus();

// Enable the "remove" button. This only shows once you have a duplicated section.
    $('#btnDel_1').attr('disabled', false);

// Right now you can only add 4 sections, for a total of 5. Change '5' below to the max number of sections you want to allow.
    // This first if statement is for forms using input type="button" (see older demo). Delete if using button element.
    if (newNum == 5)
    $('#btnAdd_1').attr('disabled', true).prop('value', "You've reached the limit"); // value here updates the text in the 'add' button when the limit is reached
    // This second if statement is for forms using the new button tag (see Bootstrap demo). Delete if using input type="button" element.
    if (newNum == 5)
    $('#btnAdd_1').attr('disabled', true).text("You've reached the limit"); // value here updates the text in the 'add' button when the limit is reached 
});

$('#btnDel_1').click(function () {
// Confirmation dialog box. Works on all desktop browsers and iPhone.
    if (confirm("Are you sure you wish to remove this email? This cannot be undone."))
        {
            var num = $('.clonedInput_1').length;
            // how many "duplicatable" input fields we currently have
            $('#entry' + num).slideUp('slow', function () {$(this).remove();
            // if only one element remains, disable the "remove" button
                if (num -1 === 1)
            $('#btnDel_1').attr('disabled', true);
            // enable the "add" button. IMPORTANT: only for forms using input type="button" (see older demo). DELETE if using button element.
            $('#btnAdd_1').attr('disabled', false).prop('value', "Add email");
            // enable the "add" button. IMPORTANT: only for forms using the new button tag (see Bootstrap demo). DELETE if using input type="button" element.
            $('#btnAdd_1').attr('disabled', false).text("Add email");});
        }
    return false; // Removes the last section you added
});
// Enable the "add" button
$('#btnAdd_1').attr('disabled', false);
// Disable the "remove" button
$('#btnDel_1').attr('disabled', true);





//news
var ajx = null;
$(document).on('click','.view-detail',function(){
	var id = $(this).attr('data-id');
	$('#popViewDetail').modal('show');
	$('#view-detail').html('<div id="loading-modal"><i class="fa fa-spinner fa-spin"></i> Loading....</div>');
	if ( ajx ) {ajx.abort()};
	ajx = $.ajax({
			url         : base_url+'apps/news/version_detail/'+id,
			type        : "POST",
			data        : '',
			error		: function () {$('#view-detail').html('error loading data, please try again');},
			success     : function(ret){$('#view-detail').html(ret);}
		})
})

//view detail contact us
function convert_to_uri(val){
    return val
        .toLowerCase()
        .replace(/ /g,'-')
        .replace(/[^\w-]+/g,'')
        ;
}


var ajax_contact;
$(document).on('click','.detail-hubungi-kami',function(){
	$('#message-reply').val('');
	$('#id-reply').val($(this).attr('data-id'));
	$('#popDetail').modal('show');
	var btn = $(this);
    $('#loading-contactus').show();
    if(ajax_contact){
    	ajax_contact.abort();
    }
    var ajax_contact = $.ajax({
							url         : this_controller+'detail/'+btn.attr('data-id'),
							error		: function () {
											$('#loading-contactus').hide();
											$('#content-contactus').html('error!<br>Please try again');

										},
							success     : function(ret){
											$('#loading-contactus').hide();
											$('#content-contactus').html(ret);

							}
						})
})
function export_to_excel(type){
	if(type == 1){
		$.ajax({
			type 	: 'POST',
			url 	: $('#export_to_excel_form').attr('action'),
			data 	: $('#export_to_excel_form').serialize(),
			success : function(){
				alert('Terima Kasih. Cek email Anda untuk mengunduh file.');
			}
		})
	}else{
		$('#export_to_excel_form').submit();
	}
}

//frontend menu
function frontend_menu(id){
	$('#id_frontend_menu_type'+id).change(function(){
		var val = $(this).val();
		if(val == 1){
			$('#type_module'+id).show();
			$('#type_extra'+id).hide();
			$('#extra_param'+id).attr('readonly',true);
		}
		else if(val == 2){
			$('#type_module'+id).hide();
			$('#type_extra'+id).show();
			$('#extra_param'+id).attr('readonly',false);
		}
		$('#extra_param'+id).val('');
		$('#id_module'+id).select2('val','');
	})

	$(document).on('click','.select-data',function(){
		var url = $(this).attr('data-url');
		$('#extra_param'+id).val(url);
		$('#popViewDetail'+id).modal('hide');
		$('#change-data'+id).show();
		$('#type_extra'+id).show();

	})

	var ajax = null;
	var ajax2 = null;
	$('#id_module'+id).change(function(){
		var idd = $(this).val();
		$('#loading-callback').show();
		if ( ajax ) { ajax.abort();}
		ajax = $.ajax({
			url         : base_url+'apps/frontend_menu/get_callback/'+idd,
			error		: function () {$('#view-detail').html('error loading data, please try again');},
			success     : function(ret){
							if(ret){
								$('#view-detail').html('<div id="loading-modal"><i class="fa fa-spinner fa-spin"></i> Loading....</div>');
								$('#popViewDetail').modal('show');
								
								if ( ajax2 ) { ajax.abort();}

								ajax2 = $.ajax({
									url         : base_url+'apps/'+ret,
									type        : "POST",
									data        : '',
									error 		:function(err){
													if (err.statusText != 'abort') {
														var err_close ='<i class="icon-remove-sign icon-2x closeModal" data-dismiss="modal"></i>';
														$('#view-detail'+id).html('Error!'+' '+err.status + ' '+err.statusText+err_close);
														$('#id_module'+id).select2('val','');
														$('#extra_param'+id).val('');
														$('#type_extra'+id).hide();
													}
												},
									success     : function(ret){$('#view-detail').html(ret);}
								})
							}
							else{
								$('#extra_param'+id).val('');
								//$('#type_extra').hide();
							}
							$('#loading-callback').hide();
			}
		});
	})
}


$(document).ready(function(){
	$('.lang_na').change(function(){
		var stat   = $(this).attr('stat-grup');
		var idlang = $(this).attr('stat-id');
		if($(this).attr("checked")){
			$(this).parent().parent().parent().find('.form-content').hide();

			if (idlang == 0) {
				if (stat == 'open') {
					$('#id_news_category1').parent().parent().removeClass('hide');
					$('#publish_date1').parent().parent().removeClass('hide');
					$('#id_status_publish1').parent().parent().removeClass('hide');
				} else {
					$('#id_news_category1').parent().parent().removeClass('hide');
				}

				$('#form-content-event1').children("div.inv").removeClass("hide");
				$('#nav-pills-tab-0 [data-parsley-required]').attr("data-parsley-required",false);
				$('#nav-pills-tab-1 [data-parsley-required]').attr("data-parsley-required",true);
			}
		}
		else{
			$(this).parent().parent().parent().find('.form-content').show();

			if (idlang == 0) {
				if (stat == 'open') {
					$('#id_news_category1').parent().parent().addClass('hide');
					$('#publish_date1').parent().parent().addClass('hide');
					$('#id_status_publish1').parent().parent().addClass('hide');
				} else {
					$('#id_news_category1').parent().parent().addClass('hide');
				}

				$('#form-content-event1').children("div.inv").addClass("hide");
				$('#nav-pills-tab-0 [data-parsley-required]').attr("data-parsley-required",true);
				$('#nav-pills-tab-1 [data-parsley-required]').attr("data-parsley-required",false);
			}
		}
	})
	$('.lang_na').trigger('change');
})

function delImage(idKey, url){
	$('#img' + idKey + ' img').attr("src", url + "images/article/small/no_image.png");
	$('#imgDelete' + idKey).val('1');
}

function delFile(e,idKey){
	// removed file
	$(e).parent().find('div').find('input').val('')
	//remove label
	$(e).parent().find('span').find('input').val('');
	$('#fileTampil' + idKey).addClass('hide');
	$('#fileDelete' + idKey).val('1');
}
$(document).on('click','a.modal-send-invoice',function(){
  var member_id = $(this).attr('data-id'); 
  $('#modal-id-member').val(member_id);  
	$.ajax({
		url         : base_url+'apps/individual/invoice_check/'+member_id,
		type        : "POST",
		dataType	: 'json',
		// data        : $('#form1').serialize(),
		// data        : formData,
		processData: false,
		contentType: false,
		error		: function () {
						notify('error!');
						loadingcomplete();
					},
		success     : function(ret){
						if (ret.error==1) {
							$.gritter.add({title:page_name,text:ret.message});
							// $('#save-schedule').modal('hide')
							loadingcomplete();
						}else{
							$('[name="invoice_number"]').val(ret.invoice_number);
							$('.inputfile-6').next( 'label' ).find( 'span' ).html(ret.filename);
							if (ret.is_sent) {
								$('.savedraft').addClass('invis');
							}else{
								$('.savedraft').removeClass('invis');
							}
							
							// window.location.href=this_controller + back_url;
							// clear_form_elements('#form1');
							$('#modal-send-invoice').modal('show');
						}		
		}
	})
});

$(document).on('click','a.modal-check-invoice, a.modal-check-invoice-event',function(){
	var i = $(this);
  var member_id = $(this).attr('data-id'); 
  var payment_id = $(this).attr('data-id-payment'); 
  $('#modal-id-member').val(member_id);
  // $('#modal-send-invoice').modal('show');
  var url_go = $(this).hasClass('modal-check-invoice-event') 
  ? 'apps/payment_confirmation/invoice_check/'+member_id+"/"+payment_id+'/1'
  :'apps/payment_confirmation/invoice_check/'+member_id+"/"+payment_id;
	$.ajax({
		url         : base_url+url_go,
		type        : "POST",
		dataType	: 'json',
		data        : $('#form2').serialize(),
		// data        : formData,
		processData: false,
		contentType: false,
		error		: function () {
						notify('error!');
						// clear_form_elements('#form1');
						// $('#modal-send-invoice').modal('hide')
						loadingcomplete();
					},
		success     : function(ret){
						if (ret.error==1) {
							$.gritter.add({title:page_name,text:ret.message});
							// $('#save-schedule').modal('hide')
							loadingcomplete();
						}else{
							if (i.hasClass('modal-check-invoice-event')) {
								if ($("[name='member_id']").length) {
									$("[name='member_id']").remove();
									$("[name='event_id']").remove();
								}
								$('#form2').append('<input type="hidden" name="member_id" value="'+ret.event_participant_id+'">');
								$('#form2').append('<input type="hidden" name="event_id" value="'+ret.event_id+'">');
							}
							if (ret.payment_active == 1) {
								$('input#note')           .attr('disabled','disable');
								$('input#no_anggota')	  .attr('disabled','disable');
							}else{
								$('input#note')           .removeAttr('disabled');
								$('input#no_anggota')	  .removeAttr('disabled');
							}

							if (ret.is_bank_transfer) {
								$('input#note')           .parent().show();
								$('.inputfile-6').parent().parent().show()
							}else{
								$('.inputfile-6').parent().parent().hide()
								$('input#note')           .parent().hide();
							}
							$('input#invoice_number')	 .val(ret.invoice_number);
							$('input#note')          	 .val(ret.note);
							$('input#no_anggota')	 	 .val(ret.no_anggota);
							// $('input#expired_date')	 	 .val(ret.expired_date);
							$('input#modal-id-member')	 .val(ret.member_id);
							
							// disable no anggota saat ingin approve invoice renew
							// if (ret.disable_expired) {
							// 	$('input#no_anggota').attr('readonly',true);
							// }else{
							// 	$('input#no_anggota').attr('readonly',false);
							// }

							// hide tombol approve jika user sudah aktif
							if (ret.dsp_btn_approve) {
								$(".btn-submit-konten").addClass('invis');
							}else{
								$(".btn-submit-konten").removeClass('invis');
							}

							// disable expired untuk invoice bukan expireds
							if (ret.dsp_expired_date){
								// $('#input_expired_date').hide();
								// $('#input_expired_date').attr('disabled',false);
								// $('input#expired_date').attr('required',false);
							}else{
								// $('#input_expired_date').show();
								// $('#input_expired_date').attr('disabled',true);
								// $('input#expired_date').attr('required',true);
							}

							$('input#user_id')		 .val(ret.user_id);
							if (ret.filename == ''){
	    						$('.box').show();
	    						// $('.inputfile').attr('required','');
	        					$('.invoice_img').hide();
								$('.inputfile-6').next( 'label' ).find( 'span' ).html('');
	    					}else{
	    						// $('.inputfile').removeAttr('required','');
	        					$('.box').hide();
	        					$('.invoice_img').show().find('img').attr('src',ret.filename);
								$('.inputfile-6').next( 'label' ).find( 'span' ).html(ret.filename);
	    					}

							if (ret.is_sent) {
								$('.savedraft').addClass('invis');
							}else{
								$('.savedraft').removeClass('invis');
							}
							if (ret.is_paid == 2) {
								$('#invoice_detail').hide();
							}else{
								$('#invoice_detail').show();
							}
							
							// window.location.href=this_controller + back_url;
							// clear_form_elements('#form1');
							$('#modal-check-invoice').modal('show');
						}		
		}
	})
});
/*$(document).on('click','a.modal-send-invoice-expired',function(){
  var member_id = $(this).attr('data-id'); 
  $('#modal-id-member').val(member_id);  
  // $('#modal-send-invoice').modal('show');

	$.ajax({
		url         : base_url+'apps/member/invoice_check/'+member_id,
		type        : "POST",
		dataType	: 'json',
		// data        : $('#form1').serialize(),
		// data        : formData,
		processData: false,
		contentType: false,
		error		: function () {
						notify('error!');
						// clear_form_elements('#form1');
						// $('#modal-send-invoice').modal('hide')
						loadingcomplete();
					},
		success     : function(ret){
						if (ret.error==1) {
							$.gritter.add({title:page_name,text:ret.message});
							// $('#save-schedule').modal('hide')
							loadingcomplete();
						}else{
							$('[name="invoice_number"]').val(ret.invoice_number);
							$('.inputfile-6').next( 'label' ).find( 'span' ).html(ret.filename);
							if (ret.is_sent) {
								$('.savedraft').addClass('invis');
							}else{
								$('.savedraft').removeClass('invis');
							}
							
							// window.location.href=this_controller + back_url;
							// clear_form_elements('#form1');
							$('#modal-send-invoice').modal('show');
						}		
		}
	})
});*/

$(document).on('click','#btn-submit-konten,.btn-submit-konten',function(){
	var form_url = $(this).closest('form').attr('action');	
	var close_modal = 'modal-send-invoice';
	var id_form = $(this).closest('form').attr('id');

	// console.log(id_form);
	// console.log(last_url);
	// console.log(close_modal);


	if ($(this).hasClass('savedraft')){
		var last_url = $('#'+id_form).attr('action',form_url + "/1");
	}
	
    if ($('#'+id_form).parsley().validate()) {        	
      $(this).attr('Disabled',true);
      submit_form(id_form,last_url,close_modal);
    }
    return false;
});

function submit_form(id_form,last_url,close_modal) {    
// var back_url = $(this).attr('data-back') || '';
	$('#'+id_form).ajaxSubmit({
	  url       : $('#'+id_form).attr('action'),
	  type      : 'post',
	  dataType    : 'json',
	  error: function () {
	  	$('form').find('a[disabled]').removeAttr('Disabled');
	    alert('error');
	  },
	  success     : function(ret){
	  	$('#'+id_form).find('a[disabled]').removeAttr('Disabled');

	  	if (typeof last_url != 'undefined'){
	        $('#'+id_form).attr('action',last_url);
	    }

	    if (ret.error == 1 ) {
	      if(typeof ret.msg != 'undefined'){
	        $.gritter.add({title:page_name,text:ret.msg});
	      }
	      if(typeof ret.modalname != 'undefined'){
	        $('#'+ret.modalname).modal('show');
	      }
	      if (typeof ret.redirect != 'undefined'){
	        window.location.href = ret.redirect;
	      }
	      return false;

	    }else{
	      if(typeof ret.msg != 'undefined'){
	        $.gritter.add({title:page_name,text:ret.msg});
	      }

	      if(typeof ret.modalname != 'undefined'){
	        $('#'+ret.modalname).modal('show');
	      }

	      if (typeof close_modal != 'undefined'){
	       	$('#'+close_modal).modal('hide'); 
	      }
	      if (typeof ret.close_modal != 'undefined'){
	       	$('#'+ret.close_modal).modal('hide'); 
	      }

	      if (typeof ret.call_function != 'undefined'){
	      	
	      	if (ret.call_function == "refresh_price") {
	      		$("#event_price0").select2({
	      			tags: [ret.price],
	      			tokenSeparators: [","],
	      			createTag: function (params) {
	      				return null ;
	      			}
	      		})
	      	}
	      	
	      }
	      if (typeof ret.no_reload != 'undefined'){
	      	return false;
	      }else{
	      	setTimeout(function reload(){location.reload();},2000);
	      }

	    }
	}
	});
}



/*$(document).on('click','a.modal-send-invoice-extend',function(){
	var member_id = $(this).attr('data-id'); 
	$('#modal-id-member').val(member_id);  
	$('#modal-send-invoice-extend').modal('show');
});

$(document).on('click','a.modal-check-invoice-extend',function(){
	var member_id = $(this).attr('data-id'); 
  	$('#modal-id-member').val(member_id);  

	$.ajax({
		url         : base_url+'apps/payment_confirmation/invoice_check/'+member_id,
		type        : "POST",
		dataType	: 'json',
		processData: false,
		contentType: false,
		error		: function () {
						notify('error!');
						loadingcomplete();
					},
		success     : function(ret){
						if (ret.error==1) {
							$.gritter.add({title:page_name,text:ret.message});
							loadingcomplete();
						}else{
							$('input#invoice_number').val(ret.invoice_number);
							$('input#note').val(ret.note);
							$('input#user_id').val(ret.user_id);
							if (ret.filename == ''){
	    						$('.box').show();
	        					$('.invoice_img').hide();
	    					}else{
	        					$('.box').hide();
	        					$('.invoice_img').show().find('img').attr('src',ret.filename);
	    					}

							if (ret.is_sent) {
								$('.savedraft').addClass('invis');
							}else{
								$('.savedraft').removeClass('invis');
							}
							$('#modal-check-invoice-extend').modal('show');
						}		
		}
	})
});
*/
$(document).on('click','.btn-filter-date-unhide',function(e){
	$('#filter-date').removeClass('hidden');

	// remove current class
	$(this).removeClass('btn-filter-date-unhide');

	// add new class
	$(this).addClass('btn-filter-date-hide');
});

$(document).on('click','.btn-filter-date-hide',function(e){
	$('#filter-date').addClass('hidden');

	// remove current class
	$(this).removeClass('btn-filter-date-hide');

	// add new class
	$(this).addClass('btn-filter-date-unhide');
});

$(document).on("keyup",".upper",function(){
     this.value = this.value.toUpperCase();
});

function readonly_select(objs, action) {
    if (action===true)
    	$('[aria-labelledby=select2-'+objs+'-container]').parent().prepend('<div class="disabled-select"></div>');
    else
        $(".disabled-select", $('[aria-labelledby=select2-'+objs+'-container]').parent()).remove();
	}


$(window).bind('resize', function(e)
{
	// $('iframe').find('img').height();
	// console.log('window resized..');
	// this.location.reload(false); /* false to get page from cache */
	/* true to fetch page from server */
});

// jquery number
!function(e){"use strict";function t(e,t){if(this.createTextRange){var a=this.createTextRange();a.collapse(!0),a.moveStart("character",e),a.moveEnd("character",t-e),a.select()}else this.setSelectionRange&&(this.focus(),this.setSelectionRange(e,t))}function a(e){var t=this.value.length;if(e="start"==e.toLowerCase()?"Start":"End",document.selection){var a,i,n,l=document.selection.createRange();return a=l.duplicate(),a.expand("textedit"),a.setEndPoint("EndToEnd",l),i=a.text.length-l.text.length,n=i+l.text.length,"Start"==e?i:n}return"undefined"!=typeof this["selection"+e]&&(t=this["selection"+e]),t}var i={codes:{46:127,188:44,109:45,190:46,191:47,192:96,220:92,222:39,221:93,219:91,173:45,187:61,186:59,189:45,110:46},shifts:{96:"~",49:"!",50:"@",51:"#",52:"$",53:"%",54:"^",55:"&",56:"*",57:"(",48:")",45:"_",61:"+",91:"{",93:"}",92:"|",59:":",39:'"',44:"<",46:">",47:"?"}};e.fn.number=function(n,l,s,r){r="undefined"==typeof r?",":r,s="undefined"==typeof s?".":s,l="undefined"==typeof l?0:l;var u="\\u"+("0000"+s.charCodeAt(0).toString(16)).slice(-4),h=new RegExp("[^"+u+"0-9]","g"),o=new RegExp(u,"g");return n===!0?this.is("input:text")?this.on({"keydown.format":function(n){var u=e(this),h=u.data("numFormat"),o=n.keyCode?n.keyCode:n.which,c="",v=a.apply(this,["start"]),d=a.apply(this,["end"]),p="",f=!1;if(i.codes.hasOwnProperty(o)&&(o=i.codes[o]),!n.shiftKey&&o>=65&&90>=o?o+=32:!n.shiftKey&&o>=69&&105>=o?o-=48:n.shiftKey&&i.shifts.hasOwnProperty(o)&&(c=i.shifts[o]),""==c&&(c=String.fromCharCode(o)),8!=o&&45!=o&&127!=o&&c!=s&&!c.match(/[0-9]/)){var g=n.keyCode?n.keyCode:n.which;if(46==g||8==g||127==g||9==g||27==g||13==g||(65==g||82==g||80==g||83==g||70==g||72==g||66==g||74==g||84==g||90==g||61==g||173==g||48==g)&&(n.ctrlKey||n.metaKey)===!0||(86==g||67==g||88==g)&&(n.ctrlKey||n.metaKey)===!0||g>=35&&39>=g||g>=112&&123>=g)return;return n.preventDefault(),!1}if(0==v&&d==this.value.length?8==o?(v=d=1,this.value="",h.init=l>0?-1:0,h.c=l>0?-(l+1):0,t.apply(this,[0,0])):c==s?(v=d=1,this.value="0"+s+new Array(l+1).join("0"),h.init=l>0?1:0,h.c=l>0?-(l+1):0):45==o?(v=d=2,this.value="-0"+s+new Array(l+1).join("0"),h.init=l>0?1:0,h.c=l>0?-(l+1):0,t.apply(this,[2,2])):(h.init=l>0?-1:0,h.c=l>0?-l:0):h.c=d-this.value.length,h.isPartialSelection=v==d?!1:!0,l>0&&c==s&&v==this.value.length-l-1)h.c++,h.init=Math.max(0,h.init),n.preventDefault(),f=this.value.length+h.c;else if(45!=o||0==v&&0!=this.value.indexOf("-"))if(c==s)h.init=Math.max(0,h.init),n.preventDefault();else if(l>0&&127==o&&v==this.value.length-l-1)n.preventDefault();else if(l>0&&8==o&&v==this.value.length-l)n.preventDefault(),h.c--,f=this.value.length+h.c;else if(l>0&&127==o&&v>this.value.length-l-1){if(""===this.value)return;"0"!=this.value.slice(v,v+1)&&(p=this.value.slice(0,v)+"0"+this.value.slice(v+1),u.val(p)),n.preventDefault(),f=this.value.length+h.c}else if(l>0&&8==o&&v>this.value.length-l){if(""===this.value)return;"0"!=this.value.slice(v-1,v)&&(p=this.value.slice(0,v-1)+"0"+this.value.slice(v),u.val(p)),n.preventDefault(),h.c--,f=this.value.length+h.c}else 127==o&&this.value.slice(v,v+1)==r?n.preventDefault():8==o&&this.value.slice(v-1,v)==r?(n.preventDefault(),h.c--,f=this.value.length+h.c):l>0&&v==d&&this.value.length>l+1&&v>this.value.length-l-1&&isFinite(+c)&&!n.metaKey&&!n.ctrlKey&&!n.altKey&&1===c.length&&(p=d===this.value.length?this.value.slice(0,v-1):this.value.slice(0,v)+this.value.slice(v+1),this.value=p,f=v);else n.preventDefault();f!==!1&&t.apply(this,[f,f]),u.data("numFormat",h)},"keyup.format":function(i){var n,s=e(this),r=s.data("numFormat"),u=i.keyCode?i.keyCode:i.which,h=a.apply(this,["start"]),o=a.apply(this,["end"]);0!==h||0!==o||189!==u&&109!==u||(s.val("-"+s.val()),h=1,r.c=1-this.value.length,r.init=1,s.data("numFormat",r),n=this.value.length+r.c,t.apply(this,[n,n])),""===this.value||(48>u||u>57)&&(96>u||u>105)&&8!==u&&46!==u&&110!==u||(s.val(s.val()),l>0&&(r.init<1?(h=this.value.length-l-(r.init<0?1:0),r.c=h-this.value.length,r.init=1,s.data("numFormat",r)):h>this.value.length-l&&8!=u&&(r.c++,s.data("numFormat",r))),46!=u||r.isPartialSelection||(r.c++,s.data("numFormat",r)),n=this.value.length+r.c,t.apply(this,[n,n]))},"paste.format":function(t){var a=e(this),i=t.originalEvent,n=null;return window.clipboardData&&window.clipboardData.getData?n=window.clipboardData.getData("Text"):i.clipboardData&&i.clipboardData.getData&&(n=i.clipboardData.getData("text/plain")),a.val(n),t.preventDefault(),!1}}).each(function(){var t=e(this).data("numFormat",{c:-(l+1),decimals:l,thousands_sep:r,dec_point:s,regex_dec_num:h,regex_dec:o,init:this.value.indexOf(".")?!0:!1});""!==this.value&&t.val(t.val())}):this.each(function(){var t=e(this),a=+t.text().replace(h,"").replace(o,".");t.number(isFinite(a)?+a:0,l,s,r)}):this.text(e.number.apply(window,arguments))};var n=null,l=null;e.isPlainObject(e.valHooks.text)?(e.isFunction(e.valHooks.text.get)&&(n=e.valHooks.text.get),e.isFunction(e.valHooks.text.set)&&(l=e.valHooks.text.set)):e.valHooks.text={},e.valHooks.text.get=function(t){var a,i=e(t),l=i.data("numFormat");return l?""===t.value?"":(a=+t.value.replace(l.regex_dec_num,"").replace(l.regex_dec,"."),(0===t.value.indexOf("-")?"-":"")+(isFinite(a)?a:0)):e.isFunction(n)?n(t):void 0},e.valHooks.text.set=function(t,a){var i=e(t),n=i.data("numFormat");if(n){var s=e.number(a,n.decimals,n.dec_point,n.thousands_sep);return e.isFunction(l)?l(t,s):t.value=s}return e.isFunction(l)?l(t,a):void 0},e.number=function(e,t,a,i){i="undefined"==typeof i?"1000"!==new Number(1e3).toLocaleString()?new Number(1e3).toLocaleString().charAt(1):"":i,a="undefined"==typeof a?new Number(.1).toLocaleString().charAt(1):a,t=isFinite(+t)?Math.abs(t):0;var n="\\u"+("0000"+a.charCodeAt(0).toString(16)).slice(-4),l="\\u"+("0000"+i.charCodeAt(0).toString(16)).slice(-4);e=(e+"").replace(".",a).replace(new RegExp(l,"g"),"").replace(new RegExp(n,"g"),".").replace(new RegExp("[^0-9+-Ee.]","g"),"");var s=isFinite(+e)?+e:0,r="",u=function(e,t){return""+ +(Math.round((""+e).indexOf("e")>0?e:e+"e+"+t)+"e-"+t)};return r=(t?u(s,t):""+Math.round(s)).split("."),r[0].length>3&&(r[0]=r[0].replace(/\B(?=(?:\d{3})+(?!\d))/g,i)),(r[1]||"").length<t&&(r[1]=r[1]||"",r[1]+=new Array(t-r[1].length+1).join("0")),r.join(a)}}(jQuery);
//# sourceMappingURL=jquery.number.min.js.map

$(document).ready(function(){
	$('.number').number( true, 0 )
})
