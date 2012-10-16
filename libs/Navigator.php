<?php

class Navigator
{
	
	private $pageCount;
	private $peerPage;
	private $actualPage;
	private $totalPages;
	private $url;
	private $html;
	private $cssCurrent = "current";
	private $queryStr;
	private $labelNext = "ďalšie &raquo;";
	private $labelPrev = "&laquo; predošlé";
	private $separator = "/";
	// -------------------------------------------------------------------------

	public function __construct($pageCount, $actualPage, $url, $peerPage, $queryStr = ""){
		
		$this->pageCount	= $pageCount;
		$this->actualPage	= $actualPage;
		$this->peerPage 	= $peerPage;
		$this->url 		= $url;
		$this->queryStr		= $queryStr;
		$this->setTotalPages();
		
	}
	
	// -------------------------------------------------------------------------
	
	public function simpleNumNavigator(){
		$this->html = '<div class="navigator">';
		
		for($i=1;$i < $this->totalPages+1; $i++){
			$this->getPageLink($i);
		}
	$this->html .= '</div>';
	return $this->html;
	}
	
	
	// -------------------------------------------------------------------------
	
	public function smartNavigator($neighbor = 4){
		if($this->pageCount <= $this->peerPage) return;
		$this->html = '<div class="navigator">';
		
		if($this->actualPage == 1){
			$this->html .= "<span>".$this->labelPrev."</span>";
		}else{
			$this->html .= '<a href="'.$this->url.($this->actualPage == 2 ?  "" : $this->separator.($this->actualPage - 1)).$this->queryStr.'">'.$this->labelPrev.'</a>';
		}
		
		$this->getPageLink(1);
		
		if($this->actualPage - $neighbor -1 > 0){
			$i = $this->actualPage - $neighbor;
			$this->html .= " ... ";
		}else{
			$i = 2;
		}

		for($i; $i < $this->actualPage ; $i++)
				$this->getPageLink($i);
		
		if($this->actualPage != 1) 
			$this->getPageLink($this->actualPage); 
			
		
		if($this->actualPage + $neighbor + 1 < $this->totalPages){
			$to = $this->actualPage + $neighbor +1;
		}else{
			$to = $this->totalPages;
		}
		
		for($i = $this->actualPage +1; $i < $to ; $i++)	
				$this->getPageLink($i);		
		
		
		if($this->actualPage + $neighbor + 1 < $this->totalPages)
			$this->html .= " ... ";
				
		if($this->totalPages != $this->actualPage) { $this->getPageLink($this->totalPages); }
		
		if($this->actualPage == $this->totalPages)
                    $this->html .= "<span>".$this->labelNext."</span>";
		else
                    $this->html .= '<a href="'.$this->url.$this->separator.($this->actualPage + 1).$this->queryStr.'">'.$this->labelNext.'</a>';
		
                $this->html .= '</div>';
	return $this->html;
	}
	
	
	// -------------------------------------------------------------------------

	private function setTotalPages(){
    	$this->totalPages = ceil($this->pageCount/$this->peerPage) ;
    }
	
	// -------------------------------------------------------------------------
	
	public function getNavigator(){
		return $this->html;
	}
	
	// -------------------------------------------------------------------------
	
	private function getPageLink($i){
			if($this->actualPage == $i){
				$this->html .= '<span>'.$i.'</span>';
			}else{
				$this->html .= '<a href="'.$this->url.($i == 1 ? "" : $this->separator.$i).$this->queryStr.'">'.$i.'</a>';
			}
	}
	
	// -------------------------------------------------------------------------
	
	public function setLabelNext($label){
		$this->labelNext = $label;
	}
	
	// -------------------------------------------------------------------------
	
	public function setLabelPrev($label){
		$this->labelPrev = $label;
	}
	
	// -------------------------------------------------------------------------
	
	public function setSeparator($sep){
		$this->separator = $sep;
	}
}
?>
