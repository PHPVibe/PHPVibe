<?php class pagination{
	
	public $per_page = 10;
	
	private $values;
	
	private $current;
	
	/* config the view logic here */
	
	public $pages_items = 7; //recommended to be a impare number
	
	private $show_last = false; 
	
	private $show_first = false;
	
	/*
	@ setter current page 
	*/
	function set_current($value){
		$this->current = preg_replace("/[^0-9]/","",$value);
	}
	
	/*
	@ setter of show last page button
	*/
	function set_last_page($val){
		$this->show_last=$val;
	}
	function set_first_page($val){
		$this->show_first=$val;
	}			
	
	
	/*
	@ setter of $per_page
	*/
	public function set_per_page($value){
		$this->per_page = preg_replace("/[^0-9]/","",$value);
	}	
	
	/*
	@ how many values needed to be paginated
	*/
	public function set_values($values){
		return $this->values=preg_replace("/[^0-9]/","",$values);
	}	
	
	/*
	@ setter of $pages_items 
	displays how many links to appear on the page
	ex: $pages_items = 5; 
	output: 1 2 3 4 5
	*/
	public function set_pages_items($value){
		$this->pages_items = preg_replace("/[^0-9]/","",$value);
	}	
	
	/*
	@ returns the number of maxim page
	core of class
	*/
	public function page_numbers(){
		$pages = ceil($this->values/$this->per_page);
		return $pages;
	}
	/*
	@ engine of class
	returns an array with needed pages
	*/
	private function pages_array(){
		$pages = array();
		$mpag = ceil($this->pages_items/2);
		$aux = $this->current+$mpag;
		//var_dump($this->pages_items);
		//var_dump($this->page_numbers());
		if($this->pages_items > $this->page_numbers()){
			$this->pages_items = $this->page_numbers();
		}	
		switch($aux){
					
			case ($aux<=$this->page_numbers()) && $mpag<=$this->current :
				for($i=$this->current;$i<$this->current+$mpag;$i++){
					$pages[] = $i;
				}
				for($i=$this->current;$i>$this->current-$mpag;$i--){
					$pages[] = $i;
				}
				sort($pages);
				$pages = array_unique($pages);
				break;
			
			case ($this->current < $mpag):
				for($i=1;$i<=$this->pages_items;$i++){
					$pages[] = $i;
				}	
				break;
			
			case ($this->current+$mpag > $this->page_numbers()):
				for($i=$this->page_numbers()-$this->pages_items+1;$i<=$this->page_numbers();$i++){
					$pages[] = $i;
				}
				for($i=$this->current-$mpag+1;$i<$this->current;$i++){
					$pages[] = $i;
				}
				sort($pages);
				$pages = array_unique($pages);		
				break;			
		}
		return $pages;
	}
	
	/*
	Used to return only array with needed pages
	You can create your own view logic based on this array
	*/
	public function return_only_pages(){
		return $this->pages_array();
	}	
	
	/*
	@ view logic - HTML 
	*/
	public function show_pages($pagi_url){
	if ($this->page_numbers() > 1) {
		$output = '<div class="paginator">	<ul class="pagination pagination-no-border pagination-lg">
';
		$prev = $this->current-1;
		
		if($this->show_first==true){
			$output .= '<li> <a href="'.$pagi_url.'1" ><i class="material-icons">&#xe5dc;</i></a></li>';
		}
		if($this->current>2){
			$output .= '<li><a href="'.$pagi_url.$prev.'" title="'._lang("Previous page").'"><i class="material-icons">&#xe408;</i></a></li>';
		}
		$test = $this->pages_array();
		foreach($test as $t){
			if($this->current==$t){
				$output .= '<li class="active"><a href="'.$pagi_url.$t.'">'.$t.'</a></li>';
			} else {
				$output .= '<li><a href="'.$pagi_url.$t.'">'.$t.'</a></li>';
			}		
		}	
		$next = $this->current+1;
		if($this->current<$this->page_numbers()){
			$output .= '<li><a class="next" href="'.$pagi_url.$next.'" title="'._lang("Next page").'"><i class="material-icons">&#xe409;</i></a></li>';
		}
		
			$output .= '<li><a href="'.$pagi_url.$this->page_numbers().'" title="'._lang("Last page").'"><i class="material-icons">&#xe5dd;</i></a></li>';
		
		//$output .= '<li> Page '.$this->current.' from '.$this->page_numbers().'</li>';
		$output .= '</ul></div>';
		echo $output;
	}
	}
}
