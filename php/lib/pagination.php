<?php
class Pagination {

	var $output;
	var $nbtotal;
	var $_getName;
	var $nbmaxparpage;
	var $nbdepages;
	var $minid;

	function Pagination($pageencours=1,$nbtotal,$nbmaxparpage=10,$getName='page') {
		$this->nbtotal = intval($nbtotal);
		$this->nbmaxparpage = intval($nbmaxparpage);
		$this->nbdepages = ceil($this->nbtotal / $this->nbmaxparpage);
		$this->_getName = $getName;
		Pagination::Generate($pageencours);
	}
	function Generate($pageencours) {
		unset($this->output);
		$this->minid = ( $pageencours - 1 ) * $this->nbmaxparpage;
		if ( $this->nbdepages > 1 ) {
			for ( $i=1; $i <= $this->nbdepages; $i++ )
			{
				if ( $i == $pageencours )
					$this->output[] = array('link' => 0, 'page' => $i);
				else
					$this->output[] = array('link' => 1, 'page' => $i);
			}
		}
		else
			$this->output = NULL;
	}
	function getPagination($idDiv){
	  if ( isset($this->output) && is_array($this->output) )
	  {
	  	$var = '<div class="tablenav"><div class="tablenav-pages" style="float:left;">';
		$var .= '<b> '.__('Page','roomplanning').'s : </b>';
	  	foreach ( $this->output as $key) {
	  		if ( $key['link'] == 1 )
				$var .= '<a class="page-numbers" href="#" onclick="protoAdminList.paginationTable('.$key['page'].',\''.$idDiv.'\'); return false;">'.$key['page'].'</a> ';
			else
				$var .= '<span class="current" attr="'.$key['page'].'">'.$key['page'].'</span> ';
		}
		$var .= '</div><br class="clear"></div>';
	  }
	  return $var;
	}
}
?>