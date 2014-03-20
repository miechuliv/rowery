<?php echo $header; ?>

        <div class="content" >
    <div class="buttons">
        <a onclick="$('#master-form').submit();" class="button" ><?php echo $this->language->get('text_save_import'); ?></a>
    </div>
            <form id="master-form" action="<?php echo $action; ?>" method="post" >
                <table class="form" >
                    <tr>
                        <td><?php echo $this->language->get('entry_name'); ?></td>
                        <td><input type="text" name="name" value="<?php echo $import['name']; ?>" /></td>
                    </tr>

                    <tr>
                        <td><?php echo $this->language->get('entry_url'); ?></td>
                        <td><input type="text" name="url" value="<?php echo $import['url']; ?>" /></td>
                    </tr>

                    <tr>
                        <td><?php echo $this->language->get('entry_interval'); ?></td>
                        <td><input type="text" name="interval" value="<?php echo $import['interval']; ?>" /></td>
                    </tr>

                    <tr>
                        <td><?php echo $this->language->get('entry_stock_type'); ?></td>
                        <td><select name="stock_type">
                                <option value="switch" <?php if($import['stock_type']=='percent'){ echo 'selected="selected"'; } ?> ><?php echo $this->language->get('text_switch'); ?></option>
                                <option value="numeric" <?php if($import['stock_type']=='flat'){ echo 'selected="selected"'; } ?> ><?php echo $this->language->get('text_numeric'); ?></option>
                            </select></td>
                    </tr>

                    <tr>
                        <td><?php echo $this->language->get('entry_price_mod_type'); ?></td>
                        <td><select name="price_mod_type">
                                <option value="percent" <?php if($import['price_mod_type']=='percent'){ echo 'selected="selected"'; } ?> ><?php echo $this->language->get('text_percentage'); ?></option>
                                <option value="flat" <?php if($import['price_mod_type']=='flat'){ echo 'selected="selected"'; } ?> ><?php echo $this->language->get('text_flat'); ?></option>
                        </select></td>
                    </tr>

                    <tr>
                        <td><?php echo $this->language->get('entry_price_mod'); ?></td>
                        <td><input type="text" name="price_mod" value="<?php echo $import['price_mod']; ?>" /></td>
                    </tr>

                    <tr>
                        <td><?php echo $this->language->get('entry_category_separator'); ?></td>
                        <td><input type="text" name="category_separator" value="<?php echo $import['category_separator']; ?>" /></td>
                    </tr>

                    <?php $description_row = 0; ?>
                    <?php foreach($import['description'] as $description){ ?>
                    <tr>
                        <td>
                            <?php echo $this->language->get('entry_title'); ?>
                        </td>
                        <td>


                            <textarea  name="description[<?php echo $description['language_id']; ?>][title_template]" ><?php echo $description['title_template']; ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <?php echo $this->language->get('entry_description'); ?>
                        </td>
                        <td>
                            <textarea  name="description[<?php echo $description['language_id']; ?>][description_template]" ><?php echo $description['description_template']; ?></textarea>
                        </td>
                    </tr>



                    <?php $description_row++; ?>
                    <?php } ?>

                    <?php $field_row = 0; ?>
                    <?php foreach($import['fields'] as $field){ ?>

                    <tr>
                        <td><?php echo $this->language->get('entry_attribute'); ?></td>
                        <td>
                            <input type="hidden" value="<?php echo $field['import_id']; ?>" name="fields[<?php echo $field_row; ?>][import_id]" />

                            <input type="text" value="<?php echo $field['symbolic_name']; ?>" name="fields[<?php echo $field_row; ?>][symbolic_name]" />
                            <input type="text" value="<?php echo $field['tag_name']; ?>" name="fields[<?php echo $field_row; ?>][tag_name]" />

                            <select name="fields[<?php echo $field_row; ?>][type]" >
                                <option value="id" <?php if($field['type'] == 'id'){ echo 'selected="selected"'; } ?> ><?php echo $this->language->get('text_type_id'); ?></option>
                                <option value="category" <?php if($field['type'] == 'category'){ echo 'selected="selected"'; } ?> ><?php echo $this->language->get('text_type_category'); ?></option>
                                <option value="manufacturer" <?php if($field['type'] == 'manufacturer'){ echo 'selected="selected"'; } ?> ><?php echo $this->language->get('text_type_manufacturer'); ?></option>
                                <option value="main_image" <?php if($field['type'] == 'main_image'){ echo 'selected="selected"'; } ?> ><?php echo $this->language->get('text_type_main_image'); ?></option>

                                <option value="price" <?php if($field['type'] == 'price'){ echo 'selected="selected"'; } ?> ><?php echo $this->language->get('text_type_price'); ?></option>
                                <option value="attribute" <?php if($field['type'] == 'attribute'){ echo 'selected="selected"'; } ?> ><?php echo $this->language->get('text_type_attribute'); ?></option>
                                <option value="stock" <?php if($field['type'] == 'stock'){ echo 'selected="selected"'; } ?> ><?php echo $this->language->get('text_type_stock'); ?></option>
                            </select>
                        </td>
                    </tr>


                    <?php $field_row++; ?>

                    <?php } ?>

                </table>

                <?php // @todo dodawanie nowych pól ( fields ) i kasowanie istniejacych ?>

            </form>
        </div>
<?php echo $footer; ?>