<?php

namespace App\Admin\Controllers;

use App\Models\CouponCode;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;

/**
 * 注意： Laravel-Admin内置删除方法 destroy
 * Class CouponCodesController
 * @package App\Admin\Controllers
 */
class CouponCodesController extends Controller
{

    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        /*        return $content
                    ->header('Index')
                    ->description('description')
                    ->body($this->grid());*/

        return Admin::content(function (Content $content) {
            $content->header('优惠券列表');
            $content->body($this->grid());
        });
    }

    /**
     * Show interface.
     *
     * @param mixed $id
     * @param Content $content
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->header('Detail')
            ->description('description')
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     * 优惠券管理： 修改优惠券
     * @param mixed $id
     * @return Content
     */
    public function edit($id)
    {

        return Admin::content(function (Content $content) use ($id) {
            $content->header('编辑优惠券');
            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @param Content $content
     * @return Content
     */
    public function create(Content $content)
    {
        return Admin::content(function (Content $content) {
            $content->header('新增优惠券');
            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

        return Admin::grid(CouponCode::class, function (Grid $grid) {
            $grid->model()->orderBy('created_at', 'desc');
            $grid->id('ID')->sortable();
            $grid->name('名称');
            $grid->code('优惠码');
            $grid->description('描述');
            /**
             * 【用量】是虚拟出来的一个字段，然后通过 display() 来输出这个虚拟字段的内容。
             */
            $grid->column('usage', '用量')->display(function ($value) {
                return "{$this->used} / {$this->total}";
            });
            $grid->enabled('是否启用')->display(function ($value) {
                return $value ? '是' : '否';
            });
            $grid->created_at('创建时间');
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(CouponCode::findOrFail($id));

        $show->id('Id');
        $show->name('Name');
        $show->code('Code');
        $show->type('Type');
        $show->value('Value');
        $show->total('Total');
        $show->used('Used');
        $show->min_amount('Min amount');
        $show->not_before('Not before');
        $show->not_after('Not after');
        $show->enabled('Enabled');
        $show->created_at('Created at');
        $show->updated_at('Updated at');

        return $show;
    }

    /**
     * Make a form builder.
     *
     * 对于优惠码 code 字段，我们的第一个校验规则是 nullable，允许管理员用户不填，不填的情况优惠码将由系统生成。
     * 对于折扣 value 字段，我们的校验规则是一个匿名函数，
     * 当我们的校验规则比较复杂，或者需要根据管理员用户提交的其他字段来判断时就可以用匿名函数的方式来定义校验规则。
     * $form->saving() 方法用来注册一个事件处理器，在表单的数据被保存前会被触发，
     * 这里我们判断如果管理员用户没有输入优惠码，就通过 findAvailableCode() 来自动生成。
     *
     * @return Form
     */
    protected function form()
    {
        $form = Admin::form(CouponCode::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name', '名称')->rules('required');
//            $form->text('code', '优惠码')->rules('nullable|unique:coupon_codes');
            $form->text('code', '优惠码')->rules(function ($form) {
                // 如果 $form->model()->id 不为空，代表是编辑操作
                if ($id = $form->model()->id) {
                    return 'nullable|unique:coupon_codes,code,'.$id.',id';
                } else {
                    return 'nullable|unique:coupon_codes';
                }
            });
            $form->radio('type', '类型')->options(CouponCode::$typeMap)->rules('required');
            $form->text('value', '折扣')->rules(function ($form) {
                if ($form->type === CouponCode::TYPE_PERCENT) {
                    // 如果选择了百分比折扣类型，那么折扣范围只能是 1 ~ 99
                    return 'required|numeric|between:1,99';
                } else {
                    // 否则只要大等于 0.01 即可
                    return 'required|numeric|min:0.01';
                }
            });
            $form->text('total', '总量')->rules('required|numeric|min:0');
            $form->text('min_amount', '最低金额')->rules('required|numeric|min:0');
            $form->datetime('not_before', '开始时间');
            $form->datetime('not_after', '结束时间');
            $form->radio('enabled', '启用')->options(['1' => '是', '0' => '否']);

            $form->saving(function (Form $form) {
                if (!$form->code) {
                    $form->code = CouponCode::findAvailableCode();
                }
            });
        });

        return $form;
    }
}
