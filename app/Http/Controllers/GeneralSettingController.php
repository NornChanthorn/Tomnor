<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Constants\Message;
use App\Models\GeneralSetting;
use App\Http\Requests\GeneralSettingRequest;
use App\Traits\FileHandling;
use Auth;

class GeneralSettingController extends Controller
{
    use FileHandling;

    /** @var string Folder name to store site logo */
    private $logoFolder = 'site-logo';

    /**
     * Show view of general setting.
     *
     * @return Response
     */
    public function index()
    {
        if(!Auth::user()->can('app.setting')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }
        $setting = GeneralSetting::first() ?? new GeneralSetting();
        return view('setting.general', compact('setting'));
    }

    /**
     * Save general setting.
     *
     * @param GeneralSettingRequest $request
     *
     * @return Response
     */
    public function save(GeneralSettingRequest $request)
    {
        if(!Auth::user()->can('app.setting')){
            return back()->with([Message::ERROR_KEY=>trans('message.no_permission'), 'alert-type' => 'warning'], 403);
        }

        $setting = GeneralSetting::first() ?? new GeneralSetting();
        $setting->site_title = $request->site_title;

        if (isset($request->site_logo)) {
            $setting->site_logo = $this->uploadImage($this->logoFolder, $request->site_logo);
        }
        if($request->enable_over_sale==1){
            $setting->enable_over_sale=1;
        }else {
            $setting->enable_over_sale=0;
        }
        $setting->save();
        session()->flash(Message::SUCCESS_KEY, trans('message.item_saved_success'));
        return back();
    }
}
