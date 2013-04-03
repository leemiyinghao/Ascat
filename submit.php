<?php
	if($_POST['context'] and $_POST['context']!="" and mb_strlen($_POST['context'],'utf8') <=1000){
		header( 'Location: index.html');
	};
	function string_encode($input){
		$input = htmlspecialchars($input);
		$patterns = array();
		$replacement = array();
		$patterns[0] = "/--([^-]+)--/i";
		$replacement[0] = '<del>$1</del>';
		$patterns[1] = "/(https?\:\/.+\/[^\/]+\.(png|jpg|gif|bmp))/";
		$replacement[1] = "<a href=\&quot;$1\&quot; onclick=\&quot;TINY.box.show({image:'$1',boxid:'frameless',animate:true});return false;\&quot;><img src=\&quot;$1\&quot;/></a>";
		$patterns[2] = "/\*\*([^*]+)\*\*/i";
		$replacement[2] = '<b>$1</b>';
		$patterns[3] = "/\*([^*]+)\*/i";
		$replacement[3] = '<em>$1</em>';
		$patterns[4] = "/__([^_]+)__/i";
		$replacement[4] = '<u>$1</u>';
		return preg_replace($patterns,$replacement,$input);
	};
	function string_decode($input){
		$input = str_replace('\&quot;','"',$input);
		return stripslashes($input);
	}
?>
<?php
	if($_GET['reload'] == 1){
		$database = file_get_contents('database.data');
		preg_match_all('/\("([^"]+)","([^"]+)","([^"]+)","([^"]+)"\)/',$database,$out, PREG_SET_ORDER);
		#reload index.html
		$index = "<!DOCTYPE HTML>
<html lang=\"zh-TW\">
<head>
<title>Ascat</title>
<meta charset=\"UTF-8\" />
<meta name=\"description\" content=\"Ascat,一個提供貓李線上問答服務的系統\" />
<meta name=\"keywords\" content=\"catLee\" />
<meta name=\"author\" content=\"catLee\" />
<link rel=\"stylesheet\" href=\"style.css\" />
<script type=\"text/javascript\" src=\"tinybox/tinybox.js\"></script>
</head>
<body>
	<div id=\"title\">Ascat</div>
	<div id=\"subtitle\">不要問我，去問貓</div>
	<div id=\"column\">
		<div id=\"FillForm\">
			</br>(最長250字)
			<form action=\"submit.php\" method=\"post\">
				<input name=\"context\" type=text size=\"80\">
				<input id=\"button\" type=\"submit\" value=\"送出\">
			</form>
		</div>
		<div id=\"maincon\"></br>目前共".(count($out))."筆問答</br>";
		foreach($out as $inner){
			$index = $index."
			<div class=\"thread\">
				<div class=\"timestamp\">$inner[3]</div>
				<div class=\"context\">".string_decode($inner[2])."</div>
			</div>
			<div class=\"answer\">
				<div class=\"context\">".string_decode($inner[4])."</div>
			</div>";
		};
		$index = $index."
		</div>
	</div>
</body>";
		file_put_contents("index.html",$index,LOCK_EX);
	}
?>
<?php if((!$_POST['context'] or $_POST['context'] == "") and $_GET['reload'] != 1) : ?>
<!DOCTYPE HTML>
<html lang="zh-TW">
<head>
<title>Ascat</title>
<meta charset="UTF-8" />
<meta name="description" content="Ascat,一個提供貓李線上問答服務的系統" />
<meta name="keywords" content="catLee" />
<meta name="author" content="catLee" />
<link rel="stylesheet" href="style.css" />
<body style="text-align:center;">
</br>
</br>
</br>
<div id="title">看來在資料的送出過程中出了點問題，麻煩再試一次</div>
</br>
[<a href="index.html">回到首頁</a>]
</body>
<?php endif; ?>
<?php if($_POST['context'] and $_POST['context']!="" and strlen($_POST['context']) >250) : ?>
<!DOCTYPE HTML>
<html lang="zh-TW">
<head>
<title>Ascat</title>
<meta charset="UTF-8" />
<meta name="description" content="Ascat,一個提供貓李線上問答服務的系統" />
<meta name="keywords" content="catLee" />
<meta name="author" content="catLee" />
<link rel="stylesheet" href="style.css" />
<body style="text-align:center;">
</br>
</br>
</br>
<div id="title">抱歉，最長250字！</div>
</br>
[<a href="index.html">回到首頁</a>]
</body>
<?php endif; ?>
<?php
	if($_POST['context'] and $_POST['context']!="" and mb_strlen($_POST['context'],'utf8') <=1000){
		$database = file_get_contents('database.data');
		preg_match_all('/\("([^"]+)","([^"]+)","([^"]+)","([^"]+)"\)/',$database,$out, PREG_SET_ORDER);
		$id = (count($out)+1);
		$timestamp = date("H:i d,M Y",time());
		$db = fopen("database.data", "w+");
		fwrite($db,"(\"".$id."\",\"".string_encode($_POST['context'])."\",\"".$timestamp."\",\"尚未回應\")\n");
		fwrite($db,$database);
		fclose($db);
		#reload index.html
		$database = file_get_contents('database.data');
		preg_match_all('/\("([^"]+)","([^"]+)","([^"]+)","([^"]+)"\)/',$database,$out, PREG_SET_ORDER);
		$index = "<!DOCTYPE HTML>
<html lang=\"zh-TW\">
<head>
<title>Ascat</title>
<meta charset=\"UTF-8\" />
<meta name=\"description\" content=\"Ascat,一個提供貓李線上問答服務的系統\" />
<meta name=\"keywords\" content=\"catLee\" />
<meta name=\"author\" content=\"catLee\" />
<link rel=\"stylesheet\" href=\"style.css\" />
<script type=\"text/javascript\" src=\"tinybox/tinybox.js\"></script>
</head>
<body>
	<div id=\"title\">Ascat</div>
	<div id=\"subtitle\">不要問我，去問貓</div>
	<div id=\"column\">
		<div id=\"FillForm\">
			</br>(最長250字)
			<form action=\"submit.php\" method=\"post\">
				<input name=\"context\" type=text size=\"80\">
				<input id=\"button\" type=\"submit\" value=\"送出\">
			</form>
		</div>
		<div id=\"maincon\"></br>目前共".($id)."筆問答</br>";
		foreach($out as $inner){
			$index = $index."
			<div class=\"thread\">
				<div class=\"timestamp\">$inner[3]</div>
				<div class=\"context\">".string_decode($inner[2])."</div>
			</div>
			<div class=\"answer\">
				<div class=\"context\">".string_decode($inner[4])."</div>
			</div>";
		};
		$index = $index."
		</div>
	</div>
</body>";
		file_put_contents("index.html",$index,LOCK_EX);
	};
?>