<?php

namespace App\Http\Controllers\Hospital;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Models\UserNotification;
use App\Models\Hospital\Hospital;
use App\Models\Admin\SiteSections;
use App\Models\Admin\BasicSettings;
use App\Http\Controllers\Controller;
use App\Models\Admin\BookingTempData;
use App\Constants\PaymentGatewayConst;
use App\Models\Hospital\HospitalOfflineWallet;
use App\Models\Hospital\Investigation;
use App\Models\Hospital\ServiceBooking;
use App\Notifications\EmailNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class HomeServiceController extends Controller
{
    public function homeService()
    {
        $page_title = __("Home Service");
        $contact    = SiteSections::where("key", "contact")->first();
        $hospital   = Hospital::with('investigations')->where("status", true)->get();
        return view("frontend.pages.home-service.index", compact(
            "page_title",
            "contact",
            "hospital"
        ));
    }

    public function getHomeService(Request $request)
    {
        $hospitalId = $request->hospital_id;

        $investigations = Investigation::where('hospital_id', $hospitalId)
            ->join('investigation_has_categories', 'investigations.id', '=', 'investigation_has_categories.investigation_id')
            ->join('investigation_categories', 'investigation_has_categories.investigation_category_id', '=', 'investigation_categories.id')
            ->where('investigation_categories.id', GlobalConst::Home_Service)
            ->select(
                'investigations.id',
                'investigations.name',
                'investigations.offer_price',
                'investigations.regular_price',
            )
            ->get();


        return response()->json([
            'success' => true,
            'investigations' => $investigations

        ]);
    }

    public function confirm(Request $request)
    {

        if (auth()->check() == false) return back()->with(['error' => [__('Please Login First.')]]);


        $validator              = Validator::make($request->all(), [
            'name'               => 'required|string',
            'hospital_id'        => 'nullable',
            'schedule_id'        => 'nullable',
            'gender'             => 'required|string',
            'age'                => 'required|string',
            'time'               => 'required|string',
            'shift'              => 'required|string',
            'age_type'           => "required|string",   
            'number'             => "required|regex:/^\+?[0-9]+$/",

            'email'              => "required|string",
            'date'               => 'required|date_format:Y-m-d|after_or_equal:today',
            'investigations'     => 'required|array',
            'message'            => "nullable"
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all());
        }

        $validated                  = $validator->validate();

        $investigations             = Investigation::whereIn("id", $validated["investigations"])->get();

        $price = $investigations->sum(function ($investigation) {
            return $investigation->offer_price ?? $investigation->regular_price;
        });


        $validated['price']         = $price;
        $validated['hospital_id']   = $validated['hospital_id'];
        $validated['user_id']       = auth()->user()->id;
        $validated['slug']          = Str::slug($validated['name']);
        $validated['uuid']          = Str::uuid();
        $validated['data']          = $validated;

        try {
            $booking =  BookingTempData::create($validated);
        } catch (Exception $e) {

            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }
        return redirect()->route('frontend.home.service.preview', $booking->data->uuid);
    }

    /**
     * Method for show the preview page
     * @param $slug
     * @param \Illuminate\Http\Request $request
     */
    public function preview(Request $request, $uuid)
    {
        $page_title         = __("Appointment Preview");
        $booking            = BookingTempData::where('uuid', $uuid)->first();
        $hospital           = Hospital::where('id', $booking->data->hospital_id)->first();
        $investigations     = Investigation::whereIn('id', $booking->data->investigations)->get();

        if (!$booking) {
            return redirect()->route('frontend.find.doctor')->with(['error' => [__('Booking not found')]]);
        }

        return view('frontend.pages.home-service.preview', compact(
            'page_title',
            'booking',
            'hospital',
            'investigations'
        ));
    }

    /**
     * Method for confirm the booking
     * @param $slug
     * @param \Illuminate\Http\Request $request
     */
    public function bookingConfirm($uuid)
    {
        $booking        = BookingTempData::where('uuid', $uuid)->first();
        $hospital       = Hospital::where('id', $booking->data->hospital_id)->first();
        $otp_exp_sec    = GlobalConst::BOOKING_EXP_SEC;
        $basic_setting  = BasicSettings::first();
        $user           = auth()->user();

        if ($booking->created_at->addSeconds($otp_exp_sec) < now()) {
            $booking->delete();
            return redirect()->route('frontend.find.doctor')->with(['error' => [__('Booking Time Out!')]]);
        }

        try {
            $trx_id = generateTrxString('doctor_bookings', 'trx_id', 'PB', 8);
            ServiceBooking::create([
                'trx_id'            => $trx_id,
                'hospital_id'       => $booking->data->hospital_id,
                'booking_data'      => ['data' => $booking->data],
                'payment_method'    => GlobalConst::CASH_PAYMENT,
                'date'              => $booking->data->date,
                'slug'              => $booking->slug,
                'uuid'              => $booking->uuid,
                'type'              => GlobalConst::CASH_PAYMENT,
                'user_id'           => auth()->user()->id,
                'total_charge'      => null,
                'price'             => $booking->data->price,
                'payable_price'     => null,
                'remark'            => GlobalConst::CASH_PAYMENT,
                'status'            => PaymentGatewayConst::STATUS_PENDING,
            ]);

        } catch (Exception $e) {
            
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }
        return redirect()->route('user.my.booking.service')->with(['success' => [__('Congratulations!Booking Confirmed Successfully.')]]);
    }
}
