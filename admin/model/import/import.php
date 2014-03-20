<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 19.03.14
 * Time: 10:24
 * To change this template use File | Settings | File Templates.
 */

class ModelImportImport extends Model{


    private function dowloadImage($data)
    {

        $ch = curl_init();

        curl_setopt ($ch, CURLOPT_URL, $data);

        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);

        $fileContents = curl_exec($ch);

        curl_close($ch);

        if($fileContents){
            $newImg = imagecreatefromstring($fileContents);

            $tmp = explode('/',$data);



            $newshort="data/".array_pop($tmp);
            $newname=DIR_IMAGE.$newshort;

            // to sa png wszystko
            imagejpeg($newImg,$newname ,100);

            return $newshort;
        }else{
            return 'no_image.jpg';
        }



    }
    /*
     * ściąga dane z podanego url do bufora
     */
    public function getImportData($import_id)
    {
        $import = $this->getImport($import_id);



        if(isset($import['url']))
        {
            $xml = simplexml_load_file($import['url']);

            if(!$xml)
            {
                trigger_error('Nie udało się zaciągnać xml z url:'.$import['url'].', import_id: '.$import_id.' ');
                return false;
            }

            if(empty($import['fields']))
            {
                trigger_error('Brak pól do zaciągnięcia url:'.$import['url'].', import_id: '.$import_id.' ');
                return false;
            }


            if(!isset($import['fields']['id']))
            {
                trigger_error('Brak pola wskazującego na id url:'.$import['url'].', import_id: '.$import_id.' ');
                return false;
            }

            if(empty($import['description']))
            {
                trigger_error('Brak konfiguracji opisu url:'.$import['url'].', import_id: '.$import_id.' ');
                return false;
            }

            $this->load->model('catalog/attribute');

            $this->load->model('catalog/product');

            $this->load->model('localisation/language');

            $languages = $this->model_localisation_language->getLanguages();

            // czyścimy buffer
            $ids = $this->db->query("SELECT * FROM ".DB_PREFIX."product_buffer WHERE import_id = '".(int)$import_id."' ");

            foreach($ids->rows as $id)
            {
                $this->model_catalog_product->deleteBufferProduct($id['product_id']);
            }

            foreach($xml->channel->item as $item)
            {
                $dom = dom_import_simplexml($item);

                // jakie pola szukamy
                // id
                $id = $import['fields']['id'];

                $find = $dom->getElementsByTagName($id['tag_name']);

                $id = $find->item(0);


                if(!$id)
                {
                    trigger_error('Nie udało się odzyskać id produktu url:'.$import['url'].', import_id: '.$import_id.' ');
                    continue;
                }
                else
                {
                    // po tym będziemy identyfikować produkt ( nie po product_id bo to wew. opencarta jest )
                    $product_model = $id->nodeValue;
                }


                // kategoria
                $category_name = $this->getField($dom,$import['fields'],'category');
                $manufacturer_name = $this->getField($dom,$import['fields'],'manufacturer');
                // @todo nałożyć modyfikator cenowy
                $price = $this->getField($dom,$import['fields'],'price');
                $original_price = $price;

                if($import['price_mod_type'] == 'percent')
                {
                        $price = $price*(1+($import['price_mod']/100));
                }
                elseif($import['price_mod_type'] == 'flat')
                {
                        $price += $import['price_mod'];
                }

                $ean = $this->getField($dom,$import['fields'],'ean');
                $stock = $this->getField($dom,$import['fields'],'stock');



                if($import['stock_type'] == 'switch')
                {
                    if($stock)
                    {
                        $product_quantity = 10;
                    }
                    else
                    {
                        $product_quantity = 0;
                    }
                }
                else
                {
                    $product_quantity = $stock;
                }
               // $gtin = $this->getField($dom,$import['fields'],'gtin');

                $image = $this->getField($dom,$import['fields'],'main_image');

                $image = $this->dowloadImage($image);




                // @todo wage trzeba bedzie parsować pod kontem typu wagi ( np : kg g itp )
                $weight = $this->getField($dom,$import['fields'],'weight');

                // łapiemy wszystkie atrybuty
                $attributes = array();



                if(isset($import['fields']['attribute']) AND !empty($import['fields']['attribute']))
                {
                    foreach($import['fields']['attribute'] as $atr)
                    {
                        $value = $this->getTag($dom,$atr['tag_name']);

                        if($value)
                        {
                            // attribute_id i text
                            $search_data = array(
                                'filter_name' => $atr['symbolic_name']
                            );

                            $res = $this->model_catalog_attribute->getAttributes($search_data);

                            if(!empty($res))
                            {
                                $attribute_id = $res[0]['attribute_id'];
                            }
                            else
                            {
                                $desc = array();
                                foreach($languages as $language)
                                {
                                    $desc[$language['language_id']] = array(
                                        'name' => $atr['symbolic_name']
                                    );
                                }
                                $insert_data = array(
                                    'attribute_group_id' => 0,
                                    'sort_order' => 0,
                                    'attribute_description' => $desc,
                                );


                                $attribute_id = $this->model_catalog_attribute->addAttribute($insert_data);
                            }

                            $attr_desc = array();
                            foreach($languages as $language)
                            {
                                $attr_desc[$language['language_id']] = array(
                                    'text' => $value
                                );
                            }

                            $attributes[] = array(
                                'attribute_id' => $attribute_id,
                                'product_attribute_description' => $attr_desc,

                            );
                        }

                    }
                }

                // @todo zaimplementować opcje

                // dodatkowe obrazki

                // łapiemy wszystkie atrybuty
                $images = array();

                if(isset($import['fields']['image']) AND !empty($import['fields']['image']))
                {
                    foreach($import['fields']['image'] as $im)
                    {
                        $value = $this->getTag($dom,$im['tag_name']);

                        if($value)
                        {
                            // @todo obrazek trzeba ściągnć i zapisać
                            $images[] = array(
                                'name' => $im['symbolic_name'],
                                'value' => $value,
                            );
                        }

                    }
                }

                // łapiemy teraz opisy
                $product_description = array();



                foreach($import['description'] as $description)
                {
                    // tytuł
                    $tags_to_use = array();

                    preg_match_all('/<+([a-zA-Z]+)>/',htmlspecialchars_decode($description['title_template']),$tags_to_use);


                    $title = htmlspecialchars_decode($description['title_template']);

                    $tags_values = array();

                    if(!empty($tags_to_use) AND !empty($tags_to_use[1]))
                    {
                        foreach($tags_to_use[1] as $tag)
                        {
                            $tags_values[$tag] = $this->getTag($dom,$tag);
                        }
                    }


                    if(!empty($tags_values))
                    {
                        foreach($tags_values as $key => $tag)
                        {
                            if($tag)
                            {
                                $title = preg_replace('/<'.$key.'>/',$tag,$title);
                            }
                            else
                            {
                                $title = preg_replace('/<'.$key.'>/','',$title);
                            }

                        }
                    }

                    // opis
                    $tags_to_use = array();
                    preg_match_all('/<+([a-zA-Z]+)>/',htmlspecialchars_decode($description['description_template']),$tags_to_use);

                    $p_description = htmlspecialchars_decode($description['description_template']);

                    $tags_values = array();

                    if(!empty($tags_to_use) AND !empty($tags_to_use[1]))
                    {
                        foreach($tags_to_use[1] as $tag)
                        {
                            $tags_values[$tag] = $this->getTag($dom,$tag);
                        }
                    }


                    if(!empty($tags_values))
                    {
                        foreach($tags_values as $key => $tag)
                        {
                            if($tag)
                            {
                                $p_description = preg_replace('/<'.$key.'>/',$tag,$p_description);
                            }
                            else
                            {
                                $p_description = preg_replace('/<'.$key.'>/','',$p_description);
                            }

                        }
                    }

                    // short description @todo to implement

                    // format opencart
                    $product_description[$description['language_id']] = array(
                        'name' => $title,
                        'description' => $p_description,
                        // @todo to implement
                        'meta_description' => '',
                        'meta_keyword' => '',
                        'tag' => '',
                    );

                }


                $product = array(
                    'category_name' => $category_name,
                    'model' => $product_model,
                    'status' => 1,
                    'product_description' => $product_description,

                    'sku'    =>  NULL,
                    'upc'    =>  NULL,
                    'location'    =>  NULL,
                    'quantity'    =>  $product_quantity,
                    'image'    =>  $image,
                    // @todo manufacturer
                    'manufacturer_name'    =>  $manufacturer_name,
                    'price'    =>  $price,
                    'points'    =>  NULL,
                    'ean' =>  $ean,
                    'jan' =>  NULL,
                    'isbn' =>  NULL,
                    'mpn' =>  NULL,

                    'date_available'    =>  date('Y-m-d'),
                    'weight_class'    =>  NULL,
                    'length'    =>  NULL,
                    'height'    =>  NULL,
                    'height_class'    =>  NULL,

                    'minimum'    =>  NULL,
                    'sort_order' => 0,


                    'subtract' => 1,
                    'stock_status_id' => 0,
                    'shipping' => 1,
                    'weight' => $weight,
                    'weight_class_id' => 0,
                    'width' => 0,
                    'length_class_id' => 0,
                    'tax_class_id' => 0,
                    'product_tag' => 0,

                    'product_store' => array(0,1),
                    'import_id' => $import_id,
                    'product_attribute' => $attributes,

                    'original_price' => $original_price,
                    'original_quantity' => $product_quantity,

                );


                $this->model_catalog_product->addBufferProduct($product);

                break;

            }


            //@todo to implement
        }
    }

    private function getTag(DOMElement $dom, $tag_name)
    {
        $find = $dom->getElementsByTagName($tag_name);

        if(is_object($find->item(0)))
        {
            return $find->item(0)->nodeValue;
        }
        else
        {
            return false;
        }
    }

    private function getField($dom, $fields , $fields_name)
    {
        if(isset($fields[$fields_name]))
        {

            $find = $dom->getElementsByTagName($fields[$fields_name]['tag_name']);

            $obj = $find->item(0);

            if(is_object($obj))
            {
                return $obj->nodeValue;
            }
            else{
                return false;
            }
        }
    }


    public function addImport($data)
    {
        $sql = "INSERT INTO ".DB_PREFIX."import SET
        name = '".$this->db->escape($data['name'])."',
        url = '".$this->db->escape($data['url'])."',
        interval = '".(int)$data['interval']."',
        price_mod_type = '".$this->db->escape($data['price_mod_type'])."',
        price_mod = '".(int)$data['price_mod']."',
        stock_type = '".$this->db->escape($data['stock_type'])."'
        ";

        $import_id = $this->db->query($sql);

        $this->addImportDescription($data['description'],$import_id);

        $this->addImportFields($data['fields'],$import_id);


    }

    public function editImport($data,$import_id)
    {
        $sql = "UPDATE ".DB_PREFIX."import SET
        name = '".$this->db->escape($data['name'])."',
        url = '".$this->db->escape($data['url'])."',
        interval = '".(int)$data['interval']."',
        price_mod_type = '".$this->db->escape($data['price_mod_type'])."',
        price_mod = '".(int)$data['price_mod']."',
         stock_type = '".$this->db->escape($data['stock_type'])."'
         WHERE import_id = '".(int)$import_id."'
        ";

        $this->deleteImportDescription($import_id);

        $this->addImportDescription($data['description'],$import_id);

        $this->deleteImportFields($import_id);

        $this->addImportFields($data['fields'],$import_id);


    }

    public function deleteImportDescription($import_id)
    {
        $this->db->query("DELETE FROM ".DB_PREFIX."import_description WHERE import_id = '".(int)$import_id."' ");
    }

    public function deleteImportFields($import_id)
    {
        $this->db->query("DELETE FROM ".DB_PREFIX."import_field WHERE import_id = '".(int)$import_id."' ");
    }

    public function addImportDescription($data,$import_id)
    {
        foreach($data as $language_id => $value)
        {
            $sql = "INSERT INTO ".DB_PREFIX."import_description SET
        import_id = '".(int)$import_id."',
        language_id = '".(int)$language_id."',
        title_template = '".$this->db->escape($value['title_template'])."',
        description_template = '".$this->db->escape($value['description_template'])."'


        ";
            // short_description_template = '".$this->db->escape($value['short_description_template'])."'

         $this->db->query($sql);
        }

    }

    public function addImportFields($data,$import_id)
    {
        foreach($data as $field)
        {
            $sql = "INSERT INTO ".DB_PREFIX."import_field SET
        import_id = '".(int)$import_id."',

        symbolic_name = '".$this->db->escape($field['symbolic_name'])."',
        tag_name = '".$this->db->escape($field['tag_name'])."',
        type = '".$this->db->escape($field['type'])."'

        ";

            $this->db->query($sql);
        }
    }

    public function getImportFields($import_id,$flat = false)
    {
        $res = $this->db->query("SELECT * FROM ".DB_PREFIX."import_field WHERE import_id = '".(int)$import_id."' ");

        $fields = array();

        if($res->num_rows)
        {
            foreach($res->rows as $row)
            {
                if($flat)
                {

                        $fields[] = $row;

                }
                else
                {
                    if(in_array($row['type'],array('id','category','manufacturer','main_image','price','stock')))
                    {
                        $fields[$row['type']] = $row;
                    }
                    else
                    {
                        if(!isset($fields[$row['type']]))
                        {
                            $fields[$row['type']] = array();
                        }

                        $fields[$row['type']][] = $row;
                    }
                }


            }
        }



        return $fields;
    }

    public function getImportDescription($import_id)
    {
        $res = $this->db->query("SELECT * FROM ".DB_PREFIX."import_description WHERE import_id = '".(int)$import_id."' ");

        return $res->rows;
    }

    public function getImport($import_id,$flat = false)
    {
        $res = $this->db->query("SELECT * FROM ".DB_PREFIX."import WHERE import_id = '".(int)$import_id."' ");

        $import = array();

        if($res->num_rows)
        {
            $import = $res->row;

            $import['fields'] = $this->getImportFields($import_id,$flat);
            $import['description'] = $this->getImportDescription($import_id);
        }

        return $import;
    }

    public function getImports()
    {
          $res = $this->db->query("SELECT * FROM ".DB_PREFIX."import ");

          return $res->rows;



    }
}