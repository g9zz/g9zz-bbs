<?php

namespace App\Http\Controllers\Index;

use App\Http\Requests\Index\AppendRequest;
use App\Services\Index\AppendService;
use App\Transformers\AppendTransformer;
use App\Http\Controllers\Controller;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class AppendController extends Controller
{
    protected $appendService;

    public function __construct(AppendService $appendService)
    {
        $this->appendService = $appendService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $paginate = $this->appendService->paginate();
        $resource = new Collection($paginate,new AppendTransformer());
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginate));
        $this->setData($resource);
        return $this->response();
    }

    /**
     * @param AppendRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AppendRequest $request)
    {
        $result = $this->appendService->store($request);
        $resource = new Item($result,new AppendTransformer());
        $this->setData($resource);
        return $this->response();
    }

    /**
     * @param $hid
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($hid)
    {
        $result = $this->appendService->hidFind($hid);
        $resource = new Item($result,new AppendTransformer());
        $this->setData($resource);
        return $this->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->appendService->hidDelete($id);
        if ($result) $this->response();
    }
}
