<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>プログラム提出</title>
		<link rel="shortcut icon" href="../images/favicon.ico">
		<link rel="apple-touch-icon" href="../images/favicon_180.png">
		<link rel="icon" href="../images/favicon_192.png">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
		<link href="https://use.fontawesome.com/releases/v6.2.0/css/all.css" rel="stylesheet">
		<style>
			section.section {
				padding-top: 20px;
			}
		</style>
	</head>

	<body>
		<section class="hero">
			<div class="hero-body">
				<div class="container has-text-centered">
					<p class="title">プログラム提出</p>
				</div>
			</div>
		</section>

		<div class="container">

<?php
# ソースコードをPHPの変数に代入
# master
$isset_master = false;
if (empty($_POST['master_code'])) {
	# exit;
} else {
	$master_code = $_POST['master_code'];
	$isset_master = true;
}

# slave
$isset_slave = false;
if (empty($_POST['slave_code'])) {
	# exit;
} else {
	$slave_code = $_POST['slave_code'];
	$isset_slave = true;
}

# チーム名フォルダ以下にmaster/slaveをそれぞれ保存
$path = './submit/'.$_POST['team'].'/';
$master_fname = 'master.rb';
$slave_fname = 'slave.rb';

$cmd = 'mkdir '.$path;
exec($cmd, $opt);
$cmd = 'chmod g+w '.$path;
exec($cmd, $opt);

if (is_writeable($path)) {
	if($isset_master) {
		file_put_contents($path.$master_fname, $master_code);
		# print "<p>success! save master code.</p>";
		$cmd = 'chmod g+w '.$path.$master_fname;
		exec($cmd, $opt);
	}
	if($isset_slave) {
		file_put_contents($path.$slave_fname, $slave_code);
		# print "<p>success! save slave code.</p>";
		$cmd = 'chmod g+w '.$path.$slave_fname;
		exec($cmd, $opt);
	}
} else {
	print "<p>failed to save code.</p>";
	exit;
}
?>

			<article class="message is-info" id="compiledMessage">
				<div class="message-header">
					<p>提出完了！</p>
					<button class="delete" aria-label="delete" onclick="closeMessage();"></button>
				</div>
				<div class="message-body">
					<p>ご協力ありがとうございます</p>
					<p>このページは消してください</p>
				</div>
			</article>

			<div class="field is-grouped is-grouped-centered">
				<p class="control">
					<button class="button is-light" onclick="windowClose();">
						<span class="icon is-small">
							<i class="fa-solid fa-xmark"></i>
						</span>
						<span>Close</span>
					</button>
				</p>
			</div>
		</div>

		<section class="hero">
			<div class="hero-body">
				<p class="title"></p>
			</div>
		</section>
		<section class="hero">
			<div class="hero-body">
				<p class="title"></p>
			</div>
		</section>
		<section class="hero">
			<div class="hero-body">
				<p class="title"></p>
			</div>
		</section>
		
		<footer class="footer">
			<div class="content has-text-centered">
				<p class="copy-right">Copyright &copy; kanicon 2023</p>
			</div>
		</footer>

		<script>
			function windowClose() {
				open('about: blank', '_self').close();
			}
		</script>
	</body>
</html>
