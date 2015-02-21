<?php

namespace AppBundle\Timer\Provider;


interface Provider {

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function getGeneral();


    /**
     * description
     *
     * @param void
     * @return void
     */
    public function getTeam($id);


}
