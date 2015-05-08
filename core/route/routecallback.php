<?php

abstract class RouteCallback {

    /**
     * @var Template
     */
    public $layout;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var string
     */
    protected $callback;

    public function start() {}

    public function finish() {

        echo $this->layout->render();
    }

    /**
     * Error content
     */
    public function errorCallback(Exception $e) {

        $code = 404;
        $message = 'Страница не найдена';

        if ($e) {
            if ($e->getCode()) {
                $code = $e->getCode();
            }

            if ($e->getMessage()) {
                $message = $e->getMessage();
            }
        }

        header('HTTP/1.1 ' . $code);

        $content = new Template('template/error');
        echo $content->render(array('code' => $code, 'message' => $message));
        exit(0);
    }

    public function redirect($path) {

        $this->request->redirect($path);
    }

    public function run(Request $request, $callback, $arguments = array()) {

        $this->request = $request;
        $this->callback = $callback;
        $this->start();
        call_user_func_array(array($this, $this->callback), $arguments);
        $this->finish();
    }
}