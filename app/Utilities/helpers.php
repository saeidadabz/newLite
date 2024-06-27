<?php


use Illuminate\Support\Facades\Http;

function getPhoneNumber($phone)
{
    if ($phone === NULL) {
        return NULL;
    }
    // Remove any non-digit characters
    $phone = convert($phone);

    $phone = preg_replace('/\D/', '', convert($phone));

    // Check if the number starts with '0098' or '+98' and remove it
    if (preg_match('/^(00|\+)98|0/', $phone)) {
        $phone = preg_replace('/^(00|\+)98|^0/', '', $phone);
    }

    // Add the country code '98' if it's missing
    if (!preg_match('/^98/', $phone)) {
        $phone = '98' . $phone;
    }

    return $phone;
}


function sendSocket($eventName, $channel, $data)
{
    //TODO: has to go to queue.
    try {
        $data = [
            'eventName' => $eventName,
            'channel'   => $channel,
            'data'      => $data
        ];
        Http::post(env('SOCKET_URL', 'http://localhost:3010') . '/emit', $data);
    } catch (Exception $e) {

    }

}

function sendSms($phone, $code)
{
    return Http::asForm()->withHeader('apikey', '001a87a26baf886222895114bff20fcde5a54706f09e22487645b422fbd4dd15')
               ->post('https://api.ghasedak.me/v2/verification/send/simple', [
                   'param1'   => $code,
                   'template' => 'resanaAuth',
                   'type'     => '1',
                   'receptor' => $phone,
               ])->json();

    //TODO: // Have to go in queue.
}


/*---------------------------------------------------------------------API--------------------------------------------------------------------------------------------*/

function api($data = NULL, $message = 'success', $code = 1000,
             $http_code = 200): \Illuminate\Foundation\Application|\Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
{
    if ($message === 'success') {
        $status = 'success';
    } else {
        $status = 'fail';
    }
    $response = [
        'status' => $status,
        'meta'   => [
            'code'    => $code,
            'message' => $message,
        ],
        'data'   => $data,
    ];

    return response($response, $http_code);
}

/**
 * @throws Exception
 */
function error($message, $code = 400)
{

    throw new \RuntimeException(unConvert($message), $code);
}


function convert($value): array|string
{
    $western = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
    $eastern = ['۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '۰'];


    return str_replace($eastern, $western, $value);
}

function unConvert($value): array|string
{
    $western = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
    $eastern = ['۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '۰'];


    return str_replace($western, $eastern, $value);
}
