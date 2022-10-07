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

#$t = '20220929-134421';	#
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
		
		<script>
			let espPort;
			let espReader;
			let espOutputFunc;
			let espChipType;
			let espMacAddr;
			let espUploadBaudrate;

			// Command
			const ESP_FLASH_BEGIN = 0x02;
			const ESP_FLASH_DATA = 0x03;
			const ESP_FLASH_END = 0x04;
			const ESP_MEM_BEGIN = 0x05;
			const ESP_MEM_END = 0x06;
			const ESP_MEM_DATA = 0x07;
			const ESP_SYNC = 0x08;
			const ESP_WRITE_REG = 0x09;
			const ESP_READ_REG = 0x0A;
			const ESP_SPI_SET_PARAMS = 0x0B;
			const ESP_SPI_ATTACH = 0x0D;
			const ESP_READ_FLASH_SLOW = 0x0e;
			const ESP_CHANGE_BAUDRATE = 0x0F;
			const ESP_FLASH_DEFL_BEGIN = 0x10;
			const ESP_FLASH_DEFL_DATA = 0x11;
			const ESP_FLASH_DEFL_END = 0x12;
			const ESP_SPI_FLASH_MD5 = 0x13;
			const ESP_GET_SECURITY_INFO = 0x14;
			const ESP_ERASE_FLASH = 0xD0;
			const ESP_ERASE_REGION = 0xD1;
			const ESP_READ_FLASH = 0xD2;
			const ESP_RUN_USER_CODE = 0xD3;
			const ESP_FLASH_ENCRYPT_DATA = 0xD4;

			let ESP_FLASH_BLOCK_SIZE = 0x400;

			function espSetOutput(func) {
				espOutputFunc = func;
			}

			function espOutput(str) {
				if (espOutputFunc) {
					espOutputFunc(str);
				}
			}

			async function espConnect(setBaudrate) {
				if (espPort) {
					espDisconnect();
				} else {
					espPort = await navigator.serial.requestPort();
				}

				if (setBaudrate < 115200) {
					setBaudrate = 115200;
				}
				espUploadBaudrate = setBaudrate;

				await espPort.open({ baudRate: 115200 });
				espOutput("Serial Open\n");
				const { usbProductId, usbVendorId } = espPort.getInfo();
				espOutput("Connect VendorId=0x" + usbVendorId.toString(16) + " ProductId=0x" + usbProductId.toString(16) + "\n");

				espOutput("Connecting...");
				for (let i = 0; i < 10; i++) {
					await espDownload();
					let ret = await espSync();
					if (ret) {
						await espGetChipType();
						if (espChipType) {
							espOutput("Sync\n\n");
							break;
						}
					}
				}

				await espGetChipType();
				espOutput("Chip: " + espChipType + "\n");

				await espGetMacAddr();
				espOutput("MAC: " + espMacAddr.map(value => value.toString(16).toUpperCase().padStart(2, "0")).join(":") + "\n");

				await espRunStub();

				await espChangeBaudrate();
			}

			async function espDisconnect(reselect = false) {
				if (!espPort)
					return;

				if (espPort.readable) {
					if (espReader) {
						await espReader.cancel();
						await espReader.releaseLock();
					}
					await espPort.close();
					espReader = null;
					espChipType = null;
					espMacAddr = null;
					espOutput("Serial Close\n");
				}

				if (reselect) {
					espPort = null;
				}
			}

			async function espReset() {
				// Reset
				await espPort.setSignals({ dataTerminalReady: false, requestToSend: true });
				await new Promise(resolve => setTimeout(resolve, 100));
				await espPort.setSignals({ dataTerminalReady: false, requestToSend: false });
				await new Promise(resolve => setTimeout(resolve, 1000));
			}

			async function _espDownload(longDelay = false) {
				// Reset + Download mode
				if (longDelay) {
					await espReset();
					await new Promise(resolve => setTimeout(resolve, 500));
					await espReset();
					await new Promise(resolve => setTimeout(resolve, 500));
				}

				await espPort.setSignals({ dataTerminalReady: false, requestToSend: true });
				await new Promise(resolve => setTimeout(resolve, 100));
				if (longDelay) {
					await new Promise(resolve => setTimeout(resolve, 1200));
				}
				await espPort.setSignals({ dataTerminalReady: true, requestToSend: false });
				await new Promise(resolve => setTimeout(resolve, 50));
				if (longDelay) {
					await new Promise(resolve => setTimeout(resolve, 400));
				}
				await espPort.setSignals({ dataTerminalReady: false, requestToSend: false });

				await new Promise(resolve => setTimeout(resolve, 1000));

				// Receive
				for (let i = 0; i < 2; i++) {
					const { value, done, error } = await espReceivePacket();
					if (!done) {
						console.log("espDownload", new TextDecoder().decode(value));
						return true;
					} else {
						break;
					}
				}

				return false;
			}

			async function espDownload() {
				// Retry
				for (let i = 0; i < 4; i++) {
					let ret = await _espDownload(i & 1);
					if (ret) {
						return true;
					}
				}

				espOutput("Download mode failure.\n");
				return false;
			}

			async function espReaderTimeout() {
				console.log("timeout");
				if (espReader) {
					espReader.cancel();
					console.log("canceled");
				}
			}

			function espMakePakcet(command, data = [], checksum = 0) {
				let packet = new ArrayBuffer(10 + data.byteLength);
				// バイナリデータの読み書きができる
				let dv = new DataView(packet);

				// Header
				let index = 0;
				// indexから符号なし8bit整数値で上書き
				dv.setUint8(index, 0xC0);
				index++;
				dv.setUint8(index, 0x00);
				index++;

				// ESP_READ_REG
				dv.setUint8(index, command);
				index++;

				// Address length
				dv.setUint16(index, data.byteLength, true);
				index += 2;

				// Checksum
				dv.setUint32(index, checksum, true);
				// 32bit=4byteなのでindex+=4
				index += 4;

				// Data
				let dv2 = new DataView(data);
				for (let i = 0; i < data.byteLength; i++) {
					dv.setUint8(index, dv2.getUint8(i));
					index++;
				}

				// End Header
				dv.setUint8(index, 0xC0);
				index++;

				return packet;
			}

			function espChecksum(data) {
				let checksum = 0xEF;

				for (let c of data) {
					checksum ^= c.charCodeAt(0);
				}
				return checksum;
			}

			async function espSendPacket(packet) {
				let dv = new DataView(packet);
				const espWriter = espPort.writable.getWriter();
				let sendPacket = [];

				sendPacket.push(0xC0);
				for (let i = 1; i < dv.byteLength - 1; i++) {
					let byte = dv.getUint8(i);
					if (byte == 0xDB) {
						sendPacket = sendPacket.concat([0xDB, 0xDD]);
					} else if (byte == 0xC0) {
						sendPacket = sendPacket.concat([0xDB, 0xDC]);
					} else {
						sendPacket.push(byte);
					}
				}
				sendPacket.push(0xC0);
				console.log("espSendPacket", sendPacket);
				await espWriter.write(new Uint8Array(sendPacket));
				espWriter.releaseLock();

				// Wait
				await new Promise(resolve => setTimeout(resolve, 100));
			}

			// default: timeOut = 200
			async function espReceivePacket(timeOut = 200, replaceC0 = true) {
				// リーダーを作る
				espReader = espPort.readable.getReader();
				let timer = setTimeout(espReaderTimeout, timeOut);
				let error = 0;
				let { value, done } = await espReader.read();
				clearTimeout(timer);
				await espReader.releaseLock();
				espReader = null;

				if (value && replaceC0) {
					let dv = new DataView(value.buffer);
					let receivePacket = [];
					receivePacket.push(0xC0);
					for (let i = 1; i < dv.byteLength - 1; i++) {
						let byte = dv.getUint8(i);
						if (byte == 0xDB) {
							i++;
							let byte2 = dv.getUint8(i);
							if (byte2 == 0xDD) {
								receivePacket.push(0xDB);
							} else {
								receivePacket.push(0xC0);
							}
						} else {
							receivePacket.push(byte);
						}
					}
					receivePacket.push(0xC0);
					value = new Uint8Array(receivePacket);
				}

				if (value) {
					console.log("espReceivePacket value", value);
				}

				if (done) {
					console.log("espReceivePacket done", "Timeout");
				}

				if (!done && value.byteLength >= 12) {
					let dv2 = new DataView(value.buffer);
					if (dv2.getUint8(0, true) == 0xC0 && dv2.getUint8(1, true) == 0x01) {
						let len = dv2.getUint16(3, true);
						console.log("espReceivePacket len", len);
						error = dv2.getUint8(5 + len, true);
						if (error) {
							console.log("espReceivePacket error", error);
						}
					}
				}

				return { value, done, error };
			}

			async function espSync() {
				let data = new ArrayBuffer(36);
				let dv = new DataView(data);
				let ret = false;

				// Data
				let index = 0;
				dv.setUint8(index, 0x07, true);
				index++;
				dv.setUint8(index, 0x07, true);
				index++;
				dv.setUint8(index, 0x12, true);
				index++;
				dv.setUint8(index, 0x20, true);
				index++;
				for (let i = 0; i < 32; i++) {
					dv.setUint8(index, 0x55, true);
					index++;
				}

				// Make Packet
				let packet = espMakePakcet(ESP_SYNC, data);

				// Retry
				for (let i = 0; i < 10; i++) {
					// Send
					await espSendPacket(packet);

					// Receive
					const { value, done, error } = await espReceivePacket();

					if (!done && value.length >= 12) {
						// Sync
						ret = true;
						break;
					} else {
						espOutput(".");
					}

					// Wait
					await new Promise(resolve => setTimeout(resolve, 100));
				}

				// Flash Receive
				for (let i = 0; i < 10; i++) {
					const { value, done, error } = await espReceivePacket();
					if (done) {
						break;
					}
				}

				// Wait
				await new Promise(resolve => setTimeout(resolve, 1000));

				return ret;
			}

			async function espReadRegister(reg) {
				let data = new ArrayBuffer(4);
				let dv = new DataView(data);

				// Data
				let index = 0;
				dv.setUint32(index, reg, true);
				index += 4;

				// Make Packet
				let packet = espMakePakcet(ESP_READ_REG, data);

				// Send
				for (let i = 0; i < 10; i++) {
					await espSendPacket(packet);

					// Receive
					const { value, done, error } = await espReceivePacket();

					if (!done && !error) {
						let index = 5;
						let dv2 = new DataView(value.buffer);
						let regValue = dv2.getUint32(index, true);
						index += 4;

						console.log("regValue", regValue);
						if (error == 0) {
							return regValue;
						}
					}
				}
			};

			// マイコンの種類を取得
			async function espGetChipType() {
				// Chip type
				let ret = await espReadRegister(0x40001000);
				if (ret == 0xfff0c101) {
					espChipType = "ESP8266";
				} else if (ret == 0x00f01d83) {	// カニロボはこれ
					espChipType = "ESP32";
				} else if (ret == 0x000007c6) {
					espChipType = "ESP32-S2";
				} else if (ret == 0xeb004136) {
					espChipType = "ESP32-S3(beta2)";
				} else if (ret == 0x9) {
					espChipType = "ESP32-S3(beta3)";
				} else if (ret == 0x6921506f) {
					espChipType = "ESP32-C3";
				} else if (ret == 0x0da1806f) {
					espChipType = "ESP32-C3";
				} else if (ret == 0x1b31506f) {
					espChipType = "ESP32-C6 BETA";
				}

				return espChipType;
			}

			async function espGetMacAddr() {
				espMacAddr = [0, 0, 0, 0, 0, 0];

				if (!espChipType) {
					await espGetChipType();
				}

				// Mac
				if (espChipType == "ESP32") {
					let mac0 = await espReadRegister(0x3ff5a000 + 4 * 1);
					let mac1 = await espReadRegister(0x3ff5a000 + 4 * 2);
					mac1 = mac1 & 0xffff;
					espMacAddr[0] = mac1 >> 8;
					espMacAddr[1] = mac1 & 0xff;
					espMacAddr[2] = mac0 >> 24;
					espMacAddr[3] = (mac0 >> 16) & 0xff;
					espMacAddr[4] = (mac0 >> 8) & 0xff;
					espMacAddr[5] = mac0 & 0xff;
				}

				// singned -> unsigned
				for (let i = 0; i < 6; i++) {
					espMacAddr[i] = (espMacAddr[i] >>> 0) & 0xff;
				}

				return espMacAddr;
			}

			async function espRunStub() {
				if (!espChipType) {
					await espGetChipType();
				}

				// Mac
				if (espChipType == "ESP32") {
					// text
					let text_data = atob("CAD0PxwA9D8AAPQ/pOv9PxAA9D82QQAh+v/AIAA4AkH5/8AgACgEICB0nOIGBQAAAEH1/4H2/8AgAKgEiAigoHTgCAALImYC54b0/yHx/8AgADkCHfAAAPgg9D/4MPQ/NkEAkf3/wCAAiAmAgCRWSP+R+v/AIACICYCAJFZI/x3wAAAAECD0PwAg9D8AAAAINkEA5fz/Ifv/DAjAIACJApH7/4H5/8AgAJJoAMAgAJgIVnn/wCAAiAJ88oAiMCAgBB3wAAAAAEA2QQBl/P8Wmv+B7f+R/P/AIACZCMAgAJgIVnn/HfAAAAAAAAEAAIAAmMD9P////wAEIPQ/NkEAIfz/MiIEFkMFZfj/FuoEpfv/OEIM+AwUUfT/N6gLOCKAMxDMM1Hy/xwEiCJAOBEl8/+B8P+AgxAx8P/AIACJAzHS/8AgAFJjAMAgAFgDVnX/OEJAM8A5QjgiSkNJIh3wAJDA/T8IQP0/gIAAAISAAABAQAAASID9P5TA/T82QQCx+P8goHRlrwCW6gWB9v+R9v+goHSQmIDAIACyKQCR8/+QiIDAIACSGACQkPQbycDA9MAgAMJYAJqbwCAAokkAwCAAkhgAger/kJD0gID0h5lGgeT/keX/oej/mpjAIADICbHk/4ecGUYCAHzohxrhRgkAAADAIACJCsAgALkJRgIAwCAAuQrAIACJCZHY/5qIDAnAIACSWAAd8AAAUC0GQDZBAEGz/1g0UDNjFuMDWBRaU1BcQYYAACXs/4hEphgEiCSHpfKl5P8Wmv+oFDDDICCyIIHy/+AIAIw6IqDEKVQoFDoiKRQoNDAywDk0HfAACCD0PwAAQABw4vo/SCQGQPAiBkA2YQCl3f+tAYH8/+AIAD0KDBLs6ogBkqIAkIgQiQFl4v+R8v+h8//AIACICaCIIMAgAIJpALIhAKHv/4Hw/+AIAKAjgx3wAAD/DwAANkEAgYf/kqABkkgAMJxBkmgCkfr/MmgBKTgwMLSaIiozMDxBDAIpWDlIpfj/LQqMGiKgxR3wAAAskgBANkEAgqDArQKHkg6ioNuB+//gCACioNyGAwCCoNuHkgiB9//gCACioN2B9P/gCAAd8AAAADZBADoyBgIAAKICABsi5fv/N5L0HfAAAAAQAABYEAAAfNoFQNguBkCc2gVAHNsFQDYhIaLREIH6/+AIAIYJAABR9v+9AVBDY80ErQKB9v/gCAD8Ks0EvQGi0RCB8//gCABKIkAzwFZj/aHs/7LREBqqge7/4AgAoen/HAsaqiX4/y0DBgEAAAAioGMd8AAAADZBAKKgwIHM/+AIAB3wAABsEAAAaBAAAHAQAAB0EAAAeBAAAPxnAEDQkgBACGgAQDZBIWH5/4H5/xpmSQYaiGLREAwELApZCEJmGoH2/+AIAFHx/4HN/xpVWAVXuAIGNwCtBoHL/+AIAIHt/3Hp/xqIelFZCEYlAIHo/0BzwBqIiAi9AXB4Y80HIKIggcL/4AgAjLpx4P8MBVJmFnpxhgwA5fX/cLcgrQFl7P8l9f/NB70BYKYggbj/4AgAeiJ6RDe00IHW/1B0wBqIiAiHN6cG8P8ADAqiRmyB0f8aiKIoAIHR/+AIAFbq/rGo/6IGbBq7pXsA9+oM9kUJWreiSwAbVYbz/7Kv/reayGZFCFImGje1Ale0qKGd/2C2IBCqgIGf/+AIAKXt/6GY/xwLGqrl4//l7P8sCoG9/+AIAB3wAMD8P09IQUmo6/0/fOELQBTgC0AMAPQ/OED0P///AAAAAAEAjIAAABBAAAAAQAAAAMD8PwTA/D8QJwAAFAD0P/D//wCo6/0/CMD8P7DA/T98aABA7GcAQFiGAEBsKgZAODIGQBQsBkDMLAZATCwGQDSFAEDMkABAeC4GQDDvBUBYkgBATIIAQDbBACHe/wwKImEIQqAAge7/4AgAIdn/Mdr/BgEAQmIASyI3Mvcl4f8MS6LBIKXX/2Xg/zHm/iHm/kHS/yojwCAAOQKx0f8hi/4MDAxaSQKB3//gCABBzf9SoQHAIAAoBCwKUCIgwCAAKQSBfv/gCACB2P/gCAAhxv/AIAAoAsy6HMRAIhAiwvgMFCCkgwwLgdH/4AgA8b//0Ur/wb//saz+4qEADAqBzP/gCAAhvP8MBSozIan+YtIrwCAAKAMWcv/AIAAoAwwUwCAAWQNCQRBCAgEMJ0JBEXJRCVlRJpQHHDd3FB4GCABCAgNyAgKARBFwRCBmRBFIIsAgAEgESVFGAQAAHCRCUQnl0v8Mi6LBEGXJ/0ICA3ICAoBEEXBEIHGg/3Bw9Ee3EqKgwGXE/6Kg7iXE/yXQ/0bf/wByAgEM2ZeXAoafAHc5TmZnAgbJAPZ3IGY3AsZxAPZHCGYnAkZXAAYmAGZHAkaFAGZXAoakAEYiAAyZl5cCxpcAdzkIZncCRqYARh0AZpcChpkADLmXlwJGggAGGQAcOZeXAgZCAHc5Kma3AsZPABwJdzkMDPntBZeXAoY2AMYQABwZl5cCBlcAHCRHlwIGbQCGCwCSoNKXlwLGMgB3ORCSoNCXFySSoNGXFzHGBAAAAJKg05eXAoY6AZKg1JeXAoZIAO0FcqD/RqMADBdWZCiBdP/gCACgdIOGngAAACaEBAwXBpwAQiICciIDcJQgkJC0Vrn+pav/cESAnBoG+P8AoKxBgWj/4AgAVjr9ctfwcKTAzCeGcQAAoID0Vhj+RgQAoKD1gWH/4AgAVir7gUv/gHfAgUr/cKTAdzjkxgMAAKCsQYFY/+AIAFY6+XLX8HCkwFan/kZhAHKgwCaEAoZ9AO0FRlMAAAAmtPUGVAByoAEmtAKGdwCyIgOiIgLlsf8GCQAAcqABJrQCBnIAkTb/QiIEUOUgcqDCR7kCBm4AuFKoIgwXZaX/oHWDxmkADBlmtCxIQqEs/+0FcqDCR7oCBmUAeDK4UqgicHSCmeHlov9BEv6Y4VlkQtQreSSglYN9CQZcAJEN/u0FogkAcqDGFkoWeFmYIkLE8ECZwKKgwJB6kwwKkqDvhgIAAKqysgsYG6qwmTBHKvKiAgVCAgSAqhFAqiBCAgbtBQBEEaCkIEICB4BEAaBEIECZwEKgwZB0k4ZEAEH1/e0FkgQAcqDGFkkQmDRyoMhWyQ+SRAB4VAY9AAAcie0FDBeXFALGOQDoYvhy2FLIQrgyqCKBCP/gCADtCqB1g0YzAAwXJkQCxjAAqCK9BYEA/+AIAIYPAADtBXKgwCa0AgYrAEgieDLAIAB5BAwHhicAZkQCRqj/7QVyoMAGJAAADBcmtAJGIQBB5/6YUngimQRB5f55BH0FhhwAseL+DBfYC0LE8J0FQJeT0HWTcJkQ7QVyoMZWeQWB3P5yoMnICEc8TECgFHKgwFY6BH0KDB+GAgAAepKYaUt3mQqdD3qtcOzARzftFvniqQvpCAaK/wAMF2aEF0HM/ngEjBdyoMhZBAwaQcj+cKWDWQR9Cu0FcKB04mENpY3/4iEN4KB0JY3/JZn/VsfAQgIBcqAPdxRARzcUZkQCRnkAZmQCxn8AJjQChvv+hh8AHCd3lAKGcwBHNwscF3eUAgY6AEb1/gByoNJ3FE9yoNR3FHNG8f4AAAC4MqGu/ngiucGBuv7gCAAhq/6RrP7AIAAoArjBIEQ1wCIRkCIQICQgsLKCrQVwu8KBsf7gCACio+iBrv7gCAAG4P4AANIiBcIiBLIiA6giJZL/Rtv+ALICA0ICAoC7EUC7ILLL8KLCGKVy/wbV/kICA3ICAoBEEXBEIHF6/ULE8Jg3kERjFqSzmBealJCcQQYCAJJhDqVd/5IhDqInBKYaBKgnp6nrpVX/Fpr/oicBQMQgssIYgZH+4AgAFkoAIqDEKVcoF0oiKRcoN0BCwEk3xrv+cgIDkgICgHcRkHcgQsIYcsfwDBwGIACRd/4hev3iKQByYQfgIsAiYQYoJgwaJ7cBDDqZ4anB6dElVv+owSFu/qkB6NGhbf69BMLBHPLBGN0CgXb+4AgAzQq4JqhxmOGgu8C5JqB3wLgJqkSoYaq7C6ygrCC5CaCvBSC7wMya0tuADB7QroMW6gCtApnhycHlYv+Y4cjBKQmBPf0oOIynwJ8xwJnA1ikAVrL21qwAgTj9QqDHSVhGAACMPJwCxov+FsKiQTP9IqDIKVRGiP4AgTD9IqDJKVhGhf4AKCJW8qCtBYFT/uAIAKE//oFN/uAIAIFQ/uAIAEZ9/gAoMhbynq0FgUv+4AgAoqPogUX+4AgA4AIABnb+HfAAADZBAJ0CgqDAKAOHmQ/MMgwShgcADAIpA3zihg4AJhIHJiIWhgMAAACCoNuAKSOHmSYMIikDfPJGBwAioNwnmQgMEikDLQiGAwCCoN188oeZBgwSKQMioNsd8AAA");
					let size = text_data.length;
					let blocksize = 0x0800;
					let blocks = Math.floor((size + blocksize - 1) / blocksize);
					let offset = 0x400be000;
					await espMemoryBegin(size, blocks, blocksize, offset);

					for (let seq of Array(blocks).keys()) {
						let fromOffset = seq * blocksize;
						let toOffset = fromOffset + blocksize;
						if (toOffset > size) {
							toOffset = size;
						}
						await espMemoryData(text_data.substr(fromOffset, toOffset), seq);
					}

					// data
					let data_data = atob('CMD8Pw==');
					size = data_data.length;
					blocksize = 0x0800;
					blocks = Math.floor((size + blocksize - 1) / blocksize);
					offset = 0x3ffdeba8;
					await espMemoryBegin(size, blocks, blocksize, offset);

					for (let seq of Array(blocks).keys()) {
						let fromOffset = seq * blocksize;
						let toOffset = fromOffset + blocksize;
						if (toOffset > size) {
							toOffset = size;
						}
						await espMemoryData(data_data.substr(fromOffset, toOffset), seq);
					}

					await espMemoryEnd(0x400BE598);
				}

				espOutput("\nRun Stub\n");
			}

			async function espMemoryBegin(size, blocks, blocksize, offset) {
				let data = new ArrayBuffer(4 * 4);
				let dv = new DataView(data);

				// Data
				let index = 0;
				dv.setUint32(index, size, true);
				index += 4;
				dv.setUint32(index, blocks, true);
				index += 4;
				dv.setUint32(index, blocksize, true);
				index += 4;
				dv.setUint32(index, offset, true);
				index += 4;

				// Make Packet
				let packet = espMakePakcet(ESP_MEM_BEGIN, data);

				// Send
				for (let i = 0; i < 10; i++) {
					await espSendPacket(packet);

					// Receive
					const { value, done, error } = await espReceivePacket();

					if (!done && !error) {
						return;
					}
				}
			};

			async function espMemoryData(memdata, seq) {
				let data = new ArrayBuffer(4 * 4 + memdata.length);
				let dv = new DataView(data);

				// Data
				let index = 0;
				dv.setUint32(index, memdata.length, true);
				index += 4;
				dv.setUint32(index, seq, true);
				index += 4;
				dv.setUint32(index, 0, true);
				index += 4;
				dv.setUint32(index, 0, true);
				index += 4;
				for (let i = 0; i < memdata.length; i++) {
					dv.setUint8(index, memdata[i].charCodeAt(0));
					index++;
				}

				// Make Packet
				let packet = espMakePakcet(ESP_MEM_DATA, data, espChecksum(memdata));

				// Send
				for (let i = 0; i < 10; i++) {
					await espSendPacket(packet);

					// Receive
					const { value, done, error } = await espReceivePacket();

					if (!done && !error) {
						return;
					}
				}
			};

			async function espMemoryEnd(entrypoint = 0) {
				let data = new ArrayBuffer(2 * 4);
				let dv = new DataView(data);

				// Data
				let index = 0;
				dv.setUint32(index, parseInt(entrypoint == 0), true);
				index += 4;
				dv.setUint32(index, entrypoint, true);
				index += 4;

				// Make Packet
				let packet = espMakePakcet(ESP_MEM_END, data);

				// Send
				for (let i = 0; i < 10; i++) {
					await espSendPacket(packet);

					// Receive
					const { value, done, error } = await espReceivePacket();

					if (!done && !error) {
						return;
					}
				}
			};

			async function espChangeBaudrate(newBaudrate = 750000, nowBaudrate = 115200) {
				let data = new ArrayBuffer(2 * 4);
				let dv = new DataView(data);

				// Data
				let index = 0;
				dv.setUint32(index, newBaudrate, true);
				index += 4;
				dv.setUint32(index, nowBaudrate, true);
				index += 4;

				// Make Packet
				let packet = espMakePakcet(ESP_CHANGE_BAUDRATE, data);

				// Send
				for (let i = 0; i < 10; i++) {
					await espSendPacket(packet);

					// Receive
					const { value, done, error } = await espReceivePacket();

					if (!done && !error) {
						// Close
						if (espPort.readable) {
							if (espReader) {
								await espReader.cancel();
								await espReader.releaseLock();
							}
							await espPort.close();
							espReader = null;
						}

						// Reopen
						await espPort.open({ baudRate: newBaudrate });
						espOutput("ChangeBaudrate: " + newBaudrate + "\n");

						return;
					}
				}
			};

			async function espFlash(binData = [], offset) {
				for (let i = 0; i < binData.length; i++) {
					let size = binData[i].length;
					let blocksize = 0x4000;
					let blocks = Math.floor((size + blocksize - 1) / blocksize);
					await espFlashBegin(size, blocks, blocksize, offset[i]);

					for (let seq of Array(blocks).keys()) {
						let fromOffset = seq * blocksize;
						let toOffset = fromOffset + blocksize;
						if (toOffset > size) {
							toOffset = size;
						}
						let length = toOffset - fromOffset;

						console.log("espFlash fromOffset", fromOffset);
						console.log("espFlash toOffset", toOffset);
						console.log("espFlash seq", seq);
						console.log("espFlash bin.length", length);

						espOutput("Flash Write:" + (seq + 1) + "/" + blocks + "\n");
						await espFlashData(binData[i].substr(fromOffset, length), seq);
					}
				}

				await espFlashEnd(0x400BE598);

				espOutput("\nFlash End\n");

				await espDownload();
				await espReset();
			}

			async function espFlashBegin(size, blocks, blocksize, offset) {
				let data = new ArrayBuffer(4 * 4);
				let dv = new DataView(data);

				// Data
				let index = 0;
				dv.setUint32(index, size, true);
				index += 4;
				dv.setUint32(index, blocks, true);
				index += 4;
				dv.setUint32(index, blocksize, true);
				index += 4;
				dv.setUint32(index, offset, true);
				index += 4;

				console.log("espFlashBegin size", size);
				console.log("espFlashBegin blocks", blocks);
				console.log("espFlashBegin blocksize", blocksize);
				console.log("espFlashBegin offset", offset);

				// Make Packet
				let packet = espMakePakcet(ESP_FLASH_BEGIN, data);

				// Send
				for (let i = 0; i < 10; i++) {
					await espSendPacket(packet);

					// Receive
					const { value, done, error } = await espReceivePacket();

					if (!done && !error) {
						return;
					}
				}
			};

			async function espFlashData(memdata, seq) {
				let data = new ArrayBuffer(4 * 4 + memdata.length);
				let dv = new DataView(data);

				console.log("espFlashData length", memdata.length);
				console.log("espFlashData seq", seq);

				// Data
				let index = 0;
				dv.setUint32(index, memdata.length, true);
				index += 4;
				dv.setUint32(index, seq, true);
				index += 4;
				dv.setUint32(index, 0, true);
				index += 4;
				dv.setUint32(index, 0, true);
				index += 4;
				for (let i = 0; i < memdata.length; i++) {
					//console.log("espFlashData memdata["+i+"]", memdata[i], memdata[i].charCodeAt(0));
					dv.setUint8(index, memdata[i].charCodeAt(0));
					index++;
				}

				// Make Packet
				let packet = espMakePakcet(ESP_FLASH_DATA, data, espChecksum(memdata));

				// Send
				for (let i = 0; i < 10; i++) {
					await espSendPacket(packet);

					// Receive
					const { value, done, error } = await espReceivePacket();

					if (!done && !error) {
						return;
					}
				}
			};

			async function espFlashEnd(entrypoint = 0) {
				let data = new ArrayBuffer(2 * 4);
				let dv = new DataView(data);

				// Data
				let index = 0;
				dv.setUint32(index, parseInt(entrypoint == 0), true);
				index += 4;
				dv.setUint32(index, entrypoint, true);
				index += 4;

				// Make Packet
				let packet = espMakePakcet(ESP_FLASH_END, data);

				// Send
				for (let i = 0; i < 10; i++) {
					await espSendPacket(packet);

					// Receive
					const { value, done, error } = await espReceivePacket();

					if (!done && !error) {
						return;
					}
				}
			};

			// textarea を更新
			function addSerial(msg) {
				var textarea = document.getElementById('outputArea');
				textarea.value += msg;
				if($('#outputSrea').hasClass('is-auto-scroll')) {
					textarea.scrollTop = textarea.scrollHeight;
				}
			}

			// 書き込み、Writeボタン
			async function writeBtn() {
				$('#writebutton').addClass('is-loading');
				
				console.log("button clicked");
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
					addSerial("Error: Open" + error + "\n");
					$('#writebutton').removeClass('is-loading');
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
