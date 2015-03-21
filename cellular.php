<!DOCTYPE HTML> 
<html lang=en>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-15" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<!-- font -->
<link href='http://fonts.googleapis.com/css?family=Open+Sans:100,300,700,600,400' rel='stylesheet' type='text/css'>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<title> :evngpsn-#cellular </title>
<link rel="shortcut icon" href="favicon.ico" />
<link rel="stylesheet" type="text/css" href="cell.css" >
<?php 
// ingredients, functions, variables
include 'cellularBrain.php';
// uh ohhhhhh
set_time_limit(0);
ignore_user_abort(1);
// we need to start the session
// in order to make sure the variables
// are there when we refresh.
if (!isset($_SESSION)) {
	/*if($seedChart!=0) {
		// don't forget to clean up
		// that chart nonsense as well
		$qry = "chart".$seedChart.".png";
		unlink($qry);
		// reset seedChart variable to
		// ensure new filename.
		$seedChart = (int)((rand(1,183927)*1.2-9*rand(1,3))*rand(8,12)*.1);
	}*/
	// start up that php session
	// for grabbing POST variables.
	session_start();
	$thePortable =  'this is session id from index.php: ' .session_id();
	// this function lives in <include>.php
	debug_to_console($thePortable);
}
?>
</head>           
<body>
	<?php if(!isset($_GET['evolve']) && !isset($_GET['created']) && !isset($_GET['rawData'])): ?>	
		<?php 
			// variable init
			$_SESSION['evolveId']=1; 
			$_SESSION['worldName']=genWorldName();
		?>
		<div class="container">
			<h1>World Generator</h1>
			<form class="form" action="cellular.php?created=1" method="post">
				<!-- <p>
					<label for="island"><small>base land masses (1-20) </small></label>
					<input type="text" name="island" id="island" value="4" /> 
				</p> -->
				<p>
					<label for="island"><small>land expansion % (50-90%) </small></label>
					<input type="text" style="color:#121212;" name="expand" id="expand" value="80" /> 
				</p>
				<!-- <p>
					<label for="vision"><small>vision distance (2-max 5)</small></label>
					<input type="text" name="vision" id="vision" value="3" /> 
				</p> -->
				<label for="size"><small>world size</small></label>
				<select name="size">
					<?php
						$theClasses = array("small","medium","large","extra large");
						$i=0;
						$theSelected = rand(1,count($theClasses));
						foreach($theClasses as $class) {
							if(++$i == $theSelected) {
								echo "<option value='".$class."' selected>".ucfirst($class)."</option>";
							}
							else {
								echo "<option value='".$class."'>".ucfirst($class)."</option>";
							}
						}
					?>
				</select> 
				<br />
				<input class="submit" style="color:#121212;" type="submit" value="Create" /> 
			</form>
		</div>
	<?php else: ?>
		<?php if(isset($_GET['rawData'])): ?>
			<div class='world-container'>
				<?php drawBitMap($_SESSION['bigArray']); ?>
			</div>
		<?php elseif(isset($_GET['created'])): ?>
			<div class='world-container'>
				<?php $_SESSION['bigArray']=createMap($_POST); ?>
				<?php //$world = drawMap(12,12,$_POST['type']); ?>
			</div>
			<div class='toolTip shadow-1'>CLICK ON MAP TO <b>EVOLVE</b></div>
		<?php else: ?>
			<?php $_SESSION['evolveId']++; ?>
			<div class='world-container'>
				<?php $_SESSION['bigArray']=finalExpand($_SESSION['bigArray'][0],$_SESSION['bigArray'][1],$_SESSION['bigArray'][2],$_SESSION['bigArray'][3]); ?>
				<?php //$world = drawMap(12,12,$_POST['type']); ?>
			</div>
		<?php endif; ?>
		<div class='stickyMenu'><i class="fa fa-cog fa-2x"></i></div>
		<div class='stickyPanel'>
				<p class="list" style='width:100%;text-align:center;'><?php $_SESSION['worldName']; ?></p>
					<p class="list"><?php echo "Cells"; ?></p>
					<?php 
						$amount = $_SESSION['bigArray'][2]*$_SESSION['bigArray'][3];
					?>
					<p class="data"><?php echo $amount; ?></p>
				<?php if(!isset($_GET['rawData'])): ?>
					<p class="list"><?php echo "Evolution"; ?></p>
					<p class="data"><?php echo $_SESSION['evolveId']; ?></p>
					<p class="list" style='width:100%;text-align:center;'>
					<form action="cellular.php?rawData=1" method="post">	
						<input class='submit' type="submit" value="Raw Map Data" name="mapdata"><br /> 
					</form>
				<?php else: ?>
					<p class="list"><?php echo "Maximum Height"; ?></p>
					<?php 
						$max = 1;
						for($x=0;$x<$_SESSION['bigArray'][2];$x++) {
							for($y=0;$y<$_SESSION['bigArray'][3];$y++) {
								if($_SESSION['bigArray'][0][$x][$y]>$max) {
									$max=$_SESSION['bigArray'][0][$x][$y];
								}
							}
						}
						$_SESSION['max']=$max;
					?>
					<p class="data"><?php echo $_SESSION['max']; ?></p>
					<div style="clear:both"></div>
					<p class="list"><?php echo "Maximum Depth"; ?></p>
					<?php 
						$min = 0;
						for($x=0;$x<$_SESSION['bigArray'][2];$x++) {
							for($y=0;$y<$_SESSION['bigArray'][3];$y++) {
								if($_SESSION['bigArray'][0][$x][$y]<$min) {
									$min=$_SESSION['bigArray'][0][$x][$y];
								}
							}
						}
						$_SESSION['min']=$min;
					?>
					<p class="data"><?php echo $_SESSION['min']; ?></p>
				<?php endif; ?>
				<?php $theTempEvo =  $_SESSION['evolveId']+1; ?>
				<form action="cellular.php?evolve=<?php echo $theTempEvo; ?>" method="post">	
					<input class='submit' style="margin-top:1.3em;" type="submit" value="Evolve Map Data" name="mapdata"><br /> 
				</form>
				<form action="cellular.php" method="post">	
					<input class='submit refreshy' type="submit" value="Create New Map" name="mapdata"><br /> 
				</form>
				<input class='submit closer' type="submit" value="Close" name="close"><br /> 
			</p>
		</div>
		
	<?php endif; ?>
	<div class='loading'>Loading...</div>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript">
      $(document).ready(function(){
		//$('.square').addClass('animated fadeIn');
		$('.square').show().delay(300).queue(function() {
			$('.stickyMenu').addClass('animated bounceInUp');
			$('.stickyMenu').show();
		});
		$('.stickyMenu').click(function() {
			$('.stickyMenu').removeClass('bounceInLeft');
			$('.stickyMenu').addClass('animated bounceOutLeft');
			$('.stickyMenu').show();
			$('.stickyPanel').removeClass('bounceOutLeft');
			$('.stickyPanel').addClass('animated bounceInLeft');
			$('.stickyPanel').show();
		});
		$('.closer').click(function() {
			$('.stickyPanel').removeClass('bounceInLeft');
			$('.stickyPanel').addClass('animated bounceOutLeft');
			$('.stickyPanel').show();
			$('.stickyMenu').removeClass('bounceOutLeft');
			$('.stickyMenu').addClass('animated bounceInLeft');
			$('.stickyMenu').show();
		});
		/*$('.refreshy').click(function() {
			window.location = "cellular.php";
		});*/
		//$('.square').click(function() {
			// pull in session evolveId
			//var evolveId = "<?php echo $_SESSION['evolveId']+1; ?>";
			// flush display
			//$( ".world-container" ).empty();
			// trigger PHP function
			//window.location = "cellular.php?evolve="+evolveId;
		//});
		/*$('.square').click(function(e) {
			$.ajax({ 
				url: 'cellular.php?type=3',
				success: function() {
				}
			});
		});*/
		/*$('.water').click(function(e) {
			$('.loading').show('slow',function(){
				$.ajax({ 
					url: 'cellular.php?type=0',
					success: function() {
						$('.loading').hide();
					}
				});
			});
		});*/
	  });
	</script>
</body>
</html>