<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 18.03.14
 * Time: 16:11
 * To change this template use File | Settings | File Templates.
 */

class ControllerImportImport extends Controller{

    /*
     * pokazuje listę żródeł importu, np: hurtowni
     */
    public function import_list()
    {
            // @todo implement
    }

    /*
     * automatyczna synchronizacja cen i stanów magazynowych
     */
    public function import_cron()
    {
        // @todo implement
    }

    /*
     * dodajemy nowy rodzaj importu ( hurtownie ) tu definiujemy jakie będa pola, co one vbeda oznaczać, jak czesto ładować
     * jka ma się tworzyć nazwa i opis , jakie narzuty na cene itp.
     */
    public function add_import()
    {

        $this->load->model('localisation/language');

        $this->data['languages'] = $this->model_localisation_language->getLanguages();


        // @todo implement
        $this->setFields(
          array(
              'name',
              'url',
              'interval',
              'price_mod_type',
              'price_mod',
              'import_fields',
              'description'
          ),
          array('import_fields' => array(
              0 => array(
                  'symbolic_name' => $this->language->load('text_id'),
                  'tag_name' => '',
                  'type' => 'ID',
              ),
              1 => array(
                  'symbolic_name' => $this->language->load('text_price'),
                  'tag_name' => '',
                  'type' => 'price',
              ),
              // główny obrazek
              2 => array(
                  'symbolic_name' => $this->language->load('text_image'),
                  'tag_name' => '',
                  'type' => 'main_image',
              ),


          ),

          ),
         'post'

        );

        $this->data['add_action'] = $this->url->link('import/import/add');

        $this->template = 'import/add.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    /*
     * wiadomo
     * @param int import_id
     */
    public function edit_import()
    {
        $this->load->model('import/import');

        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        if(!isset($this->request->get['import_id']))
        {
            trigger_error('brak id importu');
            $this->redirect($this->url->link('import/import/list','&token='.$this->session->data['token']));
        }

        // @todo walidacja
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
            $this->model_import_import->editImport($this->request->post,$this->request->get['import_id']);


            $this->session->data['success'] = $this->language->get('text_success');


            $this->redirect($this->url->link('import/import/get_buffer', '&import_id='.$this->request->get['import_id'].'&token=' . $this->session->data['token'] , 'SSL'));
        }

        $import = $this->model_import_import->getImport($this->request->get['import_id'],true);


        $this->data['import'] = $import;

        $this->data['action'] = $this->url->link('import/import/edit_import','&import_id='.$this->request->get['import_id'].'&token='.$this->session->data['token']);

        $this->template = 'import/edit.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    /*
     * tu pokazujemy bufor dla danego importu ( nowo zaciągnięte produkty )
     * @param int import_id
     */
    public function get_buffer()
    {
        $this->load->model('catalog/product');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data = array(
            'import_id' => $this->request->get['import_id'],
            'start'           => ($page - 1) * $this->config->get('config_admin_limit'),
            'limit'           => $this->config->get('config_admin_limit'),

        );


        $this->load->model('tool/image');

        $product_total = $this->model_catalog_product->getBufferTotalProducts($data);



        $results = $this->model_catalog_product->getBufferProducts($data);



        $this->load->model('tool/image');

        foreach($results as $key => $result)
        {
            $results[$key]['image_preview'] = false;
            if($result['image'])
            {
                $results[$key]['image_preview'] = $this->model_tool_image->resize($result['image'], 100, 100);

            }

            $results[$key]['product_attributes'] = $this->model_catalog_product->getProductAttributes($result['product_id'],true);
            $results[$key]['product_description'] = $this->model_catalog_product->getProductDescriptions($result['product_id']);
        }

        $this->data['buffer_products'] = $results;

        $this->data['save_buffer'] = $this->url->link('import/import/save_buffer','&import_id='.$this->request->get['import_id'].'&token='.$this->session->data['token']);

        $this->data['edit_import'] = $this->url->link('import/import/edit_import','&import_id='.$this->request->get['import_id'].'&token='.$this->session->data['token']);

        $this->data['force_buffer'] = $this->url->link('import/import/force_buffer','&import_id='.$this->request->get['import_id'].'&token='.$this->session->data['token']);

        error_reporting(E_ALL);
ini_set('display_errors', '1');

        $this->template = 'import/get_buffer.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());

    }

    /*
     * wymuszamy sciagniece najnowyszch produktów z tego importu teraz
     * @param int import_id
     */
    public function force_buffer()
    {

        $this->load->model('import/import');

        error_reporting(E_ALL);
        ini_set('display_errors', '1');

        $import_id = $this->request->get['import_id'];

        $this->model_import_import->getImportData($import_id);



        $this->redirect($this->url->link('import/import/get_buffer','&import_id='.$import_id.'&token='.$this->session->data['token']));
    }

    /*
     * zapisujmey wybrane produkty z buforu do bazy danych
     */
    public function save_buffer()
    {


        if(isset($this->request->post['selected']))
        {
            $selected = $this->request->post['selected'];
        }
        else{
            $selected = array();
        }

        $products = $this->request->post['product'];

        foreach($products as $product_id => $product)
        {
            if(!in_array($product_id,$selected))
            {
                unset($products[$product_id]);
            }
        }

        $added = array();
        $update = array();
        $fail = array();

        $this->load->model('import/import');
        $this->load->model('catalog/category');
        $this->load->model('catalog/product');
        $this->load->model('catalog/manufacturer');
        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        $import = $this->model_import_import->getImport($this->request->get['import_id']);

        if(!$import)
        {
            trigger_error('nie można odlnależć importu id: '.$this->request->get['import_id']);
            $this->session->data['error'] = $this->language->get('text_no_import');
            $this->redirect('import/import/list');
        }

        foreach($products as $product)
        {
            // rozbijamy kategorie i znajdujemy ich id, jak nie to tworyzmy nowe
            $categories = array();

            if($import['category_separator'])
            {


                $category_names = explode(htmlspecialchars_decode($import['category_separator']),htmlspecialchars_decode($product['category_name']));


                $parent_id = 0;

                foreach($category_names as $category_name)
                {
                    $search_data = array(
                        'filter_name' => $category_name,
                    );

                    $category = $this->model_catalog_category->getCategories($search_data);

                    $category_id = false;

                    if(!empty($category))
                    {
                        $category_id = $category[0]['category_id'];
                    }
                    else{

                        $desc = array();

                        foreach($languages as $language)
                        {
                            $desc[$language['language_id']] = array(
                               'name' => $category_name,
                                'meta_keyword' => '',
                                'meta_description' => '',
                                'description' => $category_name,
                            );
                        }

                        $insert_data = array(
                            'top' => 0,
                            'column' => 0,
                            'sort_order' => 0,
                            'status' => 1,
                            'virtual' => 0,
                            'category_description' => $desc,
                            'keyword' => false,
                            'parent_id' => $parent_id,
                            'category_store' => array(0),
                        );

                        $category_id = $this->model_catalog_category->addCategory($insert_data);
                    }

                    if($category_id)
                    {
                        $parent_id = $category_id;
                        $categories[] = $category_id;
                    }
                }
            }
            else
            {
                $category_name = htmlspecialchars_decode($product['category_name']);

                $search_data = array(
                    'filter_name' => $category_name,
                );

                $category = $this->model_catalog_category->getCategories($search_data);

                $category_id = false;

                if(!empty($category))
                {
                    $category_id = $category[0]['category_id'];
                }
                else
                {
                    $desc = array();

                    foreach($languages as $language)
                    {
                        $desc[$language['language_id']] = array(
                            'name' => $category_name,
                            'meta_keyword' => '',
                            'meta_description' => '',
                            'description' => $category_name,
                        );
                    }

                    $insert_data = array(
                        'top' => 0,
                        'column' => 0,
                        'sort_order' => 0,
                        'status' => 0,
                        'virtual' => 0,
                        'category_description' => $desc,
                        'keyword' => false,
                        'parent_id' => 0,
                        'category_store' => array(0),
                    );

                    $category_id = $this->model_catalog_category->addCategory($insert_data);
                }



                $categories[] = $category_id;
            }

            // znajdujemy id producentów
            $manufacturer_id = 0;
            if($product['manufacturer_name'])
            {
                $search_data = array(
                    'name' => $product['manufacturer_name']
                );

                $manufacturer = $this->model_catalog_manufacturer->getManufacturers($search_data);

                if(!empty($manufacturer))
                {
                    $manufacturer_id = $manufacturer[0]['manufacturer_id'];

                }
                else
                {
                    $insert_data = array(
                        'name' => $product['manufacturer_name'],
                        'sort_order' => 0,
                        'follow' => 0,
                    );

                    $manufacturer_id = $this->model_catalog_manufacturer->addManufacturer($insert_data);
                }
            }



            // zapisać dziada
            $data = array(
                'product_category' => $categories,
                'model' => $product['model'],
                'status' => 1,
                'product_description' => $product['product_description'],

                'sku'    =>  NULL,
                'upc'    =>  NULL,
                'location'    =>  NULL,
                'quantity'    =>  999,
                'image'    =>  $product['image'],
                // @todo manufacturer
                'manufacturer_id'    =>  $manufacturer_id,
                'price'    =>  $product['price'],
                'points'    =>  NULL,
                'ean' =>  $product['ean'],
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
                'weight' => $product['weight'],
                'weight_class_id' => 0,
                'width' => 0,
                'length_class_id' => 0,
                'tax_class_id' => 0,
                'product_tag' => 0,
                'keyword' => false,
                'google_merchant' => false,
                'product_store' => array(0),
                'import_id' => $this->request->get['import_id'],
                'product_attribute' => $product['product_attribute'],

                'original_price' => $product['original_price'],
                'original_quantity' => $product['original_quantity'],

            );

            // sprawdzamy po modelu czy istnieje, tak: update, nie: nowy
            $search_data = array(
                'filter_model_strict' => $product['model']

            );

            $products = $this->model_catalog_product->getProducts($search_data);



            if(!empty($products))
            {
                $product_id = $products[0]['product_id'];

                $res = $this->model_catalog_product->editProduct($product_id,$data);

                if($res)
                {
                    $update[] = $product['model'];
                    // usuwamy z bufora
                    $this->model_catalog_product->deleteBufferProduct($product['product_id']);

                }
                else
                {
                    $fail[] = $product['model'];
                }

            }
            else
            {
                $product_id = $this->model_catalog_product->addProduct($data);

                if($product_id)
                {
                    $added[] = $product['model'];
                    // usuwamy z bufora
                    $this->model_catalog_product->deleteBufferProduct($product['product_id']);
                }
                else
                {
                    $fail[] = $product['model'];
                }
            }

            $this->session->data['import_result'] = array(
                'added' => count($added),
                'updated' => count($update),
                'fail' => count($fail),
            );

            $this->redirect($this->url->link('import/import/get_buffer','&import_id='.$this->request->get['import_id'].'&token='.$this->session->data['token']));

        }
    }

    /*
     * @param int import_id
     */
    public function delete_import()
    {
        // @todo implement
    }

}