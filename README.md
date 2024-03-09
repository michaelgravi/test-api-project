<h1>Как в проэкте используются принципы  SOLID:</h1>
<h2>В микро маштабах:</h2>
    <h3>Принцип единственной ответственности (Single responsibility principle)</h3>
    <p>Валидация и логирование делается отдельно.</p>
<div class="highlight highlight-text-html-php notranslate position-relative overflow-auto" dir="auto"><pre>
    public function store(StoreTransactionRequest $request, LogService $logService)
    {
        $transaction = $this->transaction->create($request->all());
        $logService->log();
        return response()->json($transaction, 201);
    }
</pre><div class="zeroclipboard-container">

<h2>В макро маштабах:</h2>
    <h3>Принцип открытости-закрытости. (Open closed Principle)</h3>
    <p>В даннои коде у меня есть абстрактная функция getExchangeRate(), которую потом в дальнейшем я расписываю в наследуемом классе, таким оброзом я модифицирую класс а все остольные функции не изменяются.</p>
<div class="highlight highlight-text-html-php notranslate position-relative overflow-auto" dir="auto"><pre>
<div>
    abstract class ExchangeRate
    {
        final public function getExchangeRateData($api_url)
        {
            $this->makeApiCall($api_url);
            $this->getExchangeRate();
        }
        protected function makeApiCall($api_url)
        {
            return Http::get($api_url);
        }
        abstract public function getExchangeRate();
    }
</div>
</pre><div class="zeroclipboard-container">

<h3>Принцип подстановки Барбары Лисков (Liskov substitution Principle)</h3>
<p>В даннои коде я наследую class ExchangeRate, а затем использую родительский метод makeApiCall().</p>
<div class="highlight highlight-text-html-php notranslate position-relative overflow-auto" dir="auto"><pre>
<div>
    class ExchangeRateJsonDriver extends ExchangeRate
    {
        protected $request;
        public function __construct()
        {
            $this->request = $this->makeApiCall(config('services.exchange_rate.json_url'));
        }
        public function getExchangeRate()
        {
            $response = $this->request;
            $data = $response->body();
            $jsonData = json_decode($data, true);
            return $jsonData;
        }
    }
</div>
</pre><div class="zeroclipboard-container">


<h3>Принцип разделения интерфейсов (Interface Segregation Principle)</h3>
<p>В даннои коде у нас два класса котрые наследуют только нужные фунции, я не прописал в родительский класс фунуцию xmlFormat() чем избежал имплементить ненужной функции.</p>
<div class="highlight highlight-text-html-php notranslate position-relative overflow-auto" dir="auto"><pre>
<div>
    class ExchangeRateJsonDriver extends ExchangeRate
    {
        protected $request;
        public function __construct()
        {
            $this->request = $this->makeApiCall(config('services.exchange_rate.json_url'));
        }
        public function getExchangeRate()
        {
            $response = $this->request;
            $data = $response->body();
            $jsonData = json_decode($data, true);
            return $jsonData;
        }
    }
    class ExchangeRateXmlDriver extends ExchangeRate
    {
        protected $request;
        public function __construct()
        {
            $this->request = $this->makeApiCall(config('services.exchange_rate.xml_url'));
        }
        public function getExchangeRate()
        {
            $response = $this->request;
            return $this->xmlFormat($response->body());
        }
        public function xmlFormat($response)
        {
            $data = $response;
            $xml = simplexml_load_string($data, "SimpleXMLElement", LIBXML_NOCDATA);
            $json = json_encode($xml);
            return json_decode($json, TRUE);
        }
    }
</div>
</pre><div class="zeroclipboard-container">
<h1>Что означает правильное использование PHPDoc?</h1>
<h2>По моему правильное испрользование PHPDoc, это описание функции методов и.т.д в случаях когда в функционале есть то что нужно учесть и это не очевидно.В остольных случаях лучше использовать говоряшии и понятные названия переменых,классов, функции и.т.д.</h2>
<p>В даннои случае для того чтобы не надо было вникать в работу драйверов я задокументировал какие параметры может принимать драйвер.</p>
<div class="highlight highlight-text-html-php notranslate position-relative overflow-auto" dir="auto"><pre>
<div>
    /*
    |--------------------------------------------------------------------------
    | Exchange Rate Services
    |--------------------------------------------------------------------------
    | This configuration pertains to the exchange rate service. It comprises three
    | individual drivers, along with a combined driver. When making a request,
    | the driver needs to specify parameters, which can be XML, JSON, CSV, or an average.
    |
    */
    'exchange_rate' => [
        'driver' => env('EXCHANGE_RATE_DRIVER',"avarage"),
        'json_url'=>env('EXCHANGE_RATE_JSON_URL'),
        'csv_url'=>env('EXCHANGE_RATE_CSV_URL'),
        'xml_url'=>env('EXCHANGE_RATE_XML_URL'),
    ],
</div>
</pre><div class="zeroclipboard-container">

</pre><div class="zeroclipboard-container">
<h1>PHP 7+</h1>
<p>В PHP 7 была добавлена возможность указывать типы переменных в функциях и методах. Теперь можно задавать типы аргументов функций и методов, а также тип возвращаемого значения. Для простых типов существует возможность указать int, float, string и bool. Также можно использовать классы и интерфейсы в качестве типа.По моему типизация это полезный функционал.С его помошью мы можем быть уверены, что получим нужный нам тип данных, что позволит избежать передачи и возврата неверных значений при работе с функциями. Так же в PHP 7 добавили возможность группировать объявления импорта классов, находящихся в одном пространстве имён, что сокрашяет код.По моему оба функционала полезны и их можно будет полезно имплементировать</p>


