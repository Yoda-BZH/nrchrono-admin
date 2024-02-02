<?php

namespace App\Service;


class TimingSeparator
{

    private $original = null;
    private $new = null;
    private $datas = null;

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Defini
     *
     * @param mixed $foo foo
     *
     * @return $this
     */
    public function setOriginal($original)
    {
        $this->original = $original;

        return $this;
    }


    /**
     * Defini
     *
     * @param mixed $foo foo
     *
     * @return $this
     */
    public function setNew($new)
    {
        $this->new = $new;

        return $this;
    }

    /**
     * Defini
     *
     * @param mixed $foo foo
     *
     * @return $this
     */
    public function setDatas($datas)
    {
        $this->datas = $datas;

        return $this;
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function compute()
    {
        $interval = new \DateInterval(
            sprintf('PT%s', $this->datas->getTiming()->format('H\Hi\Ms\S'))
        );

        $n = clone $this->original->getTiming();

        $this->new->setTiming($n->sub($interval));
        $this->new->setRacer($this->datas->getRacer());
        $this->new->getCreatedAt()->add($interval);
        $this->new->setClock($this->new->getCreatedAt());


        // laisser après modification interval
        $this->original->setTiming($this->datas->getTiming());
    }

}
