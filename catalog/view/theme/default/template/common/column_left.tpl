
<div id="column-left">

<?php if ($modules) { ?>
  <?php foreach ($modules as $module) { ?>
  <?php echo $module; ?>
  <?php } ?>
<?php } ?>


<script type="text/javascript">

        $('select').each(function(){
            var title = $(this).attr('title');
            if( $('option:selected', this).val() != ''  ) title = $('option:selected',this).text();
            $(this)
                .css({'z-index':10,'opacity':0,'-khtml-appearance':'none'})
                .after('<span class="select">' + title + '</span>')
                .change(function(){
                    val = $('option:selected',this).text();
                    $(this).next().text(val);
					$(this).attr('title', val);
					if(val.length > 24) { $(this).next().text(val.substring(0,25)+"...");  }
                    })
        });

	$(document).ready(function(){
		$('select').each(function(){
			val = $('option:selected',this).text();
            $(this).next().text(val);
			$(this).attr('title', val);
			if(val.length > 24) { $(this).next().text(val.substring(0,25)+"...");  }
		});	
	});
		
</script>

</div>

