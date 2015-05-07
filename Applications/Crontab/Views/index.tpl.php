<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>定时任务控制台</title>
		<link rel="stylesheet" type="text/css" href="css/main.css" />
		<meta name="title" content="Workerman-crontab!" />
		<meta name="description" content="workerman定时任务控制台" />
	</head>
	<body>
		<div class="main_div">
			<div class="button_div">
				<button class="main_button" id="loop_read" onclick="loop_read()">定时读取</button>
				<button class="main_button" id="stop_read" onclick="stop_read()">停止读取</button>
				<button class="main_button" id="clear_log" onclick="clear_log()">清除日志</button>
				<button class="main_button" id="start_all" onclick="start_all()">全部启动</button>
				<button class="main_button" id="stop_all" onclick="stop_all()">全部停止</button>
			</div>
			<div id="log_context">
			</div>
			
			<div id="jianceyemian_div" class="page-scan-result2"></div>
			<div id="jiancexiang_div">
				<table id="con_table" width="100%" border="0" cellpadding="0" cellspacing="0" class="scan-table">
					<thead>
						<th width="30%" height="26" align="center">定时任务组</th>
						<th width="30%" align="center">描述</th>
						<th width="6%" align="center">状态</th>
						<th align="center">操作</th>
					</thead>
					<tbody id="con_tbody">
					</tbody>
				</table>
				
			</div>
		</div>
		<script type="text/javascript" src="js/jquery-1.11.1.min.js"></script>
		<script type="text/javascript">
			var port = <?php if(isset($port))echo $port;?>;
		</script>
		<script type="text/javascript" src="js/main.js"></script>
		<script type="text/javascript">
			//刷新执行
			$(function(){
				read_log();
				get_status();
				$("#jianceyemian_div").delegate('#server-show-b', 'click',function(){
					$("#jiancexiang_div").slideToggle();
					$("#server-show-b").toggleClass("on_show");
				});

			})
			
		</script>
		
	</body>
</html>