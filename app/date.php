<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>DatePicker jQuery UI</title>
<link rel="stylesheet" href="../js/jq-ui/themes/base/jquery.ui.all.css" />
<script type="text/javascript" src="../js/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="../js/jq-ui/ui/jquery-ui.js"></script>
<style type="text/css">
	.ui-datepicker-header {
		background: #407893;
	}
</style>
</head>

<body>
	
    <label for="datepicker">Enter a Date</label>
	<input type="text" id="datepicker" />
    
    <script type="text/javascript">
		$(function(){
			$("#datepicker").datepicker();	
		});
	</script>

</body>
</html>