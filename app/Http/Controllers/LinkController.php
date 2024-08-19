<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller {


    public function redirect($link) {

        $link = Link::findByCode($link);


        return $this->redirect($link->url);
    }
}
