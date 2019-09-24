<?php

namespace app\Http\Controllers;

//есть лишние use от других методов
use app\Http\Requests\ApiResponseRequest;
use app\Models\Category;
use app\Models\Feedback;
use app\Models\Order;
use app\Models\Service;
use app\Models\User;
use app\Services\GenerateListsService;
use app\Services\ImagesService;
use app\Services\Sms\Smsru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;


class ApiController extends Controller
{


    /**
     * handle requires from modal modal window 'send response'
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function apiResponse(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Not authorized.'], 403);
        }

        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'text' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Field \'text\' longer than 1000 symbols.']);
        }

        //from which view query received
        switch ($request->entity_type) {
            case 'service':

                $feedback = Feedback::where('service_id', $request->entity_id)->where('user_id', Auth::id())->get();
                $entity = Service::find($request->entity_id);
                if (!$feedback->count()) {

                    $model = new Feedback();

                    $model->fill([
                        'user_id' => Auth::id(),
                        'service_id' => $request->entity_id,
                        'text' => $request->text,
                    ]);

                    try {
                        $model->save();
                    } catch (\Exception $e) {
                        $list = [
                            'status' => 'error',
                            'message' => 'Can\'t record in db.',
                        ];
                        return response()->json($list);
                    }

                    try {
                        //send mail aor sms
                        if ($user->telephone) {
                            $smsru = new Smsru(env('SMSRU_ID'));
                            $data = new \stdClass();
                            $data->to = $user->telephone;
                            $data->msg = View::make('sms.feedback.service_text', compact('user', 'entity'))->render();
                            $smsru->send_one($data);

                        } elseif ($user->email) {
                            $paramsArr = [
                                'user' => $user,
                                'entity' => $entity,
                                'viewHtml' => 'mail.feedback.service_html',
                                'viewText' => 'mail.feedback.service_text',
                            ];
                            Mail::to($user->email)
                                ->queue(new \app\Mail\Feedback($paramsArr)
                                );
                        }

                    } catch (\Exception $e) {

                        $list = [
                            'status' => 'error',
                            'message' => 'Can\'t send mail or sms',
                        ];
                        return response()->json($list);
                    }

                    $list = [
                        'status' => 'success',
                    ];

                } else {
                    $list = [
                        'status' => 'error',
                        'message' => 'User has already responded to this service.',
                    ];
                }
                break;
            case 'order':
                $feedback = Feedback::where('order_id', $request->entity_id)->where('user_id', Auth::id())->get();
                $entity = Order::find($request->entity_id);
                if (!$feedback->count()) {
                    $model = new Feedback();
                    $model->fill([
                        'user_id' => Auth::id(),
                        'order_id' => $request->entity_id,
                        'text' => $request->text,
                    ]);

                    try {
                        $model->save();
                    } catch (\Exception $e) {
                        $list = [
                            'status' => 'error',
                            'message' => 'Can\'t record in db.',
                        ];
                        return response()->json($list);
                    }

                    try {
                        //send mail aor sms
                        if ($user->telephone) {
                            $smsru = new Smsru(env('SMSRU_ID'));
                            $data = new \stdClass();
                            $data->to = $user->telephone;
                            $data->msg = View::make('sms.feedback.order_text', compact('user', 'entity'))->render();
                            $smsru->send_one($data);
                        } elseif ($user->email) {
                            $paramsArr = [
                                'user' => $user,
                                'entity' => $entity,
                                'viewHtml' => 'mail.feedback.order_html',
                                'viewText' => 'mail.feedback.order_text',
                            ];
                            Mail::to($user->email)
                                ->queue(new \app\Mail\Feedback($paramsArr)
                                );
                        }
                        $list = [
                            'status' => 'success',
                        ];
                    } catch (\Exception $e) {

                        $list = [
                            'status' => 'error',
                            'message' => 'Can\'t send mail or sms',
                        ];

                        return response()->json($list);
                    }
                } else {
                    $list = [
                        'status' => 'error',
                        'message' => 'User has already responded to this order.',
                    ];
                }
                break;
            default:
                $list = [
                    'status' => 'error',
                    'message' => 'Entity_type wrong.',
                ];
        }
        return response()->json($list);
    }
}