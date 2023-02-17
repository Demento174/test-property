<?php
namespace Controllers\XML;

use Prewk\XmlStringStreamer;
use Prewk\XmlStringStreamer\Stream;
use Prewk\XmlStringStreamer\Parser;


/**
 * https://github.com/prewk/xml-string-streamer
 **/
class XmlStringStreamerWrapper extends XmlParent implements XmlInterface
{
    public function __construct(string $file)
    {
        $this->file = $this->set_file($file);
        $this->controller = $this->set_controller($file);
    }

    protected  function set_file(string $file)
    {
        if(!$this->check_file_exist_by_url($file))
        {
            throw new \Exception(self::ERROS['file_not_found']);
        }

        return $file;
    }

    /**
     * @param $file
     * @return mixed
     * return streamer
     */
    protected  function set_controller($file)
    {
        return \Prewk\XmlStringStreamer::createStringWalkerParser( $file );
    }

    public function get_streamer()
    {
        return $this->controller;
    }

    public  function get_streamer_for_unique_node($node)
    {

        return \Prewk\XmlStringStreamer::createUniqueNodeParser($this->file, array("uniqueNode" => $node));
    }

    function parse($streamer,$callback=null): ?array
    {


        while ($node = $streamer->getNode())
        {

            // $node will be a string like this:
            // "<customer><firstName>Jane</firstName><lastName>Doe</lastName></customer>"
            $simpleXmlNode = simplexml_load_string($node);

            if(null !== $callback)
            {
                $callback($simpleXmlNode);
            }
            $result[]= $simpleXmlNode;

        }

        return  $result;
    }

    public static function init_parse($file,$callback = null): ?array
    {

        // TODO: Implement init_parse() method.

        $class = new self($file);

        $streamer = $class->get_streamer();

        return  $class->parse($streamer,$callback);
    }

    /**
     * DONT WORK FUCKING LIBRARY
     * @param string $file
     * @param string $nodeName
     * @param $callback
     * @return array|null
     */

    public static function init_unique_node(string $file,string $nodeName,$callback = null):?array
    {


        // TODO: Implement init_unique_node() method.
        $class = new self($file);

        $streamer = $class->get_streamer_for_unique_node($nodeName);
        return $class->parse($streamer,$callback);

    }


}