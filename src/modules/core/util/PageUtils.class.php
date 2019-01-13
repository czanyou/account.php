<?php
!defined('IN_VISION') && exit('Access Denied');

class PageUtils extends Object {
	private $page = 1;
	private $total = 0;

    public function __construct($total, $page, $parameter='') {
		$this->page  = $page;
		$this->total = $total;
	}
	
	function getPages($pageSize = 20) {
		$total = $this->total;
		$page  = $this->page;

		if ($total < 0) {
			$total = 0;
		}
 
		$pageCount = (int)(($total - 1) / $pageSize + 1);
		if ($pageCount <= 1) {
			return array();
		}

		$startPage = max(1, $page - 4);
		$endPage = min($pageCount, $startPage + 8);
		$pages = array();

		if ($startPage > 1) {
			$pages[] = array('title'=>1, 'class'=>'');
		}

		for ($i = $startPage; $i <= $endPage; $i++) {
			$pages[] = array('title'=>$i, 'class'=>($page == $i ? "current" : ""));
		}

		if ($endPage < $pageCount) {
			$pages[] = array('title'=>$pageCount, 'class'=>'');
		}

		return $pages;
	}

	function show($url, $pageSize = 20) {
		$pages = $this->getPages($pageSize);
		foreach ($pages AS $page) {
			//print_r($page);
			echo '<a href="',$url,'page=',$page['title'],'" class="',$page['class'],'">',$page['title'],'</a>';
		}
		
	}

	function show2($url, $pageSize = 20) {
		$pages = $this->getPages($pageSize);
		foreach ($pages AS $page) {
			//print_r($page);
			echo '<a href="javascript:onPage(',$page['title'],');" class="',$page['class'],'">',$page['title'],'</a>';
		}
		
	}	
}


?>