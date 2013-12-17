<?php
/**
 * Created by JetBrains PhpStorm.
 * User: USER
 * Date: 20.09.13
 * Time: 15:25
 * To change this template use File | Settings | File Templates.
 */

include_once(DIR_APPLICATION."controller/common/oneslashseo.php");

class OneSlashSeoTest extends PHPUnit_Framework_TestCase {

    private $_register;

    public function setUp()
    {
        $this->_register =  new Registry();


    }

    /**
     * @dataProvider provider
     */
    public function testRewrite($link,$query)
    {


        $config = $this->getMock('config',array('get'));

        $config->expects($this->any())
            ->method('get')

            ->will($this->returnValue('pl'));

        $db = $this->getMock('db',array('query','escape'));

        $db->expects($this->any())
            ->method('escape')

            ->will($this->returnArgument(0));

        foreach($query as $key => $alias)
        {
            $db->expects($this->at($key))
                ->method('query')

                ->will($this->returnValue($alias));
        }


        $this->_register->set('db',$db);
        $this->_register->set('config',$config);


         $controllerOneSlashSeo  = new ControllerCommonOneSlashSeo($this->_register,'testing');

        $controllerOneSlashSeo->rewrite($link);
    }

    public function provider()
    {
        return array(
            0 => array(

                'link' => 'http://demo.stronazen.pl/nowy/index.php?route=product/product&amp;product_id=1',
                'seo_alias' => array(
                   0 => (object) array(
                    'id' => 1,
                    'num_rows'=> 1,
                    'row' => array(
                        'keyword' => 'produkcik',
                    )
                ),
                ),
            )
        );
    }

}