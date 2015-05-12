<?php

namespace AppBundle\Timer\Provider;

use AppBundle\Timer\Tour;
use AppBundle\Timer\Team;


class MatsportFile extends Matsport {

    public function __construct()
    {
        $this->setGeneralUrl(__DIR__.'/../../../../app/matsport.html');
        $this->setEquipeUrl(__DIR__.'/../../../../app/matsport.html');

        parent::__construct();
    }
}
