<?php

namespace App\Helpers;

use JeroenNoten\LaravelAdminLte\Menu\Builder;
use JeroenNoten\LaravelAdminLte\Menu\Filters\FilterInterface;
use Laratrust;
use Auth;

class MenuFilter implements FilterInterface
{
    public function transform($item, Builder $builder)
    {
        // prepare variables
        $stationManagerBlackList = ['stations', 'stationManagers', 'users'];
        $userTypeID = Auth::user()->user_type_id;
        $isMenu = array_key_exists('url', $item);

        if ($isMenu) {
            if ($userTypeID == 2) { // station manager
                if (in_array($item['url'], $stationManagerBlackList)) {
                    $item = false;
                }
            }
        }

        return $item;
    }
}