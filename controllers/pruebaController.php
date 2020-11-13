<?php  
Class PruebaController Extends ApplicationGeneral{

	function lpr($STR,$PRN) {
	  $prn=(isset($PRN) && strlen($PRN))?"$PRN":C_DEFAULTPRN ;
	  $CMDLINE="lpr -P $prn ";
	  $pipe=popen("$CMDLINE" , 'w' );
	  if (!$pipe) {print "pipe failed."; return ""; }
	  fputs($pipe,$STR);
	  pclose($pipe);
	} // lpr()
}

?>