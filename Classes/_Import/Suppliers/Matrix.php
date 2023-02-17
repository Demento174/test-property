<?php
namespace Controllers\Import\Suppliers;



use Controllers\PostsAndTax\PostProduct;
use Controllers\PostsAndTax\TaxCat;
use \Controllers\XML\XmlStringStreamerWrapper;
use \Prewk\XmlStringStreamer\Stream\File;
use function YoastSEO_Vendor\GuzzleHttp\Psr7\str;

class Matrix
{
    static string $_file = 'https://instrument.ru/yandexmarket/1b78da37-0b26-45a6-a885-095183509075.xml';
    static string $_description_category = 'Импортированная категория (API Matrix)';

    static array $_attributes  =
        [
            'url'=>'Ссылка на страницу товара у поставщика',
            'vendor'=>'Бренд',
            'manufacturer_warranty'=>'Гарантия от производителя',
            'barcode'=>'Штрихкод',
            'country_of_origin'=>'Страна производитель'
        ];


    private string|null $file   = null;
    private array|null $options = null;


    public function __construct(null|string $file = null,array|null $options=null)
    {
        $this->file     = $this->set_file($file);
        $this->options  = $options;
    }

    private function set_file(null|string $file = null):string
    {
        return null===$file?self::$_file:$file;
    }

    private function set_attributes($node):?object
    {
        return $node->attributes();
    }

    private function set_category_id(object $attributes):?string
    {
        return (int) $attributes['id'][0];
    }

    private function set_category_parentId(object $attributes):?string
    {
        return  null === $attributes['parentId'][0]?null:(int)$attributes['parentId'][0];
    }

    private function set_category_title($node):string
    {
        return (string) $node[0];
    }



    private function set_product_title($node):string
    {
        return $node->name;
    }

    private function set_product_article($node):string
    {
        return $node->adult;
    }

    private function set_product_price($node):float
    {
        return (float) $node->price;
    }

    private function set_product_weight($node):float
    {
        return  (float) $node->weight;
    }

    private function set_product_attributes($node)
    {
        $result = [];
        $result[self::$_attributes['url']]                      = (string) $node->url;
        $result[self::$_attributes['vendor']]                   = (string) $node->vendor;
        $result[self::$_attributes['manufacturer_warranty']]    = "true" === (string) $node->manufacturer_warranty?true:false;
        $result[self::$_attributes['barcode']]                  = (string) $node->barcode;
        $result[self::$_attributes['country_of_origin']]        = (string) $node->country_of_origin;
        return $result;
    }

    private function set_product_category($node):?TaxCat
    {
        if(""=== (string) $node->categoryId || !$tax = TaxCat::get_by_article_matrix((int) $node->categoryId) )
            return null;
        return $tax;
    }

    private function create_category(int $article,int|null $parentID=null,string|null $title=null):TaxCat
    {
        $title = null===$title?(string) $article:$title;
        $parent =null === $parentID || "0"===$this->options['category_update_parent']?
            null:
            TaxCat::get_by_article_matrix($parentID)->get_id();

        return TaxCat::create($title,$article,$parent,null,self::$_description_category);
    }

    private function update_category($article,int|null $parentID=null,string|null $title=null):TaxCat
    {

        $taxonomy = TaxCat::get_by_article_matrix($article);

        $parent = null === $parentID || "0"===$this->options['category_update_parent']?
            null:
            (
                TaxCat::existence_category_by_article_matrix($parentID)?
                    TaxCat::get_by_article_matrix($parentID)->get_id():
                    $this->create_category($parentID)
            );
        $title = "0"===$this->options['category_update_title']
            ?null:
            $title;
        $taxonomy->update($title,null,null,$parent);
        return $taxonomy;
    }


    public function handler_categories(bool $debug_mode=false,$count_rows_debug_mode=2)
    {

        return \Controllers\XML\XmlStringStreamerWrapper::init_parse($this->file,
            function ($node) use ($debug_mode,$count_rows_debug_mode)
        {

            $i = 0;
            $response = [];
            foreach ($node->categories->category as $category)
            {

                $id = $this->set_category_id($this->set_attributes($category));
                $parentId = $this->set_category_parentId($this->set_attributes($category));
                $title = $this->set_category_title($category);

                $response[$i] =
                    [
                        'id'=>$id,
                        'parent'=>$parentId,
                        'title'=>$title
                    ];

                if(TaxCat::existence_category_by_article_matrix($id))
                {
                    $this->update_category($id,$parentId,$title);
                    $response[$i]['action']='update';
                }else
                    {
                        $this->create_category($id,$parentId,$title);
                        $response[$i]['action']='create';
                    }

                if($debug_mode&&$i>=$count_rows_debug_mode)
                    break;
                $i++;

            }
            echo json_encode($response);

        });
    }

    public function handler_products(bool $debug_mode=false,$count_rows_debug_mode=2)
    {
        return \Controllers\XML\XmlStringStreamerWrapper::init_parse($this->file,
            function ($node) use ($debug_mode,$count_rows_debug_mode)
        {

            $i = 0;
            $response = [];

            foreach ($node->offers->offer as $offer)
            {

                $title      = $this->set_product_title($offer);
                $article    = $this->set_product_article($offer);
                $price      = $this->set_product_price($offer);
                $weight     = $this->set_product_weight($offer);
                $attributes = $this->set_product_attributes($offer);
                $category   = $this->set_product_category($offer);

                $response[$i] =
                    [
                        'title'      =>$title ,
                        'article'    =>$article ,
                        'price'      =>$price ,
                        'weight'     =>$weight ,
                        'attributes' =>$attributes ,
                        'category'   =>$category ,
                    ];

                if(\Controllers\PostsAndTax\PostProduct::existence_product_by_article_matrix($article))
                {
                    $product = PostProduct::get_product_by_article_matrix($article);

                    $product->update(
                        $title,
                        $article,
                        $price,
                        null,
                        $weight,
                        $attributes,
                        [\Controllers\PostsAndTax\PostProduct::$selectors['articles']['matrix']=>$article]
                    );
                    $response[$i]['action'] = 'Update';
                }else
                    {
                        $product = PostProduct::create(
                            $title,
                            $article,
                            $price,
                            null,
                            $weight,
                            $attributes,
                            [\Controllers\PostsAndTax\PostProduct::$selectors['articles']['matrix']=>$article]);
                        $response[$i]['action'] = 'create';
                    }
                $product->update_term($category->get_id());

                if($debug_mode&&$i>=$count_rows_debug_mode)
                    break;
                $i++;

            }

            echo json_encode($response);

        });

    }

    public function handler_products_images(bool $debug_mode=false,$count_rows_debug_mode=2)
    {
        return \Controllers\XML\XmlStringStreamerWrapper::init_parse($this->file,
            function ($node) use ($debug_mode,$count_rows_debug_mode)
            {

                $i = 0;
                $response = [];

                foreach ($node->offers->offer as $offer)
                {
                    if('' === (string) $offer->picture)
                        continue;

                    $article = $this->set_product_article($offer);
                    $product = PostProduct::get_product_by_article_matrix($article);
                    $image = (string) $offer->picture;
                    if(null === $product || null === $image)
                        continue;

                    if(null ===  $product->attachment || false === $product->attachment->compare_image_sizes($image))
                    {
                        $attachment = $product->update_attachment($image);

                        $response[$i]['url']=$attachment->get_link();
                        $response[$i]['product']=$product->get_title();
                        $response[$i]['attachment']=$product->attachment;
                        $response[$i]['compare']=$product->attachment->get_filesize();
                    }

                    if($debug_mode&&$i>=$count_rows_debug_mode)
                        break;
                    $i++;

                }

//                echo json_encode($response);
                echo  json_encode($response);

            });

    }


    public static function render()
    {

        renderBlock('admin/import/default');
    }

    public static function init()
    {
        self::render();
    }
}