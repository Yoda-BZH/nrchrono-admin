<?php

namespace AppBundle\Timer\Provider;

use AppBundle\Timer\Tour;
use AppBundle\Timer\Team;


class Chronelec implements Provider {

//    private $generalUrl = 'http://www.matsport.fr.php53-23.ord1-1.websitetestlink.com/masse/index.php?an=2014&code_course=24R&menu=1&type=1&Num_Menu=';
//    private $generalUrl = 'http://www.matsport.fr.php53-23.ord1-1.websitetestlink.com/masse/index.php?an=2015&code_course=24R&menu=2&type=1&Num_Menu=0&DebutDossard=1&DebutAlpha=A';
    //private $generalUrl = 'http://www.matsport.fr.php53-23.ord1-1.websitetestlink.com/masse/index.php?an=2015&code_course=24R&menu=1&type=1&Num_Menu=';
    private $generalUrl = 'http://chronelec.free.fr/classements/_Live_Html/Resultats.html';

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
        $dir = '/var/tmp/chronelec/'.date('Y/m/d');
        if(!is_dir($dir))
        {
            mkdir($dir, 0775, true);
        }
        file_put_contents($dir.'/chronelec.'.time().'.html', $data);
        //$pattern = '|<tr><td align=\'left\' width=\'40\'>(.*)</td></tr>|U';
        //$pattern = '|<td align=\'left\' width=\'40\'><div class=Style9>(\d+)</div></div></td><td align="left" width=\'40\'><div class=Style9>(\d+)</div></td><td align="left"><div class="Style9"><a href="javascript:affichage_popup\(\'\./recap_coureur_1\.php\?dossard=(\d+)&code_course=24R&an=(\d+)\',\'Matsport Live\'\);">(.*)</a></div></td><td align="left"><div class=Style9>(.*)</div></td><td align="left"><div class=Style9>(.*)</div></td><td align="left"><div class=Style9>(.*)</div></td><td align="left"><div class=Style9>(.*)</div></td><td align="left"><div class=Style9>(.*)</div></td><td align="left"><div class=Style9>(.*)</div></td><td align="left"><div class=Style9>(.*)</div></td><td align="left"><div class=Style9>(.*)</div></td>|U';
//        $pattern = '|<td align=\'left\' width=\'40\'><div class=Style9>(\d+)</div></div></td><td align="left"><div class=Style9><a href="javascript:affichage_popup\(\'\./recap_coureur_1\.php\?dossard=(\d+)&code_course=24R&an=2015\',\'Matsport Live\'\);">NR BN</a></div></td><td align=\'left\'><div class=Style9>(.*)</div></td><td align=\'left\'><div class=Style9>(.*)</div></td>|U';
//        $pattern = '|dossard=(\d+)&code_course=24R&an=2015\',\'Matsport Live\');">NR BN</a></div></td><td align=\'left\'><div class=Style9>(.*)</div></td><td align=\'left\'><div class=Style9>(.*)</div></td>|U';
        //$pattern = '|<tr><td align=\'left\' width=\'40\'><div class=Style9>(\d+)</div></div></td><td align=\'left\' width=\'40\'><div class=Style9>(\d+)</div></td>(.*)</td></tr>|U';
        $pattern = '|<td align="right" class="">(.*)</td>\s+<td align="right" class="">(.*)</td>\s+<td align="left" class="">(.*)</td>\s+<td align="center" class="">(.*)</td>\s+<td align="left" class="">(.*)</td>\s+<td align="center" class="">(.*)</td>\s+<td align="right" class="">(.*)</td>\s+<td align="left" class="">(.*)</td>\s+<td align="right" class="">(.*)</td>\s+<td align="right" class="">(.*)</td>\s+<td align="left" class="">(.*)</td>|Um';

        preg_match_all($pattern, $data, $matches);
//        foreach($matches as $a => $b) {
//            var_dump($b[0]);
//        }

        $equipes = array();
        foreach($matches[0] as $k => $v) {

            //if(!preg_match('/^NR /', $matches[5][$k])) {
            //    continue;
            //}
            // sometimes there a 'C' before the number of laps
//            $matches[7][$k] = trim(str_replace('(C)', '', $matches[7][$k]));

            $team = new Team;
            $team
                ->setPosition($matches[1][$k])
                ->setNumero($matches[4][$k])
                //'numero'   => $matches[3][$k],
                ->setAnnee('2016')
                ->setNom($matches[5][$k])
                ->setTemps('00:00:00') //$matches[8][$k])
                ->setTour($matches[6][$k])
                ->setEcart($matches[7][$k] == '-' ? '00:00:00' : $matches[7][$k])
                ->setDistance($matches[10][$k])
                ->setVitesse($matches[11][$k])
                ->setBestlap($matches[9][$k] == '-' ? '00:00' : $matches[9][$k])
                ->setPoscat($matches[2][$k])
                ->setCategorie($matches[3][$k])
                ;

            $equipes[$matches[5][$k]] = $team;
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
