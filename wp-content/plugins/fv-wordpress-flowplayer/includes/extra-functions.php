<?php
/*  FV Wordpress Flowplayer - HTML5 video player with Flash fallback    
    Copyright (C) 2013  Foliovision

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/ 



    if (!defined('HTTP_URL_REPLACE')) {
      define('HTTP_URL_REPLACE', 1);
    }
    if (!defined('HTTP_URL_JOIN_PATH')) {
      define('HTTP_URL_JOIN_PATH', 2);
    }
    if (!defined('HTTP_URL_JOIN_QUERY')) {
      define('HTTP_URL_JOIN_QUERY', 4);
    }
    if (!defined('HTTP_URL_STRIP_USER')) {
      define('HTTP_URL_STRIP_USER', 8);
    }
    if (!defined('HTTP_URL_STRIP_PASS')) {
      define('HTTP_URL_STRIP_PASS', 16);
    }
    if (!defined('HTTP_URL_STRIP_AUTH')) {
      define('HTTP_URL_STRIP_AUTH', 32);
    }
    if (!defined('HTTP_URL_STRIP_PORT')) {
      define('HTTP_URL_STRIP_PORT', 64);
    }
    if (!defined('HTTP_URL_STRIP_PATH')) {
      define('HTTP_URL_STRIP_PATH', 128);
    }
    if (!defined('HTTP_URL_STRIP_QUERY')) {
      define('HTTP_URL_STRIP_QUERY', 256);
    }
    if (!defined('HTTP_URL_STRIP_FRAGMENT')) {
      define('HTTP_URL_STRIP_FRAGMENT', 512);
    }
    if (!defined('HTTP_URL_STRIP_ALL')) {
      define('HTTP_URL_STRIP_ALL', 1024);
    }
    
    // Build an URL
    // The parts of the second URL will be merged into the first according to the flags argument. 
    // 
    // @param  mixed      (Part(s) of) an URL in form of a string or associative array like parse_url() returns
    // @param  mixed      Same as the first argument
    // @param  int        A bitmask of binary or'ed HTTP_URL constants (Optional)HTTP_URL_REPLACE is the default
    // @param  array      If set, it will be filled with the parts of the composed url like parse_url() would return 
    function fv_http_build_url($url, $parts=array(), $flags=HTTP_URL_REPLACE, &$new_url=false)
    {
      $keys = array('user','pass','port','path','query','fragment');
      
      // HTTP_URL_STRIP_ALL becomes all the HTTP_URL_STRIP_Xs
      if ($flags & HTTP_URL_STRIP_ALL)
      {
        $flags |= HTTP_URL_STRIP_USER;
        $flags |= HTTP_URL_STRIP_PASS;
        $flags |= HTTP_URL_STRIP_PORT;
        $flags |= HTTP_URL_STRIP_PATH;
        $flags |= HTTP_URL_STRIP_QUERY;
        $flags |= HTTP_URL_STRIP_FRAGMENT;
      }
      // HTTP_URL_STRIP_AUTH becomes HTTP_URL_STRIP_USER and HTTP_URL_STRIP_PASS
      else if ($flags & HTTP_URL_STRIP_AUTH)
      {
        $flags |= HTTP_URL_STRIP_USER;
        $flags |= HTTP_URL_STRIP_PASS;
      }
      
      // Parse the original URL
      $parse_url = parse_url($url);
      
      // Scheme and Host are always replaced
      if (isset($parts['scheme']))
        $parse_url['scheme'] = $parts['scheme'];
      if (isset($parts['host']))
        $parse_url['host'] = $parts['host'];
      
      // (If applicable) Replace the original URL with it's new parts
      if ($flags & HTTP_URL_REPLACE)
      {
        foreach ($keys as $key)
        {
          if (isset($parts[$key]))
            $parse_url[$key] = $parts[$key];
        }
      }
      else
      {
        // Join the original URL path with the new path
        if (isset($parts['path']) && ($flags & HTTP_URL_JOIN_PATH))
        {
          if (isset($parse_url['path']))
            $parse_url['path'] = rtrim(str_replace(basename($parse_url['path']), '', $parse_url['path']), '/') . '/' . ltrim($parts['path'], '/');
          else
            $parse_url['path'] = $parts['path'];
        }
        
        // Join the original query string with the new query string
        if (isset($parts['query']) && ($flags & HTTP_URL_JOIN_QUERY))
        {
          if (isset($parse_url['query']))
            $parse_url['query'] .= '&' . $parts['query'];
          else
            $parse_url['query'] = $parts['query'];
        }
      }
        
      // Strips all the applicable sections of the URL
      // Note: Scheme and Host are never stripped
      foreach ($keys as $key)
      {
        if ($flags & (int)constant('HTTP_URL_STRIP_' . strtoupper($key)))
          unset($parse_url[$key]);
      }
      
      
      $new_url = $parse_url;
      
      return 
         ((isset($parse_url['scheme'])) ? $parse_url['scheme'] . '://' : '')
        .((isset($parse_url['user'])) ? $parse_url['user'] . ((isset($parse_url['pass'])) ? ':' . $parse_url['pass'] : '') .'@' : '')
        .((isset($parse_url['host'])) ? $parse_url['host'] : '')
        .((isset($parse_url['port'])) ? ':' . $parse_url['port'] : '')
        .((isset($parse_url['path'])) ? $parse_url['path'] : '')
        .((isset($parse_url['query'])) ? '?' . $parse_url['query'] : '')
        .((isset($parse_url['fragment'])) ? '#' . $parse_url['fragment'] : '')
      ;
    }




if( !function_exists('is_utf8') && function_exists('mb_strlen') ) :

	function is_utf8($str) {
		return ( (mb_strlen($str) != strlen($str) ) ? true : false );
	}

endif; 
