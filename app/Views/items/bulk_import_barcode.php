<ul id="error_message_box" class="error_message_box"></ul>

<?= form_open('items/bulkImportBarcode', ['id' => 'bulk_import_barcode_form', 'class' => 'form-horizontal']) ?>
    <fieldset id="item_basic_info">

        <div class="form-group form-group-sm">
            <div class="col-xs-12">
                <p><?= lang('Items.bulk_import_barcode_help') ?></p>
            </div>
        </div>

        <div class="form-group form-group-sm">
            <?= form_label(lang('Items.bulk_import_barcode_codes'), 'codes', ['class' => 'required control-label col-xs-3']) ?>
            <div class="col-xs-8">
                <?= form_textarea([
                    'name'        => 'codes',
                    'id'          => 'codes',
                    'class'       => 'form-control input-sm',
                    'rows'        => 10,
                    'placeholder' => "7790895000860\n7790070419230\n7790040012347"
                ]) ?>
            </div>
        </div>

    </fieldset>
<?= form_close() ?>

<script type="text/javascript">
    // Validation and submit handling
    $(document).ready(function() {
        $('#bulk_import_barcode_form').validate($.extend({
            submitHandler: function(form) {
                $(form).ajaxSubmit({
                    success: function(response) {
                        dialog_support.hide();
                        table_support.handle_submit('<?= esc('items') ?>', response);
                    },
                    dataType: 'json'
                });
            },

            errorLabelContainer: '#error_message_box',

            rules: {
                codes: 'required'
            },

            messages: {
                codes: "<?= lang('Items.bulk_import_barcode_required') ?>"
            }
        }, form_support.error));
    });
</script>
