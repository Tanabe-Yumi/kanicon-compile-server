<!DOCTYPE html>
<html lang="ja">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>Compile n Writing</title>
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
					<p class="title">Writing</p>
					<p class="subtitle">
						compiled! then 
						<span class="icon is-small">
							<i class="fa-solid fa-arrow-right-to-bracket"></i>
						</span>
						Write
					</p>
				</div>
			</div>
		</section>

		<div class="container">

<?php
#var_dump($_POST['master_code']);
$isset_master = false;
if (empty($_POST['master_code'])) {
	print "<p>empty master source.</p>";
	# exit;
} else {
	$master_code = $_POST['master_code'];
	$isset_master = true;
}

# slave
$isset_slave = false;
if (empty($_POST['slave_code'])) {
	print "<p>empty slave source.</p>";
} else {
	$slave_code = $_POST['slave_code'];
	$isset_slave = true;
}

$t = date('Ymd-His');

$path = './source/';
$master_fname = $t.'_master.rb';
$slave_fname = $t.'_slave.rb';

if (is_writeable($path)) {
	if($isset_master) {
		file_put_contents($path.$master_fname, $master_code);
		print "<p>success! save master code.</p>";
		$cmd = 'chmod g+w '.$path.$master_fname;
		exec($cmd, $opt);
	}
	if($isset_slave) {
		file_put_contents($path.$slave_fname, $slave_code);
		print "<p>success! save slave code.</p>";
		$cmd = 'chmod g+w '.$path.$slave_fname;
		exec($cmd, $opt);
	}
} else {
	print "<p>failed to save code.</p>";
	exit;
}

$cmd = 'mkdir ./compiled/'.$t;
exec($cmd, $opt);
$cmd = 'chmod g+w ./compiled/'.$t;
exec($cmd, $opt);

# master program
if($isset_master) {
	$cmd = 'mrbc -o ./compiled/'.$t.'/master.mrbc -E '.$path.$master_fname;
	echo exec($cmd, $opt);
	#print_r($opt);
} else {
	# use an empty file.
	$cmd = 'cp ./compiled/master.mrbc ./compiled/'.$t.'/';
	exec($cmd, $opt);
}

# slave program
if($isset_slave) {
	$cmd = 'mrbc -o ./compiled/'.$t.'/slave.mrbc -E '.$path.$slave_fname;
	echo exec($cmd, $opt);
	#print_r($opt);
} else {
	# use an empty file.
	$cmd = 'cp ./compiled/slave.mrbc ./compiled/'.$t.'/';
	exec($cmd, $opt);
}

# authority
$cmd = 'chmod g+w ./compiled/'.$t.'/*';
exec($cmd, $opt);

#$t = '20221006-134537';	#
$binfilename = './bin/mrbc.'.$t.'.bin';

$cmd = 'mkspiffs -c ./compiled/'.$t.' -p 256 -b 4096 -s 0xF000 '.$binfilename;
exec($cmd, $opt);
$cmd = 'chmod g+w '.$binfilename;
exec($cmd, $opt);
print "<p>compiled</p>";

$binFile = fopen($binfilename, "rb");
fclose($binFile);

$data = file_get_contents($binfilename);
if ($data === false) {
	print "<p>failed to read binary file</p>";
	exit;
}
?>

			<div class="columns" id="compileStatus">
				<div class="column"></div>
				<div class="column is-one-fifth notification is-info is-light has-text-centered">
				<!-- <button class="delete"></button> -->
					<strong>コンパイル完了！</strong>
				</div>
				<div class="column"></div>
			</div>
			<!--
			<br />
			<input type="text" id="writereg" value="0xe000" />
			<input type="file" id="file" />
			<br />
			-->
			<div class="field is-grouped is-grouped-centered">
				<p class="control">
					<button id="writebutton" class="button is-primary" onclick="writeBtn();">
					<!-- class="is-loading" -->
						<span class="icon is-small">
							<i class="fa-solid fa-right-to-bracket"></i>
						</span>
						<span>書き込み</span>
					</button>
				</p>
				<p class="control">
					<button class="button is-light" onclick="windowClose()">
						<span class="icon is-small">
							<i class="fa-solid fa-xmark"></i>
						</span>
						<span>Close</span>
					</button>
				</p>
			</div>
			<!-- <input type="button" value="Write" onclick="writeBtn();" /> -->
			
			<section class="section">
			<div class="field">
				<label class="label">OUTPUT</label>
				<div class="control">
					<textarea rows="9" id="outputArea" class="textarea is-medium is-auto-scroll" placeholder="OUTPUT" readonly></textarea>
				</div>
			</div>
			<div class="field is-grouped">
				<p class="control">
					<button class="button is-success" onclick="showOutput()">
						<span class="icon is-small">
							<i class="fa-solid fa-bug"></i>
						</span>
						<span>Debug</span>
					</button>
				</p>
				<p class="control">
					<button class="button is-success is-outlined" onclick="autoScroll()">
						<span class="icon is-small">
							<i class="fa-solid fa-play"></i>
						</span>
						<span>Scroll</span>
					</button>
				</p>
				<p class="control">
					<button class="button is-success is-outlined" onclick="stopScroll()">
						<span class="icon is-small">
							<i class="fa-solid fa-stop"></i>
						</span>
						<span>Stop</span>
					</button>
				</p>
				<p class="control">
					<button class="button is-light" onclick="clearSerial()">
						<span class="icon is-small">
							<i class="fa-solid fa-eraser"></i>
						</span>
						<span>Clear</span>
					</button>
				</p>
				<p class="control">
					<button id="writebutton" class="button is-info" onclick="espConnect(921600)">
						<span class="icon is-small">
							<i class="fa-solid fa-bolt"></i>
						</span>
						<span>Connect</span>
					</button>
				</p>
				<p class="control">
					<button class="button is-warning is-light" onclick="espDisconnect(true)">
						<span class="icon is-small">
							<i class="fa-solid fa-eject"></i>
						</span>
						<span>Disconnect</span>
					</button>
				</p>
			</div>
			<!-- <textarea cols="80" rows="30" id="outputArea" readonly></textarea> -->
		</section>
		</div>	<!-- container -->
		
		<footer class="footer">
			<div class="content has-text-centered">
				<p class="copy-right">Copyright &copy; kanicon 2022</p>
				<!--
				<p>
					<strong>Bulma</strong> by <a href="https://jgthms.com">Jeremy Thomas</a>. The source code is licensed
					<a href="http://opensource.org/licenses/mit-license.php">MIT</a>. The website content
					is licensed <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/">CC BY NC SA 4.0</a>.
				</p>
				-->
			</div>
		</footer>
		
		<!-- <script src="https://github.com/tanakamasayuki/WebSerialEsptool/blob/master/espserial.js" charset="utf-8"></script> -->
		<!-- <script type="javascript" src="https://tanakamasayuki.github.io/WebSerialEsptool/espserial.js" charset="utf-8"></script> -->
		<script type="text/javascript" src="js/espserial.js" charset="utf-8"></script>
		<script>
			// 書き込み、Writeボタン
			async function writeBtn() {
				console.log("button clicked");
				$('#writebutton').addClass('is-loading');
				
				//var file = document.getElementById('file').files[0];
				var reg = '0x310000';
				
				// 参考：https://www.web-dev-qa-db-ja.com/ja/javascript/hex2bin%E3%82%92javascript%E3%81%A7%E3%83%97%E3%83%AD%E3%82%B0%E3%83%A9%E3%83%A0%E3%81%99%E3%82%8B%E6%96%B9%E6%B3%95%E3%81%AF%EF%BC%9F/939843794/
				function hex2bin(hex) { 
					var len = hex.length, result = "";
					for(var i = 0; i < len; i += 2) {
						result += '%' + hex.substr(i, 2);
					}

					return unescape(result);
				}
				
				const b = hex2bin('<?php echo bin2hex($data); ?>');
				
				// マイコン接続
				try {
					//const baudRate = Number(document.getElementById('baudRate').value);
					const baudRate = 921600;
					espSetOutput(addSerial);
					await espConnect(baudRate);
				} catch (error) {
					console.log("connection error");
					$('#writebutton').removeClass('is-loading');
					addSerial("Error: Open" + error + "\n");
					console.log("Error: Open " + error.message + "\n");
					console.dir(error);
					addSerial("マイコンの接続に失敗\nretry");
					return;
				}

				/*
				function binFileLoad(file) {
					// 非同期処理の完了/失敗を表す
					return new Promise((resolve, reject) => {
						// ユーザコンピュータに保存されているファイルを非同期に読み取る
						const reader = new FileReader();
						reader.onload = () => {
							// Promiseオブジェクトを返す
							resolve(reader.result);
						}
						// Blob/File オブジェクトを読み込む
						// 引数のfileを読み込む
						reader.readAsBinaryString(file);
					})
				}*/

				// ファイル読み込み
				async function binLoad() {
					let fileBin = [];
					let fileReg = [];

					/*
					fileBin[0] = "";
					fileReg[0] = parseInt('0x10000');
					if (file) {
						console.log("in file");
						fileBin[0] = await binFileLoad(file);
						fileReg[0] = parseInt(reg);
					}*/
					fileBin[0] = b;
					fileReg[0] = parseInt(reg);

					console.log("fileReg", fileReg);
					console.log("fileBin", fileBin);
					// ファイルサイズ表示
					for (let i = 0; i < fileBin.length; i++) {
						console.log("fileSize[" + i + "]", fileBin[i].length);
					}
					await espFlash(fileBin, fileReg);
				}

				await binLoad();
				await espDisconnect(true);
				
				$('#writebutton').removeClass('is-loading');
			}
			
			async function showOutput() {
				if (!espPort) {
					addSerial('マイコンが接続されていません\n');
					return;
				}
				while (espPort.readable) {
                    const reader = espPort.readable.getReader();

                    try {
                        while (true) {
                            const { value, done } = await reader.read();
                            if (done) {
                                addSerial("Canceled\n");
                                break;
                            }
                            const inputValue = new TextDecoder().decode(value);
                            addSerial(inputValue);
                        }
                    } catch (error) {
                        addSerial("Error: Read" + error + "\n");
                    } finally {
                        reader.releaseLock();
                    }
                }
			}
			
			function autoScroll() {
				$('#outputArea').addClass('is-auto-scroll');
			}
			
			function stopScroll() {
				$('#outputArea').removeClass('is-auto-scroll');
			}
			
			function clearSerial() {
				document.getElementById('outputArea').value = "";
			}
			
			// タブを閉じる
			function windowClose(){
				open('about: blank', '_self').close();    //一度再表示してからClose
			}
		</script>
	</body>
</html>
