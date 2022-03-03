function the_grid(grid_id,url_grid,per_page,order_id,order_direction,page,next){
    grid_id         = (grid_id)            	 	? '#'+grid_id       : '#myGrid';
    per_page        = (per_page)            	? per_page          : 10;
    page            = (typeof page =='number')  ? page              : 0;
    order_id        = (order_id)            	? order_id          : 'a.id';
    next            = (next)            		? next        		: '';
    order_direction = (order_direction)     	? order_direction   : 'desc';
    default_perpage = per_page;
    
	function my_grid(id, next){ // id = id field yg di sort
		var s_val;
		var s_field;
		var s_url		= '';
		var kelas 		= $('#'+id +' span').attr('class');
		if (kelas) {
            var sort_type = (kelas == 'fa fa-sort-desc float-right') ? 'asc' : 'desc';
        } else {
            var sort_type = order_direction;
        }
		var new_class 	= (kelas == 'fa fa-sort-desc float-right') ? 'fa fa-sort-asc float-right' : 'fa fa-sort-desc float-right';
		$(grid_id+' thead tr').find('span').removeClass('sort fa fa-sort-asc float-right fa fa-sort-desc');
		$('#'+id +' span').addClass(new_class);
		$(grid_id).find('.cari').each(function() {
			search_data_id  = $(this).attr('id').split('&');
			search_data_val = $(this).val();
			for (i = 0; i < search_data_id.length; ++i) {
				s_val 		 = search_data_val;
				s_field		 = search_data_id[i];
				s_url 		+= '&'+s_field+'='+s_val;
			}
		})
        s_url           += '&perpage='+per_page+'&sort_field='+id+'&sort_type='+sort_type;
	    if (next) {
		load_data(next);
		check_localstorage_search(grid_id);
	    } else {
		load_data(url_grid+'/'+page+'?page='+page+s_url);
	    }
	}
	function check_localstorage_search(grid_id){
	    var array_field_select = [];
	    $(grid_id).find('.cari').each(function() {
		s_val 		 = $(this).val();
		s_field		 = $(this).attr('id');
		s_type		 = this.type;
		s_data 		 = localStorage[s_field];
		if (s_data) {
		    $('#'+s_field).val(s_data);
		    if (s_type=='select-one') {
			array_field_select.push(s_field);
		    }
		}
	    });
	    array_field_select.forEach(function(item) {
		$('#'+item).trigger("change");
	    });
	}
    //header table di klik
	$(grid_id+' thead tr th').click(function(){
		var id 			= $(this).attr('id');
		var is_sort		= $(this).attr('title');
		if(is_sort !='Sort') return false;
		my_grid(id);
		
	})
    
	//tambahin class sort utk kolom yg mau di sort
	$(grid_id+' thead tr').find('th').each(function() {
		if($(this).attr('title')=='Sort'){
			$(this).addClass('sort');
		}
	})
        
	//reset value pencarian on refresh
	$(grid_id).find('.cari').val('');
	
	//pencarian
	function cari(){
				var val;
				var key;
				var kelas;
				var id;
				var url 		 = '';
				$(grid_id).find('.cari').each(function() {
					search_data_id = $(this).attr('id').split('&');
					search_data_val = $(this).val();
					for (i = 0; i < search_data_id.length; ++i) {
						s_val 		 = search_data_val;
						s_field		 = search_data_id[i];
						url 		+= '&'+s_field+'='+s_val;
						localStorage.setItem(s_field, search_data_val);
					}
				});
				sort_field      = kolom_sort();
				sort_type       = kolom_type();
				url             += '&perpage='+per_page+'&sort_field='+sort_field+'&sort_type='+sort_type;
				var next_data   = url_grid+'?&page=0'+url;
				var recent_data = url_grid.split(/[\s,/]+/);
				var recent_page = recent_data[recent_data.length-1];
				load_data(next_data);
				var name_local_storage = recent_data[recent_data.length-2]+'/'+recent_data[recent_data.length-1];
				localStorage.setItem(name_local_storage, recent_page+'-'+next_data);
	}
		$('.cari').keypress(function (e) {
			if (e.which == 13) {
				cari();
			}
		});
		$("[id^='filter_data']").click(function () {
			cari();
		});
		$("#filter_data").click(function () {
			cari();
		});
		$('select.cari').change(function(){
			cari();
		});
		var timer;
		$(".cari").on("keyup", function(e) {
			clearTimeout(timer);
			var ms = 500; // milliseconds
			var val = this.value;
			timer = setTimeout(function() {
			  cari();
			}, ms);
		
		});
		$(document).on('click','.ui-datepicker-close',function(){
			cari();
		})

    function kolom_sort(){
        var ret='';
        var kelas ='';
        var hasil='';
        $(grid_id+' thead tr th').each(function() {
            if($(this).attr('title')=='Sort'){
                ret = ($(this).attr('id'));
                $(this).find('span').each(function() {
                    kelas = $(this).attr('class');
                    if(kelas =='ui-icon ui-icon-carat-1-s' || kelas == 'ui-icon ui-icon-carat-1-n'){
                        hasil = ret;
                    }
                })
            }
        })
        return (hasil) ? hasil : order_id ;
    }
	function kolom_type(){
	var ret   = '';
	var kelas = '';
	var hasil ='';
    $(grid_id+' thead tr th').each(function() {
        if($(this).attr('title')=='Sort'){
            ret = ($(this).attr('id'));
            $(this).find('span').each(function() {
                kelas = $(this).attr('class');
                if(kelas =='fa fa-sort-desc float-right'){
                    hasil  = 'desc';
                }
                else if(kelas == 'fa fa-sort-asc float-right'){
                    hasil  = 'asc';
                }
            })
        }
    })
    return (hasil) ? hasil : order_direction ;
}

    function paging(){
        $('.pagination li a').click(function(){
            var url     = $(this).attr('href');
            var s_url   = '';
	    var recent_data = url.split(/[\s,/]+/);
	    var recent_page = recent_data[recent_data.length-1];
            if(url){
                $(grid_id).find('.cari').each(function() {
                    s_val 		 = $(this).val();
                    s_field		 = $(this).attr('id');
                    s_url 		+= '&'+s_field+'='+s_val;
		    localStorage.setItem(s_field, s_val);
                });
				var urls      = $(this).attr('href').split('/');
				var page      = urls.pop();
				page          = (page) ? page : 0;
				sort_field    = kolom_sort();
				sort_type     = kolom_type();
				var next_data = url+'?page='+page+'&perpage='+per_page+'&sort_field='+sort_field+'&sort_type='+sort_type+s_url;
                load_data(next_data);
		var name_local_storage = recent_data[recent_data.length-3]+'/'+recent_data[recent_data.length-2];
		localStorage.setItem(name_local_storage, recent_page+'-'+next_data);
            }
            return false;
        })
    }
	var ajx;
    function load_data(url){
        $(grid_id+' tbody').html('<tr><td colspan="100" class="center"><br>'+loadingBtn+'</br></br></td></tr>');
        if (ajx) {
			ajx.abort();
		}
        ajx = $.ajax({
            url		: url+'&'+Math.random(),
			error:function(err){
						var error = err.status + ' '+err.statusText;
						if (err.statusText != 'abort') {
							$(grid_id+' tbody').html('<tr><td colspan="100" class="center"><br>Error!</br>'+error+'</br></br></td></tr>');
							console.log(err);
						}
			},
            success	: function(msg){
                        $(grid_id+' tbody').html(msg);
						$(grid_id+' table tbody tr.footer td .paging-select').append('<select class="perpage span2"> \
							<optgroup label="Show per page"> \
								<option value="5">5</option>\
								<option value="10">10</option> \
								<option value="50">50</option>\
								<option value="100">100</option>\
							</optgroup>\
						</select></div>');
						$('.perpage').val(per_page)
                        paging(grid_id,per_page);
						auth_system(ai,au,ad,im);
						//refresh
						//$(grid_id +' .reload').click(function(){
						//	per_page = default_perpage;
						//	$(grid_id +' .perpage').val(default_perpage);
						//	$(grid_id).find('.cari').val('');
						//	my_grid(order_id);
						//})
						//per page
						$(grid_id +' .perpage').change(function(){
							per_page = $(this).val();
							my_grid(order_id);
						})
						
						$('.hapus').click(function(event){
							var idx = $(this).attr('id');
							var link = $(this).attr('data-url-rm');
							var base_link = $(this).attr('data-base-url');
							if(base_link) {
								this_controller = base_url+'apps/news/';
							}
							if(confirm('Delete Data ?')){
								loading();
								$.ajax({
									url 		: this_controller+link,
									data 		: 'iddel='+ idx,
									type 		: 'POST',
									success		: function(msg){
									    my_grid(order_id);
									    //alert('Data berhasil dihapus');
									    $.gritter.add({title:page_name,text:'Delete Success'});
									    loadingcomplete();
									}
								})
							}
							event.preventDefault();
						})
						$('.unblock').click(function(event){
							var idx = $(this).attr('id');
							var link = $(this).attr('data-url-rm');
							var base_link = $(this).attr('data-base-url');
							if(base_link) {
								this_controller = base_url+'apps/news/';
							}
							if(confirm('Unblock Member ?')){
								loading();
								$.ajax({
									url 		: this_controller+link,
									data 		: 'iddel='+ idx,
									type 		: 'POST',
									success		: function(msg){
									    my_grid(order_id);
									    //alert('Data berhasil dihapus');
									    $.gritter.add({title:page_name,text:'Unblock Success'});
									    loadingcomplete();
									}
								})
							}
							event.preventDefault();
						})
						$('.block').click(function(event){
							var idx = $(this).attr('id');
							var link = $(this).attr('data-url-rm');
							var base_link = $(this).attr('data-base-url');
							if(base_link) {
								this_controller = base_url+'apps/news/';
							}
							if(confirm('Block Member ?')){
								loading();
								$.ajax({
									url 		: this_controller+link,
									data 		: 'iddel='+ idx,
									type 		: 'POST',
									success		: function(msg){
									    my_grid(order_id);
									    //alert('Data berhasil dihapus');
									    $.gritter.add({title:page_name,text:'Success'});
									    loadingcomplete();
									}
								})
							}
							event.preventDefault();
						})

						//add by nda, coba
						$('.close_event').click(function(event){
							var idx = $(this).attr('id');
							var link = $(this).attr('data-url-rm');
							var base_link = $(this).attr('data-base-url');
							if(base_link) {
								this_controller = base_url+'apps/events/';
							}
							if(confirm('Close Event ?')){
								loading();
								$.ajax({
									url 		: this_controller+link,
									data 		: 'id='+ idx,
									type 		: 'POST',
									success		: function(msg){
									    my_grid(order_id);
									    //alert('Data berhasil dihapus');
									    $.gritter.add({title:page_name,text:'Event Closed'});
									    loadingcomplete();
									}
								})
							}
							event.preventDefault();
						})
						// end add by nda
						$('.perpage').select2();
	    				$('.number').number( true, 0 )
						
                    }
        });
   
    }
   
    
    
    my_grid(order_id,next);
    $(grid_id +' .perpage').val(default_perpage);
	
	$('.toggle_filter').click(function(){
		$('#filters').toggle('blind',{},500);
	})


}
	$(function () {
		//<script>the_grid('grid1',this_controller+'list_user',10);</script>
		$('body').find('.my_grid').each(function(){
			var v                  = $(this);
			var id                 = v.attr('id');
			var url                = v.attr('data-url');
			var perpage            = v.attr('data-perpage') || 10;
			var order_id           = v.attr('data-order-id') || 'a.id';
			var order_direction    = v.attr('data-order-direction') || 'desc';
			var recent_data        = url.split(/[\s,/]+/);
			var name_local_storage = recent_data[recent_data.length-2]+'/'+recent_data[recent_data.length-1];
			var current_page       = localStorage[name_local_storage];
			var form_array_data    = localStorage[name_local_storage+'-array'];
			var page               = 0;
			var last_query         = '';
			if (current_page) {
				var data_current_page = current_page.split('-');
				page                  = data_current_page[0];
				last_query            = data_current_page[1];
			}			
			the_grid(id,url,perpage,order_id,order_direction, page,last_query);
		});
		$('.nav li a').click(function(){
		    localStorage.clear();
		});
	});