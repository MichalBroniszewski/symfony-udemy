<?php
declare(strict_types=1);
/**
 * File: AppExtension.php
 *
 * @author    Michal Broniszewski <michal.broniszewski@lizardmedia.pl>
 * @copyright Copyright (C) 2018 Lizard Media (http://lizardmedia.pl)
 */

namespace App\Twig;

use App\Entity\LikeNotification;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

/**
 * Class AppExtension
 * @package App\Twig
 */
class AppExtension extends AbstractExtension implements GlobalsInterface
{
    /**
     * @var string
     */
    private $locale;

    /**
     * AppExtension constructor.
     * @param string $locale
     */
    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    /**
     * @return array|\Twig_Filter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('price', [$this, 'priceFilter'])
        ];
    }

    /**
     * @param $number
     * @return string
     */
    public function priceFilter($number)
    {
        return '$' . number_format($number, 2);
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return [
            'locale' => $this->locale
        ];
    }

    public function getTests()
    {
        return [
            new \Twig_SimpleTest('like',
                function ($obj) {
                    return $obj instanceof LikeNotification;
                }
            )
        ];
    }
}
