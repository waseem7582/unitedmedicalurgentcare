<?php

namespace App\Http\Controllers\Admin;

use App\Constants\SupportTicketConst;
use App\Http\Controllers\Controller;
use App\Models\UserSupportChat;
use App\Models\UserSupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Helpers\Response;
use Exception;
use App\Events\Admin\SupportConversationEvent;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Admin\NewUserNotification;
use App\Notifications\Admin\NewHospitalNotification ;
use App\Notifications\Admin\SupportTicketNotification;
use App\Models\UserSupportTicketAttachment;
use App\Models\Admin\BasicSettings;
use App\Models\Hospital\Hospital;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = __("All Ticket");
        $support_tickets = UserSupportTicket::orderByDesc("id")->get();
        return view('admin.sections.support-ticket.index', compact(
            'page_title',
            'support_tickets',
        ));
    }


    /**
     * Display The Pending List of Support Ticket
     *
     * @return view
     */
    public function pending()
    {
        $page_title = __("Pending Ticket");
        $support_tickets = UserSupportTicket::pending()->orderByDesc("id")->get();
        return view('admin.sections.support-ticket.index', compact(
            'page_title',
            'support_tickets'
        ));
    }


    /**
     * Display The Active List of Support Ticket
     *
     * @return view
     */
    public function active()
    {
        $page_title = __("Active Ticket");
        $support_tickets = UserSupportTicket::active()->orderByDesc("id")->get();
        return view('admin.sections.support-ticket.index', compact(
            'page_title',
            'support_tickets',
        ));
    }


    /**
     * Display The Solved List of Support Ticket
     *
     * @return view
     */
    public function solved()
    {
        $page_title = __("Solved Ticket");
        $support_tickets = UserSupportTicket::solved()->orderByDesc("id")->get();
        return view('admin.sections.support-ticket.index', compact(
            'page_title',
            'support_tickets',
        ));
    }


    public function conversation($encrypt_id)
    {
        $support_ticket_id = decrypt($encrypt_id);
        $support_ticket = UserSupportTicket::with('user')->findOrFail($support_ticket_id);
        $page_title = __("Support Chat");
        return view('admin.sections.support-ticket.conversation', compact(
            'page_title',
            'support_ticket',
        ));
    }


    public function messageReply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message'       => 'required|string|max:200',
            'support_token' => 'required|string|exists:user_support_tickets,token',
        ]);
        if ($validator->fails()) {
            $error = ['error' => $validator->errors()];
            return Response::error($error, null, 400);
        }
        $validated = $validator->validate();

        $support_ticket = UserSupportTicket::notSolved($validated['support_token'])->first();
        if (!$support_ticket) return Response::error(['error' => [__('This support ticket is closed.')]]);

        $data = [
            'user_support_ticket_id'    => $support_ticket->id,
            'sender'                    => auth()->user()->id,
            'sender_type'               => "ADMIN",
            'message'                   => $validated['message'],
            'receiver_type'             => "USER",
            'receiver'                  => $support_ticket->user_id,
        ];

        try {
            $chat_data = UserSupportChat::create($data);
        } catch (Exception $e) {
            $error = ['error' => [__('SMS Sending failed! Please try again.')]];
            return Response::error($error, null, 500);
        }

        try {
            event(new SupportConversationEvent($support_ticket, $chat_data));
        } catch (Exception $e) {
            $error = ['error' => [__('SMS Sending failed! Please try again.')]];
            return Response::error($error, null, 500);
        }

        if ($support_ticket->status != SupportTicketConst::ACTIVE) {
            try {
                $support_ticket->update([
                    'status'    => SupportTicketConst::ACTIVE,
                ]);
            } catch (Exception $e) {
                $error = ['error' => [__('Failed to change status to active!')]];
                return Response::error($error, null, 500);
            }
        }
    }


    public function solve(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'target'    => 'required|string|exists:user_support_tickets,token',
        ]);
        $validated = $validator->validate();

        $support_ticket = UserSupportTicket::where("token", $validated['target'])->first();
        if ($support_ticket->status == SupportTicketConst::SOLVED) return back()->with(['warning' => [__('This ticket is already solved!')]]);

        try {
            $support_ticket->update([
                'status'        => SupportTicketConst::SOLVED,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Success')]]);
    }

    /**
     * Method for view create support ticket page
     * @return view
     */
    public function create()
    {
        $page_title         = __("Create Support Ticket");
        return view('admin.sections.support-ticket.create', compact(
            'page_title'
        ));
    }
    /**
     * Method for check user
     * @param Illuminate\Http\Request $request
     */
    public function checkUser(Request $request)
    {

        $validator      = Validator::make($request->all(), [
            'email'     => 'required|email'
        ]);
        $validated      = $validator->validate();
        $user['data']   = User::where('email', $validated['email'])->first();
        if (!$user['data']) return response()->json(['not_exists' => ['Unregistered user.']]);
        return response($user);
    }

       /**
     * Method for check hospital
     * @param Illuminate\Http\Request $request
     */
    public function checkHospital(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);
        $validated = $validator->validate();

        $hospital = Hospital::where('email', $validated['email'])->first();

        if (!$hospital) {
            return response()->json([
                'not_exists' => ['Unregistered hospital.']
            ]);
        }

        return response()->json([
            'data' => $hospital,
            'type' => 'hospital'
        ]);
    }
    /**
     * Method for store support ticket information
     * @param Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'         => 'required|email',
            'user_type'     => 'required|string',

            'user_firstname' => 'required_if:user_type,' . SupportTicketConst::NEWUSER,
            'user_lastname'  => 'required_if:user_type,' . SupportTicketConst::NEWUSER,
            'user_password'  => 'required_if:user_type,' . SupportTicketConst::NEWUSER,
            'user_country'   => 'required_if:user_type,' . SupportTicketConst::NEWUSER,
            'user_city'      => 'required_if:user_type,' . SupportTicketConst::NEWUSER,
            'user_state'     => 'required_if:user_type,' . SupportTicketConst::NEWUSER,
            'user_address'   => 'required_if:user_type,' . SupportTicketConst::NEWUSER,

            'hospital_name' => 'required_if:user_type,' . SupportTicketConst::NEWHOSPITAL,
            'hospital_password'  => 'required_if:user_type,' . SupportTicketConst::NEWHOSPITAL,
            'hospital_mobile'    => 'required_if:user_type,' . SupportTicketConst::NEWHOSPITAL,

            'subject'       => 'required|string',
            'desc'          => 'required|string',
            'attachment.*'  => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all());
        }

        $validated          = $validator->validate();
        $basic_settings     = BasicSettings::first();
        if ($validated['user_type'] == SupportTicketConst::USER) {
            $user                       = User::where('email', $validated['email'])->first();
            $validated['token']         = generate_unique_string('user_support_tickets', 'token');
            $validated['user_id']       = $user->id;
            $validated['admin_id']      = auth()->user()->id;
            $validated['status']        = 0;
            $validated['created_at']    = now();
            $validated = Arr::except($validated, ['user_type', 'user_firstname', 'user_lastname', 'user_password', 'user_city','user_state','user_country','user_address','hospital_name','hospital_password','hospital_mobile','hospital_country','attachment']);
            try {
                $support_ticket_id = UserSupportTicket::insertGetId($validated);

                if ($basic_settings->email_notification == true) {
                    try {
                        $url = route('user.support.ticket.conversation', encrypt($support_ticket_id));
                        Notification::route('mail', $user->email)->notify(new SupportTicketNotification($user, $support_ticket_id, $url));
                    } catch (Exception $e) {
                    }
                }
            } catch (Exception $e) {
                return back()->with(['error' => ['Something went wrong! Please try again.']]);
            }

            if ($request->hasFile('attachment')) {
                $validated_files = $request->file("attachment");
                $attachment = [];
                $files_link = [];
                foreach ($validated_files as $item) {
                    $upload_file = upload_file($item, 'support-attachment');
                    if ($upload_file != false) {
                        $attachment[] = [
                            'user_support_ticket_id'    => $support_ticket_id,
                            'attachment'                => $upload_file['name'],
                            'attachment_info'           => json_encode($upload_file),
                            'created_at'                => now(),
                        ];
                    }

                    $files_link[] = get_files_path('support-attachment') . "/" . $upload_file['name'];
                }

                try {
                    UserSupportTicketAttachment::insert($attachment);
                } catch (Exception $e) {
                    $support_ticket_id->delete();
                    delete_files($files_link);
                    return back()->with(['error' => ['Oops! Failed to upload attachment. Please try again.']]);
                }
            }

            return redirect()->route('admin.support.ticket.index')->with(['success' => ['Support ticket created successfully!']]);
        }else if ($validated['user_type'] == SupportTicketConst::HOSPITAL) {
            $hospital                      = Hospital::where('email', $validated['email'])->first();
            $validated['token']         = generate_unique_string('user_support_tickets', 'token');
            $validated['hospital_id']      = $hospital->id;
            $validated['admin_id']      = auth()->user()->id;
            $validated['status']        = 0;
            $validated['created_at']    = now();
             $validated = Arr::except($validated, ['user_type', 'user_firstname', 'user_lastname', 'user_password', 'user_city','user_state','user_country','user_address','hospital_name','hospital_password','hospital_mobile','hospital_country','attachment']);

            try {
                $support_ticket_id = UserSupportTicket::insertGetId($validated);

                if ($basic_settings->email_notification == true) {
                    try {
                        $url = route('hospital.support.ticket.conversation', encrypt($support_ticket_id));
                        Notification::route('mail', $hospital->email)->notify(new SupportTicketNotification($hospital, $support_ticket_id, $url));
                    } catch (Exception $e) {
                    }
                }
            } catch (Exception $e) {
                return back()->with(['error' => ['Something went wrong! Please try again.']]);
            }

            if ($request->hasFile('attachment')) {
                $validated_files = $request->file("attachment");
                $attachment = [];
                $files_link = [];
                foreach ($validated_files as $item) {
                    $upload_file = upload_file($item, 'support-attachment');
                    if ($upload_file != false) {
                        $attachment[] = [
                            'user_support_ticket_id'    => $support_ticket_id,
                            'attachment'                => $upload_file['name'],
                            'attachment_info'           => json_encode($upload_file),
                            'created_at'                => now(),
                        ];
                    }

                    $files_link[] = get_files_path('support-attachment') . "/" . $upload_file['name'];
                }

                try {
                    UserSupportTicketAttachment::insert($attachment);
                } catch (Exception $e) {
                    $support_ticket_id->delete();
                    delete_files($files_link);
                    return back()->with(['error' => ['Oops! Failed to upload attachment. Please try again.']]);
                }
            }

            return redirect()->route('admin.support.ticket.index')->with(['success' => ['Support ticket created successfully!']]);
        } else if ($validated['user_type'] == SupportTicketConst::NEWUSER) {
            $user_name              = make_username(Str::slug($validated['user_firstname']), Str::slug($validated['user_lastname']));
            $check_user_name        = User::where('username', $user_name)->first();

            if ($check_user_name) {
                $user_name = $user_name . '-' . rand(123, 456);
            }

            $user_data['firstname']     = $validated['user_firstname'];
            $user_data['lastname']      = $validated['user_lastname'];
            $user_data['email']         = $validated['email'];
            $user_data['username']      = $user_name;
            $user_data['password']      = Hash::make($validated['user_password']);
            $user_data['address'] = [
                'country' => $validated['user_country'],
                'state' => $validated['user_state'],
                'city' => $validated['user_city'],
                'address' => $validated['user_address']
            ];
            $user_data['status']        = true;
            $user_data['email_verified']      = true;
            $user_data['kyc_verified']        = true;
            try {
                $user = User::create($user_data);
                if ($basic_settings->email_notification) {
                    try {
                        Notification::route('mail', $validated['email'])->notify(new NewUserNotification($data = $user_data, $request->user_password));
                    } catch (Exception $e) {
                    }
                }
            } catch (Exception $e) {
                return back()->with(['error' => [__("Something went wrong! Please try again.")]]);
            }

            $validated['token']         = generate_unique_string('user_support_tickets', 'token');
            $validated['user_id']       = $user->id;
            $validated['admin_id']      = auth()->user()->id;
            $validated['status']        = 0;
            $validated['created_at']    = now();
            $validated = Arr::except($validated, ['user_type', 'user_firstname', 'user_lastname', 'user_password', 'user_city','user_state','user_country','user_address','hospital_name','hospital_password','hospital_mobile','hospital_country','attachment']);

            try {
                $support_ticket_id = UserSupportTicket::insertGetId($validated);

                if ($basic_settings->email_notification == true) {
                    try {
                        $url = route('user.support.ticket.conversation', encrypt($support_ticket_id));
                        Notification::route('mail', $user->email)->notify(new SupportTicketNotification($user, $support_ticket_id, $url));
                    } catch (Exception $e) {
                    }
                }
            } catch (Exception $e) {
                return back()->with(['error' => ['Something went wrong! Please try again.']]);
            }

            if ($request->hasFile('attachment')) {
                $validated_files = $request->file("attachment");
                $attachment = [];
                $files_link = [];
                foreach ($validated_files as $item) {
                    $upload_file = upload_file($item, 'support-attachment');
                    if ($upload_file != false) {
                        $attachment[] = [
                            'user_support_ticket_id'    => $support_ticket_id,
                            'attachment'                => $upload_file['name'],
                            'attachment_info'           => json_encode($upload_file),
                            'created_at'                => now(),
                        ];
                    }

                    $files_link[] = get_files_path('support-attachment') . "/" . $upload_file['name'];
                }

                try {
                    UserSupportTicketAttachment::insert($attachment);
                } catch (Exception $e) {
                    $support_ticket_id->delete();
                    delete_files($files_link);

                    return back()->with(['error' => ['Oops! Failed to upload attachment. Please try again.']]);
                }
            }
            return redirect()->route('admin.support.ticket.index')->with(['success' => ['Support ticket created successfully!']]);
        } else{
            $user_name              = make_username_hospital(Str::slug($validated['hospital_name']),"hospitals");
            $check_user_name        = Hospital::where('username', $user_name)->first();

            if ($check_user_name) {
                $user_name = $user_name . '-' . rand(123, 456);
            }

            $user_data['hospital_name']     = $validated['hospital_name'];
        
            $user_data['email']         = $validated['email'];
            $user_data['mobile']        = $validated['hospital_mobile'];
            $user_data['address']       = [
                'country'   => $validated['hospital_country'] ?? "",
                'city'   => '',
                'state'   => '',
                'zip'   => '',
                'address'   => '',
            ];
            $user_data['username']      = $user_name;
            $user_data['password']      = Hash::make($validated['hospital_password']);
            $user_data['status']        = true;
            $user_data['email_verified']      = true;
            $user_data['kyc_verified']        = true;
            try {
                $hospital = Hospital::create($user_data);
                if ($basic_settings->email_notification) {
                    try {
                        Notification::route('mail', $validated['email'])->notify(new NewHospitalNotification ($data = $user_data, $request->hospital_password));
                    } catch (Exception $e) {
                    }
                }
            } catch (Exception $e) {
                       
                return back()->with(['error' => [__("Something went wrong! Please try again.")]]);
            }

            $validated['token']         = generate_unique_string('user_support_tickets', 'token');
            $validated['hospital_id']      = $hospital->id;
            $validated['admin_id']      = auth()->user()->id;
            $validated['status']        = 0;
            $validated['created_at']    = now();
            $validated = Arr::except($validated, ['user_type', 'user_firstname', 'user_lastname', 'user_password', 'user_city','user_state','user_country','user_address','hospital_name','hospital_password','hospital_mobile','hospital_country','attachment']);

            try {
                $support_ticket_id = UserSupportTicket::insertGetId($validated);

                if ($basic_settings->email_notification == true) {
                    try {
                        $url = route('hospital.support.ticket.conversation', encrypt($support_ticket_id));
                        Notification::route('mail', $hospital->email)->notify(new SupportTicketNotification($hospital, $support_ticket_id, $url));
                    } catch (Exception $e) {
                    }
                }
            } catch (Exception $e) {

                return back()->with(['error' => ['Something went wrong! Please try again.']]);
            }

            if ($request->hasFile('attachment')) {
                $validated_files = $request->file("attachment");
                $attachment = [];
                $files_link = [];
                foreach ($validated_files as $item) {
                    $upload_file = upload_file($item, 'support-attachment');
                    if ($upload_file != false) {
                        $attachment[] = [
                            'user_support_ticket_id'    => $support_ticket_id,
                            'attachment'                => $upload_file['name'],
                            'attachment_info'           => json_encode($upload_file),
                            'created_at'                => now(),
                        ];
                    }

                    $files_link[] = get_files_path('support-attachment') . "/" . $upload_file['name'];
                }

                try {
                    UserSupportTicketAttachment::insert($attachment);
                } catch (Exception $e) {
                    $support_ticket_id->delete();
                    delete_files($files_link);

                    return back()->with(['error' => ['Oops! Failed to upload attachment. Please try again.']]);
                }
            }
            return redirect()->route('admin.support.ticket.index')->with(['success' => ['Support ticket created successfully!']]);
        }
    }
  
    /**
     * Method for store support ticket information
     * @param Illuminate\Http\Request $request
     */
    // public function storeHospital(Request $request)
    // {
      
    //     $validator          = Validator::make($request->all(), [
    //         'hospital_email'         => 'required|email',
    //         'user_type'     => 'required|string',
    //         'hospital_name'     => 'required_if:user_type,==' . SupportTicketConst::NEWUSER,
    //         'password'      => 'required_if:user_type,==' . SupportTicketConst::NEWUSER,
    //         'subject'       => 'required|string',
    //         'desc'          => 'required',
    //         'attachment.*'  => "nullable|max:204800",
    //     ]);
    //     if ($validator->fails()) {
    //         return back()->withErrors($validator)->withInput($request->all());
    //     }


    //     $validated          = $validator->validate();
    //     $basic_settings     = BasicSettings::first();
    //     if ($validated['user_type'] == SupportTicketConst::USER) {
    //         $user                       = Hospital::where('email', $validated['hospital_email'])->first();
    //         $validated['token']         = generate_unique_string('user_support_tickets', 'token');
    //         $validated['user_id']       = null;
    //         $validated['hospital_id']   = null;
    //         $validated['admin_id']      = auth()->user()->id;
    //         $validated['status']        = 0;
    //         $validated['created_at']    = now();
    //         $validated = Arr::except($validated, ['user_type', 'firstname', 'lastname', 'password', 'attachment']);

    //         try {
    //             $support_ticket_id = UserSupportTicket::insertGetId($validated);

    //             if ($basic_settings->email_notification == true) {
    //                 try {
    //                     Notification::route('mail', $user->email)->notify(new SupportTicketNotification($user, $support_ticket_id));
    //                 } catch (Exception $e) {
    //                 }
    //             }
    //         } catch (Exception $e) {

    //             return back()->with(['error' => [__("Something went wrong! Please try again")]]);
    //         }

    //         if ($request->hasFile('attachment')) {
    //             $validated_files = $request->file("attachment");
    //             $attachment = [];
    //             $files_link = [];
    //             foreach ($validated_files as $item) {
    //                 $upload_file = upload_file($item, 'support-attachment');
    //                 if ($upload_file != false) {
    //                     $attachment[] = [
    //                         'user_support_ticket_id'    => $support_ticket_id,
    //                         'attachment'                => $upload_file['name'],
    //                         'attachment_info'           => json_encode($upload_file),
    //                         'created_at'                => now(),
    //                     ];
    //                 }

    //                 $files_link[] = get_files_path('support-attachment') . "/" . $upload_file['name'];
    //             }

    //             try {
    //                 UserSupportTicketAttachment::insert($attachment);
    //             } catch (Exception $e) {

    //                 $support_ticket_id->delete();
    //                 delete_files($files_link);

    //                 return back()->with(['error' => ['Ops! Failed to upload attachment. Please try again.']]);
    //             }
    //         }

    //         return redirect()->route('admin.support.ticket.index')->with(['success' => ['Support ticket created successfully!']]);
    //     } else {
           
    //         $user_name              = make_username(Str::slug($validated['firstname']), Str::slug($validated['lastname']));
    //         $check_user_name        = Hospital::where('username', $user_name)->first();

    //         if ($check_user_name) {
    //             $user_name = $user_name . '-' . rand(123, 456);
    //         }




    //         $user_data['hospital_name']     = $validated['hospital_name'];
         
    //         $user_data['email']         = $validated['hospital_email'];
    //         $user_data['username']      = $user_name;
    //         $user_data['password']      = Hash::make($validated['password']);


    //         $user_data['status']        = true;
    //         $user_data['email_verified']      = true;
    //         $user_data['kyc_verified']        = true;

    //         try {
    //             $user = Hospital::create($user_data);
    //             // if ($basic_settings->email_notification) {
    //             //     try {
    //             //         Notification::route('mail', $validated['email'])->notify(new NewUserNotification($data = $user_data, $request->password));
    //             //     } catch (Exception $e) {
    //             //     }
    //             // }
    //         } catch (Exception $e) {
    //                 dd($e);
    //             return back()->with(['error' => [__("Something went wrong! Please try again")]]);
    //         }



    //         $validated['token']         = generate_unique_string('user_support_tickets', 'token');
    //         $validated['user_id']       = null;
    //       $validated['hospital_id']       = $user->id;
    //         $validated['admin_id']      = auth()->user()->id;
    //         $validated['status']        = 0;
    //         $validated['created_at']    = now();
    //         $validated = Arr::except($validated, ['user_type', 'hospital_name', 'password', 'attachment']);

    //         try {
    //             $support_ticket_id = UserSupportTicket::insertGetId($validated);

    //             if ($basic_settings->email_notification == true) {
    //                 try {
    //                     Notification::route('mail', $user->email)->notify(new SupportTicketNotification($user, $support_ticket_id));
    //                 } catch (Exception $e) {
    //                 }
    //             }
    //         } catch (Exception $e) {

    //             return back()->with(['error' => [__("Something went wrong! Please try again")]]);
    //         }

    //         if ($request->hasFile('attachment')) {
    //             $validated_files = $request->file("attachment");
    //             $attachment = [];
    //             $files_link = [];
    //             foreach ($validated_files as $item) {
    //                 $upload_file = upload_file($item, 'support-attachment');
    //                 if ($upload_file != false) {
    //                     $attachment[] = [
    //                         'user_support_ticket_id'    => $support_ticket_id,
    //                         'attachment'                => $upload_file['name'],
    //                         'attachment_info'           => json_encode($upload_file),
    //                         'created_at'                => now(),
    //                     ];
    //                 }

    //                 $files_link[] = get_files_path('support-attachment') . "/" . $upload_file['name'];
    //             }

    //             try {
    //                 UserSupportTicketAttachment::insert($attachment);
    //             } catch (Exception $e) {
    //                 dd($e);
    //                 $support_ticket_id->delete();
    //                 delete_files($files_link);

    //                 return back()->with(['error' => ['Ops! Failed to upload attachment. Please try again.']]);
    //             }
    //         }
    //         return redirect()->route('admin.support.ticket.index')->with(['success' => ['Support ticket created successfully!']]);
    //     }
    // }

    /**
     * Method for delete multiple tickets
     * @param Illuminate\Http\Request $request
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'target' => 'required',
        ]);
        if ($validator->fails()) {
            return Response::error($validator->errors()->all());
        }

        $validated = $validator->validate();

        $ticket_ids       = array_map('intval', explode(',', $validated['target']));

        try {
            UserSupportTicket::whereIn('id', $ticket_ids)->delete();
        } catch (Exception $e) {
            return back()->with(['error' => [__("Something went wrong! Please try again")]]);
        }

        return back()->with(['success' => ['Support tickets deleted successfully.']]);
    }
    /**
     * Method for delete support ticket information
     * @param Illuminate\Http\Request $request
     */
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'target'        => 'required',
        ]);
        $validated = $validator->validate();

        $support_ticket = UserSupportTicket::where("id", $validated['target'])->first();

        try {
            $support_ticket->delete();
        } catch (Exception $e) {
            return back()->with(['error' => [__("Something went wrong! Please try again")]]);
        }

        return back()->with(['success' => ['Support ticket deleted successfully.']]);
    }
}
