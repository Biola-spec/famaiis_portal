<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SiteSetting;
use App\Models\PaymentSetting;

class SettingController extends Controller
{
    public function SiteSettingView(){
    	$setting = SiteSetting::find(1);
    	return view('backend.setting.site_update',compact('setting'));
    }

    public function SiteSettingUpdate(Request $request){

    	$setting_id = $request->id;
        $setting = SiteSetting::findOrFail($setting_id);

        $save_url = $setting->logo;
    	if ($request->file('logo')) {
            $image = $request->file('logo');
            $name_gen = hexdec(uniqid()).'.'.$image->getClientOriginalExtension();
            $image->move(public_path('upload/logo'),$name_gen);
            $save_url = 'upload/logo/'.$name_gen;
    	}

        $login_save_url = $setting->login_image;
        if ($request->file('login_image')) {
            $image2 = $request->file('login_image');
            $name_gen2 = hexdec(uniqid()).'.'.$image2->getClientOriginalExtension();
            $image2->move(public_path('upload/login_images'),$name_gen2);
            $login_save_url = 'upload/login_images/'.$name_gen2;
        }

    	$setting->update([
    		'school_name' => $request->school_name,
    		'school_email' => $request->school_email,
    		'school_mobile_one' => $request->school_mobile_one,
    		'school_mobile_two' => $request->school_mobile_two,
    		'school_address' => $request->school_address,
    		'current_session' => $request->current_session,
    		'copyright' => $request->copyright,
    		'logo' => $save_url,
            'login_image' => $login_save_url,
    	]);

	    $notification = array(
    		'message' => 'Site Setting Updated Successfully',
    		'alert-type' => 'info'
    	);

    	return redirect()->back()->with($notification);
    } // end method 

    public function PaymentSettingView()
    {
        $setting = PaymentSetting::firstOrCreate(
            ['id' => 1],
            [
                'provider' => 'paystack',
                'payment_url' => 'https://api.paystack.co',
                'bank_transfer_enabled' => true,
                'is_active' => true,
            ]
        );

        return view('backend.setting.payment_update', compact('setting'));
    }

    public function PaymentSettingUpdate(Request $request)
    {
        $data = $request->validate([
            'provider' => 'required|in:paystack,flutterwave',
            'public_key' => 'nullable|string|max:255',
            'secret_key' => 'nullable|string|max:500',
            'payment_url' => 'required|url|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:50',
            'transfer_instructions' => 'nullable|string|max:1000',
            'bank_transfer_enabled' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $setting = PaymentSetting::firstOrCreate(['id' => 1]);

        $setting->update([
            'provider' => $data['provider'],
            'public_key' => $data['public_key'] ?? null,
            'secret_key' => $data['secret_key'] ?? null,
            'payment_url' => $data['payment_url'],
            'bank_name' => $data['bank_name'] ?? null,
            'account_name' => $data['account_name'] ?? null,
            'account_number' => $data['account_number'] ?? null,
            'transfer_instructions' => $data['transfer_instructions'] ?? null,
            'bank_transfer_enabled' => (bool) ($data['bank_transfer_enabled'] ?? false),
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        $notification = [
            'message' => 'Payment settings updated successfully.',
            'alert-type' => 'success',
        ];

        return redirect()->back()->with($notification);
    }


}
