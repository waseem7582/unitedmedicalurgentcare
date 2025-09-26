<?php

namespace App\Http\Controllers\Api\V1\Hospital;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Helpers\Api\Helpers as ApiResponse;
use Illuminate\Http\Request;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use App\Http\Helpers\Response;

class ProfileController extends Controller
{
    public function profile() {
        $user = authGuardApi()['user'];

        $data =[
            'default_image' => "backend/images/default/profile-default.webp",
            "image_path"    => "frontend/user",
            "base_ur"       => url('/'),
            'user'          => $user,
            'countries'     => get_all_countries(['id', 'name', 'mobile_code']),
        ];

        $message =  ['success'=>[__('Hospital Profile')]];
        return ApiResponse::success($message,$data);
    }

    /**
     * Profile Update
     * @method POST
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
    */
    public function profileUpdate(Request $request){
        $user =authGuardApi()['user'];

        $validator = Validator::make($request->all(),[
            'hospital_name' => 'required|string|max:60',
            'country'    => 'nullable|string',
            'city'       => 'nullable|string',
            'address'    => 'nullable|string',
            'state'      => 'nullable|string',
            'zip_code'   => 'nullable|string',
            'phone'      => 'nullable|string|unique:hospitals,mobile,'.$user->id,
            'phone_code'  => 'nullable|string',
            'image'      => "nullable|image|mimes:jpg,png,jpeg,webp|max:10240",
        ]);


        if($validator->fails()){
            $error = ['error' => [$validator->errors()->all()]];
            return ApiResponse::onlyValidation($error);
        }

        $validated = $validator->validated();

        $validated['mobile']        = remove_speacial_char($validated['phone']);
        $validated['mobile_code']   = remove_speacial_char($validated['phone_code']);
        $complete_phone             = $validated['mobile_code'] . $validated['mobile'];
        $validated['full_mobile']   = $complete_phone;

        $validated['hospital_name']   = $validated['hospital_name'];


        $validated['address']       = [
            'country'  => $validated['country'],
            'city'     => $validated['city'],
            'state'    => $validated['state'],
            'zip'      => $validated['zip_code'],
            'address'  => $validated['address'],
        ];

        if($request->hasFile('image')){

            if($user->image == null){
                $oldImage = null;
            }else{
                $oldImage = $user->image;
            }

            $image = upload_file($validated['image'],'user-profile', $oldImage);
            $upload_image = upload_files_from_path_dynamic([$image['dev_path']],'user-profile');
            delete_file($image['dev_path']);
            $validated['image']     = $upload_image;
        }

        try {
            $user->update($validated);
        } catch (\Throwable $th) {
            $error = ['error'=>[__('Something went wrong! Please try again')]];
            return ApiResponse::error($error);
        }

        $message =  ['success'=>[__('Profile successfully updated')]];
        return ApiResponse::onlySuccess($message);
    }


     /**
     * Password Update
     *
     * @method POST
     * @param Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
    */
    public function passwordUpdate(Request $request){

        $basic_settings = BasicSettingsProvider::get();

        $passowrd_rule = 'required|string|min:6|confirmed';

        if($basic_settings->secure_password) {
            $passowrd_rule = ["required",Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised(),"confirmed"];
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string|min:6',
            'password' =>$passowrd_rule,
        ]);

        if($validator->fails()){
            $error =  ['error'=>$validator->errors()->all()];
            return ApiResponse::validation($error);
        }

        $validated = $validator->validate();
        $user =authGuardApi()['user'];

        if (!Hash::check($request->current_password, $user->password)) {
            $message = ['error' =>  [__('Current password didn\'t match')]];
            return ApiResponse::error($message);
        }
        if(Hash::check($request->password,$user->password)) {
            throw ValidationException::withMessages([
                'password'      => __("You Selected One Of Your Old Password, Please Selected New Password"),
            ]);
        }

        try {
            $user->update(['password' => Hash::make($validated['password'])]);
            $message = ['success' =>  [__('Password updated successfully')]];
            return ApiResponse::onlySuccess($message);
        } catch (Exception $ex) {
            info($ex);
            $message = ['error' =>  [__('Something went wrong! Please try again')]];
            return ApiResponse::error($message);
        }
    }

       /**
     * Account Delete
     *
     * @method POST
     * @return \Illuminate\Http\Response
     */
    public function deleteAccount()
    {

        $user =auth()->user();
        if (!$user) {
            $message = [[__('No user found')]];
            return Response::error($message, []);
        }

        try {
            $user->status            = 0;
            $user->deleted_at        = now();
            $user->save();
        } catch (\Throwable $th) {
            $message = [[__('Something went wrong! Please try again')]];
            return Response::error($message, []);
        }

        $message = [[__('Hospital deleted successful')]];
        return Response::success($message, $user);
    }
}
