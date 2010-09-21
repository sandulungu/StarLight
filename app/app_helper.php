<?php

class AppHelper extends Helper {

    /**
     * Extended Router::url() with proper html entities and spaces replaced by '+'
     *
     * @param mixed $url Set to bool (true = 'with base' or false) to get current requestUri
     * @param bool $full
     * @return string
     * @static
     */
    function url($url = null, $full = false) {
        return h(r(' ', '+', Sl::url($url, $full)));
    }

    /**
     * A mix between Helper::webroot() and Helper::assetTimestamp()
     *
     * @param <type> $name
     * @param <type> $type
     * @return <type>
     */
    function assetUrl($name, $type = null) {
		if (strpos($name, '://') !== false) {
            return $name;
        }

        // appends default extension
        if (strpos($name, '?') === false) {
            if ($type && !preg_match("/\.$type$/", $name)) {
                $name .= ".$type";
            }
        }

        // either assets root, vendors or cdn file here
        if ($name{0} !== '/') {
            if (strpos($name, '/') === false) {
                if ($type) {
                    $name = "$type/$name";
                }
            }
            else {
                list($vendor, $filename) = explode('/', $name, 2);
                $cdn = SlConfigure::read("Asset.cdn.{$vendor}");
                if ($cdn) {
                    $cdnUrl = "$cdn/$filename";
                    if (SlConfigure::read("Asset.options.alwaysUseCdn")) {
                        return $cdnUrl;
                    }
                }

                if (file_exists(WWW_ROOT . "vendors/$name")) {
                    $name = "vendors/$name";
                } else {
                    if ($type) {
                        $name = "$type/$name";
                    }
                }
            }
        }
        // webroot or plugin file here
        else {
            $name = substr($name, 1);
        }

        if (file_exists(WWW_ROOT . "theme/$this->theme/" . $name)) {
            $path = WWW_ROOT . "theme/$this->theme/" . $name;
            $url = $this->webroot . "theme/$this->theme/" . $name;
        }
        elseif (file_exists(WWW_ROOT . $name)) {
            $path = WWW_ROOT . $name;
            $url = $this->webroot . $name;
        }
        else {
            $themePath = App::themePath($this->theme) . "webroot/$name";
            if (file_exists($themePath)) {
                $path = $themePath;
                $url = $this->webroot . "theme/$this->theme/" . $name;
            }
            else {
                if (!empty($cdnUrl)) {
                    return $cdnUrl;
                }

                list($plugin, $fileName) = explode('/', $name, 2);
                if ($plugin == 'theme') {
                    list($theme, $fileName) = explode('/', $fileName, 2);
                    $path = App::themePath($theme) . "webroot/$filename";
                } else {
                    $path = App::pluginPath($plugin) . "webroot/$fileName";
                }
                $url = $this->webroot . $name;
            }
        }

        if (strpos($url, '?') === false && file_exists($path) && SlConfigure::read('Asset.options.timestamp')) {
            $url .= '?' . filemtime($path);
        }
        return $url;
    }

//    public function themePath($theme) {
//        if ($this->plugin) {
//            $dir = App::pluginPath($this->plugin). "views/themed/$theme";
//            if (is_dir($dir)) {
//                return "$dir/";
//            }
//        }
//        return App::themePath($theme);
//    }

  	function assetTimestamp($path) {
		if (strpos($path, '?') === false && SlConfigure::read('Asset.options.timestamp')) {
			return $this->assetUrl(preg_replace('/^' . preg_quote($this->webroot, '/') . '/', '', $path));
        }
        return $path;
    }

}