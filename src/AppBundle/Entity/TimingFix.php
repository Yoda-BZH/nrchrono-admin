<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

class TimingFix extends Timing {
    
    private $newtiming = null;
    
    /**
     * Return the value of 
     * 
     * 
     */
    public function getNewtiming()
    {
        return $this->newtiming;
    }
    
    /**
     * Defini 
     * 
     * @param mixed $foo foo
     *
     * @return $this
     */
    public function setNewtiming($newtiming)
    {
        $this->newtiming = $newtiming;
    
        return $this;
    }
    
}
