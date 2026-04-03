<?php

use YooKassa\Model\PaymentData\ElectronicCertificate\ElectronicCertificateArticle;
use YooKassa\Model\PaymentData\PaymentDataElectronicCertificate;
use YooKassa\Model\PaymentMethodType;
use YooKassa\Request\Payments\CreatePaymentRequest;
use YooKassa\Request\Payments\CreatePaymentRequestBuilder;

if ( ! class_exists('YooKassaGateway')) {
    return;
}

class YooKassaGatewayElectronicCertificate extends YooKassaGateway
{
    public $paymentMethod = PaymentMethodType::ELECTRONIC_CERTIFICATE;

    public $id = 'yookassa_electronic_certificate';

    public function __construct()
    {
        parent::__construct();

        $this->icon = YooKassa::$pluginUrl.'assets/images/kassa.png';

        $this->method_title           = __('Оплата электронным сертификатом', 'yookassa');
        $this->method_description     = __('Покупатель сможет заплатить за покупку сертификатом, привязанным к карте «Мир»', 'yookassa');

        $this->defaultTitle           = __('Электронным сертификатом', 'yookassa');
        $this->defaultDescription     = __('Привязанным к карте «Мир»', 'yookassa');

        $this->title                  = $this->getTitle();
        $this->description            = $this->getDescription();

        $this->enableRecurrentPayment = false;

        $this->has_fields             = true;
    }

    public function init_form_fields()
    {
        parent::init_form_fields();
    }

    public function is_available()
    {
        if (!parent::is_available()) {
            return false;
        }

        if (!WC() || !WC()->cart || WC()->cart->is_empty()) {
            return false;
        }

        $cartItems = WC()->cart->get_cart();

        $hasEligibleProduct = false;

        foreach ($cartItems as $cartItem) {
            $product = $cartItem['data'];

            if (!$product || !is_a($product, 'WC_Product')) {
                continue;
            }

            $availableByEc = $product->get_meta(YooKassaElectronicCertificate::AVAILABLE_BY_EC_PROP_NAME);
            $truCode = $product->get_meta(YooKassaElectronicCertificate::TRU_CODE_PROP_NAME);

            if ($availableByEc === '1' && !empty($truCode)) {
                $hasEligibleProduct = true;
                break;
            }
        }

        return $hasEligibleProduct;
    }

    /**
     * @param WC_Order $order
     *
     * @return CreatePaymentRequestBuilder
     * @throws Exception
     */
    protected function getBuilder($order)
    {
        YooKassaLogger::sendHeka(array('payment.create.init'));

        $paymentData = new PaymentDataElectronicCertificate();
        $articles = $this->getArticlesFromOrder($order);

        if (empty($articles)) {
            YooKassaLogger::error('В корзине нет товаров, доступных для оплаты электронным сертификатом');
        }

        $paymentData->setArticles($articles);

        $amount = YooKassaOrderHelper::getTotal($order);
        $metadata = $this->createMetadata();

        $builder = CreatePaymentRequest::builder()
            ->setAmount(YooKassaOrderHelper::getAmountByCurrency($amount))
            ->setPaymentMethodData($paymentData)
            ->setCapture(true)
            ->setDescription($this->createDescription($order))
            ->setConfirmation(array(
                'type'      => $this->confirmationType,
                'returnUrl' => get_site_url(null, sprintf(self::getReturnUrlPattern(), $order->get_order_key())),
            ))
            ->setMetadata($metadata);

        YooKassaLogger::info('Return url: '.$order->get_checkout_payment_url(true));

        YooKassaHandler::setReceiptIfNeeded($builder, $order, $this->subscribe);

        YooKassaLogger::sendHeka(array('payment.create.success'));

        return $builder;
    }

    /**
     * Формирует список товаров для оплаты электронным сертификатом
     *
     * @param WC_Order $order
     * @return array
     * @throws Exception
     */
    private function getArticlesFromOrder($order)
    {
        $articles = array();
        $items = $order->get_items();
        $articleNumber = 1;

        /** @var WC_Order_Item_Product $item */
        foreach ($items as $item) {
            $product = $item->get_product();

            if (!$product) {
                continue;
            }

            $availableByEc = $product->get_meta(YooKassaElectronicCertificate::AVAILABLE_BY_EC_PROP_NAME);
            $truCode = $product->get_meta(YooKassaElectronicCertificate::TRU_CODE_PROP_NAME);

            if ($availableByEc !== '1' || empty($truCode)) {
                YooKassaLogger::info(sprintf(
                    'Товар ID: %d пропущен для оплаты электронным сертификатом. available_by_ec: %s, tru_code: %s',
                    $product->get_id(),
                    $availableByEc,
                    $truCode
                ));
                continue;
            }

            $article = new ElectronicCertificateArticle();

            $article->setArticleNumber($articleNumber++)
                ->setTruCode($truCode)
                ->setArticleName($item->get_name())
                ->setQuantity($item->get_quantity());

            $price = $order->get_item_total($item, true, true);
            $amount = YooKassaOrderHelper::getAmountByCurrency($price);
            $article->setPrice($amount);

            $articles[] = $article;

            YooKassaLogger::info(sprintf(
                'Добавлен товар для оплаты электронным сертификатом: article_name: %s, article_number %d, код ТРУ: %s, количество: %d',
                $item->get_name(),
                $articleNumber - 1,
                $truCode,
                $item->get_quantity()
            ));
        }

        return $articles;
    }
}
