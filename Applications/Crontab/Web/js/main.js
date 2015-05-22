var read_t = setInterval(function(){
	read_log();
}, 2000);
var task_t = setInterval(function(){
	get_status();
}, 2000);
port = "undefined" != typeof(port) ? ':'+port : ':'+80;

//停止读取日志
function stop_read(){
    clearInterval(read_t);
    clearInterval(task_t);
    read_log();
    get_status();
}

//定时读取日志
function read_log(){
    $.ajax({
        type: 'GET',
        cache: false,
        url: 'http://'+document.domain+port+'/read_log',
        success: function(data){
            $('#log_context').html(data);
        }
    })
}

//设置循环读取
function loop_read(){
    var time = prompt("请输入读取日志间隔秒数", '2');
    if(time != null){
        time = parseInt(time)*1000;
        read_t = setInterval(function(){
            read_log();
        }, time);
        task_t = setInterval(function(){
            get_status();
        }, time);
    }
    read_log();
    get_status();
}

//清除日志
function clear_log(){
    $.ajax({
        type: 'GET',
        cache: false,
        url: 'http://'+document.domain+port+'/clear_log',
        success: function(data){
            read_log();
        }
    })
}

//定时获取子进程状态
function get_status(){
    $.ajax({
        type: 'GET',
        cache: false,
        url: 'http://'+document.domain+port+'/get_status',
        success: function(data){
            var obj = eval("("+data+")");
            $("#jianceyemian_div").html('以下共有 <font color="green"><b>'+obj.count_file+'</font></b> 条定时任务，其中 <b><font color="red">'+obj.error_file+'</font></b> 条未启动<a href="javascript:" title="" id="server-show-b" class="on_show">&nbsp;</a>');
            var content = '';
            for(var i=0; i<obj.list_file.length; i++){
                content += '<tr class="'+(obj.list_file[i].prefix ? 'auto_cron' : '')+'">';
                content +=  '<td height="25" align="left">';
                content +=  '<p>'+obj.list_file[i].file+'</p>';
                content +=  '</td>';
                content +=  '<td align="left">';
                content +=  '<p>'+obj.list_file[i].description+'</p>';
                content +=  '</td>';
                content +=  '<td align="left">';
                if(obj.list_file[i].running){
                    content +=  '<p class="correct"></p>';
                }else{
                    content +=  '<p class="error"></p>';
                }
                content +=  '</td>';
                content +=  '<td align="left">';
                content +=  '<p>';
				
                if(obj.list_file[i].black){
                    content +=  '<input type="button" value="移出黑名单" class="con_button" onclick="remove_blacklist($(this), \''+obj.list_file[i].file+'\')"/>';
                    content +=  '<input type="button" value="删除任务" class="con_button" onclick="remove_task($(this), \''+obj.list_file[i].file+'\')"/>';
                    if(obj.list_file[i].running)
                    {
                        content +=  '<input type="button" value="停止任务" class="con_button" disabled=true onclick="stop_task($(this), \''+obj.list_file[i].file+'\')"/>';
                    }else{
						if(obj.list_file[i].prefix){
							content +=  '<input type="button" value="启动任务" class="con_button" onclick="start_task($(this), \''+obj.list_file[i].file+'\')"/>';
						}else{
							content +=  '<input type="button" value="启动任务" class="con_button" disabled=true onclick="start_task($(this), \''+obj.list_file[i].file+'\')"/>';
						}
                    }
                }else{
                    content +=  '<input type="button" value="加入黑名单" class="con_button" onclick="add_blacklist($(this), \''+obj.list_file[i].file+'\')"/>';
                    content +=  '<input type="button" value="删除任务" class="con_button" onclick="remove_task($(this), \''+obj.list_file[i].file+'\')"/>';
                    if(obj.list_file[i].running)
                    {
                        content +=  '<input type="button" value="停止任务" class="con_button" onclick="stop_task($(this), \''+obj.list_file[i].file+'\')"/>';
                    }else{
                        content +=  '<input type="button" value="启动任务" class="con_button" onclick="start_task($(this), \''+obj.list_file[i].file+'\')"/>';
                    }
                }
                content +=  '</p>';
                content +=  '</td>';
                content +=  '</tr>';
            }
            $('#con_tbody').html(content);
        }
    })
    
}

//将子进程加入黑名单
function add_blacklist(obj, file){
    $.ajax({
        type: 'GET',
        cache: false,
        url: 'http://'+document.domain+port+'/add_blacklist/'+base64encode(file),
        success: function(data){
            if(data == 'success')
            {
                read_log();
                get_status();
            }
        }
    })
}

//将子进程移出黑名单
function remove_blacklist(obj, file){
    $.ajax({
        type: 'GET',
        cache: false,
        url: 'http://'+document.domain+port+'/remove_blacklist/'+base64encode(file),
        success: function(data){
            if(data == 'success')
            {
                read_log();
                get_status();
            }
        }
    })
}

//删除定时任务
function remove_task(obj, file){
    if(confirm('确定要删除'+file+'吗？如果不想删除请使用黑名单功能，点击确定直接删除。')){
        $.ajax({
            type: 'GET',
            cache: false,
            url: 'http://'+document.domain+port+'/remove_task/'+base64encode(file),
            success: function(data){
                if(data == 'success')
                {
                    read_log();
                    get_status();
                }
            }
        })
    }
}

//启动定时任务
function start_task(obj, file){
    if(obj.parents('tr').hasClass('auto_cron')){
        if(!confirm(file+'任务为断点执行任务，确定要重置到初始值？')){
            return false;
        }
    }
    $.ajax({
        type: 'GET',
        cache: false,
        url: 'http://'+document.domain+port+'/start_task/'+base64encode(file),
        success: function(data){
            if(data == 'success'){
                setTimeout(function(){read_log();}, 1000);
                get_status();
            }
        }
    })
}

//停止子进程
function stop_task(obj, file){
    if(obj.parents('tr').hasClass('auto_cron')){
        if(!confirm(file+'任务为断点执行任务，确定后将会删除断点！')){
            return false;
        }
    }
    $.ajax({
        type: 'GET',
        cache: false,
        url: 'http://'+document.domain+port+'/stop_task/'+base64encode(file),
        success: function(data){
            if(data == 'success'){
                read_log();
                get_status();
            }
        }
    })
}

function start_all(){
    $.ajax({
        type: 'GET',
        cache: false,
        url: 'http://'+document.domain+port+'/start_all/',
        success: function(data){
            if(data == 'success'){
                setTimeout(function(){read_log();}, 1000);
                get_status();
            }
        }
    })
}

function stop_all(){
    if(confirm('确定要停止所有任务吗？部分任务需要使用黑名单功能停止')){
        $.ajax({
            type: 'GET',
            cache: false,
            url: 'http://'+document.domain+port+'/stop_all/',
            success: function(data){
                if(data == 'success'){
                    read_log();
                    get_status();
                }
            }
        })
    }
}

var base64EncodeChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/"; 
var base64DecodeChars = new Array( 
    -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 
    -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 
    -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, -1, -1, -1, 63, 
    52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1, 
    -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 
    15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1, 
    -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 
    41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1); 

function base64encode(str){ 
    var out, i, len; 
    var c1, c2, c3; 

    len = str.length; 
    i = 0; 
    out = ""; 
    while(i < len) { 
    c1 = str.charCodeAt(i++) & 0xff; 
    if(i == len){ 
        out += base64EncodeChars.charAt(c1 >> 2); 
        out += base64EncodeChars.charAt((c1 & 0x3) << 4); 
        out += "=="; 
        break; 
    } 
    c2 = str.charCodeAt(i++); 
    if(i == len){ 
        out += base64EncodeChars.charAt(c1 >> 2); 
        out += base64EncodeChars.charAt(((c1 & 0x3)<< 4) | ((c2 & 0xF0) >> 4)); 
        out += base64EncodeChars.charAt((c2 & 0xF) << 2); 
        out += "="; 
        break; 
    } 
    c3 = str.charCodeAt(i++); 
    out += base64EncodeChars.charAt(c1 >> 2); 
    out += base64EncodeChars.charAt(((c1 & 0x3)<< 4) | ((c2 & 0xF0) >> 4)); 
    out += base64EncodeChars.charAt(((c2 & 0xF) << 2) | ((c3 & 0xC0) >>6)); 
    out += base64EncodeChars.charAt(c3 & 0x3F); 
    } 
    return out; 
} 

function base64decode(str){ 
    var c1, c2, c3, c4; 
    var i, len, out; 

    len = str.length; 
    i = 0; 
    out = ""; 
    while(i < len) { 
    /* c1 */ 
    do { 
        c1 = base64DecodeChars[str.charCodeAt(i++) & 0xff]; 
    } while(i < len && c1 == -1); 
    if(c1 == -1) 
        break; 

    /* c2 */ 
    do { 
        c2 = base64DecodeChars[str.charCodeAt(i++) & 0xff]; 
    } while(i < len && c2 == -1); 
    if(c2 == -1) 
        break; 

    out += String.fromCharCode((c1 << 2) | ((c2 & 0x30) >> 4)); 

    /* c3 */ 
    do { 
        c3 = str.charCodeAt(i++) & 0xff; 
        if(c3 == 61) 
        return out; 
        c3 = base64DecodeChars[c3]; 
    } while(i < len && c3 == -1); 
    if(c3 == -1) 
        break; 

    out += String.fromCharCode(((c2 & 0XF) << 4) | ((c3 & 0x3C) >> 2)); 

    /* c4 */ 
    do { 
        c4 = str.charCodeAt(i++) & 0xff; 
        if(c4 == 61) 
        return out; 
        c4 = base64DecodeChars[c4]; 
    } while(i < len && c4 == -1); 
    if(c4 == -1) 
        break; 
    out += String.fromCharCode(((c3 & 0x03) << 6) | c4); 
    } 
    return out; 
} 

function utf16to8(str){ 
    var out, i, len, c; 

    out = ""; 
    len = str.length; 
    for(i = 0; i < len; i++) { 
    c = str.charCodeAt(i); 
    if ((c >= 0x0001) && (c <= 0x007F)) { 
        out += str.charAt(i); 
    } else if (c > 0x07FF) { 
        out += String.fromCharCode(0xE0 | ((c >> 12) & 0x0F)); 
        out += String.fromCharCode(0x80 | ((c >> 6) & 0x3F)); 
        out += String.fromCharCode(0x80 | ((c >> 0) & 0x3F)); 
    } else { 
        out += String.fromCharCode(0xC0 | ((c >> 6) & 0x1F)); 
        out += String.fromCharCode(0x80 | ((c >> 0) & 0x3F)); 
    } 
    } 
    return out; 
} 

function utf8to16(str){ 
    var out, i, len, c; 
    var char2, char3; 

    out = ""; 
    len = str.length; 
    i = 0; 
    while(i < len) { 
    c = str.charCodeAt(i++); 
    switch(c >> 4) 
    { 
      case 0: case 1: case 2: case 3: case 4: case 5: case 6: case 7: 
        // 0xxxxxxx 
        out += str.charAt(i-1); 
        break; 
      case 12: case 13: 
        // 110x xxxx 10xx xxxx 
        char2 = str.charCodeAt(i++); 
        out += String.fromCharCode(((c & 0x1F) << 6) | (char2 & 0x3F)); 
        break; 
      case 14: 
        // 1110 xxxx 10xx xxxx 10xx xxxx 
        char2 = str.charCodeAt(i++); 
        char3 = str.charCodeAt(i++); 
        out += String.fromCharCode(((c & 0x0F) << 12) | 
                       ((char2 & 0x3F) << 6) | 
                       ((char3 & 0x3F) << 0)); 
        break; 
    } 
    } 

    return out; 
} 