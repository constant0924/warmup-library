<?php
namespace library\extension\video;
use \library\helper\Helper;
use \library\extension\video\Youku;
class VideoService{

    /**
     * 腾讯视频播放方式
     * @param $host 解释url后的域名
     * @param $query 解释url后的参数
     * @return string
     */
    public function TencenPlayWay($host,$query){
        if($host != 'v.qq.com' || empty($query)){
            return false;
        }
        $url = 'http://vv.video.qq.com/geturl?vid='.$query.'&otype=xml&platform=1&ran=0%2E9652906153351068';
        $xml = file_get_contents($url);
        $data = simplexml_load_string($xml);

        $res = Helper::cutstr_html($data->vd->vi->url->asXML(),999);
        $html = '<video autobuffer src="'.$res.'" controls="controls">';

        return $html;
    }

    /**
     * 优酷视频播放方式(此方法是通过youku算法获取出mp4地址，用html5形式播放)
     * @param $url 播放地址，
     * @param $host 域名
     * @return string
     */
    public function youkuPlayWay($url,$host){
        $data = Youku::parse($url);
        if(empty($data) || $data === false){
            return false;
        }

        $html = '<video autobuffer src="'.$data['high']['0'].'" controls="controls">';
        return $html;
    }

    /**
     * 优酷视频播放方式(通过url地址组合id到iframe播放)
     * @param $url 播放地址，
     * @param $host 域名
     * @return string
     */
    public function iframeYoukuPlayWay($parse_url = []){
        if(empty($parse_url) || !is_array($parse_url)){
            return false;
        }
        if(empty($parse_url['path'])){
            return false;
        }
        $path = explode('/',$parse_url['path']);
        $id = trim(str_replace(array('id', '_','.html'), "", $path[2]));
        $html = '<iframe src="http://player.youku.com/embed/'.$id.'" frameborder=0 allowfullscreen></iframe>';
        return $html;
    }

    /**
     * 解析视频地址
     * @param $url
     * @return null|string
     */
    public function parse($url){
        $res = null;
        $parse = parse_url($url);
        if(!empty($parse) && is_array($parse)){
            switch($parse['host']){
                case 'v.qq.com' :
                    $arr = explode('=',$parse['query']);
                    $res = $this->TencenPlayWay($parse['host'],$arr[1]);
                    break;
                case 'v.youku.com':
                    $res = $this->iframeYoukuPlayWay($parse);
                    break;
                default:
                    $res = '<p style="color: #FFF; text-align: center">暂不支持该视频播放</p>';
                    break;
            }
        }

        return $res;
    }

}