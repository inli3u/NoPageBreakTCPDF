<?php

class NoPageBreakTCPDF extends TCPDF
{
	// Used by the noPageBreak() method.
	private $noPageBreakTries = 0;
	private $noPageBreakNumPages = 0;

	/*
	*	Use this method in a while loop to keep the page from breaking
	*	while outputting the contents of the loop.
	*	
	*	Example:
	*		while ($this->NoPageBreak()) {
	*			$this->Cell(...);
	*			$this->Cell(...);
	*		}
	*/
	protected function NoPageBreak()
	{
		if ($this->noPageBreakTries == 0)
		{
			$this->noPageBreakNumPages = $this->getNumPages();
			$this->startTransaction();
			//error_log('Starting transaction (pages: ' . $this->noPageBreakNumPages . ')');
		}
		elseif ($this->noPageBreakTries < 3)
		{
			if ($this->getNumPages() != $this->noPageBreakNumPages)
			{
			 	//error_log('Rolling back transaction and adding a page (' . $this->getNumPages() . ' != ' . $this->noPageBreakNumPages . ')');
			 	$this->rollbackTransaction(true);
			 	$this->AddPage();
		 		
				$this->noPageBreakNumPages = $this->getNumPages();
				$this->startTransaction();
				//error_log('Starting transaction (pages: ' . $this->noPageBreakNumPages . ')');
			}
			else
			{
		 		//error_log('Committing transaction');
		 		$this->commitTransaction();
		 		$this->noPageBreakTries = 0;
		 		return false;
			}
		}
		else
		{
			// Content is too long for one page, let it break.
			//error_log('NoPageBreakTCPDF::NoPageBreak FAILED: Could not stop page break :(');
			$this->commitTransaction();
			$this->noPageBreakTries = 0;
			return false;
		}
			 
		$this->noPageBreakTries++;
		return true;
	}
}
