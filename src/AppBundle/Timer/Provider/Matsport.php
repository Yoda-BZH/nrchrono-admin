<?php

namespace AppBundle\Timer\Provider;

use AppBundle\Timer\Tour;
use AppBundle\Timer\Team;


class Matsport implements Provider {

//    private $generalUrl = 'http://www.matsport.fr.php53-23.ord1-1.websitetestlink.com/masse/index.php?an=2014&code_course=24R&menu=1&type=1&Num_Menu=';
//    private $generalUrl = 'http://www.matsport.fr.php53-23.ord1-1.websitetestlink.com/masse/index.php?an=2015&code_course=24R&menu=2&type=1&Num_Menu=0&DebutDossard=1&DebutAlpha=A';
    //private $generalUrl = 'http://www.matsport.fr.php53-23.ord1-1.websitetestlink.com/masse/index.php?an=2015&code_course=24R&menu=1&type=1&Num_Menu=';
    private $generalUrl = 'http://www.matsport.fr.php53-23.ord1-1.websitetestlink.com/masse/index.php?an=2016&code_course=24R&menu=1&type=1&Num_Menu=';

    private $equipeUrl = 'http://www.matsport.fr.php53-23.ord1-1.websitetestlink.com/masse/recap_coureur_1.php?dossard=%d&code_course=24R&an=%d';

    public function __construct()
    {
    }

    public function setGeneralUrl($url)
    {
        $this->generalUrl = $url;

        return $this;
    }

    public function setEquipeUrl($url)
    {
        $this->equipeUrl = $url;

        return $this;
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function getGeneral()
    {
        $data = file_get_contents($this->generalUrl);
        $dir = '/var/tmp/matsport/'.date('Y/m/d');
        if(!is_dir($dir))
        {
            mkdir($dir, 0775, true);
        }
        file_put_contents($dir.'/matsport.'.time().'.html', $data);
        //$pattern = '|<tr><td align=\'left\' width=\'40\'>(.*)</td></tr>|U';
        //$pattern = '|<td align=\'left\' width=\'40\'><div class=Style9>(\d+)</div></div></td><td align="left" width=\'40\'><div class=Style9>(\d+)</div></td><td align="left"><div class="Style9"><a href="javascript:affichage_popup\(\'\./recap_coureur_1\.php\?dossard=(\d+)&code_course=24R&an=(\d+)\',\'Matsport Live\'\);">(.*)</a></div></td><td align="left"><div class=Style9>(.*)</div></td><td align="left"><div class=Style9>(.*)</div></td><td align="left"><div class=Style9>(.*)</div></td><td align="left"><div class=Style9>(.*)</div></td><td align="left"><div class=Style9>(.*)</div></td><td align="left"><div class=Style9>(.*)</div></td><td align="left"><div class=Style9>(.*)</div></td><td align="left"><div class=Style9>(.*)</div></td>|U';
//        $pattern = '|<td align=\'left\' width=\'40\'><div class=Style9>(\d+)</div></div></td><td align="left"><div class=Style9><a href="javascript:affichage_popup\(\'\./recap_coureur_1\.php\?dossard=(\d+)&code_course=24R&an=2015\',\'Matsport Live\'\);">NR BN</a></div></td><td align=\'left\'><div class=Style9>(.*)</div></td><td align=\'left\'><div class=Style9>(.*)</div></td>|U';
//        $pattern = '|dossard=(\d+)&code_course=24R&an=2015\',\'Matsport Live\');">NR BN</a></div></td><td align=\'left\'><div class=Style9>(.*)</div></td><td align=\'left\'><div class=Style9>(.*)</div></td>|U';
        //$pattern = '|<tr><td align=\'left\' width=\'40\'><div class=Style9>(\d+)</div></div></td><td align=\'left\' width=\'40\'><div class=Style9>(\d+)</div></td>(.*)</td></tr>|U';
        $pattern = '|<div class=Style9>(\d+)</div></div></td><td align=\'LEFT\' width=\'40\'><div class=Style9>(\d+)</div></td><td align="left"><div class="Style9"><a href="javascript:affichage_popup\(\'\./recap_coureur_1\.php\?dossard=(\d+)&code_course=24R&an=(\d+)\',\'Matsport Live\'\);">(.*)</a></div></td><td align=\'LEFT\'><div class=Style9>(.*)</div></td><td align=\'LEFT\'><div class=Style9>(.*)</div></td><td align=\'LEFT\'><div class=Style9>(.*)</div></td><td align=\'LEFT\'><div class=Style9>(.*)</div></td><td align=\'LEFT\'><div class=Style9>(.*)</div></td><td align=\'LEFT\'><div class=Style9>(.*)</div></td><td align=\'LEFT\'><div class=Style9>(.*)</div></td><td align=\'LEFT\'><div class=Style9>(.*)</div></td></tr>|U';

        preg_match_all($pattern, $data, $matches);

        //foreach($matches as $a => $b) {
        //    var_dump($b[23]);
        //}

        $equipes = array();
        foreach($matches[0] as $k => $v) {

            //if(!preg_match('/^NR /', $matches[5][$k])) {
            //    continue;
            //}
            // sometimes there a 'C' before the number of laps
            $matches[7][$k] = trim(str_replace('(C)', '', $matches[7][$k]));

            $team = new Team;
            $team
                ->setPosition($matches[1][$k])
                ->setNumero($matches[2][$k])
                //'numero'   => $matches[3][$k],
                ->setAnnee($matches[4][$k])
                ->setNom($matches[5][$k])
                ->setTemps($matches[6][$k])
                ->setTour($matches[7][$k])
                ->setEcart($matches[8][$k])
                ->setDistance($matches[9][$k])
                ->setVitesse($matches[10][$k])
                ->setBestlap($matches[11][$k])
                ->setPoscat($matches[12][$k])
                ->setCategorie($matches[13][$k])
                ;
            $equipes[$matches[2][$k]] = $team;
        }

        return $equipes;
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    /*public function getTeam($id)
    {
        $url = sprintf($this->equipe, $id, 2014);

        $data = file_get_contents($url);
        //var_dump($data);

        $pattern = '|<tr width=\'100%\' bgcolor="\w+">(.*)</tr>|Ums';
        //$pattern = '|tr(.*)/tr|ms';

        preg_match_all($pattern, $data, $matches);
        //var_dump($matches);

        $patternData = '|<b>(.*)</b>|U';
        $patternRelais = '|relais\.png|';
        $patternBestTime = '|BestTime\.png|';
        $temps = array();
        foreach($matches[1] as $k => $v) {
            preg_match_all($patternData, $v, $m);
            $tour = $m[1][0];
            $duree = $m[1][1];

            $relais = (bool) (preg_match($patternRelais, $v));
            //if($relais) { echo 'found relais'.PHP_EOL; }
            $bestTime = (bool) (preg_match($patternBestTime, $v));
            //if($bestTime) { echo 'found best time'.PHP_EOL; }
            / *$temps[$tour] = array(
                'duree' => $duree,
                'relais' => $relais,
                'bestTime' => $bestTime,
            );* /
            $temps[$tour] = new Tour;
            $temps[$tour]
                ->setDuree($duree)
                ->setRelai($relai)
                ->setBestTime($bestTime)
                ;
        }

        return $temps;
    }*/

    public function getTeam($id)
    {
    }

}
