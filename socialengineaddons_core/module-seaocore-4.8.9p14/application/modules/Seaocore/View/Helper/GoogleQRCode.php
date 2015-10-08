<?php

class Seaocore_View_Helper_GoogleQRCode extends Zend_View_Helper_Abstract
{
    public function googleQRCode($data, $width = 100, $height = 100)
    {
    	$url = 'https://chart.googleapis.com/chart?';
    	$params = array(
    	               'cht'=>'qr',
    	               'chs'=>(int)$width . 'x' . (int)$height,
    	               'chl'=>$data
    	               );
    	$url .= http_build_query($params);
    	return $url;
    }
}
