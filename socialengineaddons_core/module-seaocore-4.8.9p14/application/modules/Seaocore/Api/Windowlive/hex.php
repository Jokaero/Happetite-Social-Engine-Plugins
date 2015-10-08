<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions 
 * @package    Suggestion
 * @copyright  Copyright 2009-2010 BigStep Technologies Pvt. Ltd.
 * @license    http://www.socialengineaddons.com/license/
 * @version    $Id: hex.php 2010-08-17 9:40:21Z SocialEngineAddOns $
 * @author     SocialEngineAddOns
 */

	//A brute force method to be able to convert hex to double long int
	//
	//With PHP not supporting DOUBLE LONG INT (at least at the time I wrote it), I had to 
	//resort to drastic measures...
	
	$hi[19]=2328306436;		$lo[19]=2313682944;
	$hi[18]=232830643;		$lo[18]=2808348672;
	$hi[17]=23283064;		$lo[17]=1569325056;
	$hi[16]=2328306;		$lo[16]=1874919424;
	$hi[15]=232830;			$lo[15]=2764472320;
	$hi[14]=23283;			$lo[14]=276447232;
	$hi[13]=2328;			$lo[13]=1316134912;
	$hi[12]=232;			$lo[12]=3567587328;
	$hi[11]=23;				$lo[11]=1215752192;
	$hi[10]=2;				$lo[10]=1410065408;
	$hi[9]=0;				$lo[9]=1000000000;
	$hi[8]=0;				$lo[8]=100000000;
	$hi[7]=0;				$lo[7]=10000000;
	$hi[6]=0;				$lo[6]=1000000;
	$hi[5]=0;				$lo[5]=100000;
	$hi[4]=0;				$lo[4]=10000;
	$hi[3]=0;				$lo[3]=1000;
	$hi[2]=0;				$lo[2]=100;
	$hi[1]=0;				$lo[1]=10;
	$hi[0]=0;				$lo[0]=1;
	
	$hexTop = substr($hexIn,0,8);
	$hexBottom = substr($hexIn,8,8);

	$decTop = hexdec($hexTop);
	$decBottom = hexdec($hexBottom);
	
	$sign="pos";
	
	if ($decTop>2147483647)
	{
		$sign="neg";
		$decTop=$decTop-2147483648;

		$decTop=($decTop-2147483648)*-1 -1;
		$decBottom=($decBottom-4294967296)*-1;		
	}

	
	for ($x=19;$x>9;$x--)
	{
		$div[$x]=0;
		while ($decTop>=$hi[$x] )
		{
			if ($decTop==$hi[$x] && $decBottom<$lo[$x])
			{
				break;
			}
			$div[$x]++;
			$decTop=$decTop-$hi[$x];
			if ($decBottom<$lo[$x])
			{				
				$decBottom=$decBottom-$lo[$x];
				$decBottom=4294967295+$decBottom;
				$decBottom++;
				$decTop--;
			}
			else
			{
				$decBottom=$decBottom-$lo[$x];
			}
		}
	}

	$x=9;
	$div[9]=0;
	
	while ($decTop>0)
	{
		if ($decBottom<$lo[$x])
		{				
			$decBottom=$decBottom-$lo[$x];
			$decBottom=4294967295+$decBottom;
			$decBottom++;
			$decTop--;
			$div[$x]++;
		}
		else 
		{
			$decBottom=$decBottom-$lo[$x];
			$div[$x]++;
		}
	}

	
	for ($x=9;$x>=0;$x--)
	{

		if ($x<9)
			$div[$x]=0;
		while ($decBottom>=$lo[$x])
		{
			$decTop=$decTop-$hi[$x];
			if ($decBottom<$lo[$x] && $decTop>0)
			{				
				$decBottom=$decBottom-$lo[$x];
				$decBottom=4294967295+$decBottom;
				$decBottom++;
				$decTop--;
				$div[$x]++;
			}
			else 
			{
				$decBottom=$decBottom-$lo[$x];
				$div[$x]++;
			}
		}
	}
		$output="";
		$x=19;
		while ($div[$x]=="0")
		{
			$div[$x]="";
			$x--;
		}
		if ($sign=="neg")
			$output="-";
		$output=$output.$div[19].$div[18].$div[17].$div[16].$div[15].$div[14].$div[13].$div[12].
				$div[11].$div[10].$div[9].$div[8].$div[7].$div[6].$div[5].$div[4].
				$div[3].$div[2].$div[1].$div[0];
				
?>
