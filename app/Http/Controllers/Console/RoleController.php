<?php

namespace App\Http\Controllers\Console;

use App\Http\Requests\Console\RoleRequest;
use App\Services\Console\RoleService;
use App\Transformers\RolePermissionListTransformer;
use App\Transformers\RoleTransformer;
use App\Http\Controllers\Controller;
use App\Transformers\ShowRoleTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class RoleController extends Controller
{
    protected $roleService;
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $paginate = $this->roleService->paginate();
        $resource = new Collection($paginate,new RoleTransformer());
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginate));
        $this->setData($resource);
        return $this->response();
    }

    /**
     * @param RoleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(RoleRequest $request)
    {
        $create = $request->only(['name','displayName','description','level']);
        $this->log('controller.request to '.__METHOD__,['create' => $create]);
        $create = parse_input($create);
        $result = $this->roleService->store($create);
        $resource = new Item($result,new RoleTransformer());
        $this->setData($resource);
        return $this->response();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $resource = new Item($this->roleService->find($id),new ShowRoleTransformer());
        $this->setData($resource);
        return $this->response();
    }

    /**
     * @param RoleRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(RoleRequest $request, $id)
    {
        $update = $request->only(['name','displayName','level']);
        if (!empty($request->get('description'))) $update['description'] = $request->get('description');
        $this->log('service.request to '.__METHOD__,['update' => $update]);
        $update = parse_input($update);
        $this->roleService->update($update,$id);
        $resource = new Item($this->roleService->find($id),new RoleTransformer());
        $this->setData($resource);
        return $this->response();
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $result = $this->roleService->delete($id);
        if ($result) return $this->response();
    }

    /**
     * 去重给角色分配权限
     * @param RoleRequest $request
     * @param $roleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function attachPermission(RoleRequest $request, $roleId)
    {
        $permissions = $request->get('permissionIds');
        $this->roleService->attachPermission($permissions,$roleId);
        $resource = new Item($this->roleService->find($roleId),new RoleTransformer());
        $this->setData($resource);
        return $this->response();
    }

    /**
     * 覆盖分配权限
     * @param RoleRequest $request
     * @param $roleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function coverAttachPermission(RoleRequest $request,$roleId)
    {
        $permissions = $request->get('permissionIds');
        $this->roleService->coverAttachPermission($permissions,$roleId);
        $resource = new Item($this->roleService->find($roleId),new RoleTransformer());
        $this->setData($resource);
        return $this->response();
    }


    /**
     * @param $roleId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPermissionByRole($roleId)
    {
        $result = $this->roleService->find($roleId);
        $resource = new Item($result,new RolePermissionListTransformer());
        $this->setData($resource);
        return $this->response();
    }


}
