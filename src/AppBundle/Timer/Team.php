<?php


namespace AppBundle\Timer;

class Team {

     //'numero'   => $matches[3][$k],

    private $position  = 0;
    private $numero    = 0;
    private $annee     = 0;
    private $nom       = '';
    private $temps     = 0;
    private $tour      = 0;
    private $ecart     = 0;
    private $distance  = 0;
    private $vitesse   = 0;
    private $bestlap   = 0;
    private $poscat    = 0;
    private $categorie = '';

    /**
     * Set the value of
     *
     *
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setAnnee($annee)
    {
        $this->annee = $annee;

        return $this;
    }

    public function getAnnee()
    {
        return $this->annee;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setTemps($temps)
    {
        $this->temps = $temps;

        return $this;
    }

    public function getTemps()
    {
        return $this->temps;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setTour($tour)
    {
        $this->tour = $tour;

        return $this;
    }

    public function getTour()
    {
        return $this->tour;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setEcart($ecart)
    {
        $this->ecart = $ecart;

        return $this;
    }

    public function getEcart()
    {
        return $this->ecart;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setVitesse($vitesse)
    {
        $this->vitesse = $vitesse;

        return $this;
    }

    public function getVitesse()
    {
        return $this->vitesse;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setBestLap($bestlap)
    {
        $this->bestlap = $bestlap;

        return $this;
    }

    public function getBestLap()
    {
        return $this->bestlap;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setPoscat($poscat)
    {
        $this->poscat = $poscat;

        return $this;
    }

    public function getPoscat()
    {
        return $this->poscat;
    }

    /**
     * Set the value of
     *
     *
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getCategorie()
    {
        return $this->categorie;
    }
}
