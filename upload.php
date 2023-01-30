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
# POSTで受け取ったソースコードをPHPの変数に保存
# master
$isset_master = false;
if (empty($_POST['master_code'])) {
	$master_code = "";
} else {
	$master_code = $_POST['master_code'];
	$isset_master = true;
}

# slave
$isset_slave = false;
if (empty($_POST['slave_code'])) {
	$slave_code = "";
} else {
	$slave_code = $_POST['slave_code'];
	$isset_slave = true;
}

$t = date('Ymd-His');
# 3文字のランダム文字列を生成
$str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPUQRSTUVWXYZ';
$randstr = substr(str_shuffle($str), 0, 3);

$path = './source/';
$master_fname = $t.$randstr.'_master.rb';
$slave_fname = $t.$randstr.'_slave.rb';

# ソースコードをRubyファイルとして保存
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

# 1つのフォルダの下にmasterとslaveを.mrbcとして配置
$cmd = 'mkdir ./compiled/'.$t.$randstr;
exec($cmd, $opt);
$cmd = 'chmod g+w ./compiled/'.$t.$randstr;
exec($cmd, $opt);

# master program
if($isset_master) {
	$cmd = 'mrbc -o ./compiled/'.$t.$randstr.'/master.mrbc -E '.$path.$master_fname;
	echo exec($cmd, $opt);
	#print_r($opt);
} else {
	# use an empty file.
	$cmd = 'cp ./compiled/master.mrbc ./compiled/'.$t.$randstr.'/';
	exec($cmd, $opt);
}

# slave program
if($isset_slave) {
	$cmd = 'mrbc -o ./compiled/'.$t.$randstr.'/slave.mrbc -E '.$path.$slave_fname;
	echo exec($cmd, $opt);
	#print_r($opt);
} else {
	# use an empty file.
	$cmd = 'cp ./compiled/slave.mrbc ./compiled/'.$t.$randstr.'/';
	exec($cmd, $opt);
}

# authority
$isCompiled = false;
$cmd = 'chmod g+w ./compiled/'.$t.$randstr.'/*';
exec($cmd, $opt);

#$t = '20221006-134537';	#
#$binfilename = './bin/mrbc.'.$t.'.bin';	#

$binfilename = './bin/mrbc.'.$t.$randstr.'.bin';

# バイナリファイル生成
$cmd = 'mkspiffs -c ./compiled/'.$t.$randstr.' -p 256 -b 4096 -s 0xF000 '.$binfilename;
exec($cmd, $opt);
$cmd = 'chmod g+w '.$binfilename;
exec($cmd, $opt);
# print "<p>compiled</p>";
$isCompiled = true;

# わからないが、以下2行消すと動作しない
$binFile = fopen($binfilename, "rb");
fclose($binFile);

$data = file_get_contents($binfilename);
if ($data === false) {
	print "<p>failed to read binary file</p>";
	exit;
}

if(!$isCompiled) {
echo <<< FAILCOMPILE
<div class="columns" id="compileStatus">
	<div class="column"></div>
	<div class="column is-one-fifth notification is-danger is-light has-text-centered">
	<!-- <button class="delete"></button> -->
		<strong>コンパイル失敗..</strong>
	</div>
	<div class="column"></div>
</div>
FAILCOMPILE;
	exit;
}

$fulMsgF = '<strong>Full</strong> <i class="fa-solid fa-file-pen" onclick="toggleDisplay(';
$fulMsgB = ');"></i>';
$empMsg = '<strong>Empty</strong> <i class="fa-regular fa-file"></i>';

#$isset_master = true;	#
#$isset_slave = false;	#
?>

			<article class="message is-info" id="compiledMessage">
				<div class="message-header">
					<p>コンパイル完了！</p>
					<button class="delete" aria-label="delete" onclick="closeMessage();"></button>
				</div>
				<div class="message-body">
					<p>master: 
<?php
echo $isset_master ? $fulMsgF."'master'".$fulMsgB : $empMsg;
?>
					</p>
					<p>slave: 
<?php
echo $isset_slave ? $fulMsgF."'slave'".$fulMsgB : $empMsg;
?>
					</p>
				</div>
			</article>
			
			<div class="columns">
				<div class="column is-half">
					<article id="masterCode" class="message is-dark" style="display: none">
						<div class="message-body">
							<strong>master</strong><br/>
							<?php echo nl2br($master_code); ?>
						</div>
					</article>
				</div>
				
				<div class="column is-half">
					<article id="slaveCode" class="message is-dark" style="display: none">
						<div class="message-body">
							<strong>slave</strong><br/>
							<?php echo nl2br($slave_code); ?>
						</div>
					</article>
				</div>
			</div>
			
			<div class="field is-grouped is-grouped-centered">
				<p class="control">
					<a href="./upload_file.php">
						<button class="button is-info is-outlined">
							<span class="icon is-small">
								<i class="fa-solid fa-upload"></i>
							</span>
							<span>ファイルアップロード</span>
						</button>
					</a>
				</p>
			</div>
			
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
			
			<div class="field is-grouped is-grouped-centered">
				<p class="control has-icons-left">
					<input id="team" name="team" class="input" placeholder="チーム名">
					<span class="icon is-small is-left">
						<i class="fa-solid fa-user-large"></i>
					</span>
				</p>
				<p class="control">
					<button id="sendbutton" class="button is-warning" onclick="submitCode();" disabled>
						<span class="icon is-small">
							<i class="fa-solid fa-paper-plane"></i>
						</span>
						<span>プログラム提出</span>
					</button>
				</p>
			</div>
			
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
							<i class="fa-solid fa-pause"></i>
						</span>
						<span>Pause</span>
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
					<button class="button is-info is-outlined" onclick="espDisconnect(true)">
						<span class="icon is-small">
							<i class="fa-solid fa-eject"></i>
						</span>
						<span>Disconnect</span>
					</button>
				</p>
			</div>
		</section>
		</div>	<!-- container -->
		
		<footer class="footer">
			<div class="content has-text-centered">
				<p class="copy-right">Copyright &copy; kanicon 2023</p>
			</div>
		</footer>
		
		<script type="text/javascript" src="js/espserial.js" charset="utf-8"></script>
		<script>
			window.onload = function() {
				const username = document.getElementById("team");
				const button = document.getElementById("sendbutton");
				username.addEventListener('keyup', function() {
					const text = username.value;
					console.log(text);
					if(text) {
						button.disabled = null;
					} else {
						button.disabled = "disabled";
					}
				})
			}
			
			// 書き込み、Writeボタン
			async function writeBtn() {
				console.log("button clicked");
				$('#writebutton').addClass('is-loading');
				
				var reg = '0x310000';
				
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
					//const baudRate = 921600;
					const baudRate = 115200;
					console.log("baudrate: " + baudRate);
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

				// ファイル読み込み
				async function binLoad() {
					let fileBin = [];
					let fileReg = [];
					
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
			function windowClose() {
				open('about: blank', '_self').close();    //一度再表示してからClose
			}

			function closeMessage() {
				document.getElementById("compiledMessage").style.display = "none";
			}
			
			// 送信されたソースコードをmaster/slaveそれぞれ表示/非表示
			function toggleDisplay(name) {
				if(document.getElementById(name + 'Code').style.display === 'none')
					document.getElementById(name + 'Code').style.display = 'inline';
				else
					document.getElementById(name + 'Code').style.display = 'none';
			}

			// ソースコード提出
			// submit/以下に保存される
			function submitCode() {
				var ele = document.createElement('form');
				ele.action = './submit.php';
				ele.method = 'post';
				ele.setAttribute('target', '_blank');

				const t = document.createElement('input');
				t.value = document.getElementById("team").value;
				t.name = 'team';
				
				var m = document.createElement('textarea');
				m.value = `
<?php echo empty($_POST['master_code']) ? null : $_POST['master_code'] ?>
`;
				m.name = 'master_code';
				
				var s = document.createElement('textarea');
				s.value = `
<?php echo empty($_POST['slave_code']) ? null : $_POST['slave_code'] ?>
`;
				s.name = 'slave_code';
				
				ele.appendChild(t);
				ele.appendChild(m);
				ele.appendChild(s);
				document.body.appendChild(ele);
				
				ele.submit();
			}
		</script>
	</body>
</html>
