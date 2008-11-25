<?php
class LabelBox extends mapWareCore
{
	var $p1;
	var $p2;
	var $p3;
	var $p4;
	var $puntos;
	
	function LabelBox($p1, $p2, $p3, $p4){
		$this->p1 = $p1;
		$this->p2 = $p2;
		$this->p3 = $p3;
		$this->p4 = $p4;
		$this->puntos = array($p1, $p2, $p3, $p4);
	}
	//funcion usada para los poligono sque dibujan un punto en su centro 
	function addPaddingToLabelBox($factor = 0.5){
		$padding = $factor * abs($this->p2[1] - $this->p3[1]);
		$this->p1 = array($this->p1[0] - $padding, $this->p1[1] - $padding);
		$this->p2 = array($this->p2[0] + $padding, $this->p2[1] - $padding);
		$this->p3 = array($this->p3[0] + $padding, $this->p3[1] + $padding);
		$this->p4 = array($this->p4[0] - $padding, $this->p4[1] + $padding);
		$this->puntos = array($this->p1, $this->p2, $this->p3, $this->p4);
	}
	function addPaddingDownToLabelBox(){
		$padding = 0.5 * abs($this->p2[1] - $this->p3[1]);
		$this->p1 = array($this->p1[0], $this->p1[1]);
		$this->p2 = array($this->p2[0], $this->p2[1]);
		$this->p3 = array($this->p3[0], $this->p3[1] + $padding);
		$this->p4 = array($this->p4[0], $this->p4[1] + $padding);
		$this->puntos = array($this->p1, $this->p2, $this->p3, $this->p4);
	}
	function intersects($labelBox){
		$intersects = false;
		for($i=0; $i<count($labelBox->puntos); $i++){
			$p = $labelBox->puntos[$i];
			//si el punto esta dentro
			if($this->InsidePolygon($this->puntos, $p)){
				$intersects = true;
			}
		}
		//al reves
		for($i=0; $i<count($this->puntos); $i++){
			$p = $this->puntos[$i];
			//si el punto esta dentro
			if($this->InsidePolygon($labelBox->puntos, $p)){
				$intersects = true;
			}
		}
		//si ninguno tiene ningun punto dentro del otro solo queda que las lineas se intersecten
		for($i=0; $i<count($labelBox->puntos); $i++){
			$line1Extremo1 = $labelBox->puntos[$i];
			$line1Extremo2 = $labelBox->puntos[($i+1) % count($labelBox->puntos)];
			//ahora para cada linea de esta caja
			for($j=0; $j<count($this->puntos); $j++){
				$line2Extremo1 = $this->puntos[$j];
				$line2Extremo2 = $this->puntos[($j+1) % count($labelBox->puntos)];
				//ver si las lineas se intersectan dentro de los segmentos
				if($this->segmentsIntersects($line1Extremo1, $line1Extremo2, $line2Extremo1, $line2Extremo2)){
					$intersects = true;
				}
			}
		}
		return $intersects;
	}
}
?>