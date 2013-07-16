<?php

abstract class BlockManagement_Abstract
{
	public function adaptSQL($input)
	{
		$input = str_replace("'","''",$input);
		return $input;
	}
}

?>