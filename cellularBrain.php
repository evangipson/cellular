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

function padCheck($x,$y) {
	$pad = floor($GLOBALS['width']/mt_rand(8,25))<=1 ? 3 : floor($GLOBALS['width']/mt_rand(8,25));//mt_rand(3,6);
	if(($x>$pad && $x<($GLOBALS['width']-$pad))&&($y>$pad && $y<($GLOBALS['height']-$pad))) {
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

function cellExpand($x,$y) {
	// pull in the global world for editing
	$world = $GLOBALS['world'];
	if(padCheck($x,$y)==false) {
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
	if($runningTotal == 1 || ($runningTotal > 4 && $runningTotal < 6 )) {
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
	// set the global to the modified world
	$GLOBALS['world'] = $world;
}
function drown($x,$y) {
	// pull in the global world for editing
	$world = $GLOBALS['world'];
	$runningTotal=0;
	//$theCheck = $world[$x][$y];
	if($world[$x][$y]<=0) {
		$theCheck=0;
		for($i=-2;$i<=2;$i++) {
		    for($r=-2;$r<=2;$r++) {
			if(isset($world[$x+$i][$y+$r]) && $world[$x+$i][$y+$r]<=$theCheck) {
			    $runningTotal += 1;
			}
			// we have to make the edge of the map
			// count toward drowning goals
			elseif(!isset($world[$x+$i][$y+$r])) {
			    $runningTotal += 1;
			}
		    }
		}
		if($runningTotal < mt_rand(13,15)) {
			$world[$x][$y]=1;
		}
		if($runningTotal >= 25) {
		    if($world[$x][$y]>-2) {
			$world[$x][$y]--;
		    }
		}
		elseif($runningTotal >= 20) {
		    if($world[$x][$y]>-1) {
			$world[$x][$y]--;
		    }
		}
	}
	else {
		$theCheck=1;
		for($i=-2;$i<=2;$i++) {
			for($r=-2;$r<=2;$r++) {
				if(isset($world[$x+$i][$y+$r]) && $world[$x+$i][$y+$r]>=$theCheck) {
					$runningTotal++;
				}
			}
		}
		if($runningTotal >= 24) {
			//debug_to_console('Made a mountain!');
			// set our max to 3
			if($world[$x][$y]<3) {
				$world[$x][$y]++;
			}
		}
		elseif($runningTotal >= 19) {
			//debug_to_console('Made a mountain!');
			// set our max to 3
			if($world[$x][$y]<2) {
				$world[$x][$y]++;
			}
		}
		// "sticky" land variable
		elseif($runningTotal <= 12) {
		    $world[$x][$y]=0;
		}
	}
	// set the global to the modified world
	$GLOBALS['world'] = $world;
	//debug_to_console("runningTotal: ".$runningTotal);
	/*debug_to_console("evaluating: ".($world[$x][$y]>0?"land":"water"));*/
}
// a more general expansion approach
function recExpand() {
	// adding an element of randomness
	// with the weird for loops
	$xIter = $yIter = array();
	for ($x=0;$x<$GLOBALS['width'];$x++)  {
		$xIter[$x]=$x;
		$yIter[$x]=$x;
	}
	for($x=0;$x<$GLOBALS['width'];$x++) {
		$newX = $xIter[mt_rand(0,count($xIter)-1)];
		array_splice($xIter,$newX,1);
		for($y=0;$y<$GLOBALS['height'];$y++) {
			$newY = $yIter[mt_rand(0,count($yIter)-1)];
			array_splice($yIter,$newY,1);
			// we need to peek at our world array
			// for JUST a second and only IF our
			// conditions are met.
			if($GLOBALS['world'][$newX][$newY]==1) {
				cellExpand($newX,$newY);
			}
		}
	}
}
//a function for biome introduction
function biomeInjection() {
	// pull in global variable set upon load
	$visionDist = $GLOBALS['vision'];
	// pull in the global world for editing
	$world = $GLOBALS['world'];
	// get some variables about for loop limits
	// before we execute them to save execution time
	$xLimit = $GLOBALS['width']-$visionDist;
	$yLimit = $GLOBALS['height']-$visionDist;
	// let's get our number we're going to test against
	$landTest = 0;
	for($i=1;$i<=$visionDist;$i++) {
	    $landTest += $i * 8;
	}
	// agent-based evaluation
	// evaluate tiles far away
	// and get aggregate, handle accordingly
	for($x=$visionDist;$x<$xLimit;$x++) {
	    for($y=$visionDist;$y<$yLimit;$y++) {
		// we only care about land for this chunk
		if($world[$x][$y]>=2) {
		
		    $runningTotal = 0;
		    
		    for($widthVision=-$visionDist;$widthVision<=$visionDist;$widthVision++) {
			// have to test for 1, and everything
			// above 1 as well, since we are "post-drown"
			for($heightVision=-$visionDist;$heightVision<=$visionDist;$heightVision++) {
			    $GLOBALS['iterCount']++;
			    if($world[$x+$widthVision][$y+$heightVision]>=2) {
				    $runningTotal++;
			    }
			}
		    }
		    // if we had land tiles surrounding this thing
		    // COMPLETELY for our entire vision....
		    if($runningTotal>=$landTest) {
			    //debug_to_console('we have a city!  this somehow worked.');
			    $world[$x][$y]=4;
		    }
		}
	    }
	}
	// set the global to the modified world
	$GLOBALS['world'] = $world;
}
//a final expansion function
function finalExpand() {
	// we'll drown a few times
	for($i=0;$i<2;$i++) {
		for($x=0;$x<$GLOBALS['width'];$x++) {
			for($y=0;$y<$GLOBALS['height'];$y++) {
			    drown($x,$y);
			}
		}
	}
	// need to re-drown land one last pass
	// for foresting issues
	for($x=0;$x<$GLOBALS['width'];$x++) {
		for($y=0;$y<$GLOBALS['height'];$y++) {
		    //if($GLOBALS['world'][$x][$y]==1) {
			drown($x,$y);
		    //}
		}
	}
	biomeInjection();
	//debug_to_console('the loop ran: '.$GLOBALS['iterCount'].' times.');
}
// we are messing with the world global array here,
// as well as the iter global.  this is such a global
// function.  :-D
function createMap() {
	// clean slate, a new world!
	$GLOBALS['world'] = array();
	// we are generating
	// a fresh map
	for($x=0;$x<$GLOBALS['width'];$x++) {
		$GLOBALS['world'][$x] = array();
		for($y=0;$y<$GLOBALS['height'];$y++) { 
			//random roll
			if(padCheck($x,$y)==true && islandCheck()==true) {
				//we have an island if it passes!
				$GLOBALS['world'][$x][$y]=1;
			}
			else {
				//just some water
				$GLOBALS['world'][$x][$y]=0;
			}
		}
	}
	// additional iterations
	for($iterator=0;$iterator<$GLOBALS['iter'];$iterator++) {
		recExpand();
	}
	finalExpand();
}
function drawMap($dataArray) {
	// gotta set up our incoming data!
	$GLOBALS['islandChance']=5;//$dataArray['island'];
	$GLOBALS['expandChance']=$dataArray['expand'];
	$GLOBALS['iter']=250;
	$GLOBALS['vision']=4;//$dataArray['vision'];
	$GLOBALS['iterCount']=0;
	// let's pull in the size to a global variable.
	$GLOBALS['size'] = $dataArray['size'];
	// set width and height from incoming size
	if($GLOBALS['size']=="small") {
		$GLOBALS['width'] = $GLOBALS['height'] = mt_rand(15,30);
	}
	elseif($GLOBALS['size']=="medium") {
		$GLOBALS['width'] = $GLOBALS['height'] = mt_rand(30,60);
	}
	elseif($GLOBALS['size']=="large") {
		$GLOBALS['width'] = $GLOBALS['height'] = mt_rand(60,100);
	}
	// extra large
	else {
		//$GLOBALS['islandChance']= 15;
		$GLOBALS['width'] = $GLOBALS['height'] = mt_rand(180,220);;//mt_rand(170,190);
		$GLOBALS['iter']= mt_rand(400,700);
		$GLOBALS['vision'] = 6;
	}
	// let's create! (we're assuming that $GLOBALS['world'] lives
	// after this function is called)
	// $GLOBALS['world'] = createMap();
	createMap();
	// this will be silly, to animate
	$animCounter = 0;
	// go through map
	for($x=0;$x<$GLOBALS['width'];$x++) {
		echo '<div style="clear:both"></div>';
		for($y=0;$y<$GLOBALS['height'];$y++) { 
			// calculate animation
			// in milliseconds
			$animTime = $animCounter/1000;
			// city
			if($GLOBALS['world'][$x][$y]==4) {
				echo "<div style='width:".(100/$GLOBALS['width'])."vw;height:".(100/$GLOBALS['height'])."vh;-webkit-animation-delay: ".$animTime."s;animation-delay: ".$animTime."s;' class='square city'></div>";
			}
			// mountain
			elseif($GLOBALS['world'][$x][$y]==3) {
				echo "<div style='width:".(100/$GLOBALS['width'])."vw;height:".(100/$GLOBALS['height'])."vh;-webkit-animation-delay: ".$animTime."s;animation-delay: ".$animTime."s;' class='square mountain'></div>";
			}
			// grass
			elseif($GLOBALS['world'][$x][$y]==2) {
				echo "<div style='width:".(100/$GLOBALS['width'])."vw;height:".(100/$GLOBALS['height'])."vh;-webkit-animation-delay: ".$animTime."s;animation-delay: ".$animTime."s;' class='square grass'></div>";
			}
			// island
			elseif($GLOBALS['world'][$x][$y]==1) {
				echo "<div style='width:".(100/$GLOBALS['width'])."vw;height:".(100/$GLOBALS['height'])."vh;-webkit-animation-delay: ".$animTime."s;animation-delay: ".$animTime."s;' class='square island'></div>";
			}
			// water
			elseif($GLOBALS['world'][$x][$y]==0) {
				echo "<div style='width:".(100/$GLOBALS['width'])."vw;height:".(100/$GLOBALS['height'])."vh;-webkit-animation-delay: ".$animTime."s;animation-delay: ".$animTime."s;' class='square water'></div>";
			}
			// deep-water
			elseif($GLOBALS['world'][$x][$y]==-1) {
				echo "<div style='width:".(100/$GLOBALS['width'])."vw;height:".(100/$GLOBALS['height'])."vh;-webkit-animation-delay: ".$animTime."s;animation-delay: ".$animTime."s;' class='square deep-water'></div>";
			}
			// abyss
			elseif($GLOBALS['world'][$x][$y]<-1) {
				echo "<div style='width:".(100/$GLOBALS['width'])."vw;height:".(100/$GLOBALS['height'])."vh;-webkit-animation-delay: ".$animTime."s;animation-delay: ".$animTime."s;' class='square abyss'></div>";
			}
			// increase animation counter
			$animCounter++;
		}
	}
}



?>
