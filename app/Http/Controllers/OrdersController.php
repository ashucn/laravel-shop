<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\UserAddress;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderService;

class OrdersController extends Controller
{
    protected $orderService;

    /**
     * 当 Laravel 初始化 Controller 类时会检查该类的构造函数参数，
     * 在本例中 Laravel 会自动创建一个 CartService 对象作为构造参数传入给 CartController。
     *
     * 利用 Laravel 的自动解析功能注入 CartService 类
     *
     * CartController constructor.
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }


    public function index(Request $request)
    {
        $orders = Order::query()
            // 使用 with 方法预加载，避免N + 1问题
            ->with(['items.product', 'items.productSku'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate();

        return view('orders.index', ['orders' => $orders]);
    }

    /**
     * 订单详情页
     * load() 方法与 with() 预加载方法有些类似，称为 延迟预加载，
     * 不同点在于 load() 是在已经查询出来的模型上调用，
     * 而 with() 则是在 ORM 查询构造器上调用。
     *
     * @param Order $order
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Order $order, Request $request)
    {
        $this->authorize('own', $order);

        return view('orders.show', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }

    /**
     * 下订单的思路：
     * 在事务里先创建了一个订单，把当前用户设为订单的用户，然后把传入的地址数据快照进 address 字段。
     * 然后遍历传入的商品 SKU 及其数量，
     * $order->items()->make() 方法可以新建一个关联关系的对象（也就是 OrderItem）但不保存到数据库，
     * 这个方法等同于 $item = new OrderItem(); $item->order()->associate($order);。
     * 然后根据所有的商品单价和数量求得订单的总价格，更新到刚刚创建的订单的 total_amount 字段。
     * 最后使用 Laravel 提供的 collect() 辅助函数快速取得所有 SKU ID，然后将本次订单中的商品 SKU 从购物车中删除。
     *
     * @param OrderRequest $request
     * @param OrderService $orderService
     * @return mixed
     */
    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user = $request->user();
        $address = UserAddress::find($request->input('address_id'));

        return $orderService->store($user, $address, $request->input('remark'), $request->input('items'));

    }
}