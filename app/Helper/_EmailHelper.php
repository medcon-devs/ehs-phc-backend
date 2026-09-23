<?php


namespace App\Helper;


use App\Models\UserToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use MailchimpTransactional\ApiClient;
use Illuminate\Support\Facades\View;


class _EmailHelper
{
    public function __construct()
    {
    }

    // public static function sendEmail($user, $data, $view, $subject)
    // {
    //     try {
    //         $mail = new PHPMailer(true);
    //         // SMTP configurations
    //         $mail->isSMTP();
    //         $mail->Host = 'smtp.dreamhost.com';
    //         $mail->SMTPAuth = true;
    //         $mail->SMTPAutoTLS = true;
    //         $mail->Username = 'info@event.medcon.ae';
    //         $mail->Password = 'xnCNzMj92^LT';
    //         $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    //         $mail->Port = 465;
    //         $mail->setFrom('info@event.medcon.ae', 'The 2nd EHS Diabetes Conference');          //This is the email your form sends From
    //         $mail->Sender = "info@event.medcon.ae";
    //         $mail->ContentType = "text/html;charset=UTF-8\r\n";
    //         $mail->addAddress("info@medcon-me.com", "The 2nd EHS Diabetes Conference");

    //         $mail->CharSet = 'UTF-8';
    //         $mail->Priority = 3;
    //         $mail->addCustomHeader("MIME-Version: 1.0\r\n");
    //         $mail->addCustomHeader("X-Mailer: PHP'" . phpversion() . "'\r\n");
    //         $mail->addAddress($user->email, $user->name);
    //         $mail->Subject = 'The 2nd EHS Diabetes Conference - ' . $subject;

    //         $mail->isHTML(true);
    //         // Email body content
    //         $mail->Body = view($view, $data)->render();
    //         // Send email
    //         if ($mail->send()) {
    //             return true;
    //         }
    //     } catch (\Exception $e) {
    //         Log::error($e);
    //     }
    //     return false;
    // }

    public static function sendEmailToInfo($email, $data, $view, $subject)
    {
        try {
            $mail = new PHPMailer();
            // SMTP configurations
            $mail->isSMTP();
            $mail->Host = 'smtp.dreamhost.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'info@ehs-diabetes-conference.com';
            $mail->Password = 'hm&a7Rx4YdZt';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;
            $mail->setFrom('info@ehs-diabetes-conference.com', 'The EHS Diabetes Conference');          //This is the email your form sends From
            $mail->ContentType = "text/html;charset=UTF-8\r\n";
            $mail->CharSet = 'UTF-8';
            $mail->Priority = 3;
            $mail->addCustomHeader("MIME-Version: 1.0\r\n");
            $mail->addCustomHeader("X-Mailer: PHP'" . phpversion() . "'\r\n");
            $mail->addAddress("info@medcon-me.com", "EHS Diabetes Conference 2024");
            $mail->Subject = 'EHS Diabetes Conference 2024 - ' . $subject;

            $mail->isHTML(true);
            // Email body content
            $mail->Body = view($view, $data)->render();
            // Send email
            if ($mail->send()) {
                return true;
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
        return false;
    }



//     public static function sendEmail($user, $data, $view, $subject)
// {
//     try {
//         $mandrill = new ApiClient();
//         $mandrill->setApiKey(config('services.mandrill.api_key'));

//         $htmlContent = view($view, $data)->render();
//         // $htmlContent = "<h1>Test Email</h1><p>Email working</p>";


//         $to = [
//             ['email' =>$user->email, 'name' => $user->first_name, 'type' => 'to']
//         ];


//         // $mandrillAttachments = [];
//         // foreach ($attachments as $filePath) {
//         //     if (file_exists($filePath)) {
//         //         $mandrillAttachments[] = [
//         //             'type' => mime_content_type($filePath),
//         //             'name' => basename($filePath),
//         //             'content' => base64_encode(file_get_contents($filePath)),
//         //         ];
//         //     }
//         // }

//         $mandrill->messages->send([
//             'message' => [
//                 'from_email' => 'info@medcon.ae',
//                 'from_name' => 'The 4th EHS Diabetes Conference',
//                 'to' => $to,
//                 'subject' =>'The 4th EHS Diabetes Conference - ' . $subject,
//                 'html' => $htmlContent,
//             ],
//             'async' => false,
//         ]);

//         return true;
//     } catch (\Exception $e) {
//         Log::error('Mandrill Error: ' . $e->getMessage());
//         return false;
//     }
// }

public static function sendEmail($user, $data, $view, $subject)
{
    try {
        $mandrill = new ApiClient();
        $mandrill->setApiKey(config('services.mandrill.api_key'));
        $htmlContent = View::make($view, $data)->render();

        $to = [
            ['email' => $user->email, 'name' => $user->first_name, 'type' => 'to']
        ];

        $mandrill->messages->send([
            'message' => [
                'from_email' => 'info@medcon.ae',
                'from_name' => 'The 1st EHS Primary Health Conference',
                'to' => $to,
                'subject' => 'The 1st EHS Primary Health Conference - ' . $subject,
                'html' => $htmlContent,
            ],
            'async' => false,
        ]);

        return true;

    } catch (\Throwable $e) {
        Log::error('Email render/send failed', [
            'error' => $e->getMessage(),
            'view' => $view,
            'user_id' => $user->id ?? null,
        ]);
        return false;
    }
}

}
