<?php
/* Copyright (C) 2015 James Campbell <neokjames@gmail.com>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>. */

/* Copyright (C) 2015 Daniel Preussker <f0o@devilcode.org>
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>. */

/**
 * Pushover API Transport
 * @author neokjames <neokjames@gmail.com>
 * @copyright 2015 neokjames, f0o, LibreNMS
 * @license GPL
 * @package LibreNMS
 * @subpackage Alerts
 */
namespace LibreNMS\Alert\Transport;

use LibreNMS\Interfaces\Alert\Transport;

class Pushover implements Transport
{
    public function deliverAlert($obj, $opts)
    {
        foreach ($opts as $api) {
            $data          = array();
            $data['token'] = $api['appkey'];
            $data['user']  = $api['userkey'];
            switch ($obj['severity']) {
                case "critical":
                    $data['priority'] = 1;
                    if (!empty($api['sound_critical'])) {
                        $data['sound'] = $api['sound_critical'];
                    }
                    break;
                case "warning":
                    $data['priority'] = 1;
                    if (!empty($api['sound_warning'])) {
                        $data['sound'] = $api['sound_warning'];
                    }
                    break;
            }
            switch ($obj['state']) {
                case 0:
                    $data['priority'] = 0;
                    if (!empty($api['sound_ok'])) {
                        $data['sound'] = $api['sound_ok'];
                    }
                    break;
            }
            $data['title']   = $obj['title'];
            $data['message'] = $obj['msg'];
            $curl            = curl_init();
            set_curl_proxy($curl);
            curl_setopt($curl, CURLOPT_URL, 'https://api.pushover.net/1/messages.json');
            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $ret  = curl_exec($curl);
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($code != 200) {
                var_dump("Pushover returned error"); //FIXME: proper debugging
                return 'HTTP Status code ' . $code;
            }
        }
        return true;
    }
}
