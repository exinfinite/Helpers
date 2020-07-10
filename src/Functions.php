<?php
//namespace Exinfinite\Helpers;
if (!function_exists(__NAMESPACE__ . '\randStr')) {
    function randStr($len = 16) {
        $seeds = str_split('ABCDEFGHJKLMNPQRSTUVWXYZ23456789');
        $seeds_len = count($seeds);
        shuffle($seeds);
        return implode(
            array_map(function ($v) use ($seeds, $seeds_len) {
                return $seeds[mt_rand(0, $seeds_len - 1)];
            }, range(1, $len))
        );
    }
}
if (!function_exists(__NAMESPACE__ . '\sanitizeStr')) {
    function sanitizeStr($input) {
        return is_string($input) ? filter_var($input, FILTER_SANITIZE_STRING) : false;
    }
}
//if (!function_exists(__NAMESPACE__ . '\uuid')) {
    function uuid() {
        mt_srand((double) microtime() * 10000);
        $charid = strtoupper(md5(uniqid(rand(), true)));
        return implode(chr(45), [
            substr($charid, 0, 8),
            substr($charid, 8, 4),
            substr($charid, 12, 4),
            substr($charid, 16, 4),
            substr($charid, 20, 12),
        ]);
    }
//}
if (!function_exists(__NAMESPACE__ . '\captcha')) {
    function captcha($session_name = "verification", $txt = 4, $line = 5, $pixel = 150) {
        unset($_SESSION[$session_name]);
        mt_srand((double) microtime() * 1000000);
        $verification__session = substr(str_shuffle('abcdefhijkmnprstuvxyz2345678'), 0, $txt);
        $_SESSION[$session_name] = $verification__session;
        $imageWidth = 120;
        $imageHeight = 50;
        $im = @imagecreatetruecolor($imageWidth, $imageHeight)
        or die("無法建立圖片！");
        $bgColor = imagecolorallocate($im, rand(240, 255), rand(240, 255), rand(240, 255));
        $Color = imagecolorallocate($im, rand(0, 150), rand(0, 150), rand(0, 150));
        $gray1 = imagecolorallocate($im, rand(0, 150), rand(0, 150), rand(0, 150));
        $gray2 = imagecolorallocate($im, rand(0, 100), rand(100, 200), rand(0, 100));
        imagefill($im, 0, 0, $bgColor);
        // 干擾線條
        for ($i = 0; $i < $line; $i++) {
            imageline($im, rand(0, $imageWidth), rand(0, $imageHeight),
                rand($imageHeight, $imageWidth), rand(0, $imageHeight), $Color);
        }
        imagettftext($im, rand(32, 38), rand(-3, 3), rand(15, 35), rand(30, 40), $Color, _dirTheme . "/fonts/70729___.TTF", $verification__session);
        // 干擾像素
        for ($i = 0; $i < $pixel; $i++) {
            imagesetpixel($im, rand() % $imageWidth,
                rand() % $imageHeight, $Color);
        }
        ob_start();
        imagepng($im);
        $imagedata = ob_get_clean();
        imagedestroy($im);
        return "data:image/png;base64," . base64_encode($imagedata);
    }
}
if (!function_exists(__NAMESPACE__ . '\captchaVerity')) {
    function captchaVerify($session_name, $input) {
        $rst = array_key_exists($session_name, $_SESSION) ?
        (mb_strtolower(trim($input)) == mb_strtolower(trim($_SESSION[$session_name]))) :
        false;
        unset($_SESSION[$session_name]);
        return $rst;
    }
}
if (!function_exists(__NAMESPACE__ . '\emailName')) {
    function emailName($email) {
        return is_string($email) ? explode('@', $email, 2)[0] : '';
    }
}
if (!function_exists(__NAMESPACE__ . '\dateFormat')) {
    function dateFormat($date, $format = "Y-m-d") {
        return date($format, strtotime($date));
    }
}
if (!function_exists(__NAMESPACE__ . '\plainText')) {
    function plainText($input) {
        return htmlspecialchars($input);
    }
}
if (!function_exists(__NAMESPACE__ . '\splitWords')) {
    function splitWords($input, $sp = '&sp;') {
        return is_string($input) ? explode($sp, $input) : [];
        /* return explode(
    $sp,
    preg_replace(
    '/[^\x{4e00}-\x{9fa5}A-Za-z0-9@\.]+/u', $sp,
    strip_tags($input)
    )
    ); */
    }
}
if (!function_exists(__NAMESPACE__ . '\stripDomain')) {
    function stripDomain($input, $domain) {
        $scheme = ['http:', 'https:'];
        $input = str_replace($scheme, '', $input);
        $domain = str_replace($scheme, '', $domain);
        return str_replace($domain, '', $input);
    }
}
if (!function_exists(__NAMESPACE__ . '\extractAllowKeys')) {
    function extractAllowKeys(array $datas, array $needles, $all = false, $dft = null) {
        $inNeed = call_user_func(function (array $datas, array $needles) {
            extract($datas);
            return compact($needles);
        }, $datas, $needles);
        if ($all === false) {
            return $inNeed;
        }
        $flip = array_flip($needles);
        $outNeed = array_fill_keys(
            array_flip(
                array_diff_key($flip, $datas)
            ), $dft
        );
        return array_merge($inNeed, $outNeed);
    }
}
if (!function_exists(__NAMESPACE__ . '\uploadImg')) {
    function uploadImg($file, $target_name, $file_dir, $max_width = 1920, $max_height = 1080, $max_size = 20 * 1048576) {
        if ($file["tmp_name"]) {
            if ($file["size"] > $max_size) {
                return "";
            }
            if (!file_exists($file_dir) && !is_dir($file_dir)) {
                mkdir($file_dir, 0755, true);
            }
            $file_host = $file_dir . "/" . $target_name;
            $resample = [
                'image/jpg' => function ($tmp, $thumb) {
                    return imagecreatefromjpeg($tmp);
                },
                'image/jpeg' => function ($tmp, $thumb) {
                    return imagecreatefromjpeg($tmp);
                },
                'image/gif' => function ($tmp, $thumb) {
                    return imagecreatefromgif($tmp);
                },
                'image/png' => function ($tmp, $thumb) {
                    imagefill($thumb, 0, 0, 0x7fff0000);
                    imagealphablending($thumb, false);
                    imagesavealpha($thumb, true);
                    $source = imagecreatefrompng($tmp);
                    imagesavealpha($source, true);
                    return $source;
                },
            ];
            $resize = function ($file) use ($max_width, $max_height, $resample) {
                list($width, $height) = getimagesize($file['tmp_name']);
                $ratio = $width / $height;
                if ($width <= $max_width && $height <= $max_height) {
                    list($max_width, $max_height) = [$width, $height];
                } elseif ($max_width / $max_height > $ratio) {
                    $max_width = $max_height * $ratio;
                } else {
                    $max_height = $max_width / $ratio;
                }
                $thumb = imagecreatetruecolor($max_width, $max_height);
                $source = call_user_func($resample[$file["type"]], $file['tmp_name'], $thumb);
                imagecopyresampled($thumb, $source, 0, 0, 0, 0, $max_width, $max_height, $width, $height);
                imageinterlace($thumb, true);
                return $thumb;
            };
            switch ($file["type"]) {
            case 'image/jpg':
            case 'image/jpeg':
                //imagejpeg($resize($file), $file_host.".jpg", 95);
                $tmp_path = sys_get_temp_dir() . "/{$target_name}.jpg";
                imagejpeg($resize($file), $tmp_path, 95);
                $size = filesize($tmp_path);
                if ($size < $file["size"]) {
                    imagejpeg($resize($file), $file_host . ".jpg", 95);
                } else {
                    move_uploaded_file($file["tmp_name"], $file_host . ".jpg");
                }
                return $target_name . ".jpg";
                break;
            case 'image/gif':
                //imagegif($resize($file), $file_host.".gif");
                move_uploaded_file($file["tmp_name"], $file_host . ".gif");
                return $target_name . ".gif";
                break;
            case 'image/png':
                //imagepng($resize($file), $file_host.".png", 8);
                $tmp_path = sys_get_temp_dir() . "/{$target_name}.png";
                imagepng($resize($file), $tmp_path, 8);
                $size = filesize($tmp_path);
                if ($size < $file["size"]) {
                    imagepng($resize($file), $file_host . ".png", 8);
                } else {
                    //imagepng($file, $file_host.".png");
                    move_uploaded_file($file["tmp_name"], $file_host . ".png");
                }
                return $target_name . ".png";
                break;
            }
        }
        return false;
    }
}
if (!function_exists(__NAMESPACE__ . '\multipleImgList')) {
    function multipleImgList($files) {
        $list = [];
        $total = count($files['name']);
        $keys = array_keys($files);
        for ($i = 0; $i < $total; $i++) {
            $file = [];
            foreach ($keys as $k) {
                $file[$k] = $files[$k][$i];
            }
            array_push($list, $file);
        }
        return $list;
    }
}
if (!function_exists(__NAMESPACE__ . '\extractKeyByDefault')) {
    function extractKeyByDefault(array $datas, array $needs) {
        return collect($needs)->map(function ($dft, $k) use ($datas) {
            return collect($datas)->get($k, $dft);
        })->toArray();
    }
}
if (!function_exists(__NAMESPACE__ . '\compactHtml')) {
    function compactHtml($content) {
        $rules = [
            // remove tabs before and after HTML tags
            '/\>[^\S ]+/s' => '>',
            '/[^\S ]+\</s' => '<',
            // remove empty lines (between HTML tags); cannot remove just any line-end characters because in inline JS they can matter!
            '/\>[\r\n\t ]+\</s' => '><',
            // shorten multiple whitespace sequences
            '/(\s)+/s' => '\\1',
            // replace end of line by a space
            '/\n/' => ' ',
            // Remove any HTML comments, except MSIE conditional comments
            '/<!--(?!\s*(?:\[if [^\]]+]|<!|>))(?:(?!-->).)*-->/s' => '',
        ];
        return preg_replace(
            array_keys($rules),
            array_values($rules),
            $content
        );
    }
}
if (!function_exists(__NAMESPACE__ . '\startsWith')) {
    function startsWith($string, $startString, $caseSensitive = false) {
        $cmp = substr($string, 0, strlen($startString));
        if ((bool) $caseSensitive) {
            return strcmp($cmp, $startString) === 0;
        }
        return strcasecmp($cmp, $startString) === 0;
    }
}
if (!function_exists(__NAMESPACE__ . '\endsWith')) {
    function endsWith($string, $endString, $caseSensitive = false) {
        $cmp = substr($string, -strlen($endString));
        if ((bool) $caseSensitive) {
            return strcmp($cmp, $endString) === 0;
        }
        return strcasecmp($cmp, $endString) === 0;
    }
}
if (!function_exists(__NAMESPACE__ . '\nowIsBetween')) {
    function nowIsBetween($start, $end, $format = 'Y-m-d') {
        $now = date($format);
        $start = date($format, strtotime($start));
        $end = date($format, strtotime($end));
        return ($now >= $start && $now <= $end);
    }
}
if (!function_exists(__NAMESPACE__ . '\isJson')) {
    function isJson($string) {
        if (!is_string($string)) {
            return false;
        }
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
if (!function_exists(__NAMESPACE__ . '\joinPath')) {
    function joinPath(array $pieces, $glue, $trim = false) {
        if (is_string($trim)) {
            $pieces = array_map(function ($s) use ($trim) {
                return trim($s, $trim);
            }, $pieces);
        }
        return join($glue, $pieces);
    }
}
if (!function_exists(__NAMESPACE__ . '\buildUrl')) {
    function buildUrl(array $pieces) {
        $glue = '/';
        $first = rtrim(array_shift($pieces), $glue);
        $rest = joinPath($pieces, $glue, $glue);
        return joinPath([$first, $rest], $glue, !startsWith($first, $glue) ? $glue : false);
    }
}
if (!function_exists(__NAMESPACE__ . '\validEmail')) {
    function validEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}
if (!function_exists(__NAMESPACE__ . '\inApp')) {
    function inApp() {
        return !collect(['Line', 'FBAV', 'micromessenger'])->every(function ($item) {
            return strpos(@$_SERVER['HTTP_USER_AGENT'], $item) === false;
        });
    }
}