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
			<div class="block has-text-centered">
				Rubyファイル（.rb）をアップロード
			</div>

			<div class="field is-grouped is-grouped-centered">
				<div id="master-file" class="file is-centered is-info has-name">
					<label class="file-label">
						<input id="masterfile" class="file-input" type="file" name="resume">
						<span class="file-cta">
							<span class="file-icon">
								<i class="fas fa-upload"></i>
							</span>
							<span class="file-label">
								master file
							</span>
						</span>
						<span class="file-name">
							No file uploaded
						</span>
					</label>
				</div>
			</div>
			
			<div class="field is-grouped is-grouped-centered">
				<div id="slave-file" class="file is-centered is-info has-name">
					<label class="file-label">
						<input id="slavefile" class="file-input" type="file" name="resume">
						<span class="file-cta">
							<span class="file-icon">
								<i class="fas fa-upload"></i>
							</span>
							<span class="file-label">
								slave file
							</span>
						</span>
						<span class="file-name">
							No file uploaded
						</span>
					</label>
				</div>
			</div>
			
			<div class="columns">
				<div class="column"></div>
			</div>

			<div class="field is-grouped is-grouped-centered">
				<p class="control">
					<button id="compilebutton" class="button is-primary" onclick="compileBtn();">
						<span class="icon is-small">
							<i class="fa-solid fa-wrench"></i>
						</span>
						<span>コンパイル</span>
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
			
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
			<div class="field is-grouped "></div>
		</div>
		
		<footer class="footer">
			<div class="content has-text-centered">
				<p class="copy-right">Copyright &copy; kanicon 2022</p>
			</div>
		</footer>
		
		<script type="text/javascript" src="js/espserial.js" charset="utf-8"></script>
		<script>
			var master_code = null;
			var slave_code = null;
			
			// ファイルの中身（テキスト）をロード
			window.addEventListener('DOMContentLoaded', function(){
				document.getElementById("masterfile").addEventListener('change', function(e){
					var file_reader = new FileReader();

					file_reader.addEventListener('load', function(e) {
						master_code = e.target.result;
					});
					file_reader.readAsText(e.target.files[0]);
				});
				
				document.getElementById("slavefile").addEventListener('change', function(e){
					var file_reader = new FileReader();
					
					file_reader.addEventListener('load', function(e) {
						slave_code = e.target.result;
					});
					file_reader.readAsText(e.target.files[0]);
				});
			});
			
			// upload.phpに送る
			function compileBtn() {
				console.log(master_code);
				console.log(slave_code);
				
				var ele = document.createElement('form');
				ele.action = './upload.php';
				ele.method = 'post';
				ele.setAttribute('target', '_blank');
				
				if(master_code) {
					var q = document.createElement('textarea');
					q.value = master_code;
					q.name = 'master_code';
					ele.appendChild(q);
				}
				if(slave_code) {
					var s = document.createElement('textarea');
					s.value = slave_code;
					s.name = 'slave_code';
					ele.appendChild(s);
				}
				
				document.body.appendChild(ele);
				ele.submit();
			}

			// ファイルアップロードで表示をファイル名に変更
			const masterInput = document.querySelector('#master-file input[type=file]');
			const slaveInput = document.querySelector('#slave-file input[type=file]');
			masterInput.onchange = () => {
				if (masterInput.files.length > 0) {
					const fileName = document.querySelector('#master-file .file-name');
					fileName.textContent = masterInput.files[0].name;
				}
			}
			slaveInput.onchange = () => {
				if (slaveInput.files.length > 0) {
					const fileName = document.querySelector('#slave-file .file-name');
					fileName.textContent = slaveInput.files[0].name;
				}
			}
			
			// タブを閉じる
			function windowClose() {
				open('about: blank', '_self').close();
			}
		</script>
	</body>
</html>
