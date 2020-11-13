<?php

class usuariowindows extends Applicationbase {

    function buscaxip($ip) {
        $data = $this->leeRegistro("wc_usuariowindows", "*", "estado=1 and ip='$ip'", "");
        return $data;
    }

}

?>