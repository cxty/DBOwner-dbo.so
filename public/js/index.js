
function TIndex(){
	this.s_url = null;
}
TIndex.prototype.ini = function(){
	
	if(this.s_url){
		$('.out_box').slideDown('slow');
	}else{
		$('.out_box').hide();	
	}
	$('#sub_go').click(function(){
		$('.out_box').slideUp('slow',function(){
			$('.qrcode').html(' ');
			$('.new_url input').val(' ');
			if($('#url').val()!=''){
				$.getJSON("?format=json&url="+escape($('#url').val()),function(data){
					if(data){
						if(data.state == true ){
							$('.new_url input').val(data.data.s_url);
							$('.qrcode').html('<img src="'+data.data.qrcode+'" />');
							$('.out_box').slideDown('slow');
						}else{
							$('.new_url input').val(data.msg);
							$('.out_box').slideDown('slow');
						}
					}
				});
			}
		});
		
	});
	$('.new_url input').click(function() {
        $('.new_url input').select();
    });
}


var Index = new TIndex();

//页面完全再入后初始化
$(document).ready(function(){
	Index.s_url = _s_url;
    Index.ini();
});

//释放
$(window).unload(function(){
    Index = null;
});