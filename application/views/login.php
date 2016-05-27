<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $title;?></title>
<?php
// RESET CSS
$resetCSS = array(
			'href' => 'assets/css/resetcss.css',
			'rel' => 'stylesheet',
			'type' => 'text/css'
			);
echo link_tag($resetCSS);

// CORE CSS
$coreCSS = array(
			'href' => 'assets/css/login.css',
			'rel' => 'stylesheet',
			'type' => 'text/css'
			);
echo link_tag($coreCSS);

// FAVICON
$favicon = array(
		'href' => 'assets/img/icons/favicon.ico',
		'rel' => 'shortcut icon'
		);
echo link_tag($favicon);
?>
</head>

<body>
<div id="warpper">
    <div id="container">
        <div id="login">
            <h1>Login</h1>
            <form method="POST" action="<?php echo 'user/verifylogin' ;?>">
                <p><input type="text" name="txt_username" placeholder="ชื่อผู้ใช้งาน"></p>
                <p><input type="password" name="txt_password" placeholder="รหัสผ่าน"></p>
                <p class="submit"><input type="submit" name="login" value="เข้าสู่ระบบ"></p>
            </form>
        </div>
    </div>
</div>
</body>
</html>