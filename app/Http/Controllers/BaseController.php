<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class BaseController extends Controller
{
    protected $title = "";
    protected $subTitle = "";
    protected $route = "";
    protected $description = "";
    protected $icon = "flaticon2-user";
    protected $resources = "admin::users.";
    protected $success = ['status' => true];
    protected $error = ['status' => false];


    public function __construct()
    {

    }

    function createResource()
    {
        return $this->resources . 'create';
    }

    function indexResource()
    {
        return $this->resources . 'index';
    }

    function editResource()
    {
        return $this->resources . 'edit';
    }

    function showResource()
    {
        return $this->resources . 'show';
    }

    function indexRoute()
    {
        return $this->route . 'index';
    }

    function gotoCrudIndex()
    {
        return redirect()->route($this->route . 'index');
    }

    function crudInfo()
    {
        $data['title'] = $this->title;
        $data['subTitle'] = $this->subTitle;
        $data['route'] = $this->route;
        $data['icon'] = $this->icon;
        $data['description'] = $this->description;
        return $data;
    }
    function returnSuccess($params = null)
    {
        $data = $this->success;
        if ($data) $data['data'] = $params;
        return response()->json($data);
    }

    function returnError($params = null)
    {
        $data = $this->error;
        if ($data) $data['data'] = $params;
        return response()->json($data);
    }

   function generateAllMiddlewareByPermission($permissionName = null)
   {
       //Take permission name automatically from title
       if (!$permissionName)
           $permissionName = strtolower($this->title);

       $this->middleware('permission:' . $permissionName)->only('index','show');
       $this->middleware('permission:' . $permissionName . '.add')->only('create', 'store');
       $this->middleware('permission:' . $permissionName . '.edit')->only('edit', 'update');
       $this->middleware('permission:' . $permissionName . '.delete')->only('destroy');
   }
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'status' => false,
            'message' => $error,
        ];


        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }


        return response()->json($response, $code);
    }
}

