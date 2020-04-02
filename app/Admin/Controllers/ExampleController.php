<?php

namespace App\Admin\Controllers;

use App\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use App\Logs\HmlLog;
use App\Http\Manager\SampleManager;
use App\Exceptions\HmlException;
use CommonConst;
use SampleConst;
use App\Http\Eloquent\ConfigEloquent;

class ExampleController extends AdminController
{
    protected $log;
    protected $manager;

    public function __construct(SampleManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Example controller';


    public function sample()
    {
        //dd(SampleConst::Matsuzaki);
        //throw new HmlException('samplefinal');

        $line_config = ConfigEloquent::getRecord('line');
        $line_config['secret'] = 'aab';
        $line_config->save();
        dd('ok');

        //保持レコードはトランザクション外で全て行う
        $this->manager->createRecord();

        \DB::connection('mysql')->beginTransaction();
        \DB::connection('log')->beginTransaction();
        try {
            //saveのみ実行
            $this->manager->saveData();
            $this->manager->saveLog();

            \DB::connection('mysql')->commit();
            \DB::connection('log')->commit();
        } catch (\Exception $exception) {
            \DB::connection('mysql')->rollBack();
            \DB::connection('log')->rollBack();
            throw $exception;
        }
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new ExampleModel);

        $grid->column('id', __('ID'))->sortable();
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed   $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(ExampleModel::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new ExampleModel);

        $form->display('id', __('ID'));
        $form->display('created_at', __('Created At'));
        $form->display('updated_at', __('Updated At'));

        return $form;
    }
}
