<?php

//后台首页控制器
class IndexController extends BaseController {


    public function index() {
        // echo "admin...index...";
        include CUR_VIEW_PATH . "index.html";
    }

    public function top() {
        include CUR_VIEW_PATH . "top.html";
    }

    public function menu() {
        include CUR_VIEW_PATH . "menu.html";
    }

    public function drag() {
        include CUR_VIEW_PATH . "drag.html";
    }

}
