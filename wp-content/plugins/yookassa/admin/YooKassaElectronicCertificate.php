<?php

/**
 * Класс по добавлению данных для оплаты электронным сертификатом в товарах
 */
class YooKassaElectronicCertificate
{
    const TRU_CODE_PROP_NAME = '_yookassa_tru_code';
    const AVAILABLE_BY_EC_PROP_NAME = '_yookassa_available_by_ec';
    const TRU_CODE_LENGTH = 30;

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * @param string $plugin_name
     * @param string $version
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;
    }


    /**
     * Добавление вкладки
     *
     * @param array $tabs
     * @return array
     */
    public function addECTab($tabs)
    {
        $tabs['electronic_certificate'] = array(
            'label'    => __('Оплата сертификатом', 'yookassa'),
            'target'   => 'yookassa_electronic_certificate_data',
            'priority' => 70,
        );
        return $tabs;
    }

    /**
     * Добавление содержимого вкладки
     *
     * @return void
     */
    public function ecTabContent()
    {
        $productId = isset($_GET['post']) ? absint($_GET['post']) : 0;
        $product = $productId ? wc_get_product($productId) : null;
        $this->render(
            'partials/electronic_certificate/ec_product_tab.php',
            array(
                'product' => $product,
            )
        );
    }

    /**
     * Сохранение данных
     *
     * @param int $productId
     * @throws Exception
     */
    public function saveTruCodeField($productId)
    {
        try {
            YooKassaLogger::info(sprintf(
                'Starting to save electronic certificate fields for product ID: %d',
                $productId
            ));

            $product = wc_get_product($productId);
            if (!$product) {
                YooKassaLogger::error(sprintf('Product not found: %d', $productId));
                return;
            }

            $isAvailableByEc = isset($_POST[self::AVAILABLE_BY_EC_PROP_NAME]) && $_POST[self::AVAILABLE_BY_EC_PROP_NAME] === '1';

            $truCode = isset($_POST[self::TRU_CODE_PROP_NAME])
                ? trim($_POST[self::TRU_CODE_PROP_NAME])
                : '';

            YooKassaLogger::info(sprintf(
                'Product ID: %d, Available by EC: %s, TRU Code: %s',
                $productId,
                $isAvailableByEc ? 'true' : 'false',
                $truCode
            ));

            $product->update_meta_data(
                self::AVAILABLE_BY_EC_PROP_NAME,
                $isAvailableByEc ? '1' : '0'
            );

            if (!$isAvailableByEc) {
                YooKassaLogger::info(sprintf(
                    'EC disabled for product ID: %d. TRU code preserved: %s',
                    $productId,
                    $product->get_meta(self::TRU_CODE_PROP_NAME)
                ));
                $product->save();
                return;
            }

            if (empty($truCode)) {
                YooKassaLogger::error(sprintf(
                    'TRU code is empty but EC enabled for product ID: %d. Code not saved, preserving old value: %s',
                    $productId,
                    $product->get_meta(self::TRU_CODE_PROP_NAME)
                ));
                $product->save();
                return;
            }

            $product->update_meta_data(self::TRU_CODE_PROP_NAME, $truCode);

            $product->save();

            YooKassaLogger::info(sprintf(
                'Successfully saved TRU code for product ID: %d. Code: %s',
                $productId,
                $truCode
            ));

        } catch (Exception $e) {
            YooKassaLogger::error(sprintf(
                'Error saving electronic certificate fields for product ID: %d. Error: %s.',
                $productId,
                $e->getMessage()
            ));
        }
    }

    /**
     * Отрисовывает новый таб с маркировкой
     *
     * @param string $viewPath
     * @param array $args
     * @return void
     */
    private function render($viewPath, $args)
    {
        extract($args);
        include(plugin_dir_path(__FILE__) . $viewPath);
    }

    /**
     * Выполняет проверку,
     * что открыта страница товара в админке
     *
     * @return bool
     */
    private function isProductPage()
    {
        if (!is_admin()) {
            return false;
        }

        global $pagenow;

        $current_screen = get_current_screen();

        return (
            $current_screen
            && $current_screen->post_type === 'product'
            && ($pagenow === 'post.php' || $pagenow === 'post-new.php')
        );
    }
}
