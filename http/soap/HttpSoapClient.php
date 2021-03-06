<?php

    namespace nox\http\soap;

    use yii\base\Component;
    use yii\base\InvalidConfigException;

    /**
     * Class HttpSoapClient
     *
     * @package nox\soap
     */
    class HttpSoapClient extends Component
    {
        /**
         * @var string
         */
        public $endpoint;

        /**
         * @var array the array of SOAP client options.
         */
        public $options = [];

        /**
         * @var \SoapClient the SOAP client instance.
         */
        private $_soapClient;

        /**
         * @inheritdoc
         */
        public function init()
        {
            parent::init();

            if (is_null($this->endpoint) || empty($this->endpoint) || !filter_var($this->endpoint, FILTER_VALIDATE_URL)) {
                throw new InvalidConfigException('The Endpoint URL property must be set.');
            }

            try {
                $this->_soapClient = new \SoapClient($this->endpoint, $this->options);
            } catch (\SoapFault $exception) {
                throw new HttpSoapException($exception->getMessage(), (int)$exception->getCode(), $exception);
            }
        }

        /**
         * @param string $name
         * @param array  $arguments
         *
         * @return mixed
         *
         * @throws \Exception
         */
        public function __call($name, $arguments)
        {
            try {
                return call_user_func_array([$this->_soapClient, $name], $arguments);
            } catch (\SoapFault $exception) {
                throw new HttpSoapException($exception->getMessage(), (int)$exception->getCode(), $exception);
            }
        }
    }
