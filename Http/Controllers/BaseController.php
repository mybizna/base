<?php

namespace Modules\Base\Http\Controllers;

use Illuminate\Routing\Controller;

class BaseController extends Controller
{
    protected $user;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
        //var_dump(get_class($this));
        //var_dump(get_class());
        //var_dump(__CLASS__);
    }
}
