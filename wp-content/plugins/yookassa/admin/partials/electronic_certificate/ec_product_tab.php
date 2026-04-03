<?php
/**
 * @var WC_Product $product
 */

// Получаем текущие значения
$tru_code_field = YooKassaElectronicCertificate::TRU_CODE_PROP_NAME;
$available_by_ec_field = YooKassaElectronicCertificate::AVAILABLE_BY_EC_PROP_NAME;
$tru_code_value = $product ? $product->get_meta($tru_code_field) : '';
$available_by_ec_value = $product ? $product->get_meta($available_by_ec_field) : '';
$is_checked = $available_by_ec_value === '1' ? 'checked' : '';
?>

<style>
    #woocommerce-product-data ul.wc-tabs li.electronic_certificate_options.electronic_certificate_tab a::before {
        content: "\f123";
    }

    .yookassa-cert-field {
        transition: all 0.3s ease;
    }

    .yookassa-cert-field:disabled {
        background-color: #f5f5f5;
        border-color: #ddd;
        color: #999;
        cursor: not-allowed;
    }
</style>

<div id="yookassa_electronic_certificate_data" class="panel woocommerce_options_panel hidden">

    <!-- Строка 1: Чекбокс -->
    <p class="form-field <?= $available_by_ec_field ?>_field">
        <label for="<?= $available_by_ec_field ?>">
            <?= __('Добавить оплату сертификатом', 'yookassa') ?>
        </label>
        <input type="checkbox"
               class="checkbox"
               name="<?= $available_by_ec_field ?>"
               id="<?= $available_by_ec_field ?>"
               value="1"
            <?= $is_checked ?>
               onclick="toggleTruCodeField(this.checked)"
        />
    </p>

    <!-- Строка 2: Текстовое поле -->
    <p class="form-field <?= $tru_code_field ?>_field">
        <label for="<?= $tru_code_field ?>">
            <?= __('Укажите код вида ТРУ для выбранного товара', 'yookassa') ?>
        </label>
        <input type="text"
               class="input-text yookassa-cert-field"
               name="<?= $tru_code_field ?>"
               id="<?= $tru_code_field ?>"
               value="<?= $tru_code_value ?>"
               placeholder="000000000.00000000000000000000"
               maxlength="30"
               minlength="30"
               required
        <?= empty($is_checked) ? 'disabled' : '' ?>"
        />
        <span class="description">
            <?= __('Укажите код вида ТРУ для выбранного товара', 'yookassa') ?>
            <br><?= __("Убедитесь, что код подходит к товару, иначе оплата по сертификату не пройдёт. Сверить коды можно <a data-qa-link='https://esnsi.gosuslugi.ru/classifiers/10616/data?pg=1&p=1' target='_blank' href='https://esnsi.gosuslugi.ru/classifiers/10616/data?pg=1&p=1'>на специальной странице</a>", 'yookassa') ?>
        </span>
    </p>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        // Инициализация при загрузке
        toggleTruCodeField($('#<?= $available_by_ec_field ?>').is(':checked'));
    });

    function toggleTruCodeField(isEnabled) {
        const truCodeField = document.getElementById('<?= $tru_code_field ?>');
        const errorSpan = document.getElementById('tru_code_error');

        if (isEnabled) {
            truCodeField.disabled = false;
            truCodeField.required = true;
            truCodeField.style.borderColor = '#7e8993';
        } else {
            truCodeField.disabled = true;
            truCodeField.required = false;
            truCodeField.style.borderColor = '#ddd';
            errorSpan.style.display = 'none';
        }
    }
</script>
