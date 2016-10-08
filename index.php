<?php 
	function __autoload($class_name) {
	    include $class_name . '.php';
	}

	$fontsize = 16;
	$bpp = 4;
	$ascii_range = "33-126";

	if ($_SERVER['REQUEST_METHOD'] === 'POST') { 

		
		if (!preg_match("/^\d+-\d+$/",$ascii_range)) {
			throw new Exception("Invalid ascii range");
		}


		
		$fontfile = sys_get_temp_dir() . DIRECTORY_SEPARATOR .  basename($_FILES['ttffile']['name']);

		
		if (move_uploaded_file($_FILES['ttffile']['tmp_name'], $fontfile)) {
			$ttftocode = new TtfToCode();

			$fontsize = $_POST['fontsize'];
			$ttftocode->setFontSize($fontsize);
			$ttftocode->setFontFile($fontfile);

			$ascii_range = $_POST['ascii_range'];
			$range = explode("-", $ascii_range);
			$ttftocode->setCharRange($range[0], $range[1]);
	
			$bpp = $_POST['bpp'];
			$ttftocode->setBitsPerPixel($bpp);
			$ttftocode->setLanguage($_POST['language']);		

			$result = $ttftocode->process();
			$image = $ttftocode->getFontAsInlineImage();
		}
		else {
			throw new Exception("Can not upload file");
		}

		
	}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
   
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

		 <title>True Type to Code</title>
	</head>
	<body>
		<div class="container">
			<div class="jumbotron">
		        <h1>True Type to Code</h1>
		        <p class="lead">
		        	Convert any true type font to code bitmap structures.
		        </p>
		        
		    </div>
			<div class="panel panel-default">
				<div class="panel-heading">Input</div>
				<div class="panel-body">
				
					<form action="index.php" method="post" enctype="multipart/form-data">
	  					<div class="form-group">
							<label for="ttffile">True Type Font</label>
	    					<input type="file" id="ttffile" name="ttffile" required>
	    				</div>

	    				<div class="form-group">
							<label for="fontsize">Font size</label>
	    					<input class="form-control" type="number" id="fontsize" name="fontsize" required value="<?php echo $fontsize ?>">
	    				</div>

	    				<div class="form-group">
	    					<label for="language">Language</label>
	    					<select id="language" class="form-control" name="language" required>
								<option value="c1">C - Arduino PROGMEM</option>
							</select>
	    				</div>

	    				<div class="form-group">
	    					<label for="bpp">Bits per pixel</label>
	    					<select id="bpp" class="form-control" name="bpp" required>
	    						<option <?php echo $bpp==1 ? "selected" : "" ?> >1</option>
								<option <?php echo $bpp==2 ? "selected" : "" ?> >2</option>
								<option <?php echo $bpp==4 ? "selected" : "" ?> >4</option>
								<option <?php echo $bpp==8 ? "selected" : "" ?> >8</option>
							</select>
	    				</div>

	    				<div class="form-group">
							<label for="ascii_range">Ascii Range</label>
	    					<input class="form-control" type="text" id="ascii_range" name="ascii_range" required value="<?php echo $ascii_range ?>">
	    				</div>

	    				

	    				<button type="submit" class="btn btn-default">Convert</button>
    				</form>
				</div>
			</div>
			<?php if (isset($result)) { ?> 
				<div class="panel panel-default">
					<div class="panel-heading">Result</div>
					<div class="panel-body">
						<p>
							<img src="<?php echo $image ?>"/>	
						</p>
						<p>
							<pre><?php echo $result ?></pre>
						</p>
						
					</div>
				</div>
			<?php } ?>
		</div>
	</body>
</html>
