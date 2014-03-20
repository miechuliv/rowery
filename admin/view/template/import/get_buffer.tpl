<?php echo $header; ?>
        <div class='container'>
    <div class='message' >
        <?php if(isset($this->session->data['import_result'])){ ?>
        <span><?php echo $this->language->get('text_added').' '.$this->session->data['import_result']['added']; ?> </span><br/>
        <span><?php echo $this->language->get('text_updated').' '.$this->session->data['import_result']['updated']; ?> </span><br/>
        <span><?php echo $this->language->get('text_fail').' '.$this->session->data['import_result']['fail']; ?> </span>
        <?php unset($this->session->data['import_result']); ?>
        <?php } ?>

        <?php if(isset($this->session->data['success'])){ ?>
        <span><?php echo $this->session->data['success']; ?> </span><br/>

        <?php unset($this->session->data['success']); ?>
        <?php } ?>

    </div>
    <div class="buttons" >
        <a href="<?php echo $edit_import; ?>" class="button" ><?php echo $this->language->get('text_edit_import'); ?></a>
        <a href="<?php echo $force_buffer; ?>" class="button" ><?php echo $this->language->get('text_force_buffer'); ?></a>
    </div>
            <?php if($buffer_products){ ?>
                <div class="buttons" >
                    <a onclick="$('#master-form').submit();" class="button" ><?php echo $this->language->get('text_save_buffer'); ?></a>
                </div>

                <form id="master-form" action="<?php echo $save_buffer; ?>" method="post" >
                    <table class="list">
                        <tr>
                            <td>
                                <input onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" type="checkbox" id="master-check" />
                            </td>
                            <td>
                                <?php echo $this->language->get('text_model'); ?>
                            </td>
                            <td>
                                <?php echo $this->language->get('text_title'); ?>
                            </td>
                            <td>
                                <?php echo $this->language->get('text_description'); ?>
                            </td>
                            <td>
                                <?php echo $this->language->get('text_price'); ?>
                            </td>
                            <td>
                                <?php echo $this->language->get('text_quantity'); ?>
                            </td>
                            <td>
                                <?php echo $this->language->get('text_image'); ?>
                            </td>
                            <td>
                                <?php echo $this->language->get('text_manufacturer_name'); ?>
                            </td>
                            <td>
                                <?php echo $this->language->get('text_category_name'); ?>
                            </td>
                            <td>
                                <?php echo $this->language->get('text_ean'); ?>
                            </td>
                            <td>
                                <?php echo $this->language->get('text_weight'); ?>
                            </td>
                            <td>
                                <?php echo $this->language->get('text_attributes'); ?>
                            </td>
                        </tr>
                        <!-- @todo filtrowanie i sortowanie -->

                        <?php foreach($buffer_products as $product){ ?>
                        <tr>
                            <td>
                                <input type="checkbox" value="<?php echo $product['product_id']; ?>" name="selected[]" />
                            </td>
                            <td>
                                <input type="hidden" value="<?php echo $product['product_id']; ?>" name="product[<?php echo $product['product_id']; ?>][product_id]" />
                                <input type="text" value="<?php echo $product['model']; ?>" name="product[<?php echo $product['product_id']; ?>][model]" />
                            </td>
                            <td>
                                <?php foreach($product['product_description'] as $key => $description){ ?>
                                    <input type="text" name="product[<?php echo $product['product_id']; ?>][product_description][<?php echo $key; ?>][name]"
                                           value="<?php echo $description['name']; ?>" />
                                <?php } ?>
                            </td>

                            <td>
                                <?php foreach($product['product_description'] as $key => $description){ ?>
                                <textarea name="product[<?php echo $product['product_id']; ?>][product_description][<?php echo $key; ?>][description]"
                                        ><?php echo $description['description']; ?></textarea>
                                <input type="hidden" value="" name="product[<?php echo $product['product_id']; ?>][product_description][<?php echo $key; ?>][meta_keyword]" />
                                <input type="hidden" value="" name="product[<?php echo $product['product_id']; ?>][product_description][<?php echo $key; ?>][meta_description]" />
                                <input type="hidden" value="" name="product[<?php echo $product['product_id']; ?>][product_description][<?php echo $key; ?>][tag]" />
                                <?php } ?>
                            </td>
                            <td>
                                <input type="text" name="product[<?php echo $product['product_id']; ?>][price]" value="<?php echo $product['price']; ?>" />
                                <input type="hidden" name="product[<?php echo $product['product_id']; ?>][original_price]" value="<?php echo $product['original_price']; ?>" />
                            </td>

                            <td>
                                <input type="text" name="product[<?php echo $product['product_id']; ?>][quantity]" value="<?php echo $product['quantity']; ?>" />
                                <input type="hidden" name="product[<?php echo $product['product_id']; ?>][original_quantity]" value="<?php echo $product['original_quantity']; ?>" />
                            </td>

                            <td>
                                <input type="hidden" name="product[<?php echo $product['product_id']; ?>][image]" value="<?php echo $product['image']; ?>" />
                                <img src="<?php echo $product['image_preview']; ?>" />
                            </td>

                            <td>
                                <input type="text" name="product[<?php echo $product['product_id']; ?>][manufacturer_name]" value="<?php echo $product['manufacturer_name']; ?>" />
                            </td>
                            <td>
                                <input type="text" name="product[<?php echo $product['product_id']; ?>][category_name]" value="<?php echo $product['category_name']; ?>" />
                            </td>

                            <td>
                                <input type="text" name="product[<?php echo $product['product_id']; ?>][ean]" value="<?php echo $product['ean']; ?>" />
                            </td>
                            <td>
                                <input type="text" name="product[<?php echo $product['product_id']; ?>][weight]" value="<?php echo $product['weight']; ?>" />
                            </td>

                            <td>
                                <?php $atr_row = 0; ?>
                                <?php foreach($product['product_attributes'] as $attribute){ ?>
                                    <input type="hidden" name="product[<?php echo $product['product_id']; ?>][product_attribute][<?php echo $atr_row; ?>][attribute_id]"
                                           value="<?php echo $attribute['attribute_id']; ?>" />
                                    <?php foreach($attribute['product_attribute_description'] as $key => $description){ ?>
                                <input type="hidden" name="product[<?php echo $product['product_id']; ?>][product_attribute][<?php echo $atr_row; ?>][product_attribute_description][<?php echo $key; ?>][text]"
                                       value="<?php echo $description['text']; ?>" />


                                    <?php } ?>
                                    <?php $desc = array_shift($attribute['product_attribute_description']); ?>
                                    <span><?php echo $attribute['name'].': '.$desc['text']; ?></span><br/>
                                <?php $atr_row++; ?>
                                <?php } ?>
                            </td>

                        </tr>

                        <?php } ?>
                    </table>
                </form>
            <?php }else{ ?>
                <?php echo $this->language->get('text_no_result'); ?>
            <?php } ?>
        </div>

<?php echo $footer; ?>