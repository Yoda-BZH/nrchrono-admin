<?php

namespace App\Service;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Doctrine\Common\Collections\Collection;


class CssFileGenerator
{
    public function __construct(
        private ContainerBagInterface $params,
    )
    {
    }

    public function setTeams(Collection|array $teams): static
    {
        $this->teams = $teams;

        return $this;
    }

    public function generate(): static
    {
        $colors = array();

        $declarationBgTemplate = '.team-bg-color-%d { background-color: %s !important; }';
        $declarationFgTemplate = '.team-color-%d { color: %s !important; }';
        $declarationGradient   = '.team-color-grad-%d { background-image: linear-gradient(to left, %s, rgba(255, 255, 255, 0) 30%%); }';
        $declarationdDouble = '.team-color-double-%d {
    background: -moz-linear-gradient(top,     %2$s 0%%, %2$s 1%%, rgba(255,255,255,0) 10%%, rgba(255,255,255,0) 90%%, %2$s 99%%, %2$s 100%%);
    background: -webkit-linear-gradient(top,  %2$s 0%%, %2$s 1%%, rgba(255,255,255,0) 10%%, rgba(255,255,255,0) 90%%, %2$s 99%%, %2$s 100%%);
    background: linear-gradient(to bottom,    %2$s 0%%, %2$s 1%%, rgba(255,255,255,0) 10%%, rgba(255,255,255,0) 90%%, %2$s 99%%, %2$s 100%%);
}';

        $declarationDouble2 = '.team-color-double2-%d {
    background: -moz-linear-gradient(45deg,    %2$s 0%%, %2$s %3$d%%, rgba(255,255,255,0) %3$d%%, rgba(255,255,255,0) %4$d%%, %2$s %4$d%%, %2$s 100%%, %2$s 100%%);
    background: -webkit-linear-gradient(45deg, %2$s 0%%, %2$s %3$d%%, rgba(255,255,255,0) %3$d%%, rgba(255,255,255,0) %4$d%%, %2$s %4$d%%, %2$s 100%%, %2$s 100%%);
    background: linear-gradient(45deg,         %2$s 0%%, %2$s %3$d%%, rgba(255,255,255,0) %3$d%%, rgba(255,255,255,0) %4$d%%, %2$s %4$d%%, %2$s 100%%, %2$s 100%%);
}';

        foreach($this->teams as $team)
        {
            if(!$team->getColor())
            {
                continue;
            }
            $colors[] = sprintf($declarationBgTemplate,
                $team->getId(),
                $team->getColor()
            );

            $colors[] = sprintf($declarationFgTemplate,
                $team->getId(),
                $team->getColor()
            );

            $colors[] = sprintf($declarationGradient,
                $team->getId(),
                $team->getColor()
            );

            $colors[] = sprintf($declarationdDouble,
                $team->getId(),
                $this->hex2rgba($team->getColor(), true)
            );

            $percent = 18;
            $colors[] = sprintf($declarationDouble2,
                $team->getId(),
                $this->hex2rgba($team->getColor(), true),
                $percent,
                100 - $percent
            );

            $colors[] = '';

        }

        $ret = file_put_contents($this->params->get('kernel.project_dir').'/public/teams.css', implode(PHP_EOL, $colors)) > 0;
        if(!$ret)
        {
            throw new \RuntimeException('Unable to write public/teams.css');
        }

        $ret = @file_put_contents('../nrchrono-dashboard/assets/stylesheets/teams.css', implode(PHP_EOL, $colors)) > 0;
        if(!$ret)
        {
            //throw new \RuntimeException('Unable to write ../nrchrono-dashboard/assets/stylesheets/teams.css');
        }

        return $this;
    }

    /**
     * http://mekshq.com/how-to-convert-hexadecimal-color-code-to-rgb-or-rgba-using-php/
     * Convert hexdec color string to rgb(a) string
     */
    public function hex2rgba($color, $opacity = false)
    {

        $default = 'rgb(0,0,0)';

        //Return default if no color provided
        if(empty($color))
        {
            return $default;
        }

        //Sanitize $color if "#" is provided
        if ($color[0] == '#' )
        {
            $color = substr( $color, 1 );
        }

        //Check if color has 6 or 3 characters and get values
        if (\strlen($color) == 6)
        {
            $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
        }
        elseif (\strlen( $color ) == 3 )
        {
            $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
        }
        else
        {
            return $default;
        }

        //Convert hexadec to rgb
        $rgb = array_map('hexdec', $hex);

        //Check if opacity is set(rgba or rgb)
        if($opacity)
        {
            if(abs($opacity) > 1)
                $opacity = 1.0;
            $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
        }
        else
        {
            $output = 'rgb('.implode(",",$rgb).')';
        }

        //Return rgb(a) color string
        return $output;
    }
}
