<?php
	$url = new url_rewrite_header(_DIVIDER_);
	$pathinfo = $url->returnArray(false);
	echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title><?php echo ($pathinfo[0]||$pathinfo[0]=="index"||$pathinfo[0]=="index.php")? $url->returnHeader(DKP_NAME.':'.DKP_STATUS.DKP_VERSION,_HEADER_SEPERATOR_,false,true) : DKP_NAME.':'.DKP_STATUS.DKP_VERSION;?></title>
		<base href="<?php echo 'http://'. $_SERVER['HTTP_HOST'];?>" />
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />
		<meta name="author" content="Alexander Loos" />
		<meta name="copyright" content="Copyright Alexander Loos, 2010 - 2010" />
		<meta name="publisher" content="Alexander Loos" />
		<meta name="programmer" content="Alexander Loos" />
		<meta http-equiv="X-UA-Compatible" content="IE=8" />
		
		<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" media="screen" />
		<link rel="stylesheet" href="/css/reset.css" type="text/css" charset="utf-8" />
		<link rel="stylesheet" href="/css/style.css" type="text/css" charset="utf-8" />
		<link rel="stylesheet" href="/css/class.css" type="text/css" charset="utf-8" />
		<link rel="stylesheet" href="/css/items.css" type="text/css" charset="utf-8" />
		
		<script src="http://static.wowhead.com/widgets/power.js" type="text/javascript"></script>
		<script src="js/mootools-1.2.4-core-yc.js" type="text/javascript"></script>
		<script src="js/highcharts.js" type="text/javascript"></script>
		<!--[if IE]>
			<script src="js/excanvas.compiled.js" type="text/javascript"></script>
		<![endif]-->
	</head>
	<body>
		<div id="wrapper">
			<div class="center divboxSmalOuter" id="menu">
				<div class="divboxInner">
					<ul>
						<li><a href="/">[Home]</a></li>
						<li>Menus !</li>
					</ul>
				</div>
			</div>
			<div class="divboxOuter" id="wraper">
				<div class="divboxInner">
					<div id="charlist"><?php include('list.php');?></div>
					<div id="content">
							<?php
								$ignores = array("index", "index.php");
								if($pathinfo['0'] == null || in_array($pathinfo['0'], $ignores)){
									get_function('listraids');
									echo listraids(10, $sql);
								} else {
									if(file_exists($pathinfo[0].".php")){
										include($pathinfo[0].".php");
									} else {
										include("404.php");
									}
								}
							?>
					</div>
				</div>
			</div>
			<div class="center divboxSmalOuter" id="footer">
				<div class="divboxInner">
					<?php include('footer.php'); ?>
				</div>
			</div>
		</div>
	</body>
</html>