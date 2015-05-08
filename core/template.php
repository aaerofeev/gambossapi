<?php

/**
 * Шаблонизатор
 */
class Template {

    /**
     * Шаблон
     *
     * @var string
     */
    protected $template;

    /**
     * Запрос
     *
     * @var Request
     */
    protected $request;

    function __construct($template) {

        $this->template = $template;
    }

    public function render($values = array()) {

        ob_start();

        foreach ($values as $k => $v)
            $this->$k = $v;

        require ($this->template . '.php');

        foreach ($values as $k => $v)
            unset($this->$k);

        return ob_get_clean();
    }

    public function baseUrl($path) {

        return $this->request->baseUrl($path);
    }

    public function url($route, $_ = null) {

        return call_user_func_array(array($this->request, 'url'), func_get_args());
    }

    public function setRequest(Request $request) {

        $this->request = $request;
    }

    public function renderPager($pages, $currentPage, $route) {

        $list = array();
        $next = NULL;
        $prev = NULL;

        $start = 1;

        for ($i = 1; $i <= $pages; $i ++) {

            $list[$i] = '?' .
                http_build_query(array_merge($_GET,
                    array(
                        'page' => $i,
                    )
                ));

            if ($i == 1) {
                $prev = $list[$i];
            }

            if ($i > $currentPage) {
                $next = $list[$i];
            }

            if ($i == ($currentPage - 5)) {
                $start = $i;
            }
        }

        $list = array_slice($list, $start - 1, 10, true);

        $pager = new Template('template/service/pager');
        $pager->setRequest($this->request);

        $pager->currentPage = $currentPage;
        $pager->pages = $pages;
        $pager->list = $list;
        $pager->next = $next;
        $pager->prev = $prev;

        return $pager->render();
    }
}