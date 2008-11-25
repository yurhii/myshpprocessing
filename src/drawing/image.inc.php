<?PHP
class image {

	var $image;
	var $status = true;
	var $width;
	var $height;
	var $bkgColour;
	var $escala;
	var $xmin;
	var $ymin;
	var $i;
	var $j;
	

	var $colours = array();
	var $fonts = array();

	var $error = array();

	function image($width, $height, $bkgColour, $transparent = false) {
		$this->bkgColour = (isset($bkgColour))? $bkgColour : 'ffffff00';
		$this->setWidth($width);
		$this->setHeight($height);
		$this->createImage($transparent);
	}

	function drawImage() {
		$image = &$this->image;
		if($this->getStatus()) {
			header('Content-Type: image/png');
			imagepng($image);
		}
	}

	function saveImage($filename) {
		$image = &$this->image;
		if($this->getStatus()) {
			imagepng($image, $filename);
		}
		imagedestroy($this->image);
	}

	function createImage($transparent) {
		//creates the base image canvas and fills the image with white with no transparency
		$image = &$this->image;
		$width = $this->getWidth();
		$height = $this->getHeight();
		//check for invalud height
		if($height <= 0) {
			$this->setError('createImage: image height must be greater than 0');
		}
		//check for invalid width
		if($width <= 0) {
			$this->setError('createImage: image width must be greater than 0');
		}
		if($this->getStatus()) {
			$this->image = imagecreatetruecolor($width, $height);
			if(!$transparent){
				$colour = $this->setColour($this->bkgColour);
			}else{
				//imagen con transparencia en el fondo
				//imagealphablending($this->image, false);
				imagesavealpha($this->image, true);
				//color transparente
				$colour = imagecolorallocatealpha($this->image, 0, 0, 0, 127);
			}
			imagefill($this->image, 0, 0, $colour);
		}
	}
	function getWidth() {
		return $this->width;
	}

	function setWidth($width) {
		$this->width = $width;
	}

	function getHeight() {
		return $this->height;
	}

	function setHeight($height) {
		$this->height = $height;
	}

	function getColour($hex) {
		$colour = &$this->colours;
		if(isset($colour[strtolower($hex)])) {
			return $colour[strtolower($hex)];
		}
		return false;
	}
	function createFromUrl($url){
		$this->image = imagecreatefrompng($url);
	}
	function copyFrom($from, $dx, $dy, $sx, $sy, $sw, $sh){
		imagecopy($this->image, $from->image, $dx, $dy, $sx, $sy, $sw, $sh);
	}
	function setColour($string) {
		$hex = strtolower($string);
		$colour = &$this->colours;
		$res_colour = $this->getColour($hex);
		if($res_colour == false) {
			$image = &$this->image;
			$rgb = array('r' => hexdec(substr($hex, 0, 2)), 'g' => hexdec(substr($hex, 2, 2)), 'b' => hexdec(substr($hex, 4, 2)), 'a' => hexdec(substr($hex, 6, 2)));
			$colour[$hex] = imagecolorallocatealpha($image, $rgb['r'], $rgb['g'], $rgb['b'], $rgb['a']);
			$res_colour = $colour[$hex];
		}
		return $res_colour;
	}

	function drawPixel($x, $y, $colour) {
		$image = &$this->image;
		$width = $this->getWidth();
		$height = $this->getHeight();
		if(($x < 0) and ($x > $width)) {
			$this->setError('drawPixel: the value '.$x.' for x must be between 0 and '.$width);
		}
		if(($y < 0) and ($y > $width)) {
			$this->setError('drawPixel: the value '.$y.' for y must be between 0 and '.$height);
		}
		$res_colour = $this->setColour($colour);
		if($this->getStatus()) {
			imagesetpixel($image, $x, $y, $res_colour);
		}
	}
	function drawFilledPolygon($puntos, $borderColor, $fillColor){
		$real_colour = $this->setColour($fillColor);
		imagefilledpolygon($this->image, $puntos, count($puntos)/2, $real_colour);
		if($borderColor != false){
			$real_colour = $this->setColour($borderColor);
			imagepolygon($this->image, $puntos, count($puntos)/2, $real_colour);
		}
	}	
	function drawLine($x1, $y1, $x2, $y2, $thickness, $colour) {
		$image = &$this->image;
		 $angle=(atan2(($y1 - $y2),($x2 - $x1))); 
		$dist_x = $thickness*(sin($angle));
		$dist_y = $thickness*(cos($angle));
		
		$p1x=$x1 + $dist_x;
		$p1y=$y1 + $dist_y;
		$p2x=$x2 + $dist_x;
		$p2y=$y2 + $dist_y;
		$p3x=$x2 - $dist_x;
		$p3y=$y2 - $dist_y;
		$p4x=$x1 - $dist_x;
		$p4y=$y1 - $dist_y;
		
		$res_colour = $this->setColour($colour);
		$array = array(0=>$p1x,$p1y,$p2x,$p2y,$p3x,$p3y,$p4x,$p4y);
		imagesetthickness($image, 1);
		imagefilledpolygon ( $image, $array, (count($array)/2), $res_colour );
	}

	function drawRectangle($x1, $y1, $x2, $y2, $thickness, $colour) {
		$image = &$this->image;
		$width = $this->getWidth();
		$height = $this->getHeight();
		if(($x1 < 0) and ($x1 > $width)) {
			$this->setError('drawLine: the value '.$x1.' for x1 must be between 0 and '.$width);
		}
		if(($x2 < 0) and ($x2 > $width)) {
			$this->setError('drawLine: the value '.$x2.' for x2 must be between 0 and '.$width);
		}
		if(($y1 < 0) and ($y1 > $height)) {
			$this->setError('drawLine: the value '.$y1.' for y1 must be between 0 and '.$height);
		}
		if(($y2 < 0) and ($y2 > $height)) {
			$this->setError('drawLine: the value '.$y2.' for y2 must be between 0 and '.$height);
		}
		$res_colour = $this->setColour($colour);
		if($this->getStatus()) {
			imagesetthickness($image, $thickness);
			$ct = ceil($thickness / 2);
			$ft = floor($thickness / 2);
			imageline($image, ($x1 - $ft), $y1, ($x2 - $ct), $y1, $res_colour);
			imageline($image, $x1, ($y1 + $ct), $x1, ($y2 + $ft), $res_colour);
			if($ct == $ft) {
				imageline($image, ($x1 + $ct), ($y2 + 1), ($x2 + $ft), ($y2 + 1), $res_colour);
				imageline($image, ($x2 + 1), ($y1 - $ft), ($x2 + 1), ($y2 - $ct), $res_colour);
			} else {
				imageline($image, ($x1 + $ct), $y2, ($x2 + $ft), $y2, $res_colour);
				imageline($image, $x2, ($y1 - $ft), $x2, ($y2 - $ct), $res_colour);
			}
		}
	}

	function drawFilledRectangle($x1, $y1, $x2, $y2, $colour) {
		$image = &$this->image;
		$width = $this->getWidth();
		$height = $this->getHeight();
		if(($x1 < 0) and ($x1 > $width)) {
			$this->setError('drawFilledRectangle: the value '.$x1.' for x1 must be between 0 and '.$width);
		}
		if(($x2 < 0) and ($x2 > $width)) {
			$this->setError('drawFilledRectangle: x2 must be less then the image width');
		}
		if(($y1 < 0) and ($y1 > $width)) {
			$this->setError('drawFilledRectangle: y1 must be less then the image height');
		}
		if(($y2 < 0) and ($y2 > $width)) {
			$this->setError('drawFilledRectangle: y2 must be less then the image height');
		}
		if($x1 > $x2) {
			$this->setError('drawFilledRectangle: the value '.$x1.' for x1 must be less than the value '.$x2.' for x2');
		}
		if($y1 > $y2) {
			$this->setError('drawFilledRectangle: the value '.$y1.' for y1 must be less than the value '.$y2.' for y2');
		}
		$res_colour = $this->setColour($colour);
		if($this->getStatus()) {
			imagefilledrectangle($image, $x1, $y1, $x2, $y2, $res_colour);
		}
	}

	function drawFilledBorderedRectangle($x1, $y1, $x2, $y2, $thickness, $linecolour, $fillcolour) {
		$image = &$this->image;
		$width = $this->getWidth();
		$height = $this->getHeight();
		if(($x1 < 0) and ($x1 > $width)) {
			$this->setError('drawFilledBorderedRectangle: the value '.$x1.' for x1 must be between 0 and '.$width);
		}
		if(($x2 < 0) and ($x2 > $width)) {
			$this->setError('drawFilledBorderedRectangle: the value '.$x2.' for x2 must be between 0 and '.$width);
		}
		if(($y1 < 0) and ($y1 > $width)) {
			$this->setError('drawFilledBorderedRectangle: the value '.$y1.' for y1 must be between 0 and '.$height);
		}
		if(($y2 < 0) and ($y2 > $width)) {
			$this->setError('drawFilledBorderedRectangle: the value '.$y2.' for y2 must be between 0 and '.$height);
		}
		if($x1 > $x2) {
			$this->setError('drawFilledBorderedRectangle: the value '.$x1.' for x1 must be less than the value '.$x2.' for x2');
		}
		if($y1 > $y2) {
			$this->setError('drawFilledBorderedRectangle: the value '.$y1.' for y1 must be less than the value '.$y2.' for y2');
		}
		if($this->getStatus()) {
			$ct = ceil($thickness / 2);
			$ft = floor($thickness / 2);
			$modifier = 0;
			if($thickness > 0) {
				$modifier = 1;
				if($thickness == 2) {
					$this->drawRectangle(($x1 + $ft), ($y1 + $ft), ($x2 - $ct) - 1, ($y2 - $ct) - 1, $thickness, $linecolour);
				} else {
					$this->drawRectangle(($x1 + $ft), ($y1 + $ft), ($x2 - $ct), ($y2 - $ct), $thickness, $linecolour);
				}
			}
			if(($ct == $ft) and ($ct >= 2) and ($ft >= 2)) {
				$modifier--;
				$this->drawFilledRectangle(($x1 + $thickness), ($y1 + $thickness), ($x2 - $thickness) - $modifier, ($y2 - $thickness) - $modifier, $fillcolour);
			} else {
				$this->drawFilledRectangle(($x1 + $thickness), ($y1 + $thickness), ($x2 - $thickness) - $modifier, ($y2 - $thickness) - $modifier, $fillcolour);
			}
		}
	}

	function getStatus() {
		return $this->status;
	}

	function setStatus($boolean) {
		$this->status = $boolean;
	}

	function setError($message) {
		$this->error[] = $message;
		$this->setStatus(false);
	}
	function drawCircle($x, $y, $r, $colour){
		$image = &$this->image;
		$res_colour = $this->setColour($colour);
		if($this->getStatus()) {
			imagesetthickness($image, 2);
			imageellipse($image, $x, $y, $r*2, $r*2, $res_colour);
		}
	}
	function drawFilledCircle($x, $y, $r, $colour){
		$image = &$this->image;
		$res_colour = $this->setColour($colour);
		if($this->getStatus()) {
			imagesetthickness($image, 4);
			imagefilledellipse($image, $x, $y, $r*2, $r*2, $res_colour);
		}
	}
	function drawPolygonLine($x1, $y1, $x2, $y2, $color, $thick){
		$this->drawLine($x1, $y1, $x2, $y2, $thick, $color);
		$this->drawFilledCircle($x1, $y1, $thick, $color);
		$this->drawFilledCircle($x2, $y2, $thick, $color);
	}
	function labelPath($xx1, $yy1, $xx2, $yy2, $text, $size, $font, $nivel){
		//sabemos de izquierda a derecha y de arriba a abajo como estan los puntos
		if(min($xx1, $xx2) == $xx1){
			$x1 = $xx1;
			$y1 = $yy1;
			$x2 = $xx2;
			$y2 = $yy2;
		}else{
			$x1 = $xx2;
			$y1 = $yy2;
			$x2 = $xx1;
			$y2 = $yy1;
		}
		$image = &$this->image;
		//definimos el angulo
		$cociente = (($x2-$x1) == 0) ? .00000001: ($x2-$x1);
		$tan = ($y2-$y1)/$cociente;
		$angRadianes = abs(atan($tan));
		//vemos en que cuadrante se encuentra nuestro path a etiquetar
		if($x1 <= $x2 && $y1 <= $y2){
			$cuadrante = 1;
			$angRadianes = -1*$angRadianes;
		}
		if($x1 <= $x2 && $y1 > $y2){
			$cuadrante = 4;
		}
		//pasar angulo a grados
		$angGrados = $angRadianes*180/pi();
		//ajustamos la caja generada por imagettfbbox
		if($text == "Ret 38"){
			echo "cuadrante = ".$cuadrante."<br />";
		}
		$box = $this->checkBox(imagettfbbox($size, $angGrados, $font, $text), $angGrados, $cuadrante, $size);
		//distancia maxima en x,y de la caja
		$xStringLength = $this->distance($box[0], 0, $box[2], 0);
		$yStringLength = $this->distance(0, $box[1], 0, $box[3]);
		//como $x1 < $x2
		//esto centra sobre el path a la caja, sin importar quien es mas grande
		//la $x y $y representan el punto desde el cual la caja debe empezar a dibujarse
		$x = $x1 + .5 * ($this->distance($x1, 0, $x2, 0) - $xStringLength) + .5 * sin($angRadianes)*distancia($box[0], $box[1], $box[6], $box[7]);
		if(min($y1, $y2) == $y1){
			$y = $y1 + .5 * ($this->distance(0, $y1, 0, $y2) - $yStringLength) + .5 * cos($angRadianes)*distancia($box[0], $box[1], $box[6], $box[7]);
		}else{
			$y = $y1 - .5 * ($this->distance(0, $y1, 0, $y2) - $yStringLength) + .5 * cos($angRadianes)*distancia($box[0], $box[1], $box[6], $box[7]);
		}
		//$this->drawLabelBox($box, $x, $y);
		$box[0] += $x;
		$box[1] += $y;
		$box[2] += $x;
		$box[3] += $y;
		$box[4] += $x;
		$box[5] += $y;
		$box[6] += $x;
		$box[7] += $y;
		$box[8] = $x;
		$box[9] = $y;
		$box[10] = $angGrados;
		$box[11] = $text;
		$box[12] = $size;
		return $box;
	}
	function checkBox($box, $angGrados, $cuadrante, $size){
			$xchange = $box[0];
			$ychange = $box[1];
			for($k=0; $k<8; $k++){
				$box[$k] -= ($k % 2 == 0) ? $xchange : $ychange;
			}
			return $box;
		}
		function drawLabelBox($box, $x, $y){
			$thickness = 1;
			$colour = 'ff000000';
			$this->drawLine($x+$box[0], $y+$box[1], $x+$box[2], $y+$box[3], $thickness, $colour);
			$this->drawLine($x+$box[2], $y+$box[3], $x+$box[4], $y+$box[5], $thickness, $colour);
			$this->drawLine($x+$box[4], $y+$box[5], $x+$box[6], $y+$box[7], $thickness, $colour);
			$this->drawLine($x+$box[6], $y+$box[7], $x+$box[0], $y+$box[1], $thickness, $colour);
		}
		function distance($x1, $y1, $x2, $y2){
			$d = sqrt(pow($x1- $x2, 2) + pow($y1 - $y2, 2));
			return $d;
		}
		function setVars($escala, $xmin, $ymin){
			$this->escala = $escala;
			$this->xmin = $xmin;
			$this->ymin = $ymin;
		}

















	function drawText($x, $y, $angle, $font, $fontsize, $colour, $text, $hibrido = false) {
		$image = &$this->image;
		$colour = $this->setColour($colour);
		//glow
		if(!$hibrido){
			$glow = $this->setColour('FFFFFF');
		}else{
			$glow = imagecolorallocatealpha($image, 255, 255, 255, 10);
		}
		imagettftext($image, $fontsize, $angle, $x+3, $y, $glow, $font, $text);
		imagettftext($image, $fontsize, $angle, $x-3, $y, $glow, $font, $text);
		imagettftext($image, $fontsize, $angle, $x, $y+3, $glow, $font, $text);
		imagettftext($image, $fontsize, $angle, $x, $y-3, $glow, $font, $text);
		//texto
		imagettftext($image, $fontsize, $angle, $x, $y, $colour, $font, $text);
	}

	function addFont($font) {
		$index = $this->arraySearch($font, $this->fonts);
		if($index == -1) {
			$this->fonts[] = $font;
			$index = (count($this->fonts) - 1);
		}
		return $index;
	}

	function arraySearch($needle, &$haystack) {
		for($i = 0; $i <= count($haystack) - 1; $i++) {
			if($haystack[$i] == $needle) {
				return $i;
			}
		}
		return -1;
	}

	function isHex($string) {
		$hex = '0123456789abcdef';
		if(strlen($string) == 0) {
			return false;
		}
		$temp = $string;
		for($i = 0; $i <= strlen($hex) - 1; $i++) {
			$temp = str_replace($hex{$i}, '', $temp);
		}
		if(strlen($temp) == 0) {
			return true;
		}
		return false;
	}
}
?>