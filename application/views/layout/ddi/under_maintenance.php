<!DOCTYPE html>
<html>
<head>
	<title>Maintenance AMCHAM</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;900&display=swap" rel="stylesheet">
	<style type="text/css">
		body {
			font-family: 'Roboto', sans-serif;
			font-weight: 400;
			margin: 0 auto;
		}
		* {
			box-sizing: border-box;
		}
		.frame {
			height: 100vh;
			width: 100%;
			display: flex;
			justify-content: center;
			align-items: center;
		}
		.inframe {
			display: inline-flex;
			padding: 15px;
		}
		.inframe div {
			display: flex;
			align-items: center;
			padding-left: 100px;
		}
		h2 {
			font-size: 48px;
			font-weight: 900;
			color: #3E3E3E;
			margin-bottom: 10px;
		}
		p {
			font-size: 20px;
			color: #9B9B9B;
		}
		img {
			max-width: 40%;
		}
		@media screen and (max-width: 767px) {
			.inframe div {
				padding-left: 30px;
			}
		}
		@media screen and (max-width: 640px) {
			.inframe {
				display: block;
				text-align: center;
			}
			.inframe div {
				padding-left: 0;
			}
			img {
				max-width: 100%;
			}
		}
	</style>
</head>
<body>
	<div class="frame">
		<div class="inframe">
			<img src="<?php echo $config['base_url'];?>asset/images/under_construction.png">
			<div>
				<span>
					<h2>Under <br>Maintenance</h2>
					<p>Our website are currently under maintenance...<br>
					Thank you for your patience, we'll be back soon!</p>
				</span>
			</div>
		</div>
	</div>
</body>
</html>