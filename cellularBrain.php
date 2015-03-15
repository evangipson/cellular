<?php
// this function will write output
// to the console, and takes in some
// data, as either array or variable.
function debug_to_console( $data ) {
    if ( is_array( $data ) )
        $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data ) . "' );</script>";
    else
        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";
    echo $output;
}

function padCheck($x,$y,$width,$height) {
	$pad = floor($width/50)<=1 ? 2 : floor($width/50);//mt_rand(3,6);
	if(($x>$pad && $x<($width-$pad))&&($y>$pad && $y<($height-$pad))) {
		return true;
	}
	return false;
}

function islandCheck() {
	//$islandChance = 5;
	//shouldn't need to declare 
	//variable because of GLOBALS array
	if(mt_rand(1,100)<$GLOBALS['islandChance']) {
		return true;
	}
	return false;
}
function expandCheck() {
	//$expandChance = 80;
	//shouldn't need to declare 
	//variable because of GLOBALS array
	if(mt_rand(1,100)<$GLOBALS['expandChance']) {
		return true;
	}
	return false;
}

function cellExpand($width,$height,$x,$y,&$world) {
	if(padCheck($x,$y,$width,$height)==false) {
		return 0;
	}
	$runningTotal = 1;
	if($world[$x-1][$y-1]==1) {
		$runningTotal += 1;
	}
	if($world[$x][$y-1]==1) {
		$runningTotal += 1;
	}
	if($world[$x+1][$y-1]==1) {
		$runningTotal += 1;
	}
	if($world[$x-1][$y]==1) {
		$runningTotal += 1;
	}
	if($world[$x+1][$y]==1) {
		$runningTotal += 1;
	} 
	if($world[$x-1][$y+1]==1) {
		$runningTotal += 1;
	}
	if($world[$x][$y+1]==1) {
		$runningTotal += 1;
	}
	if($world[$x+1][$y+1]==1) {
		$runningTotal += 1;
	}
	// if $runningTotal == 1, we need initial expansion
	// if it's inbetween 2 and 6, we need to expand
	if($runningTotal == 1 || ($runningTotal > 3 && $runningTotal < 5) ) {
		if(expandCheck()) {
			if(expandCheck()) {
				$world[$x-1][$y-1]=1;
			}
			if(expandCheck()) {
				$world[$x][$y-1]=1;
			}
			if(expandCheck()) {
				$world[$x+1][$y-1]=1;
			}
			if(expandCheck()) {
				$world[$x-1][$y]=1;
			}
			if(expandCheck()) {
				$world[$x+1][$y]=1;
			}
			if(expandCheck()) {
				$world[$x-1][$y+1]=1;
			}
			if(expandCheck()) {
				$world[$x][$y+1]=1;
			}
			if(expandCheck()) {
				$world[$x+1][$y+1]=1;
			}
		}
		else {
		    
		    $world[$x-1][$y-1]=1;
		    $world[$x][$y-1]=1;
		    $world[$x+1][$y-1]=1;
		    $world[$x-1][$y]=1;
		    $world[$x+1][$y]=1;
		    $world[$x-1][$y+1]=1;
		    $world[$x][$y+1]=1;
		    $world[$x+1][$y+1]=1;
		}
	}
}
function drown($x,$y,&$world) {
	$runningTotal=0;
	//$theCheck = $world[$x][$y];
	if($world[$x][$y]<=0) {
		$theCheck=1;
		if($world[$x-1][$y-1]>=$theCheck) {
			$runningTotal += 1;
		}
		if($world[$x][$y-1]>=$theCheck) {
			$runningTotal += 1;
		}
		if($world[$x+1][$y-1]>=$theCheck) {
			$runningTotal += 1;
		}
		if($world[$x-1][$y]>=$theCheck) {
			$runningTotal += 1;
		}
		if($world[$x+1][$y]>=$theCheck) {
			$runningTotal += 1;
		}
		if($world[$x-1][$y+1]>=$theCheck) {
			$runningTotal += 1;
		}
		if($world[$x][$y+1]>=$theCheck) {
			$runningTotal += 1;
		}
		if($world[$x+1][$y+1]>=$theCheck) {
			$runningTotal += 1;
		}
		if($runningTotal >= 7) {
			$world[$x][$y]=1;
		}
		elseif($runningTotal < 1) {
		    $world[$x][$y]=-1;
		}
	}
	else {
		$theCheck=1;
		for($i=-2;$i<=2;$i++) {
			for($r=-2;$r<=2;$r++) {
				if($world[$x+$i][$y+$r]>=$theCheck) {
					$runningTotal++;
				}
			}
		}
		if($runningTotal >= 22) {
			//debug_to_console('Made a mountain!');
			// set our max to 3
			if($world[$x][$y]<3) {
				$world[$x][$y]++;
			}
		}
		elseif($runningTotal >= 18) {
			//debug_to_console('Made a mountain!');
			// set our max to 3
			if($world[$x][$y]<2) {
				$world[$x][$y]++;
			}
		}
		elseif($runningTotal < 6) {
		    $world[$x][$y]=0;
		}
	}
	//debug_to_console("runningTotal: ".$runningTotal);
	/*debug_to_console("evaluating: ".($world[$x][$y]>0?"land":"water"));*/
}
//a more general expansion approach
function recExpand($width,$height,&$world) {
	// adding an element of randomness
	// with the weird for loops
	$xIter = $yIter = array();
	for ($x=1;$x<$width-1;$x++)  {
		$xIter[$x]=$x;
		$yIter[$x]=$x;
	}
	for($x=1;$x<$width-1;$x++) {
		$randomSelect = mt_rand(1,count($xIter));
		$newX = $xIter[$randomSelect];
		array_splice($xIter,$randomSelect,1);
		for($y=1;$y<$height-1;$y++) {
			$randomSelect = mt_rand(1,count($yIter));
			$newY = $yIter[$randomSelect];
			array_splice($yIter,$randomSelect,1);
			if($world[$newX][$newY]==1) {
				cellExpand($width,$height,$newX,$newY,$world);
			}
		}
	}
}
//a function for biome introduction
function biomeInjection($width,$height,&$world) {
	// pull in global variable set upon load
	$visionDist = $GLOBALS['vision'];
	// get some variables about for loop limits
	// before we execute them to save execution time
	$xLimit = $width-$visionDist;
	$yLimit = $height-$visionDist;
	// agent-based evaluation
	// evaluate tiles far away
	// and get aggregate, handle accordingly
	/*for($x=$visionDist;$x<$xLimit;$x++) {
		for($y=$visionDist;$y<$yLimit;$y++) {
			// we only care about land for this chunk
			if($world[$x][$y]>=2) {
			
				$runningTotal = 0;
				
				for($widthVision=-$visionDist;$widthVision<$visionDist;$widthVision++) {
					// have to test for 1, and everything
					// above 1 as well, since we are "post-drown"
					for($heightVision=-$visionDist;$heightVision<$visionDist;$heightVision++) {
						$GLOBALS['iterCount']++;
						if($world[$x+$widthVision][$y+$heightVision]>=2) {
							if($runningTotal>=($visionDist*9)) {
								break;
							}
							$runningTotal++;
						}
					}
					if($runningTotal>=($visionDist*9)) {
						break;
					}
				}
				if($runningTotal>=(9*$visionDist)) {
					//debug_to_console('we have a city!  this somehow worked.');
					$world[$x][$y]=4;
				}
			}
		}
	}*/
}
//a final expansion function
function finalExpand($width,$height,&$world) {
	// we'll drown a few times
	for($i=0;$i<3;$i++) {
		// adding an element of randomness
		// with the weird for loops
		/*$xIter = array();
		$yIter = array();
		for ($x=0;$x<$width;$x++)  {
			$xIter[$x]=$x;
			$yIter[$x]=$x;
		}
		for($x=0;$x<$width;$x++) {
			$newX = $xIter[mt_rand(0,count($xIter))];
			array_splice($xIter,$newX,1);
			for($y=0;$y<$height;$y++) {
				$newY = $yIter[mt_rand(0,count($yIter))];
				array_splice($yIter,$newY,1);
				$theDebugString = 'drowning tile: ('.$newX.','.$newY.'), and it is: '.$world[$newX][$newY].'.';
				debug_to_console($theDebugString);
				drown($newX,$newY,$world);
			}
		}*/
		for($x=0;$x<$width;$x++) {
			for($y=0;$y<$height;$y++) {
				drown($x,$y,$world);
			}
		}
	}
	biomeInjection($width,$height,$world);
	debug_to_console('the loop ran: '.$GLOBALS['iterCount'].' times.');
}
function createMap($width,$height,$type = -1) {
	// clean slate, a new world!
	$world = array();
	// if our type is -1, we are generating
	// a fresh map
	if($type==-1) {
		// plant seeds
		for($x=0;$x<$width;$x++) {
			$world[$x] = array();
			for($y=0;$y<$height;$y++) { 
				//random roll
				if(padCheck($x,$y,$width,$height)==true && islandCheck()==true) {
					//we have an island if it passes!
					$world[$x][$y]=1;
				}
				else {
					//just some water
					$world[$x][$y]=0;
				}
			}
		}
		// additional iterations
		for($iterator=0;$iterator<$GLOBALS['iter'];$iterator++) {
			recExpand($width,$height,$world);
		}
		finalExpand($width,$height,$world);
	}
	// otherwise, we are going to be generating
	// a section of the already living map
	else {
		// it was land!
		if($type>=1) {
			// plant seeds
			for($x=0;$x<$width;$x++) {
				echo '<div style="clear:both"></div>';
				$world[$x] = array();
				for($y=0;$y<$height;$y++) { 
					// random grass tile
					$world[$x][$y] = mt_rand(1,100)>66 ? 1 : mt_rand(1,100)>33 ? 2 : 3 ;
				}
			}
		}
		//it was water
		else {
			$world[$x][$y] = 0;
		}
	}
	return $world;
}
function drawMap($dataArray) {
	
	if($world==null) {
		// debug
		// gotta set up our incoming data!
		$GLOBALS['islandChance']=$dataArray['island'];
		$GLOBALS['expandChance']=$dataArray['expand'];
		$GLOBALS['iter']=mt_rand(150,250);
		$GLOBALS['vision']=$dataArray['vision'];
		$GLOBALS['iterCount']=0;
		// set width and height from incoming size
		if($dataArray['size']=="small") {
			$width = $height = mt_rand(9,20);
		}
		elseif($dataArray['size']=="medium") {
			$width = $height = mt_rand(20,30);
		}
		elseif($dataArray['size']=="large") {
			$width = $height = mt_rand(30,40);
		}
		else {
			$width = $height = mt_rand(40,50);
		}
		// let's create!
		$world = createMap($width,$height);
	}
	else {
		$world = createMap($width,$height,$worldValue);
	}
	// put the map inside of a conatiner
	echo '<div class="container">';
	// this will be silly, to animate
	$animCounter = 0;
	// go through map
	for($x=0;$x<$width;$x++) {
		echo '<div style="clear:both"></div>';
		for($y=0;$y<$height;$y++) { 
			// calculate animation
			$animTime = $animCounter/1000;
			// city
			if($world[$x][$y]==4) {
				echo "<div style='width:".(100/$width)."vw;height:".(100/$height)."vh;-webkit-animation-delay: ".$animTime."s;animation-delay: ".$animTime."s;' class='square city'></div>";
			}
			// mountain
			elseif($world[$x][$y]==3) {
				echo "<div style='width:".(100/$width)."vw;height:".(100/$height)."vh;-webkit-animation-delay: ".$animTime."s;animation-delay: ".$animTime."s;' class='square mountain'></div>";
			}
			// grass
			elseif($world[$x][$y]==2) {
				echo "<div style='width:".(100/$width)."vw;height:".(100/$height)."vh;-webkit-animation-delay: ".$animTime."s;animation-delay: ".$animTime."s;' class='square grass'></div>";
			}
			// island
			elseif($world[$x][$y]==1) {
				echo "<div style='width:".(100/$width)."vw;height:".(100/$height)."vh;-webkit-animation-delay: ".$animTime."s;animation-delay: ".$animTime."s;' class='square island'></div>";
			}
			// water
			elseif($world[$x][$y]==0) {
				echo "<div style='width:".(100/$width)."vw;height:".(100/$height)."vh;-webkit-animation-delay: ".$animTime."s;animation-delay: ".$animTime."s;' class='square water'></div>";
			}
			// deep-water
			elseif($world[$x][$y]<0) {
				echo "<div style='width:".(100/$width)."vw;height:".(100/$height)."vh;-webkit-animation-delay: ".$animTime."s;animation-delay: ".$animTime."s;' class='square deep-water'></div>";
			}
			// increase animation counter
			$animCounter++;
		}
	}
	//end the container div
	echo '</div>';
}



?>