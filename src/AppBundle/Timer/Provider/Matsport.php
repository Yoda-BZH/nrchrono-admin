<?php

namespace AppBundle\Timer\Provider;

use AppBundle\Timer\Tour;
use AppBundle\Timer\Team;


class Matsport implements Provider {

    private $general = 'http://www.matsport.fr.php53-23.ord1-1.websitetestlink.com/masse/index.php?an=2014&code_course=24R&menu=1&type=1&Num_Menu=';

    private $equipe = 'http://www.matsport.fr.php53-23.ord1-1.websitetestlink.com/masse/recap_coureur_1.php?dossard=%d&code_course=24R&an=%d';

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function getGeneral()
    {
        $data = file_get_contents($this->general);

        //$pattern = '|<tr><td align=\'LEFT\' width=\'40\'>(.*)</td></tr>|U';
        $pattern = '|<tr><td align=\'LEFT\' width=\'40\'><div class=Style9>(\d+)</div></div></td><td align=\'LEFT\' width=\'40\'><div class=Style9>(\d+)</div></td><td align="left"><div class="Style9"><a href="javascript:affichage_popup\(\'\./recap_coureur_1\.php\?dossard=(\d+)&code_course=24R&an=(\d+)\',\'Matsport Live\'\);">(.*)</a></div></td><td align=\'LEFT\'><div class=Style9>(.*)</div></td><td align=\'LEFT\'><div class=Style9>(.*)</div></td><td align=\'LEFT\'><div class=Style9>(.*)</div></td><td align=\'LEFT\'><div class=Style9>(.*)</div></td><td align=\'LEFT\'><div class=Style9>(.*)</div></td><td align=\'LEFT\'><div class=Style9>(.*)</div></td><td align=\'LEFT\'><div class=Style9>(.*)</div></td><td align=\'LEFT\'><div class=Style9>(.*)</div></td></tr>|U';
        //$pattern = '|<tr><td align=\'LEFT\' width=\'40\'><div class=Style9>(\d+)</div></div></td><td align=\'LEFT\' width=\'40\'><div class=Style9>(\d+)</div></td>(.*)</td></tr>|U';

        preg_match_all($pattern, $data, $matches);
        //var_dump($matches);

        //foreach($matches as $a => $b) {
        //    var_dump($b[23]);
        //}

        $equipes = array();
        foreach($matches[0] as $k => $v) {
            if(!preg_match('/^NR /', $matches[5][$k])) {
                continue;
            }
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
            $equipes[] = $team;
        }

        return $equipes;
    }

    /**
     * description
     *
     * @param void
     * @return void
     */
    public function getTeam($id)
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
            /*$temps[$tour] = array(
                'duree' => $duree,
                'relais' => $relais,
                'bestTime' => $bestTime,
            );*/
            $temps[$tour] = new Tour;
            $temps[$tour]
                ->setDuree($duree)
                ->setRelai($relai)
                ->setBestTime($bestTime)
                ;
        }

        return $temps;
    }

}