//全局脚本

function TComm(){
    this.sys_pop_info_txt = '';
    this.sys_pop_help_txt = '';
    this.sys_pop_important_txt = '';
    this.sys_file_server = '';
	
    this.IsShowTip = false;
    this.ShowTipObj = null;
	this.SelectedLang = null;
	this.IsShowLang = false;
}

// 初始化
TComm.prototype.ini = function(){

   this.Lang($('#lang_box'),this.SelectedLang);
};

// 显示一个提示框
TComm.prototype.ShowTip = function(sObj, sHTML){
    if (!this.IsShowTip) {
        if (!$('#' + sObj.id + '_tip')[0]) {
            $('body').append('<div id="' +
            sObj.id +
            '_tip" class="sub-nav chat-bubble"><div id="' +
            sObj.id +
            '_tip_content"></div><div class="chat-bubble-arrow-border"></div><div class="chat-bubble-arrow"></div></div>');
        }
        this.ShowTipObj = $('#' + sObj.id + '_tip');
        this.ShowTipObj.hover(function(e){
            Comm.IsShowTip = true;
        }, function(e){
            Comm.IsShowTip = false;
        });
        $('#' + this.ShowTipObj.attr('id') + '_content').html(sHTML);
        var offset = $(sObj).offset();
        this.ShowTipObj.offset({
            top: offset.top + $(sObj).height() + 10,
            left: offset.left + $(sObj).width() / 2 - this.ShowTipObj.width()
        });
        this.ShowTipObj.show();
        this.IsShowTip = true;
        offset = null;
    }
};
//语言切换
TComm.prototype.Lang = function(sObj,Selected){
	var Lang = [{lang:'简体中文',code:'zh'},{lang:'English',code:'en'}];
	if(sObj){
		var _html = '';
		var _SelectedHtml = Lang[0].lang;
		var _t = this.GetRequest();
		for(var i=0;i<Lang.length;i++){
			_url = _t;
			_url['lang']=Lang[i].code;
			
			if (Lang[i].code == Selected) {
				_SelectedHtml = Lang[i].lang;
			}
			
			_html += '<li><a href="?' + this.http_build_query(_url) + '">' + Lang[i].lang + '</a></li>';
			
		}
		_html = '<a href="javascript:void(0);" id="LangTxt">'+_SelectedHtml+ '</a> | <div id="LangBar">'+_html+'</div>';
		$(sObj).html(_html);
		$('#LangBar').hide();
		$(sObj).click(function(){
			if (Comm.IsShowLang) {
				$('#LangBar').hide();
				Comm.IsShowLang = false;
			}
			else {
				Comm.IsShowLang = true;
				var _lboxOffset = $('#LangTxt').offset();
				var _top = _lboxOffset.top-35;
				var _left = _lboxOffset.left-$('#LangBar').width()/2;
				$('#LangBar').css({'top':_top+'px','left':_left+'px','position': 'absolute','z-index':999});
				$('#LangBar').show('normal').delay(5000).hide('normal',function(){
					Comm.IsShowLang = false;
				});
			}
		});
	}
}
//取url参数
TComm.prototype.GetRequest = function() {

   var url = location.search; //获取url中"?"符后的字串
   var theRequest = new Array();

   if (url.indexOf("?") != -1) {

      var str = url.substr(1);
      strs = str.split("&");

      for(var i = 0; i < strs.length; i ++) {
         theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
      }

   }

   return theRequest;

}
//生成url
TComm.prototype.http_build_query = function(_params){
	var _re = '';
	if(_params)
	{
		for(key in _params){
			 value = _params[key];
			 _re += key+'='+value+'&';
		}
	}
	_re = _re.substring(0,_re.length-1)
	return _re;
}

/*图标控件
 sObj=显示对象
 IcoSize=[16,32,64,128]
 IcoImg=[jpg,]
 BoxSize=默认宽大小等于0时根据IcoSize设定
 Fun=[个数与IcoSize相同]指定回调函数,Fun(ImgFileCode)
 */
TComm.prototype.IcoTool = function(sObj, IcoSize, IcoImg, BoxSize, Fun){
    var ico_tool_tag = '_icotool_';
    var ico_tool_name = $(sObj).attr('id') + ico_tool_tag;
    var _html = '';
    var _img = '';
    var _imghtml = '';
    var _txt = '';
    BoxSize = (BoxSize) ? BoxSize : 0;
    Fun = (Fun) ? Fun : null;
    if (sObj && IcoSize) {
        for (var _size in IcoSize) {
            _img = (IcoImg ? IcoImg[_size] : '');
            _txt = BoxSize != 0 ? '<div class="box_bar">' + IcoSize[_size] + 'x' + IcoSize[_size] + '</div>' : '';
            _imghtml = (_img ? 'style="background-color:#fff;background-image:url(' + this.FileCode2Url(_img, (BoxSize != 0) ? BoxSize : IcoSize[_size]) + ')" filecode="' + _img + '"' : '');
            _html += '<li  alt="' + IcoSize[_size] + 'x' + IcoSize[_size] + '" id="' + ico_tool_name + IcoSize[_size] + '" class="ico_' + ((BoxSize != 0) ? BoxSize + '_nt' : IcoSize[_size]) + '"><div id="' + ico_tool_name + IcoSize[_size] + '_box" class="box_img_bar"><div ' + _imghtml + ' id="' + ico_tool_name + IcoSize[_size] + '_img" size="' + IcoSize[_size] + '" showsize="' + BoxSize + '"></div></div>' + _txt + '</li>';
            _img = null;
            _imghtml = null;
            
            if (IcoImg[_size]) {
                if (Fun) {
                    var _fun = Fun[IcoSize.indexOf(IcoSize[_size])];
                    if (_fun) {
                        _fun(IcoImg[_size]);
                    }
                }
            }
        }
        _html = '<ul id="' + ico_tool_name + '" class="ico_tool">' + _html + '</ul>';
        $(sObj).html(_html);
        
        for (var _size in IcoSize) {
            $.jUploader({
                button: $('#' + ico_tool_name + IcoSize[_size] + '_img'), // 这里设置按钮id
                action: '/file/Up', // 这里设置上传处理接口
                // 上传开始
                onUpload: function(fileName){
                    $.fancybox.showActivity();//显示loading提示框
                },
                // 上传完成事件
                onComplete: function(fileName, response){
                    $.fancybox.hideActivity();//关闭loading提示框
                    var thisID = $(this.button).attr('id');
                    
                    if (response.state) {
                        var _img = $('#' + thisID);
                        _img.attr('filecode', response.data.filecode);
                        _img.css({
                            'background-color': '#fff',
                            'background-image': 'url(' + Comm.FileCode2Url(response.data.filecode, Number($(_img).attr('showsize')) != 0 ? $(_img).attr('showsize') : $(_img).attr('size')) + ')'
                        });
                        
                        if (Fun) {
                        
                            var _fun = Fun[IcoSize.indexOf(Number($(_img).attr('size')))];
                            if (_fun) {
                                _fun(response.data.filecode);
                            }
                        }
                        _img = null;
                    }
                    else {
                        jAlert(response.msg, Comm.POP_Txt.info);
                    }
                    thisID = null;
                }
            });
        }
    }
    
    
    //返回图标列表
    this.getIco = function(){
        var re = [];
        var ico = $('#' + ico_tool_name).children('li').children('div').children('div');
        for (i = 0; i < ico.length; i++) {
            if ($(ico[i]).attr('filecode')) {
                re[i] = $(ico[i]).attr('filecode') + '|' + $(ico[i]).attr('size');
            }
        }
        return re;
    }
    
    return this;
}
//IcoCode转数组,[filecode|size]
TComm.prototype.IcoCode2Array = function(fileCode){
    if (fileCode) {
        var _f = fileCode.split(',');
        for (var i = 0; i < _f.length; i++) {
            if (_f[i]) {
                var _ff = _f[i].split('|');
                if (_ff) {
                    _f[i] = {
                        'filecode': _ff[0],
                        'size': _ff[1]
                    };//_f[i].split('|');
                }
            }
        }
        return _f;
    }
    else {
        return null;
    }
}
//文件识别码转url
TComm.prototype.FileCode2Url = function(fileCode, imgSize){
    return $.sprintf(this.sys_file_server, fileCode, imgSize);
}
//显示正在加载信息框
TComm.prototype.ShowLoading = function(){
    $.fancybox({
        'modal': false,
        'overlayShow': true,
        'hideOnOverlayClick': false,
        'hideOnContentClick': false,
        'enableEscapeButton': false,
        'showCloseButton': false,
        'centerOnScroll': true,
        'autoScale': false,
        'width': 250,
        'height': 50,
        'content': '<div class="loading"><div class="throbber"></div><div>' + this.POP_Txt.loading + '</div></div>'
    });
}
//隐藏正在加载信息框
TComm.prototype.CloseLoading = function(){
    $.fancybox.close();
}
//表单验证
TComm.prototype.formReg = function(sObj, reg){
    var _msg = $(sObj).attr('msg');
    if (reg.exec($(sObj).val())) {
        return true;
    }
    else {
        jAlert(_msg, Comm.POP_Txt.info);
        return false;
    }
};
TComm.prototype.formatDate = function(now){   
	var year=now.getYear();
	var month=now.getMonth()+1;
	var date=now.getDate();
	var hour=now.getHours();
	var minute=now.getMinutes();
	var second=now.getSeconds();
	return year+"-"+month+"-"+date+" "+hour+":"+minute+":"+second;
}   
jQuery.fn.extend({
    slideRightShow: function(){
        return this.each(function(){
            $(this).show('slide', {
                direction: 'right'
            }, 1000);
        });
    },
    slideLeftHide: function(){
        return this.each(function(){
            $(this).hide('slide', {
                direction: 'left'
            }, 1000);
        });
    },
    slideRightHide: function(){
        return this.each(function(){
            $(this).hide('slide', {
                direction: 'right'
            }, 1000);
        });
    },
    slideLeftShow: function(){
        return this.each(function(){
            $(this).show('slide', {
                direction: 'left'
            }, 1000);
        });
    }
});


[].indexOf ||
(Array.prototype.indexOf = function(v){
    for (var i = this.length; i-- && this[i] !== v;) 
        ;
    return i;
});
function binarySearch(array, x){
    var lowPoint = 1;
    var higPoint = array.length;
    var returnValue = -1;
    var midPoint;
    var found = false;
    while ((lowPoint <= higPoint) && (!found)) {
        midPoint = Math.ceil((lowPoint + higPoint) / 2);
        
        if (x > array[midPoint - 1]) {
            lowPoint = midPoint + 1;
        }
        else if (x < array[midPoint - 1]) {
            higPoint = midPoint - 1;
        }
        else if (x = array[midPoint - 1]) {
            found = true;
        }
        
    }
    if (found) {
        returnValue = midPoint;
    }
    return returnValue;
}

/**     
* 对Date的扩展，将 Date 转化为指定格式的String     
* 月(M)、日(d)、12小时(h)、24小时(H)、分(m)、秒(s)、周(E)、季度(q) 可以用 1-2 个占位符     
* 年(y)可以用 1-4 个占位符，毫秒(S)只能用 1 个占位符(是 1-3 位的数字)     
* eg:     
* (new Date()).pattern("yyyy-MM-dd hh:mm:ss.S") ==> 2006-07-02 08:09:04.423     
* (new Date()).pattern("yyyy-MM-dd E HH:mm:ss") ==> 2009-03-10 二 20:09:04     
* (new Date()).pattern("yyyy-MM-dd EE hh:mm:ss") ==> 2009-03-10 周二 08:09:04     
* (new Date()).pattern("yyyy-MM-dd EEE hh:mm:ss") ==> 2009-03-10 星期二 08:09:04     
* (new Date()).pattern("yyyy-M-d h:m:s.S") ==> 2006-7-2 8:9:4.18     
*/       
Date.prototype.pattern=function(fmt) {        
    var o = {        
    "M+" : this.getMonth()+1, //月份        
    "d+" : this.getDate(), //日        
    "h+" : this.getHours()%12 == 0 ? 12 : this.getHours()%12, //小时        
    "H+" : this.getHours(), //小时        
    "m+" : this.getMinutes(), //分        
    "s+" : this.getSeconds(), //秒        
    "q+" : Math.floor((this.getMonth()+3)/3), //季度        
    "S" : this.getMilliseconds() //毫秒        
    };        
    var week = {        
    "0" : "\u65e5",        
    "1" : "\u4e00",        
    "2" : "\u4e8c",        
    "3" : "\u4e09",        
    "4" : "\u56db",        
    "5" : "\u4e94",        
    "6" : "\u516d"       
    };        
    if(/(y+)/.test(fmt)){        
        fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));        
    }        
    if(/(E+)/.test(fmt)){        
        fmt=fmt.replace(RegExp.$1, ((RegExp.$1.length>1) ? (RegExp.$1.length>2 ? "\u661f\u671f" : "\u5468") : "")+week[this.getDay()+""]);        
    }        
    for(var k in o){        
        if(new RegExp("("+ k +")").test(fmt)){        
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));        
        }        
    }        
    return fmt;        
}       

var Comm = new TComm();

// 页面完全再入后初始化
$(document).ready(function(){
    
    Comm.POP_Txt = pop_txt;
	Comm.SelectedLang = lang;
    $.alerts.okButton = pop_txt.ok;
    $.alerts.cancelButton = pop_txt.cancel;
    $.alerts.yesButton = pop_txt.yes;
    $.alerts.noButton = pop_txt.no;
    
    Comm.ini();
});

//释放
$(window).unload(function(){
    Comm = null;
});
