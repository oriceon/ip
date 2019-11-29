<?php

namespace Oriceon;

class Ip {

    private static $local_masks = [
        '127\.0.0.1'                    => '127.0.0.1',
        '10\.\d{1,3}\.\d{1,3}\.\d{1,3}' => '10.0.0.0 - 10.255.255.255',
        '172.16\.\d{1,3}\.\d{1,3}'      => '172.16.0.0 - 172.31.255.255',
        '192.168\.\d{1,3}\.\d{1,3}'     => '192.168.0.0 - 192.168.255.255',
    ];

    public static function get($options = [])
    {
        $ip = self::_check_ip();

        if
        (
            ! isset($options['force_wan']) ||
            (
                isset($options['force_wan']) && $options['force_wan'] == true
            )
        )
        {
            return self::_parse_ip($ip);
        }

        return $ip;
    }


    private static function _check_ip()
    {
        $keys = [
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR',
        ];

        foreach ($keys as $key)
        {
            if ($_SERVER)
            {
                if (array_key_exists($key, $_SERVER))
                {
                    return $_SERVER[$key];
                }
            }
            else if (getenv($key))
            {
                return getenv($key);
            }
        }

        return 'Error';
    }

    private static function _parse_ip($ip)
    {
        if (self::_is_local_ip($ip))
        {
            // only if we find that provided ip is a local ip
            // then try to get the wan one ...

            $url = 'http://bot.whatismyipaddress.com';

            $envUrl = getenv('ORICEON_IP_API_URL');
            if ($envUrl !== false)
            {
                $url = $envUrl;
            }


            if ( ! empty($url))
            {
                $parse_ip = preg_replace('/[^0-9.]/', '', trim(strip_tags(@file_get_contents(trim($url)))));

                if ( ! empty($parse_ip))
                {
                    return $parse_ip;
                }
            }
        }

        return $ip;
    }

    private static function _is_local_ip($ip)
    {
        $is_local = false;

        foreach (self::$local_masks as $ip_mask => $ip_desc)
        {
            if (preg_match("/^" . $ip_mask . "\z/", $ip))
            {
                $is_local = true;
                break;
            }
        }

        return $is_local;
    }

}
